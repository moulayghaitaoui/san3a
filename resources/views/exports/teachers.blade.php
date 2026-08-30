<table>
    <thead>
        <tr>
            <th rowspan="2" style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #e5e7eb;">الرقم</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #e5e7eb;">الولاية</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #e5e7eb;">المؤسسة</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #e5e7eb;">الاسم واللقب</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #e5e7eb;">الرتبة</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #e5e7eb;">التخصص</th>
            <th colspan="3" style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #e5e7eb;">الوضعية</th>
        </tr>
        <tr>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #e5e7eb;">حاضر</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #e5e7eb;">غائب</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #e5e7eb;">سبب الغياب</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
        <tr>
            <td style="text-align: center; border: 1px solid #000000;">{{ $loop->iteration }}</td>
            <td style="text-align: center; border: 1px solid #000000;">{{ $record->state->name ?? '' }}</td>
            <td style="text-align: center; border: 1px solid #000000;">{{ $record->institution }}</td>
            <td style="text-align: center; border: 1px solid #000000;">{{ $record->full_name }}</td>
            <td style="text-align: center; border: 1px solid #000000;">{{ $record->rank }}</td>
            <td style="text-align: center; border: 1px solid #000000;">{{ $record->specialty }}</td>
            <td style="text-align: center; border: 1px solid #000000; font-family: DejaVu Sans, sans-serif;">{!! $record->is_present ? '&#10004;' : '' !!}</td>
            <td style="text-align: center; border: 1px solid #000000; font-family: DejaVu Sans, sans-serif;">{!! $record->is_absent ? '&#10004;' : '' !!}</td>
            <td style="text-align: center; border: 1px solid #000000;">{{ $record->absence_reason }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
