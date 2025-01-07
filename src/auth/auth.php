<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['id_usuario']);
}

if (!isLoggedIn()) {
    header("Location: ../pages/login.php");
    exit;
}
?>
