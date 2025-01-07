<?php
include '../database/conection.php';

class Marca {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getMarca() {
        $query = "SELECT * FROM marca";
        return $this->executeQuery($query);
    }

    public function insertMarca($nome) {
        $query = "INSERT INTO marca (nome) VALUES (?)";
        return $this->executeQuery($query, [$nome]);
    }

    private function executeQuery($query, $params = []) {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }
}
