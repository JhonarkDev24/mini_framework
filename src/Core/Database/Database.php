<?php

namespace App\Core\Database;

use PDO;
use PDOException;

class Database {
    protected $table;
    protected $db_host;
    protected $db_name;
    protected $db_user;
    protected $db_pass;
    protected $pdo;
    protected $config;

    protected function __construct() {
        $this->config = require_once __DIR__ . "/../Config/Env.php";

        $db_host = $this->db_host ?? $this->config['DB_HOST'];
        $db_name = $this->db_name ?? $this->config['DB_NAME']; 
        $db_user = $this->db_user ?? $this->config['DB_USER']; 
        $db_pass = $this->db_pass ?? $this->config['DB_PASS']; 

        $DSN = "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->pdo = new PDO($DSN, $db_user, $db_pass, $options);
        } catch (PDOException $e) {
            die(json_encode([
                "error" => "Database Connection Failed",
                "details" => $e->getMessage(),
            ]));
        }
    }

    protected function getConnection () {
        return $this->pdo;
    }
}