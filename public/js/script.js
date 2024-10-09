window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
    if (!localStorage.getItem('theme')) {
        applySystemDefaultTheme();
    }
})
function applySystemDefaultTheme() {
    if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
        document.body.classList.add('dark-mode');
    } else {
        document.body.classList.remove('dark-mode');
    }
}
document.addEventListener("DOMContentLoaded", function() {
    const savedTheme = localStorage.getItem('theme');

    if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
    } else if (savedTheme === 'light') {
        document.body.classList.remove('dark-mode');
    }
})

function applyLightMode() {
    dropMenu(); // removes the active and show tag
    document.body.classList.remove('dark-mode');
    localStorage.setItem('theme', 'light');
}
function applyDarkMode() {
    dropMenu();
    document.body.classList.add('dark-mode');
    localStorage.setItem('theme', 'dark');
}
function resetToSystemDefault() {
    dropMenu();
    localStorage.removeItem('theme');
    applySystemDefaultTheme();
}

function dropMenu() {
    const navButton = document.querySelector('.navbar-button');
    const navMenu = document.querySelector('.navbar-fullscreen');

    if (navButton && navMenu) {
        navButton.classList.toggle('active');
        navMenu.classList.toggle('show');
    }
}
