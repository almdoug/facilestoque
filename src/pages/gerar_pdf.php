<?php
require_once '../../vendor/autoload.php';
require_once '../class/class_produtos.php';
require_once '../class/class_movimentacoes.php';

use Dompdf\Dompdf;

require '../auth/auth.php';

$tipo = $_GET['tipo'] ?? '';

$dompdf = new Dompdf();

$produtos = new produtos();
$movimentacoes = new movimentacoes();

switch ($tipo) {
    case 'inventario':
        $produtosList = $produtos->getProdutos($_SESSION['id_comercio']);
        $html = '<h1>Inventário Atual</h1>';
        $html .= '<table border="1" cellpadding="5" cellspacing="0" style="width: 100%; border-collapse: collapse;">';
        $html .= '<tr><th>ID</th><th>Nome</th><th>Quantidade</th><th>Validade</th><th>Valor Total</th></tr>';
        $valorTotalGeral = 0;
        foreach ($produtosList as $produto) {
            $validade = $produto['validade'] ? $produto['validade'] : 'Validade não determinada';
            $valorTotalProduto = $produto['quantidade'] * $produto['preco'];
            $html .= '<tr><td>' . $produto['id'] . '</td><td>' . $produto['nome'] . '</td><td>' . $produto['quantidade'] . '</td><td>' . $validade . '</td><td>R$ ' . number_format($valorTotalProduto, 2, ',', '.') . '</td></tr>'; // Exibe o valor total do produto formatado
            $valorTotalGeral += $valorTotalProduto;
        }
        $html .= '<tr><td colspan="4" style="text-align: right;"><strong>Valor Total Geral:</strong></td><td>R$ ' . number_format($valorTotalGeral, 2, ',', '.') . '</td></tr>'; // Adiciona a linha do valor total geral formatado
        $html .= '</table>';
        break;
    case 'proximo_vencimento':
        $produtosProximoVencimento = $produtos->getProdutosProximoVencimento($_SESSION['id_comercio']);
        $html = '<h1>Produtos Próximos do Vencimento</h1>';
        $html .= '<table border="1" cellpadding="5" cellspacing="0" style="width: 100%; border-collapse: collapse;">';
        $html .= '<tr><th>ID</th><th>Nome</th><th>Quantidade</th><th>Validade</th><th>Valor</th><th>Status</th></tr>';
        foreach ($produtosProximoVencimento as $produto) {
            $validade = $produto['validade'] ? $produto['validade'] : 'Validade não determinada';
            $status = (strtotime($validade) < time()) ? 'Vencido' : 'Válido';
            $html .= '<tr><td>' . $produto['id'] . '</td><td>' . $produto['nome'] . '</td><td>' . $produto['quantidade'] . '</td><td>' . $validade . '</td><td>' . $produto['preco'] . '</td><td>' . $status . '</td></tr>';
        }
        $html .= '</table>';
        break;
    case 'movimentacao':
        $movimentacoesList = $movimentacoes->getMovimentacoes($_SESSION['id_comercio']);
        $html = '<h1>Movimentação de Estoque</h1>';
        $html .= '<table border="1" cellpadding="5" cellspacing="0" style="width: 100%; border-collapse: collapse;">';
        $html .= '<tr><th>Produto</th><th>Produto ID</th><th>Tipo</th><th>Quantidade</th><th>Data</th></tr>';
        $valorTotalVenda = 0;
        $valorTotalEntrada = 0;
        $valorTotalRetirada = 0;
        foreach ($movimentacoesList as $movimentacao) {
            $produtoInfo = $produtos->getProdutoById($movimentacao['produto_id']);
            $produtoData = $produtoInfo->fetch(PDO::FETCH_ASSOC);
            $nomeProduto = $produtoData ? $produtoData['nome'] : 'Produto não encontrado';
            $tipoMovimentacao = '';
            switch ($movimentacao['tipo']) {
                case 1:
                    $tipoMovimentacao = 'Venda';
                    $valorTotalVenda += $movimentacao['quantidade'] * $produtoData['preco'];
                    break;
                case 2:
                    $tipoMovimentacao = 'Entrada';
                    $valorTotalEntrada += $movimentacao['quantidade'] * $produtoData['preco'];
                    break;
                case 3:
                    $tipoMovimentacao = 'Retirada';
                    $valorTotalRetirada += $movimentacao['quantidade'] * $produtoData['preco'];
                    break;
                default:
                    $tipoMovimentacao = 'Tipo desconhecido';
            }
            $dataMovimentacao = $movimentacao['data_movimentacao'] ?? 'Data não disponível';
            $html .= '<tr><td>' . $nomeProduto . '</td><td>' . $movimentacao['produto_id'] . '</td><td>' . $tipoMovimentacao . '</td><td>' . $movimentacao['quantidade'] . '</td><td>' . $dataMovimentacao . '</td></tr>';
        }
        $html .= '<tr><td colspan="3" style="text-align: right;"><strong>Total de Vendas:</strong></td><td>R$ ' . number_format($valorTotalVenda, 2, ',', '.') . '</td><td></td></tr>';
        $html .= '<tr><td colspan="3" style="text-align: right;"><strong>Total de Entradas:</strong></td><td>R$ ' . number_format($valorTotalEntrada, 2, ',', '.') . '</td><td></td></tr>';
        $html .= '<tr><td colspan="3" style="text-align: right;"><strong>Total de Retiradas:</strong></td><td>R$ ' . number_format($valorTotalRetirada, 2, ',', '.') . '</td><td></td></tr>';
        $html .= '</table>';
        break;
    default:
        echo 'Tipo de relatório inválido.';
        exit;
}

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("relatorio_$tipo.pdf");
