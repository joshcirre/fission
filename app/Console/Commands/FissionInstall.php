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

    private $initializeGit = false;

    public function handle()
    {
        app()->detectEnvironment(function () {
            return 'local';
        });

        info('Starting Fission installation...');

        // Handle Git repository
        $this->handleGitRepository();

        // Handle Flux Pro activation (always optional)
        $this->handleFluxActivation();

        // Run npm install if not already done
        $this->handleNpmInstall();

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

    private function handleGitRepository()
    {
        info('Checking Git repository status...');

        // Don't remove Git if it's already initialized (could be from Laravel installer)
        if (File::isDirectory(base_path('.git'))) {
            info('Git repository already initialized. Skipping.');
            return;
        }

        // Ask if user wants to initialize a new repository after cleanup
        $this->initializeGit = confirm('Would you like to initialize a fresh Git repository after installation?', true);
    }

    private function handleFluxActivation()
    {
        // Always ask if user wants to install Flux Pro
        $installFluxPro = confirm('Would you like to install Flux Pro?', false);

        if ($installFluxPro) {
            // Check for auth.json
            $sourceAuthJson = $_SERVER['HOME'] . '/Code/flux-auth.json';

            if (File::exists($sourceAuthJson)) {
                info('Found auth.json in ~/Code/ directory. Copying to application...');
                File::copy($sourceAuthJson, base_path('auth.json'));
                info('auth.json copied successfully.');

                info('Running composer install to activate Flux Pro...');
                exec('composer install');
                info('Flux Pro activated.');
            } else {
                // No auth.json found, use the flux:activate command
                info('No preset auth.json found. Running flux:activate command...');
                $this->call('flux:activate');
            }
        } else {
            info('Skipping Flux Pro installation.');
        }
    }

    private function handleNpmInstall()
    {
        // Skip if node_modules exists
        if (File::exists('node_modules')) {
            warning('Node modules already exist. Skipping npm install.');
            return;
        }

        info('Running npm install...');
        exec('npm install');
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
            $this->call('key:generate', ['--ansi' => true]);
        } else {
            warning('Application key already exists. Skipping.');
        }
    }

    private function runMigrations()
    {
        // Check for database tables before asking
        $migrationTableExists = false;
        try {
            $migrationTableExists = \Schema::hasTable('migrations');
        } catch (\Exception $e) {
            // Database connection issue, continue anyway
        }

        if ($migrationTableExists) {
            info('Migrations have already been run. Skipping.');
            return;
        }

        $shouldRunMigrations = confirm('Do you want to run database migrations?', true);

        if ($shouldRunMigrations) {
            info('Running database migrations...');

            // Ensure database.sqlite exists
            if (! file_exists('database/database.sqlite')) {
                file_put_contents('database/database.sqlite', '');
                info('Created database.sqlite file.');
            }

            $this->call('migrate', [
                '--force' => true, // This will bypass the production check
                '--ansi' => true,
            ]);
        }
    }

    private function setProjectName()
    {
        // Only set project name if it's still the default "Laravel"
        if (env('APP_NAME') !== 'Laravel') {
            info('Project name already set. Skipping.');
            return;
        }

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
            File::deleteDirectory(app_path('Console/Commands'));

            // Keep other Console files/directories intact
            if (count(File::files(app_path('Console'))) === 0 &&
                count(File::directories(app_path('Console'))) === 0) {
                File::deleteDirectory(app_path('Console'));
            }

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
}
