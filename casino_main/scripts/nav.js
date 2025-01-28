function toggleMenu() {
    const menu = document.getElementById('nav-ul');
    const hamburger = document.querySelector('.hamburger');

    if (menu.classList.contains('active')) {
        closeMenu();
        hamburger.classList.remove('open');
    } else {
        menu.style.animation = 'slideIn 0.3s forwards';
        menu.classList.add('active');
        menu.style.transform = 'translateX(0)';
        hamburger.classList.add('open');
    }
}

function closeMenu() {
    const menu = document.getElementById('nav-ul');
    const hamburger = document.querySelector('.hamburger');

    menu.style.animation = 'slideOut 0.3s forwards';
    menu.addEventListener('animationend', function () {
        menu.classList.remove('active');
        menu.style.transform = 'translateX(0)';
        menu.style.animation = 'unset';
    }, { once: true });
    hamburger.classList.remove('open');
}

function toggleUser() {
    const userContainer = document.getElementById("user-login-reg-container");

    if (userContainer) {
        if (userContainer.classList.contains('active')) {
            userContainer.classList.add('hide');
            userContainer.addEventListener('animationend', function () {
                userContainer.classList.remove('active', 'hide');
            }, { once: true });
        } else {
            userContainer.classList.remove('hide');
            userContainer.classList.add('active');
        }
    }
}

document.addEventListener('click', (e) => {
    const menu = document.getElementById('nav-ul');
    const hamburger = document.querySelector('.hamburger');
    const userContainer = document.getElementById("user-login-reg-container");
    const userIcon = document.querySelector('.user-icon');

    if (!menu.contains(e.target) && !hamburger.contains(e.target)) {
        if (menu.classList.contains('active')) {
            closeMenu();
        }
    }

    if (userContainer && !userContainer.contains(e.target) && !userIcon.contains(e.target)) {
        if (userContainer.classList.contains('active')) {
            userContainer.classList.add('hide');
            userContainer.addEventListener('animationend', function () {
                userContainer.classList.remove('active', 'hide');
            }, { once: true });
        }
    }
});


const menuItems = document.querySelectorAll('#nav-ul li a');
menuItems.forEach(item => {
    item.addEventListener('click', () => {
        if (document.getElementById('nav-ul').classList.contains('active')) {
            closeMenu();
        }
    });
});

function toggleSearch() {
    const searchContainer = document.querySelector('.search-container');

    if (searchContainer.classList.contains('active')) {
        searchContainer.classList.remove('active');
        searchContainer.classList.add('hide');
        searchContainer.addEventListener('animationend', () => {
            searchContainer.style.display = 'none';
            searchContainer.classList.remove('hide');
        }, { once: true });
    } else {
        searchContainer.style.display = 'flex';
        searchContainer.classList.add('active');
    }
}

document.querySelector('.search-button').addEventListener('click', toggleSearch);

document.addEventListener('click', (e) => {
    const searchContainer = document.querySelector('.search-container');
    const searchButton = document.querySelector('.search-button');

    if (!searchContainer.contains(e.target) && !searchButton.contains(e.target)) {
        if (searchContainer.classList.contains('active')) {
            searchContainer.classList.remove('active');
            searchContainer.classList.add('hide');
            searchContainer.addEventListener('animationend', () => {
                searchContainer.style.display = 'none';
                searchContainer.classList.remove('hide');
            }, { once: true });
        }
    }
});




function searchUsers() {
    const query = document.getElementById('search-input').value;
    
    if (query.length > 0) {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', `search_games.php?q=${query}`, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                document.getElementById('search-results').innerHTML = xhr.responseText;
            }
        };
        xhr.send();
    } else {
        document.getElementById('search-results').innerHTML = '';
    }
}


const iconsToggle = document.querySelector('.icons-toggle');
const popupIcons = document.querySelector('.icons-container');

iconsToggle.addEventListener("click", function () {
    popupIcons.classList.toggle("show");
});

document.addEventListener('click', (event) => {
    if (!popupIcons.contains(event.target) && !iconsToggle.contains(event.target)) {
        popupIcons.classList.remove("show");
    }
});


const searchIcon = document.querySelector(".search-button");

searchIcon.addEventListener("click", function(){
    popupIcons.classList.remove("show");
});




