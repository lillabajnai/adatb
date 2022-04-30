<?php
session_status() === PHP_SESSION_ACTIVE || session_start();
if(!isset($_SESSION["user"]) || empty($_SESSION["user"] || !($_SESSION["user"]["felhasznalonev"]==='admin'))){
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Irinyi utazási iroda</title>
    <link rel="icon" href="img/negyedikicon.png"/>
    <link rel="stylesheet" href="css/style.css"/>
    <link rel="stylesheet" href="css/admin.css"/>
</head>
<body>
<?php
include_once "common/header.php";
include_once "common/fuggvenyek.php";
menuGeneralas('admin');
?>
<main>
    <div class="container">
        <?php alMenuGeneralas('rekordok'); ?>
        <div class="tablak">
            <?php osszesRekord(); ?>
        </div>
    </div>
</main>
</body>
</html>