<?php

namespace App\Models;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendee extends Model
{
    use HasFactory;

    public function conference(): BelongsTo {
        return $this->belongsTo(Conference::class);
    }

    public static function getForm(): array {
        return [
            Grid::make(2)
                ->schema([
                    TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique()
                    ->maxLength(255)
                ])
        ];
    }
}
