<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\TeacherAttendance;
use Illuminate\Support\Facades\DB;

class PresenceByRankChart extends ChartWidget
{
    protected static ?string $heading = 'التوزيع حسب الرتبة';
    protected static ?int $sort = 6;

    protected function getData(): array
    {
        $ranks = TeacherAttendance::select('rank', DB::raw('count(*) as total'))
            ->whereNotNull('rank')
            ->where('rank', '!=', '')
            ->groupBy('rank')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'عدد الأساتذة',
                    'data' => $ranks->pluck('total')->toArray(),
                    'backgroundColor' => [
                        '#8b5cf6', '#ec4899', '#f59e0b', '#10b981', '#3b82f6',
                        '#ef4444', '#06b6d4', '#f97316', '#84cc16', '#64748b'
                    ],
                ],
            ],
            'labels' => $ranks->pluck('rank')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
