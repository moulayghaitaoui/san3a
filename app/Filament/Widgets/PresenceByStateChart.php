<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\State;

class PresenceByStateChart extends ChartWidget
{
    protected static ?string $heading = 'الحضور حسب الولاية';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $states = State::withCount([
            'teacherAttendances as total_present' => fn ($q) => $q->where('is_present', true)
        ])->having('total_present', '>', 0)
        ->orderByDesc('total_present')
        ->get();

        return [
            'datasets' => [
                [
                    'label' => 'عدد الحاضرين',
                    'data' => $states->pluck('total_present')->toArray(),
                    'backgroundColor' => '#10b981',
                ],
            ],
            'labels' => $states->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
