# The Profit First Prophet
This project is a tool to enable users to implement the Profit First methodology in order to help them manage their business finances.

## Profit First Briefing Document

[Reference sheet](https://docs.google.com/spreadsheets/d/1k_18QLHUDgwWaw9ymOtyv89gBJcqcbs80-jNh2AdS5g/edit?usp=sharing)

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
