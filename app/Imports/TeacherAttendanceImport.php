<?php

namespace App\Imports;

use App\Models\TeacherAttendance;
use App\Models\UploadLog;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Illuminate\Contracts\Queue\ShouldQueue;

class TeacherAttendanceImport implements ToCollection, ShouldQueue, WithEvents
{
    public $uploadLog;

    public function __construct(UploadLog $uploadLog)
    {
        $this->uploadLog = $uploadLog;
    }

    public function collection(Collection $rows)
    {
        $headerRowIndex = -1;
        
        foreach ($rows as $index => $row) {
            $rowString = implode(' ', $row->toArray());
            if (mb_strpos($rowString, 'الاسم') !== false || mb_strpos($rowString, 'اللقب') !== false) {
                $headerRowIndex = $index;
                break;
            }
        }
        
        if ($headerRowIndex === -1) {
            $headerRowIndex = 0;
        }

        $headers1 = $rows[$headerRowIndex] ?? collect([]);
        $headers2 = $rows[$headerRowIndex + 1] ?? collect([]);

        $colMap = [
            'name' => $this->findIndex($headers1, $headers2, ['الاسم', 'اللقب']),
            'institution' => $this->findIndex($headers1, $headers2, ['المؤسسة', 'مؤسسة']),
            'specialty' => $this->findIndex($headers1, $headers2, ['التخصص', 'تخصص']),
            'rank' => $this->findIndex($headers1, $headers2, ['الرتبة', 'رتبة']),
            'ref' => $this->findIndex($headers1, $headers2, ['الرقم', 'رقم', 'رقم الولاية']),
            'present' => $this->findIndex($headers1, $headers2, ['حاضر', 'حضور', 'الوضعية', 'الملاحظة']),
            'absent' => $this->findIndex($headers1, $headers2, ['غائب', 'غياب']),
            'reason' => $this->findIndex($headers1, $headers2, ['سبب', 'السبب', 'ملاحظة']),
        ];

        // Fallback for present/absent if they are merged under "الوضعية"
        if ($colMap['present'] !== null) {
            if ($colMap['absent'] === null) $colMap['absent'] = $colMap['present'] + 1;
            if ($colMap['reason'] === null) $colMap['reason'] = $colMap['present'] + 2;
        }

        for ($i = $headerRowIndex + 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            
            $fullName = $colMap['name'] !== null ? ($row[$colMap['name']] ?? null) : null;
            $institution = $colMap['institution'] !== null ? ($row[$colMap['institution']] ?? null) : null;
            
            // Skip completely empty rows
            if (empty(trim($fullName ?? '')) && empty(trim($institution ?? ''))) {
                continue;
            }
            
            // Skip rows that look like repeated headers
            if (mb_strpos((string)($fullName ?? ''), 'الاسم') !== false || mb_strpos((string)($row[0] ?? ''), 'حاضر') !== false) {
                continue;
            }

            $isPresent = $colMap['present'] !== null ? $this->parseBoolean($row[$colMap['present']] ?? null) : false;
            $isAbsent = $colMap['absent'] !== null ? $this->parseBoolean($row[$colMap['absent']] ?? null) : false;
            $reason = $colMap['reason'] !== null ? ($row[$colMap['reason']] ?? null) : null;

            TeacherAttendance::create([
                'upload_log_id'    => $this->uploadLog->id,
                'state_id'         => $this->uploadLog->state_id,
                'reference_number' => $colMap['ref'] !== null ? ($row[$colMap['ref']] ?? null) : null,
                'full_name'        => trim((string)$fullName) ?: 'بدون اسم',
                'status_type'      => $isPresent ? 'حاضر' : ($isAbsent ? 'غائب' : null),
                'specialty'        => $colMap['specialty'] !== null ? ($row[$colMap['specialty']] ?? null) : null,
                'rank'             => $colMap['rank'] !== null ? ($row[$colMap['rank']] ?? null) : null,
                'institution'      => trim((string)$institution),
                'is_present'       => $isPresent,
                'is_absent'        => $isAbsent,
                'absence_reason'   => $reason,
            ]);
        }
    }

    private function findIndex($h1, $h2, $keywords)
    {
        foreach ($h1 as $index => $val) {
            foreach ($keywords as $kw) {
                if ($val && mb_strpos((string)$val, $kw) !== false) return $index;
            }
        }
        foreach ($h2 as $index => $val) {
            foreach ($keywords as $kw) {
                if ($val && mb_strpos((string)$val, $kw) !== false) return $index;
            }
        }
        return null;
    }

    private function parseBoolean($value)
    {
        if (is_string($value)) {
            $value = trim(mb_strtolower($value));
            return in_array($value, ['نعم', '1', 'true', 'yes', 'حاضر', 'x', '*', '+']);
        }
        return (bool)$value;
    }

    public function registerEvents(): array
    {
        return [
            AfterImport::class => function(AfterImport $event) {
                $this->uploadLog->update([
                    'status' => 'completed',
                    'records_count' => $this->uploadLog->teacherAttendances()->count(),
                ]);
            },
        ];
    }
}
