<?php

namespace App\Exports;

use App\Models\TeacherAttendance;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TeachersListExport implements FromView, ShouldAutoSize, WithStyles
{
    public function view(): View
    {
        return view('exports.teachers', [
            'records' => TeacherAttendance::with('state')->get()
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // Align center and bold header
        return [
            1    => ['font' => ['bold' => true], 'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]],
            2    => ['font' => ['bold' => true], 'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]],
        ];
    }
}
