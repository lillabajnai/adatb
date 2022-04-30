<?php
session_status() === PHP_SESSION_ACTIVE || session_start();
if(!isset($_SESSION["user"]) || empty($_SESSION["user"] || !($_SESSION["user"]["felhasznalonev"]==='admin'))){
    header("Location: index.php");
    exit();
}

include_once('common/connection.php');
$utazasiiroda = csatlakozas();

?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Irinyi utazási iroda</title>
    <link rel="icon" href="img/negyedikicon.png"/>
    <link rel="stylesheet" href="css/style.css"/>
    <link rel="stylesheet" href="css/admin.css"/>
</head>
<body>
<?php
include_once "common/header.php";
include_once "common/fuggvenyek.php";
menuGeneralas('admin');
?>
<main>
    <div class="container">
        <?php alMenuGeneralas('statisztika'); ?>
        <div class="statisztika">
            <ol style="list-style: decimal inside;">
            <table>
                <tr>
                    <td style="text-align: left; display: list-item;">Legrövidebb úttal rendelkező jegy (Gyerek jegy) (50000 alatt) (Szegedről New Yorkba):</td>
                    <td><?php
                        $stat = oci_parse($utazasiiroda,"SELECT JEGY.ID, JEGY.AR, JEGY.FELHASZNALONEV, JEGY.JARATSZAM, JARAT.ETKEZES FROM JEGY, JARAT
                                                                    WHERE JARAT.JARATSZAM = JEGY.JARATSZAM AND JEGY.JARATSZAM = 
                                                                         (SELECT JARATSZAM FROM JARAT
                                                                         WHERE (ERKEZES-INDULAS) = 
                                                                              (SELECT (ERKEZES-INDULAS) AS IDOTARTAM FROM JARAT
                                                                              WHERE HONNAN = 'Szeged' AND HOVA = 'New York'
                                                                              ORDER BY JARATSZAM
                                                                              FETCH FIRST 1 ROW ONLY) 
                                                                         AND HONNAN = 'Szeged' AND HOVA = 'New York'
                                                                    order by jaratszam FETCH FIRST 1 ROW ONLY) AND JEGY.AR < 50000");
                        oci_execute($stat) or die ('Hiba');
                        while ($current_row = oci_fetch_array($stat, OCI_ASSOC + OCI_RETURN_NULLS)) {
                            echo $current_row['ID'] . ' - ';
                            echo $current_row['AR'] . ' - ';
                            echo $current_row['FELHASZNALONEV'] . ' - ';
                            echo $current_row['JARATSZAM'] . ' - ';
                            echo $current_row['ETKEZES'] . '<br/>';
                        }
                    ?></td>
                </tr>
                <tr>
                    <td style="text-align: left; display: list-item;">Átlagos poggyászbiztosítás ár:</td>
                    <td><?php
                        $stat = oci_parse($utazasiiroda,"SELECT ROUND(AVG(BIZTOSITAS.AR)) AS ATLAG_AR FROM BIZTOSITAS, BIZTOSITAS_KATEGORIAK WHERE BIZTOSITAS_KATEGORIAK.ID = BIZTOSITAS.ID AND KATEGORIA = 'poggyászbiztosítás'");
                        oci_execute($stat) or die ('Hiba');
                        while ($current_row = oci_fetch_array($stat, OCI_ASSOC + OCI_RETURN_NULLS)) {
                            echo number_format($current_row['ATLAG_AR']) , ' Ft';
                        }
                    ?></td>
                </tr>
                <tr>
                    <td style="text-align: left; display: list-item;">Adott utas legutolsó utazása:</td>
                    <td><?php
                        $stat = oci_parse($utazasiiroda,"SELECT TO_CHAR(MAX(JARAT.INDULAS),'YYYY.MM.DD. HH:MI') AS LEGUTOLSO FROM UTAS, JEGY, JARAT
                                                                    WHERE JEGY.FELHASZNALONEV = UTAS.FELHASZNALONEV AND
                                                                    JARAT.JARATSZAM = JEGY.JARATSZAM AND
                                                                    UTAS.FELHASZNALONEV = 'Admin01'");
                        oci_execute($stat) or die ('Hiba');
                        while ($current_row = oci_fetch_array($stat, OCI_ASSOC + OCI_RETURN_NULLS)) {
                            echo $current_row['LEGUTOLSO'];
                        }
                    ?></td>
                </tr>
                <tr>
                    <td style="text-align: left; display: list-item;">Utasok megvásárolt jegyeinek a száma és összege:</td>
                    <td><?php
                        $stat = oci_parse($utazasiiroda,"SELECT UTAS.FELHASZNALONEV, COUNT(JEGY.JARATSZAM) AS JEGYEK_SZAMA, SUM(JEGY.AR) AS JEGYEK_ARA FROM UTAS, JEGY
                                                                    WHERE JEGY.FELHASZNALONEV = UTAS.FELHASZNALONEV 
                                                                    GROUP BY UTAS.FELHASZNALONEV
                                                                    ORDER BY UTAS.FELHASZNALONEV");
                        oci_execute($stat) or die ('Hiba');
                        while ($current_row = oci_fetch_array($stat, OCI_ASSOC + OCI_RETURN_NULLS)) {
                            echo $current_row['FELHASZNALONEV'] . ' - ';
                            echo $current_row['JEGYEK_SZAMA'] . ' - ';
                            echo $current_row['JEGYEK_ARA'] . '<br/>';
                        }
                    ?></td>
                </tr>
                <tr>
                    <td style="text-align: left; display: list-item;">Legtöbbet költött utas jegyei:</td>
                    <td><?php
                        $stat = oci_parse($utazasiiroda,"SELECT JEGY.ID, JEGY.AR, JEGY.JARATSZAM, JARAT.HONNAN, JARAT.HOVA, TO_CHAR(JARAT.INDULAS,'YYYY.MM.DD. HH:MI') AS INDULAS, TO_CHAR(JARAT.ERKEZES,'YYYY.MM.DD. HH:MI') AS ERKEZES, JARAT.AR FROM UTAS, JEGY, JARAT
                                                                    WHERE JEGY.FELHASZNALONEV = UTAS.FELHASZNALONEV AND
                                                                    UTAS.FELHASZNALONEV = 
                                                                        (SELECT UTAS.FELHASZNALONEV FROM UTAS, JEGY
                                                                        WHERE JEGY.FELHASZNALONEV = UTAS.FELHASZNALONEV
                                                                        GROUP BY UTAS.FELHASZNALONEV
                                                                        ORDER BY SUM(JEGY.AR) DESC
                                                                        FETCH FIRST 1 ROW ONLY) 
                                                                    AND JARAT.JARATSZAM = JEGY.JARATSZAM
                                                                    ORDER BY INDULAS");
                        oci_execute($stat) or die ('Hiba');
                        while ($current_row = oci_fetch_array($stat, OCI_ASSOC + OCI_RETURN_NULLS)) {
                            echo $current_row['ID'] . ' - ';
                            echo $current_row['AR'] . ' - ';
                            echo $current_row['JARATSZAM'] . ' - ';
                            echo $current_row['HONNAN'] . ' → ';
                            echo $current_row['HOVA'] . ' - ';
                            echo $current_row['INDULAS'] . ' → ';
                            echo $current_row['ERKEZES'] . ' - ';
                            echo $current_row['AR'] . '<br/>';
                        }
                    ?></td>
                </tr>
                <tr>
                    <td style="text-align: left; display: list-item;">Legtöbbet vásárolt biztosításkategória:</td>
                    <td><?php
                        $stat = oci_parse($utazasiiroda,"SELECT KATEGORIA, COUNT(BIZTOSITAS_KATEGORIAK.ID) AS DARAB FROM BIZTOSITAS, BIZTOSITAS_KATEGORIAK
                                                                    WHERE BIZTOSITAS.ID = BIZTOSITAS_KATEGORIAK.ID
                                                                    GROUP BY KATEGORIA
                                                                    ORDER BY DARAB DESC
                                                                    FETCH FIRST 1 ROW ONLY");
                        oci_execute($stat) or die ('Hiba');
                        while ($current_row = oci_fetch_array($stat, OCI_ASSOC + OCI_RETURN_NULLS)) {
                            echo $current_row['KATEGORIA'];
                            echo ' → ' . $current_row['DARAB'] . ' db';
                        }
                    ?></td>
                </tr>
                <tr>
                    <td style="text-align: left; display: list-item;">Adott biztosító biztosítását hányan vették meg:</td>
                    <td><?php
                        $stat = oci_parse($utazasiiroda,"SELECT BIZTOSITAS_KATEGORIAK.KATEGORIA, COUNT(BIZTOSITAS_KATEGORIAK.ID) AS DARAB FROM BIZTOSITAS_KATEGORIAK, BIZTOSITO, BIZTOSITAS
                                                                    WHERE BIZTOSITAS_KATEGORIAK.ID = BIZTOSITAS.ID AND BIZTOSITO.ID = BIZTOSITAS.BIZTOSITOID AND BIZTOSITAS.BIZTOSITOID=1001
                                                                    GROUP BY BIZTOSITAS_KATEGORIAK.KATEGORIA");
                        oci_execute($stat) or die ('Hiba');
                        while ($current_row = oci_fetch_array($stat, OCI_ASSOC + OCI_RETURN_NULLS)) {
                            echo $current_row['KATEGORIA'] . ' → ';
                            echo $current_row['DARAB'] . ' db' . '<br/>';
                        }
                    ?></td>
                </tr>
                <tr>
                    <td style="text-align: left; display: list-item;">Adott légitársaság legjobb értékelésű járata:</td>
                    <td><?php
                        $stat = oci_parse($utazasiiroda,"SELECT JARAT.LEGITARSASAG, MAX(ERTEKEL.ERTEKELES) AS LEGJOBB_ERTEKELES, JARAT.JARATSZAM FROM ERTEKEL, JARAT
                                                                    WHERE JARAT.LEGITARSASAG = ERTEKEL.LEGITARSASAG
                                                                    AND ERTEKELES >= 3 AND HONNAN = 'Szeged'
                                                                    GROUP BY JARAT.LEGITARSASAG, JARATSZAM
                                                                    FETCH FIRST 1 ROW ONLY");
                        oci_execute($stat) or die ('Hiba');
                        while ($current_row = oci_fetch_array($stat, OCI_ASSOC + OCI_RETURN_NULLS)) {
                            echo $current_row['JARATSZAM'] . ' - ';
                            echo $current_row['LEGITARSASAG'] . ' - ';
                            echo $current_row['LEGJOBB_ERTEKELES'];
                        }
                    ?></td>
                </tr>
                <tr>
                    <td style="text-align: left; display: list-item;">Adott légitársaság Szegedről induló legutolsó járata tulajdonos alapján:</td>
                    <td><?php
                        $stat = oci_parse($utazasiiroda,"SELECT JARAT.JARATSZAM, TO_CHAR(MAX(JARAT.INDULAS),'YYYY.MM.DD. HH:MI') AS INDULAS, LEGITARSASAG FROM JARAT, LEGITARSASAG
                                                                    WHERE JARAT.LEGITARSASAG = LEGITARSASAG.NEVE AND
                                                                    HONNAN = 'Szeged' AND LEGITARSASAG.TULAJDONOS = 'Grippen Gergő'
                                                                    GROUP BY JARAT.JARATSZAM, LEGITARSASAG
                                                                    FETCH FIRST 1 ROW ONLY");
                        oci_execute($stat) or die ('Hiba');
                        while ($current_row = oci_fetch_array($stat, OCI_ASSOC + OCI_RETURN_NULLS)) {
                            echo $current_row['JARATSZAM'] . ' ';
                            echo $current_row['LEGITARSASAG'] . ' ';
                            echo $current_row['INDULAS'];
                        }
                    ?></td>
                </tr>
                <tr>
                    <td style="text-align: left; display: list-item;">Szegedi telephelyű légitarsaság legtöbb szabad hellyel rendelkező járata:</td>
                    <td><?php
                        $stat = oci_parse($utazasiiroda,"SELECT JARAT.JARATSZAM, JARAT.HONNAN, JARAT.HOVA, JARAT.SZABAD_HELY FROM JARAT
                                                                    WHERE JARAT.JARATSZAM = 
                                                                        (SELECT JARAT.JARATSZAM FROM JARAT, LEGITARSASAG
                                                                        WHERE JARAT.LEGITARSASAG = LEGITARSASAG.NEVE AND TELEPHELY = 'Szeged'
                                                                        ORDER BY JARAT.SZABAD_HELY DESC
                                                                        FETCH FIRST 1 ROW ONLY)");
                        oci_execute($stat) or die ('Hiba');
                        while ($current_row = oci_fetch_array($stat, OCI_ASSOC + OCI_RETURN_NULLS)) {
                            echo $current_row['JARATSZAM'] . ' - ';
                            echo $current_row['HONNAN'] . ' → ';
                            echo $current_row['HOVA'] . ' - ';
                            echo $current_row['SZABAD_HELY'];
                        }
                    ?></td>
                </tr>
                <tr>
                    <td style="text-align: left; display: list-item;">Járatok száma légitársaságok szerint:</td>
                    <td><?php
                        $stat = oci_parse($utazasiiroda,"SELECT LEGITARSASAG.NEVE, COUNT(*) AS DARAB FROM JARAT, LEGITARSASAG
                                                                    WHERE LEGITARSASAG.NEVE = JARAT.LEGITARSASAG
                                                                    GROUP BY LEGITARSASAG.NEVE");
                        oci_execute($stat) or die ('Hiba');
                        while ($current_row = oci_fetch_array($stat, OCI_ASSOC + OCI_RETURN_NULLS)) {
                            echo $current_row['NEVE'] . ' → ';
                            echo $current_row['DARAB'] . '<br/>';
                        }
                    ?></td>
                </tr>
            </table>
            </ol>
        </div>
        </div>
</main>
</body>
</html>