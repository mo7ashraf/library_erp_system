<?php

namespace App\Console\Commands;

use App\Models\BankAccount;
use App\Models\Branch;
use App\Models\Cashbox;
use App\Models\TreasuryTransaction;
use App\Services\Finance\TreasuryService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class FinanceFlowCheckCommand extends Command
{
    protected $signature = 'finance:check-flow';

    protected $description = 'Safely checks cashbox and bank treasury processing using a rollback transaction.';

    public function handle(): int
    {
        $this->info('Starting finance processing check...');

        DB::beginTransaction();

        try {
            $suffix = now()->format('YmdHis');

            $branch = Branch::create([
                'code' => "TEST-FIN-BR-{$suffix}",
                'name' => 'فرع اختبار المالية',
                'is_active' => true,
            ]);

            $cashbox = Cashbox::create([
                'branch_id' => $branch->id,
                'code' => "TEST-CASH-{$suffix}",
                'name' => 'خزينة اختبار',
                'opening_balance' => 1000,
                'current_balance' => 1000,
                'is_active' => true,
            ]);

            $bankAccount = BankAccount::create([
                'branch_id' => $branch->id,
                'code' => "TEST-BANK-{$suffix}",
                'bank_name' => 'بنك اختبار',
                'account_name' => 'حساب اختبار',
                'account_number' => "ACC-{$suffix}",
                'iban' => null,
                'opening_balance' => 5000,
                'current_balance' => 5000,
                'is_active' => true,
            ]);

            $service = app(TreasuryService::class);

            $service->receive([
                'cashbox_id' => $cashbox->id,
                'payment_channel' => TreasuryTransaction::CHANNEL_CASH,
                'transaction_number' => "TEST-CASH-IN-{$suffix}",
                'transaction_date' => now()->toDateString(),
                'transaction_type' => TreasuryTransaction::TYPE_INCOME,
                'amount' => 200,
                'description' => 'اختبار قبض نقدي',
            ]);

            $this->assertCashboxBalance($cashbox->id, 1200, 'Cash receive +200');

            $service->pay([
                'cashbox_id' => $cashbox->id,
                'payment_channel' => TreasuryTransaction::CHANNEL_CASH,
                'transaction_number' => "TEST-CASH-OUT-{$suffix}",
                'transaction_date' => now()->toDateString(),
                'transaction_type' => TreasuryTransaction::TYPE_EXPENSE,
                'amount' => 50,
                'description' => 'اختبار صرف نقدي',
            ]);

            $this->assertCashboxBalance($cashbox->id, 1150, 'Cash payment -50');

            try {
                $service->pay([
                    'cashbox_id' => $cashbox->id,
                    'payment_channel' => TreasuryTransaction::CHANNEL_CASH,
                    'transaction_number' => "TEST-CASH-OVER-{$suffix}",
                    'transaction_date' => now()->toDateString(),
                    'transaction_type' => TreasuryTransaction::TYPE_EXPENSE,
                    'amount' => 999999,
                    'description' => 'اختبار صرف أكبر من الرصيد',
                ]);

                throw new RuntimeException('Cash over-payment was allowed. This is wrong.');
            } catch (RuntimeException $exception) {
                $this->info('✓ Cash over-payment correctly rejected.');
            }

            $this->assertCashboxBalance($cashbox->id, 1150, 'Cash balance after rejected payment');

            $service->receive([
                'bank_account_id' => $bankAccount->id,
                'payment_channel' => TreasuryTransaction::CHANNEL_BANK,
                'transaction_number' => "TEST-BANK-IN-{$suffix}",
                'transaction_date' => now()->toDateString(),
                'transaction_type' => TreasuryTransaction::TYPE_INCOME,
                'amount' => 300,
                'description' => 'اختبار إيداع بنكي',
            ]);

            $this->assertBankBalance($bankAccount->id, 5300, 'Bank receive +300');

            $service->pay([
                'bank_account_id' => $bankAccount->id,
                'payment_channel' => TreasuryTransaction::CHANNEL_BANK,
                'transaction_number' => "TEST-BANK-OUT-{$suffix}",
                'transaction_date' => now()->toDateString(),
                'transaction_type' => TreasuryTransaction::TYPE_EXPENSE,
                'amount' => 100,
                'description' => 'اختبار صرف بنكي',
            ]);

            $this->assertBankBalance($bankAccount->id, 5200, 'Bank payment -100');

            DB::rollBack();

            $this->info('------------------------------------------');
            $this->info('✓ Finance processing check passed.');
            $this->info('✓ All test data was rolled back.');
            $this->info('------------------------------------------');

            return self::SUCCESS;
        } catch (Throwable $exception) {
            DB::rollBack();

            $this->error('Finance processing check failed:');
            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }

    private function assertCashboxBalance(int $cashboxId, float $expected, string $step): void
    {
        $actual = (float) Cashbox::findOrFail($cashboxId)->current_balance;

        if (abs($actual - $expected) > 0.0001) {
            throw new RuntimeException("{$step} failed. Expected {$expected}, actual {$actual}.");
        }

        $this->info("✓ {$step}: balance = {$actual}");
    }

    private function assertBankBalance(int $bankAccountId, float $expected, string $step): void
    {
        $actual = (float) BankAccount::findOrFail($bankAccountId)->current_balance;

        if (abs($actual - $expected) > 0.0001) {
            throw new RuntimeException("{$step} failed. Expected {$expected}, actual {$actual}.");
        }

        $this->info("✓ {$step}: balance = {$actual}");
    }
}