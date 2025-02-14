<?php

ini_set("display_errors", 0);
ini_set("display_startup_errors", 0);
error_reporting(32767);
session_start();

$db = new SQLite3("./api/.ansdb.db");
$res = $db->query("SELECT * FROM ibo WHERE id='" . $_GET["update"] . "'");
$row = $res->fetchArray();
$id_mac = $row["id"];
$mac_address = $row["mac_address"];
$key = $row["key"];
$expire_date = $row["expire_date"];
$username = $row["username"];
$password = $row["password"];
$dns = $row["dns"];
$epg_url = $row["epg_url"];
$title = $row["title"];
$url = $row["url"];
$type = $row["type"];
$playlistpassword = $row['playlistpassword'];
$id_user = $row['id_user'];
$active = $row['active'];

$pwd_req = !empty($playlistpassword);

$storeType = $_SESSION['store_type'];

$auth = false;

if (isset($_POST['auth'])) {
    $auth = $playlistpassword == $_POST['password'];
} else if (isset($_POST["submit"])) {
    $auth = true;
    $address1 = strtoupper($_POST["mac_address"]);
    if ($_POST["type"] == "0") {
        $line = $_POST["dns"] . "/get.php?username=" . $_POST["username"] . "&password=" . $_POST["password"] . "&type=m3u_plus&output=ts";
    } else {
        $line = $_POST["url"];
    }

    $playlistpassword = "";
    if (isset($_POST["playlistpassword"])) {
        $playlistpassword = $_POST["playlistpassword"];
    }

    $active = $_POST["active"] == 1 ? 1 : 'NULL';

    if ($active === 'NULL') {
        $ne = date('Y-m-d', strtotime('-1 day'));
    } else {
        $we = strtotime($_POST["expire_date"]);
        $ne = date("Y-m-d", $we);
    }

    $db->exec("UPDATE ibo SET
        mac_address='" . $address1 . "',
        key='" . $_POST["key"] . "',
        expire_date='" . $ne . "',
        username='" . $_POST["username"] . "',
        password='" . $_POST["password"] . "',
        dns='" . $_POST["dns"] . "',
        epg_url='" . $_POST["epg_url"] . "',
        title='" . $_POST["title"] . "',
        url='" . $line . "',
        type='" . $_POST["type"] . "',
        playlistpassword='$playlistpassword',
        id_user='" . $_POST["id_user"] . "',
        active=$active
        WHERE id='" . $_POST["id"] . "'");

    $return = "users.php";

    if (isset($_SESSION['macs'])) {
        $res = $db->query("SELECT * FROM ibo WHERE id = " . $_POST["id"]);
        $row = $res->fetchArray();
        $db->close();

        for ($i = 0; $i < count($_SESSION['macs']); $i++) {
            $session_row = $_SESSION['macs'][$i];
            if ($session_row['id'] == $_POST["id"]) {
                $_SESSION['macs'][$i] = $row;
            }
        }
    }

    header("Location: $return");
}

include "includes/header.php";
echo "        <div class=\"container-fluid\">\n\n          <!-- Página Principal -->\n          <h1 class=\"h3 mb-1 text-gray-800\"> Atualizar Usuário</h1>\n\n              <!-- Códigos Personalizados -->\n                <div class=\"card border-left-primary shadow h-100 card shadow mb-4\">\n                <div class=\"card-header py-3\">\n                <h6 class=\"m-0 font-weight-bold text-primary\"><i class=\"fas fa-user\"></i> Editar Usuário</h6>\n                </div>
    <div class=\"card-body\"><form method=\"post\">";

if (!$pwd_req || $auth) {
    echo "<div class=\"form-group \">\n                                    <label class=\"control-label \" for=\"mac_address\">\n                                        <strong>MAC</strong> \n                                    </label>\n                                    <div class=\"input-group\">\n";
    echo "                                        <input type=\"hidden\" name=\"id\" value=\"" . $id_mac . "\">" . "\n";
    echo "                                        <input class=\"form-control text-primary\" id=\"description\" name=\"mac_address\" value=\"" . $mac_address . "\" type=\"text\" required/>" . "\n";
    echo "                                    </div>\n                                </div>\n                        <div class=\"form-group \">\n                                    <label class=\"control-label \" for=\"key\">\n                                       <strong>Chave</strong> \n                                   </label>\n                                   <div class=\"input-group\">\n";
    echo "                                        <input class=\"form-control text-primary\" id=\"description\" name=\"key\" value=\"136115\" type=\"text\" readonly/>" . "\n";
    echo "                                    </div>\n                                </div>\n                                <div class=\"form-group \">\n                                    <label class=\"control-label \" for=\"title\">\n                                        <strong>Cliente</strong>\n                                    </label>\n                                    <div class=\"input-group\">\n";
    echo "                                        <input type=\"text\" class=\"form-control text-primary\" name=\"title\" value=\"" . $title . "\" id=\"discription\" required/>" . "\n";
    echo "                                    </div>\n                                </div>\n \r\n                  <div class=\"form-group\">\n                                    \r\n                              <div>\n   \r\n                              <strong> Selecione o modo de login: </strong> \n                                    <select class=\"select form-control type\" id=\"type\" name=\"type\" >\r\n                                        <option value=\"0\" data-value=\"0\" ";
    echo $type == "0" ? "selected" : "";
    echo ">Use Xtream Codes</option> \r\n\t\t\t\t\t\t\t\t\t    <option value=\"1\" data-value=\"1\" ";
    echo $type == "1" ? "selected" : "";
    echo ">Use List M3U8</option>\r\n                          </select>\r\n        </div></div>\n                          <div class=\"active2\">\n                                <div class=\"form-group \">\n                                    <label class=\"control-label \" for=\"uls\">\n                                        <strong>URL M3U8</strong>\n                                    </label>\n                                    <div class=\"input-group\">\n";
    echo "                                        <input type=\"text\" class=\"form-control text-primary\" name=\"url\" value=\"" . $url . "\" id=\"discription\" />" . "\n";
    echo "                                    </div>\n                                </div>\n                            </div>\n                          <div class=\"active1\">\n                                <div class=\"form-group \">\n                                    <label class=\"control-label \" for=\"dns\">\n                                        <strong>DNS</strong>\n                                    </label>\n                                    <div class=\"input-group\">\n";
    echo "                                        <input type=\"text\" class=\"form-control text-primary\" name=\"dns\" value=\"" . $dns . "\" id=\"discription\"/>" . "\n";
    echo "                                    </div>\n                                </div>\n                                <div class=\"form-group \">\n                                    <label class=\"control-label \" for=\"username\">\n                                        <strong>Username</strong>\n                                    </label>\n                                    <div class=\"input-group\">\n";
    echo "                                        <input type=\"text\" class=\"form-control text-primary\" name=\"username\" value=\"" . $username . "\" id=\"discription\" />" . "\n";
    echo "                                    </div>\n                                </div>\n                                <div class=\"form-group \">\n                                    <label class=\"control-label \" for=\"password\">\n                                        <strong>Password</strong>\n                                    </label>\n                                    <div class=\"input-group\">\n";
    echo "                                        <input type=\"text\" class=\"form-control text-primary\" name=\"password\" value=\"" . $password . "\" id=\"discription\" />" . "\n";
    echo "                                    </div>\n                                </div>\n                           </div>\n"; 
    
    echo "<div class=\"form-group \">\n                                    <label class=\"control-label \" for=\"expire_date\">\n                                        <strong>Válidade</strong>\n                                    </label>\n                                    <div class=\"input-group\"><input type=\"text\" class=\"form-control text-primary\" name=\"expire_date\" placeholder=\"YYYY-MM-DD\" id=\"datetimepicker\" value=\"" . $expire_date . "\" /> " . "\n";
    echo "                                    </div>\n\n                                </div>";
        
    echo "<div class=\"form-group \">\n                                    <label class=\"control-label \" for=\"epg_url\">\n                                        <strong>EPG Url</strong>\n                                    </label>\n                                    <div class=\"input-group\">\n";
    echo "                                        <input type=\"text\" class=\"form-control text-primary\" name=\"epg_url\" value=\"" . $epg_url . "\" id=\"discription\" />
    </div>
    </div>";
    
    echo "<div class=\"form-group \">\n                                    <label class=\"control-label \" for=\"id_user\">\n                                        <strong>Código Revenda</strong>\n                                    </label>\n                                    <div class=\"input-group\">\n";
    echo "                                        <input type=\"text\" class=\"form-control text-primary\" name=\"id_user\" value=\"" . $id_user . "\" id=\"id_user\" pattern=\"[0-9]*\" title=\"Apenas números\" required/>" . "\n";
    echo "                                    </div>\n                                </div>\n";

    echo "<div class=\"form-group\">\n                                    <label class=\"control-label \" for=\"active\">\n                                        <strong>Status</strong>\n                                    </label>\n                                    <div class=\"input-group\">\n";
    echo "                                        <select class=\"form-control text-primary\" name=\"active\" id=\"active\">\n";
    echo "                                            <option value=\"1\"" . ($active == 1 ? " selected" : "") . ">Ativado</option>\n";
    echo "                                            <option value=\"NULL\"" . (is_null($active) ? " selected" : "") . ">Desativado</option>\n";
    echo "                                        </select>\n";
    echo "                                    </div>\n                                </div>\n";

    if ($storeType == '2') {
        echo "<div class=\"form-group \">
                <label class=\"control-label \" for=\"playlistpassword\"><strong>Playlist Password (Optional)</strong></label>
                    <div class=\"input-group\">
                        <input type=\"text\" class=\"form-control text-primary\" name=\"playlistpassword\" placeholder=\"Enter Playlist Password\" id=\"playlistpassword\" value='$playlistpassword'/>
                    </div>
                </div>";
    }

    echo "<div class=\"form-group\">\n                                    <div>\n                                        <button class=\"btn btn-success btn-icon-split\" name=\"submit\" type=\"submit\">\n                        <span class=\"icon text-white-50\"><i class=\"fas fa-check\"></i></span><span class=\"text\">Atualizar</span>\n                        </button>\n                                    </div>\n\n                                </div>";
} else {
    if ($invalid) {
        echo "<div class='alert alert-danger'>Senha incorreta</div>";
    }
    echo "<h2>Para editar a lista, insira a senha dessa lista</h2>";
    echo "<div class=\"form-group \">
    <label class=\"control-label \" for=\"epg_url\">
        <strong>Playlist Password</strong>
    </label>
    <div class=\"input-group\">
        <input type=\"text\" class=\"form-control text-primary\" name=\"password\" />
    </div>
    <button class=\"btn btn-success\" name=\"auth\" type=\"submit\">Login</button>
    </div>";
}

echo "</form></div></div></div>\n";
include "includes/footer.php";
echo "    <script>\n\$('#confirm-delete').on('show.bs.modal', function(e) {\n    \$(this).find('.btn-ok').attr('href', \$(e.relatedTarget).data('href'));\n});\n    </script>\n\r\n<script>\r\n//hide activecode form\r\n      \$('.active1').show(); \r\n      \$('.active2').hide(); \r\n\r\n//Show/hide activecode select\r\n\$(document).ready(function(){\r\n  \$('.type').change(function(){\r\n    if(\$('.type').val() < 1) {\r\n      \$('.active1').show(); \r\n      \$('.active2').hide(); \r\n    }else {\r\n      \$('.active2').show(); \r\n      \$('.active1').hide(); \r\n    } \r\n  });\r\n  \$('.type').ready(function(){\r\n    if(\$('.type').val() < 1) {\r\n      \$('.active1').show(); \r\n      \$('.active2').hide(); \r\n    }else {\r\n      \$('.active2').show(); \r\n      \$('.active1').hide(); \r\n    } \r\n  });\r\n});\r\n</script>\r\n\r\n\n\r\n\r\n    <script type=\"text/javascript\">\r\n// @require http://code.jquery.com/jquery-latest.js\r\n// ==/UserScript==\r\ndocument.getElementById(\"description\").addEventListener('keyup', function() { \r\n  var mac = document.getElementById('description').value;\r\n  var macs = mac.split(':').join('');\r\n  macs = chunk(macs, 2).join(':');\r\n  document.getElementById('description').value = macs.toString();\r\n});\r\n\r\nfunction chunk(str, n) {\r\n    var ret = [];\r\n    var i;\r\n    var len;\r\n\r\n    for(i = 0, len = str.length; i < len; i += n) {\r\n       ret.push(str.substr(i, n))\r\n    }\r\n\r\n    return ret\r\n};\r\n    </script>\n</body>\n\n</html>";

?>
