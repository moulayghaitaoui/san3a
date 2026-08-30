<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeacherAttendanceResource\Pages;
use App\Models\TeacherAttendance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class TeacherAttendanceResource extends Resource
{
    protected static ?string $model = TeacherAttendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'بيانات الأساتذة';
    protected static ?string $modelLabel = 'أستاذ';
    protected static ?string $pluralModelLabel = 'سجل الحضور والغياب';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('state_id')
                    ->label('الولاية')
                    ->options(\App\Models\State::pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('reference_number')
                    ->label('الرقم')
                    ->maxLength(255),
                Forms\Components\TextInput::make('full_name')
                    ->label('الاسم واللقب')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('institution')
                    ->label('المؤسسة')
                    ->maxLength(255),
                Forms\Components\TextInput::make('specialty')
                    ->label('التخصص')
                    ->maxLength(255),
                Forms\Components\TextInput::make('rank')
                    ->label('الرتبة')
                    ->maxLength(255),
                Forms\Components\TextInput::make('status_type')
                    ->label('الوضعية')
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_present')
                    ->label('حاضر؟'),
                Forms\Components\Toggle::make('is_absent')
                    ->label('غائب؟'),
                Forms\Components\TextInput::make('absence_reason')
                    ->label('سبب الغياب')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_number')->label('الرقم')->searchable(),
                Tables\Columns\TextColumn::make('full_name')->label('الاسم واللقب')->searchable(),
                Tables\Columns\TextColumn::make('state.name')->label('الولاية')->sortable(),
                Tables\Columns\TextColumn::make('institution')->label('المؤسسة')->searchable(),
                Tables\Columns\TextColumn::make('specialty')->label('التخصص')->searchable(),
                Tables\Columns\TextColumn::make('rank')->label('الرتبة')->searchable(),
                Tables\Columns\IconColumn::make('is_present')
                    ->label('حاضر')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_absent')
                    ->label('غائب')
                    ->boolean(),
                Tables\Columns\TextColumn::make('absence_reason')->label('سبب الغياب')->searchable(),
            ])
            ->filters([
                SelectFilter::make('state_id')
                    ->relationship('state', 'name')
                    ->label('تصفية بالولاية'),
                TernaryFilter::make('is_present')
                    ->label('حاضر؟'),
                TernaryFilter::make('is_absent')
                    ->label('غائب؟'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_pdf')
                    ->label('تصدير PDF (نتائج البحث)')
                    ->icon('heroicon-o-document-text')
                    ->color('danger')
                    ->action(function ($livewire) {
                        $records = $livewire->getFilteredTableQuery()->get();
                        
                        ini_set('pcre.backtrack_limit', '5000000');
                        
                        $mpdf = new \Mpdf\Mpdf([
                            'mode' => 'utf-8',
                            'format' => 'A4',
                            'autoScriptToLang' => true,
                            'autoLangToFont' => true,
                        ]);
                        $mpdf->SetDirectionality('rtl');
                        
                        $html = view('pdf.teacher-attendance', ['records' => $records])->render();
                        $mpdf->WriteHTML($html);
                        
                        return response()->streamDownload(fn () => print($mpdf->Output('', 'S')), 'teachers-report.pdf');
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make()->label('تصدير إكسل (المحدد)'),
                    Tables\Actions\BulkAction::make('export_pdf_bulk')
                        ->label('تصدير PDF (المحدد)')
                        ->icon('heroicon-o-document-text')
                        ->color('danger')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            ini_set('pcre.backtrack_limit', '5000000');
                            
                            $mpdf = new \Mpdf\Mpdf([
                                'mode' => 'utf-8',
                                'format' => 'A4',
                                'autoScriptToLang' => true,
                                'autoLangToFont' => true,
                            ]);
                            $mpdf->SetDirectionality('rtl');
                            
                            $html = view('pdf.teacher-attendance', ['records' => $records])->render();
                            $mpdf->WriteHTML($html);
                            
                            return response()->streamDownload(fn () => print($mpdf->Output('', 'S')), 'teachers-report.pdf');
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeacherAttendances::route('/'),
        ];
    }
}
