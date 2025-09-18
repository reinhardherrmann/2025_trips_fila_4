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
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Tabs as FormsTabs;
use Filament\Forms\Components\Tabs\Tab as FormsTab;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
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
                    ->url(fn(): string => route('manufacturers.create'))
                    ->label('Neuen Hersteller erstellen'),
            ])
            ->recordActions([
                Action::make('view')
                    ->iconButton()
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading('Hersteller anzeigen')
                    ->modalDescription('Anzeige der Herstellerdaten im Detail')
                    ->stickyModalHeader()
                    ->stickyModalFooter()
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Schließen')
                    ->form(function (Manufacturer $record): array {
                        return [
                            Tabs::make('Herstellerdaten')
                                ->tabs([
                                    Tab::make('Allgemein')
                                        ->columns(2)
                                        ->schema([
                                            Placeholder::make('name')
                                                ->label('Name')
                                                ->content($record->name ?? '—'),
                                            Placeholder::make('description')
                                                ->label('Beschreibung')
                                                ->content($record->description ?? '—'),
                                        ]),
                                    Tab::make('Bild')
                                        ->schema([
                                            Placeholder::make('image')
                                                ->label('Logo')
                                                ->content(function () use ($record) {
                                                    if (!$record->image) {
                                                        return 'kein Bild gespeichert.';
                                                    }

                                                    $url = Storage::url($record->image);

                                                    return new HtmlString('<img src="' . e($url) . '" alt="Logo" class="h-20 object-contain">');
                                                })
                                                ->columnSpanFull(),
                                        ]),
                                    Tab::make('Bemerkung')
                                        ->schema([
                                            Placeholder::make('remark')
                                                ->label('Bemerkung')
                                                ->content($record->remark ?? '—')
                                                ->columnSpanFull(),
                                        ]),
                                ]),
                        ];
                    })
                    ->modalActions([
                        Action::make('edit_truck')
                            ->label('LKW bearbeiten')
                            ->icon('heroicon-o-pencil-square')
                            ->color('success')
                            ->visible(fn(Manufacturer $record): bool => $record->trucks()->exists())
                            ->url(fn(Manufacturer $record): string => route('truck.edit', $record->trucks()->first())),
                        Action::make('close')
                            ->label('Schließen')
                            ->color('gray')
                            ->close(),
                    ]),
                Action::make('edit')
                    ->iconButton()
                    ->icon('heroicon-o-pencil-square')
                    ->color('info')
                    // TODO: Add correct Route to display the view form
                   ->url(fn(Manufacturer $record): string => route('manufacturer.edit',$record))
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
