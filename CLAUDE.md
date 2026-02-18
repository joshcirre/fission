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

All Livewire components use **Single-File Component (SFC)** format with the `⚡` emoji prefix. The `make:livewire` command defaults to SFC with emoji enabled — no flags needed.

There are three view namespaces:

| Namespace | Directory | Purpose |
|---|---|---|
| `pages::` | `resources/views/pages/` | Full-page Livewire components routed via `Route::livewire()` |
| `layouts::` | `resources/views/layouts/` | Layout templates referenced via `#[Layout('layouts::app')]` |
| *(default)* | `resources/views/components/` | Reusable Livewire SFC components (non-page) |

### Page Components

Pages are Livewire 4 full-page components in the `pages::` namespace with `⚡` prefix:

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

### Reusable Components

Non-page components live in `resources/views/components/` and are referenced without a namespace prefix:

```
resources/views/components/
├── ⚡counter.blade.php         → <livewire:counter />
└── dashboard/
    └── ⚡stats-card.blade.php  → <livewire:dashboard.stats-card />
```

### Layouts

Layout templates live in the `layouts::` namespace:

```
resources/views/layouts/
├── app.blade.php               → #[Layout('layouts::app')]
└── auth.blade.php              → #[Layout('layouts::auth')]
```

### SFC Structure

All components follow this single-file format:

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

### Creating Components

**Pages** (full-page, routed):

```bash
php artisan make:livewire pages::<name>
```

Then add route in `routes/web.php`:

```php
Route::livewire('/path', 'pages::name')->name('name');
```

**Reusable components** (non-page):

```bash
php artisan make:livewire <name>
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
├── components/       # Reusable Livewire SFC components (⚡ prefix)
├── layouts/          # Layout templates (layouts:: namespace)
└── pages/            # Full-page Livewire SFC components (⚡ prefix, pages:: namespace)

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

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to enhance the user's satisfaction building Laravel applications.

## Foundational Context
This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4.17
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- livewire/flux (FLUXUI_FREE) - v2
- livewire/flux-pro (FLUXUI_PRO) - v2
- livewire/livewire (LIVEWIRE) - v4
- larastan/larastan (LARASTAN) - v3
- laravel/mcp (MCP) - v0
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- rector/rector (RECTOR) - v2
- prettier (PRETTIER) - v3
- tailwindcss (TAILWINDCSS) - v4

## Conventions
- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts
- Do not create verification scripts or tinker when tests cover that functionality and prove it works. Unit and feature tests are more important.

## Application Structure & Architecture
- Stick to existing directory structure; don't create new base folders without approval.
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
- Use the `list-artisan-commands` tool when you need to call an Artisan command to double-check the available parameters.

## URLs
- Whenever you share a project URL with the user, you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain/IP, and port.

## Tinker / Debugging
- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.

## Reading Browser Logs With the `browser-logs` Tool
- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)
- Boost comes with a powerful `search-docs` tool you should use before any other approaches when dealing with Laravel or Laravel ecosystem packages. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- The `search-docs` tool is perfect for all Laravel-related packages, including Laravel, Inertia, Livewire, Filament, Tailwind, Pest, Nova, Nightwatch, etc.
- You must use this tool to search for Laravel ecosystem documentation before falling back to other approaches.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic-based queries to start. For example: `['rate limiting', 'routing rate limiting', 'routing']`.
- Do not add package names to queries; package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax
- You can and should pass multiple queries at once. The most relevant results will be returned first.

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'.
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit".
3. Quoted Phrases (Exact Position) - query="infinite scroll" - words must be adjacent and in that order.
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit".
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms.

=== php rules ===

## PHP

- Always use curly braces for control structures, even if it has one line.

### Constructors
- Use PHP 8 constructor property promotion in `__construct()`.
    - <code-snippet>public function __construct(public GitHub $github) { }</code-snippet>
- Do not allow empty `__construct()` methods with zero parameters unless the constructor is private.

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
- Prefer PHPDoc blocks over inline comments. Never use comments within the code itself unless there is something very complex going on.

## PHPDoc Blocks
- Add useful array shape type definitions for arrays when appropriate.

## Enums
- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.

=== tests rules ===

## Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== laravel/core rules ===

## Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Database
- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries.
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
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

### Vite Error
- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== laravel/v12 rules ===

## Laravel 12

- Use the `search-docs` tool to get version-specific documentation.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

### Laravel 12 Structure
- In Laravel 12, middleware are no longer registered in `app/Http/Kernel.php`.
- Middleware are configured declaratively in `bootstrap/app.php` using `Application::configure()->withMiddleware()`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- The `app\Console\Kernel.php` file no longer exists; use `bootstrap/app.php` or `routes/console.php` for console configuration.
- Console commands in `app/Console/Commands/` are automatically available and do not require manual registration.

### Database
- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 12 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models
- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

=== fluxui-pro/core rules ===

## Flux UI Pro

- This project is using the Pro version of Flux UI. It has full access to the free components and variants, as well as full access to the Pro components and variants.
- Flux UI is a component library for Livewire. Flux is a robust, hand-crafted UI component library for your Livewire applications. It's built using Tailwind CSS and provides a set of components that are easy to use and customize.
- You should use Flux UI components when available.
- Fallback to standard Blade components if Flux is unavailable.
- If available, use the `search-docs` tool to get the exact documentation and code snippets available for this project.
- Flux UI components look like this:

<code-snippet name="Flux UI Component Example" lang="blade">
    <flux:button variant="primary"/>
</code-snippet>

### Available Components
This is correct as of Boost installation, but there may be additional components within the codebase.

<available-flux-components>
accordion, autocomplete, avatar, badge, brand, breadcrumbs, button, calendar, callout, card, chart, checkbox, command, composer, context, date-picker, dropdown, editor, field, file-upload, heading, icon, input, kanban, modal, navbar, otp-input, pagination, pillbox, popover, profile, radio, select, separator, skeleton, slider, switch, table, tabs, text, textarea, time-picker, toast, tooltip
</available-flux-components>

=== livewire/core rules ===

## Livewire

- Use the `search-docs` tool to find exact version-specific documentation for how to write Livewire and Livewire tests.
- Use the `php artisan make:livewire [Posts\CreatePost]` Artisan command to create new components.
- State should live on the server, with the UI reflecting it.
- All Livewire requests hit the Laravel backend; they're like regular HTTP requests. Always validate form data and run authorization checks in Livewire actions.

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

- Prefer lifecycle hooks like `mount()`, `updatedFoo()` for initialization and reactive side effects:

<code-snippet name="Lifecycle Hook Examples" lang="php">
    public function mount(User $user) { $this->user = $user; }
    public function updatedSearch() { $this->resetPage(); }
</code-snippet>

## Testing Livewire

<code-snippet name="Example Livewire Component Test" lang="php">
    Livewire::test(Counter::class)
        ->assertSet('count', 0)
        ->call('increment')
        ->assertSet('count', 1)
        ->assertSee(1)
        ->assertStatus(200);
</code-snippet>

<code-snippet name="Testing Livewire Component Exists on Page" lang="php">
    $this->get('/posts/create')
    ->assertSeeLivewire(CreatePost::class);
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
- All tests must be written using Pest. Use `php artisan make:test --pest {name}`.
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
- To run all tests: `php artisan test --compact`.
- To run all tests in a file: `php artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --compact --filter=testName` (recommended after making a change to a related file).
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
- Use datasets in Pest to simplify tests that have a lot of duplicated data. This is often the case when testing validation rules, so consider this solution when writing tests for validation rules.

<code-snippet name="Pest Dataset Example" lang="php">
it('has emails', function (string $email) {
    expect($email)->not->toBeEmpty();
})->with([
    'james' => 'james@laravel.com',
    'taylor' => 'taylor@laravel.com',
]);
</code-snippet>

=== pest/v4 rules ===

## Pest 4

- Pest 4 is a huge upgrade to Pest and offers: browser testing, smoke testing, visual regression testing, test sharding, and faster type coverage.
- Browser testing is incredibly powerful and useful for this project.
- Browser tests should live in `tests/Browser/`.
- Use the `search-docs` tool for detailed guidance on utilizing these features.

### Browser Testing
- You can use Laravel features like `Event::fake()`, `assertAuthenticated()`, and model factories within Pest 4 browser tests, as well as `RefreshDatabase` (when needed) to ensure a clean state for each test.
- Interact with the page (click, type, scroll, select, submit, drag-and-drop, touch gestures, etc.) when appropriate to complete the test.
- If requested, test on multiple browsers (Chrome, Firefox, Safari).
- If requested, test on different devices and viewports (like iPhone 14 Pro, tablets, or custom breakpoints).
- Switch color schemes (light/dark mode) when appropriate.
- Take screenshots or pause tests for debugging when appropriate.

### Example Tests

<code-snippet name="Pest Browser Test Example" lang="php">
it('may reset the password', function () {
    Notification::fake();

    $this->actingAs(User::factory()->create());

    $page = visit('/sign-in'); // Visit on a real browser...

    $page->assertSee('Sign In')
        ->assertNoJavascriptErrors() // or ->assertNoConsoleLogs()
        ->click('Forgot Password?')
        ->fill('email', 'nuno@laravel.com')
        ->click('Send Reset Link')
        ->assertSee('We have emailed your password reset link!')

    Notification::assertSent(ResetPassword::class);
});
</code-snippet>

<code-snippet name="Pest Smoke Testing Example" lang="php">
$pages = visit(['/', '/about', '/contact']);

$pages->assertNoJavascriptErrors()->assertNoConsoleLogs();
</code-snippet>

=== tailwindcss/core rules ===

## Tailwind CSS

- Use Tailwind CSS classes to style HTML; check and use existing Tailwind conventions within the project before writing your own.
- Offer to extract repeated patterns into components that match the project's conventions (i.e. Blade, JSX, Vue, etc.).
- Think through class placement, order, priority, and defaults. Remove redundant classes, add classes to parent or child carefully to limit repetition, and group elements logically.
- You can use the `search-docs` tool to get exact examples from the official documentation when needed.

### Spacing
- When listing items, use gap utilities for spacing; don't use margins.

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

## Tailwind CSS 4

- Always use Tailwind CSS v4; do not use the deprecated utilities.
- `corePlugins` is not supported in Tailwind v4.
- In Tailwind v4, configuration is CSS-first using the `@theme` directive — no separate `tailwind.config.js` file is needed.

<code-snippet name="Extending Theme in CSS" lang="css">
@theme {
  --color-brand: oklch(0.72 0.11 178);
}
</code-snippet>

- In Tailwind v4, you import Tailwind using a regular CSS `@import` statement, not using the `@tailwind` directives used in v3:

<code-snippet name="Tailwind v4 Import Tailwind Diff" lang="diff">
   - @tailwind base;
   - @tailwind components;
   - @tailwind utilities;
   + @import "tailwindcss";
</code-snippet>

### Replaced Utilities
- Tailwind v4 removed deprecated utilities. Do not use the deprecated option; use the replacement.
- Opacity values are still numeric.

| Deprecated |	Replacement |
|------------+--------------|
| bg-opacity-* | bg-black/* |
| text-opacity-* | text-black/* |
| border-opacity-* | border-black/* |
| divide-opacity-* | divide-black/* |
| ring-opacity-* | ring-black/* |
| placeholder-opacity-* | placeholder-black/* |
| flex-shrink-* | shrink-* |
| flex-grow-* | grow-* |
| overflow-ellipsis | text-ellipsis |
| decoration-slice | box-decoration-slice |
| decoration-clone | box-decoration-clone |
</laravel-boost-guidelines>

<fuel>
## Fuel Protocol
- Prefer end-to-end verify; if blocked, fix it, or use a needs-human task to block yourself and exit.
- Keep files <~500 LOC; split/refactor as needed.
- Commits: Conventional Commits (feat|fix|refactor|build|ci|chore|docs|style|perf|test).
- Docs: Live in .fuel/docs/
- Docs: Keep notes short; update docs when behavior/API changes (no ship w/o docs).
- Critical Thinking: Fix root cause (not band-aid). Unsure: read more code, ask w/ short options.

## Fuel Task Management

This project uses **Fuel** for agent orchestration, epic management, and task tracking.

Run `fuel --help` to see all available commands, and `fuel <command> --help` for detailed help on any command.

### Quick Reference

```bash
fuel add "Task title" -d "Simple description" [--blocked-by=f-id]           # Add a new task with simple description
fuel add "Task title" <<'DESC'   # Add task with complex/multi-line description (see Descriptions with Special Characters)
Multi-line description here
DESC
fuel add:json e-xxxxxx [path|-] # Bulk import tasks from JSON file or STDIN
fuel add "Idea" --someday       # Add to backlog (future work)
fuel show <id>                  # View task details
fuel tree                       # Tree view
fuel backlog                    # List backlog items
fuel promote <b-id>             # Promote backlog item to task
fuel defer <f-id>               # Move task to backlog
fuel dep:add <id> <blocker>     # Add dependency
fuel dep:remove <id> <blocker>  # Remove dependency
fuel close <id>                  # Close task without marking as completed (soft remove)
fuel epic:plan <e-id> <<'PLAN_EOF' # Update epic plan file (piped content replaces file)
```

You must never work on fuel tasks you add in-session. Fuel will pickup and manage tasks. You work on non-fuel tasks when requested.

### TodoWrite vs Fuel

Use **TodoWrite** for single-session step tracking. Use **fuel** for work that anything moderately complex or that outlives the session (multi-session, dependencies, discovered work for future). When unsure, prefer fuel.

### Epic Plan Files

Plans are stored in `.fuel/plans/{epic-title-kebab}-{epic-id}.md` and committed to git.

**When planning an epic:**
1. Ask the user if they'd like it selfguided or parallel.
2. Create epic: `fuel epic:add "Feature name" [--selfguided]` to get the ID
3. Use `fuel epic:plan <e-id> < plan.md` to update the plan file with your planning content
    - there is a different structure depending on the task, it's important you merge your thinking into the existing structure
4. If non-selfguided: breakdown the epic into well defined tasks (use the skill)

**Epic review tasks are MANDATORY for non-selfguided epics.** Always use `--complexity=complex` and list acceptance criteria:

```bash
fuel add "Review: Feature name" \
  --epic=e-xxxx \
  --complexity=complex \
  --blocked-by=f-task1,f-task2,... \
  --description="Verify epic complete. Acceptance criteria: 1) [behavior], 2) [API works], 3) [errors handled], 4) All tests pass: vendor/bin/pest path/to/tests"
```

**Review tasks must verify:**
1. **Intent** - Does it match the epic description? Would the user be happy?
2. **Correctness** - Do behaviors work? Tests pass? Edge cases handled?
3. **Quality** - No debug calls (dd, console.log), no useless comments, follows patterns

Parallel example:
```bash
fuel epic:add "Add user preferences"    # Create epic (note the ID)
fuel add "Add preferences API" --epic=e-xxxx -e e-xxxx  # Link task
fuel add "Add preferences UI" --epic=e-xxxx --blocked-by=f-xxxx             # Link another
fuel epics                               # List all epics with status
fuel epic:show <e-id>                   # View epic + linked tasks
fuel epic:reviewed <e-id>               # Mark as human-reviewed
```

When parallel tasks share an interface, define it in a parent task's description. Avoid parallel work on tasks touching same files - use dependencies instead.
**Always use epics for multi-task work.** Standalone tasks are fine for single-file fixes.

### Task Options

```bash
fuel add "Title" --description="..." --type=bug|fix|feature|task|epic|chore|docs|test|refactor --priority=0|1|2|3|4 --blocked-by=f-xxxx --labels=api,urgent --complexity=trivial|simple|moderate|complex --epic=e-xxxx --status=open|in_progress|review|done|cancelled|someday|paused --attach=path/or/url
```
**Always set `--complexity`:** `trivial` (typos) | `simple` (single focus) | `moderate` (multiple files) | `complex` (multiple files, requires judgement or careful coordination)

**Task Attachments:** Use `--attach=<path-or-url>` (repeatable) to attach files or URLs as context. Stored under `.fuel/context/tasks/<task_id>/`.

### Writing Good Descriptions

Descriptions should be explicit enough for a less capable agent to complete without guessing. Include: files to modify (exact paths), what to change (methods, patterns), expected behavior, and patterns to follow. **Give one clear solution, not options—subagents execute, they don't decide.**

**Bad**: "Fix the ID display bug"
**Good**: "BoardCommand.php:320 uses substr($id, 5, 4) for old format. Change to substr($id, 2, 6) for f-xxxxxx format."

### Descriptions with Special Characters

For simple one-line descriptions without special characters, `-d` works fine:
```bash
fuel add "Title" -d "Simple description"
```

For multi-line or complex descriptions (containing quotes, newlines, code snippets, special chars), use heredoc piping:
```bash
fuel add "Task title" --complexity=moderate --epic=e-xxxx <<'DESC'
Multi-line description here.
Can contain "quotes", newlines, code snippets, etc.
Files to modify: app/Services/FooService.php
DESC
```

⚠️  **AVOID using `-d`/`--description` for descriptions containing quotes, newlines, or special shell characters** - they will be mangled by shell escaping. Use heredoc piping instead.

### Cross-Project and External Dependencies

Your messages may not be read by a human. If work requires action in another repo, project, backend, frontend, or external system, create a task with the `needs-human` label so it gets flagged for human review.

**Example:**
```bash
fuel add "Title" --labels=needs-human --description="..."
```

Without this label, cross-project dependencies will be missed.

### Backlog Management

The backlog is for **rough ideas and future work** that isn't ready to implement yet. Tasks are for **work ready to implement now**.

**When to use backlog vs tasks:**
- **Backlog (`fuel add --someday`)**: Rough ideas, future enhancements, "nice to have" features, exploratory concepts, work that needs more thought before implementation
- **Tasks (`fuel add`)**: Work that's ready to implement now, has clear requirements, can be started immediately

### Testing Visual Changes with Browser
Use the fuel browser testing skill. If you don't have the skill, run fuel --help | grep -i browser.

fuel includes a browser daemon for testing webpages:

**Tips:**
- Screenshots are saved to `/tmp` by default, specify custom paths as needed
- Browser daemon auto-manages lifecycle, no manual cleanup needed

### Git Commits
- Quality gates enforced via pre-commit hook
- FORBIDDEN: `git commit --no-verify`, `git commit -n` (bypasses quality checks)

### Dev Processes

Use `fuel run` to manage long-lived dev processes (artisan serve, Vite, queue workers). Define processes in `.fuel/run.yml`.

```bash
fuel run             # Start all processes in .fuel/run.yml
fuel run:stop       # Stop all running processes
fuel run:status     # Show status (JSON) - includes URLs for web services
fuel run:logs       # Stream output from all processes
```

**Tip:** Run `fuel run:status` after starting to get URLs for Vite, artisan serve, etc.</fuel>
