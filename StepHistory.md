# Step History To List Changes Made over time

## Step Main Bugfix

Sources: Junie

- fixed bug that Navlist "Grundtabellen in son collapsed by default"
    - adding "expandable :expanded="false" to Navlist
- Bug Dashboard doesn't show open Trips fixed with:
    - correct query to show Trips with status_id <= 2
---
## Step 29 Create Modals to Start and End Shift
Sources: none and support of Junie
- created Modals as first step. needs to be improved.
---
## Step 28 Create ListWorkRecords to display worked shifts
Sources: none and support ofJunie
- using command "php artisan make:filament-livewire-table WorkRecords/ListWorkRecords --generate"
- add Route to display ListWorkRecords
- add Link to sidebar
- add HeaderAction and several RowActions
- add Softdelete to table
- add filter to show deleted Rows
- add filterable Select Field to search for Shifts
- fixed some bugs:
    - sorted tripsList initially by date and created_at
    - added TripsPerDay to dashboard doesn't work in Prod
    - changed icons to start and end Trip to solid
  - changed layout of trips-kpi.blade
---
## Step 27 Create Migrations and Models for Shift Plan and WorkRecords
Sources: https://claude.ai/chat/85e49efb-d39a-47a8-8fcf-507e0b1fcabf
- create the following Models with Migrations:
    - shift_types_table, work_records_table absences_table
- defined all fields and Soft deletes
- created the Relationships:
- run the migrations
- added shifts to ShiftTypesSeeder
- run ShiftTypeSeeder to create ShiftTypes
---

## Step 26 Install Widget and Laradumps globally
Sources: https://laradumps.dev/get-started/installation.html?id=desktop-app#laravel-package
and Junie
- cretaed Widget to show number of trips for the last 7 days
- created Widget to show open trips
- installed laradumps globally
- added link to trip to edit open Trip
- fixed bug in EditTrip that caused incorrect Title when saving trip
---

## Step 25 Add Component to edit Trip

Sources: none

- create Route to edit trip
- create Component EditTrip using command "php artisan make:filament-livewire-form Trips/EditTrip --edit"
- add Route to display EditTrip
- add Form to EditTrip
- add Tabs to better organize Form
- add Validation with custom Messages
- add Flashmessage for Success and Error
- add Button to Save and Cancel Edit
- add Confirmation for Cancelling Creation
- added Filter to ListTrips to only show Trips with softDeletes = true
- added function to Trip Model to calculate drivendistance before saving

---

## Step 24 Add Buttons to start end End Trip
Sources: none
- add Column status_id to trips table
- add status_id to Trip Model
- added Select for Trip's Status when creating a Trip
- changed status Column in ListTrips to show status
- added Button to start Trip
- changed Status to be displayed as badge
- added button to end trip

---

## Step 23 Create Component CreateTrip
Sources: none
- use command "php artisan make:filament-livewire-form Trips/CreateTrip --generate"
- add Route to display CreateTrip
- add Form to CreateTrip
- add Validation
- add Flashmessage for Success and Error
- add Button to Save and Cancel Edit
- add Confirmation for Cancelling Edit or Create
- added Filter to ListTrips to only show Trips with softDeletes = true
- added Relation for TripTypes
- added Migration to delete Column Field from table stopps
- run migration
- tested with 2 Users, can only see ohn trips
---
## Step 22 Create Component to List Trips with stopps
Sources: none
- use command "php artisan make:filament-livewire-table Trips/ListTrips --generate"
- add Route to display ListTrips
- correct the Relationships for Trips
- Add SoftDeletes to Trip Model and Create Migration
- create Headers for List
- created RowActions and Heading for Table

---

## Step 23 Define Trip, Stopp, Store, and Stock relationships

Sources: Junie

- Trip: added belongsTo relationships startStock() and targetStock() to Stock via start_stock_id and target_stock_id. ✓
- Verified Stopp already has trip() and store() belongsTo relationships (each Stop has one Store). ✓
- No UI changes required. ✓

---

## Step 24 Restrict Trips list to authenticated user

Sources: Junie

- Updated ListTrips table base query to filter by auth()->id() and eager-load user, startStock, and targetStock. ✓
- Ensures only trips belonging to the logged-in user are displayed. ✓
- Routes already have auth + verified middleware, so no further changes required. ✓

---

## Step 25 Add soft deletes to trips table

Sources: Junie

- Created migration 2025_09_23_092400_add_soft_deletes_to_trips_table.php adding deleted_at column with $table->
  softDeletes(). ✓
- Trip model already uses SoftDeletes trait, so no model changes required. ✓
- ListTrips already includes a deleted_at column; optional filters or restore actions can be added later if needed. ✓

---
## Step 21 Create Component EditStore
Sources: none
- using command "php artisan make:filament-livewire-form Stores/EditStore --edit"
- Add Component Trucks/EditTruck
- Add Route to display EditTruck
- Add Form to EditTruck
- Add Tabs to better organize Form
- Add Validation with custom Messages
- Add Flashmessage for Success and Error
- Add Button to Save and Cancel Edit
- Add Confirmation for Cancelling Creation
---
## Step 20 Edit Component CreateStore To Store Images
Sources: none
- created Component CreateStore
- added path to store images
- added Notification for success and error
- added button to save and cancel edit
- added Confirmation for Cancelling Edit or Create
- published livewire confirmation
- added cast to Model store to save images
- changes Liststores to display image of Store
- added Action to View Store in a Modal

---

## Step 19 Create Component CreateStore
Sources: none
- using command "php artisan make:filament-livewire-form Stores/CreateStore --generate"
- Add Route to display CreateStore
- Add Form to CreateStore
- Add Validation
- Add Flash Message for Success and Error
- Add Button to Save and Cancel Edit
- Add Confirmation for Cancelling Create

---
## Step 18 Create Component to List all Stores
Sources: none
- create raw Component
- add Route to navigate to List
- add Link to Sidebar
- add HeaderAction and several RowActions
- add Softdelete to table
- add filter to show deleted Rows
- add filterable Select Field to search for Addresses and
- if not exists to add new aAddress and connect to Store
- Add the same to generate a NavAddress
---
## Step 17 Make Modal to Show Manufacturer and Brand Data
Sources: Junie
- Changed ViewAction accordingly to display data and Image for
    - Manufacturers
    - Brands
-
---
## Step 16 Make Component to view Truck Data

Sources: Junie

- make ViewAction as Modal
- show all data in modal
- add Link to edit shown Dataset

---
## Step 15 Make Filter to List Trucks, or Trailers or all
Sources: none
- Changed query to display Trucks
- added TernaryFilter to ListTrucks:
    - show all Trucks if no filter is selected
    - show Trucks only as default
    - show Trailers, if wanted
    - Add change to display truck_number with leading zeroes
---
## Step 14 Make Component to edit Truck
Sources: none
- use command "php artisan make:filament-livewire-form Trucks/EditTruck --edit"
- Change ListTrucks to initially show no Trailers
- Add Component Trucks/EditTruck
- Add Route to display EditTruck
- Add Form to EditTruck
- Add Tabs to better organize Form
- Add Validation with custom Messages
- Add Flashmessage for Success and Error
- Add Button to Save and Cancel Edit
- Add Confirmation for Cancelling Creation
- install laravel debugbar
---
## Step 13 Make Component to Create a New Truck
Sources: none
- use Command "php artisan make:filament-livewire-form Trucks/CreateTruck --generate"
- create Component Trucks/CreateTruck
- add Route to display CreateTruck
- add Form to CreateTruck
- add Validation with custom Messages
- add Flashmessage for Success and Error
- add Button to Save and Cancel Creation
- add Confirmation for Cancelling Edit or Create
- added Filter to ListTrucks to only show Trailers
---

## Step 12 Make Component to List Trucks
Sources: none
- create Route to show List of Trucks
- add Link to sidebar
- create Livewire Component to show List of Trucks
- Add Softdeletes to Truck Model
- Add Table descriptions
- Add Delete Action
- Add custom Confirmation Dialog
- Add Flashmessage for Success and Error
- Add Button to Create Truck
---
## Step 11 install Laradumps
Source: https://laradumps.dev/get-started/installation.html
- installed Laradumps
---
## Step 10 Make Component to Edit Manufacturer
Sources: none
- use command "php artisan make:filament-livewire-form Manufacturers/EditManufacturer --edit"
- Created Form with Validation
- added Notification for Success and Error
- added Button to Save and Cancel Edit
- added Confirmation for Cancelling Edit or Create
- added Validation
- added Flashmessage for Success and Error
- added Route to display EditManufacturer
---
## Step 09 Make Component to Create Manufacturer
Sources: none
- use command "php artisan make:filament-livewire-form Manufacturers/CreateManufacturer --generate"
- Made Form with Validation
- added Notification for Success and Error
- added Button to Save and Cancel Edit
- added Confirmation for Cancelling Edit or Create
- added Validation
- added Flashmessage for Success and Error
---
## Step 08 Make LivewireTable for Manufacturers
Sources: https://www.youtube.com/watch?v=YXkhgq3z7DY&t=11079s
- use command "php artisan make:livewire-table Manufacturers/ListManufacturers --generate"
- adapt the Columns to be displayed
- change Link in Sidebar to Manufacturers/LivewireTable
- add Route to display Manufacturers/LivewireTable
- add Filter to display only Manufacturers with softDeletes = true
- add RowAction to view Manufacturer
- add RowAction to delete Manufacturer
- add RowAction to edit Manufacturer
---
## Step 07 Make Filter for ListBrands
Sources: none
- add Filter to Brands/ListBrands
- handle Filter in ListBrands
- added empty file ToDo-List.md to keep track of what to do
---
## Step 06 Create Component CreateBrand
Sources: https://www.youtube.com/watch?v=YXkhgq3z7DY&t=407s
- use command " php artisan make:filament-livewire-form Brands/CreateBrand --generate"
- created all form fields
- added path to store images
- added Notification for success and error
- added button to save and cancel edit
- added Confirmation for Cancelling Edit or Create
- published livewire confirmation
---
## Step 05 Create Component CreateBrand
Sources: https://www.youtube.com/watch?v=YXkhgq3z7DY&t=407s
    and https://filamentphp.com/docs/4.x/components/form
- create Livewire Component CreateBrand
- add Route to display CreateBrand
- add Form to CreateBrand
- add Validation
- add Flashmessage for Success and Error
- add Button to Save and Cancel Edit
---
## Step 04 Change Theme
Sources: https://www.youtube.com/watch?v=FJ4jEN974-w
- changed theme accordingly to Video
- added to Logos to /public/images
- change Logo on Registerpage, Loginpage and Dashboard
- change Logo on Welcome.blade
- change Logo and App Title on Sidebar
---
## Step 03 Create Component to List Brands
Sources: https://www.youtube.com/watch?v=YXkhgq3z7DY&t=407s
- create Component Brands/ListBrands
- add Route for ListBrands
- add Link to sidebar
- Add Table with rows
- Add Delete Action
- Add custom Confirmation Dialog
- Add Flashmessage for Success and Error
---
## Step 02 Install Filament the individual components
Sources: https://www.youtube.com/watch?v=YXkhgq3z7DY&t=407s
- install components
- install Tailwindcss V4
- seed Databse woith Brands and Manufacturers
---
## Step 01 Create Models, Migrations, Factories and Seeders
Sources: none
- create all migrations
- create some seeders
- designed models 
---

## Step 16 Fix saving weight and height in EditTruck

Sources: none

- Root cause: Truck model did not include 'height' and 'weight' in $fillable, so mass assignment via update() ignored
  them.
- Fix: Added 'height' and 'weight' to App\Models\Truck::$fillable.
- Verified EditTruck form already has TextInputs for weight and height and save() calls update($data).

---

## Step 17 Show truck_number with leading zeroes in EditTruck

Sources: none

- Requirement: Display truck number with leading zeroes (max 4 digits) in the EditTruck form.
- Change: In app/Livewire/Trucks/EditTruck.php, updated TextInput('truck_number') to use:
    - formatStateUsing() to display padded value (e.g., 7 -> 0007).
    - dehydrateStateUsing() to strip leading zeros and cast to int when saving.
- Notes: Keeps DB storage as integer while improving display formatting.

---

## Step 18 Add View modal with tabbed layout for Trucks

Sources: Junie

- Added a View record action to app/Livewire/Trucks/ListTrucks.php.
- The action opens a modal and renders a tabbed schema (Allgemein, Erweitert, Bild) mirroring EditTruck but fully
  disabled (read-only).
- Includes formatted truck_number with leading zeros, relationship selects disabled, and image preview read-only.
- Modal has only a Close button, no submit.

---

## Step 21 (continued) Implement EditStore layout and navigation

- Added route stores.edit to open the EditStore component. ✓
- Rebuilt EditStore form to mirror CreateStore/EditTruck with Tabs: Allgemein, Adresse, Navigationsadresse, Bild,
  Erweitert. ✓
- Added validation with custom messages (same as CreateStore). ✓
- Added Save button and Cancel button with confirmation dialog. ✓
- Implemented success notification after saving and redirect to stores.index. ✓
- Implemented cancel confirmation and redirect to stores.index. ✓
- Added Edit row action (pencil icon) in ListStores to navigate to stores.edit, keeping the new eye-icon View modal
  intact. ✓

- Implemented WorkRecords clock-in/clock-out modals in ListWorkRecords header actions.
    - Added form fields: work_date (default today), time (default now), and shift type select.
    - Clock-in creates a new WorkRecord; clock-out updates the latest open WorkRecord and computes differences.
    - Success notifications added; table refreshes after actions.

---

## Step 22 Configure global timezone to MEZ (Europe/Berlin)

- Updated config/app.php to use Europe/Berlin as the default timezone via env override:
  `'timezone' => env('APP_TIMEZONE', 'Europe/Berlin')`. ✓
- This makes all now(), Carbon formatting, Filament Date/Time display and defaults use MEZ/CE(S)T automatically. ✓
- For production, set `APP_TIMEZONE=Europe/Berlin` in your .env and run `php artisan config:clear` to ensure config
  cache picks it up. ✓

- 2025-10-02: Display target_minutes, actual_minutes, and difference_minutes in ListWorkRecords as hours (float,
  minutes/60) using formatStateUsing in the Filament table columns. No changes to stored values; only UI formatting
  adjusted.
