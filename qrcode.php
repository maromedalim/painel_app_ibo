<?php

include ('includes/header.php');
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Ibo Pro</title> 
</head>
<body>

<style>
    /* Estilo para botões verdes */
    .btn-green {
        background-color: #1cc88a; /* Cor de fundo verde */
        color: white; /* Cor do texto branco */
        padding: 10px 20px; /* Espaçamento interno */
        border: none; /* Sem borda */
        border-radius: 5px; /* Bordas arredondadas */
        cursor: pointer; /* Cursor de mão ao passar */
        text-decoration: none; /* Sem sublinhado */
    }

    /* Estilo para botões verdes ao passar o mouse */
    .btn-green:hover {
        background-color: #17A673; /* Cor de fundo mais escura */
    }

    /* Estilo para a tabela */
    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #f2f2f2;
        color: #333;
        font-weight: bold;
    }

    td {
        background-color: #fff;
        color: #555;
    }

    /* Estilo para linhas alternadas */
    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    /* Estilo para hover */
    tr:hover {
        background-color: #f5f5f5;
        transition: background-color 0.3s ease;
    }

    /* Estilo para botões de ação */
    .action-buttons button {
        padding: 8px 12px;
        margin-right: 5px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .action-buttons button.edit {
        background-color: #3498db;
        color: #fff;
    }

    .action-buttons button.delete {
        background-color: #e74c3c;
        color: #fff;
    }

    .action-buttons button:hover {
        opacity: 0.8;
    }

    .btn-cell {
        text-align: center; 
    }

    .btn-cell button {
        margin-right: 10px; 
    }

    .image-cell {
        text-align: center; 
    }

    .image-cell img {
        display: block; 
        margin: 0 auto; 
    }

    .title-cell {
        text-align: center; 
    }
</style>

<div class="col-lg-12">
    <!-- Códigos Personalizados -->
    <div class="card border-left-primary shadow h-100 card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Gerenciar QrCode</h6>
        </div>
        <div class="card-body">
            <button class="btn-green" onclick="showForm()">Adicionar QrCode</button>

            <!-- Formulário de adicionar banner (inicialmente oculto) -->
            <div class="card-header py-3" id="bannerForm" style="display: none;">
                <h1 class="h3 mb-1 text-gray-800">QrCode</h1>
                <form action="upload.php" method="post" enctype="multipart/form-data">
                    <label for="title">Título:</label>
                    <input type="text" name="title" id="title"><br>
                    <input type="file" name="banner" id="banner">
                    <input class="btn-green" type="submit" value="Enviar" name="submit">
                </form>
            </div>

            <h2>QrCode Existentes</h2>
            <table border="1">
                <tr>
                    <th style="text-align: center;">Título</th>
                    <th style="text-align: center;">Imagem</th>
                    <th style="text-align: center;">Ações</th>
                </tr>
                <?php 
                // Diretório onde os banners são armazenados
                $directory = "uploads/";

                // Verifica se o diretório de uploads existe e é acessível
                if (is_dir($directory) && is_readable($directory)) {
                    // Obtém todos os arquivos de imagem no diretório de uploads
                    $banner_files = glob($directory . "*.{jpg,jpeg,png,gif}", GLOB_BRACE);

                    // Exibe os banners na tabela
                    foreach ($banner_files as $file) {
                        $filename = pathinfo($file, PATHINFO_FILENAME);
                        $file_extension = pathinfo($file, PATHINFO_EXTENSION); // Obtém a extensão do arquivo
                        $full_filename = $filename . '.' . $file_extension; // Nome completo do arquivo

                        echo "<tr>";
                        echo "<td class='title-cell'>$filename</td>"; // Exibir o título do banner
                        echo "<td class='image-cell'><img src='$file' alt='$filename' width='100'></td>";
                        echo "<td class='btn-cell'>";
                        // Botões de ação (Editar e Excluir)
                        echo "<button class='btn-green' onclick='showEditForm(\"$filename\", \"$filename\")'>Editar</button>";
                        echo "<button class='btn-green' onclick='confirmDelete(\"$full_filename\")'>Excluir</button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>O diretório de uploads não existe ou não é acessível.</td></tr>";
                }
                ?>
            </table>

            <!-- Formulário de edição de banner (inicialmente oculto) -->
            <div class="card-header py-3" id="editBannerForm" style="display: none;">
                <h2>Editar Banner</h2>
                <form action="edit.php" method="post" enctype="multipart/form-data">
                    <label for="editTitle">Novo Título:</label>
                    <input type="text" name="editTitle" id="editTitle"><br>
                    Selecione a nova imagem do banner:
                    <input type="file" name="editBanner" id="editBanner">
                    <!-- Campo oculto para o novo título -->
                    <input type="hidden" name="newTitle" id="newTitle">
                    <input type="submit" value="Salvar" name="submit">
                </form>
            </div>

            <script>
                // Função para mostrar o formulário de adicionar banner
                function showForm() {
                    document.getElementById("bannerForm").style.display = "block";
                }

                // Função para mostrar o formulário de editar banner
                function showEditForm(filename, title) {
                    // Preencher o campo de título do formulário de edição
                    document.getElementById("editTitle").value = title;
                    // Preencher o campo oculto com o novo título
                    document.getElementById("newTitle").value = title;
                    // Exibir o formulário de edição
                    document.getElementById("editBannerForm").style.display = "block";
                }

                // Função para confirmar a exclusão do banner
                function confirmDelete(filename) {
                    if (confirm("Tem certeza que deseja excluir o banner '" + filename + "'?")) {
                        // Se confirmado, redirecionar para a mesma página com um parâmetro de exclusão
                        window.location.href = "qrcode.php?delete=" + filename;
                    }
                }
            </script>

            <?php
            // Diretório onde os banners são armazenados
            $directory = "uploads/";

            // Lógica para exclusão do banner
            if (isset($_GET['delete'])) {
                $filename = basename($_GET['delete']); // Nome do arquivo completo (incluindo extensão)
                $filepath = $directory . $filename; // Caminho completo do arquivo

                if (file_exists($filepath)) {
                    unlink($filepath);
                    echo "<script>alert('Banner $filename excluído com sucesso.');</script>";
                    echo "<meta http-equiv='refresh' content='0'>"; // Atualiza a página após a exclusão
                    exit; // Impede a execução do restante do código PHP
                } 
            }
            ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
