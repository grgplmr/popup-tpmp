<?php
/**
 * Plugin Name: B07 Simple Image Popup
 * Description: Affiche une popup de notification pour le dernier article publié.
 * Version: 2.0.0
 * Author: Popup TPMP
 * License: GPL-2.0-or-later
 */

if (!defined('ABSPATH')) {
    exit;
}

function b07_popup_get_latest_post_data(): ?array
{
    $latest_posts = get_posts([
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'numberposts'    => 1,
        'no_found_rows'  => true,
        'fields'         => 'objects',
        'suppress_filters' => false,
    ]);

    if (empty($latest_posts)) {
        return null;
    }

    $post = $latest_posts[0];

    return [
        'id'          => (int) $post->ID,
        'title'       => get_the_title($post),
        'permalink'   => get_permalink($post),
        'publishedAt' => get_the_date(DATE_W3C, $post),
    ];
}

function b07_popup_enqueue_assets(): void
{
    if (is_admin()) {
        return;
    }

    $version = '2.0.0';

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

    $latest_post = b07_popup_get_latest_post_data();

    if ($latest_post) {
        wp_localize_script(
            'b07-popup-script',
            'PopupNewPostData',
            [
                'postId'   => $latest_post['id'],
                'title'    => wp_strip_all_tags($latest_post['title']),
                'permalink'=> esc_url_raw($latest_post['permalink']),
                'seenKey'  => 'popup_last_seen_post_id',
                'delayMs'  => absint(get_option('b07_popup_delay_ms', 2000)) ?: 2000,
            ]
        );
    }
}
add_action('wp_enqueue_scripts', 'b07_popup_enqueue_assets');

function b07_popup_render_markup(): void
{
    if (is_admin()) {
        return;
    }

    $latest_post = b07_popup_get_latest_post_data();

    if (!$latest_post) {
        return;
    }
    ?>
    <div class="b07-popup-overlay" aria-hidden="true">
        <div class="b07-popup" role="dialog" aria-modal="true" aria-label="Notification nouvel article" tabindex="-1">
            <button type="button" class="b07-popup-close" aria-label="Fermer la notification">×</button>
            <div class="b07-popup-body">
                <p class="b07-popup-kicker"><?php echo esc_html__('Un nouvel article vient de paraître', 'b07-popup'); ?></p>
                <h3 class="b07-popup-title"><?php echo esc_html($latest_post['title']); ?></h3>
                <a class="b07-popup-button" href="<?php echo esc_url($latest_post['permalink']); ?>">
                    <?php echo esc_html__('Découvrir l\'article', 'b07-popup'); ?>
                </a>
            </div>
        </div>
    </div>
    <?php
}
add_action('wp_footer', 'b07_popup_render_markup');

function b07_popup_register_settings(): void
{
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
        'b07_popup_delay_ms',
        __('Délai (ms)', 'b07-popup'),
        'b07_popup_render_delay_field',
        'b07_popup_options',
        'b07_popup_settings_section'
    );
}
add_action('admin_init', 'b07_popup_register_settings');

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
