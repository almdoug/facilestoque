<?php
require '../auth/auth.php';
include '../class/class_produtos.php';
include '../class/class_movimentacoes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $produtos = new produtos();
        $movimentacoes = new movimentacoes();

        $produtosArray = $_POST['produtos'];
        $quantidadesArray = $_POST['quantidades'];

        var_dump($produtosArray, $quantidadesArray, $_SESSION);

        foreach ($produtosArray as $index => $produtoId) {
            $dados_produto = $produtos->getProdutoById($produtoId)->fetch(PDO::FETCH_ASSOC);
            $quantidade = $quantidadesArray[$index];
            $produtos->updateProdutoQuantidade($produtoId, $quantidade);
            $movimentacoes->insertMovimentacao($dados_produto['comercio_id'], $produtoId, 1, $quantidade);
            
            echo "Produto ID: $produtoId, Quantidade: $quantidade<br>";
        }

        unset($_SESSION['carrinho']);
    } catch (Exception $e) {
        echo "Ocorreu um erro: " . $e->getMessage();
    } finally {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
}
?>