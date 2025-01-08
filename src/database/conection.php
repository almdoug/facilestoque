<?php
    if (!defined('DB_HOST')) define('DB_HOST', $_ENV['DB_HOST']);
    if (!defined('DB_NAME')) define('DB_NAME', $_ENV['DB_NAME']);
    if (!defined('DB_USER')) define('DB_USER', $_ENV['DB_USER']);
    if (!defined('DB_PASSWORD')) define('DB_PASSWORD', $_ENV['DB_PASSWORD']);
    if (!defined('DB_PORT')) define('DB_PORT', $_ENV['DB_PORT']);

    try {
        $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4;port=".DB_PORT, DB_USER, DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch(PDOException $e) {
        echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
        die();
    }
