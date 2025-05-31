# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development Commands

### Primary Development
```bash
composer run dev  # Starts all services: Laravel server, queue, logs, and Vite
```

This single command runs:
- Laravel development server
- Queue listener for background jobs
- Laravel Pail for real-time log viewing
- Vite dev server for frontend assets

### Alternative Commands
```bash
php artisan solo      # Alternative development server
npm run dev          # Vite dev server only
npm run build        # Build assets for production
```

### Testing
```bash
./vendor/bin/pest    # Run all tests
php artisan test     # Alternative test command
./vendor/bin/pest tests/Feature/ExampleTest.php  # Run specific test file
./vendor/bin/pest --filter "test name"           # Run specific test
```

### Code Quality
```bash
./vendor/bin/pint    # Format PHP code (strict Laravel conventions)
php artisan pint     # Alternative lint command
```

## Architecture Overview

This is **Fission**, an opinionated Laravel starter kit that uses:

- **Laravel 12.x** with file-based routing (Folio)
- **Livewire 3.x** with Volt single-file components
- **Livewire Flux** UI component library (requires license)
- **SQLite** database by default
- **Tailwind CSS 4.x** with Vite

### Key Architectural Decisions

1. **File-Based Routing**: Pages are in `resources/views/pages/`. The file structure determines routes:
   - `pages/index.blade.php` → `/`
   - `pages/profile/index.blade.php` → `/profile`
   - `pages/auth/login.blade.php` → `/auth/login`

2. **Single-File Components**: Livewire Volt allows PHP logic and Blade templates in one file. Components use the `@volt` directive.

3. **Authentication**: Pre-built auth system similar to Laravel Breeze with login, registration, password reset, and email verification.

4. **UI Components**: Uses Livewire Flux (premium) for consistent UI components. Fallback to standard Blade components if Flux is unavailable.

### Important Service Providers

- **FolioServiceProvider**: Configures page routes and middleware
- **VoltServiceProvider**: Sets up Volt component discovery
- **SoloServiceProvider**: Configures the Solo development server

### Development Workflow

1. Routes are automatically created by adding files to `resources/views/pages/`
2. Use Volt for interactive components within pages
3. Flux components provide consistent UI (e.g., `<flux:button>`, `<flux:input>`)
4. Tests use Pest PHP with Laravel-specific helpers
5. Code style is enforced via Pint with strict settings