<?php

namespace App\Filament\Resources\PurchaseInvoices\Pages;

use App\Filament\Resources\PurchaseInvoices\PurchaseInvoiceResource;
use App\Models\PurchaseInvoice;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditPurchaseInvoice extends EditRecord
{
    protected static string $resource = PurchaseInvoiceResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if ($this->record->status === PurchaseInvoice::STATUS_POSTED) {
            Notification::make()
                ->title('لا يمكن تعديل فاتورة مرحلة')
                ->body('استخدم مرتجع مشتريات أو مستند تسوية بدل تعديل الفاتورة بعد الترحيل.')
                ->danger()
                ->send();

            $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
        }
    }

    protected function beforeSave(): void
    {
        if ($this->record->status === PurchaseInvoice::STATUS_POSTED) {
            throw ValidationException::withMessages([
                'record' => 'لا يمكن تعديل فاتورة مشتريات مرحلة.',
            ]);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('عرض'),
        ];
    }
}