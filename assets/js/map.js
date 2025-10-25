// initialisation du DOM
document.addEventListener('DOMContentLoaded', function() {
    // Initialisation de la carte avec une vue sur la France
    window.map = L.map('map').setView([46.603354, 1.888334], 6);

    // on ajoute les tuiles
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(window.map);

    
    // on ajoute l'évenement qui permettra de récupérer la localisation au click
    function clickPosition(event){
        if (event) {
            console.log(event.latlng.lat);
            console.log(event.latlng.lng);
            // ouvre le formulaire d'alerte
            const modal = document.getElementById('crud-modal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                modal.setAttribute('aria-hidden', 'false');
            }
        } else {
            console.log('Non définis');
        }
    } 

    map.on('click', clickPosition);
});
