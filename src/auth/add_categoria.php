<?php
session_start();
include '../class/class_produtos.php';

$ob_produto = new produtos();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['categoria'];
    $id_comercio = $_SESSION['id_comercio'];

    $ob_produto->insertCategoria($nome, $id_comercio);

    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}
