<?php

namespace App\Livewire\Stores;

use App\Models\Store;
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

class ListStores extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
    {
        return $table
            ->heading("Übersicht der Märkte")
            ->description("Anzeige aller gespeicherten Märkte")
            ->query(fn(): Builder => Store::query()->with(['brand', 'address', 'navAddress']))
            ->columns([
                ImageColumn::make('brand.image')
                    ->label('Kette'),
                TextColumn::make('cost_center_number')
                    ->label('Marktnummer')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('phone_number')
                    ->label('Telefonnummer')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label('Aktiv')
                    ->boolean(),
                IconColumn::make('has_ramp')
                    ->label('Rampenmarkt')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Erstellt am')
                    ->dateTime(format: 'd.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Aktualisiert am')
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
                    //->url(fn(): string => route('trucks.create'))
                    ->label('Neuen Markt erstellen'),
            ])
            ->recordActions([
                ViewAction::make(),

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
        return view('livewire.stores.list-stores');
    }
}
