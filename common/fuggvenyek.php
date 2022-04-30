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
        (isset($_SESSION['user']) === true ? '<a href="profil.php">Profil</a>' : '<a href="bejelentkezes.php">Bejelentkezés/Regisztráció</a>') .
        "</li>" .
        (isset($_SESSION['user']) === true ? '<li><a href="kijelentkezes.php">Kijelentkezés</a></li>' : '') .
        (isset($_SESSION['user']) === true && $_SESSION['user']['felhasznalonev'] === 'admin' ? "<li" . ($aktualisOldal === "admin" ? ' class=active' : "") . '><a href="admin_statisztika.php">Admin</a></li>' : '') .
        "</ul></div></nav>";
}

function kiindulasiHelyListazas() {
    include_once('common/connection.php');
    $utazasiiroda = csatlakozas();

    $honnan = oci_parse($utazasiiroda, "SELECT DISTINCT(HONNAN) FROM JARAT");
    oci_execute($honnan);

    while ($current_row = oci_fetch_array($honnan, OCI_ASSOC + OCI_RETURN_NULLS)) {
        echo '<option value="'. $current_row["HONNAN"] . '"' . '>' . $current_row["HONNAN"] . '</option>';
    }

    if(isset($honnan) && is_resource($honnan)) {
        oci_free_statement($honnan);
    }

    csatlakozas_zarasa($utazasiiroda);
}

function erkezesiHelyListazas() {
    include_once('common/connection.php');
    $utazasiiroda = csatlakozas();

    $hova = oci_parse($utazasiiroda, "SELECT DISTINCT(HOVA) FROM JARAT");
    oci_execute($hova);

    while ($current_row = oci_fetch_array($hova, OCI_ASSOC + OCI_RETURN_NULLS)) {
        echo '<option value="'. $current_row["HOVA"] . '"' . '>' . $current_row["HOVA"] . '</option>';
    }

    if(isset($hova) && is_resource($hova)) {
        oci_free_statement($hova);
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

function kereses($kiindulasiHely, $erkezesiHely, $datum, $legitarsasag, $utasSzam, $melyik) {
    if($melyik === 'egy') {
        $kiindulasiHely = empty($kiindulasiHely) === true ? '%' : $kiindulasiHely;
        $erkezesiHely = empty($erkezesiHely) === true ? '%' : $erkezesiHely;
        $datum = empty($datum) === true ? '%' : $datum;
        $legitarsasag = empty($legitarsasag) === true ? '%' : $legitarsasag;

        $egyiranyu_kereses = "SELECT JARATSZAM, HONNAN, HOVA, TO_CHAR(INDULAS,'YYYY.MM.DD. HH:MI') AS INDULAS, TO_CHAR(ERKEZES,'YYYY.MM.DD. HH:MI') AS ERKEZES, LEGITARSASAG.NEVE, AR, TOBBMEGALLOS, SZABAD_HELY FROM JARAT, LEGITARSASAG 
                                        WHERE JARAT.LEGITARSASAG=LEGITARSASAG.NEVE AND TOBBMEGALLOS = 0 AND SZABAD_HELY >= '$utasSzam' AND HONNAN LIKE '$kiindulasiHely' 
                                        AND HOVA LIKE '$erkezesiHely' AND INDULAS LIKE '$datum' AND LEGITARSASAG.NEVE LIKE '$legitarsasag'";

        return $egyiranyu_kereses;
    } else if ($melyik === 'tobb') {
        $kiindulasiHely = empty($kiindulasiHely) === true ? '%' : $kiindulasiHely;
        $erkezesiHely = empty($erkezesiHely) === true ? '%' : $erkezesiHely;
        $datum = empty($datum) === true ? '%' : $datum;
        $legitarsasag = empty($legitarsasag) === true ? '%' : $legitarsasag;

        $tobbmegallos_kereses = "SELECT ROWNUM, 
                                    egyik.JARATSZAM AS egyikJaratszam, TO_CHAR(egyik.INDULAS,'YYYY.MM.DD. HH:MI') AS egyikIndulas, TO_CHAR(egyik.ERKEZES,'YYYY.MM.DD. HH:MI') AS egyikErkezes, egyik.HONNAN AS egyikHonnan, egyik.HOVA AS egyikHova, egyik.LEGITARSASAG AS egyikLegitarsasag, egyik.AR AS egyikAr, egyik.TOBBMEGALLOS,
                                    masik.JARATSZAM AS masikJaratszam, TO_CHAR(masik.INDULAS,'YYYY.MM.DD. HH:MI') AS masikIndulas, TO_CHAR(masik.ERKEZES,'YYYY.MM.DD. HH:MI') AS masikErkezes, masik.HONNAN AS masikHonnan, masik.HOVA AS masikHova, masik.LEGITARSASAG AS masikLegitarsasag, masik.AR AS masikAr
                                    FROM JARAT egyik
                                    INNER JOIN JARAT masik ON egyik.TOBBMEGALLOS = masik.TOBBMEGALLOS AND egyik.TOBBMEGALLOS > 0 AND masik.TOBBMEGALLOS > 0
                                    AND egyik.HONNAN LIKE '$kiindulasiHely' AND masik.HOVA LIKE '$erkezesiHely' AND egyik.INDULAS LIKE '$datum' 
                                    AND egyik.LEGITARSASAG LIKE '$legitarsasag' AND masik.LEGITARSASAG LIKE '$legitarsasag' 
                                    AND egyik.SZABAD_HELY >= '$utasSzam' AND masik.SZABAD_HELY >= '$utasSzam'";
        return $tobbmegallos_kereses;
    }
}

function utasAdatok($tipus, $utas_szam, $jaratszam) {
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
        if(etkezes($jaratszam) === 1) {
            echo '<tr>';
            echo '<th>';
            echo '<label><input type="checkbox" name="etkezes-' . $tipus. '-' . $i . '" value="etkezes" checked>Étkezés</label>';
            echo '</th>';
            echo '<td> +5,720 Ft</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '</fieldset>';
    }

    csatlakozas_zarasa($utazasiiroda);
}

function repulojegyAra($jaratszam): int {
    include_once('common/connection.php');
    $utazasiiroda = csatlakozas();

    $repjegy_ara='0';
    $repjegy_ar_lekerdezes = oci_parse($utazasiiroda, "SELECT AR FROM JARAT WHERE JARATSZAM = '$jaratszam'") or die ('Hibás utasítás!');
    oci_execute($repjegy_ar_lekerdezes);
    while($current_row = oci_fetch_array($repjegy_ar_lekerdezes, OCI_ASSOC + OCI_RETURN_NULLS)) {
        $repjegy_ara = $current_row["AR"];
    }

    if(isset($repjegy_ar_lekerdezes) && is_resource($repjegy_ar_lekerdezes)) {
        oci_free_statement($repjegy_ar_lekerdezes);
    }

    csatlakozas_zarasa($utazasiiroda);
    return $repjegy_ara;
}

function etkezes($jaratszam): int {
    include_once('common/connection.php');
    $utazasiiroda = csatlakozas();

    $etkezes=0;
    $etkezes_lekerdezes = oci_parse($utazasiiroda, "SELECT ETKEZES FROM JARAT WHERE JARATSZAM = '$jaratszam'");
    oci_execute($etkezes_lekerdezes);
    while($current_row = oci_fetch_array($etkezes_lekerdezes, OCI_ASSOC + OCI_RETURN_NULLS)) {
        $etkezes = $current_row["ETKEZES"];
    }

    if(isset($etkezes_lekerdezes) && is_resource($etkezes_lekerdezes)) {
        oci_free_statement($etkezes_lekerdezes);
    }

    csatlakozas_zarasa($utazasiiroda);
    return $etkezes;
}

function foglalasokListazasa($felhasznalonev) {
    include_once('common/connection.php');
    $utazasiiroda = csatlakozas();

    $foglalt = oci_parse($utazasiiroda, "SELECT JARAT.JARATSZAM, JARAT.HONNAN, JARAT.HOVA, TO_CHAR(JARAT.INDULAS,'YYYY.MM.DD. HH:MI') AS INDULAS, JEGY.AR, JEGY.TIPUS FROM JEGY, JARAT WHERE JEGY.FELHASZNALONEV = '$felhasznalonev' AND JEGY.JARATSZAM=JARAT.JARATSZAM") or die ('Hibás utasítás!');
    oci_execute($foglalt);

    echo '<table id="foglalas-adatok-table">
                <tr>
                    <th>Kiindulási hely</th>
                    <th>Érkezési hely</th>
                    <th>Indulás</th>
                    <th>Jegy típusa</th>
                    <th>Ár</th>
                    <th></th>
                </tr>';
    while ($current_row = oci_fetch_array($foglalt, OCI_ASSOC + OCI_RETURN_NULLS)) {
            echo '<tr>';
            echo '<td>' . $current_row['HONNAN'] . '</td>';
            echo '<td>' . $current_row['HOVA'] . '</td>';
            echo '<td>' . $current_row['INDULAS'] . '</td>';
            echo '<td>' . ($current_row['TIPUS'] == 1 ? 'Felnőtt jegy' : 'Gyermek jegy') . '</td>';
            echo '<td>' . number_format($current_row['AR']) . ' Ft' .  '</td>';
            echo '<td><form action="profil.php" method="POST">';
            echo '<input type="hidden" name="jaratszam" value=' . $current_row["JARATSZAM"] . '>';
            echo '<input type="button" onclick=document.getElementById("biztositas-kotes").style.display="block" class="biztositas-kotes" name="biztositas-kotes-gomb" value="Biztosítás kötés">';
            echo '</form></td>';
            echo '</tr>';
    }
    echo '</table>';

    if(isset($foglalas) && is_resource($foglalas)) {
        oci_free_statement($foglalas);
    }

    csatlakozas_zarasa($utazasiiroda);
}

function ertekelesekListazasa($felhasznalonev) {
    include_once('common/connection.php');
    $utazasiiroda = csatlakozas();

    $ertekeles = oci_parse($utazasiiroda, "SELECT LEGITARSASAG.NEVE, ERTEKELES FROM LEGITARSASAG, ERTEKEL WHERE ERTEKEL.FELHASZNALONEV = '$felhasznalonev' AND LEGITARSASAG.NEVE=ERTEKEL.LEGITARSASAG");
    oci_execute($ertekeles);

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

    if(isset($ertekeles) && is_resource($ertekeles)) {
        oci_free_statement($ertekeles);
    }

    csatlakozas_zarasa($utazasiiroda);
}

function alMenuGeneralas(string $aktualisOldal) {
    echo '<div class="alMenu"><table> <caption>Admin felület</caption><thead><tr>';
        echo "<th><ul>" .
            "<li>" .
            "<a href='admin_statisztika.php'" . ($aktualisOldal === "statisztika" ? ' class=active' : "") . ">Statisztika</a>" .
            "</li>" .
            "<li>" .
            "<a href='admin_rekordok.php'" . ($aktualisOldal === "rekordok" ? ' class=active' : "") . ">Rekordok</a>" .
            "</li>" .
            "<li>" .
            "<a href='admin_naplozas.php'" . ($aktualisOldal === "naplozas" ? ' class=active' : "") . ">Naplózás</a>" .
            "</li>" .
            "</ul></th>";
    echo '</tr></thead></table></div>';
}

function logListazas() {
    include_once('common/connection.php');
    $utazasiiroda = csatlakozas();

    $naplok = ['UTAS_LOG', 'JEGY_LOG', 'ERTEKEL_LOG', 'JARAT_LOG', 'LEGITARSASAG_LOG', 'BIZTOSITO_LOG', 'BIZTOSITAS_LOG', 'BIZTOSITAS_KATEGORIAK_LOG'];

    foreach($naplok as $log) {
        $naplo = oci_parse($utazasiiroda, "SELECT * FROM $log");
        oci_execute($naplo);
        oci_fetch($naplo);
        if(oci_num_rows($naplo) !== 0) {
            $nfields = oci_num_fields($naplo);
            echo '<table><caption>' . $log . '</caption>';
            echo '<tr>';
            for ($i = 1; $i<=$nfields; $i++){
                $field = oci_field_name($naplo, $i);
                echo '<th>' . $field . '</th>';
            }
            echo '</tr>';

            oci_execute($naplo);
            while ( $row = oci_fetch_array($naplo, OCI_ASSOC + OCI_RETURN_NULLS)) {
                echo '<tr>';
                foreach ($row as $item) {
                    echo '<td>' . $item . '</td>';
                }
                echo '</tr>';
            }
            echo '</table>';
        }
    }

    if(isset($naplo) && is_resource($naplo)) {
        oci_free_statement($naplo);
    }

    csatlakozas_zarasa($utazasiiroda);
}

function osszesRekord() {
    include_once('common/connection.php');
    $utazasiiroda = csatlakozas();

    $tablak = ['UTAS', 'JEGY', 'ERTEKEL', 'JARAT', 'LEGITARSASAG', 'BIZTOSITO', 'BIZTOSITAS', 'BIZTOSITAS_KATEGORIAK'];

    foreach($tablak as $tablaNev) {
        $tabla = oci_parse($utazasiiroda, "SELECT * FROM $tablaNev");
        oci_execute($tabla);
        oci_fetch($tabla);
        if(oci_num_rows($tabla) !== 0) {
            $nfields = oci_num_fields($tabla);
            echo '<table><caption>' . $tablaNev . '</caption>';
            echo '<tr>';
            for ($i = 1; $i<=$nfields; $i++){
                $field = oci_field_name($tabla, $i);
                echo '<th>' . $field . '</th>';
            }
            echo '</tr>';

            oci_execute($tabla);
            while ($row = oci_fetch_array($tabla, OCI_ASSOC + OCI_RETURN_NULLS)) {
                echo '<tr>';
                foreach ($row as $item) {
                    echo '<td>' . $item . '</td>';
                }
                echo '</tr>';
            }
            echo '</table>';
        }
    }

    if(isset($tabla) && is_resource($tabla)) {
        oci_free_statement($tabla);
    }

    csatlakozas_zarasa($utazasiiroda);
}

function biztositasListazas() {
    include_once('common/connection.php');
    $utazasiiroda = csatlakozas();

    $biztositok = ['Hello, én a Biztonságos Biztosító vagyok, és nagyon biztonságos vagyok',
        'Üdv, én a Megbízható Biztosító vagyok, és nagyon megbízható vagyok',
        'Hello, én az Olcsó Biztosító vagyok, és nagyon olcsó vagyok',
        'Üdv, én a Legjobb Biztosító vagyok, és én vagyok a legjobb',
        'Hello, én a Jó Biztosító vagyok, és nagyon jó vagyok'];

    foreach($biztositok as $biztosito) {
        $biztositas = oci_parse($utazasiiroda, "SELECT DISTINCT(BIZTOSITAS_KATEGORIAK.KATEGORIA), BIZTOSITAS.AR FROM BIZTOSITAS, BIZTOSITO, BIZTOSITAS_KATEGORIAK 
                                                    WHERE BIZTOSITO.ID=BIZTOSITAS.BIZTOSITOID AND BIZTOSITAS_KATEGORIAK.ID=BIZTOSITAS.ID 
                                                    AND BIZTOSITO.LEIRAS = '$biztosito' ORDER BY BIZTOSITAS_KATEGORIAK.KATEGORIA");
        oci_execute($biztositas) or die('hiba');

        echo '<table id="biztositas-table">';
            echo '<caption>' . $biztosito . '</caption>';
                echo '<tr>
                    <th>Kategória</th>
                    <th>Ár</th>
                </tr>';
        while ($current_row = oci_fetch_array($biztositas, OCI_ASSOC + OCI_RETURN_NULLS)) {
            echo '<tr>';
            echo '<td>' . $current_row['KATEGORIA'] . '</td>';
            echo '<td>' . $current_row['AR'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    if(isset($biztositas) && is_resource($biztositas)) {
        oci_free_statement($biztositas);
    }

    csatlakozas_zarasa($utazasiiroda);
}

function biztositasokListazas($melyik) {
    include_once('common/connection.php');
    $utazasiiroda = csatlakozas();

    $biztositasok = oci_parse($utazasiiroda, "SELECT BIZTOSITAS_KATEGORIAK.KATEGORIA FROM BIZTOSITAS, BIZTOSITO, BIZTOSITAS_KATEGORIAK 
                                                    WHERE BIZTOSITO.ID=BIZTOSITAS.BIZTOSITOID AND BIZTOSITAS_KATEGORIAK.ID=BIZTOSITAS.ID 
                                                    AND BIZTOSITO.LEIRAS LIKE '$melyik' ORDER BY BIZTOSITAS_KATEGORIAK.KATEGORIA");
    oci_execute($biztositasok) or die('HIBA');

    while ($current_row = oci_fetch_array($biztositasok, OCI_ASSOC + OCI_RETURN_NULLS)) {
        echo '<option value="'. $current_row["KATEGORIA"] . '"' . '>' . $current_row["KATEGORIA"] . '</option>';
    }

    if(isset($biztositasok) && is_resource($biztositasok)) {
        oci_free_statement($biztositasok);
    }

    csatlakozas_zarasa($utazasiiroda);
}

function regisztracio($felhasznalonev, $email, $jelszo): array {
    include_once "common/connection.php";
    include_once "common/fuggvenyek.php";
    $utazasiiroda = csatlakozas();

    $errors = [];

    $felhasznalok = oci_parse($utazasiiroda, 'SELECT * FROM UTAS');
    oci_execute($felhasznalok);
    while ($current_row = oci_fetch_array($felhasznalok, OCI_ASSOC + OCI_RETURN_NULLS)) {
        if ($current_row['FELHASZNALONEV'] === $felhasznalonev) {
            $errors[] = "A felhasználónév már foglalt!";
        }
        if ($current_row['EMAIL'] === $email) {
            $errors[] = "Az e-mail cím már foglalt!";
        }
    }

    if (count($errors) === 0) {
        $jelszo = password_hash($jelszo, PASSWORD_DEFAULT);
        $ujUtas = oci_parse($utazasiiroda, "INSERT INTO UTAS  (FELHASZNALONEV, EMAIL, JELSZO) VALUES ('$felhasznalonev','$email','$jelszo')");
        oci_execute($ujUtas) or die('Hiba');
        header("Location: bejelentkezes.php?reg=true");
    }

    if(isset($felhasznalok) && is_resource($felhasznalok)) {
        oci_free_statement($felhasznalok);
    }
    if(isset($ujUtas) && is_resource($ujUtas)) {
        oci_free_statement($ujUtas);
    }
    csatlakozas_zarasa($utazasiiroda);

    return $errors;
}

function bejelentkezes($felhasznalonev, $jelszo): bool {
    include_once('common/connection.php');
    $utazasiiroda = csatlakozas();

    $error = false;
    $felhasznalo_adat = array();
    $bejelentkezve = false;

    $felhasznalok = oci_parse($utazasiiroda, 'SELECT * FROM UTAS');
    oci_execute($felhasznalok);
    while ($current_row = oci_fetch_array($felhasznalok, OCI_ASSOC + OCI_RETURN_NULLS)) {
        if ($felhasznalonev === $current_row["FELHASZNALONEV"] && password_verify($jelszo, $current_row["JELSZO"])) {
            $bejelentkezve = true;
            $felhasznalo_adat["felhasznalonev"] = $current_row["FELHASZNALONEV"];
            $felhasznalo_adat["email"] = $current_row["EMAIL"];
            break;
        }
    }

    if ($bejelentkezve) {
        $_SESSION["user"] = $felhasznalo_adat;
        header("Location: profil.php?login=true");
    } else {
        $error = true;
    }

    if(isset($felhasznalok) && is_resource($felhasznalok)) {
        oci_free_statement($felhasznalok);
    }
    csatlakozas_zarasa($utazasiiroda);

    return $error;
}

function legitarsasagErtekelo() {
    echo '<div id="legitarsasagi-ertekeles" class="modal">
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
        <script src="js/modal.js"></script>';
}