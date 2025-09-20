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
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section as FormsSection;
use Filament\Forms\Components\Tabs as FormsTabs;
use Filament\Forms\Components\Tabs\Tab as FormsTab;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
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
            ->query(fn(): Builder => Truck::query()) //->where('manufacturer_id', '!=', 3))
            ->columns([
                ImageColumn::make('image')
                    ->label('Bild'),
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
                TernaryFilter::make('trailer_toggle')
                    ->label('Anzeige')
                    ->trueLabel('Nur LKW')
                    ->falseLabel('Nur Trailer')
                    ->placeholder('Alle')
                    ->default('false')
                    ->queries(
                        false: fn(Builder $query) => $query->where('manufacturer_id', 3),
                        true: fn(Builder $query) => $query->where('manufacturer_id', '!=', 3),
                        blank: fn(Builder $query) => $query,
                    )
//                Filter::make('manufacturer_id')
//                    ->label('Nur Trailer anzeigen')
//                    ->modifyBaseQueryUsing(function ($query) {
//                        Truck::query()->where('manufacturer_id', '=', 3);
//                    })
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
                    ->color('info')
                    ->modalHeading('Fahrzeug anzeigen')
                    ->modalDescription('Anzeige der Fahrzeugdaten im Detail')
                    ->stickyModalHeader()
                    ->stickyModalFooter()
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Schließen')
                    ->form(function (Truck $record): array {
                        $trailerLabel = $record->trailer
                            ? trim(($record->trailer->truck_number ? ('#' . str_pad((string)$record->trailer->truck_number, 4, '0', STR_PAD_LEFT)) : '') . ' ' . ($record->trailer->licence_plate ?? ''))
                            : '—';

                        return [
//                            Section::make('Fahrzeugdaten')
//                                ->description('Gleiche Struktur wie im Bearbeiten-Dialog'),
                            Tabs::make('Fahrzeugdaten')
                                ->tabs([
                                    Tab::make('Allgemein')
                                        ->columns(2)
                                        ->schema([
                                            Placeholder::make('manufacturer_name')
                                                ->label('Hersteller')
                                                ->content($record->manufacturer?->name ?? '—'),
                                            Placeholder::make('type')
                                                ->label('Fahrzeugtyp')
                                                ->content($record->type ?? '—'),
                                            Placeholder::make('truck_number')
                                                ->label('Fahrzeug-Nr.')
                                                ->content($record->truck_number !== null ? str_pad((string)$record->truck_number, 4, '0', STR_PAD_LEFT) : '—'),
                                            Placeholder::make('licence_plate')
                                                ->label('Kennzeichen')
                                                ->content($record->licence_plate ?? '—'),
                                            Placeholder::make('capacity')
                                                ->label('Kapazität')
                                                ->content($record->capacity !== null ? (string)$record->capacity : '—'),
                                            Placeholder::make('weight')
                                                ->label('Gewicht in kg')
                                                ->content($record->weight !== null ? (string)$record->weight : '—'),
                                            Placeholder::make('height')
                                                ->label('Höhe in cm')
                                                ->content($record->height !== null ? (string)$record->height : '—'),
                                            Placeholder::make('trailer_id')
                                                ->label('Trailer-Nummer')
                                                ->content($trailerLabel),
                                            Placeholder::make('is_active')
                                                ->label('Aktiv')
                                                ->content($record->is_active ? 'Ja' : 'Nein'),
                                        ]),
                                    Tab::make('Erweitert')
                                        ->schema([
                                            Placeholder::make('remark')
                                                ->label('Bemerkung')
                                                ->content($record->remark ?? '—')
                                                ->columnSpanFull(),
                                        ]),
                                    Tab::make('Bild')
                                        ->schema([
                                            Placeholder::make('image')
                                                ->label('Logo')
                                                ->content(function () use ($record) {
                                                    if (!$record->image) {
                                                        return 'kein Bild gespeichert!';
                                                    }

                                                    $url = Storage::url($record->image);

                                                    return new HtmlString('<img src="' . e($url) . '" alt="LKW Bild" class="h-20 object-contain">');
                                                })
                                                ->columnSpanFull(),
                                        ]),
                                ]),
                        ];
                    })
                    ->modalActions([
                        Action::make('edit')
                            ->label('Bearbeiten')
                            ->icon('heroicon-o-pencil-square')
                            ->color('success')
                            ->url(fn(Truck $record): string => route('truck.edit', $record)),
                        Action::make('close')
                            ->label('Schließen')
                            ->color('gray')
                            ->close(),
                    ]),

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
                    ->action(function (Truck $record) {
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
