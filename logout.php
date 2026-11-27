<?php
session_start();

// Destruction de la session
session_destroy();

// Suppression des cookies
setcookie("PHPSESSID", "", time() - 3600, "/");

// Redirection vers la page de login
header("Location: login.php");
exit();
?>
