<?php

declare(strict_types=1);

namespace App\Providers;

use AaronFrancis\Solo\Facades\Solo;
use AaronFrancis\Solo\Providers\SoloApplicationServiceProvider;

final class SoloServiceProvider extends SoloApplicationServiceProvider
{
    public function register(): void
    {
        Solo::useTheme('dark')
            // Commands that auto start.
            ->addCommands([
                'Logs' => 'php artisan pail',
                'Vite' => 'npm run dev',
                'HTTP' => 'php artisan serve',
                'About' => 'php artisan solo:about',
            ])
            // Not auto-started
            ->addLazyCommands([
                'Queue' => 'php artisan queue:listen --tries=1',
                // 'Reverb' => 'php artisan reverb:start',
                'Pan' => 'php artisan pan',
                'Pint' => './vendor/bin/pint --ansi',
            ])
            // FQCNs of trusted classes that can add commands.
            ->allowCommandsAddedFrom([
                //
            ]);
    }

    public function boot(): void
    {
        //
    }
}
