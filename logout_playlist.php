<?php


session_start();
session_destroy();
setcookie("auth", "");
header("location: login_mac.php");

?>