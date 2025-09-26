// initialisation du DOM
document.addEventListener('DOMContentLoaded', function() {

    // on position le point gps et le marqueur
    var map = L.map('map').setView([51.505, -0.09], 13);
    var marker = L.marker([51.5, -0.09]).addTo(map);

    // on ajoute les tuiles
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    // on ajoute un pop-up 
    marker.bindPopup("<b>Hello world!</b><br>I am a popup.").openPopup();

});