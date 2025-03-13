<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\text;

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

        $this->line('');
        $this->info('Starting Fission installation...');
        $this->line('');

        // Handle Git repository based on installation method
        $this->handleGitRepository();

        // Handle Flux Pro activation (always optional)
        $this->handleFluxActivation();

        $this->setupEnvFile();
        $this->reloadEnvironment();
        $this->runMigrations();
        $this->installPan();
        $this->setProjectName();

        $this->cleanup();

        // Initialize Git repository after cleanup if requested
        $this->initializeGitRepository();

        // Create a visually distinct completion message
        $this->displayCompletionMessage();

        // The Laravel installer will continue, but we've made our message stand out
        return 0;
    }

    /**
     * Display a visually distinct completion message
     */
    private function displayCompletionMessage()
    {
        $this->line('');
        $this->line('');
        $this->line('<bg=blue;fg=white>                                                           </>');
        $this->line('<bg=blue;fg=white>  Fission installation completed successfully! ☢️           </>');
        $this->line('<bg=blue;fg=white>                                                           </>');
        $this->line('<bg=blue;fg=white>  👉 Run `php artisan solo` or `composer run dev`          </>');
        $this->line('<bg=blue;fg=white>    to start the local server.                             </>');
        $this->line('<bg=blue;fg=white>                                                           </>');
        $this->line('<bg=blue;fg=white>  Keep creating. 🫡                                        </>');
        $this->line('<bg=blue;fg=white>                                                           </>');
        $this->line('');
        $this->line('');
    }

    private function handleGitRepository()
    {
        $this->line('Checking Git repository status...');

        // Don't remove Git if it's already initialized
        if (File::isDirectory(base_path('.git'))) {
            $this->line('Git repository already initialized. Skipping.');

            return;
        }

        // Ask if user wants to initialize a new repository after cleanup
        $this->initializeGit = confirm('Would you like to initialize a fresh Git repository after installation?', true);
    }

    private function handleFluxActivation()
    {
        $this->line('Checking Flux Pro status...');

        // Check for auth.json
        $sourceAuthJson = $_SERVER['HOME'].'/Code/flux-auth.json';

        if (File::exists($sourceAuthJson)) {
            $this->line('Found auth.json in ~/Code/ directory. Copying to application...');
            File::copy($sourceAuthJson, base_path('auth.json'));
            $this->line('auth.json copied successfully.');

            // Update composer.json to add Flux Pro repository
            $this->line('Adding Flux Pro repository to composer.json...');

            $composerJson = json_decode(file_get_contents(base_path('composer.json')), true);

            // Add the repository if it doesn't exist
            if (! isset($composerJson['repositories']['flux-pro'])) {
                $composerJson['repositories']['flux-pro'] = [
                    'type' => 'composer',
                    'url' => 'https://composer.fluxui.dev',
                ];
            }

            // Add flux-pro to dependencies if it doesn't exist
            if (! isset($composerJson['require']['livewire/flux-pro'])) {
                $composerJson['require']['livewire/flux-pro'] = '^2.0';
            }

            // Save the updated composer.json
            file_put_contents(
                base_path('composer.json'),
                json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            );

            // Now run composer update to install it
            $this->line('Running composer update to install Flux Pro...');
            exec('composer update livewire/flux-pro --no-interaction');

            $this->line('Flux Pro activated successfully.');

            return;
        }

        // No auth.json found, ask if they have a Flux Pro account
        $hasFluxPro = confirm('Do you have a Flux Pro account?', false);

        if ($hasFluxPro) {
            $this->line('Running flux:activate command...');
            $this->call('flux:activate');
        } else {
            $this->comment('This starter kit uses some Flux Pro components, however, feel free to remove them if needed.');
        }
    }

    private function initializeGitRepository()
    {
        if ($this->initializeGit) {
            $this->line('Initializing fresh Git repository...');

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
                $this->line('Created .gitignore file.');
            }

            // Create initial commit with everything
            exec('git add .');
            exec('git commit -m "Initial commit"');

            $this->line('Git repository initialized with initial commit.');
        }
    }

    private function setupEnvFile()
    {
        // Only create .env if it doesn't exist (should already be handled by Laravel installer)
        if (! File::exists('.env') && File::exists('.env.example')) {
            $this->line('Creating .env file...');
            File::copy('.env.example', '.env');
        }

        // Ensure APP_ENV is set to local - do this silently
        $envContent = File::get('.env');
        if (! preg_match('/^APP_ENV=local/m', $envContent)) {
            $this->updateEnv('APP_ENV', 'local');
        }
    }

    private function runMigrations()
    {
        if (confirm('Do you want to run database migrations?', true)) {
            $this->line('Running database migrations...');

            // Ensure database.sqlite exists
            if (! file_exists(database_path('database.sqlite'))) {
                file_put_contents(database_path('database.sqlite'), '');
                $this->line('Created database.sqlite file.');
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
            $this->line('Project name already set. Skipping.');

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
        $this->line('Removing installation files...');

        // Remove the Commands folder but keep this command until it's completely done
        $currentCommand = get_class($this);
        $commandFile = app_path('Console/Commands/'.class_basename($currentCommand).'.php');

        // Remove other command files
        foreach (File::glob(app_path('Console/Commands/*.php')) as $file) {
            if ($file !== $commandFile) {
                File::delete($file);
            }
        }

        // This will be cleaned up by Laravel after the command completes
        $this->line('Installation files removed.');
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
