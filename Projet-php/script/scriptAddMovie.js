function sendFilmRequest(action, formData) {
    formData.append('action', action);
    fetch('../ajax/Movie.php', {
        method: 'POST',
        body: formData  // Retirer explicitement tout header pour laisser le navigateur gérer le Content-Type
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Film added successfully');
            console.log(data.message);
            window.location.href = '../pages/movie.php?idMovie=' + data.id_movie;

        } else {
            alert('Un problème est survenu lors de l\'ajout du film');
            console.error(data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}




function createFilm() {
    // Collecter les données du formulaire
    const title = document.getElementById('title').textContent; // Assurez-vous que c'est la bonne méthode pour récupérer le titre
    const releaseYear = document.querySelector('input[name="release_year"]').value;
    const synopsis = document.querySelector('input[name="synopsis"]').value;
    const director = document.querySelector('input[name="director"]:checked');
    const PosterInput = document.getElementById('file');
    const tags = Array.from(document.querySelectorAll('input[name="tag"]:checked'));
    const actors = Array.from(document.querySelectorAll('input[name="actor"]:checked'));

    // Valider les champs obligatoires
    if (!title || !releaseYear || !synopsis) {
        alert('Veuillez remplir tous les champs requis (titre, année de sortie, synopsis).');
        return;
    }
    if (PosterInput.files.length === 0) {
        alert('Veuillez sélectionner un fichier pour le poster.');
        return;
    }
    if (tags.length === 0) {
        alert('Veuillez sélectionner au moins un tag.');
        return;
    }
    if (!director) {
        alert('Veuillez sélectionner un réalisateur.');
        return;
    }
    if (actors.length === 0) {
        alert('Veuillez sélectionner au moins un acteur.');
        return;
    }

    // Préparer les données à envoyer avec FormData
    const formData = new FormData();
    formData.append('title', title);
    formData.append('releaseDate', releaseYear);
    formData.append('synopsis', synopsis);
    formData.append('directorId', director.value);
    formData.append('seen', 0); 
    formData.append('rating', 0); 
    formData.append('poster', PosterInput.files[0]);
    tags.forEach(tag => formData.append('tags[]', tag.value));
    actors.forEach(actor => formData.append('actors[]', actor.value));

    // Affichage des données formées pour le debug
    console.log('Film data prepared to send:', Object.fromEntries(formData));

    // Envoyer la demande pour ajouter le film
    sendFilmRequest('add', formData);
}











/********************************************************************** */
function sendTagRequest(action, data) {
    data.action = action;
    const formData = new URLSearchParams();
    for (const key in data) {
        formData.append(key, data[key]);
    }

    fetch('../ajax/Tag.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: formData.toString(),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log(data.message);
            console.log('Tag ID:', data.id); 
            createTagCard(data.name, data.id);
            closePopupTag(); 
        } else {
            alert('Un problème est survenu');
            console.error(data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}



function createTagCard(name, id_tag) {
    // Création de l'élément span et définition de sa classe et de son id
    const tagSpan = document.createElement('span');
    tagSpan.className = 'tag';
    tagSpan.id = id_tag;

    // Création de l'input checkbox
    const checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    checkbox.id = `checkbox_${id_tag}`;
    checkbox.name = `tag`;
    checkbox.value = id_tag;
    checkbox.checked = true;  // Cocher la checkbox par défaut

    // Création du label associé à la checkbox
    const label = document.createElement('label');
    label.htmlFor = `checkbox_${id_tag}`;
    label.textContent = name;

    // Ajout de la checkbox et du label au span
    tagSpan.appendChild(checkbox);
    tagSpan.appendChild(label);

    // Ajout du span à l'élément '#tags' du DOM
    const tagsContainer = document.querySelector('#tags');
    tagsContainer.appendChild(tagSpan);
}



function closePopupTag() {
    const popup = document.getElementById('popupTag'); // Sélectionne le popup par sa classe
    if (popup) {
        document.body.removeChild(popup); // Supprime le popup du DOM s'il existe
    }
}
/***************************** */
//director

function sendDirectorRequest(action, data) {
    inputPhoto= data.photo;
    const formData = new FormData();
    formData.append('action', action);
    formData.append('last_name', data.last_name);
    formData.append('first_name', data.first_name);
    formData.append('photo', data.photo);

    fetch('../ajax/Director.php', {
        method: 'POST',
        body: formData 
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            createPersonCard('director',data.last_name, data.first_name, inputPhoto, data.id);
            addEventListenerToCheckboxDirector();
            resetPopupPeople();

        } else {
        alert('Un problème est survenu: ' + data.message);
        console.error(data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}




function createPersonCard(type, last_name, first_name, fileInput, id_person) {
    // Déterminer le conteneur cible en fonction du type
    const container = document.getElementById(type === 'director' ? 'directors' : 'actors');

    // Créer les éléments nécessaires pour la carte
    const cardDiv = document.createElement('div');
    cardDiv.className = `PeopleCard`;

    const checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    checkbox.id = `${type}_${id_person}`;
    checkbox.name = `${type}`;
    checkbox.value = id_person;
    checkbox.checked = true; // Coché par défaut

    const label = document.createElement('label');
    label.htmlFor = `${type}_${id_person}`;

    const img = document.createElement('img');
    // Récupérer l'image depuis l'input file
    const reader = new FileReader();
    reader.onload = function(e) {
        img.src = e.target.result; // Définir le résultat du FileReader comme source de l'image
    };
    reader.readAsDataURL(fileInput); // Lire le fichier comme URL de données

    const span = document.createElement('span');
    span.textContent = `${first_name} ${last_name}`;

    // Assembler les éléments
    label.appendChild(img);
    label.appendChild(span);
    cardDiv.appendChild(checkbox);
    cardDiv.appendChild(label);

    // Ajouter la nouvelle carte au conteneur
    container.appendChild(cardDiv);
}



/***************************** */
//actor


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
            createPersonCard('actor',data.last_name, data.first_name, inputPhoto, data.id);
            addEventListenerToCheckboxDirector();
            resetPopupPeople();

        } else {
        alert('Un problème est survenu: ' + data.message);
        console.error(data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

function addEventListenerToCheckboxDirector(){
    const directorCheckboxes = document.querySelectorAll('#directors input[type="checkbox"]');

    directorCheckboxes.forEach(function(checkbox) {//si rélaistaeur coché, alors décoche les autres
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                directorCheckboxes.forEach(function(box) {
                    if (box !== checkbox) {
                        box.checked = false;
                    }
                });
            }
        });
    });
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




document.addEventListener("DOMContentLoaded", function() {
    addEventListenerToCheckboxDirector();

    const newTagButton = document.getElementById('newTag');
    newTagButton.addEventListener('click', function() {
        // Créer le popup
        const popup = document.createElement('div');
        popup.id = 'popupTag';  // Utilisation de la classe pour le style

        const input = document.createElement('input');
        input.type = 'text';
        input.placeholder = 'Nouveau tag';

        const addButton = document.createElement('button');
        addButton.textContent = 'Ajouter';
        addButton.onclick = function() {
            if(input.value == ''){
                alert("Veuillez entrer un tag");
                return;
            }
            sendTagRequest('add', { name: input.value });

        };

        const closeButton = document.createElement('button');
        closeButton.textContent = 'Fermer';
        closeButton.onclick = function() {
            document.body.removeChild(popup); // Ferme le popup
        };

        // Ajouter les éléments au popup
        popup.appendChild(input);
        popup.appendChild(addButton);
        popup.appendChild(closeButton);

       

        // Ajouter le popup au corps du document
        document.body.appendChild(popup);

       
    });


    //partie form people
    //director
    document.getElementById('newDirector').addEventListener('click', function(){
        const elt = document.getElementById('popupPeople');
         document.getElementById('titleFormPeople').textContent = "Ajouter un réalisateur";
        elt.className ='director';
        elt.style.display = 'block';
    });


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






    //actor
    document.getElementById('newActor').addEventListener('click', function(){
        const elt = document.getElementById('popupPeople');
         document.getElementById('titleFormPeople').textContent = "Ajouter un acteur";
        elt.className ='actor';
        elt.style.display = 'block';
    });



    
});



