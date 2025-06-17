<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Calculate amount_due based on total and amount_paid
        $data['amount_due'] = $data['total'] - $data['amount_paid'];
        
        // The items will be saved after the invoice is created,
        // so we'll just set default values here
        $data['subtotal'] = 0;
        $data['tax'] = 0;
        $data['discount'] = 0;
        $data['total'] = 0;
        $data['amount_due'] = 0;
        
        return $data;
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
