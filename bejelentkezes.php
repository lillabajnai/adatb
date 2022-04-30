<?php
session_status() === PHP_SESSION_ACTIVE || session_start();

if(isset($_SESSION["user"]) && !empty($_SESSION["user"])){
    header("Location: profil.php");
    exit();
}
include_once "common/fuggvenyek.php";

if(isset($_POST["bejelentkezes"])) {
    $felhasznalonev = $_POST["felhasznalonev"];
    $jelszo = $_POST["jelszo"];
    $error = bejelentkezes($felhasznalonev, $jelszo);
}

?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Irinyi utazási iroda</title>
    <link rel="icon" href="img/negyedikicon.png"/>
    <link rel="stylesheet" href="css/style.css"/>
    <link rel="stylesheet" href="css/bejelentkezes.css">
</head>
<body>
<?php
    include_once "common/header.php";
    menuGeneralas('profil');
?>
<main>
    <div class="container">
        <div class="bejelentkezes">
            <?php
            if (isset($_GET["hiba"])) {
                echo "<div class='hiba'>Bejelentkezés szükséges!</div>";
            } else if (isset($_GET["logout"])) {
                echo "<div class='siker'>Sikeres kijelentkezés!</div>";
            } else if (isset($_GET["reg"])) {
                echo "<div class='siker'>Sikeres regisztráció!</div>";
            } else if(isset($error) === true) {
                echo "<div class='hiba'>Hibás felhasználónév vagy jelszó!</div>";
            }
            ?>
            <form method="POST" action="bejelentkezes.php">
                <label class="required-label">Felhasználónév: <input type="text" id="felhasznalonev" name="felhasznalonev" placeholder="Felhasználónév" required/></label> <br/><br/>
                <label class="required-label">Jelszó: <input type="password" id="password" name="jelszo" placeholder="Jelszó" required/></label> <br/><br/>
                <input type="submit" id="bejelentkezes" name="bejelentkezes" value="Bejelentkezés">
            </form>
            <button id="regisztracio-gomb" onclick="location.href='regisztracio.php'">Regisztráció</button>
        </div>
    </div>
</main>
</body>
</html>