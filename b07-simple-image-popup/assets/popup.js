(function () {
    const settings = window.b07PopupSettings || {};
    const overlay = document.querySelector('.b07-popup-overlay');
    const popupImage = overlay ? overlay.querySelector('.b07-popup-image') : null;
    const closeButton = overlay ? overlay.querySelector('.b07-popup-close') : null;

    if (!overlay || !popupImage) {
        return;
    }

    const closePopup = () => {
        overlay.classList.remove('b07-popup-visible');
    };

    if (closeButton) {
        closeButton.addEventListener('click', closePopup);
    }

    overlay.addEventListener('click', (event) => {
        if (event.target === overlay) {
            closePopup();
        }
    });

    window.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closePopup();
        }
    });

    const imageUrl = settings.imageUrl;
    if (imageUrl) {
        popupImage.src = imageUrl;
    }

    const delayMs = Number(settings.delayMs) || 2000;

    window.setTimeout(() => {
        overlay.classList.add('b07-popup-visible');
    }, delayMs);
})();
