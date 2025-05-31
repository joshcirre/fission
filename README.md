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
> To get up and running quickly, use the new Laravel installer with the using option: `laravel new my-project --using=joshcirre/fission`

## Why Does This Exist?

Up until Livewire Flux released, I used Breeze as a starting point for 99% of new projects that I would create. Typically, those new projects were built for demos on videos or starting points for tutorials. In addition, I would start side projects or app ideas with Breeze, as well.

Eventually I knew I wanted to create my own starting kit that worked well for what I needed in most scenarios. Authentication and a dashboard where I can start writing code.

Once Livewire Flux released, it was the perfect time to make this happen.

## Flux License Required

A license for Flux (technically, "Flux Pro") is required to use Fission. Fission does not contain any of Flux's CSS, built CSS, or Blade components. However, if you do not have a license there are only two components used in the Flux Pro version (Toast and Card). Feel free to remove them from the starter kit.

## Installation

This project includes a custom installation script that streamlines the setup process. If you are not using the Laravel installer, you can still use this script to install Fission. Use the composer create command to do so: `composer create-project joshcirre/fission myapp`.

- Installs required Composer packages
- Sets up environment (.env file)
- Activates Flux Pro (skipped if auth file found)
- Installs NPM dependencies
- Generates application key
- Offers to run database migrations
- Configures project name and URL
- Offers to remove installation files
- Provides instructions to start local development server

## License

The Fission starter kit is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
