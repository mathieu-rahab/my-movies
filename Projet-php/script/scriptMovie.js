function getParamInUrl(param) {//permet de recupéré l'id du film
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has(param)) {
        var value = urlParams.get(param);
        if (value == 'null') return null;
        return value;
    } else {
        return null;
    }
}


function updateFilmStatus(viewed, rating) {
    let movieId = getParamInUrl('idMovie');
    if(movieId == null) return;
    fetch('../ajax/updateFilmStatus.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${movieId}&viewed=${viewed}&rating=${rating}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                //alert('Mise à jour réussie : ' + data.message);
            } else {
                alert('Erreur : ' + data.message);
            }
        })
        .catch(error => console.error('Erreur:', error));
}



function createStarRating() {
    // Créer un élément div avec la classe "star-rating"
    if(document.getElementById('star-rating') != null) return null;

    const starRatingDiv = document.createElement('div');
    starRatingDiv.id = 'star-rating';

    // Boucle pour créer et ajouter les éléments span pour les étoiles
    for (let i = 1; i <= 5; i++) {
        const starSpan = document.createElement('span');
        starSpan.className = 'star';
        starSpan.setAttribute('data-value', i);
        starSpan.textContent = '★';
        starRatingDiv.appendChild(starSpan);
    }

    // Ajouter l'élément star-rating à la fin de l'élément userInfo
    const userInfo = document.getElementById('userInfo');
    userInfo.appendChild(starRatingDiv);
    rating();

}



function rating() {
    try {
        const stars = document.querySelectorAll('.star');
        stars.forEach(star => {
            star.addEventListener('mouseover', function () {
                const value = this.getAttribute('data-value');
                stars.forEach(s => {
                    if (s.getAttribute('data-value') <= value) {
                        s.classList.add('selected');
                    } else {
                        s.classList.remove('selected');
                    }
                });
            });

            star.addEventListener('mouseout', function () {
                stars.forEach(s => s.classList.remove('selected'));
            });

            star.addEventListener('click', function (event) {
                if(!userIsAdmin()) return;
                let rating = event.target.getAttribute('data-value');
                updateFilmStatus(1, rating);
                if (rating >= 0 && rating <= 5) {
                    //ajouté requete pour modifié rating
                    stars.forEach(s => s.classList.remove('note'));
                    stars.forEach(s => {
                        if (s.getAttribute('data-value') <= rating) {
                            s.classList.add('note');
                        }
                    });

                }
            })

        });
    }
    catch (e) {
        console.log('Une erreur est survenue');
    }
}


function clearStarRating() {
    // Trouver l'élément star-rating par son ID
    const starRatingDiv = document.getElementById('star-rating');
    // Si l'élément star-rating existe, le supprimer
    if (starRatingDiv) {
        starRatingDiv.parentNode.removeChild(starRatingDiv);
    } else {
        console.error('Element with ID "star-rating" not found.');
    }
}






function userIsAdmin(){
    if(document.getElementById('adminTools') == null){
        alert("Vous n'avez pas les droits pour modifier ce film, connecté vous");
        return false;
    }
    return true;
}


function setToogle(seen){
    console.log('setToogle', seen)
    toogle = document.getElementById('toogle');
    if(seen){
        toogle.classList.add('seen');
    }
    else{
        toogle.classList.remove('seen');
        clearStarRating();
    }
}

/****************************************************************************** */
//requete AJAX

async function updateMovie(movieId, movieData) {
    let formData = new FormData();
    for (const [key, value] of Object.entries(movieData)) {
        formData.append(key, value);
    }
    formData.append('action', 'update');
    formData.append('id', movieId);

    try {
        const response = await fetch('../ajax/Movie.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        if (data.success) {
            console.log('Film updated successfully');
            console.log(data.message);
            return true;  // Retourne true si la mise à jour est un succès
        } else {
            console.error('Un problème est survenu lors de la mise à jour du film');
            console.error(data.message);
            return false;  // Retourne false en cas d'échec
        }
    } catch (error) {
        console.error('Error:', error);
        return false;  // Retourne false en cas d'erreur de réseau ou autre
    }
}

/* ***************************Gestion de l'ajout d'un acteur **************** */

function sendActorRequest(action, data) {
    inputPhoto= data.photo;
    const formData = new FormData();
    formData.append('action', action);
    formData.append('last_name', data.last_name);
    formData.append('first_name', data.first_name);
    formData.append('photo', data.photo);

    fetch('../ajax/Actor.php', {
        method: 'POST',
        body: formData 
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            addActorToMovie(Number(getParamInUrl('idMovie')), data.id);

        } else {
        alert('Un problème est survenu: ' + data.message);
        console.error(data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}





function addActorToMovie(filmId, actorId) {
        fetch('../ajax/Movie.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'addActorToMovie',
                id_movie: filmId,
                id_actor: actorId
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(text => {
            try {
                const data = JSON.parse(text);
                console.log('Success:', data);
            } catch (error) {
                throw new Error('Invalid JSON: ' + text);
            }
        })
        .catch((error) => {
            console.error('Error:', error);
        });
    }



/* *****************Gestion des tags********************* */

function addTagElement(idTag, tagName) {
    //crée un block pour le nouveau tag ajouté
    // Sélectionner le conteneur où ajouter le nouveau tag
    const tagsContainer = document.querySelector('.tags');

    // Créer un nouvel élément span
    const span = document.createElement('span');
    span.className = 'tag update updateAdmin'; // Ajouter les classes
    span.id = idTag; // Définir l'ID du tag
    span.textContent = tagName; // Définir le nom du tag comme contenu textuel

    // Ajouter le nouvel élément span au conteneur de tags
    tagsContainer.appendChild(span);
}




async function addTagToFilm(movieId, tagName) {
    // Créer un objet FormData pour envoyer les données POST
    let formData = new FormData();
    formData.append('action', 'addTagToFilm');
    formData.append('id_movie', movieId);
    formData.append('tag_name', tagName);

    try {
        // Envoyer la requête POST au script PHP
        const response = await fetch('../ajax/Movie.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            console.log('Tag ajouté avec succès:', data.message);
            addTagElement(data.tag_id, data.tag_name);
            addAdminUpdateClass();
            return true;
        } else {
            alert('Erreur lors de l\'ajout du tag: ' + data.message);
            return false;
        }
    } catch (error) {
        alert('Erreur lors de l\'envoi de la requête:', error);
        return false;
    }
}


async function removeTagFromFilm(movieId, tagId) {
    let formData = new FormData();
    formData.append('action', 'removeTag');
    formData.append('id_movie', movieId);
    formData.append('id_tag', tagId);

    try {
        const response = await fetch('../ajax/Movie.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.success) {
            console.log(data.message);
            const tagElement = document.getElementById(`tag-${tagId}`);
            if (tagElement) tagElement.remove();
            return true;
        } else {
            console.error('Erreur lors de la suppression du tag:', data.message);
            return false;
        }
    } catch (error) {
           console.error('Error:', error);
        return false;
    }
}



/* *****************Gestion modification title, date, synopsys ********************* */


async function validatePopup(elementId, value) {
    //validation du pop up pour modifcation de title, date, synopsys
        console.log('Validating:', elementId, value);
        if (value == '') {
            alert('Veuillez entrer une valeur');
            return;
        }

        let movieId = getParamInUrl('idMovie');
        if (movieId == null) return;

        let fieldName;
        switch (elementId) {
            case 'titleFilm':
                fieldName = 'title';
                if(await updateMovie(movieId, { title: value })){
                    document.getElementById('titleFilm').textContent = value;
                    destroyPopup()
                }
                break;
            case 'realiseDate':
                fieldName = 'release_date';
                if(await updateMovie(movieId, { release_date: value })){
                    document.getElementById('realiseDate').textContent = value;
                    destroyPopup()
                }
                break;
            case 'synopsis':
                fieldName = 'synopsis';
                if(await updateMovie(movieId, { synopsis: value })){
                    document.getElementById('synopsis').textContent = value;
                    destroyPopup()
                }
                break;
            default:
                console.log('Element inconnu:', elementId);
                return; // Sortir de la fonction si l'elementId n'est pas reconnu
        }
    }





/*************************************** */

function destroyPopup() {
    const popups = document.querySelectorAll('.popup');


    popups.forEach(popup => {
        if (popup.parentNode) {
            popup.parentNode.removeChild(popup);
        }
    });
}



function createPopup(elementId) {
    //gere la création du pop pour la modification du titre, de la date de sortie ou du synopsis
    destroyPopup();
    // Créer l'élément de base pour le popup
    const popup = document.createElement('div');
    popup.classList.add('popup');

    // Déterminer le contenu basé sur l'ID
    let inputType = '';
    let inputPlaceholder = '';
    let inputValue = '';
    switch (elementId) {
        case 'titleFilm':
            inputType = 'text';
            inputPlaceholder = 'Enter film title';
            inputValue = (document.getElementById('titleFilm').textContent).trim();
            
            break;
        case 'realiseDate':
            inputType = 'date';
            break;

        case 'synopsis':
            inputType = 'text';
            inputPlaceholder = 'synopsis';
            inputValue = (document.getElementById('synopsis').textContent).trim();


        break;
        default:
            console.log('Element inconnu:', elementId);
    }

    // Ajouter un input
    const input = document.createElement('input');
    input.type = inputType;
    input.placeholder = inputPlaceholder;
    input.value = inputValue;
    popup.appendChild(input);

    // Ajouter un bouton de validation
    const validateButton = document.createElement('button');
    validateButton.textContent = 'Validate';
    validateButton.addEventListener('click', function() {
        validatePopup(elementId, input.value);
    });
    popup.appendChild(validateButton);

    // Ajouter un bouton de fermeture
    const closeButton = document.createElement('button');
    closeButton.textContent = 'Close';
    closeButton.addEventListener('click', function() {
        document.body.removeChild(popup);
    });
    popup.appendChild(closeButton);

    // Afficher le popup
    document.body.appendChild(popup);
}




function eventAdminUpdate(event) {
    if (event.target.classList.contains('updateAdmin')) {
        console.log("L'élément a la classe 'updateAdmin'");
        const id = event.target.getAttribute('id');
        console.log(id);
        createPopup(id);  // Appeler la fonction de création de popup
    } else {
        console.log("L'élément n'a pas la classe 'updateAdmin'");
    }
}




async function validatePopupTag(value){
    if(value == ''){
        alert( 'Veuillez entrer une valeur');
        return;
    }
    let movieId = getParamInUrl('idMovie');
    if(await addTagToFilm(movieId, value)){
        console.log('Tag ajouté avec succès:', value);
        destroyPopup();
    }
    
    
}

function createPopupTag(){
    destroyPopup();
    // Créer l'élément de base pour le popup
    const popup = document.createElement('div');
    popup.classList.add('popup');

    // Déterminer le contenu basé sur l'ID
    let inputType = 'text';
    let inputPlaceholder = 'nom du tag à ajouté';
    
    // Ajouter un input
    const input = document.createElement('input');
    input.type = inputType;
    input.placeholder = inputPlaceholder;
    popup.appendChild(input);

    // Ajouter un bouton de validation
    const validateButton = document.createElement('button');
    validateButton.textContent = 'Validate';
    validateButton.addEventListener('click', function() {
        validatePopupTag(input.value);
    });
    popup.appendChild(validateButton);

    // Ajouter un bouton de fermeture
    const closeButton = document.createElement('button');
    closeButton.textContent = 'Close';
    closeButton.addEventListener('click', function() {
        document.body.removeChild(popup);
    });
    popup.appendChild(closeButton);

    // Afficher le popup
    document.body.appendChild(popup);
}





function eventAdminUpdateTag(event) {
    const id = event.target.getAttribute('id');
    createPopupTag(id);  // Appeler la fonction de création de popup
    
}


function popUpRemoveTagFromMovie(event) {
    // Récupérer l'ID de l'élément sur lequel le clic a été effectué
    const tagElement = event.target;
    const idTag = tagElement.getAttribute('id');
    const idMovie = getParamInUrl('idMovie');
 
    if (confirm(`Êtes-vous sûr de vouloir retirer "${tagElement.textContent}" du film ?`)) {
        removeTagFromFilm(idMovie, idTag).then(success => {
            if (success) {
                console.log('Le tag a été retiré avec succès du film.');
                tagElement.remove(); 
            } else {
                console.log('La tentative de retirer un tag du film a échoué.');
            }
        });
    } else {
        // Si l'utilisateur annule, rien ne se passe
        console.log('Suppression annulée.');
    }
}





function addAdminUpdateClass() {
    /*
    Fonction qui gère les element de modification pour l'admin
    */
    const updateElements = document.querySelectorAll('.update');

    // Parcourir chaque élément, ajouter la classe 'updateAdmin', et ajouter un écouteur d'événement
    updateElements.forEach(element => {
        const id = element.getAttribute('id');
        element.classList.add('updateAdmin');

        if (element.classList.contains('tag')) {//rajouté fonction pour géré tag
            element.addEventListener('click', popUpRemoveTagFromMovie);
        }
        else if(element.classList.contains('actor')){
            console.log('actor');
        }
        else if(element.classList.contains('director')){
            console.log('director');

        }

        else{
        element.addEventListener('click', eventAdminUpdate);
        }

    });
    createAddTagButton();
    createAddActorButton();
}



function createAddTagButton() {
    console.log('Bouton add tag');
    // Vérifier si le bouton existe déjà
    if (!document.getElementById('addTagButton')) {
        // Créer le bouton
        var button = document.createElement('button');
        button.id = 'addTagButton';  // Corriger l'ID pour qu'il soit cohérent
        button.textContent = 'Add Tag';

        // Ajouter l'écouteur d'événement
        button.addEventListener('click', eventAdminUpdateTag);

        // Récupérer le premier élément avec la classe 'tags'
        var tagsContainer = document.querySelector('.tags');
        if (tagsContainer) {
            // Ajouter le bouton au début du bloc 'tags'
            tagsContainer.insertBefore(button, tagsContainer.firstChild);
        } 
    } 
}



function eventAdminAddActor(event){
    const elt = document.getElementById('popupPeople');
     document.getElementById('titleFormPeople').textContent = "Ajouter un acteur";
    elt.className ='actor';
    elt.style.display = 'block';
}


function resetPopupPeople() {
    // Récupérer le formulaire dans le popup
    var form = document.getElementById('directorForm');

    // Réinitialiser les valeurs des champs du formulaire
    form.reset();

    // Masquer le popup en modifiant le style display
    var popup = document.getElementById('popupPeople');
    popup.style.display = 'none';
}



function createAddActorButton() {
    console.log('Bouton add tag');
    // Vérifier si le bouton existe déjà
    console.log('creation')
    if (!document.getElementById('addActorButton')) {
        // Créer le bouton
        var button = document.createElement('button');
        button.id = 'addActorButton';  // Corriger l'ID pour qu'il soit cohérent
        button.textContent = 'Add actor';

        // Ajouter l'écouteur d'événement
        button.addEventListener('click', eventAdminAddActor);

        // Récupérer le premier élément avec la classe 'tags'
        var tagsContainer = document.querySelector('.acteur');
        if (tagsContainer) {
            // Ajouter le bouton au début du bloc 'tags'
            tagsContainer.insertBefore(button, tagsContainer.firstChild);
        } 
        else{
            console.log('pas trouvé')
        }
    } 
}



document.addEventListener("DOMContentLoaded", function () {


    //gestion du toogle de visionage du film
    const toogle = document.getElementById('toogle');
    const toSeen = document.getElementById('toSeen');
    const seen = document.getElementById('seen');

        
    toSeen.addEventListener('click', function () {
        if (userIsAdmin() && toogle.className == "seen") {
            //mettre requete pour mettre viewed = false
            updateFilmStatus(0, 0);
            setToogle(false);
        }
    });

    seen.addEventListener('click', function () {
        if (userIsAdmin() && toogle.className != "seen") {
            //mettre requete pour mettre viewed = true
            createStarRating();
            updateFilmStatus(1, 0);
            setToogle(true);
        }
    });

     if(document.getElementById('star-rating') != null){//si l'utilisateur à déja vu le film , mettre les écouteur d'évenement
         rating();
     }

//rating();



    //gestion vérification formulaire nouvel personne

    document.getElementById('buttonFormPeople').addEventListener('click', function(event){
        event.preventDefault(); // Empêche la soumission automatique du formulaire
    console.log('clique')
        // Récupérer les éléments et leurs valeurs
        const lastName = document.getElementById('lastName').value.trim();
        const firstName = document.getElementById('firstName').value.trim();
        const fileInput = document.getElementById('photoPeople');

        console.log(fileInput.files)
        // Vérification des champs un par un avec des messages spécifiques
        if (!lastName) {
            alert('Le champ "Nom de famille" est requis.');
            return;
        }

        if (!firstName) {
            alert('Le champ "Prénom" est requis.');
            return;
        }

        if (fileInput.files.length === 0) {
            alert('Veuillez sélectionner un fichier pour la photo.');
            return;
        }

        // Si tous les champs sont validés, continuez avec la logique conditionnelle
        const elt = document.getElementById('popupPeople');
        if (elt.classList.contains('director')) {
            console.log('Director:', { lastName, firstName, photo: fileInput.files[0].name });
            sendDirectorRequest('add', {
                last_name: lastName,
                first_name: firstName,
                photo: fileInput.files[0]
            });
        } else if (elt.classList.contains('actor')) {
            console.log('Actor:', { lastName, firstName, photo: fileInput.files[0].name });
            sendActorRequest('add', {
                last_name: lastName,
                first_name: firstName,
                photo: fileInput.files[0]
            });
        }
    });
    






    

    //gestion admin tools
    if(userIsAdmin()){
        document.getElementById('updateMovie').addEventListener('click', function(){
            addAdminUpdateClass();
        });
        
        
        
    }
    

    
});

