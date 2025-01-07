<?php
session_start();
include '../class/class_produtos.php';

$ob_produto = new produtos();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produto = isset($_POST['produto']) ? $_POST['produto'] : null;
    $categoria_id = isset($_POST['categoria_id']) ? $_POST['categoria_id'] : null;

    if (isset($_POST['tem_validade'])) {
        $tem_validade = true;
        $validade = isset($_POST['validade']) ? $_POST['validade'] : null;
    } else {
        $tem_validade = false;
        $validade = null;
    }

    $caminho_imagem = null;
    $id_comercio = $_SESSION['id_comercio'];

    $pasta_imagens = "../../public/assets/image/produtos/$id_comercio";

    if (!is_dir($pasta_imagens)) {
        if (mkdir($pasta_imagens, 0755, true)) {
            echo "Pasta criada: $pasta_imagens<br>";
        } else {
            echo "Falha ao criar a pasta: $pasta_imagens<br>";
        }
    } else {
        echo "Pasta já existe: $pasta_imagens<br>";
    }

    if (isset($_FILES['imagem'])) {
        var_dump($_FILES['imagem']);

        if ($_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            $imagem = $_FILES['imagem'];

            $extensao = pathinfo($imagem['name'], PATHINFO_EXTENSION);
            $nome_imagem = preg_replace('/[^a-zA-Z0-9_-]/', '_', $produto) . ".$extensao";
            $caminho_imagem = "$pasta_imagens/$nome_imagem";

            if (move_uploaded_file($imagem['tmp_name'], $caminho_imagem)) {
                echo "Imagem movida com sucesso: $caminho_imagem<br>";
            } else {
                $caminho_imagem = '../../public/assets/image/image.png';
                echo "Falha ao mover a imagem. Usando imagem padrão: $caminho_imagem<br>";
            }
        } else {
            $caminho_imagem = '../../public/assets/image/image.png';
            echo "Erro no upload da imagem: " . $_FILES['imagem']['error'] . "<br>";
        }
    } else {
        $caminho_imagem = '../../public/assets/image/image.png';
        echo "Nenhuma imagem enviada. Usando imagem padrão: $caminho_imagem<br>";
    }

    if ($tem_validade == true) {
        $ob_produto->insertProdutoValido($produto, $categoria_id, $validade, $_SESSION['id_comercio'], $caminho_imagem);
    } else {
        $ob_produto->insertProduto($produto, $categoria_id, $_SESSION['id_comercio'], $caminho_imagem);
    }

    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}
