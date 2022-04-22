<?php
session_status() === PHP_SESSION_ACTIVE || session_start();

if(isset($_POST['egyiranyu-kereses'])) {
    $felnott = $_POST['felnott-egy'];
    $gyermek = $_POST['gyermek-egy'];
} else if(isset($_POST['tobbmegallos-kereses'])) {
    $felnott = $_POST['felnott-tobb'];
    $gyermek = $_POST['gyermek-tobb'];
}

if(!isset($_POST['felnott-egy']) && !isset($_POST['gyermek-egy']) && !isset($_POST['felnott-tobb']) && !isset($_POST['gyermek-tobb'])) {
    header("Location: jegyfoglalas.php?hibas=true");
}

include_once("common/fuggvenyek.php");
include_once("common/connection.php");
$utazasiiroda = csatlakozas();

if(isset($_POST['egyiranyu-kereses'])) {
    $egyiranyu_result = oci_parse($utazasiiroda, kereses(
        ($_POST['kiindulasi-hely-egy'] ?? ""), ($_POST['erkezesi-hely-egy'] ??  ""),
        ($_POST['datum-egy'] ?? ""), ($_POST['legitarsasag-egy'] ?? ""), 'egy'));
    oci_execute($egyiranyu_result);

//    if(oci_num_rows($egyiranyu_result) < 1) {
//        header("Location: jegyfoglalas.php?noresult=true");
//    }
}

if(isset($_POST['tobbmegallos-kereses'])) {
    $tobbmegallos_result = oci_parse($utazasiiroda, kereses(
                            ($_POST['kiindulasi-hely-tobb-1'] ?? ""),($_POST['erkezesi-hely-tobb-1'] ?? ""),
                            ($_POST['datum-tobb'] ?? ""), ($_POST['legitarsasag-tobb'] ?? ""), 'tobb'));
    oci_execute($tobbmegallos_result);

//    if(oci_num_rows($tobbmegallos_result) < 1) {
//        header("Location: jegyfoglalas.php?noresult=true");
//    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Irinyi utazási iroda</title>
    <link rel="icon" href="img/negyedikicon.png"/>
    <link rel="stylesheet" href="css/style.css"/>
    <link rel="stylesheet" href="css/szurt-jaratok.css"/>
</head>
<body>
<?php
    include_once("common/header.php");
    include_once("common/fuggvenyek.php");
    menuGeneralas('jegyfoglalas');
?>
<main>
    <div class="container">
        <div class="szurt">
            <table>
                <thead>
                    <tr>
                        <th id="jaratszam">Járat</th>
                        <th id="kiindulopont">Kiindulópont</th>
                        <th id="uticel">Úticél</th>
                        <th id="indulasi-ido">Indulási idő</th>
                        <th id="erkezesi-ido">Érkezési idő</th>
                        <th id="legitarsasag">Légitársaság</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    while (isset($_POST['egyiranyu-kereses']) && ($current_row = oci_fetch_array($egyiranyu_result, OCI_ASSOC + OCI_RETURN_NULLS))) {
                            echo '<tr class="egy-jarat">';
                                echo '<td headers="jaratszam">' . $current_row["JARATSZAM"] . '</td>';
                                echo '<td headers="kiindulopont">' . $current_row["HONNAN"] . '</td>';
                                echo '<td headers="uticel">' . $current_row["HOVA"] . '</td>';
                                echo '<td headers="indulasi-ido">' . $current_row["INDULAS"] . '</td>';
                                echo '<td headers="erkezesi-ido">' . $current_row["ERKEZES"] . '</td>';
                                echo '<td headers="legitarsasag">' . $current_row["NEVE"] . '</td>';
                                echo '<td><form action="jegyfoglalas_foglalas.php" method="POST">';
                                echo '<input type="hidden" name="post-jaratszam-egy" value=' . $current_row["JARATSZAM"] . '>';
                                echo '<input type="hidden" name="post-felnott-egy" value=' . $felnott . '>';
                                echo '<input type="hidden" name="post-gyermek-egy" value=' . $gyermek . '>';
                                echo '<input type="submit" class="foglalas-gomb" name="egyiranyu-kereses-gomb" value="Foglalás">';
                                echo '</form></td>';
                            echo '</tr>';
                    }
                    while (isset($_POST['tobbmegallos-kereses']) && ($current_row = oci_fetch_array($tobbmegallos_result, OCI_ASSOC + OCI_RETURN_NULLS))) {
                        echo '<tr class="egy-jarat">';
                            echo '<td headers="jaratszam">' . $current_row["JARATSZAM"] . '</td>';
                            echo '<td headers="kiindulopont">' . $current_row["HONNAN"] . '</td>';
                            echo '<td headers="uticel">' . $current_row["HOVA"] . '</td>';
                            echo '<td headers="indulasi-ido">' . $current_row["INDULAS"] . '</td>';
                            echo '<td headers="erkezesi-ido">' . $current_row["ERKEZES"] . '</td>';
                            echo '<td headers="legitarsasag">' . $current_row["NEVE"] . '</td>';
                            echo '<td><form action="jegyfoglalas_foglalas.php" method="POST">';
                            echo '<input type="hidden" name="post-jaratszam-tobb" value=' . $current_row["JARATSZAM"] . '>';
                            echo '<input type="hidden" name="post-felnott-tobb" value=' . $felnott . '>';
                            echo '<input type="hidden" name="post-gyermek-tobb" value=' . $gyermek . '>';
                            echo '<input type="submit" class="foglalas-gomb" name="tobbmegallos-kereses-gomb" value="Foglalás">';
                            echo '</form></td>';
                        echo '</tr>';
                    }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?php
    include_once("common/footer.php");
?>
</body>
</html>
<?php
    if(isset($egyiranyu_result) && is_resource($egyiranyu_result)) {
        oci_free_statement($egyiranyu_result);
    }

    if(isset($tobbmegallos_result) && is_resource($tobbmegallos_result)) {
        oci_free_statement($tobbmegallos_result);
    }

    csatlakozas_zarasa($utazasiiroda);
?>