<?php
// Session cookie settings
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);

// Security settings
ini_set('session.use_strict_mode', 1);

// Start session
session_start();
