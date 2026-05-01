<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages;
use App\Models\AuditLog;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static ?string $navigationIcon   = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel  = 'Nhật ký hoạt động';
    protected static ?string $modelLabel       = 'Nhật ký';
    protected static ?string $pluralModelLabel = 'Nhật ký hoạt động';
    protected static ?string $navigationGroup  = 'Hệ thống';
    protected static ?int    $navigationSort   = 10;

    // Read-only resource — no create/edit
    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->searchPlaceholder('Tìm theo hành động, subject...')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->width(60),

                Tables\Columns\TextColumn::make('user.full_name')
                    ->label('Người dùng')
                    ->description(fn(AuditLog $r): string => $r->user?->email ?? 'Hệ thống')
                    ->searchable()
                    ->placeholder('Hệ thống'),

                Tables\Columns\BadgeColumn::make('action')
                    ->label('Hành động')
                    ->colors([
                        'success' => fn($state) => str_contains($state, 'creat') || str_contains($state, 'confirm'),
                        'warning' => fn($state) => str_contains($state, 'updat') || str_contains($state, 'refund'),
                        'danger'  => fn($state) => str_contains($state, 'delet') || str_contains($state, 'cancel'),
                        'gray'    => fn() => true,
                    ])
                    ->searchable(),

                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Đối tượng')
                    ->formatStateUsing(fn(?string $state): string => $state ? class_basename($state) : '—')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('subject_id')
                    ->label('ID')
                    ->width(60)
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('new_values')
                    ->label('Giá trị mới')
                    ->getStateUsing(fn(AuditLog $r): HtmlString => new HtmlString(
                        '<pre style="font-size:11px;max-width:260px;overflow:auto;white-space:pre-wrap;word-break:break-all;">'
                        . e(json_encode($r->new_values, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT))
                        . '</pre>'
                    ))
                    ->html()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Thời gian')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->label('Hành động')
                    ->options(
                        AuditLog::select('action')->distinct()->pluck('action', 'action')->toArray()
                    )
                    ->placeholder('Tất cả hành động'),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
        ];
    }
}
