const sliderEvent = document.querySelector(".sliderEvent")
const prevBtn = document.querySelector(".leftBtnEB")
const nextBtn = document.querySelector(".rightBtnEB")
let currentIndex = 0;

if(sliderEvent){
    // Fonction pour recalculer maxIndex lorsque la taille de l'écran change
    function updateMaxIndex() {
        const carouselWidth = sliderEvent.clientWidth;
        const itemsToShow = carouselWidth >= 600 ? 2 : 1;
        maxIndex = sliderEvent.children.length - itemsToShow;
    }

    // Appel de la fonction au chargement de la page
    updateMaxIndex();

    // Appel de la fonction à chaque redimensionnement de la fenêtre
    window.addEventListener('resize', updateMaxIndex);

    nextBtn.addEventListener('click', () => {
        currentIndex++;
        if (currentIndex > maxIndex) {
            currentIndex = 0;
        }
        updateCarousel();
    });

    prevBtn.addEventListener('click', () => {
        if (currentIndex > 0) {
            currentIndex--;
        } else {
            currentIndex = maxIndex;
        }
        updateCarousel();
    });

    function updateCarousel() {
        const carouselWidth = sliderEvent.clientWidth;
        const itemsToShow = carouselWidth >= 600 ? 2 : 1;
        const itemWidth = carouselWidth / itemsToShow;
        const transformValue = -currentIndex * itemWidth;
        sliderEvent.style.transform = `translateX(${transformValue}px)`;
    }
}