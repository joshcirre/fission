# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## 🚨 CRITICAL COMMANDS - Run After Every Change

**ALWAYS run these two commands after making any changes:**

```bash
composer test    # Run all tests, linting, type checking, and refactor validation
composer fix     # Fix code style, apply refactoring, and format code
```

These commands ensure code quality and must pass before committing. They catch issues early and maintain consistent code standards.

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
./vendor/bin/pest tests/Feature/Auth/LoginTest.php  # Run specific test file
./vendor/bin/pest --filter "test name"              # Run specific test
```

### Code Quality

#### Quick Commands

```bash
composer fix         # Fix all: types check, refactor, format JS/CSS, format PHP
composer lint        # Lint both PHP (Pint) and JS/CSS (Prettier)
composer refactor    # Apply Rector refactoring rules
composer test        # Run all tests and checks (no fixes)
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

## Setup Requirements

### Flux Pro Setup (Optional)

This starter kit uses some Flux Pro components (`<flux:toast>`, `<flux:card>`) for enhanced UI. The basic test suite runs without Flux Pro, but for full functionality:

1. **Add Flux Pro repository** to composer.json:

```json
"repositories": {
    "flux-pro": {
        "type": "composer",
        "url": "https://composer.fluxui.dev"
    }
}
```

2. **Install Flux Pro**:

```bash
composer require livewire/flux-pro
```

3. **Configure auth.json** (for CI/deployment):

```bash
composer config --auth http-basic.composer.fluxui.dev your-email@example.com your-license-key
```

Note: Tests will pass without Flux Pro installed. The FissionInstall command handles Flux Pro setup automatically when credentials are available.

### Memory Requirements

Some commands require increased memory limits:

- **PHPStan**: Already configured with 256MB in composer.json
- **Large projects**: May need to increase PHP memory limit in php.ini or use `-d memory_limit=512M`

## Architecture Overview

This is **Fission**, an opinionated Laravel starter kit that uses:

- **Laravel 12.x** with Livewire 4 full-page components
- **Livewire 4 beta** with single-file components
- **Livewire Flux Pro** UI component library (requires license)
- **SQLite** database by default
- **Tailwind CSS 4.x** with Vite

### Key Architectural Decisions

1. **Livewire Full-Page Components**: Pages are single-file Livewire components in `resources/views/pages/` with the ⚡ emoji prefix. Routes are explicitly defined in `routes/web.php`:

    - `⚡dashboard.blade.php` with `Route::livewire('/', 'pages::dashboard')` → `/`
    - `⚡index.blade.php` in `profile/` with `Route::livewire('/profile', 'pages::profile.index')` → `/profile`
    - `⚡login.blade.php` in `auth/` with `Route::livewire('/auth/login', 'pages::auth.login')` → `/auth/login`

2. **Single-File Components**: Livewire 4 allows PHP logic and Blade templates in one file using anonymous classes extending `Component`.

3. **Authentication**: Pre-built auth system similar to Laravel Breeze with login, registration, password reset, and email verification.

4. **UI Components**: Uses Livewire Flux Pro for consistent UI components.

### Important Service Providers

- **SoloServiceProvider**: Configures the Solo development server

### Development Workflow

1. Create routes in `routes/web.php` using `Route::livewire()` pointing to page components
2. Create page components using `php artisan make:livewire pages::<name>`
3. Use Livewire 4 single-file components with `#[Layout()]` attributes
4. Flux Pro components provide consistent UI (e.g., `<flux:button>`, `<flux:input>`, `<flux:card>`)
5. Tests use Pest PHP with Livewire testing helpers
6. Code style is enforced via Pint with strict settings
7. Code quality is maintained via Rector with Laravel-optimized rules
8. Static analysis is enforced via PHPStan at max level with Larastan

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

You are an expert in PHP, Laravel, Livewire 4, Blade, Pest, and Tailwind CSS.

### 1. Language & Framework Requirements

- **Use PHP 8.2+ features** (match version in composer.json)
- **Follow pint.json coding rules** strictly
- **Enforce strict types** and array shapes via PHPStan
- **Use Livewire 4 full-page components** for all pages
- **Define routes explicitly** in `routes/web.php` using `Route::livewire()`
- **Leverage MCP servers** (Context7 for Laravel, Flux UI for components)

### 2. Project Structure & Architecture

#### General Rules

- **Delete .gitkeep** when adding files to directories
- **Stick to existing structure** - no new folders without explicit requirement
- **No DB:: usage** - always use `Model::query()` instead
- **No dependency changes** without explicit approval
- **Use environment variables** via config files, never `env()` directly

#### Page Creation (Livewire 4)

- **Create new pages** with `php artisan make:livewire pages::<name>`
- **All pages live in** `resources/views/pages/` with the ⚡ emoji prefix
- **Routes are explicitly defined** in `routes/web.php`:
    - `⚡dashboard.blade.php` → `Route::livewire('/', 'pages::dashboard')->name('dashboard');`
    - `⚡about.blade.php` → `Route::livewire('/about', 'pages::about')->name('about');`
    - `⚡user-show.blade.php` → `Route::livewire('/users/{id}', 'pages::user-show')->name('users.show');`
- **Components use `#[Layout()]` attribute** to specify the layout

#### Livewire 4 Component Example

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

    public function decrement(): void
    {
        $this->count--;
    }

    public function getDoubleProperty(): int
    {
        return $this->count * 2;
    }
};

?>

<div>
    <h1>Count: {{ $count }}</h1>
    <h2>Double: {{ $this->double }}</h2>
    <button wire:click="increment">+</button>
    <button wire:click="decrement">-</button>
</div>
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
@foreach ($items as $item)
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

- Livewire pages: `tests/Feature/Pages`
- Auth features: `tests/Feature/Auth`
- Actions: `tests/Unit/Actions`
- Models: `tests/Unit/Models`

#### Livewire 4 Test Example

```php
use Livewire\Livewire;

test('counter increments', function () {
    Livewire::test('pages::counter')
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

**CRITICAL**: Always run these commands after any changes:

1. **`composer test`** - Run complete test suite (tests, linting, types, refactor check)
2. **`composer fix`** - Fix code style, types, and apply refactoring

These commands ensure code quality and prevent issues. Never commit without running both successfully.

#### Alternative Individual Commands

- `composer lint` - Check code formatting without fixing
- `composer refactor` - Apply Rector refactoring rules
- `./vendor/bin/phpstan` - Run static analysis only
- `./vendor/bin/pest` - Run tests only

#### Task Completion Checklist

- [ ] All code follows these standards
- [ ] Tests written and passing
- [ ] Frontend assets recompiled if needed (`npm run build`)
- [ ] No new dependencies without approval
- [ ] Used proper artisan commands for generation
- [ ] Environment variables added to config files
- [ ] Eager loading implemented where needed
- [ ] **CRITICAL**: `composer test` passes
- [ ] **CRITICAL**: `composer fix` completes successfully

### 8. Performance Best Practices

- **Eager load relationships** to prevent N+1 queries
- **Use database transactions** for data integrity
- **Cache expensive operations** appropriately
- **Use queues** for time-consuming tasks
- **Implement proper pagination** for large datasets
- **Use batch operations** when processing multiple records

Remember: Always use `php artisan make:livewire pages::<name>` for new pages, add routes using `Route::livewire()` in `routes/web.php`, use `#[Layout()]` attributes for layouts, and follow Livewire 4 conventions. Refer to Context7 MCP for Laravel docs and Flux UI MCP for component documentation.

# important-instruction-reminders

Do what has been asked; nothing more, nothing less.
NEVER create files unless they're absolutely necessary for achieving your goal.
ALWAYS prefer editing an existing file to creating a new one.
NEVER proactively create documentation files (\*.md) or README files. Only create documentation files if explicitly requested by the User.

## Creating New Features: Step-by-Step Guide

### 1. Creating a New Page

**Use Livewire 4 full-page components:**

```bash
php artisan make:livewire pages::products
# Creates: resources/views/pages/⚡products.blade.php
# Then add route to routes/web.php

php artisan make:livewire pages::product-show
# Creates: resources/views/pages/⚡product-show.blade.php
# Then add route to routes/web.php with parameter
```

**Basic page structure:**

```php
<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new
#[Layout('layouts::app')]
class extends Component {
    // Component logic here
};

?>

<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold mb-6">Products</h1>
            <!-- Page content -->
        </div>
    </div>
</div>
```

**Add route to routes/web.php:**

```php
Route::livewire('/products', 'pages::products')
    ->name('products.index')
    ->middleware(['auth', 'verified']);
```

**With interactivity:**

```php
<?php

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\Product;

new
#[Layout('layouts::app')]
class extends Component {
    public string $search = '';

    public function getFilteredProductsProperty()
    {
        return Product::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->get();
    }
};

?>

<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold mb-6">Products</h1>

            <flux:input wire:model.live="search" placeholder="Search products..." />

            <div class="grid gap-4 mt-6">
                @foreach($this->filteredProducts as $product)
                    <flux:card wire:key="product-{{ $product->id }}">
                        <h3>{{ $product->name }}</h3>
                        <p>{{ $product->description }}</p>
                    </flux:card>
                @endforeach
            </div>
        </div>
    </div>
</div>
```

### 2. Creating a New Model with Factory

```bash
php artisan make:model Product -mf
# Creates: app/Models/Product.php, database/migrations/xxxx_create_products_table.php, database/factories/ProductFactory.php
```

**Model example:**

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

**Factory example:**

```php
<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->productName(),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 10, 1000),
            'is_active' => fake()->boolean(80),
            'user_id' => User::factory(),
        ];
    }
}
```

### 3. Creating Actions for Business Logic

```bash
php artisan make:action CreateProduct
# Creates: app/Actions/CreateProduct.php
```

**Action example:**

```php
<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Product;
use App\Models\User;

class CreateProduct
{
    public function handle(User $user, array $data): Product
    {
        return $user->products()->create([
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'is_active' => $data['is_active'] ?? true,
        ]);
    }
}
```

### 4. Creating Forms with Livewire

**Form class:**

```bash
php artisan livewire:form ProductForm
# Creates: app/Livewire/Forms/ProductForm.php
```

Form objects allow you to re-use form logic across components and provide a nice way to keep your component class cleaner by grouping all form-related code into a separate class.

```php
<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class ProductForm extends Form
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string')]
    public string $description = '';

    #[Validate('required|numeric|min:0')]
    public float $price = 0;

    #[Validate('boolean')]
    public bool $is_active = true;

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ];
    }
}
```

**Using in Volt component:**

```php
<?php
use App\Actions\CreateProduct;
use App\Livewire\Forms\ProductForm;
use Livewire\Volt\Component;
use function Laravel\Folio\name;

name('products.create');

new class extends Component {
    public ProductForm $form;

    public function create(CreateProduct $createProduct): void
    {
        $this->validate();

        $createProduct->handle(auth()->user(), $this->form->all());

        $this->form->reset();
        session()->flash('success', 'Product created successfully!');
        $this->redirect(route('products.index'));
    }
}; ?>

<x-layouts.app>
    @volt('pages.products.create')
        <form wire:submit="create" class="space-y-6">
            <flux:input wire:model="form.name" label="Name" />
            <flux:textarea wire:model="form.description" label="Description" />
            <flux:input wire:model="form.price" label="Price" type="number" step="0.01" />
            <flux:checkbox wire:model="form.is_active" label="Active" />

            <flux:button type="submit" variant="primary">Create Product</flux:button>
        </form>
    @endvolt
</x-layouts.app>
```

### 5. Writing Tests

**Feature test for pages:**

```php
<?php

declare(strict_types=1);

use App\Models\User;
use function Pest\Laravel\{get, actingAs};

test('products page requires authentication', function () {
    get('/products')->assertRedirect('/auth/login');
});

test('authenticated users can access products page', function () {
    $user = User::factory()->create();

    actingAs($user)->get('/products')->assertOk();
});
```

**Volt component test:**

```php
<?php

declare(strict_types=1);

use App\Models\{User, Product};
use Livewire\Volt\Volt;

test('product form creates product', function () {
    $user = User::factory()->create();

    Volt::test('pages.products.create')
        ->actingAs($user)
        ->set('form.name', 'Test Product')
        ->set('form.description', 'Test Description')
        ->set('form.price', 99.99)
        ->call('create')
        ->assertHasNoErrors()
        ->assertRedirect(route('products.index'));

    expect(Product::where('name', 'Test Product')->exists())->toBeTrue();
});
```

**Action test:**

```php
<?php

declare(strict_types=1);

use App\Actions\CreateProduct;
use App\Models\User;

test('create product action works', function () {
    $user = User::factory()->create();
    $action = new CreateProduct();

    $product = $action->handle($user, [
        'name' => 'Test Product',
        'description' => 'Test Description',
        'price' => 99.99,
        'is_active' => true,
    ]);

    expect($product->name)->toBe('Test Product');
    expect($product->user_id)->toBe($user->id);
});
```

### 6. Adding Database Relationships

**In your model:**

```php
// Product.php
public function user()
{
    return $this->belongsTo(User::class);
}

public function categories()
{
    return $this->belongsToMany(Category::class);
}

// User.php
public function products()
{
    return $this->hasMany(Product::class);
}
```

**Eager loading in components:**

```php
$products = computed(function () {
    return Product::with(['user', 'categories'])
        ->latest()
        ->get();
});
```

### 7. Adding Middleware and Route Protection

**In Folio pages:**

```php
<?php
use function Laravel\Folio\{name, middleware};

name('admin.products');
middleware(['auth', 'verified', 'can:manage-products']);
?>
```

### 8. Feature Development Checklist

When creating a new feature:

- [ ] Create model with migration and factory (`php artisan make:model X -mf`)
- [ ] Create pages with Folio (`php artisan folio:page`)
- [ ] Create actions for business logic (`php artisan make:action`)
- [ ] Create forms if needed (`php artisan livewire:form`)
- [ ] Add relationships to models
- [ ] Write feature tests and unit tests
- [ ] Use Flux components for consistent UI
- [ ] Add proper validation and error handling
- [ ] Implement proper authorization if needed
- [ ] **REQUIRED**: Run `composer test` to ensure everything passes
- [ ] **REQUIRED**: Run `composer fix` to format and lint code

### 9. Common Patterns

**CRUD Operations with Volt:**

```php
<?php
use App\Models\Product;
use Livewire\Volt\Component;

new class extends Component {
    public ?int $editing = null;
    public string $search = '';

    public function getProductsProperty()
    {
        return Product::when($this->search,
            fn($q) => $q->where('name', 'like', "%{$this->search}%")
        )->get();
    }

    public function edit(Product $product): void
    {
        $this->editing = $product->id;
    }

    public function delete(Product $product): void
    {
        $product->delete();
        Flux::toast('Product deleted successfully.', variant: 'success');
    }
}; ?>

@volt('products.list')
    <!-- UI here -->
@endvolt
```

**Real-time search:**

```php
<flux:input
    wire:model.live.debounce.300ms="search"
    placeholder="Search..."
/>
```

**Loading states:**

```php
<flux:button wire:click="save" wire:loading.attr="disabled">
    <span wire:loading.remove>Save</span>
    <span wire:loading>Saving...</span>
</flux:button>
```

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to enhance the user's satisfaction building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4.11
- laravel/folio (FOLIO) - v1
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- livewire/flux (FLUXUI_FREE) - v2
- livewire/livewire (LIVEWIRE) - v3
- livewire/volt (VOLT) - v1
- larastan/larastan (LARASTAN) - v3
- laravel/pint (PINT) - v1
- pestphp/pest (PEST) - v3
- rector/rector (RECTOR) - v2
- tailwindcss (TAILWINDCSS) - v4

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove it works. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure - don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

=== boost rules ===

## Laravel Boost

- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan

- Use the `list-artisan-commands` tool when you need to call an Artisan command to double check the available parameters.

## URLs

- Whenever you share a project URL with the user you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain / IP, and port.

## Tinker / Debugging

- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.

## Reading Browser Logs With the `browser-logs` Tool

- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)

- Boost comes with a powerful `search-docs` tool you should use before any other approaches. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation specific for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- The 'search-docs' tool is perfect for all Laravel related packages, including Laravel, Inertia, Livewire, Filament, Tailwind, Pest, Nova, Nightwatch, etc.
- You must use this tool to search for Laravel-ecosystem documentation before falling back to other approaches.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic based queries to start. For example: `['rate limiting', 'routing rate limiting', 'routing']`.
- Do not add package names to queries - package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax

- You can and should pass multiple queries at once. The most relevant results will be returned first.

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit"
3. Quoted Phrases (Exact Position) - query="infinite scroll" - Words must be adjacent and in that order
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit"
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms

=== php rules ===

## PHP

- Always use strict typing at the head of a `.php` file: `declare(strict_types=1);`.
- Always use curly braces for control structures, even if it has one line.

### Constructors

- Use PHP 8 constructor property promotion in `__construct()`.
    - <code-snippet>public function \_\_construct(public GitHub $github) { }</code-snippet>
- Do not allow empty `__construct()` methods with zero parameters.

### Type Declarations

- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<code-snippet name="Explicit Return Types and Method Params" lang="php">
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
</code-snippet>

## Comments

- Prefer PHPDoc blocks over comments. Never use comments within the code itself unless there is something _very_ complex going on.

## PHPDoc Blocks

- Add useful array shape type definitions for arrays when appropriate.

## Enums

- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.

=== folio/core rules ===

## Laravel Folio

- Laravel Folio is a file based router. With Laravel Folio, a new route is created for every Blade file within the configured Folio directory. For example, pages are usually in in `resources/views/pages/` and the file structure determines routes:
    - `pages/index.blade.php` → `/`
    - `pages/profile/index.blade.php` → `/profile`
    - `pages/auth/login.blade.php` → `/auth/login`
- You may list available Folio routes using `php artisan folio:list` or using Boost's `list-routes` tool.

### New Pages & Routes

- Always create new `folio` pages and routes using `artisan folio:page [name]` following existing naming conventions.

<code-snippet name="Example folio:page Commands for Automatic Routing" lang="shell">
    // Creates: resources/views/pages/products.blade.php → /products
    php artisan folio:page 'products'

    // Creates: resources/views/pages/products/[id].blade.php → /products/{id}
    php artisan folio:page 'products/[id]'

</code-snippet>

- Add a 'name' to each new Folio page at the very top of the file so it has a named route available for other parts of the codebase to use.

<code-snippet name="Adding named route to Folio page" lang="php">
use function Laravel\Folio\name;

name('products.index');
</code-snippet>

### Support & Documentation

- Folio supports: middleware, serving pages from multiple paths, subdomain routing, named routes, nested routes, index routes, route parameters, and route model binding.
- If available, use Boost's `search-docs` tool to use Folio to its full potential and help the user effectively.

<code-snippet name="Folio Middleware Example" lang="php">
use function Laravel\Folio\{name, middleware};

name('admin.products');
middleware(['auth', 'verified', 'can:manage-products']);
?>
</code-snippet>

=== laravel/core rules ===

## Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Database

- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `php artisan make:model`.

### APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

### Controllers & Validation

- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

### Queues

- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

### Authentication & Authorization

- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

### URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

### Configuration

- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

### Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] <name>` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

### Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== laravel/v12 rules ===

## Laravel 12

- Use the `search-docs` tool to get version specific documentation.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

### Laravel 12 Structure

- No middleware files in `app/Http/Middleware/`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- **No app\Console\Kernel.php** - use `bootstrap/app.php` or `routes/console.php` for console configuration.
- **Commands auto-register** - files in `app/Console/Commands/` are automatically available and do not require manual registration.

### Database

- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 11 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models

- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

=== fluxui-free/core rules ===

## Flux UI Free

- This project is using the free edition of Flux UI. It has full access to the free components and variants, but does not have access to the Pro components.
- Flux UI is a component library for Livewire. Flux is a robust, hand-crafted, UI component library for your Livewire applications. It's built using Tailwind CSS and provides a set of components that are easy to use and customize.
- You should use Flux UI components when available.
- Fallback to standard Blade components if Flux is unavailable.
- If available, use Laravel Boost's `search-docs` tool to get the exact documentation and code snippets available for this project.
- Flux UI components look like this:

<code-snippet name="Flux UI Component Usage Example" lang="blade">
    <flux:button variant="primary"/>
</code-snippet>

### Available Components

This is correct as of Boost installation, but there may be additional components within the codebase.

<available-flux-components>
avatar, badge, brand, breadcrumbs, button, callout, checkbox, dropdown, field, heading, icon, input, modal, navbar, profile, radio, select, separator, switch, text, textarea, tooltip
</available-flux-components>

=== livewire/core rules ===

## Livewire Core

- Use the `search-docs` tool to find exact version specific documentation for how to write Livewire & Livewire tests.
- Use the `php artisan make:livewire [Posts\\CreatePost]` artisan command to create new components
- State should live on the server, with the UI reflecting it.
- All Livewire requests hit the Laravel backend, they're like regular HTTP requests. Always validate form data, and run authorization checks in Livewire actions.

## Livewire Best Practices

- Livewire components require a single root element.
- Use `wire:loading` and `wire:dirty` for delightful loading states.
- Add `wire:key` in loops:

    ```blade
    @foreach ($items as $item)
        <div wire:key="item-{{ $item->id }}">
            {{ $item->name }}
        </div>
    @endforeach
    ```

- Prefer lifecycle hooks like `mount()`, `updatedFoo()`) for initialization and reactive side effects:

<code-snippet name="Lifecycle hook examples" lang="php">
    public function mount(User $user) { $this->user = $user; }
    public function updatedSearch() { $this->resetPage(); }
</code-snippet>

## Testing Livewire

<code-snippet name="Example Livewire component test" lang="php">
    Livewire::test(Counter::class)
        ->assertSet('count', 0)
        ->call('increment')
        ->assertSet('count', 1)
        ->assertSee(1)
        ->assertStatus(200);
</code-snippet>

    <code-snippet name="Testing a Livewire component exists within a page" lang="php">
        $this->get('/posts/create')
        ->assertSeeLivewire(CreatePost::class);
    </code-snippet>

=== livewire/v3 rules ===

## Livewire 3

### Key Changes From Livewire 2

- These things changed in Livewire 2, but may not have been updated in this application. Verify this application's setup to ensure you conform with application conventions.
    - Use `wire:model.live` for real-time updates, `wire:model` is now deferred by default.
    - Components now use the `App\Livewire` namespace (not `App\Http\Livewire`).
    - Use `$this->dispatch()` to dispatch events (not `emit` or `dispatchBrowserEvent`).
    - Use the `components.layouts.app` view as the typical layout path (not `layouts.app`).

### New Directives

- `wire:show`, `wire:transition`, `wire:cloak`, `wire:offline`, `wire:target` are available for use. Use the documentation to find usage examples.

### Alpine

- Alpine is now included with Livewire, don't manually include Alpine.js.
- Plugins included with Alpine: persist, intersect, collapse, and focus.

### Lifecycle Hooks

- You can listen for `livewire:init` to hook into Livewire initialization, and `fail.status === 419` for the page expiring:

<code-snippet name="livewire:load example" lang="js">
document.addEventListener('livewire:init', function () {
    Livewire.hook('request', ({ fail }) => {
        if (fail && fail.status === 419) {
            alert('Your session expired');
        }
    });

    Livewire.hook('message.failed', (message, component) => {
        console.error(message);
    });

});
</code-snippet>

=== volt/core rules ===

## Livewire Volt

- This project uses Livewire Volt for interactivity within its pages. New pages requiring interactivity must also use Livewire Volt. There is documentation available for it.
- Make new Volt components using `php artisan make:volt [name] [--test] [--pest]`
- Volt is a **class-based** and **functional** API for Livewire that supports single-file components, allowing a component's PHP logic and Blade templates to co-exist in the same file
- Livewire Volt allows PHP logic and Blade templates in one file. Components use the `@livewire("volt-anonymous-fragment-eyJuYW1lIjoidm9sdC1hbm9ueW1vdXMtZnJhZ21lbnQtYmQ5YWJiNTE3YWMyMTgwOTA1ZmUxMzAxODk0MGJiZmIiLCJwYXRoIjoic3RvcmFnZVwvZnJhbWV3b3JrXC92aWV3c1wvMTUxYWRjZWRjMzBhMzllOWIxNzQ0ZDRiMWRjY2FjYWIuYmxhZGUucGhwIn0=", Livewire\Volt\Precompilers\ExtractFragments::componentArguments([...get_defined_vars(), ...array (
  )]))
  </code-snippet>

### Volt Class Based Component Example

To get started, define an anonymous class that extends Livewire\Volt\Component. Within the class, you may utilize all of the features of Livewire using traditional Livewire syntax:

<code-snippet name="Volt Class-based Volt Component Example" lang="php">
use Livewire\Volt\Component;

new class extends Component {
public $count = 0;

    public function increment()
    {
        $this->count++;
    }

} ?>

<div>
    <h1>{{ $count }}</h1>
    <button wire:click="increment">+</button>
</div>
</code-snippet>

### Testing Volt & Volt Components

- Use the existing directory for tests if it already exists. Otherwise, fallback to `tests/Feature/Volt`.

<code-snippet name="Livewire Test Example" lang="php">
use Livewire\Volt\Volt;

test('counter increments', function () {
Volt::test('counter')
->assertSee('Count: 0')
->call('increment')
->assertSee('Count: 1');
});
</code-snippet>

<code-snippet name="Volt Component Test Using Pest" lang="php">
declare(strict_types=1);

use App\Models\{User, Product};
use Livewire\Volt\Volt;

test('product form creates product', function () {
$user = User::factory()->create();

    Volt::test('pages.products.create')
        ->actingAs($user)
        ->set('form.name', 'Test Product')
        ->set('form.description', 'Test Description')
        ->set('form.price', 99.99)
        ->call('create')
        ->assertHasNoErrors();

    expect(Product::where('name', 'Test Product')->exists())->toBeTrue();

});
</code-snippet>

### Common Patterns

<code-snippet name="CRUD With Volt" lang="php">
<?php

use App\Models\Product;
use function Livewire\Volt\{state, computed};

state(['editing' => null, 'search' => '']);

$products = computed(fn() => Product::when($this->search,
fn($q) => $q->where('name', 'like', "%{$this->search}%")
)->get());

$edit = fn(Product $product) => $this->editing = $product->id;
$delete = fn(Product $product) => $product->delete();

?>

<!-- HTML / UI Here -->
</code-snippet>

<code-snippet name="Real-Time Search With Volt" lang="php">
    <flux:input
        wire:model.live.debounce.300ms="search"
        placeholder="Search..."
    />
</code-snippet>

<code-snippet name="Loading States With Volt" lang="php">
    <flux:button wire:click="save" wire:loading.attr="disabled">
        <span wire:loading.remove>Save</span>
        <span wire:loading>Saving...</span>
    </flux:button>
</code-snippet>

=== pint/core rules ===

## Laravel Pint Code Formatter

- You must run `vendor/bin/pint --dirty` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test`, simply run `vendor/bin/pint` to fix any formatting issues.

=== pest/core rules ===

## Pest

### Testing

- If you need to verify a feature is working, write or update a Unit / Feature test.

### Pest Tests

- All tests must be written using Pest. Use `php artisan make:test --pest <name>`.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files - these are core to the application.
- Tests should test all of the happy paths, failure paths, and weird paths.
- Tests live in the `tests/Feature` and `tests/Unit` directories.
- Pest tests look and behave like this:
  <code-snippet name="Basic Pest Test Example" lang="php">
  it('is true', function () {
  expect(true)->toBeTrue();
  });
  </code-snippet>

### Running Tests

- Run the minimal number of tests using an appropriate filter before finalizing code edits.
- To run all tests: `php artisan test`.
- To run all tests in a file: `php artisan test tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --filter=testName` (recommended after making a change to a related file).
- When the tests relating to your changes are passing, ask the user if they would like to run the entire test suite to ensure everything is still passing.

### Pest Assertions

- When asserting status codes on a response, use the specific method like `assertForbidden` and `assertNotFound` instead of using `assertStatus(403)` or similar, e.g.:
  <code-snippet name="Pest Example Asserting postJson Response" lang="php">
  it('returns all', function () {
  $response = $this->postJson('/api/docs', []);

                    $response->assertSuccessful();

    });
    </code-snippet>

### Mocking

- Mocking can be very helpful when appropriate.
- When mocking, you can use the `Pest\Laravel\mock` Pest function, but always import it via `use function Pest\Laravel\mock;` before using it. Alternatively, you can use `$this->mock()` if existing tests do.
- You can also create partial mocks using the same import or self method.

### Datasets

- Use datasets in Pest to simplify tests which have a lot of duplicated data. This is often the case when testing validation rules, so consider going with this solution when writing tests for validation rules.

<code-snippet name="Pest Dataset Example" lang="php">
it('has emails', function (string $email) {
    expect($email)->not->toBeEmpty();
})->with([
    'james' => 'james@laravel.com',
    'taylor' => 'taylor@laravel.com',
]);
</code-snippet>

=== tailwindcss/core rules ===

## Tailwind Core

- Use Tailwind CSS classes to style HTML, check and use existing tailwind conventions within the project before writing your own.
- Offer to extract repeated patterns into components that match the project's conventions (i.e. Blade, JSX, Vue, etc..)
- Think through class placement, order, priority, and defaults - remove redundant classes, add classes to parent or child carefully to limit repetition, group elements logically
- You can use the `search-docs` tool to get exact examples from the official documentation when needed.

### Spacing

- When listing items, use gap utilities for spacing, don't use margins.

                  <code-snippet name="Valid Flex Gap Spacing Example" lang="html">
                      <div class="flex gap-8">
                          <div>Superior</div>
                          <div>Michigan</div>
                          <div>Erie</div>
                      </div>
                  </code-snippet>

### Dark Mode

- If existing pages and components support dark mode, new pages and components must support dark mode in a similar way, typically using `dark:`.

=== tailwindcss/v4 rules ===

## Tailwind 4

- Always use Tailwind CSS v4 - do not use the deprecated utilities.
- `corePlugins` is not supported in Tailwind v4.
- In Tailwind v4, you import Tailwind using a regular CSS `@import` statement, not using the `@tailwind` directives used in v3:

<code-snippet name="Tailwind v4 Import Tailwind Diff" lang="diff"

- @tailwind base;
- @tailwind components;
- @tailwind utilities;

* @import "tailwindcss";
  </code-snippet>

### Replaced Utilities

- Tailwind v4 removed deprecated utilities. Do not use the deprecated option - use the replacement.
- Opacity values are still numeric.

| Deprecated | Replacement |
|------------+--------------|
| bg-opacity-_ | bg-black/_ |
| text-opacity-_ | text-black/_ |
| border-opacity-_ | border-black/_ |
| divide-opacity-_ | divide-black/_ |
| ring-opacity-_ | ring-black/_ |
| placeholder-opacity-_ | placeholder-black/_ |
| flex-shrink-_ | shrink-_ |
| flex-grow-_ | grow-_ |
| overflow-ellipsis | text-ellipsis |
| decoration-slice | box-decoration-slice |
| decoration-clone | box-decoration-clone |
</laravel-boost-guidelines>
