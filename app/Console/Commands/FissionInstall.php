<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Support\RemovesTeamSupport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Laravel\Prompts\Support\Logger;
use Symfony\Component\Process\Process;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\note;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\task;
use function Laravel\Prompts\text;
use function Laravel\Prompts\warning;

final class FissionInstall extends Command
{
    protected $signature = 'fission:install {name? : The project name}';

    protected $description = 'Run the Fission installation process';

    private bool $initializeGit = false;

    public function handle(): int
    {
        app()->detectEnvironment(fn (): string => 'local');

        intro('Installing Fission');

        $this->handleGitRepository();
        $this->handleOptionalPackages();
        $this->handleTeamSupport();

        spin(function (): void {
            $this->setupEnvFile();
            $this->reloadEnvironment();
        }, 'Configuring environment');

        $this->runMigrations();
        $this->setProjectName();

        $this->installInstruckt();
        $this->updateBoost();

        spin(fn () => $this->cleanup(), 'Cleaning up installation files');

        $this->initializeGitRepository();
        $this->generatePhpStanBaseline();

        $this->displayCompletionMessage();

        return 0;
    }

    private function displayCompletionMessage(): void
    {
        outro('Fission installation complete!');
        note(
            'Next steps:'.PHP_EOL.
            '  1. Run `composer run dev` to start the development server'.PHP_EOL.
            '     (includes Laravel, queue, logs, and Vite)'.PHP_EOL.PHP_EOL.
            '  Keep creating.'
        );
    }

    private function handleGitRepository(): void
    {
        if (File::isDirectory(base_path('.git'))) {
            if ($this->isCloneOfFissionTemplate()) {
                warning('This appears to be a clone of the Fission starter template.');

                if (confirm('Would you like to remove the existing Git history and start fresh?', true)) {
                    File::deleteDirectory(base_path('.git'));
                    $this->initializeGit = true;
                }
            }

            return;
        }

        $this->initializeGit = confirm('Would you like to initialize a fresh Git repository after installation?', true);
    }

    private function isCloneOfFissionTemplate(): bool
    {
        $process = new \Symfony\Component\Process\Process(['git', 'remote', 'get-url', 'origin']);
        $process->setWorkingDirectory(base_path());
        $process->run();

        if (! $process->isSuccessful()) {
            return false;
        }

        $remoteUrl = trim($process->getOutput());

        return str_contains($remoteUrl, 'joshcirre/fission')
            || str_contains($remoteUrl, 'github.com/joshcirre/fission');
    }

    private function postInstallFluxPro(): void
    {
        $sourceAuthJson = $_SERVER['HOME'].'/Code/flux-auth.json';

        if (File::exists($sourceAuthJson) && ! File::exists(base_path('auth.json'))) {
            File::copy($sourceAuthJson, base_path('auth.json'));
            info('Flux Pro credentials copied from ~/Code/flux-auth.json.');

            return;
        }

        if (! File::exists(base_path('auth.json'))) {
            note('Running flux:activate to configure your Flux Pro credentials...');
            $this->call('flux:activate');

            return;
        }

        info('Flux Pro installed. Credentials already configured.');
    }

    private function initializeGitRepository(): void
    {
        if (! $this->initializeGit) {
            return;
        }

        if (! $this->runTask('Initializing fresh Git repository', ['git init'])) {
            error('Could not initialize a Git repository. Run `git init` manually once the problem above is resolved.');

            return;
        }

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
        }

        if (! $this->runTask('Creating initial commit', ['git add .', 'git commit -m "Initial commit"'])) {
            error('Could not create the initial commit. Your files are untouched — commit manually when ready.');

            return;
        }

        info('Git repository initialized with initial commit.');
    }

    private function setupEnvFile(): void
    {
        if (! File::exists('.env') && File::exists('.env.example')) {
            File::copy('.env.example', '.env');
        }

        $envContent = File::get('.env');
        if (in_array(preg_match('/^APP_ENV=local/m', $envContent), [0, false], true)) {
            $this->updateEnv('APP_ENV', 'local');
        }
    }

    private function runMigrations(): void
    {
        if (! confirm('Do you want to run database migrations?', true)) {
            return;
        }

        if (! file_exists(database_path('database.sqlite'))) {
            file_put_contents(database_path('database.sqlite'), '');
        }

        $success = $this->runTask(
            'Running database migrations',
            [PHP_BINARY.' artisan migrate --force --no-interaction'],
        );

        if ($success) {
            info('Database migrated.');
        }
    }

    private function setProjectName(): void
    {
        $currentAppName = env('APP_NAME');
        $currentAppUrl = env('APP_URL');

        if ($currentAppName !== 'Laravel' && $currentAppName !== null) {
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

            $url = mb_rtrim($url, '/');

            $this->updateEnv('APP_URL', $url);
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
                'flux-pro' => 'Flux Pro – Premium UI components (requires a Flux license)',
                'bento' => 'Bento – Customer engagement & email automation',
                'filament' => 'Filament – Admin panel & UI toolkit',
                'nightwatch' => 'Nightwatch – Application monitoring & alerting',
                'laravel-ai' => 'Laravel AI – First-party AI integrations',
                'pirsch' => 'Pirsch Analytics – Privacy-friendly analytics',
            ],
            hint: 'Space to select, Enter to confirm.',
        );

        if ($selected === []) {
            return;
        }

        $packageMap = [
            'flux-pro' => 'livewire/flux-pro:"^2.0"',
            'bento' => 'bentonow/bento-laravel-sdk',
            'filament' => 'filament/filament:"^5.0"',
            'nightwatch' => 'laravel/nightwatch',
            'laravel-ai' => 'laravel/ai',
            'pirsch' => 'pirsch-analytics/laravel',
        ];

        if (in_array('flux-pro', $selected, true)) {
            $this->runTask(
                'Registering Flux Pro composer repository',
                ['composer config repositories.flux-pro composer https://composer.fluxui.dev --no-interaction'],
            );
        }

        $composerPackages = array_map(fn (string $key): string => $packageMap[$key], $selected);

        $success = $this->runTask(
            'Installing optional packages',
            ['composer require '.implode(' ', $composerPackages).' --no-interaction'],
        );

        if (! $success) {
            error('Failed to install one or more optional packages.');

            return;
        }

        info('Installed: '.implode(', ', $selected));

        foreach ($selected as $package) {
            match ($package) {
                'flux-pro' => $this->postInstallFluxPro(),
                'bento' => note('Add your BENTO_SITE_UUID and BENTO_PUBLISHABLE_KEY to .env to complete Bento setup.'),
                'filament' => $this->postInstallFilament(),
                'nightwatch' => note('Run `php artisan nightwatch:install` to complete Nightwatch setup.'),
                'laravel-ai' => null,
                'pirsch' => note('Add your PIRSCH_CLIENT_ID to .env to complete Pirsch Analytics setup.'),
            };
        }
    }

    private function handleTeamSupport(): void
    {
        if (! class_exists(\App\Support\RemovesTeamSupport::class)) {
            note('Teams support step already completed. Skipping.');

            return;
        }

        if (confirm('Would you like to add teams support to your application?', false)) {
            info('Teams support enabled.');

            return;
        }

        spin(fn () => app(\App\Support\RemovesTeamSupport::class)->handle(), 'Removing teams support');
    }

    private function postInstallFilament(): void
    {
        $this->runTask(
            'Installing Filament panel',
            [PHP_BINARY.' artisan filament:install --panels --no-interaction'],
        );
    }

    private function updateBoost(): void
    {
        $this->runTask(
            'Updating Boost guidelines & skills',
            [PHP_BINARY.' artisan boost:update --no-interaction'],
        );
    }

    private function installInstruckt(): void
    {
        $this->runTask(
            'Installing Instruckt agent integration',
            [PHP_BINARY.' artisan instruckt:install --no-interaction'],
        );
    }

    private function cleanup(): void
    {
        $currentCommand = self::class;
        $commandFile = app_path('Console/Commands/'.class_basename($currentCommand).'.php');

        foreach (File::glob(app_path('Console/Commands/*.php')) as $file) {
            if ($file !== $commandFile) {
                File::delete($file);
            }
        }

        File::delete(app_path('Support/RemovesTeamSupport.php'));
        File::deleteDirectory(resource_path('stubs/no-teams'));
        File::delete(base_path('tests/Feature/RemovesTeamSupportTest.php'));
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
        $this->runTask(
            'Generating PHPStan baseline',
            ['./vendor/bin/phpstan analyse --memory-limit=256M --generate-baseline --no-interaction'],
        );
    }

    /**
     * Run one or more shell commands inside a Laravel Prompts task spinner,
     * streaming their output into the task log area.
     *
     * @param  array<int, string>  $commands
     */
    private function runTask(string $label, array $commands): bool
    {
        $result = task(
            label: $label.'...',
            callback: function (Logger $logger) use ($commands, $label): bool {
                foreach ($commands as $command) {
                    $process = Process::fromShellCommandline($command, base_path());
                    $process->setTimeout(null);
                    $process->run(function (string $type, string $line) use ($logger): void {
                        foreach (preg_split('/\r\n|\r|\n/', mb_rtrim($line)) ?: [] as $singleLine) {
                            if ($singleLine !== '') {
                                $logger->line($singleLine);
                            }
                        }
                    });

                    if (! $process->isSuccessful()) {
                        $logger->error($label.' failed');

                        return false;
                    }
                }

                $logger->success($label);

                return true;
            },
        );

        return $result === true;
    }
}
