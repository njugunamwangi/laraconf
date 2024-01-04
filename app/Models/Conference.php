<?php

namespace App\Models;

use App\Enums\Region;
use App\Enums\Status;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard;
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
            Grid::make(1)
                ->schema([
                    Wizard::make([
                        Wizard\Step::make('Basic Information')
                            ->columnSpanFull()
                            ->schema([
                                Section::make('Conference Information')
                                    ->description('Some conference description')
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Conference Name')
                                            ->required()
                                            ->maxLength(255),
                                        RichEditor::make('description')
                                            ->columnSpanFull()
                                            ->label('Conference Description')
                                            ->required(),
                                        Fieldset::make('Status')
                                            ->schema([
                                                Select::make('status')
                                                    ->required()
                                                    ->searchable()
                                                    ->enum(Status::class)
                                                    ->options(Status::class),
                                                Toggle::make('is_published')
                                            ])
                                    ]),
                                Section::make('Dates')
                                    ->description('Conference Commence and End date')
                                    ->schema([
                                        DateTimePicker::make('start_date')
                                            ->required(),
                                        DateTimePicker::make('end_date')
                                            ->required(),
                                    ])->columns(2),
                            ]),
                            Wizard\Step::make('Metadata')
                                ->columnSpanFull()
                                ->schema([
                                    Section::make('Region')
                                        ->description('Region and Venue')
                                        ->schema([
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
                                        ])->columns(2),
                                    Section::make('Speakers')
                                        ->description('Select Speakers for this conference')
                                        ->schema([
                                            CheckboxList::make('speakers')
                                                ->relationship('speakers', 'name')
                                                ->columnSpanFull()
                                                ->searchable()
                                                ->options(
                                                    Speaker::all()->pluck('name', 'id')
                                                )
                                                ->columns(3),
                                        ])
                                ])
                        ])
            ])
        ];
    }
}
