<?php
/* ===============================
Define the necessary roles for the routes
=============================== */

return [
    'roles' => [
        '/admin' => ['admin'],
        '/admin/dashboard' => ['admin'],
        '/admin/login' => [],
        '/admin/register' => [],
        '/admin/error' => [],
        '/admin/unknown_password' => [],
        '/admin/profile' => ['admin'],
        '/admin/users' => ['admin'],
        '/admin/users/all_users' => ['admin'],
    ]
];
