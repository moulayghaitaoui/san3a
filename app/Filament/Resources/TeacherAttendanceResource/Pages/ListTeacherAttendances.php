<?php

namespace App\Filament\Resources\TeacherAttendanceResource\Pages;

use App\Filament\Resources\TeacherAttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TeachersListExport;

class ListTeacherAttendances extends ListRecords
{
    protected static string $resource = TeacherAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('إضافة أستاذ'),
            Actions\Action::make('export_excel')
                ->label('تصدير القائمة الاسمية (Excel)')
                ->color('success')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {
                    return Excel::download(new TeachersListExport, 'القائمة_الاسمية_للاساتذة.xlsx');
                }),
        ];
    }
}
