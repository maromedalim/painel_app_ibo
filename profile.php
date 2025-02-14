<?php
session_start();

// Verificar se o usuário está autenticado e é um administrador
if (!isset($_SESSION['id']) || !$_SESSION['admin']) {
    header("Location: login.php");
    exit();
}

ini_set("display_errors", 0);
ini_set("display_startup_errors", 0);
error_reporting(32767);
$db = new SQLite3("./api/.anspanel.db");
$res = $db->query("SELECT * FROM USERS WHERE ID='1'");
$row = $res->fetchArray();
$message = "<div class=\"alert alert-primary\" id=\"flash-msg\"><h4><i class=\"icon fa fa-check\"></i>Perfil Atualizado!</h4></div>";

if (isset($_POST["submit"])) {
    $target_dir = "img2/"; // Diretório onde a imagem será salva
    $target_file = $target_dir . "logo.png"; // Nome do arquivo salvo será sempre 'logo.png'
    
    // Verificar se o campo de arquivo foi enviado e não está vazio
    if (!empty($_FILES["logo"]["tmp_name"])) {
        move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file);

        // Atualizar a URL da imagem no banco de dados
        $db->exec("UPDATE USERS SET NAME='" . $_POST["name"] . "', USERNAME='" . $_POST["username"] . "', PASSWORD='" . $_POST["password"] . "', LOGO='" . $target_file . "' WHERE ID='1'");

        // Atualizar a sessão do usuário para refletir a nova imagem
        $_SESSION["name"] = $_POST["username"];
        $_SESSION["logo"] = $target_file . "?t=" . time(); // Adiciona um timestamp para evitar cache
    }

    header("Location: profile.php?m=" . urlencode($message));
}

$name = $row["NAME"];
$user = $row["USERNAME"];
$pass = $row["PASSWORD"];
$logo = isset($_SESSION["logo"]) ? $_SESSION["logo"] : $row["LOGO"];
include "includes/header.php";

echo "<!-- Início do Conteúdo da Página -->\n<div class=\"container-fluid\">\n\n";
if (isset($_GET["m"])) {
    echo urldecode($_GET["m"]);
}

echo "<h1 class=\"h3 mb-1 text-gray-800\">Atualizar Login</h1>\n<div class=\"row\">\n<div class=\"col-lg-12\">\n<div class=\"card border-left-primary shadow h-100 card shadow mb-4\">\n<div class=\"card-header py-3\">\n<h6 class=\"m-0 font-weight-bold text-primary\"><i class=\"fa fa-user\"></i> Atualizar Perfil</h6>\n</div>\n<div class=\"card-body\">\n<form method=\"post\" enctype=\"multipart/form-data\">\n";

echo "<div class=\"form-group \">\n<label class=\"control-label \" for=\"name\"><strong>Nome</strong></label>\n<div class=\"input-group\">\n";
echo "<input type=\"text\" class=\"form-control text-primary\" name=\"name\" value=\"" . $name . "\" placeholder=\"Digite o Nome\">\n";
echo "</div>\n</div>\n";

echo "<div class=\"form-group \">\n<label class=\"control-label \" for=\"username\"><strong>Usuário</strong></label>\n<div class=\"input-group\">\n";
echo "<input type=\"text\" class=\"form-control text-primary\" name=\"username\" value=\"" . $user . "\" placeholder=\"Digite o Nome de Usuário\">\n";
echo "</div>\n</div>\n";

echo "<div class=\"form-group \">\n<label class=\"control-label \" for=\"password\"><strong>Senha</strong></label>\n<div class=\"input-group\">\n";
echo "<input type=\"text\" class=\"form-control text-primary\" name=\"password\" value=\"" . $pass . "\" placeholder=\"Digite a Senha\">\n";
echo "</div>\n</div>\n";

echo "<div class=\"form-group \">\n<label class=\"control-label \" for=\"logo\"><strong>Logo</strong></label>\n<div class=\"input-group\">\n";
echo "<input type=\"file\" class=\"form-control text-primary\" name=\"logo\" placeholder=\"Upload da imagem do Perfil\">\n";
echo "</div>\n</div>\n";

echo "<div class=\"form-group\">\n<div>\n<button class=\"btn btn-success btn-icon-split\" name=\"submit\" type=\"submit\">\n";
echo "<span class=\"icon text-white-50\"><i class=\"fas fa-check\"></i></span><span class=\"text\">Atualizar</span>\n";
echo "</button>\n</div>\n</div>\n";

echo "<img type=\"image\" width=\"100px\" src=\"" . $logo . "\" alt=\"imagem\" /></div>\n";
echo "</div>\n</form>\n</div>\n</div>\n</div>\n";
include "includes/footer.php";
require "includes/ans.php";
echo "</body>\n";
?>
