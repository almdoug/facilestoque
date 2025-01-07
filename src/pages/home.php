<?php
require '../auth/auth.php';
include '../class/class_produtos.php';

$ob_produto = new produtos();
$categorias = $ob_produto->getCategorias($_SESSION['id_comercio'])->fetchAll(PDO::FETCH_ASSOC);
$produtos = $ob_produto->getProdutos($_SESSION['id_comercio'])->fetchAll(PDO::FETCH_ASSOC);

if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

if (isset($_POST['acao']) && $_POST['acao'] == 'adicionar') {
    $id_produto = $_POST['id_produto'];
    $quantidade = $_POST['quantidade'] ?? 1;

    foreach ($produtos as $produto_info) {
        if ($produto_info['id'] == $id_produto) {
            if ($quantidade > $produto_info['quantidade']) {
                header('Location: home.php?erro=quantidade_excedida');
                exit;
            }
            break;
        }
    }

    $produto_existente = false;
    foreach ($_SESSION['carrinho'] as &$produto) {
        if ($produto['id'] == $id_produto) {
            $produto['quantidade'] += $quantidade;
            $produto_existente = true;
            break;
        }
    }

    if (!$produto_existente) {
        $_SESSION['carrinho'][] = [
            'id' => $id_produto,
            'quantidade' => $quantidade
        ];
    }
    header('Location: home.php');
    exit;
}

if (isset($_POST['acao']) && $_POST['acao'] == 'remover') {
    $id_produto = $_POST['id_produto'];
    foreach ($_SESSION['carrinho'] as $key => $produto) {
        if ($produto['id'] == $id_produto) {
            unset($_SESSION['carrinho'][$key]);
            break;
        }
    }
    header('Location: home.php');
    exit;
}

if (isset($_POST['acao']) && $_POST['acao'] == 'limpar') {
    $_SESSION['carrinho'] = [];
    header('Location: home.php');
    exit;
}

function getQuantidadeNoCarrinho($id_produto)
{
    $quantidade = 0;
    if (!empty($_SESSION['carrinho'])) {
        foreach ($_SESSION['carrinho'] as $produto) {
            if ($produto['id'] == $id_produto) {
                $quantidade = $produto['quantidade'];
                break;
            }
        }
    }
    return $quantidade;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Estoque</title>
    <link rel="stylesheet" href="../../public/assets/css/style.css">
</head>

<body class="pos-body">
    <div class="navbar-pos">
        <a href="dashboard.php" class="btn-one">
            <svg aria-hidden="true" class="icon-resize" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M4 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"></path><path d="M14 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"></path><path d="M4 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"></path><path d="M14 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"></path></svg>
            Dashboard
        </a>
    </div>

    <div class="main-container">
        <div class="carrinho">
            <h2>Carrinho de Produtos</h2>
            <ul>
                <?php
                $totalCarrinho = 0;
                if (empty($_SESSION['carrinho'])): ?>
                    <li>O carrinho está vazio.</li>
                <?php else: ?>
                    <?php foreach ($_SESSION['carrinho'] as $produto): ?>
                        <li class="pos-item">
                            <?php
                            foreach ($produtos as $produto_info) {
                                if ($produto_info['id'] == $produto['id']) {
                                    echo '<img src="' . htmlspecialchars($produto_info['imagem']) . '" alt="' . htmlspecialchars($produto_info['nome']) . '" class="produto-imagem">';
                                    echo htmlspecialchars($produto_info['nome']);

                                    if (isset($produto_info['preco'])) {
                                        $totalCarrinho += $produto_info['preco'] * $produto['quantidade'];
                                    }
                                    break;
                                }
                            }
                            ?>
                            <div>
                                <input type="number" value="<?php echo htmlspecialchars($produto['quantidade']); ?>"
                                    data-id="<?php echo htmlspecialchars($produto['id']); ?>"
                                    class="quantidade-input">
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="acao" value="remover">
                                    <input type="hidden" name="id_produto" value="<?php echo htmlspecialchars($produto['id']); ?>">
                                    <button type="submit">Remover</button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
            <p class="total">Total do Carrinho: R$ <?php echo number_format($totalCarrinho, 2, ',', '.'); ?></p>
            <form method="POST" action="salvar_carrinho_estoque.php">
                <?php foreach ($_SESSION['carrinho'] as $produto): ?>
                    <input type="hidden" name="produtos[]" value="<?php echo htmlspecialchars($produto['id']); ?>">
                    <input type="hidden" name="quantidades[]" value="<?php echo htmlspecialchars($produto['quantidade']); ?>">
                <?php endforeach; ?>
                <button type="submit">Cadastrar no Estoque</button>
            </form>
        </div>

        <div class="estoque">
            <h2>Estoque de Produtos</h2>
            <input type="text" id="search" placeholder="Pesquisar produtos..." style="margin-bottom: 20px; padding: 10px; width: 100%; border: 1px solid #ccc; border-radius: 4px;">
            <ul id="product-list">
                <?php foreach ($produtos as $produto): ?>
                    <?php
                    $quantidadeNoCarrinho = getQuantidadeNoCarrinho($produto['id']);
                    $estoqueDisponivel = $produto['quantidade'] - $quantidadeNoCarrinho;
                    ?>
                    <li>
                        <img src="<?php echo htmlspecialchars($produto['imagem']); ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>" class="produto-imagem">
                        <div class="produto-info"><?php echo htmlspecialchars($produto['nome']); ?></div>
                        <div class="produto-info">Em Estoque: <?php echo htmlspecialchars($produto['quantidade']); ?></div>
                        <div class="produto-info">Preço: <?php echo $produto['preco'] === null ? 'Sem Preço' : 'R$ ' . htmlspecialchars($produto['preco']); ?></div>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="acao" value="adicionar">
                            <input type="hidden" name="id_produto" value="<?php echo htmlspecialchars($produto['id']); ?>">
                            <button type="submit" <?php echo $estoqueDisponivel <= 0 ? 'disabled' : ''; ?>>Adicionar</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <script>
        document.querySelectorAll('.quantidade-input').forEach(input => {
            input.addEventListener('blur', function() {
                const idProduto = this.getAttribute('data-id');
                const novaQuantidade = parseInt(this.value);

                let maxQuantidade = 0;
                let nomeProduto = '';
                <?php foreach ($produtos as $produto): ?>
                    if (idProduto == <?php echo $produto['id']; ?>) {
                        maxQuantidade = <?php echo $produto['quantidade']; ?>;
                        nomeProduto = "<?php echo htmlspecialchars($produto['nome']); ?>";
                    }
                <?php endforeach; ?>

                if (novaQuantidade > maxQuantidade) {
                    this.value = maxQuantidade;
                    alert(`A quantidade inserida para o produto "${nomeProduto}" (ID: ${idProduto}) excede o estoque disponível. Ajustando para o máximo disponível.`);
                }

                fetch('atualizar_quantidade.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id_produto=${idProduto}&quantidade=${this.value}`
                    })
                    .then(response => response.text())
                    .then(data => {
                        console.log('Quantidade atualizada:', data);
                        location.reload();
                    })
                    .catch(error => {
                        console.error('Erro ao atualizar quantidade:', error);
                    });
            });
        });

        document.querySelectorAll('input[name="quantidade"]').forEach(input => {
            input.addEventListener('input', function() {
                const maxQuantidade = parseInt(this.getAttribute('max'));
                const botaoAdicionar = this.nextElementSibling;

                if (parseInt(this.value) > maxQuantidade) {
                    botaoAdicionar.disabled = true;
                } else {
                    botaoAdicionar.disabled = false;
                }
            });
        });

        document.getElementById('search').addEventListener('input', function() {
            const query = this.value.toLowerCase();
            const produtos = document.querySelectorAll('#product-list li');

            produtos.forEach(produto => {
                const nomeProduto = produto.querySelector('.produto-info').textContent.toLowerCase();
                const idProduto = produto.querySelector('input[name="id_produto"]').value;
                if (nomeProduto.includes(query) || idProduto.includes(query)) {
                    produto.style.display = '';
                } else {
                    produto.style.display = 'none';
                }
            });
        });
    </script>
</body>

</html>