<?php

namespace App\Livewire\Trips;

use App\Models\Address;
use App\Models\Trip;
use App\Models\Truck;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use App\Models\Store;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
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
    public $finished = false;

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

                Wizard::make([
                    Step::make('Allgemeines')
                        ->columns(2)
                        ->schema([
                            TextInput::make('trip_number')
                                ->label('Tournummer')
                                ->hint('Tournummer oder Name der Tour')
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
                            TextInput::make('plan_distance')
                                ->label('Km geplant')
                                ->numeric(),
                            TextInput::make('distance_driven')
                                ->label('gefahrene Distanz')
                                ->numeric()
                                ->disabled(),
                        ]),
                    Step::make('Zeiten')
                        ->columns(2)
                        ->schema([
                            TimePicker::make('duration')
                                ->label('Dauer')
                                ->required()
                                ->default('03:30'),
                            TimePicker::make('depot_vt')
                                ->label('Depotzeit vor Tour')
                                ->default('01:05'),
                            TimePicker::make('depot_nt')
                                ->label('Depotzeit nach Tour')
                                ->default('00:10'),
                        ]),
                    Step::make('Lager und LKW')
                        ->columns(2)
                        ->schema([
                            Select::make('start_stock_id')
                                ->label('Startlager')
                                ->placeholder('Bitte Startlager wählen')
                                ->relationship('startStock', 'name'),
                            Select::make('target_stock_id')
                                ->label('Ziellager')
                                ->placeholder('Bitte Ziellager wählen')
                                ->relationship('targetStock', 'name'),
                            Select::make('truck_id')
                                // ToDo: show only trucks to the current user
                                ->label('LKW')
                                ->placeholder('Bitte LKW wählen')
                                // Select Trucks only
                                ->options(Truck::query()->where('trucks.manufacturer_id', '!=', 3)->pluck('licence_plate', 'id')),
                            Select::make('trailer_id')
                                // ToDo: show only trucks to the current user
                                ->label('Trailer')
                                ->placeholder('Bitte Trailer wählen')
                                // Select Trucks only
                                ->options(Truck::query()->where('trucks.manufacturer_id', '=', 3)->pluck('licence_plate', 'id'))
                        ]),
                    Step::make('Stopps')
                        //->columns(2)
                        ->schema([
                            Repeater::make('stopps')
                                ->label('Stopps')
                                ->relationship('stopps')
                                ->orderable('order_number')
                                ->defaultItems(1)
                                ->minItems(1)
                                ->columns(2)
                                ->collapsible()
                                ->collapsed()
                                ->required()
                                ->schema([
                                    Select::make('store_id')
                                        ->label('Markt')
                                        ->relationship('store', 'name')
                                        ->getOptionLabelFromRecordUsing(function (\App\Models\Store $record): string {
                                            $num = trim((string)($record->cost_center_number ?? ''));
                                            $name = trim((string)($record->name ?? ''));
                                            $addr = '';
                                            $address = $record->relationLoaded('address') ? $record->address : $record->address()->first();
                                            if ($address) {
                                                $street = trim((string)($address->street ?? ''));
                                                $city = trim((string)(($address->postal_code ?? '') . ' ' . ($address->city ?? '')));
                                                $district = $address->district ? ' (' . $address->district . ')' : '';
                                                $addr = trim($street . ' — ' . $city . $district);
                                            }
                                            $left = $num !== '' ? '[' . $num . ']' : '';
                                            return trim($left . ' ' . $name . ($addr ? ' — ' . $addr : ''));
                                        })
                                        ->getSearchResultsUsing(function (string $search): array {
                                            return \App\Models\Store::query()
                                                ->with('address')
                                                ->where('name', 'like', "%{$search}%")
                                                ->orWhere('cost_center_number', 'like', "%{$search}%")
                                                ->orWhereHas('address', function ($q) use ($search) {
                                                    $q->where('street', 'like', "%{$search}%")
                                                        ->orWhere('district', 'like', "%{$search}%")
                                                        ->orWhere('city', 'like', "%{$search}%")
                                                        ->orWhere('postal_code', 'like', "%{$search}%");
                                                })
                                                ->orderBy('cost_center_number')
                                                ->limit(50)
                                                ->get()
                                                ->mapWithKeys(function ($s) {
                                                    $num = trim((string)($s->cost_center_number ?? ''));
                                                    $name = trim((string)($s->name ?? ''));
                                                    $addr = '';
                                                    $a = $s->address;
                                                    if ($a) {
                                                        $street = trim((string)($a->street ?? ''));
                                                        $city = trim((string)(($a->postal_code ?? '') . ' ' . ($a->city ?? '')));
                                                        $district = $a->district ? ' (' . $a->district . ')' : '';
                                                        $addr = trim($street . ' — ' . $city . $district);
                                                    }
                                                    $left = $num !== '' ? '[' . $num . ']' : '';
                                                    $label = trim($left . ' ' . $name . ($addr ? ' — ' . $addr : ''));
                                                    return [$s->getKey() => $label];
                                                })->toArray();
                                        })
                                        ->searchable()
                                        ->preload()
                                        //->required()
                                        ->columnSpanFull()
                                        ->createOptionForm([
                                            Select::make('brand_id')
                                                ->label('Handelskette*')
                                                ->relationship('brand', 'name')
                                                ->required(),
                                            TextInput::make('cost_center_number')
                                                ->label('Marktnummer*')
                                                ->required()
                                                ->regex('/^\d{1,8}$/'),
                                            TextInput::make('name')
                                                ->label('Marktname*')
                                                ->required(),
                                            TextInput::make('phone_number')
                                                ->label('Telefonnummer')
                                                ->tel(),
                                            Toggle::make('is_active')
                                                ->label('Aktiv')
                                                ->default(true)
                                                ->required(),
                                            Toggle::make('has_ramp')
                                                ->label('Rampenmarkt')
                                                ->default(false)
                                                ->required(),
                                            Select::make('address_id')
                                                ->label('Adresse*')
                                                ->searchable()
                                                ->preload()
                                                ->options(function () {
                                                    return \App\Models\Address::query()
                                                        ->orderBy('street')
                                                        ->limit(100)
                                                        ->get()
                                                        ->mapWithKeys(function ($a) {
                                                            $street = trim($a->street ?? '');
                                                            $city = trim(($a->postal_code ?? '') . ' ' . ($a->city ?? ''));
                                                            $district = $a->district ? " ({$a->district})" : '';
                                                            return [$a->getKey() => trim($street . ' — ' . $city . $district)];
                                                        })->toArray();
                                                })
                                                ->getSearchResultsUsing(function (string $search) {
                                                    return \App\Models\Address::query()
                                                        ->where('street', 'like', "%{$search}%")
                                                        ->orWhere('district', 'like', "%{$search}%")
                                                        ->orWhere('city', 'like', "%{$search}%")
                                                        ->orWhere('postal_code', 'like', "%{$search}%")
                                                        ->limit(50)
                                                        ->get()
                                                        ->mapWithKeys(function ($a) {
                                                            $street = trim($a->street ?? '');
                                                            $city = trim(($a->postal_code ?? '') . ' ' . ($a->city ?? ''));
                                                            $district = $a->district ? " ({$a->district})" : '';
                                                            return [$a->getKey() => trim($street . ' — ' . $city . $district)];
                                                        })->toArray();
                                                })
                                                ->createOptionForm([
                                                    TextInput::make('street')->label('Straße')->required(),
                                                    TextInput::make('postal_code')->label('PLZ')->required(),
                                                    TextInput::make('city')->label('Stadt')->required(),
                                                    TextInput::make('district')->label('Stadtteil'),
                                                ])
                                                ->createOptionAction(function ($action) {
                                                    return $action
                                                        ->modalHeading('Neue Adresse anlegen')
                                                        ->modalSubmitActionLabel('Adresse erstellen');
                                                })
                                                ->createOptionUsing(function (array $data): int {
                                                    return Address::query()->create($data)->getKey();
                                                })
                                                ->required(),
                                            Select::make('nav_id')
                                                ->label('Navigationsadresse')
                                                ->searchable()
                                                ->preload()
                                                ->options(function () {
                                                    return \App\Models\Address::query()
                                                        ->orderBy('street')
                                                        ->limit(100)
                                                        ->get()
                                                        ->mapWithKeys(function ($a) {
                                                            $street = trim($a->street ?? '');
                                                            $city = trim(($a->postal_code ?? '') . ' ' . ($a->city ?? ''));
                                                            $district = $a->district ? " ({$a->district})" : '';
                                                            return [$a->getKey() => trim($street . ' — ' . $city . $district)];
                                                        })->toArray();
                                                })
                                                ->getSearchResultsUsing(function (string $search) {
                                                    return \App\Models\Address::query()
                                                        ->where('street', 'like', "%{$search}%")
                                                        ->orWhere('district', 'like', "%{$search}%")
                                                        ->orWhere('city', 'like', "%{$search}%")
                                                        ->orWhere('postal_code', 'like', "%{$search}%")
                                                        ->limit(50)
                                                        ->get()
                                                        ->mapWithKeys(function ($a) {
                                                            $street = trim($a->street ?? '');
                                                            $city = trim(($a->postal_code ?? '') . ' ' . ($a->city ?? ''));
                                                            $district = $a->district ? " ({$a->district})" : '';
                                                            return [$a->getKey() => trim($street . ' — ' . $city . $district)];
                                                        })->toArray();
                                                })
                                                ->createOptionForm([
                                                    TextInput::make('street')->label('Straße')->required(),
                                                    TextInput::make('postal_code')->label('PLZ')->required(),
                                                    TextInput::make('city')->label('Stadt')->required(),
                                                    TextInput::make('district')->label('Stadtteil'),
                                                ])
                                                ->createOptionAction(function ($action) {
                                                    return $action
                                                        ->modalHeading('Neue Adresse anlegen')
                                                        ->modalSubmitActionLabel('Adresse erstellen');
                                                })
                                                ->createOptionUsing(function (array $data): int {
                                                    return Address::query()->create($data)->getKey();
                                                }),
                                        ])
                                        ->createOptionAction(function ($action) {
                                            return $action
                                                ->modalHeading('Neuen Markt anlegen')
                                                ->modalSubmitActionLabel('Markt erstellen');
                                        })
                                        ->createOptionUsing(function (array $data): int {
                                            return Store::query()->create($data)->getKey();
                                        }),
                                    Section::make('Details')
                                        ->collapsible()
                                        ->collapsed()
                                        ->columns(2)
                                        ->schema([
                                            DateTimePicker::make('arrival_time')->label('Ankunft')->seconds(false),
                                            DateTimePicker::make('departure_time')->label('Abfahrt')->seconds(false),
                                            Textarea::make('remark')->label('Bemerkung')->columnSpanFull(),
                                        ])->columnSpanFull(),

                                ]),
                        ]),
                    Step::make('Bemerkungen')
                        ->schema([
                            Textarea::make('remark')
                                ->label('Bemerkung'),
                        ]),
                    Step::make('Tourzeiten, Status')
                        ->columns(2)
                        ->schema([
                            DateTimePicker::make('start')
                                ->label('Tourstart'),
                            TextInput::make('start_km')
                                ->label('Abfahrtskilometerstand')
                                ->numeric(),
                            DateTimePicker::make('end')
                                ->label('Tourende'),
                            TextInput::make('end_km')
                                ->label('Ankunftskilometerstand')
                                ->numeric(),
                            TextInput::make('distance_driven')
                                ->label('gefahrene Distanz')
                                ->numeric()
                                ->disabled(),
                            SELECT::make('status_id')
                                ->label('Status')->label('Status')
                                ->relationship('status', 'value')
                                ->default(1)
                        ]),
                    Step::make('Störzeiten')
                        ->columns(2)
                        ->schema([
                            // ToDo Hier Repeater für Störzeiten
                        ]),
                ])
                    ->nextAction(
                        fn(Action $action) => $action->label('Weiter'),
                    )
                    ->previousAction(
                        fn(Action $action) => $action
                            ->label('Zurück')
                            ->color('gray'),
                    )



                // Hier sind die Tabs ...
                /*Tabs::make('Tourdaten')
                    ->tabs([
                        Tab::make('Allgemein')
                            ->columns(2)
                            ->schema([
                                TextInput::make('trip_number')
                                    ->label('Tournummer')
                                    ->hint('Tournummer oder Name der Tour')
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
                                TextInput::make('plan_distance')
                                    ->label('Km geplant')
                                    ->numeric(),
                                TextInput::make('distance_driven')
                                    ->label('gefahrene Distanz')
                                    ->numeric()
                                    ->disabled(),
                            ]),
                        Tab::make('Zeiten')
                            ->columns(2)
                            ->schema([
                                TimePicker::make('duration')
                                    ->label('Dauer')
                                    ->required()
                                    ->default('03:30'),
                                TimePicker::make('depot_vt')
                                    ->label('Depotzeit vor Tour')
                                    ->default('01:05'),
                                TimePicker::make('depot_nt')
                                    ->label('Depotzeit nach Tour')
                                    ->default('00:10'),
                            ]),
                        Tab::make('Lager und LKW')
                            ->columns(2)
                            ->schema([
                                Select::make('start_stock_id')
                                    ->label('Startlager')
                                    ->placeholder('Bitte Startlager wählen')
                                    ->relationship('startStock', 'name'),
                                Select::make('target_stock_id')
                                    ->label('Ziellager')
                                    ->placeholder('Bitte Ziellager wählen')
                                    ->relationship('targetStock', 'name'),
                                Select::make('truck_id')
                                    // ToDo: show only trucks to the current user
                                    ->label('LKW')
                                    ->placeholder('Bitte LKW wählen')
                                    // Select Trucks only
                                    ->options(Truck::query()->where('trucks.manufacturer_id', '!=', 3)->pluck('licence_plate', 'id')),
                                Select::make('trailer_id')
                                    // ToDo: show only trucks to the current user
                                    ->label('Trailer')
                                    ->placeholder('Bitte Trailer wählen')
                                    // Select Trucks only
                                    ->options(Truck::query()->where('trucks.manufacturer_id', '=', 3)->pluck('licence_plate', 'id'))

                            ]),
                        Tab::make('Stopps')
                            ->schema([
                                Repeater::make('stopps')
                                    ->label('Stopps')
                                    ->relationship('stopps')
                                    ->orderable('order_number')
                                    ->defaultItems(1)
                                    ->minItems(1)
                                    ->columns(2)
                                    ->collapsible()
                                    ->collapsed()
                                    ->schema([
                                        Select::make('store_id')
                                            ->label('Markt')
                                            ->relationship('store', 'name')
                                            ->getOptionLabelFromRecordUsing(function (\App\Models\Store $record): string {
                                                $num = trim((string)($record->cost_center_number ?? ''));
                                                $name = trim((string)($record->name ?? ''));
                                                $addr = '';
                                                $address = $record->relationLoaded('address') ? $record->address : $record->address()->first();
                                                if ($address) {
                                                    $street = trim((string)($address->street ?? ''));
                                                    $city = trim((string)(($address->postal_code ?? '') . ' ' . ($address->city ?? '')));
                                                    $district = $address->district ? ' (' . $address->district . ')' : '';
                                                    $addr = trim($street . ' — ' . $city . $district);
                                                }
                                                $left = $num !== '' ? '[' . $num . ']' : '';
                                                return trim($left . ' ' . $name . ($addr ? ' — ' . $addr : ''));
                                            })
                                            ->getSearchResultsUsing(function (string $search): array {
                                                return \App\Models\Store::query()
                                                    ->with('address')
                                                    ->where('name', 'like', "%{$search}%")
                                                    ->orWhere('cost_center_number', 'like', "%{$search}%")
                                                    ->orWhereHas('address', function ($q) use ($search) {
                                                        $q->where('street', 'like', "%{$search}%")
                                                            ->orWhere('district', 'like', "%{$search}%")
                                                            ->orWhere('city', 'like', "%{$search}%")
                                                            ->orWhere('postal_code', 'like', "%{$search}%");
                                                    })
                                                    ->orderBy('cost_center_number')
                                                    ->limit(50)
                                                    ->get()
                                                    ->mapWithKeys(function ($s) {
                                                        $num = trim((string)($s->cost_center_number ?? ''));
                                                        $name = trim((string)($s->name ?? ''));
                                                        $addr = '';
                                                        $a = $s->address;
                                                        if ($a) {
                                                            $street = trim((string)($a->street ?? ''));
                                                            $city = trim((string)(($a->postal_code ?? '') . ' ' . ($a->city ?? '')));
                                                            $district = $a->district ? ' (' . $a->district . ')' : '';
                                                            $addr = trim($street . ' — ' . $city . $district);
                                                        }
                                                        $left = $num !== '' ? '[' . $num . ']' : '';
                                                        $label = trim($left . ' ' . $name . ($addr ? ' — ' . $addr : ''));
                                                        return [$s->getKey() => $label];
                                                    })->toArray();
                                            })
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->columnSpanFull()
                                            ->createOptionForm([
                                                Select::make('brand_id')
                                                    ->label('Handelskette*')
                                                    ->relationship('brand', 'name')
                                                    ->required(),
                                                TextInput::make('cost_center_number')
                                                    ->label('Marktnummer*')
                                                    ->required()
                                                    ->regex('/^\d{1,8}$/'),
                                                TextInput::make('name')
                                                    ->label('Marktname*')
                                                    ->required(),
                                                TextInput::make('phone_number')
                                                    ->label('Telefonnummer')
                                                    ->tel(),
                                                Toggle::make('is_active')
                                                    ->label('Aktiv')
                                                    ->default(true)
                                                    ->required(),
                                                Toggle::make('has_ramp')
                                                    ->label('Rampenmarkt')
                                                    ->default(false)
                                                    ->required(),
                                                Select::make('address_id')
                                                    ->label('Adresse*')
                                                    ->searchable()
                                                    ->preload()
                                                    ->options(function () {
                                                        return \App\Models\Address::query()
                                                            ->orderBy('street')
                                                            ->limit(100)
                                                            ->get()
                                                            ->mapWithKeys(function ($a) {
                                                                $street = trim($a->street ?? '');
                                                                $city = trim(($a->postal_code ?? '') . ' ' . ($a->city ?? ''));
                                                                $district = $a->district ? " ({$a->district})" : '';
                                                                return [$a->getKey() => trim($street . ' — ' . $city . $district)];
                                                            })->toArray();
                                                    })
                                                    ->getSearchResultsUsing(function (string $search) {
                                                        return \App\Models\Address::query()
                                                            ->where('street', 'like', "%{$search}%")
                                                            ->orWhere('district', 'like', "%{$search}%")
                                                            ->orWhere('city', 'like', "%{$search}%")
                                                            ->orWhere('postal_code', 'like', "%{$search}%")
                                                            ->limit(50)
                                                            ->get()
                                                            ->mapWithKeys(function ($a) {
                                                                $street = trim($a->street ?? '');
                                                                $city = trim(($a->postal_code ?? '') . ' ' . ($a->city ?? ''));
                                                                $district = $a->district ? " ({$a->district})" : '';
                                                                return [$a->getKey() => trim($street . ' — ' . $city . $district)];
                                                            })->toArray();
                                                    })
                                                    ->createOptionForm([
                                                        TextInput::make('street')->label('Straße')->required(),
                                                        TextInput::make('postal_code')->label('PLZ')->required(),
                                                        TextInput::make('city')->label('Stadt')->required(),
                                                        TextInput::make('district')->label('Stadtteil'),
                                                    ])
                                                    ->createOptionAction(function ($action) {
                                                        return $action
                                                            ->modalHeading('Neue Adresse anlegen')
                                                            ->modalSubmitActionLabel('Adresse erstellen');
                                                    })
                                                    ->createOptionUsing(function (array $data): int {
                                                        return Address::query()->create($data)->getKey();
                                                    })
                                                    ->required(),
                                                Select::make('nav_id')
                                                    ->label('Navigationsadresse')
                                                    ->searchable()
                                                    ->preload()
                                                    ->options(function () {
                                                        return \App\Models\Address::query()
                                                            ->orderBy('street')
                                                            ->limit(100)
                                                            ->get()
                                                            ->mapWithKeys(function ($a) {
                                                                $street = trim($a->street ?? '');
                                                                $city = trim(($a->postal_code ?? '') . ' ' . ($a->city ?? ''));
                                                                $district = $a->district ? " ({$a->district})" : '';
                                                                return [$a->getKey() => trim($street . ' — ' . $city . $district)];
                                                            })->toArray();
                                                    })
                                                    ->getSearchResultsUsing(function (string $search) {
                                                        return \App\Models\Address::query()
                                                            ->where('street', 'like', "%{$search}%")
                                                            ->orWhere('district', 'like', "%{$search}%")
                                                            ->orWhere('city', 'like', "%{$search}%")
                                                            ->orWhere('postal_code', 'like', "%{$search}%")
                                                            ->limit(50)
                                                            ->get()
                                                            ->mapWithKeys(function ($a) {
                                                                $street = trim($a->street ?? '');
                                                                $city = trim(($a->postal_code ?? '') . ' ' . ($a->city ?? ''));
                                                                $district = $a->district ? " ({$a->district})" : '';
                                                                return [$a->getKey() => trim($street . ' — ' . $city . $district)];
                                                            })->toArray();
                                                    })
                                                    ->createOptionForm([
                                                        TextInput::make('street')->label('Straße')->required(),
                                                        TextInput::make('postal_code')->label('PLZ')->required(),
                                                        TextInput::make('city')->label('Stadt')->required(),
                                                        TextInput::make('district')->label('Stadtteil'),
                                                    ])
                                                    ->createOptionAction(function ($action) {
                                                        return $action
                                                            ->modalHeading('Neue Adresse anlegen')
                                                            ->modalSubmitActionLabel('Adresse erstellen');
                                                    })
                                                    ->createOptionUsing(function (array $data): int {
                                                        return Address::query()->create($data)->getKey();
                                                    }),
                                            ])
                                            ->createOptionAction(function ($action) {
                                                return $action
                                                    ->modalHeading('Neuen Markt anlegen')
                                                    ->modalSubmitActionLabel('Markt erstellen');
                                            })
                                            ->createOptionUsing(function (array $data): int {
                                                return Store::query()->create($data)->getKey();
                                            }),
                                        Section::make('Details')
                                            ->collapsible()
                                            ->collapsed()
                                            ->columns(2)
                                            ->schema([
                                                DateTimePicker::make('arrival_time')->label('Ankunft')->seconds(false),
                                                DateTimePicker::make('departure_time')->label('Abfahrt')->seconds(false),
                                                Textarea::make('remark')->label('Bemerkung')->columnSpanFull(),
                                            ])->columnSpanFull(),

                                    ]),
                            ]),
                        Tab::make('Bemerkung')
                            ->schema([
                                Textarea::make('remark')
                                    ->label('Bemerkung'),
                            ]),
                        Tab::make('Tourzeiten und Status')
                            ->columns(2)
                            ->schema([
                                DateTimePicker::make('start')
                                    ->label('Tourstart'),
                                TextInput::make('start_km')
                                    ->label('Abfahrtskilometerstand')
                                    ->numeric(),
                                DateTimePicker::make('end')
                                    ->label('Tourende'),
                                TextInput::make('end_km')
                                    ->label('Ankunftskilometerstand')
                                    ->numeric(),
                                TextInput::make('distance_driven')
                                    ->label('gefahrene Distanz')
                                    ->numeric()
                                    ->disabled(),
                                SELECT::make('status_id')
                                    ->label('Status')->label('Status')
                                    ->relationship('status', 'value')
                                    ->default(1)
                            ]),
                        Tab::make('Störzeiten')
                            ->columns(2)
                            ->schema([
                                // ToDo Hier Repeater für Störzeiten
                            ]),
                    ])*/

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
        $data['user_id'] = auth()->user()->id;

        //dd($data);

        $record = Trip::create($data);

        $this->form->model($record)->saveRelationships();

        Notification::make()
            ->title('Neue Tour erstellen')
            ->success()
            ->body("Tour " . $record->trip_number . " erfolgreich erstellt.")
            ->duration(2000)
            ->send();
        // Livewire-Redirector zurückgeben
        return $this->redirectRoute('trips.index');

    }

    public function render(): View
    {
        return view('livewire.trips.create-trip');
    }
}
