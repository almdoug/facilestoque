<?php
include '../database/conection.php';

class movimentacoes {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getMovimentacoes($id_comercio) {
        $query = "SELECT * FROM movimentacoes WHERE comercio_id = ?";
        return $this->executeQuery($query, [$id_comercio]);
    }

    public function getMovimentacaoById($id) {
        $query = "SELECT * FROM movimentacoes WHERE id = ?";
        return $this->executeQuery($query, [$id]);
    }

    public function insertMovimentacao($comercio_id, $produto_id, $tipo, $quantidade) {
        $query = "INSERT INTO movimentacoes(comercio_id, produto_id, tipo, quantidade) VALUES (?,?,?,?)";
        return $this->executeQuery($query, [$comercio_id, $produto_id, $tipo, $quantidade]);
    }

    public function updateMovimentacaoQuantidade($id, $quantidade) {
        $query = "UPDATE movimentacoes SET quantidade = quantidade + ? WHERE id = ?";
        return $this->executeQuery($query, [$quantidade, $id]);
    }

    private function executeQuery($query, $params = []) {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }
}
