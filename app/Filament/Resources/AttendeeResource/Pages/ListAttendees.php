<?php

namespace App\Filament\Resources\AttendeeResource\Pages;

use App\Filament\Resources\AttendeeResource;
use App\Filament\Resources\AttendeeResource\Widgets\AttendeesStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttendees extends ListRecords
{
    protected static string $resource = AttendeeResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            AttendeesStats::class
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
