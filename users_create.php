<?php

ini_set("display_errors", 0);
ini_set("display_startup_errors", 0);
error_reporting(32767);
session_start();

$id = $_SESSION['id'];
$isAdmin = $_SESSION['admin'];
$storeType = $_SESSION['store_type'];

$limited = false;
$db = new SQLite3("./api/.ansdb.db");
$db->exec("CREATE TABLE IF NOT EXISTS ibo(id INTEGER PRIMARY KEY NOT NULL,\r\nmac_address VARCHAR(100),\r\nkey VARCHAR(100),\r\nusername VARCHAR(100),\r\npassword VARCHAR(100),\r\nexpire_date VARCHAR(100),\r\ndns VARCHAR(100),\r\nepg_url VARCHAR(100),\r\ntitle VARCHAR(100),\r\nurl VARCHAR(100),\r\ntype VARCHAR(100))");
$res = $db->query("SELECT * FROM ibo");

if (isset($_POST["submit"])) {
    $we = strtotime($_POST["expire_date"]);
    $ne = date("Y-m-d", $we);
    
    if($storeType == 2){
        $ne = date('Y-m-d', strtotime('+1 year'));
    }
    
    if ($_POST["type"] == "0") {
        $line = $_POST["dns"] . "/get.php?username=" . $_POST["username"] . "&password=" . $_POST["password"] . "&type=m3u_plus&output=ts";
    } else {
        $line = $_POST["url"];
    }
    $address1 = strtoupper($_POST["mac_address"]);
    $playlistpassword = "";
    if(isset($_POST["playlistpassword"])){
        $playlistpassword = $_POST["playlistpassword"];
    }

    if (!$isAdmin) {
        $dbUsers = new SQLite3("./api/.anspanel.db");
        $res = $dbUsers->query("SELECT mac_amount FROM USERS WHERE id = '$id' ");
        $macCount = $res->fetchArray()['mac_amount'];
        $dbUsers->close();

        $res = $db->query("SELECT COUNT(*) as count FROM ibo WHERE id_user = '$id' AND active = 1 AND expire_date > date('now')");
        $count = $res->fetchArray()['count'];

        if ($count >= $macCount) {
            $limited = true;
        }
    }
    if(!$limited){
        $res = $db->query("SELECT COUNT(*) as count FROM ibo WHERE mac_address = '$address1'");
        $count = $res->fetchArray()['count'];
        
        if ($count > 0) {
            // Editar o registro existente
            $db->exec("UPDATE ibo SET key = '" . $_POST["key"] . "', username = '" . $_POST["username"] . "', password = '" . $_POST["password"] . "', expire_date = '" . $ne . "', dns = '" . $_POST["dns"] . "', epg_url = '" . $_POST["epg_url"] . "', title = '" . $_POST["title"] . "', url = '" . $line . "', type = '" . $_POST["type"] . "', id_user = '$id', playlistpassword = '$playlistpassword', active = 1 WHERE mac_address = '$address1'");
        } else {
            // Inserir novo registro
            $db->exec("INSERT INTO ibo(mac_address, key, username, password, expire_date, dns, epg_url, title, url, type, id_user, playlistpassword, active) VALUES('" . $address1 . "', '" . $_POST["key"] . "', '" . $_POST["username"] . "', '" . $_POST["password"] . "', '" . $ne . "', '" . $_POST["dns"] . "', '" . $_POST["epg_url"] . "', '" . $_POST["title"] . "', '" . $line . "', '" . $_POST["type"] . "', '$id', '$playlistpassword', 1)");
        }
        
        if (!isset($_SESSION['macs'])){
            $_SESSION['macs'] = [];    
        }
        
        $macRes = $db->query("SELECT * FROM ibo WHERE mac_address = '$address1'");
        while($row = $macRes->fetchArray()){
            if(!sessionContains($row)){
                array_push($_SESSION['macs'], $row);   
            }
        }
        
        header("Location: users.php");
    }

    $db->close();
}

function sessionContains($searchRow){
    foreach ($_SESSION['macs'] as $session_row){
        if($session_row['id'] == $searchRow['id']){
            return true;
        }
    }
    
    return false;
}

include "includes/header.php";

if($limited){
    echo "<div class='alert alert-danger'>Limite de MACs excedido!</div>";
}
echo "        <div class=\"container-fluid\">\n\n          <!-- Page Heading -->\n          <h1 class=\"h3 mb-1 text-gray-800\"> Ativar Usuário</h1>\n\n              <!-- Códigos Personalizados -->\n                <div class=\"card border-left-primary shadow h-100 card shadow mb-4\">\n                <div class=\"card-header py-3\">\n                <h6 class=\"m-0 font-weight-bold text-primary\"><i class=\"fas fa-user\"></i> Detalhes do Usuário</h6>\n                </div>\n                <div class=\"card-body\">\n                        <form method=\"post\">          \n                        <div class=\"form-group \">\n                                    <label class=\"control-label \" for=\"mac_address\">\n                                        <strong>MAC do Dispositivo</strong> \n                                    </label>\n                                    <div class=\"input-group\">\n                                        <input class=\"form-control mac_address text-primary\" id=\"description\" name=\"mac_address\" placeholder=\"Insira o ID do Dispositivo: 00:11:22:33:44:55\" type=\"text\" maxlength=\"26\" required/>\n                                    </div>\n                                </div>\n                        <input type=\"hidden\" name=\"key\" value=\"136115\">\n	  \r\n\r\n<div class=\"form-group\">\n                                    \r\n<div>\n                            <strong> Selecione o Modo de Login: </strong> \r\n                                    <select class=\"select form-control type\" id=\"type\" name=\"type\" >\r\n\t\t\t\t\t\t\t\t\t    \r\n                                        <option value=\"0\" data-value=\"op0\">Usar Xtream Codes\r\n</option>\r\n<option value=\"1\" data-value=\"op1\">Usar Lista M3U8\r\n</option>\r\n                          </select>\r\n</div>\n\n  \r\n\r\n        \r\n\r\n</div>\n    \r\n                                <div class=\"form-group \">\n                                    <label class=\"control-label \" for=\"title\">\n                                        <strong>Nome do Servidor</strong>\n                                    </label>\n                                    <div class=\"input-group\">\n                                        <input type=\"text\" class=\"form-control text-primary\" name=\"title\" placeholder=\"Insira o Nome do Servidor\" id=\"title\" required/>\n                                    </div>\n                                </div>\n                <div class=\"active1\">\n                                <div class=\"form-group \">\n                                    <label class=\"control-label \" for=\"username\">\n                                        <strong>Nome de Usuário</strong>\n                                    </label>\n                                    <div class=\"input-group\">\n                                        <input type=\"text\" class=\"form-control text-primary\" name=\"username\" placeholder=\"Insira o Nome de Usuário\" id=\"discription\"/>\n                                    </div>\n                                </div>\n                                <div class=\"form-group \">\n                                    <label class=\"control-label \" for=\"password\">\n                                        <strong>Senha</strong>\n                                    </label>\n                                    <div class=\"input-group\">\n                                        <input type=\"text\" class=\"form-control text-primary\" name=\"password\" placeholder=\"Insira a Senha\" />\n                                    </div>\n                                </div>\n

                                 <div class=\"form-group \">\n                                    <label class=\"control-label \" for=\"dns\">\n                                        <strong>DNS</strong>\n                                    </label>\n                                    <div class=\"input-group\">\n                                        <input type=\"text\" class=\"form-control text-primary\" name=\"dns\" placeholder=\"Insira o Servidor DNS\" id=\"discription\" />\n                                    </div>\n                                </div>\n                         </div>\n                     <div class=\"active2\">\n                                 <div class=\"form-group \">\n                                    <label class=\"control-label \" for=\"dns\">\n                                        <strong>Lista M3U</strong>\n                                    </label>\n                                    <div class=\"input-group\">\n                                        <input type=\"text\" class=\"form-control text-primary\" name=\"url\" placeholder=\"Insira a URL da Lista M3U\" id=\"discription\" />\n                                    </div>\n                                </div>\n                       </div>\n                                 <div class=\"form-group \">\n                                    <label class=\"control-label \" for=\"epg_url\">\n                                        <strong>URL do EPG</strong>\n                                    </label>\n                                    <div class=\"input-group\">\n                                        <input type=\"text\" class=\"form-control text-primary\" name=\"epg_url\" placeholder=\"Insira o Guia EPG\" id=\"epg_url\"/>\n                                    </div>\n                                </div>";
                                if ($storeType == '1'){ 
                                    echo "<div class=\"form-group \">\n                                    <label class=\"control-label \" for=\"expire_date\">\n                                        <strong>Data de Expiração</strong>\n                                    </label>\n                                    <div class=\"input-group\">\n                                        <input type=\"text\" class=\"form-control text-primary\" name=\"expire_date\" value='2050-01-01' placeholder=\"AAAA-MM-DD\" id=\"datetimepicker\" autocomplete=\"off\"/> \n                                    </div>\n\n                                </div>\n";
                                }

                                if ($storeType == '2') {
                                    echo "<div class=\"form-group \">
                                                <label class=\"control-label \" for=\"playlistpassword\"><strong>Senha da Lista de Reprodução (Opcional)</strong></label>
                                            <div class=\"input-group\">\n                                        <input type=\"text\" class=\"form-control text-primary\" name=\"playlistpassword\" placeholder=\"Insira a Senha da Lista de Reprodução\" id=\"playlistpassword\"/>
                                        </div>
                                    </div>";
                                }

                                echo "<button class=\"btn btn-success btn-icon-split\" name=\"submit\" type=\"submit\">\n                        <span class=\"icon text-white-50\"><i class=\"fas fa-check\"></i></span><span class=\"text\">Enviar</span>\n                        </button>\n                                    </div>\n\n                                </div>\n                            </form>\n                    </div>\n                </div>\n                </div>\n    <br><br><br>\n";
include "includes/footer.php";
echo "\r\n<script>\r\n//select activecode form\r\n//var response = {};\r\n//response.val = \"op2\";\r\n//\$(\"#codemode option[data-value='\" + response.val +\"']\").attr(\"selected\",\"selected\");\r\n\r\n//hide activecode form\r\n\$('.active1').show(); \r\n\$('.active2').hide(); \r\n\r\n//Show/hide activecode select\r\n\$(document).ready(function(){\r\n  \$('.type').change(function(){\r\n    if(\$('.type').val() < 1) {\r\n      \$('.active1').show(); \r\n      \$('.active2').hide(); \r\n    } else {\r\n      \$('.active2').show(); \r\n      \$('.active1').hide(); \r\n    } \r\n  });\r\n});\r\n</script>\r\n\r\n\n    <script>\n\$('#confirm-delete').on('show.bs.modal', function(e) {\n    \$(this).find('.btn-ok').attr('href', \$(e.relatedTarget).data('href'));\n});\n\r\n\r\n    </script>\r\n    <script type=\"text/javascript\">\r\n// @require http://code.jquery.com/jquery-latest.js\r\n// ==/UserScript==\r\ndocument.getElementById(\"description\").addEventListener('keyup', function() { \r\n  var mac = document.getElementById('description').value;\r\n  var macs = mac.split(':').join('');\r\n  macs = chunk(macs, 2).join(':');\r\n  document.getElementById('description').value = macs.toString();\r\n});\r\n\r\nfunction chunk(str, n) {\r\n    var ret = [];\r\n    var i;\r\n    var len;\r\n\r\n    for(i = 0, len = str.length; i < len; i += n) {\r\n       ret.push(str.substr(i, n))\r\n    }\r\n\r\n    return ret\r\n};\r\n    </script>\n</body>\n\n</html>";

?>
