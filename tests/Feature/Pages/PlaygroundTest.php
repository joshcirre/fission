<?php

declare(strict_types=1);

use function Pest\Laravel\get;

test('playground page requires authentication', function () {
    get('/playground')->assertRedirect(route('login'));
});
