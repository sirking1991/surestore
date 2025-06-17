<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Filament\Resources\QuoteResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateQuote extends CreateRecord
{
    protected static string $resource = QuoteResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Calculate totals based on items
        $subtotal = 0;
        $tax = 0;
        $discount = 0;
        
        // The items will be saved after the quote is created,
        // so we'll just set default values here
        $data['subtotal'] = 0;
        $data['tax'] = 0;
        $data['discount'] = 0;
        $data['total'] = 0;
        
        return $data;
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
