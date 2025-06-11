<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->beforeEach(function () {
        // Mock Flux components if Flux is not installed
        if (! class_exists('Flux\Flux')) {
            mockFluxComponents();
        }
    })
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', fn () => $this->toBe(1));

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * Mock Flux UI components for testing when Flux is not installed.
 * This prevents "Unable to locate a class or view for component [flux::toast]" errors.
 */
function mockFluxComponents(): void
{
    // Register a mock toast component
    Illuminate\Support\Facades\Blade::component('flux::toast', function () {
        return '<div class="mock-flux-toast"></div>';
    });

    // Mock other Flux components as needed
    $fluxComponents = [
        'flux::button', 'flux::card', 'flux::input', 'flux::field',
        'flux::label', 'flux::error', 'flux::checkbox', 'flux::link',
        'flux::heading', 'flux::subheading', 'flux::header', 'flux::main',
        'flux::dropdown', 'flux::navbar', 'flux::sidebar', 'flux::navlist',
        'flux::navlist.item', 'flux::navlist.group', 'flux::icon',
        'flux::separator', 'flux::badge', 'flux::dropdown.menu',
        'flux::dropdown.header', 'flux::dropdown.item',
    ];

    foreach ($fluxComponents as $component) {
        Illuminate\Support\Facades\Blade::component($component, function ($props = []) use ($component) {
            $tag = str_contains($component, 'heading') ? 'h2' : 'div';

            return "<{$tag} class=\"mock-".str_replace('::', '-', $component)."\">{{ \$slot ?? '' }}</{$tag}>";
        });
    }
}
