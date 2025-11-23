<?php
/**
 * Plugin Name: TPMP Popup
 * Description: Affiche une popup simple avec une image deux secondes après l'ouverture du site.
 * Version: 1.0.0
 * Author: Popup TPMP
 * License: GPL-2.0-or-later
 */

if (!defined('ABSPATH')) {
    exit;
}

function tpmp_popup_enqueue_assets(): void
{
    if (is_admin()) {
        return;
    }

    $version = '1.0.0';

    wp_enqueue_style(
        'tpmp-popup-style',
        plugins_url('assets/popup.css', __FILE__),
        [],
        $version
    );

    wp_enqueue_script(
        'tpmp-popup-script',
        plugins_url('assets/popup.js', __FILE__),
        [],
        $version,
        true
    );
}
add_action('wp_enqueue_scripts', 'tpmp_popup_enqueue_assets');

function tpmp_popup_render_markup(): void
{
    if (is_admin()) {
        return;
    }

    $image_url = plugins_url('assets/popup-image.svg', __FILE__);
    ?>
    <div class="tpmp-popup-overlay" role="dialog" aria-modal="true" aria-label="Promotion">
        <div class="tpmp-popup-content">
            <button type="button" class="tpmp-popup-close" aria-label="Fermer la popup">×</button>
            <img src="<?php echo esc_url($image_url); ?>" alt="Promotion du site" loading="lazy" />
        </div>
    </div>
    <?php
}
add_action('wp_footer', 'tpmp_popup_render_markup');
