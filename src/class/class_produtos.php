<?php
include '../database/conection.php';

class produtos {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getProdutos($id_comercio) {
        $query = "SELECT * FROM produtos WHERE comercio_id = ?";
        return $this->executeQuery($query, [$id_comercio]);
    }

    public function getProdutoById($id) {
        $query = "SELECT * FROM produtos WHERE id = ?";
        return $this->executeQuery($query, [$id]);
    }
    
    public function getCategorias($id_comercio) {
        $query = "SELECT id_categoria,nome FROM categorias WHERE id_comercio = ?";
        return $this->executeQuery($query, [$id_comercio]);
    }

    public function insertCategoria($nome, $id_comercio) {
        $query = "INSERT INTO categorias(nome, id_comercio) VALUES (?,?)";
        return $this->executeQuery($query, [$nome, $id_comercio]);
    }

    public function insertProdutoValido($produto, $categoria_id, $validade, $id_comercio, $caminho_imagem) {
        $query = "INSERT INTO produtos(nome, comercio_id, validade, id_categoria, imagem) VALUES (?,?,?,?,?)";
        return $this->executeQuery($query, [$produto, $id_comercio, $validade, $categoria_id, $caminho_imagem]);
    }

    public function insertProduto($produto, $categoria_id, $id_comercio, $caminho_imagem) {
        $query = "INSERT INTO produtos(nome, comercio_id, id_categoria, imagem) VALUES (?,?,?,?)";
        return $this->executeQuery($query, [$produto, $id_comercio, $categoria_id, $caminho_imagem]);
    }

    public function updateProdutoQuantidade($id, $quantidade) {
        $query = "UPDATE produtos SET quantidade = quantidade - ? WHERE id = ?";
        return $this->executeQuery($query, [$quantidade, $id]);
    }

    public function updateProduto($id, $nome, $quantidade, $preco, $validade, $imagem) {
        $query = "UPDATE produtos SET nome = ?, quantidade = ?, preco = ?, validade = ?, imagem = ? WHERE id = ?";
        return $this->executeQuery($query, [$nome, $quantidade, $preco, $validade, $imagem, $id]);
    }

    public function getCaminhoImagem($id) {
        $query = "SELECT imagem FROM produtos WHERE id = ?";
        $stmt = $this->executeQuery($query, [$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['imagem'] : null;
    }

    public function getProdutosBaixoEstoque($id_comercio) {
        $query = "SELECT * FROM produtos WHERE comercio_id = ? AND quantidade < ?";
        $limiteQuantidade = 5;
        return $this->executeQuery($query, [$id_comercio, $limiteQuantidade]);
    }

    public function getProdutosProximoVencimento($id_comercio) {
        $query = "SELECT * FROM produtos WHERE comercio_id = ? AND validade < DATE_ADD(CURDATE(), INTERVAL 30 DAY)";
        return $this->executeQuery($query, [$id_comercio]);
    }

    private function executeQuery($query, $params = []) {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }
}
