document.addEventListener('DOMContentLoaded', function() {

    
});
document.getElementById('login-button').addEventListener('click', function () {
    openPopup('login-popup');
});


document.getElementById('register-button').addEventListener('click', function () {
    openPopup('register-popup');
});
document.getElementById('login-account').addEventListener('click', function () {
    closeAllPopups();
    openPopup('login-popup');
});

document.getElementById('create-account').addEventListener('click', function () {
    closeAllPopups();
    openPopup('register-popup');
});


document.getElementById('loginAccount').addEventListener('click', function () {
    closeAllPopups();
    openPopup('login-popup');
});

document.getElementById('createAccount').addEventListener('click', function () {
    closeAllPopups();
    openPopup('register-popup');
});


function openPopup(popupId) {
    const popup = document.getElementById(popupId);
    const overlay = document.getElementById('overlay');

    if (!document.body.classList.contains('popup-open')) {
        document.body.classList.add('no-scroll'); 
        overlay.style.display = 'block';
    }
    
    popup.style.display = 'block';
    popup.style.animation = 'slideUp 0.3s ease-out';
    document.body.classList.add('popup-open');
}

function closePopup(popupId) {
    const popup = document.getElementById(popupId);
    const overlay = document.getElementById('overlay');

    popup.style.animation = 'none';
    setTimeout(() => {
        popup.style.display = 'none';
    }, 200);

    const allPopupsClosed = Array.from(document.querySelectorAll('.popup')).every(p => p.style.display === 'none');
    
    if (allPopupsClosed) {
        overlay.style.display = 'none';
        document.body.classList.remove('popup-open');
        document.body.classList.remove('no-scroll');
    }
}


document.getElementById('overlay').addEventListener('click', function () {
    closeAllPopups();
});

function closeAllPopups() {
    document.querySelectorAll('.popup').forEach(popup => {
        popup.style.display = 'none';
    });
    const overlay = document.getElementById('overlay');
    overlay.style.display = 'none';
    document.body.classList.remove('popup-open');
    document.body.classList.remove('no-scroll');
}



let isAdult = true;

document.getElementById('date').addEventListener('change', function () {
    const dateInput = this.value;
    const warningIcon = document.querySelector('.warning-icon');
    const submitButton = document.getElementById("register-submit");
    const today = new Date();
    const userDate = new Date(dateInput);
    let age = today.getFullYear() - userDate.getFullYear();
    const monthDiff = today.getMonth() - userDate.getMonth();

    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < userDate.getDate())) {
        age--;
    }

    if (age < 18) {
        isAdult = false;
        warningIcon.style.display = 'block';
        submitButton.disabled = true;
    } else {
        isAdult = true;
        warningIcon.style.display = 'none';
        submitButton.disabled = false;
    }
});


document.querySelector('form').addEventListener('submit', function (event) {
    if (!isAdult) {
        event.preventDefault();
    }
});







