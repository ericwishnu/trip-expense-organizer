<?php

namespace App\Filament\Resources\Trips\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TripInvitesRelationManager extends RelationManager
{
    protected static string $relationship = 'invites';

    protected static ?string $title = 'Invites';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('role')
                    ->options([
                        'editor' => 'Editor',
                        'viewer' => 'Viewer',
                    ])
                    ->default('editor')
                    ->required(),
                DateTimePicker::make('expires_at')
                    ->label('Expires at'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('invite_url')
                    ->label('Invite link')
                    ->copyable()
                    ->copyableState(fn ($record) => $record->invite_url)
                    ->tooltip(fn ($record) => $record->invite_url)
                    ->limit(40),
                TextColumn::make('role')
                    ->badge(),
                IconColumn::make('accepted_at')
                    ->label('Accepted')
                    ->boolean(),
                TextColumn::make('accepter.name')
                    ->label('Accepted by')
                    ->toggleable(),
                TextColumn::make('expires_at')
                    ->dateTime()
                    ->toggleable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('New invite')
                    ->modalHeading('Create invite link')
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
