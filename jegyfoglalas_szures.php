<?php
session_status() === PHP_SESSION_ACTIVE || session_start();

if(!isset($_POST['felnott-egy']) && !isset($_POST['gyermek-egy']) && !isset($_POST['felnott-tobb']) && !isset($_POST['gyermek-tobb'])) {
    header("Location: jegyfoglalas.php?hibas=true");
}

include_once("common/fuggvenyek.php");
include_once("common/connection.php");
$utazasiiroda = csatlakozas();

if(isset($_POST['egyiranyu-kereses'])) {
    $felnott = $_POST['felnott-egy'];
    $gyermek = $_POST['gyermek-egy'];

    $egyiranyu_result = oci_parse($utazasiiroda, kereses(
        ($_POST['kiindulasi-hely-egy'] ?? ""), ($_POST['erkezesi-hely-egy'] ??  ""),
        ($_POST['datum-egy'] ?? ""), ($_POST['legitarsasag-egy'] ?? ""), $felnott+$gyermek, 'egy'));
    oci_execute($egyiranyu_result);

    if(oci_fetch_all($egyiranyu_result, $res) === 0) {
        header("Location: jegyfoglalas.php?noresult=true");
    } else {
        oci_execute($egyiranyu_result);
    }
} else if(isset($_POST['tobbmegallos-kereses'])) {
    $felnott = $_POST['felnott-tobb'];
    $gyermek = $_POST['gyermek-tobb'];

    $tobbmegallos_result = oci_parse($utazasiiroda, kereses(
        ($_POST['kiindulasi-hely-tobb-1'] ?? ""),($_POST['erkezesi-hely-tobb-1'] ?? ""),
        ($_POST['datum-tobb'] ?? ""), ($_POST['legitarsasag-tobb'] ?? ""),$felnott+$gyermek, 'tobb'));
    oci_execute($tobbmegallos_result);

//    if(oci_fetch_all($tobbmegallos_result, $res) === 0) {
//        header("Location: jegyfoglalas.php?noresult=true");
//    }
//    else {
        oci_execute($tobbmegallos_result);
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
                        <th id="ar">Ár (HUF)</th>
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
                                echo '<td headers="ar">' . number_format($current_row["AR"]) . '</td>';
                                echo '<td><form action="jegyfoglalas_foglalas.php" method="POST">';
                                echo '<input type="hidden" name="post-jaratszam-egy" value=' . $current_row["JARATSZAM"] . '>';
                                echo '<input type="hidden" name="post-felnott-egy" value=' . $felnott . '>';
                                echo '<input type="hidden" name="post-gyermek-egy" value=' . $gyermek . '>';
                                echo '<input type="submit" class="foglalas-gomb" name="egyiranyu-kereses-gomb" value="Foglalás">';
                                echo '</form></td>';
                            echo '</tr>';
                    }
                    while (isset($_POST['tobbmegallos-kereses']) && ($current_row = oci_fetch_array($tobbmegallos_result, OCI_ASSOC + OCI_RETURN_NULLS))) {
                        if($current_row['ROWNUM'] % 2 === 0 && $current_row["EGYIKJARATSZAM"] !== $current_row["MASIKJARATSZAM"]) {
                            echo '<tr class="egy-jarat">';
                                echo '<td headers="jaratszam">' . $current_row["EGYIKJARATSZAM"] . '</td>';
                                echo '<td headers="kiindulopont">' . $current_row["EGYIKHONNAN"] . '</td>';
                                echo '<td headers="uticel">' . $current_row["EGYIKHOVA"] . '</td>';
                                echo '<td headers="indulasi-ido">' . $current_row["EGYIKINDULAS"] . '</td>';
                                echo '<td headers="erkezesi-ido">' . $current_row["EGYIKERKEZES"] . '</td>';
                                echo '<td headers="legitarsasag">' . $current_row["EGYIKLEGITARSASAG"] . '</td>';
                                echo '<td headers="ar">' . number_format($current_row["EGYIKAR"]) . '</td>';
                                echo '<td></td>';
                            echo '</tr>';
                            echo '<tr class="egy-jarat">';
                                echo '<td headers="jaratszam">' . $current_row["MASIKJARATSZAM"] . '</td>';
                                echo '<td headers="kiindulopont">' . $current_row["MASIKHONNAN"] . '</td>';
                                echo '<td headers="uticel">' . $current_row["MASIKHOVA"] . '</td>';
                                echo '<td headers="indulasi-ido">' . $current_row["MASIKINDULAS"] . '</td>';
                                echo '<td headers="erkezesi-ido">' . $current_row["MASIKERKEZES"] . '</td>';
                                echo '<td headers="legitarsasag">' . $current_row["MASIKLEGITARSASAG"] . '</td>';
                                echo '<td headers="ar">' . number_format($current_row["MASIKAR"]) . '</td>';
                                echo '<td><form action="jegyfoglalas_foglalas.php" method="POST">';
                                echo '<input type="hidden" name="post-jaratszam-tobb-egyik" value=' . $current_row["EGYIKJARATSZAM"] . '>';
                                echo '<input type="hidden" name="post-jaratszam-tobb-masik" value=' . $current_row["MASIKJARATSZAM"] . '>';
                                echo '<input type="hidden" name="post-felnott-tobb" value=' . $felnott . '>';
                                echo '<input type="hidden" name="post-gyermek-tobb" value=' . $gyermek . '>';
                                echo '<input type="submit" class="foglalas-gomb" name="tobbmegallos-kereses-gomb" value="Foglalás">';
                                echo '</form></td>';
                            echo '</tr>';
                        }
                    }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
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