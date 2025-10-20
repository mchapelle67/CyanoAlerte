// initialisation du DOM
document.addEventListener('DOMContentLoaded', function() {
    // Initialisation de la carte avec une vue sur la France
    window.map = L.map('map').setView([46.603354, 1.888334], 6);

    // on ajoute les tuiles
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(window.map);

    // on ajoute un marqueur (centré sur la France)
    window.marker = L.marker([46.603354, 1.888334]).addTo(window.map);
});
