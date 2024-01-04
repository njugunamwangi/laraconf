<?php

namespace App\Models;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Speaker extends Model
{
    use HasFactory;

    const QUALIFICATIONS = [
        'business-leader' => 'Business Leader',
        'charisma' => 'Charismatic Speaker',
        'first-time' => 'First Time Speaker',
        'hometown-hero' => 'Hometown Hero',
        'laracasts-contributor' => 'Laracasts Contributor',
        'twitter-influencer' => 'Large Twitter Following',
        'youtube-influencer' => 'Large YouTube Following',
        'open-source' => 'Open Source Creator / Maintainer',
        'unique-perspective' => 'Unique Perspective'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'qualifications' => 'array',
    ];

    public function conferences(): BelongsToMany
    {
        return $this->belongsToMany(Conference::class);
    }

    public function talks(): HasMany {
        return $this->hasMany(Talk::class);
    }

    public static function getForm(): array {
        return [
            Section::make('Contact Information')
                ->schema([
                    FileUpload::make('avatar')
                        ->avatar()
                        ->directory('avatars')
                        ->imageEditor()
                        ->columnSpanFull(),
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->maxLength(255),
                    TextInput::make('twitter_handle')
                        ->columnSpanFull()
                        ->maxLength(255),
                ])->columns(2),
            Section::make('Bio & Qualifications')
                ->schema([
                    RichEditor::make('bio')
                        ->maxLength(65535)
                        ->columnSpanFull(),
                    CheckboxList::make('qualifications')
                        ->columnSpanFull()
                        ->bulkToggleable()
                        ->searchable()
                        ->options(self::QUALIFICATIONS)->columns(3),
                ])
        ];
    }
}
