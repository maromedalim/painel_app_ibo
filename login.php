<?php

function getIPAddress()
{
    $ipAddress = 'undefined';

    if (isset($_SERVER)) {
        $ipAddress = $_SERVER['REMOTE_ADDR'];

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        }
    } else {
        $ipAddress = getenv('REMOTE_ADDR');

        if (getenv('HTTP_X_FORWARDED_FOR')) {
            $ipAddress = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_CLIENT_IP')) {
            $ipAddress = getenv('HTTP_CLIENT_IP');
        }
    }

    $ipAddress = htmlspecialchars($ipAddress, ENT_QUOTES, 'UTF-8');
    return $ipAddress;
}

session_start();
$jsondata111 = file_get_contents("./includes/ansibo.json");
$json111 = json_decode($jsondata111, true);
$col1 = $json111["info"];
$col2 = $col1["aa"];
$db_check1 = new SQLite3("api/.anspanel.db");
$db_check1->exec("CREATE TABLE IF NOT EXISTS USERS(id INT PRIMARY KEY, NAME TEXT, USERNAME TEXT, PASSWORD TEXT, LOGO TEXT)");

$rows = $db_check1->query("SELECT COUNT(*) as count FROM USERS");
$row = $rows->fetchArray();
$numRows = $row["count"];

if ($numRows == 0) {
    $db_check1->exec("INSERT INTO USERS(id, NAME, USERNAME, PASSWORD, LOGO) VALUES('1','Seu Nome','admin','admin','img2/logo.png')");
}

$res_login = $db_check1->query("SELECT * FROM USERS WHERE id='1'");
$row_login = $res_login->fetchArray();
$name_login = $row_login["NAME"];
$logo_login = $row_login["LOGO"];

if (isset($_POST["login"])) {
    if (!$db_check1) {
        echo $db_check1->lastErrorMsg();
    }

    $sql_check = "SELECT * FROM USERS WHERE USERNAME='" . $_POST["username"] . "' AND PASSWORD='" . $_POST["password"] . "'";
    $ret_check = $db_check1->query($sql_check);

    while ($row_check = $ret_check->fetchArray()) {
        $id_check = $row_check["id"];
        $store_type = $row_check["store_type"];
        $NAME = $row_check["NAME"];
        $LOGO_check = $row_check["LOGO"];
        $isAdmin = $row_check['ADMIN'];
    }

    if (empty($id_check)) {
        $message = "<div class=\"alert alert-danger\" id=\"flash-msg\"><h4><i class=\"icon fa fa-times\"></i>Usuário ou senha inválidos!</h4></div>";
        echo $message;
    } else {
        $_SESSION["admin"] = $isAdmin;
        $_SESSION["N"] = $id_check;
        $_SESSION["id"] = $id_check;
        $_SESSION["store_type"] = $store_type;

        $path = "users";
        if ($store_type == '2') {
            $path .= '_mac';
        }

        header("Location: $path.php");
    }

    $db_check1->close();
}

$date = date("d-m-Y H:i:s");
$IPADDRESS = getIPAddress();

$imageFilex = '/img2/logo.png?' . time();


?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAINEL IBO REVENDA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" crossorigin="anonymous" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
    <link href="css/sb-admin-<?php echo $col2; ?>.css" rel="stylesheet">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <style>
        @media (max-width: 767px) {
            body {
                padding-top: 40px;
                background-color: black;
                color: white;
            }

            .container {
                padding: 0 20px;
                /* Adapte as margens conforme necessário */
            }
        }

        body {
            font-family: 'Roboto', sans-serif;
        }

        .container {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            height: 100vh;
        }

        .form-container {
            background-color: #000;
            padding: 20px;
            border-radius: 10px;
            border-top: 5px solid #4e73df;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            /* Margem superior do formulário */
            width: 100%;
            /* Largura do formulário */
            max-width: 400px;
            /* Largura máxima do formulário */
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .btn {
            padding: 5px 20px;
            font-size: 16px;
        }

        .btn2 {
            padding: 10px 20px;
            font-size: 20px;
        }

        .password-toggle-icon {
            cursor: pointer;
            user-select: none;
        }

        .outside-image {
            width: 50%; /* Aumente a largura da imagem */
            max-width: 100%;
            height: auto; /* Mantém a proporção da imagem */
            margin-bottom: 20px; /* Margem inferior da imagem */
        }

        .form-title {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        .image-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px; /* Margem inferior da imagem */
        }

        /* Estilo da mensagem de erro */
        .alert {
            transition: opacity 0.5s ease-in-out;
        }
    </style>
</head>

<body class="bg-gradient-primary">

    <div class="container">

        <div class="form-container">
            <div class="image-container">
                <img src="<?= $imageFilex ?>" alt="Descrição da Imagem" class="img-fluid outside-image">
            </div>
            <div class="form-title">Painel Ibo Revendas Chat Temas</div>
            <form method="POST">
                <p class="text" style="transition-delay: 0.4s"><br>
                </p>
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="NOME DE USUÁRIO" name="username" required autofocus />
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <input type="password" class="form-control" placeholder="SENHA" name="password" required />
                        <button type="button" class="btn btn-secondary password-toggle-icon" onclick="togglePasswordVisibility()">
                            <i class="fas fa-eye-slash"></i>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <button class="btn2 btn-lg btn btn-primary btn-block" name="login" type="submit">Login</button>
                </div>
                <p class="text-center text-warning"></p><br>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-easing@1.4.1/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.querySelector('[name="password"]');
            const passwordIcon = document.querySelector('.password-toggle-icon i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.className = 'fas fa-eye';
            } else {
                passwordInput.type = 'password';
                passwordIcon.className = 'fas fa-eye-slash';
            }
        }

        // Oculta a mensagem de erro após 2 segundos
        setTimeout(function() {
            var flashMsg = document.getElementById('flash-msg');
            if (flashMsg) {
                flashMsg.style.opacity = '0';
                setTimeout(function() {
                    flashMsg.style.display = 'none';
                }, 500); // Espera 0.5s para ocultar completamente após o fade-out
            }
        }, 2000); // 2 segundos
    </script>
</body>

</html>
