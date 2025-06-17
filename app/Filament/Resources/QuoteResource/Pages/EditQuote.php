<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Filament\Resources\QuoteResource;
use App\Models\QuoteItem;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQuote extends EditRecord
{
    protected static string $resource = QuoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function afterSave(): void
    {
        // Calculate and update the quote totals based on the items
        $quote = $this->record;
        $items = $quote->items;
        
        $subtotal = $items->sum('subtotal');
        $tax = $items->sum('tax_amount');
        $discount = $items->sum('discount_amount');
        $total = $subtotal + $tax - $discount;
        
        $quote->update([
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount' => $discount,
            'total' => $total,
        ]);
    }
}
