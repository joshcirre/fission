<?php

declare(strict_types=1);

use function Pest\Laravel\get;

test('dashboard page requires authentication', function () {
    get('/')->assertRedirect(route('login'));
});
