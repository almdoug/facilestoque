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
    <title>Cadastro de Categorias e Produtos</title>
    <link rel="stylesheet" href="../../public/assets/css/style.css">
    <link rel="stylesheet" href="../../public/assets/css/cropper.min.css">
</head>

<body>
    <div class="corpo">
        <?php include 'includes/nav.php'; ?>
        <div class="container">
            <a href="dashboard.php" class="back-button">Voltar</a>
            <a href="#" id="criar_categoria" class="back-button">Nova categoria</a>

            <div id="new-category" class="hidden">
                <h2 style="margin-top: 20px;">Nova categoria</h2>
                <div class="form-item">
                    <form action="../auth/add_categoria.php" method="POST">
                        <label for="categoria">Nome da categoria:</label>
                        <input type="text" id="categoria" name="categoria" required class="form-input">
                        <button type="submit" class="btn-primary">Adicionar categoria</button>
                    </form>
                </div>
            </div>

            <h2 style="margin-top: 20px;">Cadastrar produto</h2>
            <div class="form-item">
                <form action="../auth/add_produto.php" method="POST" class="form-item-add-produto" enctype="multipart/form-data">
                    <div style="display: flex; gap: 20px;">
                        <div class="form-group" style="flex: 1;">
                            <label for="produto">Nome do produto:</label>
                            <input type="text" id="produto" name="produto" style="font-size: 2rem;" required class="form-input">
                        </div>

                        <div class="form-group" style="flex: 1;">
                            <label for="produto">Código do produto:</label>
                            <input type="text" id="cod_produto" name="cod_produto" style="font-size: 2rem;" required class="form-input">
                        </div>

                        <!-- <div class="form-group" style="flex: 1;">
                            <label for="produto">Unidade:</label>
                            <select class="form-input" name="unidade" id="unidade" style="font-size: 2rem;" required>
                                <option value="" selected disabled>Selecione</option>
                                <option value="unidade">Unidade</option>
                                <option value="gramas">Gramas (g)</option>
                            </select>
                        </div> -->
                    </div>



                    <div class="form-group">
                        <label for="categoria_id">Categoria:</label>
                        <select id="categoria_id" name="categoria_id" required class="form-input" style="font-size: 2rem;">
                            <option value="" disabled selected>Selecione a categoria</option>
                            <?php if (!empty($categorias)): ?>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?= $categoria['id_categoria']; ?>"><?= $categoria['nome']; ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div>
                        <label for="tem_validade">Adicionar validade?</label>
                        <input type="checkbox" id="tem_validade" name="tem_validade" onclick="toggleValidade()">

                        <div id="data_validade" style="display:none;">
                            <label for="validade">Data de validade:</label>
                            <input type="date" class="form-input" id="validade" name="validade">
                        </div>
                    </div>

                    <div>
                        <label for="imagem_produto">Adicionar imagem do produto?</label>
                        <input type="checkbox" id="imagem_produto" name="imagem_produto" onclick="toggleImagem()">

                        <div id="input_imagem" style="display:none;">
                            <label for="imagem">Selecione a imagem:</label>
                            <input type="file" id="imagem" name="imagem" accept="image/*">

                            <input type="hidden" name="cropped_image" id="cropped-image-input">

                            <div id="thumbnail-container" style="display: none; margin-top: 10px;">
                                <img id="thumbnail-preview" class="thumbnail-preview" src="" alt="Imagem recortada">
                            </div>
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="btn-primary">Cadastrar produto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="../../public/assets/js/cropper.min.js"></script>
    <script>
        function toggleValidade() {
            var checkbox = document.getElementById("tem_validade");
            var dataValidade = document.getElementById("data_validade");

            if (checkbox.checked) {
                dataValidade.style.display = "block";
            } else {
                dataValidade.style.display = "none";
            }
        }

        function toggleImagem() {
            var checkbox = document.getElementById("imagem_produto");
            var inputImagem = document.getElementById("input_imagem");

            if (checkbox.checked) {
                inputImagem.style.display = "block";
            } else {
                inputImagem.style.display = "none";
            }
        }

        let criar_categoria = document.getElementById("criar_categoria");
        let category_container = document.getElementById("new-category");
        criar_categoria.addEventListener("click", function() {
            category_container.classList.toggle("hidden");
        })

        document.addEventListener('DOMContentLoaded', function() {
            const imageInput = document.getElementById('imagem');
            const imagePreview = document.getElementById('image-preview');
            const cropModal = document.getElementById('crop-modal');
            const closeModal = document.getElementById('close-modal');
            const cropButton = document.getElementById('crop-button');
            const croppedImageInput = document.getElementById('cropped-image-input');
            const thumbnailContainer = document.getElementById('thumbnail-container');
            const thumbnailPreview = document.getElementById('thumbnail-preview');
            let cropper = null;

            function openModal() {
                cropModal.style.display = 'block';
            }

            function closeModalAndReset() {
                cropModal.style.display = 'none';
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
            }

            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = function(event) {
                    imagePreview.src = event.target.result;

                    openModal();

                    imagePreview.onload = function() {
                        if (cropper) {
                            cropper.destroy();
                        }

                        cropper = new Cropper(imagePreview, {
                            aspectRatio: 1,
                            viewMode: 1,
                            autoCropArea: 1,
                            responsive: true,
                            restore: false
                        });
                    };
                };
                reader.readAsDataURL(file);
            });

            closeModal.addEventListener('click', function() {
                closeModalAndReset();
                imageInput.value = '';
            });

            cropButton.addEventListener('click', function() {
                if (!cropper) return;

                const croppedCanvas = cropper.getCroppedCanvas({
                    width: 500,
                    height: 500
                });

                const croppedImageUrl = croppedCanvas.toDataURL();
                croppedImageInput.value = croppedImageUrl;

                thumbnailPreview.src = croppedImageUrl;
                thumbnailContainer.style.display = 'block';

                closeModalAndReset();
            });

            cropModal.addEventListener('click', function(e) {
                if (e.target === cropModal) {
                    closeModalAndReset();
                    imageInput.value = '';
                }
            });
        });
    </script>

    <div id="crop-modal" class="modal-overlay">
        <div class="modal-foto">
            <div class="modal-header">
                <h3 class="modal-title">Recortar Imagem</h3>
            </div>

            <div id="image-container" style="max-width: 100%; margin: 0 auto;">
                <img id="image-preview" src="" style="max-width: 100%; display: block;">
            </div>

            <div class="modal-buttons">
                <button type="button" id="close-modal" class="btn-secondary">Fechar</button>
                <button type="button" id="crop-button" class="btn-primary">Recortar</button>
            </div>
        </div>
    </div>

</body>

</html>