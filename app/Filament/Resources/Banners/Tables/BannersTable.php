<?php

namespace App\Filament\Resources\Banners\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BannersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Obrázok')
                    ->disk('public')
                    ->height(60)
                    ->width(100),
                TextColumn::make('name')
                    ->label('Názov')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('link_url')
                    ->label('Odkaz')
                    ->limit(30)
                    ->url(fn ($record) => $record->link_url, shouldOpenInNewTab: true)
                    ->placeholder('—'),
                IconColumn::make('is_active')
                    ->label('Aktívny')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label('Poradie')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Vytvorené')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order', 'asc')
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Stav')
                    ->trueLabel('Aktívne')
                    ->falseLabel('Neaktívne')
                    ->placeholder('Všetky'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
