<?php

namespace App\Livewire\Trips;

use App\Models\Address;
use App\Models\Trip;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CreateTrip extends Component implements HasActions, HasSchemas
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
                Section::make('Neue Tour erstellen')
                    ->description('Erstellen einer neuen Tour'),
                Tabs::make('Tourdaten')
                    ->tabs([
                        Tab::make('Allgemein')
                            ->columns(2)
                            ->schema([
                                TextInput::make('trip_number')
                                    ->label('Tournummer')
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'Die Tournummer ist ein Pflichtfeld.',
                                    ]),
                                Select::make('trip_type_id')
                                    ->label('Tourtyp')
                                    ->relationship('tripType', 'name')
                                    ->default(1)
                                    ->preload()
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'Der Tourtyp ist ein Pflichtfeld.',
                                    ]),
                                DatePicker::make('date')
                                    ->label('Datum')
                                    ->default(now())
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'Das Datum ist ein Pflichtfeld.',
                                    ]),
                                DatePicker::make('plan_date')
                                    ->label('Plandatum')
                                    ->default(now()),
                            ]),
                        Tab::make('Zeiten')
                            ->columns(2)
                            ->schema([]),

                    ])
//                Select::make('user_id')
//                    ->relationship('user', 'name')
//                    ->required(),
//                Select::make('start_stock_id')
//                    ->relationship('startStock', 'name'),
//                Select::make('target_stock_id')
//                    ->relationship('targetStock', 'name'),
//                TextInput::make('truck_id')
//                    ->numeric(),
//                TextInput::make('trip_type_id')
//                    ->numeric(),
//                TextInput::make('status_id')
//                    ->numeric(),
//                TextInput::make('trip_number')
//                    ->required(),
//                DatePicker::make('date')
//                    ->required(),
//                DatePicker::make('plan_date'),
//                TimePicker::make('duration'),
//                TimePicker::make('depot_vt'),
//                TimePicker::make('depot_nt'),
//                DateTimePicker::make('start'),
//                DateTimePicker::make('end'),
//                TextInput::make('plan_distance')
//                    ->numeric(),
//                TextInput::make('distance_driven')
//                    ->numeric(),
//                TextInput::make('start_km')
//                    ->numeric(),
//                TextInput::make('end_km')
//                    ->numeric(),
//                TextInput::make('status'),
//                Textarea::make('remark')
//                    ->columnSpanFull(),
            ])
            ->statePath('data')
            ->model(Trip::class);
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
                $this->redirectRoute('trips.index');
            });
    }

    public function create()
    {
        $data = $this->form->getState();

        $record = Trip::create($data);

        $this->form->model($record)->saveRelationships();

        Notification::make()
            ->title('Neue Tour erstellen')
            ->success()
            ->body("Tour " . $record->trip_number . " erfolgreich erstellt.")
            ->duration(2000)
            ->send();
        // Livewire-Redirector zurückgeben
        return $this->redirectRoute('trucks.index');

    }

    public function render(): View
    {
        return view('livewire.trips.create-trip');
    }
}
