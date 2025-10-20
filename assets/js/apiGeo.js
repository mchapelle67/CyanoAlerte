// initialisation du DOM
document.addEventListener('DOMContentLoaded', function() {
    const inputCity = document.querySelector('.city');
    const resultsList = document.querySelector('#cityResults');
    
    // Création de l'élément d'erreur
    const errorMsg = document.querySelector('#error-message') || document.createElement('div');
    if (!errorMsg.id) {
        errorMsg.id = 'error-message';
        errorMsg.style.color = 'red';
        inputCity.parentNode.insertBefore(errorMsg, inputCity.nextSibling);
    }

    // Fonction helper pour gérer les erreurs
    function handleError(error, userMessage) {
        console.error('Erreur détaillée:', error);
        errorMsg.textContent = userMessage;
        setTimeout(() => {
            errorMsg.textContent = '';
        }, 5000);
    }

    // Fonction pour mettre à jour le marqueur
    function updateMarker(city) {
        if (!window.marker || !window.map) {
            handleError(
                new Error('Carte non initialisée'),
                'La carte n\'est pas encore chargée. Veuillez rafraîchir la page.'
            );
            return;
        }

        try {
            window.marker.setLatLng([city.centre.coordinates[1], city.centre.coordinates[0]]);
            window.map.setView([city.centre.coordinates[1], city.centre.coordinates[0]], 13);
        } catch (error) {
            handleError(error, 'Erreur lors de la mise à jour de la carte.');
        }
    }

    // Cache la liste des résultats si on clique ailleurs
    document.addEventListener('click', (e) => {
        if (!inputCity.contains(e.target) && !resultsList.contains(e.target)) {
            resultsList.classList.add('hidden');
        }
    });

    // ajoute un écouteur d'événement "input" (pendant la saisie) au champ 
    inputCity.addEventListener("input", () => {
        // efface les erreurs précédentes
        errorMsg.textContent = '';
        
        // récupère la valeur entrée dans le champ 
        let value = inputCity.value;
        
        // Cacher la liste si le champ est vide
        if (!value.trim()) {
            resultsList.classList.add('hidden');
            return;
        }
        
        // effectue une requête fetch vers l'API de géolocalisation 
        fetch(`https://geo.api.gouv.fr/communes?nom=${value}&fields=code,nom,contour,departement,codesPostaux,centre&boost=population`)
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`Erreur HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then((data) => {
                console.log(data);
                if (!data || data.length === 0) {
                    handleError(new Error('Aucun résultat'), 'Aucune ville trouvée avec ce nom.');
                    return;
                }

                // Vide la liste des résultats
                resultsList.innerHTML = '';
                
                // Affiche les résultats
                data.forEach((city) => {
                    if (!city.centre || !Array.isArray(city.centre.coordinates) || city.centre.coordinates.length !== 2) {
                        return; // Ignore les villes sans coordonnées valides
                    }

                    const li = document.createElement('li');
                    li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';
                    li.textContent = `${city.nom} (${city.departement.code})`;
                    
                    // Quand on clique sur une ville
                    li.addEventListener('click', () => {
                        inputCity.value = city.nom;
                        resultsList.classList.add('hidden');
                        updateMarker(city);
                    });
                    
                    resultsList.appendChild(li);
                });
                
                // Affiche la liste si on a des résultats
                if (data.length > 0) {
                    resultsList.classList.remove('hidden');
                } else {
                    resultsList.classList.add('hidden');
                }
            })
            .catch((error) => {
                handleError(error, 'Erreur lors de la recherche. Veuillez réessayer plus tard.');
            });
    });
});