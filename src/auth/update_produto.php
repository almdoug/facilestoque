<?php
session_start();

include '../class/class_produtos.php';

$ob_produto = new produtos();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? $_POST['id'] : null;

    $produto_atual = $ob_produto->getProdutoById($id)->fetch(PDO::FETCH_ASSOC);

    $produto = isset($_POST['produto']) ? $_POST['produto'] : null;
    $quantidade = isset($_POST['quantidade']) ? $_POST['quantidade'] : null;
    $validade = isset($_POST['validade']) ? $_POST['validade'] : null;
    $caminho_imagem = null;

    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $imagem = $_FILES['imagem'];
        $extensao = pathinfo($imagem['name'], PATHINFO_EXTENSION);
        $nome_imagem = preg_replace('/[^a-zA-Z0-9_-]/', '_', $produto) . ".$extensao";
        $id_comercio = $_SESSION['id_comercio'];
        $pasta_imagens = "../../public/assets/image/produtos/$id_comercio";
        $caminho_imagem = "$pasta_imagens/$nome_imagem";

        if (move_uploaded_file($imagem['tmp_name'], $caminho_imagem)) {
            echo "Imagem movida com sucesso: $caminho_imagem<br>";
        } else {
            echo "Falha ao mover a imagem. Usando imagem padrão.<br>";
            $caminho_imagem = '../../public/assets/image/image.png';
        }
    } else {
        $caminho_imagem = '../../public/assets/image/image.png';
    }

    /* // Atualiza o produto no banco de dados
    if ($validade) {
        $ob_produto->updateProdutoValido($id, $produto, $quantidade, $validade, $_SESSION['id_comercio'], $caminho_imagem);
    } else {
        $ob_produto->updateProduto($id, $produto, $quantidade, $_SESSION['id_comercio'], $caminho_imagem);
    } */

    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}
?>
