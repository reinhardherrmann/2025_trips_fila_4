
















## Step 09 Make Component to Create Manufacturer
Sources: 
- use command " php artisan make:filament-livewire-form Manufacturers/CreateManufacturer --generate"
- Made Form with Validation
- added Notification for Success and Error
- added Button to Save and Cancel Edit
- added Confirmation for Cancelling Edit or Create
- added Validation
- added Flashmessage for Success and Error
---
## Step 08 Make LivewireTable for Manufacturers
Sources: https://www.youtube.com/watch?v=YXkhgq3z7DY&t=11079s
- use command " php artisan make:livewire-table Manufacturers/ListManufacturers --generate"
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
