<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Irinyi utazási iroda</title>
    <link rel="icon" href="img/negyedikicon.png"/>
    <link rel="stylesheet" href="css/style.css"/>
    <link rel="stylesheet" href="css/jegyfoglalas.css"/>
    <script src="js/repjegy-tipus.js"></script>
</head>
<body>
<?php
    include_once("common/header.php");
    include_once("common/fuggvenyek.php");
    menuGeneralas('jegyfoglalas');
?>

<main>
    <div class="container">
        <div class="kereso">
            <?php
            if(isset($_GET["hibas"])) {
                echo "<div class='hiba'>Ismételje meg a keresést!</div>";
            } else if(isset($_GET["noresult"])) {
                echo "<div class='hiba'>A beírt adatok szerint nem található hely. Kérjük módosítsa a feltételeket!</div>";
            } else if(isset($_GET["foglalas"])) {
                echo "<div class='siker'>Sikeres foglalás!</div>";
                include_once('common/connection.php');
                $utazasiiroda = csatlakozas();
                $jegy = oci_parse($utazasiiroda, "SELECT * FROM JEGY");
                oci_execute($jegy);

                echo '<table>';
                while ($row = oci_fetch_array($jegy, OCI_ASSOC + OCI_RETURN_NULLS)) {
                    echo '<tr>';
                    foreach ($row as $item) {
                        echo '<td>' . $item . '</td>';
                    }
                    echo '</tr>';
                }
                echo '</table>';
            }

            ?>
            <h1>Válassza ki az Önnek megfelelő járatot!</h1>
            <form action="jegyfoglalas_szures.php" method="POST">
                <div class="repjegy-tipus">
                    <input type="radio" id="egyiranyu" name="repjegy-tipus" value="Egyirányú">
                    <label for="egyiranyu">Egyirányú</label>

                    <div class="egyiranyu-repjegy">
                        <label for="kiindulasi-hely-egy">Kiindulási hely:</label>
                        <select id="kiindulasi-hely-egy" name="kiindulasi-hely-egy" class="require-if-active" data-require-pair="#repjegy-tipus">
                            <option value="" disabled selected>Nincs preferencia</option>
                            <?php kiindulasiHelyListazas();?>
                        </select>

                        <label for="erkezesi-hely-egy">Érkezési hely:</label>
                        <select id="erkezesi-hely-egy" name="erkezesi-hely-egy" class="require-if-active" data-require-pair="#repjegy-tipus">
                            <option value="" disabled selected>Nincs preferencia</option>
                            <?php erkezesiHelyListazas(); ?>
                        </select>

                        <label for="datum-egy">Dátum:</label>
                        <input type="datetime-local" id="datum-egy" name="datum-egy" class="require-if-active" data-require-pair="#repjegy-tipus">

                        <label for="legitarsasag-egy">Légitársaság:</label>
                        <select id="legitarsasag-egy" name="legitarsasag-egy">
                            <option value="" disabled selected>Nincs preferencia</option>
                            <?php legitarsasagListazas(); ?>
                        </select>

                        <label for="felnott-egy">Felnőtt:</label>
                        <input type="number" id="felnott-egy" name="felnott-egy" class="require-if-active" data-require-pair="#repjegy-tipus" min="1" max="5" step="1" value="1" placeholder="Felnőttek száma">

                        <label for="gyermek-egy">Gyermek:</label>
                        <input type="number" id="gyermek-egy" name="gyermek-egy" class="require-if-active" data-require-pair="#repjegy-tipus" min="0" max="5" step="1" value="0" placeholder="Gyermekek száma">

                        <input type="submit" value="Keresés" name="egyiranyu-kereses" formaction="jegyfoglalas_szures.php"/>
                    </div>
                </div>

                <div class="repjegy-tipus">
                    <input type="radio" id="tobb-megallos" name="repjegy-tipus">
                    <label for="tobb-megallos">Több megállós</label>

                    <div class="tobb-megallos-repjegy">
                        <label for="kiindulasi-hely-tobb-1">Kiindulási hely:</label>
                        <select id="kiindulasi-hely-tobb-1" name="kiindulasi-hely-tobb-1" class="require-if-active" data-require-pair="#repjegy-tipus">
                            <option value="" disabled selected>Nincs preferencia</option>
                            <?php kiindulasiHelyListazas(); ?>
                        </select>

                        <label for="erkezesi-hely-tobb-1">Érkezési hely:</label>
                        <select id="erkezesi-hely-tobb-1" name="erkezesi-hely-tobb-1" class="require-if-active" data-require-pair="#repjegy-tipus">
                            <option value="" disabled selected>Nincs preferencia</option>
                            <?php erkezesiHelyListazas(); ?>
                        </select>

                        <label for="datum-tobb">Dátum:</label>
                        <input type="datetime-local" id="datum-tobb" name="datum-tobb" class="require-if-active" data-require-pair="#repjegy-tipus">

                        <label for="legitarsasag-tobb">Légitársaság:</label>
                        <select id="legitarsasag-tobb" name="legitarsasag-tobb">
                            <option value="" disabled selected>Nincs preferencia</option>
                            <?php legitarsasagListazas(); ?>
                        </select>

                        <label for="felnott-tobb">Felnőtt:</label>
                        <input type="number" id="felnott-tobb" name="felnott-tobb" class="require-if-active" data-require-pair="#repjegy-tipus" min="1" max="5" step="1" value="1" placeholder="Felnőttek száma">

                        <label for="gyermek-tobb">Gyermek:</label>
                        <input type="number" id="gyermek-tobb" name="gyermek-tobb" class="require-if-active" data-require-pair="#repjegy-tipus" min="0" max="5" step="1" value="0" placeholder="Gyermekek száma">

                        <input type="submit" value="Keresés" name="tobbmegallos-kereses" formaction="jegyfoglalas_szures.php">
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>
<?php
    include_once("common/footer.php");
?>
</body>
</html>