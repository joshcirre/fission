<?php

it('returns a redirect response for the homepage', function () {
    $response = $this->get('/');

    $response->assertStatus(302);
});
