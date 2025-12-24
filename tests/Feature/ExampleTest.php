<?php

test('the application redirects to agenda', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('agenda.index'));
});
