<?php

namespace EntryTrackingApi\Core;

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

        if (empty($this->body)) {
            $rawInput = file_get_contents("php://input");
            $json = json_decode($rawInput, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->body = $json;
            }
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function input($key, $default = null) {
        return $this->body[$key] 
            ?? $this->query[$key] 
            ?? $default;
    }

    public function allInputs() {
        return array_merge($this->query, $this->body);
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
