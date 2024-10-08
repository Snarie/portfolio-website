document.addEventListener('DOMContentLoaded', () => {
    const navButton = document.querySelector('.navbar-button');
    const navMenu = document.querySelector('.navbar-fullscreen');

    if (navButton && navMenu) {
        navButton.addEventListener('click', () => {
            navButton.classList.toggle('active');
            navMenu.classList.toggle('show');
        })
    }
})