<?php
/* ===============================
Define the necessary roles for the routes
=============================== */

return [
    'roles' => [
        '* /admin/users' => ['admin'],
        'POST /admin/users/update_with_code/' => ['admin'],
    ]
];
