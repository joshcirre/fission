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

    private $initializeGit = false;

    public function handle()
    {
        app()->detectEnvironment(function () {
            return 'local';
        });

        info('Starting Fission installation...');

        // Remove existing Git repository first
        $this->removeGitRepository();

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
        $this->installPan();
        $this->setProjectName();

        $this->cleanup();

        // Initialize Git repository after cleanup if requested
        $this->initializeGitRepository();

        info('Fission installation completed successfully! ☢️');
        info('👉 Run `php artisan solo` or `composer run dev` to start the local server.');
        info('Keep creating. 🫡');
    }

    private function removeGitRepository()
    {
        info('Checking Git repository status...');

        if (File::isDirectory(base_path('.git'))) {
            // Remove existing Git repository
            File::deleteDirectory(base_path('.git'));
            info('Removed existing Git repository.');
        }

        // Ask if user wants to initialize a new repository after cleanup
        $this->initializeGit = confirm('Would you like to initialize a fresh Git repository after installation?', true);
    }

    private function initializeGitRepository()
    {
        if ($this->initializeGit) {
            info('Initializing fresh Git repository...');

            exec('git init');

            // Create a basic .gitignore if it doesn't exist
            if (! File::exists(base_path('.gitignore'))) {
                File::put(base_path('.gitignore'), implode("\n", [
                    '/.phpunit.cache',
                    '/vendor',
                    'composer.phar',
                    'composer.lock',
                    '.DS_Store',
                    'Thumbs.db',
                    '/phpunit.xml',
                    '/.idea',
                    '/.fleet',
                    '/.vscode',
                    '.phpunit.result.cache',
                ]));
                info('Created .gitignore file.');
            }

            // Create initial commit with everything
            exec('git add .');
            exec('git commit -m "Initial commit"');

            info('Git repository initialized with initial commit.');
        }
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
            $this->updateEnv('APP_ENV', 'local');
            info('APP_ENV set to local.');
        } else {
            $envContent = preg_replace('/^APP_ENV=(.*)$/m', 'APP_ENV=local', $envContent);
            $this->updateEnv('APP_ENV', 'local');
            info('APP_ENV updated to local.');
        }
    }

    private function generateAppKey()
    {
        info('Checking application key...');
        if (empty(env('APP_KEY'))) {
            $this->call('key:generate --ansi');
        } else {
            warning('Application key already exists. Skipping.');
        }
    }

    private function runMigrations()
    {
        if (confirm('Do you want to run database migrations?', true)) {
            info('Running database migrations...');

            // Ensure database.sqlite exists
            if (! File::exists('database/database.sqlite')) {
                File::touch('database/database.sqlite');
                info('Created database.sqlite file.');
            }

            $this->call('migrate --graceful --ansi', [
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

    private function installPan()
    {
        $this->call('install:pan');
    }

    private function copyAuthJson()
    {
        $sourceAuthJson = $_SERVER['HOME'].'/Code/flux-auth.json';
        $destinationAuthJson = base_path('auth.json');

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
