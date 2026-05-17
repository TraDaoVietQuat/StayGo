<?php

namespace App\Filament\HotelPartner\Resources\PartnerReviewResource\Pages;

use App\Filament\HotelPartner\Resources\PartnerReviewResource;
use Filament\Resources\Pages\ListRecords;

class ListPartnerReviews extends ListRecords
{
    protected static string $resource = PartnerReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
