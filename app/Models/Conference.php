<?php

namespace App\Models;

use App\Enums\Region;
use App\Enums\Status;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Conference extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'region' => Region::class,
        'start_date' => 'timestamp',
        'end_date' => 'timestamp',
        'venue_id' => 'integer',
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(Speaker::class);
    }

    public function talks(): BelongsToMany
    {
        return $this->belongsToMany(Talk::class);
    }

    public static function getForm(): array {
        return [
            TextInput::make('name')
                    ->label('Conference Name')
                    ->required()
                    ->maxLength(255),
            Select::make('status')
                ->required()
                ->searchable()
                ->enum(Status::class)
                ->options(Status::class),
            RichEditor::make('description')
                ->columnSpanFull()
                ->label('Conference Description')
                ->required(),
            DateTimePicker::make('start_date')
                ->required(),
            DateTimePicker::make('end_date')
                ->required(),
            Select::make('region')
                ->live()
                ->enum(Region::class)
                ->searchable()
                ->options(Region::class),
            Select::make('venue_id')
                ->searchable()
                ->preload()
                ->createOptionForm(Venue::getForm())
                ->editOptionForm(Venue::getForm())
                ->relationship('venue', 'name', modifyQueryUsing: function(Builder $query, Get $get) {
                    return $query->where('region', $get('region'));
                }),
            CheckboxList::make('speakers')
                ->relationship('speakers', 'name')
                ->columnSpanFull()
                ->searchable()
                ->options(
                    Speaker::all()->pluck('name', 'id')
                )
                ->columns(3),
            Toggle::make('is_published')
        ];
    }
}
