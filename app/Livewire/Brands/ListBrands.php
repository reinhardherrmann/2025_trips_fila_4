<?php

namespace App\Livewire\Brands;

use App\Models\Brand;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
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
                    ->label('Logo'),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->limit(50)
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
                //
            ])
            ->headerActions([
                Action::make('create')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    // TODO: Add correct Route to display the view form
                    // ->url(fn(): string => route('brands.create'))
                    ->label('Handelskette erstellen'),
            ])
            ->recordActions([
                Action::make('view')
                    ->iconButton()
                    ->icon('heroicon-o-eye')
                    ->color('info')
                // TODO: Add correct Route to display the view form
                //->url(fn(): string => route('brands.edit'))
                ,
                Action::make('edit')
                    ->iconButton()
                    ->icon('heroicon-o-pencil-square')
                    ->color('success')
                // TODO: Add correct Route to display the view form
                ->url(fn(Brand $record): string => route('brands.edit',$record))
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
