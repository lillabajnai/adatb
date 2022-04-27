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
    $jaratszam = htmlspecialchars($_POST['jaratszam']);
    $honnan = htmlspecialchars($_POST['honnan']);
    $hova = htmlspecialchars($_POST['hova']);
    $indulas = htmlspecialchars($_POST['indulas']);
    $erkezes = htmlspecialchars($_POST['erkezes']);
    $legitarsasag = htmlspecialchars($_POST['legitarsasag']);
    $ar = htmlspecialchars($_POST['ar']);

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

    if(empty($_POST['indulas'])){
        $errors = "Az indulás mezőt ki kell tölteni!";
    }

    if(empty($_POST['erkezes'])){
        $errors = "Az érkezés mezőt ki kell tölteni!";
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
        $ujJarat = oci_parse($utazasiiroda,"INSERT INTO JARAT VALUES ('$jaratszam','$honnan','$hova', '$indulas','$erkezes', '$legitarsasag','$ar')");
        oci_execute($ujJarat) or die('Hiba');
        header('Location: admin.php?jarat=true');
    }
}

// LÉGITÁRSASÁG HOZZÁADÁSA
if(isset($_POST['legitarsasag-gomb'])) {
    $ujLegitarsasagNeve = htmlspecialchars($_POST['neve']);
    $ujLegitarsasagTelephelye = htmlspecialchars($_POST['telephelye']);
    $ujLegitarsasagTulajdonosa = htmlspecialchars($_POST['tulajdonosa']);

    $errors = [];

    if(empty($_POST['neve'])) {
        $errors = "Az új légitársaságnak nevet kell adni!";
    }

    if(empty($_POST['telephelye'])) {
        $errors = "Az új légitársaság telephelyét meg kell adni!";
    }

    if(empty($_POST['tulajdonosa'])) {
        $errors = "Az új légitársaság tulajdonosát meg kell adni!";
    }

    if (count($errors) > 0) {
        print_r($errors);
    } else {
        $ujLegitarsasag = oci_parse($utazasiiroda,"INSERT INTO LEGITARSASAG VALUES('$ujLegitarsasagNeve','$ujLegitarsasagTelephelye','$ujLegitarsasagTulajdonosa')");
        oci_execute($ujLegitarsasag) or die("Hiba!");
        header('Location: admin.php?legitarsasag=true');
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
                    <th><?php alMenuGeneralas('rekordok'); ?></th>
                </tr>
                </thead>
            </table>



            <div id="urlapok">
                <?php
                if(isset($_GET["jarat"])) {
                    echo "<div class='siker'>Új járat sikeresen hozzáadva!</div>";
                } else if (isset($_GET["legitarsasag"])) {
                    echo "<div class='siker'>Új légitársaság sikeresen hozzáadva!</div>";
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
                <h2>Járatok hozzáadása az adatbázishoz:</h2>
                <form action="admin.php" method="POST">
                    <label for="jaratszam">Járatszám:
                        <input type="number" id="jaratszam" name="jaratszam" required/></label><br/>

                    <label for="honnan">Kiindulási hely:
                        <input type="text" id="honnan" name="honnan" required/></label><br/>

                    <label for="hova">Érkezési hely:
                        <input type="text" id="hova" name="hova" required/></label><br/>

                    <label for="indulas">Indulás:
                        <input type="datetime-local" id="indulas" name="indulas" required/></label><br/>

                    <label for="erkezes">Érkezés:
                        <input type="datetime-local" id="erkezes" name="erkezes" required/></label><br/>

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

                    <label for="tulajdonosa">Tulajdonosa:
                        <input type="text" id="tulajdonosa" name="tulajdonosa" required/></label><br/>

                    <label for="telephelye">Telephelye:
                        <input type="text" id="telephelye" name="telephelye" required/></label><br/>

                    <input type="submit" name="legitarsasag-gomb" value="Megerősítés"><br/>
                </form>

                </form>
            </div>
        </div>
    </div>
</main>
<?php
include_once "common/footer.php";
?>
</body>
</html>