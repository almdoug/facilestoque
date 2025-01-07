<?php
include '../database/conection.php';

class usuario {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getUsuario($email) {
        $query = "SELECT * FROM usuarios WHERE email = ?";
        return $this->executeQuery($query, [$email]);
    }

    private function executeQuery($query, $params = []) {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }
}
