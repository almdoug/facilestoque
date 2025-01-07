<?php
require '../auth/auth.php';

if (isset($_POST['id_produto'], $_POST['quantidade'])) {
    $id_produto = $_POST['id_produto'];
    $nova_quantidade = (int)$_POST['quantidade'];

    foreach ($_SESSION['carrinho'] as &$produto) {
        if ($produto['id'] == $id_produto) {
            $produto['quantidade'] = $nova_quantidade;
            break;
        }
    }
    echo 'Quantidade atualizada com sucesso';
} else {
    echo 'Dados inválidos';
}
?>
