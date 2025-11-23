(function () {
    const overlay = document.querySelector('.tpmp-popup-overlay');
    if (!overlay) {
        return;
    }

    const closeButton = overlay.querySelector('.tpmp-popup-close');

    const closePopup = () => {
        overlay.classList.remove('tpmp-popup-visible');
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

    window.setTimeout(() => {
        overlay.classList.add('tpmp-popup-visible');
    }, 2000);
})();
