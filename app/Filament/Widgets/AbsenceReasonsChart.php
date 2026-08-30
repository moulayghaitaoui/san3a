<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\TeacherAttendance;
use Illuminate\Support\Facades\DB;

class AbsenceReasonsChart extends ChartWidget
{
    protected static ?string $heading = 'أسباب الغياب';
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $reasons = TeacherAttendance::select('absence_reason', DB::raw('count(*) as total'))
            ->where('is_absent', true)
            ->whereNotNull('absence_reason')
            ->where('absence_reason', '!=', '')
            ->groupBy('absence_reason')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'عدد الغيابات',
                    'data' => $reasons->pluck('total')->toArray(),
                    'backgroundColor' => [
                        '#f43f5e', '#ec4899', '#d946ef', '#a855f7', '#8b5cf6',
                        '#6366f1', '#3b82f6', '#0ea5e9', '#06b6d4', '#14b8a6'
                    ],
                ],
            ],
            'labels' => $reasons->pluck('absence_reason')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
