document.addEventListener('DOMContentLoaded', function() {
    // on ajoute l'évenement qui permettra de récupérer la localisation au click
    async function setAlertFormPosition(event) {
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
                const citySearchInput = document.getElementById('citySearchInput'); // Input manuel autocomplete
                const cityFormInput = document.querySelector('[name="alert_type_form[waterbody][city]"]'); // Vrai champ Symfony
                const depInput = document.getElementById('alert_type_form_waterbody_department');


                if (citySearchInput) {
                    citySearchInput.value = 'Chargement...';
                }
                
                const cityData = await window.getCityFromCoordinates(lat, lng);                
                if (cityData) {
                    if (cityData.ville) {
                        console.log('Remplissage ville:', cityData.ville);
                        // Remplit l'input de recherche visible
                        if (citySearchInput) {
                            citySearchInput.value = cityData.ville;
                        }
                        // Remplit le vrai champ Symfony 
                        if (cityFormInput) {
                            cityFormInput.value = cityData.ville;
                        }
                    }
                    if (cityData.departement && depInput) {
                        depInput.value = cityData.departement;
                    }
                } else {
                    if (citySearchInput) citySearchInput.value = '';
                    if (cityFormInput) cityFormInput.value = '';
                    console.log('Pas de données de géocodage');
                }
            }

            // remplit les inputs cachés
            const latForm = document.getElementById('alert_type_form_waterbody_latitude');
            const lngForm = document.getElementById('alert_type_form_waterbody_longitude');
            if (latForm) latForm.value = lat;
            if (lngForm) lngForm.value = lng;
        }
    }

    // fonction pour afficher le nb de fichier séléctionnées 
    function updateFileName(input) {
        const fileChosen = document.getElementById('file-chosen');
        if (input.files.length === 0) {
            fileChosen.textContent = 'Aucun fichier sélectionné';
        } else if (input.files.length === 1) {
            fileChosen.textContent = input.files[0].name;
        } else {
            fileChosen.textContent = input.files.length + ' fichiers sélectionnés';
        }
    }

     window.updateFileName = updateFileName;


    // Fonction pour remplir le formulaire depuis les données d'une ville sélectionnée
    function fillFormFromCity(cityData) {
        
        const citySearchInput = document.getElementById('citySearchInput'); 
        const cityFormInput = document.querySelector('[name="alert_type_form[waterbody][city]"]'); 
        const depInput = document.getElementById('alert_type_form_waterbody_department');
        const latInput = document.getElementById('alert_type_form_waterbody_latitude');
        const lngInput = document.getElementById('alert_type_form_waterbody_longitude');
        const positionSpan = document.getElementById('position-display');
        const customText = document.getElementById('custom-text');
        
        console.log('Éléments trouvés:');
        console.log('- citySearchInput:', citySearchInput);
        console.log('- cityFormInput:', cityFormInput);
        console.log('- depInput:', depInput);
        console.log('- latInput:', latInput);
        console.log('- lngInput:', lngInput);
        
        if (cityData.nom) {
            // Remplit l'input manuel (autocomplete)
            if (citySearchInput) {
                citySearchInput.value = cityData.nom;
            }
            // Remplit le vrai champ Symfony 
            if (cityFormInput) {
                cityFormInput.value = cityData.nom;
            } 
        }
        
        if (cityData.departement) {
            if (depInput) {
                depInput.value = cityData.departement.nom || cityData.departement;
            }
        }
        
        if (cityData.centre && cityData.centre.coordinates) {
            const lng = cityData.centre.coordinates[0];
            const lat = cityData.centre.coordinates[1];
            
            if (latInput) {
                latInput.value = lat;
            }
            if (lngInput) {
                lngInput.value = lng;
            }
            
            if (positionSpan) {
                positionSpan.textContent = lat + ', ' + lng;
            }
            if (customText) {
                customText.style.display = 'none';
            }
        }
        
    }

    // Exposer globalement pour que apiGeo.js puisse l'appeler
    window.fillFormFromCity = fillFormFromCity;

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

        document.querySelectorAll('.modal-backdrop, [modal-backdrop], #modal-backdrop').forEach((overlay) => {
            overlay.remove();
        });

        document.body.classList.remove('overflow-hidden');
        document.documentElement.classList.remove('overflow-hidden');
        
        // Réinitialise les champs
        const positionSpan = document.getElementById('position-display');
        const customText = document.getElementById('custom-text');
        const citySearchInput = document.getElementById('citySearchInput'); 
        
        if (positionSpan) positionSpan.textContent = '';
        if (customText) customText.style.display = '';
        if (citySearchInput) citySearchInput.value = '';
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

    const modal = document.getElementById('crud-modal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
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