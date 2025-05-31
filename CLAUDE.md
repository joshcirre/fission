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

#### Quick Commands
```bash
composer lint        # Lint both PHP (Pint) and JS/CSS (Prettier)
composer refactor    # Apply Rector refactoring rules
composer test        # Run all tests and checks
```

#### Individual Tools
```bash
./vendor/bin/pint    # Format PHP code (strict Laravel conventions)
./vendor/bin/rector  # Apply code refactoring rules
./vendor/bin/phpstan # Run static analysis at max level
./vendor/bin/peck    # Check for typos in codebase
npm run lint         # Check JS/CSS formatting with Prettier
npm run lint:fix     # Auto-fix JS/CSS formatting
```

#### Test Commands
```bash
composer test              # Run complete test suite
composer test:unit         # Run Pest tests in parallel
composer test:unit:coverage # Run tests with coverage report
composer test:lint         # Check code formatting without fixing
composer test:types        # Run PHPStan static analysis
composer test:refactor     # Preview Rector changes (dry-run)
composer test:typos        # Check for typos with Peck
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
6. Code quality is maintained via Rector with Laravel-optimized rules
7. Static analysis is enforced via PHPStan at max level with Larastan

## MCP Servers for Claude

This project is configured to work with Claude's Model Context Protocol (MCP) servers for enhanced development assistance. When using Claude Code (claude.ai/code), the following MCP servers should be available:

### Available MCP Servers

1. **Context7** - Provides access to Laravel documentation
   - Use for Laravel framework questions and best practices
   - Includes up-to-date Laravel docs and examples

2. **Flux UI** - Provides access to Livewire Flux component documentation
   - Use for Flux UI component questions
   - Includes component examples and API references

### Ensuring MCP Servers are Running

When starting a development session with Claude Code, verify MCP servers are connected by running:

```bash
mcp
```

You should see all servers with a "connected" status. If any show "connecting..." wait a moment and check again.

These servers provide Claude with direct access to relevant documentation and tools, improving code suggestions and problem-solving capabilities.

## Coding Standards & Best Practices

You are an expert in PHP, Laravel, Livewire Volt, Folio, Blade, Pest, and Tailwind CSS.

### 1. Language & Framework Requirements

- **Use PHP 8.2+ features** (match version in composer.json)
- **Follow pint.json coding rules** strictly
- **Enforce strict types** and array shapes via PHPStan
- **Use Livewire Volt** for interactive components with `@volt` directive
- **Use Folio** for file-based routing in `resources/views/pages`
- **Leverage MCP servers** (Context7 for Laravel, Flux UI for components)

### 2. Project Structure & Architecture

#### General Rules
- **Delete .gitkeep** when adding files to directories
- **Stick to existing structure** - no new folders without explicit requirement
- **No DB:: usage** - always use `Model::query()` instead
- **No dependency changes** without explicit approval
- **Use environment variables** via config files, never `env()` directly

#### Page Creation (Folio)
- **Create new pages** with `php artisan folio:page`
- **All pages live in** `resources/views/pages/`
- **File structure determines routes**:
  - `pages/index.blade.php` → `/`
  - `pages/about.blade.php` → `/about`
  - `pages/users/[id].blade.php` → `/users/{id}`
- **Interactive elements** must be wrapped in `@volt` directive

#### Volt Component Example
```php
@volt
<?php
use function Livewire\Volt\{state, computed};

state(['count' => 0]);

$increment = fn () => $this->count++;
$decrement = fn () => $this->count--;

$double = computed(fn () => $this->count * 2);
?>

<div>
    <h1>Count: {{ $count }}</h1>
    <h2>Double: {{ $this->double }}</h2>
    <button wire:click="increment">+</button>
    <button wire:click="decrement">-</button>
</div>
@endvolt
```

#### Directory Conventions

**app/Actions**
- Use Actions pattern for reusable business logic
- Create with `php artisan make:action` (via Essentials package)
- Name with verb phrases
- Example implementation:
```php
namespace App\Actions;

class CreateTodoAction
{
    public function handle(User $user, array $data): Todo
    {
        return $user->todos()->create($data);
    }
}
```

**app/Models**
- Avoid using `$fillable` - use `$guarded = []` instead
- Always use `SoftDeletes` trait for models:
```php
class User extends Model
{
    use SoftDeletes;
}
```
- Prevent N+1 queries with eager loading:
```php
$users = User::with('posts')->get();
```

**database/migrations**
- Omit `down()` method in new migrations
- Use descriptive names with full timestamp

### 3. Laravel 11+ Specific

- **No app\Console\Kernel.php** - use `bootstrap/app.php` for console configurations
- **Commands auto-register** - files in `app/Console/Commands/` are automatically available
- **Use artisan generators**: `php artisan make:{model,migration,controller,action}`
- **Config over env**: Always use `config('app.name')` not `env('APP_NAME')`

### 4. Livewire 3 & Volt Standards

#### Key Changes from Livewire 2
- **Namespace**: Components now use `App\Livewire` (not `App\Http\Livewire`)
- **Events**: Use `$this->dispatch()` (not `emit` or `dispatchBrowserEvent`)
- **Layout path**: `components.layouts.app` (not `layouts.app`)
- **Deferred by default**: Use `wire:model.live` for real-time updates
- **Alpine included**: Don't manually include Alpine.js

#### Livewire Best Practices
- **Single root element** in Blade components
- **Add wire:key** in loops:
```blade
@foreach($items as $item)
    <div wire:key="item-{{ $item->id }}">
        {{ $item->name }}
    </div>
@endforeach
```
- **Use attributes** for event listeners:
```php
#[On('todo-created')]
public function refreshList()
{
    // ...
}
```
- **Loading states**: Use `wire:loading` and `wire:dirty`
- **Confirmations**: Use `wire:confirm="Are you sure?"`

### 5. Testing Requirements

#### General Testing Rules
- **Use Pest PHP** for all tests
- **Run `composer lint`** after changes
- **Run `composer test`** before completing tasks
- **All code must be tested**
- **Generate factories** with each model

#### Test Directory Structure
- Folio pages: `tests/Feature/Pages`
- Volt components: `tests/Feature/Volt`
- Actions: `tests/Unit/Actions`
- Models: `tests/Unit/Models`

#### Livewire Test Example
```php
use Livewire\Volt\Volt;

test('counter increments', function () {
    Volt::test('counter')
        ->assertSee('Count: 0')
        ->call('increment')
        ->assertSee('Count: 1');
});
```

### 6. Styling & UI Guidelines

- **Use Tailwind CSS v4** exclusively
- **Use Flux UI components** when available
- **Custom CSS** goes in `resources/css/app.css`
- **Keep UI minimal and clean**
- **Mobile-first responsive design**

### 7. Code Quality Standards

#### Before Committing
1. Run `composer lint` to fix code style
2. Run `composer test` to ensure all tests pass
3. Run `composer refactor` to apply improvements
4. Check `composer phpstan` for type safety

#### Task Completion Checklist
- [ ] All code follows these standards
- [ ] Tests written and passing
- [ ] Frontend assets recompiled if needed (`npm run build`)
- [ ] No new dependencies without approval
- [ ] Used proper artisan commands for generation
- [ ] Environment variables added to config files
- [ ] Eager loading implemented where needed

### 8. Performance Best Practices

- **Eager load relationships** to prevent N+1 queries
- **Use database transactions** for data integrity
- **Cache expensive operations** appropriately
- **Use queues** for time-consuming tasks
- **Implement proper pagination** for large datasets
- **Use batch operations** when processing multiple records

Remember: Always use `php artisan folio:page` for new pages, wrap interactive elements in `@volt`, and follow Livewire 3 conventions. Refer to Context7 MCP for Laravel docs and Flux UI MCP for component documentation.