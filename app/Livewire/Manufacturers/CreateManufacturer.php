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

class CreateManufacturer extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Neuen Hersteller erstellen')
                    ->description('Erstellen eines neuen Herstellers')
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->minLength(3)
                            ->validationMessages([
                                'required' => 'Der Name ist ein Pflichtfeld.',
                                'minLength' => 'Der Name muss mindestens 3 Zeichen lang sein.'
                            ])
                            ->columnSpanFull(),
                        TextInput::make('description')
                            ->label('Beschreibung')
                            ->minLength(3)
                            ->columnSpanFull(),
                        FileUpload::make('image')
                            ->label('Logo')
                            ->disk('public')
                            ->directory('manufacturers')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'])
                            ->deletable(true)
                            ->visibility('public'),
                        Textarea::make('remark')
                            ->label('Bemerkung')
                            ->columnSpanFull(),
                    ])

            ])
            ->statePath('data')
            ->model(Manufacturer::class);
    }

    public function create()
    {
        $data = $this->form->getState();

        $record = Manufacturer::create($data);

        $this->form->model($record)->saveRelationships();

        Notification::make()
            ->title('Hersteller erstellen')
            ->success()
            ->body("Hersteller " . $record->name . " erfolgreich erstellt.")
            ->duration(2000)
            ->send();
        // Livewire-Redirector zurückgeben
        return $this->redirectRoute('manufacturers.index');
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

    public function render(): View
    {
        return view('livewire.manufacturers.create-manufacturer');
    }
}
