<?php
session_status() === PHP_SESSION_ACTIVE || session_start();

if(!isset($_SESSION["user"]) || empty($_SESSION["user"])){
    header("Location: bejelentkezes.php?hiba=true");
    exit();
} else {
    $felhasznalonev = $_SESSION["user"]["felhasznalonev"];
}

include_once("common/fuggvenyek.php");
if(isset($_POST['ertekeles'])) {
    $legitarsasag = htmlspecialchars($_POST['legitarsasag']);
    $ertekeles = ((htmlspecialchars($_POST['szemelyzet']) ?? 3) +
                    (htmlspecialchars($_POST['szolgaltatas']) ?? 3) +
                    (htmlspecialchars($_POST['menetrend']) ?? 3) +
                    (htmlspecialchars($_POST['ar-ertek']) ?? 3)) / 4;
    ertekel($felhasznalonev, $legitarsasag, $ertekeles);
}

if(isset($_POST['biztositas-1-kotes']) || isset($_POST['biztositas-2-kotes']) ||
    isset($_POST['biztositas-3-kotes']) || isset($_POST['biztositas-4-kotes']) ||
    isset($_POST['biztositas-5-kotes'])) {
    $sysdate = date('d/M/y');

    isset($_POST['biztositas-1-kotes']) === true ? biztositasKotes($felhasznalonev, $_POST['biztositoID-1'], htmlspecialchars($_POST['kategoria-1']), $sysdate) : null;
    isset($_POST['biztositas-2-kotes']) === true ? biztositasKotes($felhasznalonev, $_POST['biztositoID-2'], htmlspecialchars($_POST['kategoria-2']), $sysdate) : null;
    isset($_POST['biztositas-3-kotes']) === true ? biztositasKotes($felhasznalonev, $_POST['biztositoID-3'], htmlspecialchars($_POST['kategoria-3']), $sysdate) : null;
    isset($_POST['biztositas-4-kotes']) === true ? biztositasKotes($felhasznalonev, $_POST['biztositoID-4'], htmlspecialchars($_POST['kategoria-4']), $sysdate) : null;
    isset($_POST['biztositas-5-kotes']) === true ? biztositasKotes($felhasznalonev, $_POST['biztositoID-5'], htmlspecialchars($_POST['kategoria-5']), $sysdate) : null;
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
    <script src="js/biztosito.js"></script>
</head>
<body>
<?php
    include_once("common/header.php");
    menuGeneralas('profil');
?>
<main>
    <div class="container">
        <div class="sajat-adatok">
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
                } else if (isset($_GET['biztositasKotes'])) {
                    echo "<div class='siker'>Sikeres biztosítás kötés!</div>";
                }
            ?>
            <table id="adatok-table">
                <caption>Saját adatok</caption>
                <tr>
                    <th>Felhasználónév:</th>
                    <td><?=$_SESSION["user"]["felhasznalonev"]?></td>
                </tr>
                <tr>
                    <th>Email cím:</th>
                    <td><?=$_SESSION["user"]["email"]?></td>
                </tr>
                <tr>
                    <td colspan="2"><button id="ertekeles">Értékelje a járatokat üzemeltető légitársaságot!</button></td>
                </tr>
            </table>
            <h2>Foglalások</h2>
                <?php foglalasokListazasa($felhasznalonev); ?>
            <h2>Értékelések</h2>
                <?php ertekelesekListazasa($felhasznalonev); ?>
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

        <div id="biztositas-kotes" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <span onclick="document.getElementById('biztositas-kotes').style.display='none'" class="bezaras">&times;</span>
                    <h2>Biztosítás kötés</h2>
                </div>

                <div class="modal-body">
                    <?php biztositasListazas(); ?>
                    <form method="POST" action="profil.php">
                        <div id="szabaly-checkbox">
                            <label for="szabalyzat-elfogadas">
                                <input type="checkbox" id="szabalyzat-elfogadas" name="szabalyzat" required>
                                A biztosítás megkötésével elfogadom az irinyiutazasiiroda.hu szabályzatát.
                            </label> <br/>
                            <label for="18-felett">
                                <input type="checkbox" id="18-felett" name="szabalyzat" required>
                                Büntetőjogi felelősségem teljes tudatában kijelentem, hogy elmúltam 18 éves.
                            </label> <br/>
                        </div>
                        <div class="biztosito">
                            <input type="radio" id="biztosito-1" name="biztosito">
                            <label for="biztosito-1">Biztonságos Biztosító</label>

                            <div class="biztositas-1">
                                <label for="kategoria-1">Kategória:</label>
                                <select id="kategoria-1" name="kategoria-1" class="require-if-active" data-require-pair="biztosito">
                                    <option disabled selected>Nincs kiválasztva</option>
                                    <?php biztositasokListazas('Hello, én a Biztonságos Biztosító vagyok, és nagyon biztonságos vagyok');?>
                                </select>
                                <input type="hidden" name="biztositoID-1" value="1001"/>
                                <input type="submit" value="Megkötés" name="biztositas-1-kotes"/>
                            </div>
                        </div>

                        <div class="biztosito">
                            <input type="radio" id="biztosito-2" name="biztosito">
                            <label for="biztosito-2">Megbízható Biztosító</label>

                            <div class="biztositas-2">
                                <label for="kategoria-2">Kategória:</label>
                                <select id="kategoria-2" name="kategoria-2" class="require-if-active" data-require-pair="#biztosito">
                                    <option disabled selected>Nincs kiválasztva</option>
                                    <?php biztositasokListazas('Üdv, én a Megbízható Biztosító vagyok, és nagyon megbízható vagyok');?>
                                </select>
                                <input type="hidden" name="biztositoID-2" value="1002"/>
                                <input type="submit" value="Megkötés" name="biztositas-2-kotes"/>
                            </div>
                        </div>

                        <div class="biztosito">
                            <input type="radio" id="biztosito-3" name="biztosito">
                            <label for="biztosito-3">Olcsó Biztosító</label>

                            <div class="biztositas-3">
                                <label for="kategoria-3">Kategória:</label>
                                <select id="kategoria-3" name="kategoria-3" class="require-if-active" data-require-pair="#biztosito">
                                    <option disabled selected>Nincs kiválasztva</option>
                                    <?php biztositasokListazas('Hello, én az Olcsó Biztosító vagyok, és nagyon olcsó vagyok');?>
                                </select>
                                <input type="hidden" name="biztositoID-3" value="Hello, én az Olcsó Biztosító vagyok, és nagyon olcsó vagyok"/>
                                <input type="submit" value="Megkötés" name="1003"/>
                            </div>
                        </div>

                        <div class="biztosito">
                            <input type="radio" id="biztosito-4" name="biztosito">
                            <label for="biztosito-4">Legjobb Biztosító</label>

                            <div class="biztositas-4">
                                <label for="kategoria-4">Kategória:</label>
                                <select id="kategoria-4" name="kategoria-4" class="require-if-active" data-require-pair="#biztosito">
                                    <option disabled selected>Nincs kiválasztva</option>
                                    <?php biztositasokListazas('Üdv, én a Legjobb Biztosító vagyok, és én vagyok a legjobb');?>
                                </select>
                                <input type="hidden" name="biztositoID-4" value="1004"/>
                                <input type="submit" value="Megkötés" name="biztositas-4-kotes"/>
                            </div>
                        </div>

                        <div class="biztosito">
                            <input type="radio" id="biztosito-5" name="biztosito">
                            <label for="biztosito-5">Jó Biztosító</label>

                            <div class="biztositas-5">
                                <label for="kategoria-5">Kategória:</label>
                                <select id="kategoria-5" name="kategoria-5" class="require-if-active" data-require-pair="#biztosito">
                                    <option disabled selected>Nincs kiválasztva</option>
                                    <?php biztositasokListazas('Hello, én a Jó Biztosító vagyok, és nagyon jó vagyok');?>
                                </select>
                                <input type="hidden" name="biztositoID-5" value="1005"/>
                                <input type="submit" value="Megkötés" name="biztositas-5-kotes"/>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
</body>
</html>
