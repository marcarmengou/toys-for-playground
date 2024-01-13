<?php
/*
Plugin Name: Toys for Playground
Plugin URI: https://wordpress.org/plugins/toys-for-playground/
Description: Toys for Playground allows you to set up development, training, and testing environments in WordPress Playground easily. No Playground API knowledge needed.
Version: 1.2.0
Requires at least: 6.0
Tested up to: 6.4
Requires PHP: 7.0
Tested up to PHP: 8.3
Author: Marc Armengou
Author URI: https://www.marcarmengou.com/
Text Domain: toys-for-playground
License: GPL2
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


// Add settings link to plugin page
function toys_add_settings_link($links) {
    $settings_link = '<a href="admin.php?page=toys-playground">' . __('Settings') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'toys_add_settings_link');

// Register the plugin menu
add_action('admin_menu', 'toys_plugin_menu');

function toys_plugin_menu() {
    // Add the "Playground" menu item
    add_menu_page(
        'Playground',              // Page title
        'Playground',              // Menu title
        'manage_options',          // Capability required to access
        'toys-playground',         // Unique identifier for the page
        'toys_playground_page',    // Callback to render the page
        'dashicons-superhero'      // Icon URL or dashicon class
    );

    // Register "Cloner" as a hidden submenu
    add_submenu_page(
        null,                       // Set parent menu to null to hide the submenu
        '',                         // Empty Page title
        '',                         // Empty Menu title
        'manage_options',           // Capability required to access
        'toys-cloner',              // Unique identifier for the page
        'toys_cloner_page'          // Callback to render the page
    );

    // Register "Generator" as a hidden submenu
    add_submenu_page(
        null,                       // Set parent menu to null to hide the submenu
        '',                         // Empty Page title
        '',                         // Empty Menu title
        'manage_options',           // Capability required to access
        'toys-generator',           // Unique identifier for the page
        'toys_generator_page'       // Callback to render the page
    );
}

// Callback function for Playground page
function toys_playground_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Toys for Playground', 'toys-for-playground'); ?></h1>

        <div class="tool-box">
            <h2><?php esc_html_e('Cloner', 'toys-for-playground'); ?></h2>
            <p><?php esc_html_e('Clone site, theme, and plugins to Playground.', 'toys-for-playground'); ?></p>
            <a href="admin.php?page=toys-cloner" class="button"><?php esc_html_e('Play with Cloner', 'toys-for-playground'); ?></a>
        </div>

        <div class="tool-box">
            <h2><?php esc_html_e('Generator', 'toys-for-playground'); ?></h2>
            <p><?php esc_html_e('Create a Playground from scratch, with the theme and plugins you want.', 'toys-for-playground'); ?></p>
            <a href="admin.php?page=toys-generator" class="button"><?php esc_html_e('Play with Generator', 'toys-for-playground'); ?></a>
        </div>

        <div class="tool-box">
            <h2><?php esc_html_e('Plugin Explorer', 'toys-for-playground'); ?></h2>
            <p><?php esc_html_e('Explore any plugin directly from the WordPress repository in Playground.', 'toys-for-playground'); ?></p>
            <a href="plugin-install.php" class="button"><?php esc_html_e('Play with Plugin Explorer', 'toys-for-playground'); ?></a>
        </div>

        <div class="tool-box">
            <h2><?php esc_html_e('Theme Explorer', 'toys-for-playground'); ?></h2>
            <p><?php esc_html_e('Explore any theme directly from the WordPress repository in Playground.', 'toys-for-playground'); ?></p>
            <a href="theme-install.php" class="button"><?php esc_html_e('Play with Theme Explorer', 'toys-for-playground'); ?></a>
        </div>        
        
        <div class="tool-box">
            <h2><?php esc_html_e('Sharer', 'toys-for-playground'); ?></h2>
            <p><?php esc_html_e('Generate a Playground link of your current WordPress page for debugging or sharing.', 'toys-for-playground'); ?></p>
            
            <form method="post" action="">
                <input type="hidden" name="toggle_sharer" value="<?php echo get_option('enable_sharer', 0) ? '0' : '1'; ?>">
                <input type="submit" class="button" value="<?php echo get_option('enable_sharer', 0) ? esc_html_e('Disable Sharer', 'toys-for-playground') : esc_html_e('Enable Sharer', 'toys-for-playground'); ?>">
            </form>
        </div>
    </div>
    <?php
}

// Handle form submission for Sharer tool
if (isset($_POST['toggle_sharer'])) {
    $new_sharer_status = intval($_POST['toggle_sharer']);
    update_option('enable_sharer', $new_sharer_status);
}

// Function to include sharer.php in the footer
function toys_include_sharer_in_footer() {
    if (get_option('enable_sharer') == 1) {
        include_once(plugin_dir_path(__FILE__) . 'sharer.php');
    }
}
add_action('admin_footer', 'toys_include_sharer_in_footer');  // For admin area


// Function shown by the Cloner toy
function toys_cloner_page() {
    define('TOYSPG_MAIN_PLUGIN_BASENAME', plugin_basename(__FILE__)); // Define plugin to exclude it in Cloner toy
    include(plugin_dir_path(__FILE__) . 'cloner.php');
}

// Function shown by the Generator toy
function toys_generator_page() {
    include(plugin_dir_path(__FILE__) . 'generator.php');
}

// Enqueue the Plugin Explorer script
function toys_for_playground_explorer_enqueue_scripts($hook) {
    if ('plugin-install.php' != $hook) {
        return;
    }
    wp_enqueue_script('toys-for-playground-explorer-script', plugin_dir_url(__FILE__) . 'plugin-explorer.js', array('jquery'), '1.0.0', true);
}
add_action('admin_enqueue_scripts', 'toys_for_playground_explorer_enqueue_scripts');

// Enqueue the Theme Explorer script
function toys_for_playground_theme_explorer_enqueue_scripts($hook) {
    if ('theme-install.php' != $hook) {
        return;
    }
    wp_enqueue_script('toys-for-playground-theme-explorer-script', plugin_dir_url(__FILE__) . 'theme-explorer.js', array('jquery'), '1.0.0', true);
}
add_action('admin_enqueue_scripts', 'toys_for_playground_theme_explorer_enqueue_scripts');
