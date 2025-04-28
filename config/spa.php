<?php

// config for Itstudioat/Spa
return [
    'version' => '0.0.7',
    'logo' => 'logo.png',
    'copyright' => '(c) 2025 ITStudio.at by Günther Kron',
    'title' => 'Spa',
    'company' => 'ItStudio.at',
    'web_throttle' => 20, // web-requests per user per minute
    'api_throttle' => 60, // api-requests per user per minute
    'global_throttle' => 100, // all-requests per minute
    'token_expire_time' => 120, // minutes when token expires
    'register_admin_allowed' => true,
    'registered_admin_must_be_confirmed' => true,
    'super_admin' => 'super_admin',
];
