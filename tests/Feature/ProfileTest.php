<?php

declare(strict_types=1);

use function Pest\Laravel\get;

test('profile page requires authentication', function () {
    get('/profile')->assertRedirect('/auth/login');
});
