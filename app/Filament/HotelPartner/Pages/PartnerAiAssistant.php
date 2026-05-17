<?php

namespace App\Filament\HotelPartner\Pages;

use Filament\Pages\Page;

class PartnerAiAssistant extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-sparkles';
    protected static ?string $navigationLabel = 'Trợ lý AI';
    protected static ?string $title           = 'Trợ lý AI Khách sạn';
    protected static ?int    $navigationSort  = 99;

    protected static string $view = 'filament.hotel-partner.pages.partner-ai-assistant';
}
