<?php

namespace App\Filament\HotelPartner\Resources\PricingResource\Pages;

use App\Filament\HotelPartner\Resources\PricingResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePricing extends CreateRecord
{
    protected static string $resource = PricingResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
