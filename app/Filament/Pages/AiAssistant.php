<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class AiAssistant extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationLabel = 'Trợ lý AI';
    protected static ?string $title = 'Trợ lý AI Admin';
    protected static ?string $navigationGroup = 'Công cụ';
    protected static ?int $navigationSort = 10;

    protected static string $view = 'filament.pages.ai-assistant';
}
