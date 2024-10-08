<p align="center">
  <picture>
    <source media="(prefers-color-scheme: dark)" srcset="https://github.com/user-attachments/assets/7f3c77b9-e549-4887-872e-a0d512678945">
    <source media="(prefers-color-scheme: light)" srcset="https://github.com/user-attachments/assets/8cf172b8-0e36-47c4-b096-a6fad0044e32">
    <img alt="Fission Logo" src="https://github.com/user-attachments/assets/fd074588-4ffd-47f3-af6e-a24500ecbc55">
  </picture>
</p>

> [!IMPORTANT]
> This is an opinionated starter kit created by me (Josh Cirre) using Laravel Folio, Livewire Volt, and Livewire Flux. While PRs are welcome, this is designed to fit the needs of one person.

> [!TIP]
> To get up and running quickly, clone this repo and run `bash install.sh` in your cloned directory.

## Why Does This Exist?
Up until Livewire Flux released, I used Breeze as a starting point for 99% of new projects that I would create. Typically, those new projects were built for demos on videos or starting points for tutorials. In addition, I would start side projects or app ideas with Breeze, as well.

Eventually I knew I wanted to create my own starting kit that worked well for what I needed in most scenarios. Authentication and a dashboard where I can start writing code.

Once Livewire Flux released, it was the perfect time to make this happen.

## Flux License Required
A license for Flux (technically, "Flux Pro") is required to use Fission. Fission does not contain any of Flux's CSS, built CSS, or Blade components.

## Installation

This project includes a custom installation script that streamlines the setup process. Here's what the `install.sh` script does in a nutshell:

- Installs required Composer packages
- Runs custom `fission:install` Artisan command
- Sets up environment (.env file)
- Activates Flux Pro (skipped if auth file found)
- Installs NPM dependencies
- Generates application key
- Offers to run database migrations
- Configures project name and URL
- Offers to remove installation files
- Provides instructions to start local development server

To install, simply run:

`bash install.sh`

## License

The Fission starter kit is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
