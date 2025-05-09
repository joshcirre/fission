<?php

declare(strict_types=1);

it('returns a redirect response for the homepage', function () {
    $response = $this->get('/');

    $response->assertStatus(302);
});
