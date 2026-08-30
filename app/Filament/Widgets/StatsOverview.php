<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\TeacherAttendance;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalTeachers = TeacherAttendance::count();
        $totalPresent = TeacherAttendance::where('is_present', true)->count();
        $totalAbsent = TeacherAttendance::where('is_absent', true)->count();
        $totalStates = TeacherAttendance::distinct('state_id')->count('state_id');

        $presentPercentage = $totalTeachers > 0 ? round(($totalPresent / $totalTeachers) * 100, 2) : 0;
        $absentPercentage = $totalTeachers > 0 ? round(($totalAbsent / $totalTeachers) * 100, 2) : 0;

        return [
            Stat::make('الولايات المشاركة', $totalStates)
                ->description('إجمالي الولايات التي رفعت بياناتها')
                ->descriptionIcon('heroicon-m-map')
                ->color('primary'),
            Stat::make('إجمالي الأساتذة', number_format($totalTeachers))
                ->description('الأساتذة المعنيين بالبرنامج')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
            Stat::make('الحاضرين', number_format($totalPresent) . ' (' . $presentPercentage . '%)')
                ->description('نسبة الحضور الوطنية')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            Stat::make('الغائبين', number_format($totalAbsent) . ' (' . $absentPercentage . '%)')
                ->description('نسبة الغياب الوطنية')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
