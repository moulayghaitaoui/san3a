<?php

namespace App\Filament\Resources\UploadLogResource\Pages;

use App\Filament\Resources\UploadLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use App\Models\TeacherAttendance;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\TeacherAttendanceImport;
use Filament\Notifications\Notification;

class ManageUploadLogs extends ManageRecords
{
    protected static string $resource = UploadLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('رفع ملف جديد')
                ->mutateFormDataUsing(function (array $data): array {
                    $data['status'] = 'processing';
                    return $data;
                })
                ->after(function (\App\Models\UploadLog $record) {
                    // Delete previous records for this state to avoid duplication
                    TeacherAttendance::where('state_id', $record->state_id)->delete();
                    
                    try {
                        // Queue the import
                        Excel::queueImport(new TeacherAttendanceImport($record), $record->file_name, 'local');
                        
                        Notification::make()
                            ->title('جاري معالجة الملف')
                            ->body('تم رفع الملف بنجاح وستتم معالجته في الخلفية.')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        $record->update(['status' => 'failed']);
                        Notification::make()
                            ->title('خطأ في معالجة الملف')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
