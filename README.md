<p align="center"><img src="https://github.com/user-attachments/assets/fd074588-4ffd-47f3-af6e-a24500ecbc55" alt="Fission Logo"></p>

## IN PROGRESS

This is an opinionated starter kit created by Josh Cirre using Laravel Folio, Livewire Volt, and Livewire Flux. There will be changes.

## Setup

### Install Dependencies

First, instsall the PHP dependencies:

```bash
composer install
```

Next, install the frontend dependencies:

```bash
npm install
```

### Create a Database

To create a local SQLite database, run the following command:

```bash
touch database/database.sqlite
```

### Create a .env file

Copy the `.env.example` file to `.env` and update the values to match your local environment.

```bash
cp .env.example .env
```

### Generate app key

Run the following command to generate a new app key:

```bash
php artisan key:generate
```

### Run migrations

To run the migrations, run the following command:

```bash
php artisan migrate
```

### Run the server

To run the server, run the following command:

```bash
php artisan serve
```

### Compile assets during development

```bash
npm run dev
```

#### Compile assets for production

```bash
npm run build
```

You should now be able to view the site at http://localhost:8000.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
