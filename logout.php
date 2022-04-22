<?php
session_status() === PHP_SESSION_ACTIVE || session_start();

$_SESSION = [];

if(isset($_COOKIE[session_name()])){
    setcookie(session_name(),session_id(),time() - 3600, '/');
}

session_destroy();

header("Location: bejelentkezes.php?logout=true");
?>
