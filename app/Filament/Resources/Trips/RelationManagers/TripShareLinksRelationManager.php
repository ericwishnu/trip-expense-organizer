<?php

namespace App\Filament\Resources\Trips\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TripShareLinksRelationManager extends RelationManager
{
    protected static string $relationship = 'shareLinks';

    protected static ?string $title = 'Share links';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DateTimePicker::make('expires_at')
                    ->label('Expires at'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('share_url')
                    ->label('Share link')
                    ->copyable()
                    ->copyableState(fn ($record) => $record->share_url)
                    ->tooltip(fn ($record) => $record->share_url)
                    ->limit(40),
                TextColumn::make('view_count')
                    ->label('Views')
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->dateTime()
                    ->toggleable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('New share link')
                    ->modalHeading('Create share link')
                    ->modalSubmitActionLabel('Generate link')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['created_by'] = auth()->id();

                        return $data;
                    }),
            ])
            ->recordActions([
                DeleteAction::make()
                    ->label('Revoke'),
            ]);
    }
}
