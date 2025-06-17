<?php

namespace App\Filament\Resources\DisbursementResource\Pages;

use App\Filament\Resources\DisbursementResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDisbursement extends CreateRecord
{
    protected static string $resource = DisbursementResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
