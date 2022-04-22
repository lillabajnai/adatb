<?php
session_status() === PHP_SESSION_ACTIVE || session_start();

if(!isset($_SESSION["user"]) || empty($_SESSION["user"])){
    header("Location: bejelentkezes.php?hiba=true");
    exit();
} else {
    $felhasznalonev = $_SESSION["user"]["felhasznalonev"];
}

if(isset($_POST['megerosites'])) {
    $jaratszam=$_POST['post-jaratszam'];
    $felnott=$_POST['post-felnott'];
    $gyermek=$_POST['post-gyermek'];
}

include_once("common/connection.php");
$utazasiiroda = csatlakozas();

// MENTÉS AZ ADATBÁZISBA
if(isset($_POST['foglalas'])) {
    $jarat = $_POST['jaratszam-vegso'];
    $ar = $_POST['post-ara'];
    echo $felhasznalonev;
    $foglalas = oci_parse($utazasiiroda, "INSERT INTO JEGY (AR, FELHASZNALONEV, JARATSZAM) VALUES ('$ar', '$felhasznalonev', '$jarat')");
    oci_execute($foglalas) or die('Hibás utasítás!');
    header("Location: profil.php?foglalas=true");
}

if((!isset($_POST['post-felnott']) && !isset($_POST['foglalas']) && !isset($_POST['post-gyermek']))) {
    header("Location: jegyfoglalas.php?hibas=true");
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Irinyi utazási iroda</title>
    <link rel="icon" href="img/negyedikicon.png"/>
    <link rel="stylesheet" href="css/style.css"/>
    <link rel="stylesheet" href="css/foglalas.css"/>
</head>
<body>
<?php
    include_once("common/header.php");
    include_once("common/fuggvenyek.php");
    menuGeneralas('jegyfoglalas');
?>
<main>
<div class="container">
    <div class="jarat-adatok">
        <table id="repjegy-dijtetelei">
        <caption>Repülőjegy díjtételei:</caption>
            <?php
            $alapar=0;
            echo '<tr>';
                echo '<th>Felnőtt alapár</th>';
                $felnott_ar = rand(10000,100000);
                echo '<td>' . $felnott . ' x ' . number_format($felnott_ar) . ' Ft' . '</td>';
            echo '</tr>';
            $alapar+=$felnott*$felnott_ar;
            if($gyermek > 0) {
                echo '<tr>';
                    echo '<th>Gyermek alapár</th>';
                    $gyermek_ar = rand(10000,100000);
                    echo '<td>' . $gyermek . ' x ' . number_format($gyermek_ar) . ' Ft' . '</td>';
                echo '</tr>';
                $alapar+=$gyermek*$gyermek_ar;
            }
            // ÉTKEZÉS ÁRAK - FELNŐTT
            $etkezes_szamlalo=0;
            for($i = 1; $i <= $felnott; ++$i) {
                isset($_POST['etkezes-felnőtt-'. $i]) === true ? $etkezes_szamlalo++ : null;
            }

            // ÉTKEZÉS ÁRAK - GYERMEK
            if($gyermek > 0) {
                for ($i = 1; $i <= $gyermek; ++$i) {
                    isset($_POST['etkezes-gyermek-' . $i]) === true ? $etkezes_szamlalo++ : null;
                }
            }

            echo '<tr>';
                echo '<th>Étkezés</th>';
                echo '<td>' . $etkezes_szamlalo . ' x 5,720 Ft' . '</td>';
            echo '</tr>';
            $etkezes_ara = $etkezes_szamlalo*5720;

            echo '<tr>';
                echo '<th>Kezelési költség</th>';
                echo '<td>1 x 214 Ft</td>';
                $kezelesi=214;
            echo '</tr>';

            // OSSZEGEK SZUMMAZASA
            $osszesen=$alapar + $etkezes_ara + $kezelesi;
            echo '<tr>';
                echo '<th>Összesen:</th>';
                echo '<td>' . number_format($osszesen) . ' Ft' . '</td>';
            echo '</tr>';
            ?>
        </table>
        <form method="post" action="jegyfoglalas_megerosites.php">
            <div id="szabaly-checkbox">
                <label for="szabalyzat-elfogadas">
                    <input type="checkbox" id="szabalyzat-elfogadas" name="szabalyzat" required>
                    A repülőjegy megvásárlásával elfogadom az irinyiutazasiiroda.hu szabályzatát.
                </label> <br/>
                <label for="18-felett">
                    <input type="checkbox" id="18-felett" name="szabalyzat" required>
                    Büntetőjogi felelősségem teljes tudatában kijelentem, hogy elmúltam 18 éves.
                </label> <br/>
            </div>
            <?php
            echo '<input type="hidden" name="jaratszam-vegso" value=' . $jaratszam . '>';
            echo '<input type="hidden" name="post-ara" value=' . $osszesen . '>';
            ?>
            <input type="submit" name="foglalas" value="Foglalás">
        </form>
    </div>
</div>
</main>
</body>
</html>
<?php
    if(isset($foglalas) && is_resource($foglalas)) {
        mysqli_free_result($foglalas);
    }

    csatlakozas_zarasa($utazasiiroda);
?>