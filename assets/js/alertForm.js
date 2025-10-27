document.addEventListener('DOMContentLoaded', function() {
    // on ajoute l'évenement qui permettra de récupérer la localisation au click
    function setAlertFormPosition(event){
        if (event && event.latlng) {
            let lat = event.latlng.lat;
            let lng = event.latlng.lng;

            // Affiche la position dans le span
            const positionSpan = document.getElementById('position-display');
            if (positionSpan) {
                positionSpan.textContent = lat + ', ' + lng;
                document.getElementById('custom-text').style.display = 'none';
            }
        
            // Remplit les inputs cachés
            const latForm = document.getElementById('alert_type_form_waterbody_latitude');
            const lngForm = document.getElementById('alert_type_form_waterbody_longitude');
            if (latForm) latForm.value = lat;
            if (lngForm) lngForm.value = lng;

            // Ouvre le formulaire d'alerte
            const modal = document.getElementById('crud-modal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                modal.setAttribute('aria-hidden', 'false');
            }
        } else {
            console.log('Position non définie');
        }
    }
    // remettre la position à zéro qd on quitte le form sans l'envoyer
    function resetAlertFormPosition() {
        const positionSpan = document.getElementById('position-display');
        const customText = document.getElementById('custom-text');
        if (positionSpan) positionSpan.textContent = '';
        if (customText) customText.style.display = '';
    }

    // ajout d'un écouteur sur le bouton de fermeture
    const modal = document.getElementById('crud-modal');
    if (modal) {
        const closeBtn = modal.querySelector('[data-modal-toggle]');
        if (closeBtn) {
            closeBtn.addEventListener('click', resetAlertFormPosition);
        }
    }

    map.on('click', setAlertFormPosition);
});