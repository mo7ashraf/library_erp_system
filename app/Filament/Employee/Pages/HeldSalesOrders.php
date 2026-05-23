<?php

namespace App\Filament\Employee\Pages;

use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Http\RedirectResponse;
use UnitEnum;

class HeldSalesOrders extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static string|UnitEnum|null $navigationGroup = 'نقطة البيع';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.employee.pages.held-sales-orders';

    public array $heldOrders = [];

    public static function getNavigationLabel(): string
    {
        return 'طلبات معلقة';
    }

    public function getTitle(): string
    {
        return 'طلبات معلقة';
    }

    public function mount(): void
    {
        $this->loadOrders();
    }

    public function loadOrders(): void
    {
        $this->heldOrders = array_values(session()->get($this->heldOrdersSessionKey(), []));
    }

    public function resume(string $id): RedirectResponse
    {
        session()->put($this->resumeHeldOrderSessionKey(), $id);

        return redirect('/employee/sales-desk');
    }

    public function delete(string $id): void
    {
        $orders = session()->get($this->heldOrdersSessionKey(), []);

        unset($orders[$id]);

        session()->put($this->heldOrdersSessionKey(), $orders);

        $this->loadOrders();

        Notification::make()
            ->title('تم حذف الطلب المعلق')
            ->success()
            ->send();
    }

    private function heldOrdersSessionKey(): string
    {
        return 'sales_desk_held_orders_user_' . auth()->id();
    }

    private function resumeHeldOrderSessionKey(): string
    {
        return 'sales_desk_resume_held_order_user_' . auth()->id();
    }
}