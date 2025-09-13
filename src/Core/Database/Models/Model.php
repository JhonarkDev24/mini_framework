<?php

namespace App\Core\Database\Models;

use App\Core\Database\Database;
use PDO;

class Model extends Database {
    protected $table = "";
    protected $query = "";
    protected $bindings = [];

    public function all () {
        $model = $this->getConnection();
        $stmt = $model->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function select (string $columns = "*") {
        $this->query = "";
        $this->bindings = [];

        $array = array_map("trim", explode(",", $columns));
        $columns = implode(", ", $array);

        $this->query = "SELECT {$columns} FROM {$this->table}";

        return $this;
    }

    public function update (array $bindings) {
        if (empty($bindings)) {
            throw new \InvalidArgumentException("Invalid Arguments");
        }

        $this->query = "";
        $this->bindings = [];
        $set = [];

        foreach ($bindings as $key => $value) {
            $placeholder = "update_$key";
            $set[] = "$key = :$placeholder";
            $this->bindings[$placeholder] = $value;
        }

        $this->query = "UPDATE {$this->table} SET " . implode(", ", $set);

        return $this;
    }

    public function insert (array $bindings) {
        if (empty($bindings)) {
            throw new \InvalidArgumentException("Invalid Arguments");
        }
        
        $this->query = "";
        $this->bindings = [];
        $count = 1;
        $cols = [];
        $vals = [];

        foreach ($bindings as $key => $value) {
            $placeholder = "insert_$count";
            $cols[] = $key;
            $vals[] = ":$placeholder";
            $this->bindings[$placeholder] = $value;
            $count++;
        }

        $cols = implode(", ", $cols);
        $vals = implode(", ", $vals);
        $this->query = "INSERT INTO {$this->table} ($cols) VALUES ($vals)";

        return $this;
    }

    public function where(array $bindings, string $operator = "AND") {
        if (empty($bindings)) {
            throw new \InvalidArgumentException("Invalid Arguments");
        }

        $cols = [];
        $count = count($this->bindings) + 1;

        foreach ($bindings as $key => $value) {
            $placeholder = "where_$count";
            $cols[] = "$key = :$placeholder";
            $this->bindings[$placeholder] = $value;
            $count++;
        }

        $prefix = stripos($this->query, "WHERE") === false ? " WHERE " : " $operator ";
        $this->query .= $prefix . "(" . implode(" AND ", $cols) . ")";

        return $this;
    }


    public function get () {
        $pdo = $this->getConnection();
        $stmt = $pdo->prepare($this->query);
        $stmt->execute($this->bindings);

        $this->query = "";
        $this->bindings = [];

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function exec () {
        $model = $this->getConnection();
        $stmt = $model->prepare($this->query);
        $stmt->execute($this->bindings);

        $this->query = "";
        $this->bindings = [];

        return $stmt->rowCount();
    }
}