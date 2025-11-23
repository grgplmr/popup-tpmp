(function ($) {
    const imageField = $('#b07_popup_image_url');
    const previewContainer = $('.b07-popup-image-preview');
    let mediaFrame = null;

    const renderPreview = (url) => {
        if (url) {
            previewContainer.html('<img src="' + url + '" alt="Prévisualisation" style="max-width: 100%; height: auto;" />');
        } else {
            previewContainer.html('<em>Aucune image sélectionnée.</em>');
        }
    };

    $('.b07-popup-image-select').on('click', function (event) {
        event.preventDefault();

        if (mediaFrame) {
            mediaFrame.open();
            return;
        }

        mediaFrame = wp.media({
            title: 'Choisir une image de popup',
            button: { text: 'Utiliser cette image' },
            library: { type: 'image' },
            multiple: false,
        });

        mediaFrame.on('select', () => {
            const attachment = mediaFrame.state().get('selection').first().toJSON();
            imageField.val(attachment.url);
            renderPreview(attachment.url);
        });

        mediaFrame.open();
    });

    $('.b07-popup-image-remove').on('click', function (event) {
        event.preventDefault();
        imageField.val('');
        renderPreview('');
    });

    renderPreview(imageField.val());
})(jQuery);
