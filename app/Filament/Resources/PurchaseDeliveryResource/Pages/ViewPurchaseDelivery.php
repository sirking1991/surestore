<?php

namespace App\Filament\Resources\PurchaseDeliveryResource\Pages;

use App\Filament\Resources\PurchaseDeliveryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPurchaseDelivery extends ViewRecord
{
    protected static string $resource = PurchaseDeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
