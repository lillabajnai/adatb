<?php
session_status() === PHP_SESSION_ACTIVE || session_start();
if(!isset($_SESSION["user"]) || empty($_SESSION["user"] || !($_SESSION["user"]["felhasznalonev"]==='admin'))){
    header("Location: index.php");
    exit();
}

include_once('common/connection.php');
$utazasiiroda = csatlakozas();

    // JÁRAT HOZZÁADÁSA
    if(isset($_POST["jarat-gomb"])){
        $jaratszam = $_POST['jaratszam'];
        $honnan = $_POST['honnan'];
        $hova = $_POST['hova'];
        $honnan2 = $_POST['honnan2'];
        $hova2 = $_POST['hova2'];
        $datum = $_POST['datum'];
        $indulas = $_POST['indulas'];
        $menetido = $_POST['menetido'];
        $erkezes = $_POST['erkezes'];
        $indulas2 = $_POST['indulas2'];
        $menetido2 = $_POST['menetido2'];
        $erkezes2 = $_POST['erkezes2'];
        $osztaly = $_POST['osztaly'];
        $legitarsasag = $_POST['legitarsasag'];
        $ar = $_POST['ar'];

        $errors = [];

        if(empty($_POST['jaratszam'])){
            $errors = "A járatszám mezőt ki kell tölteni!";
        }
        if(empty($_POST['honnan'])){
            $errors = "A kiindulási hely mezőt ki kell tölteni!";
        }
        if(empty($_POST['hova'])){
            $errors = "Az érkezési hely mezőt ki kell tölteni!";
        }

        if(empty($_POST['honnan2'])){
            $honnan2 = '';
        }

        if(empty($_POST['hova2'])){
            $hova2 = '';
        }

        if(empty($_POST['datum'])){
            $errors = "A dátum mezőt ki kell tölteni!";
        }

        if(empty($_POST['indulas'])){
            $errors = "Az indulás mezőt ki kell tölteni!";
        }

        if(empty($_POST['menetido'])){
            $errors = "A menetidő mezőt ki kell tölteni!";
        }

        if(empty($_POST['erkezes'])){
            $errors = "Az érkezés mezőt ki kell tölteni!";
        }

        if(empty($_POST['indulas2'])){
            $indulas2 = '';
        }

        if(empty($_POST['menetido2'])){
            $menetido2 = '';
        }

        if(empty($_POST['erkezes2'])){
            $erkezes2 = '';
        }

        if(empty($_POST['osztaly'])){
            $errors = "Az osztály mezőt ki kell tölteni!";
        }

        if(empty($_POST['legitarsasag'])){
            $errors = "A légitársaság mezőt ki kell tölteni!";
        }

        if(empty($_POST['ar'])){
            $errors = "Az ár mezőt ki kell tölteni!";
        }

        if(count($errors) > 0){
            print_r($errors);
        } else{
            $jaratszam = $utazasiiroda->real_escape_string($_POST['jaratszam']);
            $honnan = $utazasiiroda->real_escape_string($_POST['honnan']);
            $hova = $utazasiiroda->real_escape_string($_POST['hova']);
            $honnan2 = $utazasiiroda->real_escape_string($_POST['honnan2']);
            $hova2 = $utazasiiroda->real_escape_string($_POST['hova2']);
            $datum = $utazasiiroda->real_escape_string($_POST['datum']);
            $indulas = $utazasiiroda->real_escape_string($_POST['indulas']);
            $menetido = $utazasiiroda->real_escape_string($_POST['menetido']);
            $erkezes = $utazasiiroda->real_escape_string($_POST['erkezes']);
            $indulas2 = $utazasiiroda->real_escape_string($_POST['indulas2']);
            $menetido2 = $utazasiiroda->real_escape_string($_POST['menetido2']);
            $erkezes2 = $utazasiiroda->real_escape_string($_POST['erkezes2']);
            $osztaly = $utazasiiroda->real_escape_string($_POST['osztaly']);
            $legitarsasag = $utazasiiroda->real_escape_string($_POST['legitarsasag']);
            $ar = $utazasiiroda->real_escape_string($_POST['ar']);

            $adat = mysqli_query($utazasiiroda,"INSERT INTO JARAT VALUES ('$jaratszam','$honnan','$hova',NULLIF('$honnan2',''), NULLIF('$hova2', ''),'$datum',
            '$indulas','$menetido','$erkezes', NULLIF('$indulas2',''), NULLIF('$menetido2',''), NULLIF('$erkezes2',''), '$osztaly', '$legitarsasag','$ar')") or die(mysqli_error($utazasiiroda));
            header('Location: admin.php?jarat=true');
        }
    }

    // LÉGITÁRSASÁG HOZZÁADÁSA
    if(isset($_POST['legitarsasag-gomb'])) {

        $ujlegitarsneve = $_POST['neve'];

        $errors = [];

        if(empty($_POST['neve'])) {
            $errors = "Az új légitársaságnak nevet kell adni!";
        }

        if (count($errors) == 1) {
            print_r($errors);
        } else {
            $ujlegitarsneve = $utazasiiroda->real_escape_string($_POST['neve']);

            $adat = mysqli_query($utazasiiroda,"INSERT INTO legitarsasag VALUES(NULL,'$ujlegitarsneve')") or die("Hiba!");
            header('Location: admin.php?legitarsasag=true');
        }
    }

    // OSZTÁLY HOZZÁADÁSA
    if(isset($_POST['osztaly-gomb'])){
        $ujosztalyneve = $_POST['megnevezes'];

        $errors = [];

        if(empty($_POST['megnevezes'])){
            $errors = "Az új osztálynak megnevezést kell adni!";
        }

        if (count($errors) == 1) {
            print_r($errors);
        } else {
            $ujosztalyneve = $utazasiiroda->real_escape_string($_POST['megnevezes']);

            $adat = mysqli_query($utazasiiroda,"INSERT INTO osztaly VALUES(NULL,'$ujosztalyneve')") or die("Hiba!");
            header('Location: admin.php?osztaly=true');
        }
    }

    // POGGYÁSZFAJTA HOZZÁADÁSA
    if(isset($_POST['poggyasz-gomb'])) {
        $ujpoggyasz = $_POST['megnevezes'];
        $poggyaszar = $_POST['ar'];

        $errors = [];

        if (empty($_POST['megnevezes'])) {
            $errors = "Az új poggyásznak megnevezést kell adni!";
        }
        if (empty($_POST['ar'])) {
            $errors = "Az új poggyásznak árat kell adni!";
        }

        if (count($errors) > 0) {
            print_r($errors);
        } else {
            $ujpoggyasz = $utazasiiroda->real_escape_string($_POST['megnevezes']);
            $poggyaszar = $utazasiiroda->real_escape_string($_POST['ar']);

            $adat = mysqli_query($utazasiiroda,"INSERT INTO POGGYASZ VALUES(NULL,'$ujpoggyasz','$poggyaszar')") or die("Hiba!");
            header('Location: admin.php?poggyasz=true');
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
        <div id="urlapok">
            <?php
            if(isset($_GET["jarat"])) {
                echo "<div class='siker'>Új járat sikeresen hozzáadva!</div>";
            } else if (isset($_GET["legitarsasag"])) {
                echo "<div class='siker'>Új légitársaság sikeresen hozzáadva!</div>";
            } else if (isset($_GET["osztaly"])) {
                echo "<div class='siker'>Új osztály sikeresen hozzáadva!</div>";
            } else if (isset($_GET["poggyasz"])) {
                echo "<div class='siker'>Új poggyászfajta sikeresen hozzáadva!</div>";
            }

            if(isset($_POST['jarat-gomb']) || isset($_POST['legitarsasag-gomb']) || isset($_POST['osztaly-gomb']) || isset($_POST['poggyasz-gomb'])) {
                if(count($errors) > 0) {
                    echo "<div class='hiba'>";
                    foreach ($errors as $error) {
                        echo $error . "</br>";
                    }
                    echo "</div>";
                }
            }
            ?>
            <h1>Admin felület</h1>
            <h2>Járatok hozzáadása az adatbázishoz:</h2>
            <form action="admin.php" method="POST">
                <label for="jaratszam">Járatszám:
                <input type="text" id="jaratszam" name="jaratszam" required/></label><br/>

                <label for="honnan">Kiindulási hely:
                    <input type="text" id="honnan" name="honnan" required/></label><br/>

                <label for="hova">Érkezési hely:
                    <input type="text" id="hova" name="hova" required/></label><br/>

                <label for="honnan2">Kiindulási hely (több megállós járat esetén):
                    <input type="text" id="honnan2" name="honnan2"/></label><br/>

                <label for="hova2">Kiindulási hely (több megállós járat esetén):
                    <input type="text" id="hova2" name="hova2"/></label><br/>

                <label for="datum">Dátum:
                    <input type="date" id="datum" name="datum" required/></label><br/>

                <label for="indulas">Indulás:
                    <input type="time" id="indulas" name="indulas" required/></label><br/>

                <label for="menetido">Menetidő:
                    <input type="time" id="menetido" name="menetido" required/></label><br/>

                <label for="erkezes">Érkezés:
                    <input type="time" id="erkezes" name="erkezes" required/></label><br/>

                <label for="indulas2">Indulás (több megállós járat esetén):
                    <input type="time" id="indulas2" name="indulas2"/></label><br/>

                <label for="menetido2">Menetidő (több megállós járat esetén):
                    <input type="time" id="menetido2" name="menetido2"/></label><br/>

                <label for="erkezes2">Érkezés (több megállós járat esetén):
                    <input type="time" id="erkezes2" name="erkezes2"/></label><br/>

                <label for="osztaly">Osztály:
                <select id="osztaly" name="osztaly">
                    <?php osztalyListazas(); ?>
                </select></label><br/>

                <label for="legitarsasag">Légitársaság:
                <select id="legitarsasag" name="legitarsasag">
                    <?php legitarsasagListazas(); ?>
                </select></label><br/>

                <label for="ar">Ár:
                    <input type="number" id="ar" name="ar" required/></label><br/>

                <input type="submit" name="jarat-gomb" value="Megerősítés"><br/>
            </form>
            <h2>Légitársaság hozzáadása az adatbázishoz:</h2>
            <form action="admin.php" method="POST">
                <label for="neve">Légitársaság neve:
                    <input type="text" id="neve" name="neve" required/></label><br/>

                <input type="submit" name="legitarsasag-gomb" value="Megerősítés"><br/>
            </form>
            <h2>Osztály hozzáadása az adatbázishoz:</h2>
            <form action="admin.php" method="POST">
                <label for="megnevezes">Osztály megnevezése:
                    <input type="text" id="megnevezes" name="megnevezes" required/></label><br/>

                <input type="submit" name="osztaly-gomb" value="Megerősítés"><br/>
            </form>
            <h2>Poggyászfajta hozzáadása az adatbázishoz:</h2>
            <form action="admin.php" method="POST">
                <label for="megnevezes">Poggyász megnevezése:
                    <input type="text" id="megnevezes" name="megnevezes" required/></label><br/>

                <label for="ar">Ár:
                    <input type="number" id="ar" name="ar" required/></label><br/>

                <input type="submit" name="poggyasz-gomb" value="Megerősítés"><br/>
            </form>
        </div>
    </div>
</main>
<?php
    include_once "common/footer.php";
?>
</body>
</html>