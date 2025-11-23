<?php
/**
 * Plugin Name: B07 Simple Image Popup
 * Description: Affiche une popup simple avec une image configurable depuis l'admin quelques secondes après l'ouverture du site.
 * Version: 1.1.0
 * Author: Popup TPMP
 * License: GPL-2.0-or-later
 */

if (!defined('ABSPATH')) {
    exit;
}

function b07_popup_get_default_image_url(): string
{
    return plugins_url('assets/popup-image.svg', __FILE__);
}

function b07_popup_enqueue_assets(): void
{
    if (is_admin()) {
        return;
    }

    $version = '1.1.0';

    wp_enqueue_style(
        'b07-popup-style',
        plugins_url('assets/popup.css', __FILE__),
        [],
        $version
    );

    wp_enqueue_script(
        'b07-popup-script',
        plugins_url('assets/popup.js', __FILE__),
        [],
        $version,
        true
    );

    $image_url = get_option('b07_popup_image_url', '') ?: b07_popup_get_default_image_url();
    $delay_ms  = absint(get_option('b07_popup_delay_ms', 2000)) ?: 2000;

    wp_localize_script(
        'b07-popup-script',
        'b07PopupSettings',
        [
            'imageUrl'        => esc_url_raw($image_url),
            'delayMs'         => $delay_ms,
            'enabledGlobally' => true,
        ]
    );
}
add_action('wp_enqueue_scripts', 'b07_popup_enqueue_assets');

function b07_popup_render_markup(): void
{
    if (is_admin()) {
        return;
    }

    $image_url = get_option('b07_popup_image_url', '') ?: b07_popup_get_default_image_url();
    ?>
    <div class="b07-popup-overlay" role="dialog" aria-modal="true" aria-label="Promotion">
        <div class="b07-popup">
            <button type="button" class="b07-popup-close" aria-label="Fermer la popup">×</button>
            <img class="b07-popup-image" src="<?php echo esc_url($image_url); ?>" alt="Promotion du site" loading="lazy" />
        </div>
    </div>
    <?php
}
add_action('wp_footer', 'b07_popup_render_markup');

function b07_popup_register_settings(): void
{
    register_setting('b07_popup_options', 'b07_popup_image_url', [
        'type'              => 'string',
        'sanitize_callback' => 'esc_url_raw',
        'default'           => '',
    ]);

    register_setting('b07_popup_options', 'b07_popup_delay_ms', [
        'type'              => 'integer',
        'sanitize_callback' => 'absint',
        'default'           => 2000,
    ]);

    add_settings_section(
        'b07_popup_settings_section',
        __('Configuration de la popup', 'b07-popup'),
        '__return_null',
        'b07_popup_options'
    );

    add_settings_field(
        'b07_popup_image_url',
        __('URL de l\'image', 'b07-popup'),
        'b07_popup_render_image_field',
        'b07_popup_options',
        'b07_popup_settings_section'
    );

    add_settings_field(
        'b07_popup_delay_ms',
        __('Délai (ms)', 'b07-popup'),
        'b07_popup_render_delay_field',
        'b07_popup_options',
        'b07_popup_settings_section'
    );
}
add_action('admin_init', 'b07_popup_register_settings');

function b07_popup_render_image_field(): void
{
    $image_url = esc_url(get_option('b07_popup_image_url', ''));
    $placeholder = esc_attr__('Aucune image sélectionnée', 'b07-popup');
    ?>
    <div class="b07-popup-image-field">
        <input type="url" id="b07_popup_image_url" name="b07_popup_image_url" class="regular-text" value="<?php echo $image_url; ?>" placeholder="<?php echo $placeholder; ?>" />
        <button type="button" class="button b07-popup-image-select" data-target="#b07_popup_image_url"><?php esc_html_e('Choisir une image', 'b07-popup'); ?></button>
        <button type="button" class="button b07-popup-image-remove"><?php esc_html_e('Retirer', 'b07-popup'); ?></button>
        <div class="b07-popup-image-preview" style="margin-top: 10px; max-width: 320px;">
            <?php if ($image_url) : ?>
                <img src="<?php echo $image_url; ?>" alt="<?php esc_attr_e('Prévisualisation de l\'image', 'b07-popup'); ?>" style="max-width: 100%; height: auto;" />
            <?php else : ?>
                <em><?php esc_html_e('Aucune image sélectionnée.', 'b07-popup'); ?></em>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

function b07_popup_render_delay_field(): void
{
    $delay_ms = absint(get_option('b07_popup_delay_ms', 2000));
    ?>
    <input type="number" min="0" step="100" id="b07_popup_delay_ms" name="b07_popup_delay_ms" value="<?php echo esc_attr($delay_ms); ?>" class="small-text" />
    <?php
}

function b07_popup_register_settings_page(): void
{
    add_options_page(
        __('B07 Popup', 'b07-popup'),
        __('B07 Popup', 'b07-popup'),
        'manage_options',
        'b07-popup',
        'b07_popup_render_settings_page'
    );
}
add_action('admin_menu', 'b07_popup_register_settings_page');

function b07_popup_enqueue_admin_assets(string $hook_suffix): void
{
    if ($hook_suffix !== 'settings_page_b07-popup') {
        return;
    }

    wp_enqueue_media();

    wp_enqueue_script(
        'b07-popup-admin',
        plugins_url('assets/admin.js', __FILE__),
        ['jquery'],
        '1.1.0',
        true
    );
}
add_action('admin_enqueue_scripts', 'b07_popup_enqueue_admin_assets');

function b07_popup_render_settings_page(): void
{
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('B07 Popup', 'b07-popup'); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('b07_popup_options');
            do_settings_sections('b07_popup_options');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}
