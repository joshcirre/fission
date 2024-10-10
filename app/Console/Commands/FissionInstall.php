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

    private $authJsonExists = false;

    private $additionalPackages = [];

    public function handle()
    {
        app()->detectEnvironment(function () {
            return 'local';
        });

        info('Starting Fission installation...');

        $this->copyAuthJson();

        // Run npm install
        if (! File::exists('node_modules')) {
            info('Running npm install...');
            exec('npm install');
        } else {
            warning('Node modules already exist. Skipping npm install.');
        }

        // Run flux:activate only if auth.json doesn't exist
        if (! $this->authJsonExists) {
            info('Activating Flux...');
            $this->call('flux:activate');
        } else {
            info('auth.json found. Skipping Flux manual activation.');
        }

        $this->setupEnvFile();
        $this->reloadEnvironment();
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
        if (! preg_match('/^\s*APP_ENV=/m', $envContent)) {
            File::append('.env', "\nAPP_ENV=local");
            info('APP_ENV set to local.');
        } else {
            $envContent = preg_replace('/^\s*APP_ENV=.*$/m', 'APP_ENV=local', $envContent);
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

        $defaultUrl = 'http://localhost:8000';
        $url = text(
            label: 'What is the URL of your project?',
            placeholder: $defaultUrl,
            default: $defaultUrl,
            required: true
        );

        $this->updateEnv('APP_URL', $url);

        // Extract port number from user input
        preg_match('/(?<=:)\d+/', $url, $matches);
        $port = $matches[0] ?? null;

        // Add APP_PORT variable to .env
        echo "Adding APP_PORT={$port} to .env";
        $this->updateEnv('APP_PORT', $port);
    }

    private function updateEnv($key, $value)
    {
        $path = base_path('.env');
    
        if (File::exists($path)) {
            $envContent = file_get_contents($path);
            $newEntry = "{$key}=\"{$value}\"";
    
            // Add the variable if it doesn't exist
            if (preg_match("/^{$key}=.*/m", $envContent)) {
                $envContent = preg_replace(
                    "/^{$key}=.*/m",
                    $newEntry,
                    $envContent
                );
            } else {
                $envContent .= "\n{$newEntry}";
            }
            file_put_contents($path, $envContent);
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
        $sourceAuthJson = $_SERVER['HOME'].'/.config/flux/auth.json';
        $destinationAuthJson = base_path('auth.json');

        info('Looking for auth.json at: ' . $sourceAuthJson);


        if (File::exists($sourceAuthJson)) {
            info('Found auth.json in ~/Code/ directory. Copying to application...');
            File::copy($sourceAuthJson, $destinationAuthJson);
            info('auth.json copied successfully.');

            // Run composer install again to ensure Flux Pro is properly installed
            info('Running composer install to activate Flux Pro...');
            exec('composer install');
            info('Flux Pro activated.');

            $this->authJsonExists = true;
        } else {
            warning('No preset auth.json found. You can add your credentials for Flux in a bit.');
            $this->authJsonExists = false;
        }
    }
}
