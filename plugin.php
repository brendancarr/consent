<?php
/**
 * @package Infinus
 * @version 1.1.0
 */
/*
Plugin Name: Consent Popup
Plugin URI: https://infinus.ca
Description: Custom Consent Popup with admin settings
Author: Brendan Carr	
Version: 1.1.0
Author URI: https://brendancarr.ca
*/

$plugin = WP_PLUGIN_DIR . '/consent';

function js_script() {
    wp_enqueue_script( 'theme-script', plugins_url( 'script.js' , __FILE__ ), array( 'jquery' ), '', true );
}

function css_styles() {
    wp_enqueue_style('default-style', plugins_url( 'style.css' , __FILE__ ) );
}

if ( is_dir( $plugin ) && !is_admin() ) {
    add_action('wp_enqueue_scripts', 'js_script');
    add_action('wp_enqueue_scripts', 'css_styles');
}

// Add admin menu
function consent_popup_menu() {
    add_options_page('Consent Popup Settings', 'Consent Popup', 'manage_options', 'consent-popup-settings', 'consent_popup_settings_page');
}
add_action('admin_menu', 'consent_popup_menu');

// Register settings
function consent_popup_register_settings() {
    register_setting('consent_popup_options', 'consent_popup_bg_color');
    register_setting('consent_popup_options', 'consent_popup_custom_code');
}
add_action('admin_init', 'consent_popup_register_settings');

// Settings page
function consent_popup_settings_page() {
    ?>
    <div class="wrap">
        <h1>Consent Popup Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('consent_popup_options'); ?>
            <?php do_settings_sections('consent_popup_options'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Background Color</th>
                    <td><input type="text" name="consent_popup_bg_color" value="<?php echo esc_attr(get_option('consent_popup_bg_color')); ?>" class="color-picker" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Tracking Code (gTag, Pixel)</th>
                    <td><textarea name="consent_popup_custom_code" rows="10" cols="50"><?php echo esc_textarea(get_option('consent_popup_custom_code')); ?></textarea></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Enqueue color picker
function consent_popup_admin_scripts($hook) {
    if ($hook != 'settings_page_consent-popup-settings') {
        return;
    }
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
    wp_enqueue_script('consent-popup-admin', plugins_url('admin-script.js', __FILE__), array('wp-color-picker'), false, true);
}
add_action('admin_enqueue_scripts', 'consent_popup_admin_scripts');

// Enqueue custom code in header
function consent_popup_custom_code() {
    $custom_code = get_option('consent_popup_custom_code');
    if (!empty($custom_code)) {
        echo $custom_code;
    }
}
add_action('wp_head', 'consent_popup_custom_code');

// Update popup background color
function consent_popup_update_bg_color() {
    $bg_color = get_option('consent_popup_bg_color');
    if (!empty($bg_color)) {
        echo '<style>.cookie-prompt { background-color: ' . esc_attr($bg_color) . ' !important; }</style>';
    }
}
add_action('wp_head', 'consent_popup_update_bg_color');

function get_consent_popup() {
    ob_start();
    ?>
    <div id="cookieConsentPrompt" class="cookie-prompt">
        <p>This website uses cookies to provide you with a better browsing experience. By clicking "Accept," you consent to the use of cookies.</p>
        
        <div class="button-group">
            <button onclick="setCookieConsent(true)">Accept</button>
            <button onclick="setCookieConsent(false)">Decline</button>
        </div>
    </div>
    <?php
    echo ob_get_clean();
    wp_die();
}
add_action('wp_ajax_get_consent_popup', 'get_consent_popup');
add_action('wp_ajax_nopriv_get_consent_popup', 'get_consent_popup');

function consent_popup_enqueue_scripts() {
    wp_enqueue_script('theme-script', plugins_url('consent/script.js', __FILE__), array('jquery'), '', true);
    wp_localize_script('theme-script', 'consentPopupAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
}

// Replace the existing js_script function call with this:
if (is_dir($plugin) && !is_admin()) {
    add_action('wp_enqueue_scripts', 'consent_popup_enqueue_scripts');
    add_action('wp_enqueue_scripts', 'css_styles');
}