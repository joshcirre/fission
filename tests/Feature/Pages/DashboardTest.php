<?php

declare(strict_types=1);

use function Pest\Laravel\get;

test('dashboard page requires authentication', function () {
    get('/')->assertRedirect(route('login'));
});

test('verified middleware is applied to dashboard', function () {
    // This test verifies the middleware is present in routes
    $route = app('router')->getRoutes()->getByName('dashboard');

    expect($route)->not->toBeNull();
    expect($route->middleware())->toContain('verified');
});
