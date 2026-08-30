<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\State;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class StatesTableWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'إحصائيات حسب الولاية';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                State::query()
                    ->withCount([
                        'teacherAttendances as total_teachers',
                        'teacherAttendances as total_present' => function ($query) {
                            $query->where('is_present', true);
                        },
                        'teacherAttendances as total_absent' => function ($query) {
                            $query->where('is_absent', true);
                        },
                    ])
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الولاية')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('upload_status')
                    ->label('حالة الرفع')
                    ->state(fn (State $record): string => $record->total_teachers > 0 ? 'تم الرفع' : 'لم يتم الرفع')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'تم الرفع' ? 'success' : 'danger')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('total_teachers', $direction);
                    }),
                Tables\Columns\TextColumn::make('total_teachers')
                    ->label('إجمالي الأساتذة')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_present')
                    ->label('الحاضرين')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_absent')
                    ->label('الغائبين')
                    ->sortable(),
                Tables\Columns\TextColumn::make('present_percentage')
                    ->label('نسبة الحضور')
                    ->state(function (State $record): string {
                        return $record->total_teachers > 0 
                            ? round(($record->total_present / $record->total_teachers) * 100, 2) . '%' 
                            : '-';
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy(DB::raw('total_present / NULLIF(total_teachers, 0)'), $direction);
                    }),
            ])->defaultSort('upload_status', 'asc');
    }
}
