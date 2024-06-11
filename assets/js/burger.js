const myBurgerMenu = document.querySelector(".menuBurger")
const myLinksBurger = document.querySelectorAll(".containerMenuBurger ul li")

//Partie Menu Burger
document.querySelector('#nav-icon1').addEventListener('click', function() {
    this.classList.toggle('open');
    myBurgerMenu.classList.toggle('open');
});

myLinksBurger.forEach((link) => {
    link.addEventListener("click", () => {
        myBurgerMenu.classList.remove('open');
        document.querySelector('#nav-icon1').classList.remove('open');
    })
})

window.addEventListener('resize', function() {
    if (window.innerWidth > 1250 && myBurgerMenu.classList.contains('open')) {
        myBurgerMenu.classList.remove('open')
        document.querySelector('#nav-icon1').classList.remove('open');
    }
});
