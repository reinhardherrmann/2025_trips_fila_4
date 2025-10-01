<?php

namespace App\Livewire\WorkRecords;

use App\Models\WorkRecord;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
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
                    ->numeric(),
                TextColumn::make('actual_minutes')
                    ->label('Arbeitsstunden')
                    ->numeric(),
                TextColumn::make('difference_minutes')
                    ->label('Differenz')
                    ->numeric(),
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
                    ->label('gelöschte Märkte')
                    ->modifyBaseQueryUsing(function ($query) {
                        return $query->onlyTrashed();
                    }),
            ])
            ->headerActions([
                Action::make('create')
                    ->icon('heroicon-o-arrow-left-end-on-rectangle')
                    ->color('success')
                    //->url(fn(): string => route('trips.create'))
                    ->label('Einstempeln'),
                Action::make('create')
                    ->icon('heroicon-o-arrow-left-start-on-rectangle')
                    ->color('danger')
                    //->url(fn(): string => route('trips.create'))
                    ->label('Ausstempeln'),
            ])
            ->recordActions([
                Action::make('delete')
                    ->iconButton()
                    ->requiresConfirmation()
                    ->modalHeading('Löschen bestätigen')
                    ->modalDescription('Soll diese Schicht gelöscht werden?')
                    ->modalCancelActionLabel('Abbrechen')
                    ->modalSubmitActionLabel('Löschen')
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->action(function (WorkRecord $record) {
                        $record->delete();
                    })
                    ->successNotification(
                        Notification::make()
                            ->title('Schicht löschen')
                            ->body('Schicht erfolgreich gelöscht')
                            ->success()
                    )
                    ->failureNotification(
                        Notification::make()
                            ->title('Schicht löschen')
                            ->body('Löschen der Schicht fehlgeschlagen')
                            ->danger()
                    ),
                RestoreAction::make(),
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
