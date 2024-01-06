<?php

namespace App\Filament\Resources\AttendeeResource\Widgets;

use App\Models\Attendee;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AttendeesStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Attendees Count', Attendee::count()),
            Stat::make('Total Revenue', Attendee::sum('ticket_cost') / 100),
        ];
    }
}
