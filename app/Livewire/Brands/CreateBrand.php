<?php

namespace App\Livewire\Brands;

use App\Models\Brand;
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

class CreateBrand extends Component implements HasActions, HasSchemas
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
                Section::make('Neue Handelskette erstellen')
                    ->description('Erstellen einer neuen Handelskette')
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('description'),
                        FileUpload::make('image')
                            ->label('Logo')
                            ->image()
                            ->disk('public')
                            ->directory('brands')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'])
                            ->deletable(true)
                            ->visibility('public'),
                        Textarea::make('remark')
                            ->columnSpanFull(),
                    ])

            ])
            ->statePath('data')
            ->model(Brand::class);
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


    public function create()
    {
        $data = $this->form->getState();

        //dd($data);
        $record = Brand::create($data);

        $this->form->model($record)->saveRelationships();

        Notification::make()
            ->title('Handelskette erstellen')
            ->success()
            ->body("Handelskette " . $record->name . " erfolgreich erstellt.")
            ->duration(2000)
            ->send();
        // Livewire-Redirector zurückgeben
        return $this->redirectRoute('brands.index');



    }

    public function render(): View
    {
        return view('livewire.brands.create-brand');
    }
}
