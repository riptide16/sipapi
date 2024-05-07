# Evalatore API
Deskripsi : Aplikasi Akreditasi Perpustakaan Berbasis Web dan Android PWA

## Requirements
- PHP 7.4^
- MariaDB 10
- Composer 2.1^

## Setup
- Clone this repository
- Prepare 2 MariaDB databases; one for real use, one for testing
- Make a copy of the provided `.env.example` to `.env`
- Set database configuration according to your environment. Use `db_database` for your actual database name, and `db_database_testing` for testing database
- Run `composer install`
- Run `php artisan key:generate` (Run only once)
- Run `php artisan migrate`
- Run `php artisan db:seed` to seed mandatory databases
- Generate Passport key with `php artisan passport:keys`
- Create a password grant client for frontend with `php artisan passport:client --password`
- To seed master data for Provinces, Cities, etc, run seeder with `php artisan db:seed --class=IndonesiaRegionSeeder`

## Testing
- To run the unit tests: `vendor/bin/phpunit --debug --stop-on-failure --stop-on-error`

## Seeding dummy data
- Run `php artisan db:seed --class=DummyDatabaseSeeder` or pick any class to seed individually

## Running
- Run `php artisan serve` to start a development server
