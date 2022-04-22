var modal = document.getElementById("legitarsasagi-ertekeles");
var modalGomb = document.getElementById("ertekeles");
var span = document.getElementsByClassName("bezaras")[0];

/* Megnyitja a modalt */
modalGomb.onclick = function() {
    modal.style.display = "block";
}
/* Kilpés gomb */
span.onclick = function() {
    modal.style.display = "none";
}