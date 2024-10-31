document.addEventListener("DOMContentLoaded", function () {
    const carouselImages = document.querySelector(".carousel-images");
    const dots = document.querySelectorAll(".dot");

    // Calculate the scroll amount based on item width
    function scrollToIndex(index) {
        const scrollAmount = carouselImages.scrollWidth / dots.length;
        carouselImages.scrollLeft = index * scrollAmount;
        updateActiveDot(index);
    }

    dots.forEach((dot, index) => {
        dot.addEventListener("click", () => {
            scrollToIndex(index);
        });
    });

    function updateActiveDot(activeIndex) {
        dots.forEach((dot, index) => {
            dot.classList.toggle("active", index === activeIndex);
        });
    }
});
