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

        // Run npm install
        if (! File::exists('node_modules')) {
            info('Running npm install...');
            exec('npm install');
        } else {
            warning('Node modules already exist. Skipping npm install.');
        }

        // Run flux:activate
        info('Activating Flux...');
        $this->call('flux:activate');

        // Copy .env.example to .env
        $this->setupEnvFile();

        // Generate application key
        $this->generateAppKey();

        // Run database migrations
        $this->runMigrations();

        // Set project name
        $this->setProjectName();

        info('Fission installation completed successfully!');
    }

    private function setupEnvFile()
    {
        info('Setting up .env file...');
        if (! File::exists('.env')) {
            File::copy('.env.example', '.env');
            info('.env file created successfully.');
        } else {
            warning('.env file already exists. Skipping.');
        }
    }

    private function generateAppKey()
    {
        info('Checking application key...');
        if (empty(env('APP_KEY'))) {
            $this->call('key:generate');
        } else {
            warning('Application key already exists. Skipping.');
        }
    }

    private function runMigrations()
    {
        if (confirm('Do you want to run database migrations?', true)) {
            info('Running database migrations...');
            $this->call('migrate');
        }
    }

    private function setProjectName()
    {
        $defaultName = $this->argument('name') ?: basename(getcwd());
        $name = text(
            label: 'What is the name of your project?',
            placeholder: $defaultName,
            default: $defaultName,
            required: true
        );

        $this->updateEnv('APP_NAME', $name);
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
