<?php
session_status() === PHP_SESSION_ACTIVE || session_start();

if(isset($_SESSION["user"]) && !empty($_SESSION["user"])){
    header("Location: profil.php");
    exit();
}

include_once("common/fuggvenyek.php");

if(isset($_POST['regisztracio'])) {
    $felhasznalonev = htmlspecialchars($_POST['felhasznalonev']);
    $email = htmlspecialchars($_POST['email']);
    $jelszo = htmlspecialchars($_POST['jelszo']);
    $errors = regisztracio($felhasznalonev, $email, $jelszo);
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Irinyi utazási iroda</title>
    <link rel="icon" href="img/negyedikicon.png"/>
    <link rel="stylesheet" href="css/style.css"/>
    <link rel="stylesheet" href="css/regisztracio.css">
</head>
<body>
<?php
    include_once("common/header.php");
    menuGeneralas('profil');
?>
<main>
    <div class="container">
        <div class="regisztracios-urlap">
            <?php
                if(isset($_POST['regisztracio'])) {
                    if(count($errors) > 0) {
                        echo "<div class='hiba'>";
                        foreach ($errors as $error) {
                            echo $error . "<br/>";
                        }
                        echo "</div>";
                    }
                }
            ?>
            <form action="regisztracio.php" method="POST">
                <label>Vezetéknév:</label> <input type="text" name="vezeteknev" placeholder="Vezetéknév" value="<?php if (isset($_POST['vezeteknev'])) echo $_POST['vezeteknev']; ?>"> <br/>
                <label>Keresztnév:</label> <input type="text" name="keresztnev" placeholder="Keresztnév" value="<?php if (isset($_POST['keresztnev'])) echo $_POST['keresztnev']; ?>"> <br/>
                <label class="required-label">Felhasználónév:</label> <input type="text" name="felhasznalonev" placeholder="Felhasználónév" value="<?php if (isset($_POST['felhasznalonev'])) echo $_POST['felhasznalonev']; ?>" required> <br/>
                <label class="required-label">Jelszó:</label> <input type="password" name="jelszo" minlength="3" maxlength="10" placeholder="**********" required> <br/>
                <label class="required-label">E-mail cím:</label> <input type="email" name="email" placeholder="valami@pelda.com" value="<?php if (isset($_POST['email'])) echo $_POST['email']; ?>" required> <br/>
                <input type="submit" id="submit" name="regisztracio" value="Regisztráció" formaction="regisztracio.php">
            </form>
        </div>
    </div>
</main>
</body>
</html>