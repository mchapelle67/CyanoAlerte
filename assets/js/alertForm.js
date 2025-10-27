document.addEventListener('DOMContentLoaded', function() {
    // on ajoute l'évenement qui permettra de récupérer la localisation au click
    async function setAlertFormPosition(event){
        if (event && event.latlng) {
            let lat = event.latlng.lat;
            let lng = event.latlng.lng;

            // Affiche la position dans le span
            const positionSpan = document.getElementById('position-display');
            if (positionSpan) {
                positionSpan.textContent = lat + ', ' + lng;
                document.getElementById('custom-text').style.display = 'none';
            }

            // Ouvre le modal avec overlay
            openModal();

            // récupère la ville et le département en arrière-plan (asynchrone)
            if (window.getCityFromCoordinates) {
                const cityInput = document.getElementById('alert_type_form_waterbody_city');
                const depInput = document.getElementById('alert_type_form_waterbody_department');

                if (cityInput) {
                    cityInput.value = 'Chargement...';
                }
                
                const cityData = await window.getCityFromCoordinates(lat, lng);
                if (cityData && cityInput) {
                    if (cityData.ville) {
                        cityInput.value = cityData.ville;
                    }
                    if (cityData.departement && depInput) {
                        depInput.value = cityData.departement;
                    }
                } else if (cityInput) {
                    cityInput.value = '';
                }
            }

            // remplit les inputs cachés
            const latForm = document.getElementById('alert_type_form_waterbody_latitude');
            const lngForm = document.getElementById('alert_type_form_waterbody_longitude');
            if (latForm) latForm.value = lat;
            if (lngForm) lngForm.value = lng;
        }
    }

    // Fonction pour ouvrir le modal avec overlay
    function openModal() {
        const modal = document.getElementById('crud-modal');
        let backdrop = document.getElementById('modal-backdrop');
        
        if (!backdrop) {
            backdrop = document.createElement('div');
            backdrop.id = 'modal-backdrop';
            backdrop.className = 'bg-gray-900/50 dark:bg-gray-900/80 fixed inset-0 z-40';
            document.body.appendChild(backdrop);
            
            // Ferme le modal si clic sur le backdrop
            backdrop.addEventListener('click', closeModal);
        }
        
        backdrop.classList.remove('hidden');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.setAttribute('aria-hidden', 'false');
    }

    // Fonction pour fermer le modal et réinitialiser
    function closeModal() {
        const modal = document.getElementById('crud-modal');
        const backdrop = document.getElementById('modal-backdrop');
        
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        modal.setAttribute('aria-hidden', 'true');
        
        if (backdrop) {
            backdrop.classList.add('hidden');
        }
        
        // Réinitialise les champs
        const positionSpan = document.getElementById('position-display');
        const customText = document.getElementById('custom-text');
        const cityInput = document.getElementById('alert_type_form_waterbody_city');
        
        if (positionSpan) positionSpan.textContent = '';
        if (customText) customText.style.display = '';
        if (cityInput) cityInput.value = '';
    }

    // Écouteur sur le bouton de fermeture
    const closeBtn = document.getElementById('close-modal-btn');
    if (closeBtn) {
        closeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            closeModal();
        });
    }

    // Écouteur sur le bouton "Signaler" 
    const signalerBtn = document.getElementById('signaler-btn');
    if (signalerBtn) {
        signalerBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            openModal();
        });
    }

    map.on('click', setAlertFormPosition);
});