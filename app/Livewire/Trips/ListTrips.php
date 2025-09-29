<?php

namespace App\Livewire\Trips;

use App\Models\Store;
use App\Models\Trip;
use App\Models\Status;
use App\Models\Truck;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
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

class ListTrips extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
    {
        return $table
            ->heading("Übersicht der Touren")
            ->description("Anzeige aller gespeicherten Touren")
            ->query(fn(): Builder => Trip::query()
                ->with(['user', 'startStock', 'targetStock', 'stopps', 'tripType', 'status', 'truck'])
                ->withCount('stopps')
                ->where('user_id', auth()->id()))
            ->columns([
                TextColumn::make('trip_number')
                    ->label('Tournummer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('date')
                    ->label('Datum')
                    ->date(format: 'd.m.Y')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('plan_date')
                    ->label('Plandatum')
                    ->date(format: 'd.m.Y')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('startStock.name')
                    ->label('Start-Lager')
                    ->searchable(),
                TextColumn::make('targetStock.name')
                    ->label('Ziel-Lager')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('stopps_count')
                    ->label('Stopps')
                    ->numeric(),

                TextColumn::make('status.value')
                    ->label('Status')
                    ->badge()
                    ->color(function ($state, Trip $record) {
                        $name = $record->status?->name;
                        return match ($name) {
                            'open' => 'danger',
                            'pending' => 'warning',
                            'closed' => 'success',
                            'cancelled' => 'success',
                            default => 'gray',
                        };
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Erstellt am')
                    ->dateTime(format: 'd.m.Y H:i')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('geändert am')
                    ->dateTime(format: 'd.m.Y H:i')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label('gelöscht am')
                    ->dateTime(format: 'd.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('deleted_at')
                    ->label('gelöschte Märkte')
                    ->modifyBaseQueryUsing(function ($query) {
                        return $query->onlyTrashed();
                    }),
            ])
            ->headerActions([
                Action::make('create')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    ->url(fn(): string => route('trips.create'))
                    ->label('Neue Tour erstellen'),
            ])
            ->recordActions([
                Action::make('startTrip')
                    ->iconButton()
                    ->label('Tour starten')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->modalHeading('Tour starten')
                    ->modalDescription('Bitte Startzeit und Start-Kilometer eingeben.')
                    ->modalIcon('heroicon-o-play')
                    ->modalCancelActionLabel('Abbrechen')
                    ->modalSubmitActionLabel('Speichern')
                    ->form([
                        DateTimePicker::make('start')
                            ->label('Startzeit')
                            ->seconds(false)
                            ->default(now())
                            ->required(),
                        TextInput::make('start_km')
                            ->label('Start-Kilometer')
                            ->numeric()
                            ->minValue(0)
                            ->required(),
                    ])
                    ->action(function (Trip $record, array $data) {
                        $record->start = $data['start'];
                        $record->start_km = $data['start_km'];
                        $record->status_id = Status::where('name', 'pending')->value('id');
                        $record->save();
                    })
                    ->after(function () {
                        $this->resetTable();
                    })
                    ->successNotification(
                        Notification::make()
                            ->title('Tour gestartet')
                            ->body('Die Tour wurde gestartet.')
                            ->success()
                    ),
                Action::make('endTrip')
                    ->iconButton()
                    ->label('Tour beenden')
                    ->icon('heroicon-o-stop')
                    ->color('primary')
                    ->modalHeading('Tour beenden')
                    ->modalDescription('Bitte Endzeit und End-Kilometer eingeben.')
                    ->modalIcon('heroicon-o-stop')
                    ->modalCancelActionLabel('Abbrechen')
                    ->modalSubmitActionLabel('Speichern')
                    ->form([
                        DateTimePicker::make('end')
                            ->label('Endzeit')
                            ->seconds(false)
                            ->default(now())
                            ->required(),
                        TextInput::make('end_km')
                            ->label('End-Kilometer')
                            ->numeric()
                            ->default(fn(Trip $record) => (int)($record->start_km ?? 0) + (int)($record->plan_distance ?? 0))
                            ->minValue(fn(Trip $record) => $record->start_km ?? 0)
                            ->helperText(fn(Trip $record) => 'Startzeit: ' . ($record->start) . ' Start-Kilometer: ' . ($record->start_km ?? 0) . ', Plan-km: ' . ($record->plan_distance ?? 0))
                            ->required(),
                    ])
                    ->action(function (Trip $record, array $data) {
                        $record->end = $data['end'];
                        $record->end_km = $data['end_km'];
                        $startKm = (int)($record->start_km ?? 0);
                        $endKm = (int)$data['end_km'];
                        $record->distance_driven = max(0, $endKm - $startKm);
                        $record->status_id = Status::where('name', 'closed')->value('id');
                        $record->save();
                    })
                    ->after(function () {
                        $this->resetTable();
                    })
                    ->successNotification(
                        Notification::make()
                            ->title('Tour beendet')
                            ->body('Die Tour wurde erfolgreich beendet.')
                            ->success()
                    ),
                Action::make('edit')
                    ->iconButton()
                    ->icon('heroicon-o-pencil-square')
                    ->color('success')->url(fn(Trip $record): string => route('trips.edit', $record)),
                Action::make('delete')
                    ->iconButton()
                    ->requiresConfirmation()
                    ->modalHeading('Löschen bestätigen')
                    ->modalDescription('Soll der Markt gelöscht werden?')
                    ->modalCancelActionLabel('Abbrechen')
                    ->modalSubmitActionLabel('Löschen')
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->action(function (Store $record) {
                        $record->delete();
                    })
                    ->successNotification(
                        Notification::make()
                            ->title('Markt löschen')
                            ->body('Markt erfolgreich gelöscht')
                            ->success()
                    )
                    ->failureNotification(
                        Notification::make()
                            ->title('Markt löschen')
                            ->body('Löschen des Marktes fehlgeschlagen')
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
        return view('livewire.trips.list-trips');
    }
}
