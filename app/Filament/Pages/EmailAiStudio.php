<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class EmailAiStudio extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-envelope-open';
    protected static ?string $navigationLabel = 'Email AI Studio';
    protected static ?string $title           = 'Email AI Studio';
    protected static ?string $navigationGroup = 'Công cụ';
    protected static ?int    $navigationSort  = 11;

    protected static string $view = 'filament.pages.email-ai-studio';
}
