<?php

namespace App\Livewire\Manufacturers;

use App\Models\Manufacturer;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use function Filament\Support\get_model_label;

class EditManufacturer extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public Manufacturer $record;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Hersteller bearbeiten')
                    ->description('Bearbeiten des Herstellers')
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->minLength(3)
                            ->validationMessages([
                                'required' => 'Der Name ist ein Pflichtfeld.',
                                'minLength' => 'Der Name muss mindestens 3 Zeichen lang sein.'
                            ]),
                        Textarea::make('description')
                            ->label('Beschreibung')
                            ->columnSpanFull(),
                        FileUpload::make('image')
                            ->label('Logo')
                            ->directory('manufacturers')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'])
                            ->deletable(true)
                            ->image(),
                        Textarea::make('remark')
                            ->label('Bemerkung')
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
                $this->redirectRoute('manufacturers.index');
            });
    }

    public function save()
    {
        $data = $this->form->getState();

        $this->record->update($data);

        Notification::make()
            ->title('Herstellerdaten ändern')
            ->success()
            ->body("Hersteller " . $this->record->name . " erfolgreich geändert")
            ->duration(2000)
            ->send();
        // Livewire-Redirector zurückgeben
        return $this->redirectRoute('manufacturers.index');

    }

    public function render(): View
    {
        return view('livewire.manufacturers.edit-manufacturer');
    }
}
