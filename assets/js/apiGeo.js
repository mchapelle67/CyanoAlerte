// Initialisation du DOM
document.addEventListener('DOMContentLoaded', function() {
    const inputZone = document.querySelector('.zone');
    const resultsList = document.querySelector('#zoneResults');
    
    // Création de l'élément d'erreur
    const errorMsg = document.querySelector('#error-message') || document.createElement('div');
    if (!errorMsg.id) {
        errorMsg.id = 'error-message';
        errorMsg.style.color = 'red';
        inputZone.parentNode.insertBefore(errorMsg, inputZone.nextSibling);
    }

    // Fonction helper pour gérer les erreurs
    function handleError(error, userMessage) {
        console.error('Erreur détaillée:', error);
        errorMsg.textContent = userMessage;
        setTimeout(() => {
            errorMsg.textContent = '';
        }, 5000);
    }

    // Fonction pour zoomer sur la zone sélectionnée 
    function updateZone(zone, type = 'commune') {
    try {
        if (type === 'commune') {
            window.map.setView([zone.centre.coordinates[1], zone.centre.coordinates[0]], 13);
        } else if (type === 'departement') {
            // récupérer la commune la plus peuplée du département
            fetch(`https://geo.api.gouv.fr/departements/${zone.code}/communes?fields=nom,centre,population&boost=population&limit=1`)
                .then(response => response.json())
                .then(communes => {
                    if (communes.length > 0 && communes[0].centre && communes[0].centre.coordinates) {
                        const commune = communes[0];
                        window.map.setView([commune.centre.coordinates[1], commune.centre.coordinates[0]], 8);
                    } else {
                        window.map.setView([46.603354, 1.888334], 6); // on recentre sur la france
                    }
                })
                .catch(error => {
                    handleError(error, 'Erreur lors du chargement de la commune la plus peuplée.');
                });
        }
    } catch (error) {
        handleError(error, 'Erreur lors de la mise à jour de la carte.');
    }
}

    // fonction de géocodage inversé : récupère la ville et le département depuis les coordonnées GPS
    async function getCityFromCoordinates(lat, lon) {
        // async function et await remplace fetch et .then 
        try {
            const url = `https://geo.api.gouv.fr/communes?lat=${lat}&lon=${lon}&fields=nom,code,codeDepartement,departement&format=json`;            
            const response = await fetch(url);
            
            if (!response.ok) {
                throw new Error(`Erreur HTTP : ${response.status}`);
            }
            
            const communes = await response.json();
            
            if (communes.length > 0) {
                const commune = communes[0];
                console.log('Commune:', commune);
                console.log('Département:', commune.departement);
                
                return {
                    ville: commune.nom,
                    codeDepartement: commune.codeDepartement,
                    departement: commune.departement ? commune.departement.nom : commune.codeDepartement || null
                };
            } else {
                throw new Error('Aucune commune trouvée pour ces coordonnées.');
            }
        } catch (error) {
            console.error('Erreur lors du géocodage inversé:', error);
            handleError(error, 'Impossible de trouver la ville pour ces coordonnées.');
            return null;
        }
    }

    // exposer la fonction globalement pour l'utiliser dans d'autres fichiers JS
    window.getCityFromCoordinates = getCityFromCoordinates;

    // cache la liste des résultats si on clique ailleurs
    document.addEventListener('click', (e) => {
        if (!inputZone.contains(e.target) && !resultsList.contains(e.target)) {
            resultsList.classList.add('hidden');
        }
    });

    // ajoute un écouteur d'événement "input" (pendant la saisie) au champ 
    inputZone.addEventListener("input", () => {
        // efface les erreurs précédentes
        errorMsg.textContent = '';
        
        // récupère la valeur entrée dans le champ 
        let value = inputZone.value;
        
        // Cacher la liste si le champ est vide
        if (!value.trim()) {
            resultsList.classList.add('hidden');
            return;
        }
        
        // effectue les requêtes fetch vers les deux APIs en parallèle
        if (value.trim()) {
            Promise.all([
                fetch(`https://geo.api.gouv.fr/communes?nom=${value}&fields=code,nom,contour,departement,codesPostaux,centre&boost=population`)
                    .then(response => response.json()),
                fetch(`https://geo.api.gouv.fr/departements?nom=${value}`)
                    .then(response => response.json())
            ])
            .then(([communesData, departementsData]) => {
                // Fonction pour vérifier si le nom correspond exactement
                const exactMatch = (item, searchValue) => 
                    item.nom.toLowerCase() === searchValue.toLowerCase();

                // Chercher d'abord une correspondance exacte dans les départements
                const exactDeptMatch = departementsData.find(dept => 
                    exactMatch(dept, value));

                if (exactDeptMatch) {
                    // Si on trouve un département qui correspond exactement
                    displayResults([exactDeptMatch], 'departement');
                } else if (communesData.length > 0) {
                    // Si on trouve des communes
                    displayResults(communesData, 'commune');
                } else if (departementsData.length > 0) {
                    // Si on trouve des départements (correspondance partielle)
                    displayResults(departementsData, 'departement');
                } else {
                    handleError(new Error('Aucun résultat'), 'Aucune ville ou département trouvé avec ce nom.');
                }
            })
            .catch(error => {
                handleError(error, 'Erreur lors de la recherche. Veuillez réessayer plus tard.');
            });

                // Fonction pour afficher les résultats
                function displayResults(data, type) {
                    resultsList.innerHTML = '';
                    
                    data.forEach((item) => {
                        const li = document.createElement('li');
                        li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';
                        
                        if (type === 'commune') {
                            if (!item.centre || !Array.isArray(item.centre.coordinates) || item.centre.coordinates.length !== 2) {
                                return; // Ignore les villes sans coordonnées valides
                            }
                            li.textContent = `${item.nom}`;
                            li.addEventListener('click', () => {
                                inputZone.value = item.nom;
                                resultsList.classList.add('hidden');
                                updateZone(item);
                            });
                        } else if (type === 'departement') {
                            li.textContent = `${item.nom}`;
                            li.addEventListener('click', () => {
                                inputZone.value = item.nom;
                                resultsList.classList.add('hidden');
                                updateZone(item, 'departement');
                            });
                        }
                        
                        resultsList.appendChild(li);
                    });
                    
                    if (data.length > 0) {
                        resultsList.classList.remove('hidden');
                    } else {
                        resultsList.classList.add('hidden');
                    }
                }
        }
    });
});