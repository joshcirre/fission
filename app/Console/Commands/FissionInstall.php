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
        app()->detectEnvironment(function () {
            return 'local';
        });

        info('Starting Fission installation...');

        // Run npm install
        if (! File::exists('node_modules')) {
            info('Running npm install...');
            exec('npm install');
        } else {
            warning('Node modules already exist. Skipping npm install.');
        }

        $this->copyAuthJson();

        // Run flux:activate
        info('Activating Flux...');
        $this->call('flux:activate');

        $this->setupEnvFile();
        $this->reloadEnvironment();  // Add this line
        $this->generateAppKey();
        $this->runMigrations();
        $this->setProjectName();

        $this->cleanup();

        info('Fission installation completed successfully! ☢️');
        info('👉 Run `npm run dev` to start the local server.');
        info('Keep creating. 🫡');
    }

    private function setupEnvFile()
    {
        info('Setting up .env file...');
        if (! File::exists('.env')) {
            File::copy('.env.example', '.env');
            info('.env file created successfully.');
        } else {
            warning('.env file already exists. Skipping creation.');
        }

        // Ensure APP_ENV is set to local
        $envContent = File::get('.env');
        if (! preg_match('/^APP_ENV=/', $envContent)) {
            File::append('.env', "\nAPP_ENV=local");
            info('APP_ENV set to local.');
        } else {
            $envContent = preg_replace('/^APP_ENV=(.*)$/m', 'APP_ENV=local', $envContent);
            File::put('.env', $envContent);
            info('APP_ENV updated to local.');
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
            $this->call('migrate', [
                '--force' => true, // This will bypass the production check
            ]);
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

        $defaultUrl = 'http://127.0.0.1:8000';
        $url = text(
            label: 'What is the URL of your project?',
            placeholder: $defaultUrl,
            default: $defaultUrl,
            required: true
        );

        $this->updateEnv('APP_URL', $url);
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

    private function cleanup()
    {
        if (confirm('Do you want to remove the installation files?', true)) {
            info('Removing installation files...');

            // Remove the entire Commands folder
            File::deleteDirectory(app_path('Console'));

            // Remove the install.sh script
            File::delete(base_path('install.sh'));

            info('Installation files removed.');
        } else {
            info('Installation files kept. You can manually remove them later if needed.');
        }
    }

    private function reloadEnvironment()
    {
        $app = app();
        $app->bootstrapWith([
            \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
        ]);
    }

    private function copyAuthJson()
    {
        $sourceAuthJson = $_SERVER['HOME'].'/Code/flux-auth.json';
        $destinationAuthJson = base_path('auth.json');

        if (File::exists($sourceAuthJson)) {
            info('Found auth.json in ~/Code/ directory. Copying to application...');
            File::copy($sourceAuthJson, $destinationAuthJson);
            info('auth.json copied successfully.');
        } else {
            warning('No auth.json found in ~/Code/ directory. Skipping.');
        }
    }
}
