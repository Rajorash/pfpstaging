# The Profit First Prophet
This project is a tool to enable users to implement the Profit First methodology in order to help them manage their business finances.

## Profit First Briefing Document

[Reference sheet](https://docs.google.com/spreadsheets/d/1k_18QLHUDgwWaw9ymOtyv89gBJcqcbs80-jNh2AdS5g/edit?usp=sharing)

[Github repository](https://github.com/PounceMarketingDev/pfp/tree/maryan) -  please make any commits to your assigned branch.

### Setup
pull down the repository, then install the dependency with 
```bash
composer install
```
setup up environment variables in the .env file (create by duplicating .env.example)
set key with 
```bash
php artisan key:generate
```
setup database and enter details in .env,
then node dependencies
```bash
npm install
```
The db has seeders to prefill dummy data for the site. To pregenerate this data for easier development use
```bash
php artisan migrate:fresh --seed
```

while working on js/css files you can constantly rebuild the development files by running 
```bash
npm run watch
```
which will constantly monitor for changes and rebuild the files.

### Intro

This is a calculation tool that takes input and calculates how much money should be put into various accounts. Once logged in, you can create accounts for a business and setup various cashflows for each account. There are different account types which are calculated at different stages during the process. Values are entered into the Calculator view and are updated instantly into the database (using async js at this point in time.)

Most of the data will be prefilled for you as long as you seed the db. You can log in with these credentials from the seed. 
 <dl>
  <dt>user</dt><dd>advisor@pfp.com</dd>
  <dt>pw</dt><dd>letmeinnow!</dd>
</dl>

When checking the values against the reference sheet, make sure that the entry values AND the percentage values are the same.

### Tasks
* Add Month dropdown on Calculations view
* Build Projections view

#### View by Month Dropdown

Please add a dropdown that filters the view based on the month selected.
* Default should be the current month.
* If there are amounts present previous to the current view that will affect the calculations, please account for this (ie. use them in the first calculation.)

#### Projections View

Refer to the reference sheet for proper functionality of this view
The Projection view refers to the Cashflow Projection tab. (The calculator view and percentages view refer to the Cashflow Entry and Alocation %'s tabs respectively.)

At a very simple level, you can use the calculator as a guide to how this page should work, however since this view groups calculations by weeks and/or months, please take this into consideration. Refer to the values from the reference sheet to check if this has been correctly built.

Some of this view has been created already. If any of it is not usable, feel free to implement however works most efficiently.

Important places to look for how this all works will be in the following files:
* app\Http\Controllers\AllocationsController.php
* resources\js\allocations.js

#### Notes
If needed you may include more php or js libraries, however this should be a last resort as this should be resonably possible to achieve with the correct data structures, manipulation and existing solutions.

If there is an absolute need to use more libraries please use npm and composer to add them to the dependencies and compile with build scripts. Vendor scripts have intentionally been separated in the build process, please ensure this remains so.
* [Laravel Mix documentation](https://laravel-mix.com/)
* Example of how 'arrow-table' was installed and saved to dependencies ```bash npm i arrow-table --save``` (note: this should be covered already)
* See "resources\js\app.js" for how to include js liibraries in the build script. 'arrow-table' was included for arrow navigation on the calculator view.
