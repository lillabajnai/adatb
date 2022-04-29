<?php
session_status() === PHP_SESSION_ACTIVE || session_start();

if(!isset($_SESSION["user"]) || empty($_SESSION["user"])){
    header("Location: bejelentkezes.php?hiba=true");
    exit();
}

if(isset($_POST['egyiranyu-kereses-gomb'])) {
    $egyikJaratszam=$_POST['post-jaratszam-egy'];
    $felnott=$_POST['post-felnott-egy'];
    $gyermek=$_POST['post-gyermek-egy'];
} else if(isset($_POST['tobbmegallos-kereses-gomb'])) {
    $egyikJaratszam=$_POST['post-jaratszam-tobb-egyik'];
    $masikJaratszam=$_POST['post-jaratszam-tobb-masik'];
    $felnott=$_POST['post-felnott-tobb'];
    $gyermek=$_POST['post-gyermek-tobb'];
}

if(!isset($_POST['post-felnott-egy']) && !isset($_POST['post-gyermek-egy']) && !isset($_POST['post-felnott-tobb']) && !isset($_POST['post-gyermek-tobb'])) {
    header("Location: jegyfoglalas.php?hibas=true");
}

include_once("common/connection.php");
$utazasiiroda = csatlakozas();

$jarat = oci_parse($utazasiiroda, "SELECT TO_CHAR(INDULAS,'YYYY.MM.DD. HH:MI') AS INDULAS, HONNAN, HOVA, TO_CHAR(ERKEZES,'YYYY.MM.DD. HH:MI') AS ERKEZES, TOBBMEGALLOS FROM JARAT WHERE JARATSZAM = '$egyikJaratszam'");
oci_execute($jarat);
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
        <div id="jarat-infok">
            <table>
                <caption>A járat menetrendje</caption>
                <thead>
                <tr>
                    <th id="datum">Indulási idő</th>
                    <th id="kiindulopont">Kiindulópont</th>
                    <th id="uticel">Úticél</th>
                    <th id="erkezesi-ido">Érkezési idő</th>
                </tr>
                </thead>
                <tbody>
                <?php
                    while (($current_row = oci_fetch_array($jarat, OCI_ASSOC + OCI_RETURN_NULLS))) {
                        echo '<tr class="egy-jarat">';
                        echo '<td headers="datum">' . $current_row["INDULAS"] . '</td>';
                        echo '<td headers="kiindulopont">' . $current_row["HONNAN"] . '</td>';
                        echo '<td headers="uticel">' . $current_row["HOVA"] . '</td>';
                        echo '<td headers="erkezesi-ido">' . $current_row["ERKEZES"] . '</td>';
                        echo '</tr>';
                        if($current_row['TOBBMEGALLOS'] != 0) {
                            $jarat_parja = oci_parse($utazasiiroda, "SELECT TO_CHAR(INDULAS,'YYYY.MM.DD. HH:MI') AS INDULAS, HONNAN, HOVA, TO_CHAR(ERKEZES,'YYYY.MM.DD. HH:MI') AS ERKEZES FROM JARAT WHERE JARATSZAM LIKE '$masikJaratszam'");
                            oci_execute($jarat_parja);
                            while (($current_row = oci_fetch_array($jarat_parja, OCI_ASSOC + OCI_RETURN_NULLS))) {
                                echo '<tr class="egy-jarat">';
                                echo '<td headers="indulasi-ido">' . $current_row["INDULAS"] . '</td>';
                                echo '<td headers="kiindulopont">' . $current_row["HONNAN"] . '</td>';
                                echo '<td headers="uticel">' . $current_row["HOVA"] . '</td>';
                                echo '<td headers="erkezesi-ido">' . $current_row["ERKEZES"] . '</td>';
                                echo '</tr>';
                            }
                        }
                    }
                ?>
                </tbody>
            </table>
        </div>
        <div class="jarat-adatok">
            <form method="POST" action="jegyfoglalas_megerosites.php">
                <?php
                    utasAdatok('felnőtt',$felnott,$egyikJaratszam);

                    if($gyermek > 0) {
                        utasAdatok('gyermek',$gyermek,$egyikJaratszam);
                    }
                ?>
                <fieldset id="szamlazasi-adatok">
                    <legend>Számlázási adatok</legend>
                    <select id="szamlazasi-nem" name="szamlazasi-adatok">
                        <option disabled selected>--Nem</option>
                        <option>Férfi</option>
                        <option>Nő</option>
                    </select> <br/>
                    <label for="szamlazasi-vezeteknev" class="required-label">Vezetéknév:</label>
                    <input type="text" id="szamlazasi-vezeteknev" name="szamlazasi-adatok" placeholder="Vezetéknév" required>
                    <label for="szamlazasi-keresztnev" class="required-label">Keresztnév:</label>
                    <input type="text" id="szamlazasi-keresztnev" name="szamlazasi-adatok" placeholder="Keresztnév" required> <br/>
                    <label for="orszag" class="required-label">Ország:</label>
                    <input type="text" id="orszag" name="szamlazasi-adatok" value="Magyarország" required>
                    <label for="iranyitoszam" class="required-label">Irányítószám:</label>
                    <input type="text" id="iranyitoszam" name="szamlazasi-adatok" placeholder="Irányítószám" required>
                    <label for="telepules" class="required-label">Település:</label>
                    <input type="text" id="telepules" name="szamlazasi-adatok" placeholder="Település" required>
                    <label for="cim" class="required-label">Cím:</label>
                    <input type="text" id="cim" name="szamlazasi-adatok" placeholder="Cím" required> <br/>
                    <label for="szamlakuldes-formaja">Számlaküldés formája:</label>
                    <select id="szamlakuldes-formaja" name="szamlazasi-adatok">
                        <option selected>Elektronikus úton</option>
                        <option>Papír alapon</option>
                    </select>
                </fieldset>
                <fieldset id="fizetesi-mod">
                    <legend>Fizetési mód</legend>
                    <table id="fizetes-tabla">
                        <tr>
                            <th>
                                <label for="bankkartyas-fizetes">
                                    <input type="radio" id="bankkartyas-fizetes" name="fizetesi-mod" checked>
                                    Bankkártyás fizetés
                                </label>
                            </th>
                            <td>214 Ft</td>
                        </tr>
                    </table>
                </fieldset>
                <?php
                    echo '<input type="hidden" name="post-jaratszam-egyik" value=' . $egyikJaratszam . '>';
                    echo isset($_POST['post-jaratszam-tobb-egyik']) ? '<input type="hidden" name="post-jaratszam-masik" value=' . $masikJaratszam . '>' : '';
                    echo '<input type="hidden" name="post-felnott" value=' . $felnott . '>';
                    echo '<input type="hidden" name="post-gyermek" value=' . $gyermek . '>';
                ?>
                <input type="submit" name="megerosites" value="Megerősítem">
            </form>
        </div>
    </div>
</main>
<?php
    include_once("common/footer.php");
?>
</body>
</html>
<?php
    if(isset($jarat) && is_resource($jarat)) {
        oci_free_statement($jarat);
    }

    csatlakozas_zarasa($utazasiiroda);
?>