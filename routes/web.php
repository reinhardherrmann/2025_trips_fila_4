<?php

use App\Livewire\Brands\CreateBrand;
use App\Livewire\Brands\EditBrand;
use App\Livewire\Brands\ListBrands;
use App\Livewire\Manufacturers\CreateManufacturer;
use App\Livewire\Manufacturers\EditManufacturer;
use App\Livewire\Manufacturers\ListManufacturers;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Stores\CreateStore;
use App\Livewire\Stores\EditStore;
use App\Livewire\Stores\ListStores;
use App\Livewire\Trips\CreateTrip;
use App\Livewire\Trips\EditTrip;
use App\Livewire\Trips\ListTrips;
use App\Livewire\Trucks\CreateTruck;
use App\Livewire\Trucks\EditTruck;
use App\Livewire\Trucks\ListTrucks;
use App\Livewire\WorkRecords\ListWorkRecords;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Routes to manage brands
    Route::get('brands/', ListBrands::class)->name('brands.index');
    Route::get('edit-brand/{record}', EditBrand::class)->name('brands.edit');
    Route::get('create-brand', CreateBrand::class)->name('brands.create');

    // Routes to manage Work Records and Time Tables
    Route::get('work-records/', ListWorkRecords::class)->name('work-records.index');

    // Routes to manage Manufacturers
    Route::get('manufacturers/', ListManufacturers::class)->name('manufacturers.index');
    Route::get('create-manufacturer', CreateManufacturer::class)->name('manufacturers.create');
    Route::get('edit-manufacturer/{record}', EditManufacturer::class)->name('manufacturer.edit');

    // Routes to manage Trucks
    Route::get('trucks/', ListTrucks::class)->name('trucks.index');
    Route::get('create-truck', CreateTruck::class)->name('trucks.create');
    Route::get('edit-truck/{record}', EditTruck::class)->name('truck.edit');

    // Routes to manage Stores
    Route::get('stores/', ListStores::class)->name('stores.index');
    Route::get('stores/create', CreateStore::class)->name('stores.create');
    Route::get('stores/edit/{record}', EditStore::class)->name('stores.edit');

    // Routes to manage Trips
    Route::get('trips/', ListTrips::class)->name('trips.index');
    Route::get('trips/create', CreateTrip::class)->name('trips.create');
    Route::get('trips/edit/{record}', EditTrip::class)->name('trips.edit');

});

require __DIR__.'/auth.php';
