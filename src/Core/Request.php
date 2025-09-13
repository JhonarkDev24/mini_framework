<?php

namespace App\Core;

class Request {
    public $method; 
    public $uri;
    public $query;
    public $body;

    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->query  = $_GET;
        $this->body   = $_POST;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function input($key, $default = null) {
        return $this->body[$key] 
            ?? $this->query[$key] 
            ?? $default;
    }

    public function session($key = null, $default = null) {
        if ($key === null) {
            return $_SESSION;
        }
        return $_SESSION[$key] ?? $default;
    }

    public function setSession($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function forgetSession($key) {
        unset($_SESSION[$key]);
    }
}
