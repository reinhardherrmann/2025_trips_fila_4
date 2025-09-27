<?php

namespace App\Livewire\Trips;

use App\Models\Store;
use App\Models\Trip;
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
                    ->sortable(),
                TextColumn::make('startStock.name')
                    ->label('Start-Lager')
                    ->searchable(),
                TextColumn::make('targetStock.name')
                    ->label('Ziel-Lager')
                    ->searchable(),
                TextColumn::make('stopps_count')
                    ->label('Stopps')
                    ->numeric(),

                TextColumn::make('status')
                    ->label('Status')
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
