<?php
include '../class/class_usuario.php';

$ob_usuario = new usuario();

function verificar_login($email, $senha) {
    $aux = false;
    global $ob_usuario;

    $usuario = $ob_usuario->getUsuario($email)->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        if (password_verify($senha, $usuario['senha'])) {
            $aux = $usuario;
        }
    }

    return $aux;
}