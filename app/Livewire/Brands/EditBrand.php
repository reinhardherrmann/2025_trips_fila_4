<?php

namespace App\Livewire\Brands;

use App\Models\Brand;
use Filament\Actions\Action;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\Alignment;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class EditBrand extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public Brand $record;

    public ?array $data = [];

    public function mount(): void
    {
        // Fill form data but handle image field specially
        $formData = $this->record->attributesToArray();

        // Don't include image in initial fill, let it load separately
        //unset($formData['image']);

        $this->form->fill($formData);

        //$this->form->fill($this->record->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {

        return $schema
            ->components([
                Section::make('Handelskette bearbeiten')
                    ->description('Bearbeiten der Handelskette')
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('description'),
                        FileUpload::make('image')
                            ->label('Logo')
                            ->directory('brands')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'])
                            ->deletable(true)
                            ->image()
                        ,
                        Textarea::make('remark')
                            ->columnSpanFull(),
                    ])

            ])
            ->statePath('data')
            ->model($this->record);
    }

    public function cancelAction(): Action
    {
        return Action::make('cancel')
            ->label('Abbruch')
            ->color('danger')
            ->icon('heroicon-o-x-circle')
            ->requiresConfirmation()
            ->modalHeading('Vorgang abbrechen?')
            ->modalDescription('Nicht gespeicherte Änderungen gehen verloren. Möchten Sie wirklich abbrechen?')
            ->modalSubmitActionLabel('Ja, abbrechen')
            ->modalCancelActionLabel('Weiter bearbeiten')
            ->action(function (): void {
                $this->redirectRoute('brands.index');
            });
    }

    public function save()
    {
        $data = $this->form->getState();

        $this->record->update($data);

        // Flash-Message setzen, statt ->with() zu verwenden
        // session()->flash('success', 'Handelskette erfolgreich geändert');

        Notification::make()
            ->title('Handelskette ändern')
            ->success()
            ->body("Handelskette " . $this->record->name . " erfolgreich geändert")
            ->duration(2000)
            ->send();
        // Livewire-Redirector zurückgeben
        return $this->redirectRoute('brands.index');


    }

    public function render(): View
    {
        return view('livewire.brands.edit-brand');
    }
}
