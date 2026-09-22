<?php

test('halaman utama mengarahkan pengguna belum login ke halaman login', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('login'));
});

test('halaman login dapat diakses dengan sukses', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});
