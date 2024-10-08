<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\text;
use function Laravel\Prompts\warning;

class FissionInstall extends Command
{
    protected $signature = 'fission:install {name? : The project name}';

    protected $description = 'Run the Fission installation process';

    public function handle()
    {
        info('Starting Fission installation...');

        // Run composer install
        if (! File::exists('vendor')) {
            info('Running composer install...');
            exec('composer install');
        } else {
            warning('Vendor directory already exists. Skipping composer install.');
        }

        // Run npm install
        if (! File::exists('node_modules')) {
            info('Running npm install...');
            exec('npm install');
        } else {
            warning('Node modules already exist. Skipping npm install.');
        }

        // Run flux:install
        info('Running flux:install...');
        $this->call('flux:install');

        // Copy .env.example to .env
        info('Checking .env file...');
        if (! File::exists('.env')) {
            File::copy('.env.example', '.env');
            info('.env file created successfully.');
        } else {
            warning('.env file already exists. Skipping.');
        }

        // Generate application key
        info('Checking application key...');
        if (empty(env('APP_KEY'))) {
            $this->call('key:generate');
        } else {
            warning('Application key already exists. Skipping.');
        }

        // Run database migrations
        if (confirm('Do you want to run database migrations?', true)) {
            info('Running database migrations...');
            $this->call('migrate');
        }

        // Get project name
        $defaultName = $this->argument('name') ?: basename(getcwd());
        $name = text(
            label: 'What is the name of your project?',
            placeholder: $defaultName,
            default: $defaultName,
            required: true
        );

        // Update .env with project name
        $this->updateEnv('APP_NAME', $name);

        info('Fission installation completed successfully!');
    }

    private function updateEnv($key, $value)
    {
        $path = base_path('.env');

        if (File::exists($path)) {
            file_put_contents($path, preg_replace(
                "/^{$key}=.*/m",
                "{$key}=\"{$value}\"",
                file_get_contents($path)
            ));
        }
    }
}
