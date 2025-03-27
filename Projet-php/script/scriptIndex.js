function isMobileDevice() {
    // Vérifie les agents utilisateurs courants des téléphones mobiles
    const userAgent = navigator.userAgent || navigator.vendor || window.opera;
    return /android|avantgo|blackberry|bada\/|bb|meego|mobile|iphone|ipod|iemobile|opera mini|phone|tablet|fennec|webos|wosbrowser|kindle|silk|maemo|symbian|windows phone|palm|nokia|brew|netfront|nintendo|playstation/i.test(userAgent.toLowerCase());
  }



  function openDetailsFilm(idFilm){
    //permet d'ouvrir la page détails d'un film par rapport à l'id
    console.log(idFilm)
    //à complété !!!!
    

  }


document.addEventListener("DOMContentLoaded", function() {
    //écouteur d'évenement pour le click sur un film
    document.querySelectorAll('.film').forEach(film => {
        film.addEventListener('click', function(event){
            openDetailsFilm(film.id);
        })
    })
    

   
   //gestion des flèche pour naviguer dans une catégorie
    const scrollAmount = parseInt(getComputedStyle(document.documentElement).getPropertyValue("--widthContenerFilm")) * 2; // largeur d'un film + margin (20px each side)

    document.querySelectorAll(".categorie").forEach(category => {
        const filmsContainer = category.querySelector(".films");
        const leftButton = category.querySelector(".contenerButtonTranslate.left");
        const rightButton = category.querySelector(".contenerButtonTranslate.right");

  

        // Fonction pour masquer les boutons
        function hideButtons() {
            leftButton.style.display = "none";
            rightButton.style.display = "none";
        }

        // Ajouter un écouteur d'événement de survol pour afficher les boutons
        category.addEventListener("mouseenter", toggleScrollButtons);

        // Ajouter un écouteur d'événement de sortie pour masquer les boutons
        category.addEventListener("mouseleave", hideButtons);

        // Ajouter un écouteur d'événement de clic pour le bouton gauche
        leftButton.addEventListener("click", () => {
            filmsContainer.scrollBy({
                left: -scrollAmount,
                behavior: "smooth"
            });
        });

        // Ajouter un écouteur d'événement de clic pour le bouton droit
        rightButton.addEventListener("click", () => {
            filmsContainer.scrollBy({
                left: scrollAmount,
                behavior: "smooth"
            });
        });

        // Fonction pour afficher ou masquer les boutons de défilement en fonction de la position de défilement
        function toggleScrollButtons() {
            if(isMobileDevice()) return;
            if (filmsContainer.scrollLeft === 0) {
                leftButton.style.display = "none";
            } else {
                leftButton.style.display = "block";
            }

            if (filmsContainer.scrollLeft + filmsContainer.clientWidth >= filmsContainer.scrollWidth) {
                rightButton.style.display = "none";
            } else {
                rightButton.style.display = "block";
            }
        }

        // Ajouter un écouteur d'événement de défilement au conteneur des films
        filmsContainer.addEventListener("scroll", toggleScrollButtons);

        // Appel de la fonction pour masquer les boutons au chargement de la page
        hideButtons();
    });
});
