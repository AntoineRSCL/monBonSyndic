const myBurgerMenu = document.querySelector(".menuBurger");
const myLinksBurger = document.querySelectorAll(".containerMenuBurger ul li");
const navIcon = document.querySelector('#nav-icon1');

// Vérifiez que navIcon et myBurgerMenu existent avant d'ajouter des écouteurs d'événements
if (navIcon && myBurgerMenu) {
    // Partie Menu Burger
    navIcon.addEventListener('click', function() {
        this.classList.toggle('open');
        myBurgerMenu.classList.toggle('open');
    });

    myLinksBurger.forEach((link) => {
        link.addEventListener("click", () => {
            myBurgerMenu.classList.remove('open');
            navIcon.classList.remove('open');
        });
    });

    window.addEventListener('resize', function() {
        if (window.innerWidth > 1250 && myBurgerMenu.classList.contains('open')) {
            myBurgerMenu.classList.remove('open');
            navIcon.classList.remove('open');
        }
    });
}
