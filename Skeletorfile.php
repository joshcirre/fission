<?php

use NiftyCo\Skeletor\Skeletor;

function updateEnv(string $key, string $value, string $contents): string
{
    return preg_replace(
        "/^{$key}=.*/m",
        "{$key}=\"{$value}\"",
        $contents
    );
}

return function (Skeletor $skeletor) {
    $skeletor->intro('Starting Fission setup...');

    $defaultName = basename(getcwd());

    $appName = $skeletor->text(
        label: 'What is the name of your project?',
        placeholder: $defaultName,
        default: $defaultName,
        required: true
    );

    $defaultUrl = 'http://localhost:8000';
    $appUrl = $skeletor->text(
        label: 'What is the URL of your project?',
        placeholder: $defaultUrl,
        default: $defaultUrl,
        required: true
    );


    if (!$skeletor->exists('.env')) {
        $env = $skeletor->readFile('.env.example');
        $env = updateEnv('APP_NAME', $appName, $env);
        $env = updateEnv('APP_URL', $appUrl, $env);
        $skeletor->writeFile('.env', $env);
        $skeletor->info('✓ .env file created.');
    }

    $skeletor->spin('Generating application key...', function () use ($skeletor) {
        $skeletor->exec(['php', 'artisan', 'key:generate', '--ansi']);
    });
    $skeletor->info('✓ Application key generated.');

    if (!$skeletor->exists('node_modules')) {
        $skeletor->spin('Running npm install...', function () use ($skeletor) {
            $skeletor->exec(['npm', 'install']);
        });
        $skeletor->info('✓ npm dependencies installed.');
    }

    if ($skeletor->exists($authPath = $_SERVER['HOME'] . '/Code/flux-auth.json')) {
        $skeletor->spin('Copying auth.json from ~/Code', function () use ($skeletor, $authPath) {
            $authJson = $skeletor->readFile($authPath);
            $skeletor->writeFile('auth.json', $authJson);
        });
        $skeletor->info('✓ auth.json copied.');

        $skeletor->spin('Activating Flux Pro', function () use ($skeletor) {
            $skeletor->exec(['composer', 'install']);
        });
        $skeletor->info('✓ Flux Pro activated.');
    } else {
        $email = $skeletor->text('Flux Pro License Email');
        $key = $skeletor->password('Flux Pro License Key');

        $skeletor->spin('Activating Flux Pro...', function () use ($skeletor, $email, $key) {
            $skeletor->exec(['php', 'artisan', 'flux:activate', $email, $key]);
        });
        $skeletor->info('✓ Flux Pro activated.');
    }

    $skeletor->spin('Installing pan...', function () use ($skeletor) {
        $skeletor->exec(['php', 'artisan', 'pan:install', '--ansi']);
    });
    $skeletor->info('✓ pan installed.');

    if ($skeletor->confirm('Would you like to run the database migrations?', true)) {
        $skeletor->spin('Running database migrations...', function () use ($skeletor) {
            $skeletor->writeFile('database/database.sqlite', '');
            $skeletor->exec(['php', 'artisan', 'migrate', '--ansi']);
        });
        $skeletor->info('✓ Database migrations complete.');
    }

    $skeletor->outro('Fission installation completed successfully! ☢️');
    $skeletor->info('👉 Run `php artisan solo` or `composer run dev` to start the local server.');
    $skeletor->info('Keep creating. 🫡');
};
