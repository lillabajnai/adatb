
--LEKÉRDEZÉSEK--

--LEGROVIDEBB UTTAL RENDELKEZO (GYEREKJEGYJEGY) (50000 ALATT) (Szegedrol New Yorkba)
SELECT JEGY.ID, JEGY.AR, JEGY.FELHASZNALONEV, JEGY.JARATSZAM, JARAT.ETKEZES FROM JEGY, JARAT
WHERE JARAT.JARATSZAM = JEGY.JARATSZAM AND JEGY.JARATSZAM = 
     (SELECT JARATSZAM FROM JARAT
     WHERE (ERKEZES-INDULAS) = 
          (SELECT (ERKEZES-INDULAS) AS IDOTARTAM FROM JARAT
          WHERE HONNAN = 'Szeged' AND HOVA = 'New York'
          ORDER BY JARATSZAM
          FETCH FIRST 1 ROW ONLY) 
     AND HONNAN = 'Szeged' AND HOVA = 'New York'
order by jaratszam FETCH FIRST 1 ROW ONLY) AND JEGY.AR < 50000;

--ATLAGOS POGGYASZBIZTOSITAS AR
SELECT ROUND(AVG(BIZTOSITAS.AR)) AS ATLAG_AR FROM BIZTOSITAS, BIZTOSITAS_KATEGORIAK
WHERE BIZTOSITAS_KATEGORIAK.ID = BIZTOSITAS.ID AND
KATEGORIA = 'poggyászbiztosítás';

--ADOTT UTAS LEGUTOLSO UTAZASA
SELECT MAX(JARAT.INDULAS) AS LEGUTOLSO FROM UTAS, JEGY, JARAT
WHERE JEGY.FELHASZNALONEV = UTAS.FELHASZNALONEV AND
JARAT.JARATSZAM = JEGY.JARATSZAM AND
UTAS.FELHASZNALONEV = 'Admin01';

--UTASOK MEGVASAROLT JEGYEINEK A SZAMA ES OSSZEGE
SELECT UTAS.FELHASZNALONEV, COUNT(JEGY.JARATSZAM) AS JEGYEK_SZAMA, SUM(JEGY.AR) AS JEGYEK_ARA FROM UTAS, JEGY
WHERE JEGY.FELHASZNALONEV = UTAS.FELHASZNALONEV 
GROUP BY UTAS.FELHASZNALONEV
ORDER BY UTAS.FELHASZNALONEV;

--LEGTOBBET KOLTOTT UTAS JEGYEI 
SELECT JEGY.ID, JEGY.AR, JEGY.JARATSZAM, JARAT.HONNAN, JARAT.HOVA, JARAT.INDULAS, JARAT.ERKEZES FROM UTAS, JEGY, JARAT
WHERE JEGY.FELHASZNALONEV = UTAS.FELHASZNALONEV AND
UTAS.FELHASZNALONEV = 
    (SELECT UTAS.FELHASZNALONEV FROM UTAS, JEGY
    WHERE JEGY.FELHASZNALONEV = UTAS.FELHASZNALONEV
    GROUP BY UTAS.FELHASZNALONEV
    ORDER BY SUM(JEGY.AR) DESC
   FETCH FIRST 1 ROW ONLY) 
AND JARAT.JARATSZAM = JEGY.JARATSZAM
ORDER BY INDULAS;

--LEGTOBBET VASAROLT BIZTOSITASKATEGORIA
SELECT KATEGORIA, COUNT(BIZTOSITAS_KATEGORIAK.ID) AS DARAB FROM BIZTOSITAS, BIZTOSITAS_KATEGORIAK
WHERE BIZTOSITAS.ID = BIZTOSITAS_KATEGORIAK.ID
GROUP BY KATEGORIA
ORDER BY DARAB DESC
FETCH FIRST 1 ROW ONLY;

--ADOTT BIZTOSITO BIZTOSITASAT HANYAN VETTEK MEG
SELECT BIZTOSITAS_KATEGORIAK.KATEGORIA, COUNT(BIZTOSITAS_KATEGORIAK.ID) AS DARAB FROM BIZTOSITAS_KATEGORIAK, BIZTOSITO, BIZTOSITAS
WHERE BIZTOSITAS_KATEGORIAK.ID = BIZTOSITAS.ID AND BIZTOSITO.ID = BIZTOSITAS.BIZTOSITOID AND BIZTOSITAS.BIZTOSITOID=1001
GROUP BY BIZTOSITAS_KATEGORIAK.KATEGORIA;

--ADOTT LEGITARSASAG LEGJOBB ERTEKELESU JARATA
SELECT JARAT.LEGITARSASAG, MAX(ERTEKEL.ERTEKELES) AS LEGJOBB_ERTEKELES, JARAT.JARATSZAM FROM ERTEKEL, JARAT
WHERE JARAT.LEGITARSASAG = ERTEKEL.LEGITARSASAG
AND ERTEKELES >= 3 AND HONNAN = 'Szeged'
GROUP BY JARAT.LEGITARSASAG, JARATSZAM
FETCH FIRST 1 ROW ONLY;

--ADOTT LEGITARSASAG SZEGEDROL INDULO LEGUTOLSO JARATA TULAJDONOS ALAPJAN
SELECT JARAT.JARATSZAM, MAX(JARAT.INDULAS) AS INDULAS, LEGITARSASAG FROM JARAT, LEGITARSASAG
WHERE JARAT.LEGITARSASAG = LEGITARSASAG.NEVE AND
HONNAN = 'Szeged' AND LEGITARSASAG.TULAJDONOS = 'Grippen Gergő'
GROUP BY JARAT.JARATSZAM, LEGITARSASAG
FETCH FIRST 1 ROW ONLY;

--SZEGEDI TELEPHELYU LEGITARSASAG LEGTOBB SZABAD HELLYEL RENDELKEZO JARATA
SELECT JARAT.JARATSZAM, JARAT.HONNAN, JARAT.HOVA, JARAT.SZABAD_HELY FROM JARAT
WHERE JARAT.JARATSZAM = 
    (SELECT JARAT.JARATSZAM FROM JARAT, LEGITARSASAG
    WHERE JARAT.LEGITARSASAG = LEGITARSASAG.NEVE AND TELEPHELY = 'Szeged'
    ORDER BY JARAT.SZABAD_HELY DESC
    FETCH FIRST 1 ROW ONLY);
    
--JARATOK SZAMA LEGITARSASAGOK SZERINT
SELECT LEGITARSASAG.NEVE, COUNT(*) AS DARAB FROM JARAT, LEGITARSASAG
WHERE LEGITARSASAG.NEVE = JARAT.LEGITARSASAG
GROUP BY LEGITARSASAG.NEVE;
