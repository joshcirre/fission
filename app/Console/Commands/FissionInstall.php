<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\text;

final class FissionInstall extends Command
{
    protected $signature = 'fission:install {name? : The project name}';

    protected $description = 'Run the Fission installation process';

    private bool $initializeGit = false;

    public function handle(): int
    {
        app()->detectEnvironment(fn (): string => 'local');

        $this->line('');
        $this->info('Starting Fission installation...');
        $this->line('');

        // Handle Git repository based on installation method
        $this->handleGitRepository();

        // Handle Flux Pro activation (always optional)
        $this->handleFluxActivation();

        // Optionally install additional packages
        $this->handleOptionalPackages();

        $this->setupEnvFile();
        $this->reloadEnvironment();
        $this->runMigrations();
        $this->setProjectName();

        $this->cleanup();

        // Initialize Git repository after cleanup if requested
        $this->initializeGitRepository();

        // Generate PHPStan baseline for test code
        $this->generatePhpStanBaseline();

        // Create a visually distinct completion message
        $this->displayCompletionMessage();

        // The Laravel installer will continue, but we've made our message stand out
        return 0;
    }

    private function displayCompletionMessage(): void
    {
        $this->newLine(2);
        $this->info('Fission installation completed successfully!');
        $this->newLine();
        $this->comment('Next steps:');
        $this->line('  1. Run <info>composer run dev</info> to start the development server');
        $this->line('     (includes Laravel, queue, logs, and Vite)');
        $this->newLine();
        $this->line('  Keep creating.');
        $this->newLine(2);
    }

    private function handleGitRepository(): void
    {
        $this->line('Checking Git repository status...');

        if (File::isDirectory(base_path('.git'))) {
            // Check if this is a clone of the fission template
            if ($this->isCloneOfFissionTemplate()) {
                $this->warn('This appears to be a clone of the Fission starter template.');

                if (confirm('Would you like to remove the existing Git history and start fresh?', true)) {
                    File::deleteDirectory(base_path('.git'));
                    $this->info('Removed existing Git history.');
                    $this->initializeGit = true;
                } else {
                    $this->line('Keeping existing Git repository.');
                }
            } else {
                $this->line('Git repository already initialized. Skipping.');
            }

            return;
        }

        // Ask if user wants to initialize a new repository after cleanup
        $this->initializeGit = confirm('Would you like to initialize a fresh Git repository after installation?', true);
    }

    private function isCloneOfFissionTemplate(): bool
    {
        $output = [];
        exec('git remote get-url origin 2>/dev/null', $output, $returnCode);

        if ($returnCode !== 0 || $output === []) {
            return false;
        }

        $remoteUrl = $output[0];

        // Check for various forms of the fission repo URL
        return str_contains($remoteUrl, 'joshcirre/fission')
            || str_contains($remoteUrl, 'github.com/joshcirre/fission');
    }

    private function handleFluxActivation(): void
    {
        $this->line('Checking Flux Pro credentials...');

        // Check if auth.json already exists
        if (File::exists(base_path('auth.json'))) {
            $this->info('Flux Pro credentials already configured.');

            return;
        }

        // Check for auth.json in home directory
        $sourceAuthJson = $_SERVER['HOME'].'/Code/flux-auth.json';

        if (File::exists($sourceAuthJson)) {
            $this->line('Found flux-auth.json in ~/Code/ directory. Copying to application...');
            File::copy($sourceAuthJson, base_path('auth.json'));
            $this->info('Flux Pro credentials copied successfully.');
        } else {
            // No auth.json found, ask if they have a Flux Pro account
            $hasFluxPro = confirm('Do you have a Flux Pro account?', true);

            if ($hasFluxPro) {
                $this->line('Running flux:activate command...');
                $this->call('flux:activate');
            } else {
                $this->warn('This starter kit requires Flux Pro for the UI components.');
                $this->comment('You can activate it later by running: php artisan flux:activate');
                $this->comment('Or manually add your credentials to auth.json');
            }
        }
    }

    private function initializeGitRepository(): void
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

    private function setupEnvFile(): void
    {
        // Only create .env if it doesn't exist (should already be handled by Laravel installer)
        if (! File::exists('.env') && File::exists('.env.example')) {
            $this->line('Creating .env file...');
            File::copy('.env.example', '.env');
        }

        // Ensure APP_ENV is set to local - do this silently
        $envContent = File::get('.env');
        if (in_array(preg_match('/^APP_ENV=local/m', $envContent), [0, false], true)) {
            $this->updateEnv('APP_ENV', 'local');
        }
    }

    private function runMigrations(): void
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

    private function setProjectName(): void
    {
        // Only set project name if it's still the default "Laravel"
        $currentAppName = env('APP_NAME');
        $currentAppUrl = env('APP_URL');

        if ($currentAppName !== 'Laravel' && $currentAppName !== null) {
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

        // Only ask for URL if it's not already set or is still default
        if (in_array($currentAppUrl, [null, 'http://localhost', 'http://localhost:8000'], true)) {
            $defaultUrl = 'http://localhost:8000';
            $url = text(
                label: 'What is the URL of your project?',
                placeholder: $defaultUrl,
                default: $defaultUrl,
                required: true,
                validate: fn (string $value): ?string => filter_var($value, FILTER_VALIDATE_URL)
                    ? null
                    : 'Please enter a valid URL'
            );

            // Remove trailing slash to prevent issues
            $url = mb_rtrim($url, '/');

            $this->updateEnv('APP_URL', $url);
        } else {
            $this->line('APP_URL already configured: '.$currentAppUrl);
        }
    }

    private function updateEnv(string $key, string $value): void
    {
        $path = base_path('.env');

        if (File::exists($path)) {
            file_put_contents($path, preg_replace(
                sprintf('/^%s=.*/m', $key),
                sprintf('%s="%s"', $key, $value),
                file_get_contents($path)
            ));
        }
    }

    private function handleOptionalPackages(): void
    {
        if (! confirm('Would you like to install optional packages?', false)) {
            return;
        }

        /** @var array<int, string> $selected */
        $selected = multiselect(
            label: 'Which packages would you like to install?',
            options: [
                'bento' => 'Bento – Customer engagement & email automation',
                'filament' => 'Filament – Admin panel & UI toolkit',
                'nightwatch' => 'Nightwatch – End-to-end browser testing',
                'laravel-ai' => 'Laravel AI – First-party AI integrations',
                'pirsch' => 'Pirsch Analytics – Privacy-friendly analytics',
            ],
            hint: 'Space to select, Enter to confirm.',
        );

        foreach ($selected as $package) {
            match ($package) {
                'bento' => $this->installBento(),
                'filament' => $this->installFilament(),
                'nightwatch' => $this->installNightwatch(),
                'laravel-ai' => $this->installLaravelAi(),
                'pirsch' => $this->installPirsch(),
            };
        }
    }

    private function installBento(): void
    {
        $this->line('Installing Bento...');
        passthru('composer require bentoproject/bento --no-interaction');
        $this->info('Bento installed. Add your BENTO_SITE_UUID and BENTO_PUBLISHABLE_KEY to .env to complete setup.');
    }

    private function installFilament(): void
    {
        $this->line('Installing Filament...');
        passthru('composer require filament/filament --no-interaction');
        $this->call('filament:install', ['--panels' => true]);
        $this->info('Filament installed.');
    }

    private function installNightwatch(): void
    {
        $this->line('Installing Nightwatch...');
        passthru('composer require laravel/nightwatch --no-interaction');
        $this->info('Nightwatch installed. Run php artisan nightwatch:install to complete setup.');
    }

    private function installLaravelAi(): void
    {
        $this->line('Installing Laravel AI...');
        passthru('composer require laravel/ai --no-interaction');
        $this->info('Laravel AI installed.');
    }

    private function installPirsch(): void
    {
        $this->line('Installing Pirsch Analytics...');
        passthru('composer require pirsch-analytics/laravel --no-interaction');
        $this->info('Pirsch Analytics installed. Add your PIRSCH_CLIENT_ID to .env to complete setup.');
    }

    private function cleanup(): void
    {
        $this->line('Removing installation files...');

        // Remove the Commands folder but keep this command until it's completely done
        $currentCommand = self::class;
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

    private function reloadEnvironment(): void
    {
        $app = app();
        $app->bootstrapWith([
            \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
        ]);
    }

    private function generatePhpStanBaseline(): void
    {
        $this->line('Generating PHPStan baseline for test code...');

        exec('./vendor/bin/phpstan analyse --memory-limit=256M --generate-baseline 2>&1', $output, $returnCode);

        if ($returnCode === 0) {
            $this->info('PHPStan baseline generated successfully.');
        } else {
            $this->comment('PHPStan baseline generation skipped.');
        }
    }
}
