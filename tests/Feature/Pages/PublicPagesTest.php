<?php

declare(strict_types=1);

use function Pest\Laravel\get;

test('non-existent pages return 404', function () {
    get('/non-existent-page')->assertNotFound();
});
