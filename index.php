<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Irinyi utazási iroda</title>
    <link rel="icon" href="img/negyedikicon.png"/>
    <link rel="stylesheet" href="css/style.css"/>
    <link rel="stylesheet" href="css/kezdolap.css"/>
</head>
<body>
<?php
    include_once "common/header.php";
    include_once "common/fuggvenyek.php";
    menuGeneralas('index');
?>
<main>
    <div class="container">
        <div class="kezdo-szoveg">
            <h1>Utazna, de még nem tudja hova?</h1>
            <h2>Segítünk megtalálni a tökéletes úti célt!</h2>
        </div>
        <div class="motivalo-tabla">
            <table>
                <caption>Miért jár jól az Irinyi utazási irodával?</caption>
                <thead>
                <tr>
                    <th>20 éve</th>
                    <th>500 000+</th>
                    <th>500+</th>
                    <th>3 500+</th>
                    <th>100 hívást</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>vagyunk jelen a hazai piacon</td>
                    <td>utast szolgáltunk ki eddig</td>
                    <td>légitársaság ajánlatát kínáljuk egy helyen</td>
                    <td>úti célra kínálunk repjegyeket világszerte</td>
                    <td>válaszolunk meg átlagosan naponta*</td>
                </tr>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="5">*A 2001-2021-es évek adatai alapján</td>
                </tr>
                </tfoot>
            </table>
        </div>

    <div class="hirlevel">
        <h3>Iratkozzon fel hírlevelünkre!</h3>
        <form method="POST">
            <input type="text" name="nev" placeholder="Név" required/> <br/><br/>
            <input type="email" name="email" placeholder="E-mail" required/> <br/><br/>
            <label> <input type="checkbox" name="feliratkozas" required/> Feliratkozom az irinyiutazasiiroda.hu hírlevelére, az adatkezelési tájékoztatóban leírtakat elfogadom. <br/><br/> </label>
            <input type="submit" value="Feliratkozás"/>
        </form>
    </div>

    <div class="legnepreszerubb-uticelok">
        <h3>A legnépszerűbb úticélok</h3>
        <table>
            <tbody>
            <tr>
                <td>
                    <a href="jegyfoglalas.php" title="Utazz velünk Amszterdamba!"><img src="img/ams.jpg" alt="amszterdam"/></a>
                </td>
                <td>Amszterdam <br/> 39 035 Ft-tól <br/> júniusban</td>
            </tr>
            <tr>
                <td>
                    <a href="jegyfoglalas.php" title="Utazz velünk Athénba!"><img src="img/ath.jpg" alt="athen"/></a>
                </td>
                <td>Athén <br/> 13 663 Ft-tól <br/> májusban</td>
            </tr>
            <tr>
                <td>
                    <a href="jegyfoglalas.php" title="Utazz velünk Barcelónába!"><img src="img/bcn.jpg" alt="barcelona"/></a>
                </td>
                <td>Barcelona <br/> 17 168 Ft-tól <br/> májusban</td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="kozossegi-media">
        <h3>Kövessen minket!</h3>
        <p>Ne maradjon le semmiről! Találkozzunk a közösségi oldalakon, ahol rendszeresen jelentkezünk repülőjegy akciókkal, nyereményjátékokkal és úti cél ajánlókkal!</p>
        <a href="" title="Kövess minket Facebook oldalunkon!"><img src="img/Facebook_Logo.png" alt="facebook logo"/></a>
        <a href="" title="Kövess minket Instagram oldalunkon!"><img src="img/Instagram_logo.png" alt="instagram logo"/></a>
    </div>
</div>
</main>
</body>
</html>