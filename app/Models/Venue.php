<?php

namespace App\Models;

use App\Enums\Region;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends Model
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
    ];

    public function conferences(): HasMany
    {
        return $this->hasMany(Conference::class);
    }

    public static function getForm(): array {
        return [
            Section::make('Venue Name')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                ]),
            Section::make('Venue Locales')
                ->schema([
                    TextInput::make('city')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('country')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('postal_code')
                        ->required()
                        ->maxLength(255),
                    Select::make('region')
                        ->enum(Region::class)
                        ->options(Region::class),
                ])->columns(2)
        ];
    }
}
