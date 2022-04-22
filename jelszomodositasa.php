<?php
session_status() === PHP_SESSION_ACTIVE || session_start();

include_once "common/connection.php";
include_once "classes/Felhasznalo.php";

$utazasiiroda = csatlakozas();
if (isset($_POST['modosit'])) {
    $errors = [];
    $email = $_SESSION['user']['email'];
    $jelenlegiJelszo = htmlspecialchars($_POST['jelszo']);
    $ujJelszo = htmlspecialchars($_POST['ujjelszo']);
    $ujJelszoUjra = htmlspecialchars($_POST['ujjelszoujra']);

    $jelszoCheck = mysqli_query($utazasiiroda, "SELECT JELSZO from UTAS where EMAIL = '$email'");

    while(($current_row = mysqli_fetch_assoc($jelszoCheck)) != null) {
        $ellenorizendoJelszo = $current_row['JELSZO'];
    }
    if(!password_verify($jelenlegiJelszo,$ellenorizendoJelszo)) {
        $errors[] = "A jelenlegi jelszó helytelenül lett megadva!";
    }

    if (strlen($ujJelszo) <= 3 || strlen($ujJelszo) >= 10) {
        $errors[] = "Legalább 3, legfeljebb 10 karakter hosszúságú jelszót adjon meg!";
    }

    if (!preg_match('/[A-Za-z]/', $ujJelszo) || !preg_match('/[0-9]/', $ujJelszo)) {
        $errors[] = "A jelszónak legalább egy nagy betűt és egy számot tartalmaznia kell!";
    }

    if ($ujJelszo !== $ujJelszoUjra) {
        $errors[] = "A jelszavak nem egyeznek!";
    }

    if (count($errors) === 0) {
        $ujJelszo = password_hash($ujJelszo, PASSWORD_DEFAULT);
        mysqli_query($utazasiiroda, "UPDATE UTAS SET JELSZO = '$ujJelszo' where EMAIL = '$email'");
        header("Location: profil.php?mod=true");
    }
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
    <link rel="stylesheet" href="css/jelszomodositasa.css">

</head>
<body>
<?php
    include_once("common/header.php");
    include_once("common/fuggvenyek.php");
    menuGeneralas('profil');
?>
<main>
    <div class="container">
        <div class="jelszomodositasdiv">
            <?php
            if(isset($_POST['modosit'])) {
                if(count($errors) > 0) {
                    echo "<div class='hiba'>";
                    foreach ($errors as $error) {
                        echo $error . "<br/>";
                    }
                    echo "</div>";
                }
            }
            ?>
            <form method="POST" action="jelszomodositasa.php">
                <label class="required-label">Jelenlegi jelszó: <input type="password" name="jelszo" placeholder="Jelenlegi jelszó" required/></label> <br/>
                <label class="required-label">Új jelszó: <input type="password" name="ujjelszo" placeholder="Új jelszó" required/></label> <br/>
                <p>Legalább 3, legfeljebb 10 karakter hosszúságú jelszót adjon meg!</p>
                <p>Legalább egy nagy betűt és egy számot tartalmaznia kell!</p>
                <label class="required-label">Új jelszó újra: <input type="password" name="ujjelszoujra" placeholder="Új jelszó újra" required/></label> <br/>
                <input type="submit" id="modosit" name="modosit" value="Jelszó módosítása">
                <button id="megse" onclick="location.href='profil.php'">Mégse</button>
            </form>
        </div>
    </div>
</main>
<?php
    include_once("common/footer.php");
?>
</body>
</html>
