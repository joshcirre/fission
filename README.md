<p align="center">
  <picture>
    <source media="(prefers-color-scheme: dark)" srcset="https://github.com/user-attachments/assets/7f3c77b9-e549-4887-872e-a0d512678945">
    <source media="(prefers-color-scheme: light)" srcset="https://github.com/user-attachments/assets/8cf172b8-0e36-47c4-b096-a6fad0044e32">
    <img alt="Fission Logo" src="https://github.com/user-attachments/assets/fd074588-4ffd-47f3-af6e-a24500ecbc55">
  </picture>
</p>

> [!IMPORTANT]
> This is an opinionated starter kit created by me (Josh Cirre) using Laravel Livewire and Livewire Flux. While PRs are welcome, this is designed to fit the needs of one person.

> [!TIP]
> Clone the repository and run `composer setup` to get started quickly. See [Installation](#installation) below.

## Why Does This Exist?

Up until Livewire Flux released, I used Breeze as a starting point for 99% of new projects that I would create. Typically, those new projects were built for demos on videos or starting points for tutorials. In addition, I would start side projects or app ideas with Breeze, as well.

Eventually I knew I wanted to create my own starting kit that worked well for what I needed in most scenarios. Authentication and a dashboard where I can start writing code.

Once Livewire Flux released, it was the perfect time to make this happen.

## Flux License Required

A license for Flux (technically, "Flux Pro") is required to use Fission. Fission does not contain any of Flux's CSS, built CSS, or Blade components. However, if you do not have a license there are only two components used in the Flux Pro version (Toast and Card). Feel free to remove them from the starter kit.

## Installation

### Quick Start (Recommended)

```bash
git clone https://github.com/joshcirre/fission.git my-project
cd my-project
composer setup
```

### Using Composer Create-Project

```bash
composer create-project joshcirre/fission my-project
cd my-project
composer setup
```

> [!NOTE]
> The `laravel new --using` flag is not recommended due to archive extraction issues with special characters in filenames.

The `composer setup` command handles:

- Environment configuration (.env)
- Application key generation
- SQLite database creation
- Database migrations
- NPM dependency installation
- Asset building

## Development

```bash
composer dev          # Start server, queue, logs, and Vite
```

### Code Quality

Fission enforces strict code quality through automated tooling:

```bash
composer fix          # Fix everything: types, refactoring, formatting
composer test         # Run all checks: tests, linting, types, refactoring
```

| Command             | Purpose                                              |
| ------------------- | ---------------------------------------------------- |
| `composer fix`      | PHPStan → Rector → Prettier → Pint                   |
| `composer test`     | Typos → Pest → Lint check → PHPStan → Rector dry-run |
| `composer lint`     | Pint + Prettier (quick format)                       |
| `composer refactor` | Rector only                                          |

### Individual Test Commands

```bash
composer test:unit          # Pest tests (parallel)
composer test:unit:coverage # Pest with coverage
composer test:types         # PHPStan analysis
composer test:lint          # Check formatting (no fix)
composer test:refactor      # Rector dry-run
composer test:typos         # Peck typo checker
```

### Tooling Stack

- **[Pest](https://pestphp.com)** - Testing framework
- **[PHPStan](https://phpstan.org)** + Larastan - Static analysis (max level)
- **[Rector](https://getrector.com)** - Automated refactoring
- **[Pint](https://laravel.com/docs/pint)** - PHP code style (strict Laravel)
- **[Prettier](https://prettier.io)** - JS/CSS formatting
- **[Peck](https://github.com/peckphp/peck)** - Typo detection

## License

The Fission starter kit is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
