<?php

namespace App\Livewire\Brands;

use App\Models\Brand;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
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

class ListBrands extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Übersicht der Handelsketten')
            ->description('Anzeige aller gespeicherten Handelsketten')
            ->query(fn(): Builder => Brand::query())
            ->striped()
            ->columns([
                ImageColumn::make('image')
                    ->label('Logo')
                    ->imageHeight(30),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->limit(30)
                    ->wrap()
                    ->searchable()
                    ->label('Beschreibung'),
                TextColumn::make('remark')
                    ->label('Bemerkung')
                    ->limit(50)
                    ->wrap(),
                TextColumn::make('created_at')
                    ->dateTime(format: 'd.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime(format: 'd.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('deleted_at')
                    ->modifyBaseQueryUsing(function ($query) {
                        return $query->onlyTrashed();
                    })
                // ...
            ])
            ->headerActions([
                Action::make('create')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    // TODO: Add correct Route to display the view form
                    ->url(fn(): string => route('brands.create'))
                    ->label('Neue Handelskette'),
            ])
            ->recordActions([
                Action::make('view')
                    ->iconButton()
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading('Handelskette anzeigen')
                    ->modalDescription('Anzeige der Handelsketten-Daten im Detail')
                    ->stickyModalHeader()
                    ->stickyModalFooter()
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Schließen')
                    ->form(function (Brand $record): array {
                        return [
                            Tabs::make('Handelskette')
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
                        Action::make('edit')
                            ->label('Bearbeiten')
                            ->icon('heroicon-o-pencil-square')
                            ->color('success')
                            ->url(fn(Brand $record): string => route('brands.edit', $record)),
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
                    ->url(fn(Brand $record): string => route('brands.edit', $record))
                ,
                Action::make('delete')
                    ->iconButton()
                    ->requiresConfirmation()
                    ->modalHeading('Löschen bestätigen')
                    ->modalDescription('Soll die Handelskette gelöscht werden?')
                    ->modalCancelActionLabel('Abbrechen')
                    ->modalSubmitActionLabel('Löschen')
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->action(function (Brand $record) {
                        $record->delete();
                    })
                    ->successNotification(
                        Notification::make()
                            ->title('Handelskette löschen')
                            ->body('Handelskette erfolgreich gelöscht')
                            ->success()
                    )
                    ->failureNotification(
                        Notification::make()
                            ->title('Handelskette löschen')
                            ->body('Löschen der Handelskette fehlgeschlagen')
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
        return view('livewire.brands.list-brands');
    }
}
