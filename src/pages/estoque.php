<?php
require '../auth/auth.php';
include '../class/class_produtos.php';
include '../class/class_movimentacoes.php';

$ob_produto = new produtos();
$produtos = $ob_produto->getProdutos($_SESSION['id_comercio'])->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nome = $_POST['produto'];
    $quantidadeAdicional = $_POST['quantidade'];
    $preco = $_POST['preco'];
    $validade = !empty($_POST['validade']) ? $_POST['validade'] : null;
    $imagem = $_FILES['imagem']['name'];
    $operacao = $_POST['operacao'];

    $produtoAtual = $ob_produto->getProdutoById($id)->fetch(PDO::FETCH_ASSOC);
    $quantidadeAtual = $produtoAtual['quantidade'];

    if ($operacao === 'adicionar') {
        $quantidade = $quantidadeAtual + $quantidadeAdicional;
        $tipo = 2;
    } else if ($operacao === 'retirar') {
        $quantidade = $quantidadeAtual - $quantidadeAdicional;
        $tipo = 3;
    }

    $imagemPadrao = '../../public/assets/image/image.png';
    $caminhoImagemAtual = $ob_produto->getCaminhoImagem($id);

    if ($imagem && $imagem !== basename($imagemPadrao)) {
        if ($_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['status' => 'error', 'message' => 'Erro no upload da imagem: ' . $_FILES['imagem']['error']]);
            exit;
        }

        $caminhoDestino = '../../public/assets/image/produtos/' . $_SESSION['id_comercio'] . '/';

        if (!file_exists($caminhoDestino)) {
            mkdir($caminhoDestino, 0777, true);
        }

        if ($caminhoImagemAtual !== $imagemPadrao && file_exists($caminhoImagemAtual)) {
            unlink($caminhoImagemAtual);
        }

        $caminhoCompleto = $caminhoDestino . basename($imagem);
        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminhoCompleto)) {
            $verify_confirm = $ob_produto->updateProduto($id, $nome, $quantidade, $preco, $validade, $caminhoCompleto);
            if ($verify_confirm) {
                $produtoExistente = $ob_produto->getProdutoById($produtoAtual['id']);
                if ($produtoExistente->rowCount() > 0) {
                    $ob_movimentacoes = new movimentacoes();
                    $movimentacao_inserida = $ob_movimentacoes->insertMovimentacao($_SESSION['id_comercio'], $produtoAtual['comercio_id'], $tipo, $quantidade);
                    if (!$movimentacao_inserida) {
                        echo json_encode(['status' => 'error', 'message' => 'Erro ao inserir movimentação.']);
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Produto não encontrado.']);
                }
            }
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao mover o arquivo para o diretório de destino.']);
        }
    } else {
        $verify_confirm = $ob_produto->updateProduto($id, $nome, $quantidade, $preco, $validade, $caminhoImagemAtual);
        if ($verify_confirm) {
            $produtoExistente = $ob_produto->getProdutoById($produtoAtual['id']);
            if ($produtoExistente->rowCount() > 0) {
                $ob_movimentacoes = new movimentacoes();
                $movimentacao_inserida = $ob_movimentacoes->insertMovimentacao($_SESSION['id_comercio'], $produtoAtual['id'], $tipo, $quantidade);
                if (!$movimentacao_inserida) {
                    echo json_encode(['status' => 'error', 'message' => 'Erro ao inserir movimentação.']);
                    exit;
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Produto não encontrado.']);
                exit;
            }
        }
        echo json_encode(['status' => 'success']);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos do Comércio</title>
    <link rel="stylesheet" href="../../public/assets/css/style.css">
    <link rel="stylesheet" href="../../public/assets/css/cropper.min.css">
</head>

<body>
    <div class="corpo">
        <?php include 'includes/nav.php'; ?>
        <div class="container">
            <a href="dashboard.php" class="back-button">Voltar</a>
            <h1 style="margin-top: 20px;">Produtos do comércio</h1>
            <div class="form-item">

                <div class="products-container">
                    <div class="search-bar">
                        <input type="text" class="form-input" placeholder="Pesquisar produto" id="searchInput" oninput="searchProducts()">
                    </div>
                    <div class="product-grid">
                        <?php foreach ($produtos as $produto): ?>
                            <div class="product-card">
                                <div class="product-image">
                                    <img src="<?= htmlspecialchars($produto['imagem']); ?>" alt="Imagem do produto" onerror="this.onerror=null; this.src='../../public/assets/image/image.png';">
                                </div>
                                <div class="product-info-container">
                                    <div class="product-info">ID: <span class="product-info-text"><?= htmlspecialchars($produto['id']); ?></span></div>
                                    <div class="product-info">Item: <span class="product-info-text"><?= htmlspecialchars($produto['nome']); ?></span></div>
                                    <div class="product-info">Em estoque: <span class="product-info-text"><?= htmlspecialchars($produto['quantidade']); ?></span></div>
                                    <div class="product-info">Preço: <span class="product-info-text"><?= $produto['preco'] == null ? 'Sem preço' : 'R$ ' . htmlspecialchars($produto['preco']) ?></span></div>
                                    <div class="product-info">Validade: <span class="product-info-text">
                                            <?= !empty($produto['validade']) && $produto['validade'] !== '0000-00-00' ? date('d/m/Y', strtotime($produto['validade'])) : 'Não informado'; ?>
                                        </span></div>
                                </div>
                                <div class="product-actions">
                                    <button class="btn-edit" onclick="openEditModal(<?= $produto['id']; ?>, '<?= htmlspecialchars($produto['nome']); ?>', '<?= htmlspecialchars($produto['imagem']); ?>', <?= $produto['quantidade']; ?>, '<?= $produto['preco'] ?>', '<?= $produto['validade'] ?>')">Editar</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="editModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Editar Produto</h2>
            <form id="editForm" action="estoque.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <input type="hidden" name="id" id="productId">
                    <label for="productName">Nome:</label>
                    <input type="text" name="produto" id="productName" required>
                </div>

                <div class="form-group">
                    <label>Imagem do Produto:</label>
                    <div class="image-preview-container">
                        <img id="currentImage" src="" alt="Imagem do produto">
                    </div>
                    <input type="file" name="imagem" id="productImage" accept="image/*">
                    <input type="hidden" name="cropped_image" id="cropped-image-input">
                </div>

                <div class="form-group quantity-group">
                    <div>
                        <label for="currentQuantity">Quantidade Atual:</label>
                        <span id="currentQuantity" class="current-quantity"></span>
                    </div>

                    <div class="quantity-controls">
                        <label for="quantityChange">Quantidade (Adicionar ou Retirar):</label>
                        <div class="quantity-input-group">
                            <input type="number" name="quantidade" id="quantityChange" value="0" required min="0">
                            <select name="operacao" id="operationSelect">
                                <option value="adicionar">Adicionar</option>
                                <option value="retirar">Retirar</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 15px">
                    <div class="form-group" style="width: 100%">
                        <label for="productPrice">Preço:</label>
                        <input type="text" name="preco" id="productPrice">
                    </div>

                    <div class="form-group" style="width: 100%">
                        <label for="productValidity">Data de Validade:</label>
                        <input type="date" name="validade" id="productValidity">
                    </div>
                </div>

                <button type="submit" class="btn-primary">Salvar</button>
            </form>
        </div>
    </div>

    <div id="crop-modal" class="modal-overlay">
        <div class="modal-foto">
            <div class="modal-header">
                <h3 class="modal-title">Recortar Imagem</h3>
            </div>

            <div id="image-container">
                <img id="image-preview" src="">
            </div>

            <div class="modal-buttons">
                <button type="button" id="close-modal" class="btn-secondary">Fechar</button>
                <button type="button" id="crop-button" class="btn-primary">Recortar</button>
            </div>
        </div>
    </div>

    <script src="../../public/assets/js/cropper.min.js"></script>

    <script>
        document.getElementById('editForm').addEventListener('submit', function(event) {
            event.preventDefault();

            var formData = new FormData(this);
            var operation = document.getElementById('operationSelect').value;

            formData.append('operacao', operation);

            fetch(this.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    if (data.includes("Erro ao mover o arquivo para o diretório de destino.") || data.includes("Erro no upload da imagem")) {
                        alert("Erro ao fazer upload da imagem.");
                    } else {
                        location.reload();
                    }
                })
                .catch(error => console.error('Erro:', error));
        });

        function openEditModal(id, name, image, quantity, price, validity) {
            document.getElementById('productId').value = id;
            document.getElementById('productName').value = name;
            document.getElementById('currentImage').src = image;
            document.getElementById('currentQuantity').innerText = quantity;
            document.getElementById('quantityChange').value = 0;
            document.getElementById('productPrice').value = price;
            document.getElementById('productValidity').value = validity ? validity : '';
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function searchProducts() {
            const searchValue = document.getElementById('searchInput').value.toLowerCase();
            const productCards = document.querySelectorAll('.product-card');

            productCards.forEach(card => {
                const productId = card.querySelector('.product-info-text').innerText.toLowerCase();
                const productName = card.querySelector('.product-info').nextElementSibling.innerText.toLowerCase();

                if (productId.includes(searchValue) || productName.includes(searchValue)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const imageInput = document.getElementById('productImage');
            const imagePreview = document.getElementById('image-preview');
            const cropModal = document.getElementById('crop-modal');
            const closeModal = document.getElementById('close-modal');
            const cropButton = document.getElementById('crop-button');
            const croppedImageInput = document.getElementById('cropped-image-input');
            const currentImage = document.getElementById('currentImage');
            let cropper = null;

            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = function(event) {
                    imagePreview.src = event.target.result;
                    cropModal.style.display = 'block';

                    if (cropper) {
                        cropper.destroy();
                    }

                    cropper = new Cropper(imagePreview, {
                        aspectRatio: 1,
                        viewMode: 1,
                        autoCropArea: 1
                    });
                };
                reader.readAsDataURL(file);
            });

            closeModal.addEventListener('click', function() {
                cropModal.style.display = 'none';
                imageInput.value = '';
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
            });

            cropButton.addEventListener('click', function() {
                if (!cropper) return;

                const canvas = cropper.getCroppedCanvas({
                    width: 500,
                    height: 500
                });

                const croppedImageUrl = canvas.toDataURL();
                croppedImageInput.value = croppedImageUrl;
                currentImage.src = croppedImageUrl;
                cropModal.style.display = 'none';
                cropper.destroy();
                cropper = null;
            });

            document.getElementById('editForm').addEventListener('submit', function(e) {
                const currentQty = parseInt(document.getElementById('currentQuantity').innerText);
                const changeQty = parseInt(document.getElementById('quantityChange').value);
                const operation = document.getElementById('operationSelect').value;

                if (operation === 'retirar' && changeQty > currentQty) {
                    e.preventDefault();
                    alert('Não é possível retirar mais produtos do que existe em estoque!');
                    return false;
                }
            });
        });
    </script>
</body>

</html>