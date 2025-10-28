// Initialisation du DOM
document.addEventListener('DOMContentLoaded', function() {
    const inputZone = document.querySelector('.zone');
    const resultsList = document.querySelector('#zoneResults');
    const inputCity = document.querySelector('#citySearchInput'); 
    const resultsCity = document.querySelector('#cityResults');


    // ============== FONCTIONS UTILITAIRES ==============
    
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
                        console.error('Erreur lors du chargement de la commune la plus peuplée:', error);
                    });
            }
        } catch (error) {
            console.error('Erreur lors de la mise à jour de la carte:', error);
        }
    }

    // Fonction de géocodage inversé : récupère la ville et le département depuis les coordonnées GPS
    async function getCityFromCoordinates(lat, lon) {
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
            return null;
        }
    }

    // Exposer globalement pour utilisation dans d'autres fichiers JS
    window.getCityFromCoordinates = getCityFromCoordinates;

    // Fonction pour afficher les résultats de recherche
    function displayResults(data, type, inputElement, resultsElement, onClickCallback) {
        resultsElement.innerHTML = '';
        
        data.forEach((item) => {
            // Validation des coordonnées pour les communes
            if (type === 'commune' && (!item.centre || !Array.isArray(item.centre.coordinates) || item.centre.coordinates.length !== 2)) {
                return; // Ignore les villes sans coordonnées valides
            }
            
            const li = document.createElement('li');
            li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';
            li.textContent = item.nom;
            
            // Gestion du clic sur l'élément
            li.addEventListener('click', () => {
                inputElement.value = item.nom;
                resultsElement.classList.add('hidden');
                if (onClickCallback) {
                    onClickCallback(item, type);
                }
            });
            
            resultsElement.appendChild(li);
        });
        
        // Afficher ou cacher la liste selon le nombre de résultats
        if (data.length > 0) {
            resultsElement.classList.remove('hidden');
        } else {
            resultsElement.classList.add('hidden');
        }
    }

    // Fonction pour créer un autocomplete
    function setupAutocomplete(inputElement, resultsElement, options = {}) {
        const { 
            includeDepartments = false,  // Chercher aussi dans les départements
            onSelectCallback = null      // Fonction à appeler lors de la sélection
        } = options;

        inputElement.addEventListener("input", () => {
            const value = inputElement.value;
            
            // Cacher la liste si le champ est vide
            if (!value.trim()) {
                resultsElement.classList.add('hidden');
                return;
            }
            
            // Préparer les requêtes fetch
            const fetchPromises = [
                fetch(`https://geo.api.gouv.fr/communes?nom=${value}&fields=code,nom,contour,departement,codesPostaux,centre&boost=population`)
                    .then(response => response.json())
            ];
            
            // Ajouter la recherche départements si activée
            if (includeDepartments) {
                fetchPromises.push(
                    fetch(`https://geo.api.gouv.fr/departements?nom=${value}`)
                        .then(response => response.json())
                );
            }
            
            // Exécuter les requêtes
            Promise.all(fetchPromises)
                .then((results) => {
                    const communesData = results[0];
                    const departementsData = includeDepartments ? results[1] : [];
                    
                    if (includeDepartments) {
                        // Logique avec départements (pour la recherche carte)
                        const exactMatch = (item, searchValue) => 
                            item.nom.toLowerCase() === searchValue.toLowerCase();
                        
                        const exactDeptMatch = departementsData.find(dept => 
                            exactMatch(dept, value));
                        
                        if (exactDeptMatch) {
                            displayResults([exactDeptMatch], 'departement', inputElement, resultsElement, onSelectCallback);
                        } else if (communesData.length > 0) {
                            displayResults(communesData, 'commune', inputElement, resultsElement, onSelectCallback);
                        } else if (departementsData.length > 0) {
                            displayResults(departementsData, 'departement', inputElement, resultsElement, onSelectCallback);
                        } else {
                            console.log('Aucune ville ou département trouvé avec ce nom.');
                        }
                    } else {
                        // Logique sans départements (pour le formulaire modal)
                        if (communesData.length > 0) {
                            displayResults(communesData, 'commune', inputElement, resultsElement, onSelectCallback);
                        } else {
                            console.log('Aucune ville trouvée avec ce nom.');
                        }
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la recherche:', error);
                });
        });
    }

    // ============== GESTION DES CLICS EXTÉRIEURS ==============
    
    // Cache les listes déroulantes si on clique ailleurs
    document.addEventListener('click', (e) => {
        // Vérifier inputZone et resultsList
        if (inputZone && resultsList && !inputZone.contains(e.target) && !resultsList.contains(e.target)) {
            resultsList.classList.add('hidden');
        }
        // Vérifier inputCity et resultsCity
        if (inputCity && resultsCity && !inputCity.contains(e.target) && !resultsCity.contains(e.target)) {
            resultsCity.classList.add('hidden');
        }
    });

    // ============== INITIALISATION DES AUTOCOMPLETES ==============
    
    // Autocomplete pour la recherche avec zoom sur la carte
    if (inputZone && resultsList) {
        setupAutocomplete(inputZone, resultsList, {
            includeDepartments: true,  // Recherche communes + départements
            onSelectCallback: updateZone  // Zoom sur la carte lors de la sélection
        });
    }

    // Autocomplete pour le formulaire du modal (sans zoom carte)
    if (inputCity && resultsCity) {
        setupAutocomplete(inputCity, resultsCity, {
            includeDepartments: false,  // Recherche uniquement communes
            onSelectCallback: function(cityData, type) {
                // Appelle la fonction du formulaire pour remplir les champs
                if (window.fillFormFromCity && type === 'commune') {
                    window.fillFormFromCity(cityData);
                }
            }
        });
    }
});