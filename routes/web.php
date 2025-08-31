<?php

use App\Livewire\Brands\CreateBrand;
use App\Livewire\Brands\EditBrand;
use App\Livewire\Brands\ListBrands;
use App\Livewire\Manufacturers\ListManufacturers;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
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
    Route::get('manage-brands', ListBrands::class)->name('brands.index');
    Route::get('brands/', ListBrands::class)->name('brands.index');
    Route::get('edit-brand/{record}', EditBrand::class)->name('brands.edit');
    Route::get('create-brand', CreateBrand::class)->name('brands.create');
    Route::get('manufacturers/', ListManufacturers::class)->name('manufacturers.index');

});

require __DIR__.'/auth.php';
