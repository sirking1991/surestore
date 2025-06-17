<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

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
        $invoice = $this->getRecord();
        
        // Calculate totals from items
        $items = $invoice->items;
        $subtotal = $items->sum('subtotal');
        $tax = $items->sum('tax_amount');
        $discount = $items->sum('discount_amount');
        $total = $subtotal + $tax - $discount;
        $amount_due = $total - $data['amount_paid'];
        
        $data['subtotal'] = $subtotal;
        $data['tax'] = $tax;
        $data['discount'] = $discount;
        $data['total'] = $total;
        $data['amount_due'] = $amount_due;
        
        return $data;
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Calculate amount_due based on total and amount_paid
        $data['amount_due'] = $data['total'] - $data['amount_paid'];
        
        return $data;
    }
}
