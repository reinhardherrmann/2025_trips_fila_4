<?php

namespace App\Livewire\Trucks;

use App\Models\Manufacturer;
use App\Models\Truck;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use function Livewire\Volt\form;

class ListTrucks extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
    {
        return $table
            ->heading("Übersicht der LKW und Trailer")
            ->description("Anzeige aller gespeicherten LKW und Trailer")
            // Base query shows all trucks; use filter to show only manufacturer_id = 3 when desired
            ->query(fn(): Builder => Truck::query()->where('manufacturer_id', '!=', 3))
            ->columns([
                ImageColumn::make('image'),
                TextColumn::make('manufacturer.name')
                    ->label('Hersteller')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Typ')
                    ->searchable(),
                TextColumn::make('truck_number')
                    ->label('Fahrzeug-Nr.')
                    ->numeric()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('licence_plate')
                    ->label('Kennzeichen')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('capacity')
                    ->label('Kapazität')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('trailer.licence_plate')
                    ->label('Trailer-Nr.')
                    ->numeric()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label('Aktiv')
                    ->boolean(),
                TextColumn::make('deleted_at')
                    ->label('Gelöscht am')
                    ->dateTime(format: 'd.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Erstellt am')
                    ->dateTime(format: 'd.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Geändert am:')
                    ->dateTime(format: 'd.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('deleted_at')
                    ->label('gelöschte Fahrzeuge')
                    ->modifyBaseQueryUsing(function ($query){
                        return $query->onlyTrashed();
                    }),
                Filter::make('manufacturer_id')
                    ->label('Nur Trailer anzeigen')
                    ->modifyBaseQueryUsing(function ($query) {
                        return $query->where('manufacturer_id', '=', 3);
                    })
            ])
            ->headerActions([
                Action::make('create')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    ->url(fn(): string => route('trucks.create'))
                    ->label('Neues Fahrzeug erstellen'),
            ])
            ->recordActions([
                Action::make('view')
                    ->iconButton()
                    ->icon('heroicon-o-eye')
                    ->color('info'),
                Action::make('edit')
                    ->iconButton()
                    ->icon('heroicon-o-pencil-square')
                    ->color('success')
                    // TODO: Add correct Route to display the view form
                    ->url(fn(Truck $record): string => route('truck.edit', $record))
                ,
                Action::make('delete')
                    ->iconButton()
                    ->requiresConfirmation()
                    ->modalHeading('Löschen bestätigen')
                    ->modalDescription('Soll das Fahrzeug gelöscht werden?')
                    ->modalCancelActionLabel('Abbrechen')
                    ->modalSubmitActionLabel('Löschen')
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->action(function (Manufacturer $record) {
                        $record->delete();
                    })
                    ->successNotification(
                        Notification::make()
                            ->title('Fahrzeug löschen')
                            ->body('Fahrzeug erfolgreich gelöscht')
                            ->success()
                    )
                    ->failureNotification(
                        Notification::make()
                            ->title('Fahrzeug löschen')
                            ->body('Löschen des Fahrzeugs fehlgeschlagen')
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
        return view('livewire.trucks.list-trucks');
    }
}
