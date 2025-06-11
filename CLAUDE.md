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

Remember: Always use `php artisan folio:page` for new pages, wrap interactive elements in `@volt`, and follow Livewire 3 conventions. Refer to Context7 MCP for Laravel docs and Flux UI MCP for component documentation.

# important-instruction-reminders

Do what has been asked; nothing more, nothing less.
NEVER create files unless they're absolutely necessary for achieving your goal.
ALWAYS prefer editing an existing file to creating a new one.
NEVER proactively create documentation files (\*.md) or README files. Only create documentation files if explicitly requested by the User.

## Creating New Features: Step-by-Step Guide

### 1. Creating a New Page

**Use Folio for automatic routing:**

```bash
php artisan folio:page products
# Creates: resources/views/pages/products.blade.php → /products

php artisan folio:page products/[id]
# Creates: resources/views/pages/products/[id].blade.php → /products/{id}
```

**Basic page structure:**

```php
<?php
use function Laravel\Folio\name;

name('products.index');
?>

<x-layouts.app>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold mb-6">Products</h1>
            <!-- Page content -->
        </div>
    </div>
</x-layouts.app>
```

**With Volt interactivity:**

```php
<?php
use function Laravel\Folio\name;
use function Livewire\Volt\{state, computed};

name('products.index');

state(['search' => '']);

$filteredProducts = computed(function () {
    return Product::query()
        ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
        ->get();
});
?>

@volt
<x-layouts.app>
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
</x-layouts.app>
@endvolt
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
@volt
<?php
use App\Actions\CreateProduct;
use App\Livewire\Forms\ProductForm;
use function Livewire\Volt\{form, mount};

form(ProductForm::class);

$create = function (CreateProduct $createProduct) {
    $this->validate();

    $createProduct->handle(auth()->user(), $this->form->all());

    $this->form->reset();
    session()->flash('success', 'Product created successfully!');
    $this->redirect(route('products.index'));
};
?>

<form wire:submit="create" class="space-y-6">
    <flux:input wire:model="form.name" label="Name" />
    <flux:textarea wire:model="form.description" label="Description" />
    <flux:input wire:model="form.price" label="Price" type="number" step="0.01" />
    <flux:checkbox wire:model="form.is_active" label="Active" />

    <flux:button type="submit" variant="primary">Create Product</flux:button>
</form>
@endvolt
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
        ->assertHasNoErrors();

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
@volt
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
