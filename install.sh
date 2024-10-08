#!/bin/bash

# Ensure script is run from the project root
if [ ! -f "composer.json" ]; then
    echo "Please run this script from the project root directory."
    exit 1
fi

# Run composer install
echo "Composer dependencies being installed..."
composer install

# Run the Fission installation
php artisan fission:install

# Cleanup
echo "Do you want to perform cleanup? (y/n)"
read answer

if [ "$answer" != "${answer#[Yy]}" ] ;then
    echo "Performing cleanup..."

    # Remove Laravel Prompts
    composer remove laravel/prompts

    # Remove the FissionInstall command
    rm app/Console/Commands/FissionInstall.php

    # Remove this install script
    rm -- "$0"

    echo "Cleanup finished!"
else
    echo "Skipping cleanup. You can manually remove unnecessary files later."
fi

echo "Fission installation complete."
