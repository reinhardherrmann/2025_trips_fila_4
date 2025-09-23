<?php

namespace App\Livewire\Stores;

use App\Models\Store;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Support\HtmlString;
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
                ImageColumn::make('images')
                    ->label('Bild')
                    ->imageHeight(30)
                    ->disk('public'),
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
                    ->url(fn(): string => route('stores.create'))
                    ->label('Neuen Markt erstellen'),
            ])
            ->recordActions([
                Action::make('view')
                    ->iconButton()
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading('Markt anzeigen')
                    ->modalDescription('Anzeige der Marktdaten im Detail')
                    ->stickyModalHeader()
                    ->stickyModalFooter()
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Schließen')
                    ->form(function (Store $record): array {
                        $address = $record->address;
                        $addressLabel = $address ? trim(($address->street ?? '') . ' — ' . trim(($address->postal_code ?? '') . ' ' . ($address->city ?? '')) . ($address->district ? ' (' . $address->district . ')' : '')) : '—';

                        $nav = $record->navAddress;
                        $navLabel = $nav ? trim(($nav->street ?? '') . ' — ' . trim(($nav->postal_code ?? '') . ' ' . ($nav->city ?? '')) . ($nav->district ? ' (' . $nav->district . ')' : '')) : '—';

                        return [
                            Tabs::make('Marktdaten')
                                ->tabs([
                                    Tab::make('Allgemein')
                                        ->columns(2)
                                        ->schema([
                                            Placeholder::make('brand_name')
                                                ->label('Handelskette')
                                                ->content($record->brand?->name ?? '—'),
                                            Placeholder::make('cost_center_number')
                                                ->label('Marktnummer')
                                                ->content($record->cost_center_number ?? '—'),
                                            Placeholder::make('name')
                                                ->label('Marktname')
                                                ->content($record->name ?? '—'),
                                            Placeholder::make('phone_number')
                                                ->label('Telefonnummer')
                                                ->content($record->phone_number ?? '—'),
                                            Placeholder::make('is_active')
                                                ->label('Aktiv')
                                                ->content($record->is_active ? 'Ja' : 'Nein'),
                                            Placeholder::make('has_ramp')
                                                ->label('Rampenmarkt')
                                                ->content($record->has_ramp ? 'Ja' : 'Nein'),
                                        ]),
                                    Tab::make('Adresse')
                                        ->schema([
                                            Placeholder::make('address')
                                                ->label('Adresse')
                                                ->content($addressLabel)
                                                ->columnSpanFull(),
                                        ]),
                                    Tab::make('Navigationsadresse')
                                        ->schema([
                                            Placeholder::make('nav_address')
                                                ->label('Navigationsadresse')
                                                ->content($navLabel)
                                                ->columnSpanFull(),
                                        ]),
                                    Tab::make('Bild')
                                        ->schema([
                                            Placeholder::make('images')
                                                ->label('Bild')
                                                ->content(function () use ($record) {
                                                    $images = $record->images;
                                                    if (!$images) {
                                                        return 'kein Bild gespeichert!';
                                                    }
                                                    $images = is_array($images) ? $images : [$images];
                                                    $html = '';
                                                    foreach ($images as $path) {
                                                        if (!$path) {
                                                            continue;
                                                        }
                                                        $url = Storage::url($path);
                                                        $html .= '<img src="' . e($url) . '" alt="Store Bild" class="h-20 object-contain inline-block mr-2">';
                                                    }
                                                    return new HtmlString($html ?: 'kein Bild gespeichert!');
                                                })
                                                ->columnSpanFull(),
                                        ]),
                                    Tab::make('Erweitert')
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
                        Action::make('edit')
                            ->label('Bearbeiten')
                            ->icon('heroicon-o-pencil-square')
                            ->color('success')
                            ->visible(fn() => RouteFacade::has('stores.edit'))
                            ->url(fn(Store $record): string => route('stores.edit', $record)),
                        Action::make('close')
                            ->label('Schließen')
                            ->color('gray')
                            ->close(),
                    ]),

                Action::make('edit')
                    ->iconButton()
                    ->icon('heroicon-o-pencil-square')
                    ->color('success')
                    ->url(fn(Store $record): string => route('stores.edit', $record)),

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
