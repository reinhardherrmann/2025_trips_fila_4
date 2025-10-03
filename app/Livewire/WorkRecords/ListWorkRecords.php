<?php

namespace App\Livewire\WorkRecords;

use App\Models\WorkRecord;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Select;
use App\Models\ShiftType;
use App\Models\Absence;
use Filament\Forms\Get;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ListWorkRecords extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Übersicht der Schichten')
            ->description('Anzeige aller gespeicherten Schichten und geleisteten Arbeitsstunden')
            ->query(fn(): Builder => WorkRecord::query()
                ->with(['user', 'shiftType', 'user.absence'])->where('user_id', auth()->id()))
            ->columns([
                TextColumn::make('work_date')
                    ->label('Datum')
                    ->date(format: 'd.m.Y')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('shiftType.name')
                    ->label('Schicht')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('start_time')
                    ->label('Start')
                    ->time(format: 'H:i')
                    ->sortable(),
                TextColumn::make('end_time')
                    ->label('Ende')
                    ->time(format: 'H:i')
                    ->sortable(),
                TextColumn::make('target_minutes')
                    ->label('Soll')
                    ->formatStateUsing(fn($state) => number_format((($state ?? 0) / 60), 2)),
                TextColumn::make('actual_minutes')
                    ->label('Ist')
                    ->formatStateUsing(fn($state) => number_format((($state ?? 0) / 60), 2)),
                TextColumn::make('difference_minutes')
                    ->label('Differenz')
                    ->formatStateUsing(fn($state) => number_format((($state ?? 0) / 60), 2)),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('work_date', 'desc')
            ->filters([
                // Date presets
                Filter::make('current_week')
                    ->label('Aktuelle Woche')
                    ->default()
                    ->query(function (Builder $query) {
                        $start = now()->startOfWeek();
                        $end = now()->endOfWeek();
                        $query->whereBetween('work_date', [$start, $end]);
                    }),
                Filter::make('last_week')
                    ->label('Letzte Woche')
                    ->query(function (Builder $query) {
                        $start = now()->subWeek()->startOfWeek();
                        $end = now()->subWeek()->endOfWeek();
                        $query->whereBetween('work_date', [$start, $end]);
                    }),
                Filter::make('current_month')
                    ->label('Aktueller Monat')
                    ->query(function (Builder $query) {
                        $start = now()->startOfMonth();
                        $end = now()->endOfMonth();
                        $query->whereBetween('work_date', [$start, $end]);
                    }),
                Filter::make('last_month')
                    ->label('Letzter Monat')
                    ->query(function (Builder $query) {
                        $base = now()->subMonthNoOverflow();
                        $start = $base->copy()->startOfMonth();
                        $end = $base->copy()->endOfMonth();
                        $query->whereBetween('work_date', [$start, $end]);
                    }),
                Filter::make('current_year')
                    ->label('Aktuelles Jahr')
                    ->query(function (Builder $query) {
                        $start = now()->startOfYear();
                        $end = now()->endOfYear();
                        $query->whereBetween('work_date', [$start, $end]);
                    }),
                Filter::make('last_year')
                    ->label('Letztes Jahr')
                    ->query(function (Builder $query) {
                        $base = now()->subYear();
                        $start = $base->copy()->startOfYear();
                        $end = $base->copy()->endOfYear();
                        $query->whereBetween('work_date', [$start, $end]);
                    }),

                // Soft-deleted
                Filter::make('deleted_at')
                    ->label('gelöschte Schichten')
                    ->modifyBaseQueryUsing(function ($query) {
                        return $query->onlyTrashed();
                    }),
            ])
            ->headerActions([
                Action::make('clockIn')
                    ->icon('heroicon-o-arrow-left-end-on-rectangle')
                    ->color('success')
                    ->label('Einstempeln')
                    ->modalHeading('Schichtbeginn erfassen')
                    ->modalIcon('heroicon-o-arrow-left-end-on-rectangle')
                    ->modalCancelActionLabel('Abbrechen')
                    ->modalSubmitActionLabel('Speichern')
                    ->form([
                        DatePicker::make('work_date')
                            ->label('Datum')
                            ->default(now())
                            ->required(),
                        Select::make('absence_type')
                            ->label('Abwesenheit')
                            ->options([
                                'vacation' => 'Urlaub',
                                'sick' => 'Krankheit',
                                'unpaid' => 'Unbezahlt',
                                'other' => 'Sonstiges',
                            ])
                            ->hint('Leer lassen, um eine normale Schicht zu erfassen')
                            ->native(false)
                            ->searchable()
                            ->placeholder('— Keine Abwesenheit —')
                            ->nullable(),
                        TimePicker::make('start_time')
                            ->label('Startzeit')
                            ->seconds(false)
                            ->default(now())
                            ->required(fn($get) => empty($get('absence_type')))
                            ->hidden(fn($get) => !empty($get('absence_type'))),
                        Select::make('shift_type_id')
                            ->label('Schichttyp')
                            ->options(ShiftType::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->required(fn($get) => empty($get('absence_type')))
                            ->hidden(fn($get) => !empty($get('absence_type'))),
                    ])
                    ->action(function (array $data) {
                        $userId = auth()->id();
                        $workDate = \Carbon\Carbon::parse($data['work_date']);

                        // If an absence type is selected, create an Absence instead of a WorkRecord
                        if (!empty($data['absence_type'])) {
                            $daysCount = $workDate->isWeekday() ? 1 : 0;

                            Absence::create([
                                'user_id' => $userId,
                                'type' => $data['absence_type'],
                                'start_date' => $workDate->toDateString(),
                                'end_date' => $workDate->toDateString(),
                                'days_count' => $daysCount,
                                // Leave remark null, status uses default from migration (pending)
                            ]);

                            return; // Stop here; no work record is created
                        }

                        // Otherwise, create a normal work record
                        $startDateTime = \Carbon\Carbon::parse($data['work_date'] . ' ' . $data['start_time']);

                        $record = new WorkRecord();
                        $record->user_id = $userId;
                        $record->shift_type_id = $data['shift_type_id'];
                        $record->work_date = $workDate;
                        $record->start_time = $startDateTime;
                        // Pre-fill target minutes from shift type if available
                        $shift = ShiftType::find($data['shift_type_id']);
                        $record->target_minutes = $shift?->duration_minutes ?? 0;
                        $record->save();
                    })
                    ->after(function () {
                        $this->resetTable();
                    })
                    ->successNotification(
                        Notification::make()
                            ->title('Schichtbeginn gespeichert')
                            ->body('Der Schichtbeginn wurde erfolgreich gespeichert.')
                            ->success()
                    ),

                Action::make('clockOut')
                    ->icon('heroicon-o-arrow-left-start-on-rectangle')
                    ->color('danger')
                    ->label('Ausstempeln')
                    ->modalHeading('Schichtende erfassen')
                    ->modalIcon('heroicon-o-arrow-left-start-on-rectangle')
                    ->modalCancelActionLabel('Abbrechen')
                    ->modalSubmitActionLabel('Speichern')
                    ->form([
                        DatePicker::make('work_date')
                            ->label('Datum')
                            ->default(now())
                            ->required(),
                        TimePicker::make('end_time')
                            ->label('Endzeit')
                            ->seconds(false)
                            ->default(now())
                            ->required(),
//                        Select::make('shift_type_id')
//                            ->label('Schichttyp')
//                            ->options(ShiftType::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id'))
//                            ->searchable()
//                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $userId = auth()->id();

                        $openRecord = WorkRecord::query()
                            ->where('user_id', $userId)
                            ->whereNull('end_time')
                            ->latest('start_time')
                            ->first();

                        if (!$openRecord) {
                            Notification::make()
                                ->title('Keine offene Schicht gefunden')
                                ->body('Es wurde keine offene Schicht zum Beenden gefunden.')
                                ->warning()
                                ->send();
                            return;
                        }

                        $openRecord->work_date = \Carbon\Carbon::parse($data['work_date']);
                        if (isset($data['shift_type_id'])) {
                            $openRecord->shift_type_id = $data['shift_type_id'];
                        }
                        $openRecord->end_time = \Carbon\Carbon::parse($data['work_date'] . ' ' . $data['end_time']);
                        $openRecord->calculateDifference();
                        $openRecord->save();
                    })
                    ->after(function () {
                        $this->resetTable();
                    })
                    ->successNotification(
                        Notification::make()
                            ->title('Schichtende gespeichert')
                            ->body('Das Schichtende wurde erfolgreich gespeichert.')
                            ->success()
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.work-records.list-work-records');
    }
}
