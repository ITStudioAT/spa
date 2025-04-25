<?php
/* ===============================
Define the necessary roles for the routes
=============================== */

return [
    'roles' => [
        '/admin' => ['super_admin', 'admin'],
        '/admin/dashboard' => ['super_admin', 'admin'],
        '/admin/test_route/:id' => ['super_admin', 'admin'],
        '/admin/login' => [],
        '/admin/register' => [],
        '/admin/error' => [],
        '/admin/unknown_password' => [],
    ]
];
