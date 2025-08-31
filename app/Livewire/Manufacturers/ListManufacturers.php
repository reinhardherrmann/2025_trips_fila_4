<?php

namespace App\Livewire\Manufacturers;

use App\Models\Brand;
use App\Models\Manufacturer;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ListManufacturers extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Übersicht der LKW Hersteller')
            ->description('Anzeige aller gespeicherten Hersteller')
            ->query(fn (): Builder => Manufacturer::query())
            ->columns([
                ImageColumn::make('image')
                ->label('Logo')
                ->imageHeight(30),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                ->sortable(),
                TextColumn::make('description')
                    ->label('Beschreibung')
                    ->limit(30)
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Erstellt am')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('updated_at')
                    ->label('geändert am')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('deleted_at')
                    ->modifyBaseQueryUsing(function ($query){
                        return $query->onlyTrashed();
                    })
            ])
            ->headerActions([
                Action::make('create')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    // TODO: Add correct Route to display the view form
                    //->url(fn(): string => route('brands.create'))
                    ->label('Neuen Hersteller erstellen'),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('edit')
                    ->iconButton()
                    ->icon('heroicon-o-pencil-square')
                    ->color('success')
                    // TODO: Add correct Route to display the view form
                   // ->url(fn(Manufacturer $record): string => route('brands.edit',$record))
                ,
                Action::make('delete')
                    ->iconButton()
                    ->requiresConfirmation()
                    ->modalHeading('Löschen bestätigen')
                    ->modalDescription('Soll der Hersteller gelöscht werden?')
                    ->modalCancelActionLabel('Abbrechen')
                    ->modalSubmitActionLabel('Löschen')
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->action(function (Manufacturer $record) {
                        $record->delete();
                    })
                    ->successNotification(
                        Notification::make()
                            ->title('Hersteller löschen')
                            ->body('Hersteller erfolgreich gelöscht')
                            ->success()
                    )
                    ->failureNotification(
                        Notification::make()
                            ->title('Hersteller löschen')
                            ->body('Löschen des Herstellers fehlgeschlagen')
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
        return view('livewire.manufacturers.list-manufacturers');
    }
}
