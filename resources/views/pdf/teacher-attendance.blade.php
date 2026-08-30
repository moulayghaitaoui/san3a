<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>تقرير حضور الأساتذة</title>
    <style>
        body {
            font-family: sans-serif;
            direction: rtl;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #444;
            padding: 5px;
            text-align: right;
        }
        th {
            background-color: #e5e7eb;
            font-weight: bold;
        }
        h2, h3 {
            text-align: center;
            margin-bottom: 15px;
            color: #1f2937;
        }
    </style>
</head>
<body>
    <h2>التقرير الوطني لإحصائيات الأساتذة</h2>

    @php
        $summary = [];
        $activeStateIds = [];
        $totalTeachers = 0;
        $totalPresent = 0;
        $totalAbsent = 0;

        foreach($records as $record) {
            $stateName = $record->state->name ?? 'غير محدد';
            if ($record->state) {
                $activeStateIds[] = $record->state->id;
            }
            if (!isset($summary[$stateName])) {
                $summary[$stateName] = ['total' => 0, 'absent' => 0, 'present' => 0];
            }
            
            $summary[$stateName]['total']++;
            $totalTeachers++;
            
            if ($record->is_absent) {
                $summary[$stateName]['absent']++;
                $totalAbsent++;
            }
            if ($record->is_present) {
                $summary[$stateName]['present']++;
                $totalPresent++;
            }
        }
        $activeStateIds = array_unique($activeStateIds);
        $totalParticipatingStates = count($activeStateIds);
        $missingStates = \App\Models\State::whereNotIn('id', $activeStateIds)->orderBy('id')->get();
        
        $overallPresentPercent = $totalTeachers > 0 ? round(($totalPresent / $totalTeachers) * 100, 2) : 0;
        $overallAbsentPercent = $totalTeachers > 0 ? round(($totalAbsent / $totalTeachers) * 100, 2) : 0;
    @endphp

    <div style="background-color: #f3f4f6; padding: 15px; margin-bottom: 25px; border: 1px solid #d1d5db;">
        <h3 style="margin-top: 0; text-align: center; color: #111827;">الإحصائيات العامة (إجمالي)</h3>
        <table style="width: 100%; border-collapse: collapse; text-align: center; margin-bottom: 0;">
            <tbody>
                <tr>
                    <td style="border: 1px solid #d1d5db; padding: 10px; background-color: #e0f2fe; width: 25%;">
                        <strong style="color: #0369a1; font-size: 14px;">الولايات المشاركة</strong><br>
                        <span style="font-size: 20px; font-weight: bold; color: #0284c7;">{{ $totalParticipatingStates }}</span>
                    </td>
                    <td style="border: 1px solid #d1d5db; padding: 10px; background-color: #f3f4f6; width: 25%;">
                        <strong style="color: #374151; font-size: 14px;">إجمالي الأساتذة</strong><br>
                        <span style="font-size: 20px; font-weight: bold; color: #111827;">{{ $totalTeachers }}</span>
                    </td>
                    <td style="border: 1px solid #d1d5db; padding: 10px; background-color: #dcfce7; width: 25%;">
                        <strong style="color: #166534; font-size: 14px;">الحضور</strong><br>
                        <span style="font-size: 20px; font-weight: bold; color: #15803d;">{{ $totalPresent }} ({{ $overallPresentPercent }}%)</span>
                    </td>
                    <td style="border: 1px solid #d1d5db; padding: 10px; background-color: #fee2e2; width: 25%;">
                        <strong style="color: #991b1b; font-size: 14px;">الغياب</strong><br>
                        <span style="font-size: 20px; font-weight: bold; color: #b91c1c;">{{ $totalAbsent }} ({{ $overallAbsentPercent }}%)</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <h3>ملخص الإحصائيات مفصل حسب الولاية</h3>
    <table>
        <thead>
            <tr>
                <th>الولاية</th>
                <th>إجمالي الأساتذة</th>
                <th>عدد الحاضرين</th>
                <th>عدد الغائبين</th>
            </tr>
        </thead>
        <tbody>
            @foreach($summary as $state => $stats)
            <tr>
                <td style="font-weight: bold;">{{ $state }}</td>
                <td style="text-align: center;">{{ $stats['total'] }}</td>
                <td style="text-align: center; color: #15803d; font-weight: bold;">{{ $stats['present'] }}</td>
                <td style="text-align: center; color: #b91c1c; font-weight: bold;">{{ $stats['absent'] }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #e5e7eb; font-weight: bold;">
                <td style="text-align: center;">المجموع الكلي</td>
                <td style="text-align: center;">{{ $totalTeachers }}</td>
                <td style="text-align: center; color: #15803d;">{{ $totalPresent }}</td>
                <td style="text-align: center; color: #b91c1c;">{{ $totalAbsent }}</td>
            </tr>
        </tfoot>
    </table>

    <br>

    @if($missingStates->count() > 0)
    <h3>الولايات التي لم ترسل البيانات</h3>
    <table>
        <thead>
            <tr>
                <th>رقم الولاية</th>
                <th>اسم الولاية</th>
                <th>الملاحظة</th>
            </tr>
        </thead>
        <tbody>
            @foreach($missingStates as $missingState)
            <tr>
                <td style="text-align: center;">{{ str_pad($missingState->id, 2, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $missingState->name }}</td>
                <td style="text-align: center; color: red;">لم ترسل البيانات</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #e5e7eb; font-weight: bold;">
                <td colspan="2" style="text-align: center;">إجمالي الولايات المتأخرة</td>
                <td style="text-align: center; color: red;">{{ $missingStates->count() }}</td>
            </tr>
        </tfoot>
    </table>
    <br>
    @endif

    <hr>
    <br>

    @php
        $absentRecords = $records->filter(function($record) {
            return $record->is_absent;
        });
    @endphp

    @if($absentRecords->count() > 0)
    <h3>قائمة الأساتذة الغائبين فقط</h3>
    <table>
        <thead>
            <tr>
                <th>الرقم</th>
                <th>الولاية</th>
                <th>المؤسسة</th>
                <th>الاسم واللقب</th>
                <th>الرتبة</th>
                <th>التخصص</th>
                <th>سبب الغياب</th>
            </tr>
        </thead>
        <tbody>
            @foreach($absentRecords as $record)
            <tr>
                <td style="text-align: center;">{{ $loop->iteration }}</td>
                <td style="text-align: center;">{{ $record->state->name ?? '' }}</td>
                <td style="text-align: center;">{{ $record->institution }}</td>
                <td style="text-align: center;">{{ $record->full_name }}</td>
                <td style="text-align: center;">{{ $record->rank }}</td>
                <td style="text-align: center;">{{ $record->specialty }}</td>
                <td style="text-align: center;">{{ $record->absence_reason }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #e5e7eb; font-weight: bold;">
                <td colspan="6" style="text-align: center;">إجمالي الأساتذة الغائبين</td>
                <td style="text-align: center; color: #b91c1c;">{{ $absentRecords->count() }}</td>
            </tr>
        </tfoot>
    </table>
    <br>
    <hr>
    <br>
    @endif

    <h3>القائمة الاسمية للأساتذة (الجميع)</h3>
    <table>
        <thead>
            <tr>
                <th rowspan="2">الرقم</th>
                <th rowspan="2">الولاية</th>
                <th rowspan="2">المؤسسة</th>
                <th rowspan="2">الاسم واللقب</th>
                <th rowspan="2">الرتبة</th>
                <th rowspan="2">التخصص</th>
                <th colspan="3">الوضعية</th>
            </tr>
            <tr>
                <th>حاضر</th>
                <th>غائب</th>
                <th>سبب الغياب</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
            <tr>
                <td style="text-align: center;">{{ $loop->iteration }}</td>
                <td style="text-align: center;">{{ $record->state->name ?? '' }}</td>
                <td style="text-align: center;">{{ $record->institution }}</td>
                <td style="text-align: center;">{{ $record->full_name }}</td>
                <td style="text-align: center;">{{ $record->rank }}</td>
                <td style="text-align: center;">{{ $record->specialty }}</td>
                <td style="text-align: center; font-size: 16px;">{!! $record->is_present ? '&#10004;' : '' !!}</td>
                <td style="text-align: center; font-size: 16px;">{!! $record->is_absent ? '&#10004;' : '' !!}</td>
                <td style="text-align: center;">{{ $record->absence_reason }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #e5e7eb; font-weight: bold;">
                <td colspan="6" style="text-align: center;">إجمالي الأساتذة</td>
                <td colspan="3" style="text-align: center;">{{ $records->count() }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
