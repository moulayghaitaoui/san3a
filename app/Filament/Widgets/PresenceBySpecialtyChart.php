<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\TeacherAttendance;
use Illuminate\Support\Facades\DB;

class PresenceBySpecialtyChart extends ChartWidget
{
    protected static ?string $heading = 'التوزيع حسب التخصص';
    protected static ?int $sort = 5;

    protected function getData(): array
    {
        $specialties = TeacherAttendance::select('specialty', DB::raw('count(*) as total'))
            ->whereNotNull('specialty')
            ->where('specialty', '!=', '')
            ->groupBy('specialty')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'عدد الأساتذة',
                    'data' => $specialties->pluck('total')->toArray(),
                    'backgroundColor' => [
                        '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6',
                        '#ec4899', '#06b6d4', '#f97316', '#84cc16', '#64748b'
                    ],
                ],
            ],
            'labels' => $specialties->pluck('specialty')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
