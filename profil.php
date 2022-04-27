<?php
include_once("common/fuggvenyek.php");

session_status() === PHP_SESSION_ACTIVE || session_start();

if(!isset($_SESSION["user"]) || empty($_SESSION["user"])){
    header("Location: bejelentkezes.php?hiba=true");
    exit();
} else {
    $felhasznalonev = $_SESSION["user"]["felhasznalonev"];
}

// ERTEKELES MENTESE AZ ADATBAZISBA
if(isset($_POST['ertekeles'])) {
    ertekel($felhasznalonev,
        (htmlspecialchars($_POST['legitarsasag']) ?? ""),
        ( (htmlspecialchars($_POST['szemelyzet']) ?? 3) + (htmlspecialchars($_POST['szolgaltatas']) ?? 3) + (htmlspecialchars($_POST['menetrend']) ?? 3) + (htmlspecialchars($_POST['ar-ertek']) ?? 3) ) / 4);
    header("Location: profil.php?ertekeles=true");
}

include_once "common/connection.php";
$utazasiiroda = csatlakozas();

    if(isset($_POST['torol'])){
        $profiltorol = $_SESSION["user"]["felhasznalonev"];

        $adat = "DELETE FROM UTAS WHERE FELHASZNALONEV = '$profiltorol'";

        if(mysqli_query($utazasiiroda, $adat)){
            header('Location: logout.php');
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
    <link rel="stylesheet" href="css/profil.css">
    <link rel="stylesheet" href="css/modal.css"/>
</head>
<body>
<?php
    include_once("common/header.php");
    menuGeneralas('profil');
?>
<main>
    <div class="container">
        <div class="content">
            <?php
            if(isset($_GET["login"])) {
                echo "<div class='siker'>Sikeres bejelentkezés!</div>";
            } else if (isset($_GET["foglalas"])) {
                echo "<div class='siker'>Sikeres jegyfoglalás!</div>";
            } else if (isset($_GET['mod'])) {
                echo "<div class='siker'>A jelszó sikeresen meg lett változtatva!</div>";
            } else if (isset($_GET['profkepSiker'])) {
                echo "<div class='siker'>A profilkép sikeresen meg lett változtatva!</div>";
            } else if (isset($_GET['ertekeles'])) {
                echo "<div class='siker'>Sikeres értékelés!</div>";
            }
            ?>
            <table id="adatok-table">
                <caption><em>Saját adatok</em></caption>
                <tr>
                    <th>Felhasználónév:</th>
                    <td><?=$_SESSION["user"]["felhasznalonev"]?></td>
                </tr>
                <tr>
                    <th>Email cím:</th>
                    <td><?=$_SESSION["user"]["email"]?></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <form action="jelszomodositasa.php" method="post">
                            <input type="submit" name="ujjelszo" value="Jelszó módosítása">
                        </form>
                    </td>
                </tr>
                <tr id="profil-torles">
                    <td colspan="2">
                        <form action="profil.php" method="post">
                            <input type="hidden" name="profiltorol" value="<?=$_SESSION["user"]["felhasznalonev"]?>">
                            <input type="submit" id="submit" name="torol" value="Profil törlése">
                        </form>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><button id="ertekeles">Értékelje a járatokat üzemeltető légitársaságot!</button></td>
                </tr>
            </table>
            <table id="foglalas-adatok-table">
                <caption>Foglalások</caption>
                <tr>
                    <th>Kiindulási hely</th>
                    <th>Érkezési hely</th>
                    <th>Indulás</th>
                    <th>Összesített ár</th>
                </tr>
                <?php foglalasokListazasa($felhasznalonev); ?>
            </table>
            <table id="ertekeles-adatok-table">
                <caption>Értékelések</caption>
                <tr>
                    <th>Légitársaság</th>
                    <th>Összesített értékelés pontszáma</th>
                </tr>
                <?php ertekelesekListazasa($felhasznalonev); ?>
            </table>
        </div>
        <div id="legitarsasagi-ertekeles" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="bezaras">&times;</span>
                    <h2>Légitársasági értékelés</h2>
                    <p>Ossza meg tapasztalatait legutóbbi utazásáról és értékelje a járatokat üzemeltető légitársaságot. Töltse ki a lenti kérdőívet, ezzel segítve a légitársaságok munkáját és más utazókat a tájékozódásban.</p>
                </div>
                <div class="modal-body">
                    <form method="POST" action="profil.php">
                        <fieldset>
                            <legend>1. A repülőút adatai</legend>
                            <label for="melyik-legitarsasag">Melyik légitársasággal utazott?</label>
                            <br/><select id="melyik-legitarsasag" name="legitarsasag">
                                <?php legitarsasagListazas(); ?>
                            </select><br/>

                            <label for="mikor">Mikor utazott?</label>
                            <br/><input type="date" id="mikor" name="mikor" required/>
                        </fieldset>
                        <fieldset>
                            <legend>2. A légitársaság értékelése</legend>
                            <label for="szemelyzet">A személyzet munkája</label>
                            <p>Mennyire volt elégedett a személyzet munkájával? Mennyire volt segítőkész, hozzáértő, udvarias a személyzet? (1-5)</p>
                            <br/><input type="range" id="szemelyzet" name="szemelyzet" min="1" max="5"/> <br/>
                            <label for="szolgaltatas">A fedélzeti szolgáltatások színvonala</label>
                            <p>Mennyire volt elégedett a fedélzeti szolgáltatások színvonalával? Mennyire volt elégedett az ételek és az italok minőségével? Mennyire volt kényelmes az ülés? Volt szórakoztatórendszer a fedélzeten? (1-5)</p>
                            <br/><input type="range" id="szolgaltatas" name="szolgaltatas" min="1" max="5"/> <br/>
                            <label for="menetrend">A menetrend</label>
                            <p>Mennyire volt megfelelő Önnek a járat menetrendje? Pontosan indult és érkezett a repülőgép? (1-5)</p>
                            <br/><input type="range" id="menetrend" name="menetrend" min="1" max="5"/> <br/>
                            <label for="ar-ertek">Az ár-érték arány</label>
                            <p>Mennyire felelt meg a légitársaság által nyújtott szolgáltatás színvonala a repülőjegy árához képest? Azt kapta, amit az árért elvárt? (1-5)</p>
                            <br/><input type="range" id="ar-ertek" name="ar-ertek" min="1" max="5"/> <br/>
                        </fieldset>
                        <fieldset>
                            <legend>3. Szöveges értékelés</legend>
                            <label for="szoveges-ertekeles">Ide írja élménybeszámolóját!</label>
                            <br/><textarea rows="4" cols="50" id="szoveges-ertekeles" name="szoveges-ertekeles"></textarea><br/>
                        </fieldset>
                        <fieldset>
                            <legend>Köszönjük, hogy részt vett az értékelésben!</legend>
                            <br/><input type="submit" id="modal-ertekeles" name="ertekeles" value="Elküld"/>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
        <script src="js/modal.js"></script>
    </div>
</main>
<?php
    include_once("common/footer.php");
?>
</body>
</html>
