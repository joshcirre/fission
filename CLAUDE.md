# CLAUDE.md

This file provides guidance to AI coding assistants when working with code in this repository.

## Critical Commands

**ALWAYS run these commands after making changes:**

```bash
composer test    # Run all tests, linting, type checking, and refactor validation
composer fix     # Fix code style, apply refactoring, and format code
```

## Quick Reference

| Command                | Description                                        |
| ---------------------- | -------------------------------------------------- |
| `composer run dev`     | Start all dev services (server, queue, logs, Vite) |
| `./vendor/bin/pest`    | Run tests                                          |
| `./vendor/bin/pint`    | Format PHP code                                    |
| `./vendor/bin/phpstan` | Static analysis                                    |
| `npm run build`        | Build frontend assets                              |

## Stack

- **PHP 8.2+** with strict types
- **Laravel 12** with Livewire 4
- **Livewire Volt** for single-file components
- **Livewire Flux Pro** UI components
- **Pest v4** for testing
- **Tailwind CSS v4**
- **SQLite** by default

## Architecture

### Page Components

Pages are Livewire 4 full-page components in `resources/views/pages/` with `⚡` prefix:

```
resources/views/pages/
├── ⚡dashboard.blade.php      → /
├── ⚡playground.blade.php     → /playground
├── auth/
│   ├── ⚡login.blade.php      → /auth/login
│   └── ⚡register.blade.php   → /auth/register
└── profile/
    └── ⚡index.blade.php      → /profile
```

Routes are defined in `routes/web.php` using `Route::livewire()`.

### Component Structure

```php
<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new
#[Layout('layouts::app')]
class extends Component {
    public int $count = 0;

    public function increment(): void
    {
        $this->count++;
    }
};

?>

<div>
    <h1>{{ $count }}</h1>
    <button wire:click="increment">+</button>
</div>
```

### Creating New Pages

```bash
php artisan make:livewire pages::<name>
```

Then add route in `routes/web.php`:

```php
Route::livewire('/path', 'pages::name')->name('name');
```

## Coding Standards

### PHP

- Use `declare(strict_types=1);`
- Use PHP 8+ features (constructor promotion, match, etc.)
- Use `$guarded = []` instead of `$fillable` in models
- Use `Model::query()` instead of `DB::`
- Use config files, never `env()` directly

### Models

- Always use `SoftDeletes` trait
- Create models with factory and seeder: `php artisan make:model X -mfs`
- Prevent N+1 queries with eager loading

### Migrations

- Omit `down()` method in new migrations

### Actions

- Use Actions pattern for business logic (`php artisan make:action`)
- Name with verb phrases (e.g., `CreateProduct`, `SendInvoice`)

### Livewire

- Single root element in templates
- Use `wire:key` in loops
- Use `wire:model.live` for real-time binding
- Use `#[On('event')]` for event listeners

### Testing

- All tests use Pest PHP
- Feature tests in `tests/Feature/`
- Unit tests in `tests/Unit/`
- Use factories for test data

```php
test('example', function () {
    Livewire::test('pages::dashboard')
        ->assertOk();
});
```

## Project Structure

```
app/
├── Actions/          # Business logic (verb phrases)
├── Models/           # Eloquent models
└── Providers/        # Service providers

resources/views/
├── components/       # Reusable Blade components
├── layouts/          # Layout templates
└── pages/            # Livewire full-page components (⚡ prefix)

tests/
├── Feature/          # Integration tests
│   ├── Auth/
│   ├── Pages/
│   └── ProfileTest.php
└── Unit/             # Unit tests
```

## Do Not

- Create new directories without approval
- Add dependencies without approval
- Use `DB::` facade (use `Model::query()`)
- Use `env()` outside config files
- Create documentation files unless requested
- Remove existing tests
