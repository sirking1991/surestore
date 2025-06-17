<?php

namespace App\Filament\Resources\PurchaseDeliveryResource\Pages;

use App\Filament\Resources\PurchaseDeliveryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePurchaseDelivery extends CreateRecord
{
    protected static string $resource = PurchaseDeliveryResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
