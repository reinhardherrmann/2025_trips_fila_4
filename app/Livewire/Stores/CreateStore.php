<?php

namespace App\Livewire\Stores;

use App\Models\Address;
use App\Models\Store;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CreateStore extends Component implements HasActions, HasSchemas
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
                Section::make('Neuen Markt erstellen')
                    ->description('Erstellen eines neuen Marktes'),
                Tabs::make('Marktdaten')
                    ->tabs([
                        Tab::make('Allgemein')
                            ->columns(2)
                            ->schema([
                                Select::make('brand_id')
                                    ->label('Handelskette*')
                                    ->relationship('brand', 'name')
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'Bitte wählen Sie eine Handelskette aus.',]),
                                TextInput::make('cost_center_number')
                                    ->label('Marktnummer*')
                                    ->required()
                                    ->regex('/^\d{1,8}$/')
                                    ->validationMessages([
                                        'required' => 'Bitte geben Sie eine Marktnummer an.',
                                        'regex' => 'Die Marktnummer muss aus 1 bis 6 Ziffern bestehen.',
                                    ]),
                                TextInput::make('name')
                                    ->label('Marktname*')
                                    ->required()
                                    ->validationMessages([
                                        'reuired' => 'Bitte geben Sie einen Marktnamen an.'
                                    ]),
                                TextInput::make('phone_number')
                                    ->label('Telefonnummer')
                                    ->tel(),
                                Toggle::make('is_active')
                                    ->label('Aktiv')
                                    ->required(),
                                Toggle::make('has_ramp')
                                    ->label('Rampenmarkt')
                                    ->required(),


                            ]),
                        Tab::make('Adresse')
                            ->columns(2)
                            ->schema([
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
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'Bitte geben Sie eine Adresse an.'])
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Navigationsadresse')
                            ->columns(2)
                            ->schema([
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
                                    })
//                                    ->required()
//                                    ->validationMessages([
//                                        'required' => 'Bitte geben Sie eine Adresse an.'])
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Bild')
                            ->columns(2)
                            ->schema([
                                FileUpload::make('images')
                                    ->label('Bild')
                                    ->disk('public')
                                    ->directory('stores')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'])
                                    ->deletable(true)
                                    ->visibility('public'),
                            ]),
                        Tab::make('Erweitert')
                            ->columns(2)
                            ->schema([
                                Textarea::make('remark')
                                    ->label('Bemerkung')
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ])
            ->statePath('data')
            ->model(Store::class);
    }

    public function create()
    {
        $data = $this->form->getState();

        // images in JSON-String wandeln, falls Array (bei multiple)
//        if (isset($data['images']) && is_array($data['images'])) {
//            $data['images'] = json_encode($data['images']);
//        }

        $record = Store::create($data);

        $this->form->model($record)->saveRelationships();

        Notification::make()
            ->title('Neuen Markt erstellen')
            ->success()
            ->body("Markt " . $record->cost_center_number . " erfolgreich erstellt.")
            ->duration(2000)
            ->send();
        // Livewire-Redirector zurückgeben
        return $this->redirectRoute('stores.index');
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
                $this->redirectRoute('stores.index');
            });
    }

    public function render(): View
    {
        return view('livewire.stores.create-store');
    }
}
