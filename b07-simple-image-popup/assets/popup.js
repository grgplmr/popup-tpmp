(function () {
    const data = window.PopupNewPostData;
    const overlay = document.querySelector('.b07-popup-overlay');
    const popup = overlay ? overlay.querySelector('.b07-popup') : null;
    const closeButton = overlay ? overlay.querySelector('.b07-popup-close') : null;
    const seenKey = data && data.seenKey ? data.seenKey : 'popup_last_seen_post_id';

    if (!overlay || !popup || !data || !data.postId || !data.permalink) {
        return;
    }

    const storage = window.localStorage;
    const previousElement = document.activeElement;

    const hidePopup = () => {
        overlay.classList.remove('b07-popup-visible');
        overlay.setAttribute('aria-hidden', 'true');

        try {
            storage.setItem(seenKey, String(data.postId));
        } catch (e) {
            // Silently ignore storage errors (private mode, etc.).
        }

        if (previousElement && typeof previousElement.focus === 'function') {
            previousElement.focus();
        } else {
            document.body.focus({ preventScroll: true });
        }
    };

    const showPopup = () => {
        overlay.classList.add('b07-popup-visible');
        overlay.removeAttribute('aria-hidden');
        popup.focus({ preventScroll: true });
    };

    const handleEscape = (event) => {
        if (event.key === 'Escape') {
            hidePopup();
        }
    };

    const handleOverlayClick = (event) => {
        if (event.target === overlay) {
            hidePopup();
        }
    };

    if (closeButton) {
        closeButton.addEventListener('click', hidePopup);
    }

    overlay.addEventListener('click', handleOverlayClick);
    window.addEventListener('keydown', handleEscape);

    const alreadySeen = (() => {
        try {
            return storage.getItem(seenKey) === String(data.postId);
        } catch (e) {
            return false;
        }
    })();

    if (alreadySeen) {
        return;
    }

    const delayMs = Number(data.delayMs) >= 0 ? Number(data.delayMs) : 0;

    window.setTimeout(showPopup, delayMs);
})();
