<?php

function menuGeneralas(string $aktualisOldal) {
    session_status() === PHP_SESSION_ACTIVE || session_start();
    echo "<nav><div class='menu'><ul>" .
        "<li" . ($aktualisOldal === "index" ? ' class=active' : "") . ">" .
        "<a href='index.php'>Kezdőlap</a>" .
        "</li>" .
        "<li" . ($aktualisOldal === "jegyfoglalas" ? ' class=active' : "") . ">" .
        "<a href='jegyfoglalas.php'>Jegyfoglalás</a>" .
        "</li>" .
        "<li" . ($aktualisOldal === "profil" ? ' class=active' : "") . ">" .
        (isset($_SESSION['user']) === true ? '<li><a href="profil.php">Profil</a></li>' : '<li><a href="bejelentkezes.php">Bejelentkezés/Regisztráció</a></li>') .
        "</li>" .
        (isset($_SESSION['user']) === true ? '<li><a href="logout.php">Kijelentkezés</a></li>' : '') .
        (isset($_SESSION['user']) === true && $_SESSION['user']['felhasznalonev'] === 'admin' ? "<li" . ($aktualisOldal === "admin" ? ' class=active' : "") . '><a href="admin_statisztika.php">Admin</a></li>' : '') .
        "</ul></div></nav>";
}

function kiindulasiHelyListazas(int $melyik) {
    include_once('common/connection.php');
    $utazasiiroda = csatlakozas();

    if($melyik === 1) {
        $honnan1 = oci_parse($utazasiiroda, "SELECT DISTINCT(HONNAN) FROM JARAT");
        oci_execute($honnan1);

        while ($current_row = oci_fetch_array($honnan1, OCI_ASSOC + OCI_RETURN_NULLS)) {
            echo '<option value="'. $current_row["HONNAN"] . '"' . '>' . $current_row["HONNAN"] . '</option>';
        }
    }

    if(isset($honnan1) && is_resource($honnan1)) {
        oci_free_statement($honnan1);
    }

    csatlakozas_zarasa($utazasiiroda);
}

function erkezesiHelyListazas(int $melyik) {
    include_once('common/connection.php');
    $utazasiiroda = csatlakozas();

    if($melyik === 1) {
        $hova1 = oci_parse($utazasiiroda, "SELECT DISTINCT(HOVA) FROM JARAT");
        oci_execute($hova1);

        while ($current_row = oci_fetch_array($hova1, OCI_ASSOC + OCI_RETURN_NULLS)) {
            echo '<option value="'. $current_row["HOVA"] . '"' . '>' . $current_row["HOVA"] . '</option>';
        }
    }

    if(isset($hova1) && is_resource($hova1)) {
        oci_free_statement($hova1);
    }

    csatlakozas_zarasa($utazasiiroda);
}

function legitarsasagListazas() {
    include_once('common/connection.php');
    $utazasiiroda = csatlakozas();

    $legitarsasag = oci_parse($utazasiiroda, "SELECT NEVE FROM LEGITARSASAG");
    oci_execute($legitarsasag);

    while ($current_row = oci_fetch_array($legitarsasag, OCI_ASSOC + OCI_RETURN_NULLS)) {
        echo '<option value="'. $current_row["NEVE"] . '"' . '>' . $current_row["NEVE"] . '</option>';
    }

    if(isset($legitarsasag) && is_resource($legitarsasag)) {
        oci_free_statement($legitarsasag);
    }

    csatlakozas_zarasa($utazasiiroda);
}

function ertekel($felhasznalonev, $legitarsasag_id, $ertekeles) {
    include_once('common/connection.php');
    $utazasiiroda = csatlakozas();

    $ertekeles = oci_parse($utazasiiroda, "INSERT INTO ERTEKEL VALUES ('$felhasznalonev', '$legitarsasag_id', '$ertekeles')") or die ('Hibás utasítás!');
    oci_execute($ertekeles);
    header("Location: profil.php?ertekeles=true");

    if(isset($ertekeles) && is_resource($ertekeles)) {
        oci_free_statement($ertekeles);
    }

    csatlakozas_zarasa($utazasiiroda);
}

function kereses($kiindulasiHely, $erkezesiHely, $datum, $legitarsasag, $melyik) {
    if($melyik === 'egy') {
        $kiindulasiHely = empty($kiindulasiHely) === true ? '%' : $kiindulasiHely;
        $erkezesiHely = empty($erkezesiHely) === true ? '%' : $erkezesiHely;
        $datum = empty($datum) === true ? '%' : $datum;
        $legitarsasag = empty($legitarsasag) === true ? '%' : $legitarsasag;

        $egyiranyu_kereses = "SELECT JARATSZAM, HONNAN, HOVA, TO_CHAR(INDULAS,'YYYY.MM.DD. HH:MI') AS INDULAS, TO_CHAR(ERKEZES,'YYYY.MM.DD. HH:MI') AS ERKEZES, LEGITARSASAG.NEVE, AR, TOBBMEGALLOS, SZABAD_HELY FROM JARAT, LEGITARSASAG 
                                        WHERE JARAT.LEGITARSASAG=LEGITARSASAG.NEVE AND TOBBMEGALLOS=0 AND SZABAD_HELY > 0 AND HONNAN LIKE '$kiindulasiHely' 
                                        AND HOVA LIKE '$erkezesiHely' AND INDULAS LIKE '$datum' AND LEGITARSASAG.NEVE LIKE '$legitarsasag'";

        return $egyiranyu_kereses;
    } else if ($melyik === 'tobb') {
        $kiindulasiHely = empty($kiindulasiHely) === true ? '%' : $kiindulasiHely;
        $erkezesiHely = empty($erkezesiHely) === true ? '%' : $erkezesiHely;
        $datum = empty($datum) === true ? '%' : $datum;
        $legitarsasag = empty($legitarsasag) === true ? '%' : $legitarsasag;

        $tobbmegallos_kereses = "SELECT JARATSZAM, HONNAN, HOVA, TO_CHAR(INDULAS,'YYYY.MM.DD. HH:MI') AS INDULAS, TO_CHAR(ERKEZES,'YYYY.MM.DD. HH:MI') AS ERKEZES, LEGITARSASAG.NEVE, AR, TOBBMEGALLOS FROM JARAT, LEGITARSASAG 
                                        WHERE JARAT.LEGITARSASAG=LEGITARSASAG.NEVE AND TOBBMEGALLOS NOT LIKE 0 AND SZABAD_HELY > 0 AND HONNAN LIKE '$kiindulasiHely' 
                                        AND HOVA LIKE '$erkezesiHely' AND INDULAS LIKE '$datum' AND LEGITARSASAG.NEVE LIKE '$legitarsasag'";

        return $tobbmegallos_kereses;
    }
}

function utasAdatok($tipus, $utas_szam) {
    include_once('common/connection.php');
    $utazasiiroda = csatlakozas();

    for($i = 1; $i <= $utas_szam; ++$i) {
        echo '<fieldset>';
        echo '<legend>' . $i . ". Utas adatai ($tipus)" . '</legend>';
        echo "<select required>";
        echo '<option disabled selected value>--Nem</option>';
        echo '<option>Férfi</option>';
        echo '<option>Nő</option>';
        echo '</select> <br/>';
        echo '<label class="required-label">Vezetéknév:<input type="text" placeholder="Vezetéknév" required></label>';
        echo '<label class="required-label">Keresztnév:</label><input type="text" placeholder="Keresztnév" required> <br/>';
        echo '<label class="required-label">Születési dátum:</label><input type="date" required>';
        echo '<tr>';
        echo '<th>';
        echo '<label><input type="checkbox" name="etkezes-' . $tipus. '-' . $i . '" value="etkezes" checked>Étkezés</label>';
        echo '</th>';
        echo '<td>+ 5,720 Ft</td>';
        echo '</tr>';
        echo '</table>';
        echo '</fieldset>';
    }

    csatlakozas_zarasa($utazasiiroda);
}

function repulojegyAra($jaratszam): int {
    include_once('common/connection.php');
    $utazasiiroda = csatlakozas();

    $repjegy_ara='0';               // az ár kezdőértéke 0
    $repjegy_ar_lekerdezes = oci_parse($utazasiiroda, "SELECT AR FROM JARAT WHERE JARATSZAM = '$jaratszam'") or die ('Hibás utasítás!');
    oci_execute($repjegy_ar_lekerdezes);
    while($current_row = oci_fetch_array($repjegy_ar_lekerdezes, OCI_ASSOC + OCI_RETURN_NULLS)) {
        $repjegy_ara = $current_row["AR"];
    }

    oci_free_statement($repjegy_ar_lekerdezes);
    csatlakozas_zarasa($utazasiiroda);
    return $repjegy_ara;
}

function foglalasokListazasa($felhasznalonev) {
    include_once('common/connection.php');
    $utazasiiroda = csatlakozas();

    $foglalt = oci_parse($utazasiiroda, "SELECT JARAT.HONNAN, JARAT.HOVA, TO_CHAR(JARAT.INDULAS,'YYYY.MM.DD. HH:MI') AS INDULAS, JEGY.AR FROM JEGY, JARAT WHERE JEGY.FELHASZNALONEV = '$felhasznalonev' AND JEGY.JARATSZAM=JARAT.JARATSZAM") or die ('Hibás utasítás!');
    oci_execute($foglalt);
    oci_fetch($foglalt);
    if(oci_num_rows($foglalt) === 0) {
        echo '<p>' . 'Még egyetlen foglalás sem történt!' . '</p>';
    } else {
        echo '<table id="foglalas-adatok-table">
                    <tr>
                        <th>Kiindulási hely</th>
                        <th>Érkezési hely</th>
                        <th>Indulás</th>
                        <th>Összesített ár</th>
                    </tr>';
        while ($current_row = oci_fetch_array($foglalt, OCI_ASSOC + OCI_RETURN_NULLS)) {
                echo '<tr>';
                echo '<td>' . $current_row['HONNAN'] . '</td>';
                echo '<td>' . $current_row['HOVA'] . '</td>';
                echo '<td>' . $current_row['INDULAS'] . '</td>';
                echo '<td>' . number_format($current_row['AR']) . ' Ft' .  '</td>';
                echo '</tr>';
        }
        echo '</table>';
    }

//    if(isset($foglalas) && is_resource($foglalas)) {
//        mysqli_free_result($foglalas);
//    }

    csatlakozas_zarasa($utazasiiroda);
}

function ertekelesekListazasa($felhasznalonev) {
    include_once('common/connection.php');
    $utazasiiroda = csatlakozas();

    $ertekeles = oci_parse($utazasiiroda, "SELECT LEGITARSASAG.NEVE, ERTEKELES FROM LEGITARSASAG, ERTEKEL WHERE ERTEKEL.FELHASZNALONEV = '$felhasznalonev' AND LEGITARSASAG.NEVE=ERTEKEL.LEGITARSASAG");
    oci_execute($ertekeles);
    oci_fetch($ertekeles);
    if(oci_num_rows($ertekeles) === 0) {
        echo '<p>' . 'Még egyetlen értékelés sem történt!' . '</p>';
    } else {
        echo '<table id="ertekeles-adatok-table">
                <tr>
                    <th>Légitársaság</th>
                    <th>Összesített értékelés pontszáma</th>
                </tr>';
        while ($current_row = oci_fetch_array($ertekeles, OCI_ASSOC + OCI_RETURN_NULLS)) {
            echo '<tr>';
            echo '<td>' . $current_row['NEVE'] . '</td>';
            echo '<td>' . $current_row['ERTEKELES'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }

//    if(isset($ertekeles) && is_resource($ertekeles)) {
//        mysqli_free_result($ertekeles);
//    }

    csatlakozas_zarasa($utazasiiroda);
}

function alMenuGeneralas(string $aktualisOldal) {
    echo "<ul>" .
        "<li>" .
        "<a href='admin_statisztika.php'" . ($aktualisOldal === "statisztika" ? ' class=active' : "") . ">Statisztika</a>" .
        "</li>" .
        "<li>" .
        "<a href='admin_rekordok.php'" . ($aktualisOldal === "rekordok" ? ' class=active' : "") . ">Rekordok</a>" .
        "</li>" .
        "<li>" .
        "<a href='admin_naplozas.php'" . ($aktualisOldal === "naplozas" ? ' class=active' : "") . ">Naplózás</a>" .
        "</li>" .
        "</ul>";
}