<?php

namespace App\Filament\Resources\PurchaseDeliveryResource\Pages;

use App\Filament\Resources\PurchaseDeliveryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPurchaseDeliveries extends ListRecords
{
    protected static string $resource = PurchaseDeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
