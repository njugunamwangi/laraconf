<?php

namespace App\Filament\Resources;

use App\Enums\TalkLength;
use App\Enums\TalkStatus;
use App\Filament\Resources\TalkResource\Pages;
use App\Filament\Resources\TalkResource\RelationManagers;
use App\Models\Talk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class TalkResource extends Resource
{
    protected static ?string $model = Talk::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Talk::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->persistFiltersInSession()
            ->filtersTriggerAction(function ($action) {
                return $action->button()->label('Filters');
            })
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->description(function(Talk $record) {
                        return Str::of($record->abstract)->limit(40);
                    }),
                ImageColumn::make('speaker.avatar')
                    ->label('Avatar')
                    ->circular()
                    ->defaultImageUrl(function ($record) {
                        return 'https://ui-avatars.com/api/?background=random&name=' . urlencode($record->speaker->name);
                    }),
                Tables\Columns\TextColumn::make('speaker.name')
                    ->sortable(),
                ToggleColumn::make('new_talk'),
                TextColumn::make('status')
                    ->badge()
                    ->color(function($state) {
                        return $state->getColor();
                    }),
                IconColumn::make('length')
                    ->icon(function ($state) {
                        return match($state) {
                            TalkLength::LIGHTNING => 'heroicon-o-bolt',
                            TalkLength::NORMAL => 'heroicon-o-megaphone',
                            TalkLength::KEYNOTE => 'heroicon-o-key',
                        };
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('new_talk'),
                SelectFilter::make('speaker')
                    ->relationship('speaker', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Filter::make('has_avatar')
                    ->label('Speakers with avatar')
                    ->query(function($query) {
                        return $query->whereHas('speaker', function(Builder $query) {
                            $query->whereNotNull('avatar');
                        });
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver(),
                ActionGroup::make([
                    Action::make('approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(function($record) {
                            return $record->status === (TalkStatus::SUBMITTED);
                        })
                        ->action(function(Talk $record) {
                            $record->approve();
                        })
                        ->after(function() {
                            Notification::make()
                                ->duration(1000)
                                ->success()
                                ->title('Talk Approved')
                                ->send();
                        }),
                    Action::make('reject')
                        ->icon('heroicon-o-hand-thumb-down')
                        ->color('danger')
                        ->visible(function($record) {
                            return $record->status === (TalkStatus::SUBMITTED);
                        })
                        ->requiresConfirmation()
                        ->action(function(Talk $record) {
                            $record->reject();
                        })
                        ->after(function() {
                            Notification::make()
                                ->duration(1000)
                                ->danger()
                                ->title('Talk Rejected')
                                ->send();
                        }),
                ])

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('approve')
                        ->color('success')
                        ->icon('heroicon-o-hand-thumb-up')
                        ->action(function(Collection $records) {
                            $records->each->approve();
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTalks::route('/'),
            'create' => Pages\CreateTalk::route('/create'),
            // 'edit' => Pages\EditTalk::route('/{record}/edit'),
        ];
    }
}
