function showPopup(id) {
    const popup = document.getElementById(id);
    const message = popup.getAttribute("data-message");
    if (message) {
        popup.querySelector('p').textContent = message;
        popup.classList.remove("hidden-popup");
        setTimeout(() => {
            popup.classList.add("visible-popup");
        }, 10);

        setTimeout(() => {
            popup.classList.add("fade-out-popup");
            setTimeout(() => {
                popup.classList.remove("visible-popup", "fade-out-popup");
                popup.classList.add("hidden-popup");
            }, 500);
        }, 3000);
    }
}
