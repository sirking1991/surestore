<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // If we're editing an existing record, we'll need to calculate the totals
        // based on the items that are already saved
        $order = $this->getRecord();
        
        // Calculate totals from items
        $items = $order->items;
        $subtotal = $items->sum('subtotal');
        $tax = $items->sum('tax_amount');
        $discount = $items->sum('discount_amount');
        $total = $subtotal + $tax - $discount;
        
        $data['subtotal'] = $subtotal;
        $data['tax'] = $tax;
        $data['discount'] = $discount;
        $data['total'] = $total;
        
        return $data;
    }
}
