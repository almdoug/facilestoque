<?php
require '../auth/auth.php';
include '../class/class_produtos.php';

$ob_produto = new produtos();
$categorias = $ob_produto->getCategorias($_SESSION['id_comercio'])->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../../public/assets/css/style.css">
</head>

<body>
    <div class="corpo">
        <?php include 'includes/nav.php'; ?>
        <div class="container">
            <h1>Relatórios do Sistema de Estoque</h1>
            <table>
                <thead>
                    <tr>
                        <th>Tipo de Relatório</th>
                        <th>Descrição</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Inventário Atual</td>
                        <td>Lista todos os produtos em estoque com suas quantidades atuais</td>
                        <td><a href="gerar_pdf.php?tipo=inventario" class="btn-primary">Baixar PDF</a></td>
                    </tr>
                    <tr>
                        <td>Produtos Próximos ao Vencimento</td>
                        <td>Lista produtos que estão próximos à data de validade</td>
                        <td><a href="gerar_pdf.php?tipo=proximo_vencimento" class="btn-primary">Baixar PDF</a></td>
                    </tr>
                    <tr>
                        <td>Movimentação de Estoque</td>
                        <td>Relatório de entradas e saídas de produtos no período selecionado</td>
                        <td><a href="gerar_pdf.php?tipo=movimentacao" class="btn-primary">Baixar PDF</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>