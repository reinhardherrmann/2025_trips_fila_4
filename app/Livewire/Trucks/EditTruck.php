<?php

namespace App\Livewire\Trucks;

use App\Models\Truck;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class EditTruck extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public Truck $record;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Fahrzeugdaten bearbeiten')
                    ->description('Ändern der Fahrzeugdaten'),

                Tabs::make('Fahrzeugdaten')
                    ->tabs([
                        Tab::make('Allgemein')
                            ->columns(2)
                            ->schema([
                                Select::make('manufacturer_id')
                                    ->label('Hersteller*')
                                    ->relationship('manufacturer', 'name')
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'Der Hersteller ist ein Pflichtfeld.',
                                    ]),
                                TextInput::make('type')
                                    ->label('Fahrzeugtyp*')
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'Der Fahrzeugtyp ist ein Pflichtfeld.',
                                    ]),
                                TextInput::make('truck_number')
                                    ->label('Fahrzeug-Nr.*')
                                    ->required()
                                    ->numeric()
                                    ->length(4)
                                    ->validationMessages([
                                        'required' => 'Die Fahrzeugnummer ist ein Pflichtfeld.',
                                        'numeric' => 'Es sind nur Ziffern erlaubt.'
                                    ]),
                                TextInput::make('licence_plate')
                                    ->label('Kennzeichen*')
                                    ->required()
                                    ->string()
                                    ->validationMessages([
                                        'required' => 'Das Kennzeichen ist ein Pflichtfeld.',
                                        'string' => 'Es sind nur Buchstaben und Zahlen erlaubt.'
                                    ]),
                                TextInput::make('capacity')
                                    ->label('Kapazität')
                                    ->required()
                                    ->numeric()
                                    ->validationMessages([
                                        'required' => 'Die Ladekapazität ist ein Pflichtfeld.',
                                        'numeric' => 'Es sind nur Ziffern erlaubt.'
                                    ]),
                                TextInput::make('weight')
                                    ->label('Gewicht in kg')
                                    ->numeric(),


                                TextInput::make('height')
                                    ->label('Höhe in cm')
                                    ->numeric(),
                                Select::make('trailer_id')
                                    ->label('Trailer-Nummer')
                                    ->relationship('trailer', 'licence_plate')
                                    ->preload()
                                    ->searchable()
                                    ->getOptionLabelFromRecordUsing(function (Truck $record): string {
                                        $num = $record->truck_number ? ('#' . $record->truck_number) : '';
                                        $lp = $record->licence_plate ?? '';
                                        return trim($num . ' ' . $lp);
                                    }),
                                Toggle::make('is_active')
                                    ->label('Aktiv')
                                    ->default(true)
                                    ->required(),
                            ]),
                        Tab::make('Erweiter')
                            ->schema([
                                Textarea::make('remark')
                                    ->label('Bemerkung')
                                    ->columnSpanFull(),]),
                        Tab::make('Bild')
                            ->schema([
                                FileUpload::make('image')
                                    ->image(),
                            ]),
                    ]),


            ])
            ->statePath('data')
            ->model($this->record);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $this->record->update($data);
    }

    public function render(): View
    {
        return view('livewire.trucks.edit-truck');
    }
}
