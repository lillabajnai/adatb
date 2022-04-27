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
        <div class="tabladiv">
        <table>
            <caption>Admin felület</caption>
            <thead>
            <tr>
                <th><?php alMenuGeneralas('naplozas'); ?></th>
            </tr>
            </thead>
            <tbody>
            <tr>

            </tr>
            </tbody>
        </table>
        </div>
    </div>
</main>
<?php
include_once "common/footer.php";
?>
</body>
</html>