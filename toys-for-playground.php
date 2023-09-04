<?php
/*
Plugin Name: Toys for Playground
Plugin URI: https://wordpress.org/plugins/toys-for-playground/
Description: Toys for Playground allows you to set up development, training and testing environments in WordPress Playground without having to learn how to use the API.
Version: 1.1.2
Requires at least: 5.9
Tested up to: 6.3
Requires PHP: 5.6
Tested up to PHP: 8.2
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

    // Add the "Cloner" submenu item under "Playground"
    add_submenu_page(
        'toys-playground',        // Parent menu slug
        'Cloner',                 // Page title
        'Cloner',                 // Menu title
        'manage_options',         // Capability required to access
        'toys-cloner',            // Unique identifier for the page
        'toys_cloner_page'        // Callback to render the page
    );

    // Add the "Generator" submenu item under "Playground"
    add_submenu_page(
        'toys-playground',        // Parent menu slug
        'Generator',              // Page title
        'Generator',              // Menu title
        'manage_options',         // Capability required to access
        'toys-generator',         // Unique identifier for the page
        'toys_generator_page'     // Callback to render the page
    );
}

// Callback function for Playground page
function toys_playground_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Toys for Playground', 'toys-for-playground'); ?></h1>

        <div class="tool-box">
            <h2><?php esc_html_e('Cloner', 'toys-for-playground'); ?></h2>
            <p><?php esc_html_e('Clone your site with its theme and all its plugins in Playground.', 'toys-for-playground'); ?></p>
            <a href="admin.php?page=toys-cloner" class="button"><?php esc_html_e('Play with Cloner', 'toys-for-playground'); ?></a>
        </div>

        <div class="tool-box">
            <h2><?php esc_html_e('Generator', 'toys-for-playground'); ?></h2>
            <p><?php esc_html_e('Create a Playground from scratch, with the theme and plugins you want.', 'toys-for-playground'); ?></p>
            <a href="admin.php?page=toys-generator" class="button"><?php esc_html_e('Play with Generator', 'toys-for-playground'); ?></a>
        </div>

        <div class="tool-box">
            <h2><?php esc_html_e('Plugin Explorer', 'toys-for-playground'); ?></h2>
            <p><?php esc_html_e('Explore any WordPress plugin, in Playground, directly from the WordPress plugins repository.', 'toys-for-playground'); ?></p>
            <a href="plugin-install.php" class="button"><?php esc_html_e('Play with Plugin Explorer', 'toys-for-playground'); ?></a>
        </div>

        <div class="tool-box">
            <h2><?php esc_html_e('Theme Explorer', 'toys-for-playground'); ?></h2>
            <p><?php esc_html_e('Explore any WordPress theme, in Playground, directly from the WordPress themes repository.', 'toys-for-playground'); ?></p>
            <a href="theme-install.php" class="button"><?php esc_html_e('Play with Theme Explorer', 'toys-for-playground'); ?></a>
        </div>
    </div>
    <?php
}


// Callback function for Cloner page
function toys_cloner_page() {
    echo "<div class='wrap'>";
    echo "<h1>" . esc_html__('Cloner', 'toys-for-playground') . "</h1>";

    // Retrieve all plugins and themes, both active and inactive.
    function toyspg_get_all_plugins() {
        $all_plugins = get_plugins();
        $active_plugins = get_option('active_plugins', []);
        $plugins_data = ['active' => [], 'inactive' => []];

        $plugin_basename = plugin_basename(__FILE__);

        foreach($all_plugins as $plugin_path => $plugin) {
            // Exclude the own plugin
            if ($plugin_path !== $plugin_basename) {
                $slug = explode('/', $plugin_path)[0];
                $plugin['slug'] = sanitize_key($slug);
                if (in_array($plugin_path, $active_plugins)) {
                    $plugins_data['active'][] = $plugin;
                } else {
                    $plugins_data['inactive'][] = $plugin;
                }
            }
        }

        return $plugins_data;
    }

    function toyspg_get_all_themes() {
        $all_themes = wp_get_themes();
        $active_theme = wp_get_theme();
        $themes_data = ['active' => [], 'inactive' => []];

        foreach($all_themes as $theme) {
            $theme_data = [
                'name' => sanitize_text_field($theme->get('Name')),
                'slug' => sanitize_key(strtolower($theme->get('TextDomain'))),
            ];

            if($active_theme->get('Name') === $theme->get('Name')) {
                $themes_data['active'][] = $theme_data;
            } else {
                $themes_data['inactive'][] = $theme_data;
            }
        }

        return $themes_data;
    }

    // Render the plugin configuration page.
    function toyspg_render_admin_page() {
        $plugins = toyspg_get_all_plugins();
        $themes = toyspg_get_all_themes();

        // Define WordPress and PHP versions.
        $wp_versions = ['5.9', '6.0', '6.1', '6.2', '6.3', 'latest'];
        $php_versions = ['5.6', '7.0', '7.1', '7.2', '7.3', '7.4', '8.0', '8.1', '8.2', 'latest'];

        ob_start(); ?>

        <div class="notice notice-warning">
            <p><?php esc_html_e('Alert: Uncheck all non-repository plugins or themes (e.g. premium or custom). WordPress Playground cannot install these, causing potential issues.', 'toys-for-playground'); ?></p>
        </div>

        <form method="post" action="">
        <?php wp_nonce_field("toys_for_playground_action", "toys_for_playground_nonce"); ?>
            <h2><?php esc_html_e('Active Plugins', 'toys-for-playground'); ?></h2>
            <p><?php esc_html_e('This list shows all the active plugins of your WordPress installation, which will appear checked by default.', 'toys-for-playground'); ?></p>

            <?php foreach($plugins['active'] as $plugin): ?>
                <input type="checkbox" name="plugins[]" value="<?php echo esc_attr($plugin['slug']) ?>" checked /> <?php echo esc_html($plugin['Name']) ?><br/>
            <?php endforeach; ?>

            <h2><?php esc_html_e('Inactive Plugins', 'toys-for-playground'); ?></h2>
            <p><?php esc_html_e('This list shows all the inactive plugins of your WordPress installation, which will appear unchecked by default.', 'toys-for-playground'); ?></p>

            <?php foreach($plugins['inactive'] as $plugin): ?>
                <input type="checkbox" name="plugins[]" value="<?php echo esc_attr($plugin['slug']) ?>" /> <?php echo esc_html($plugin['Name']) ?><br/>
            <?php endforeach; ?>

            <h2><?php esc_html_e('Active Theme', 'toys-for-playground'); ?></h2>
            <p><?php esc_html_e('This list shows all the active themes of your WordPress, which will appear checked by default. WordPress Playground only allows you to upload one theme per request.', 'toys-for-playground'); ?></p>

            <?php foreach($themes['active'] as $theme): ?>
                <input type="checkbox" name="themes[]" value="<?php echo esc_attr($theme['slug']) ?>" checked /> <?php echo esc_html($theme['name']) ?><br/>
            <?php endforeach; ?>

            <h2><?php esc_html_e('Inactive Themes', 'toys-for-playground'); ?></h2>
            <p><?php esc_html_e('This list shows all the inactive themes of your WordPress, which will appear unchecked by default. WordPress Playground only allows you to upload one theme per request.', 'toys-for-playground'); ?></p>

            <?php foreach($themes['inactive'] as $theme): ?>
                <input type="checkbox" name="themes[]" value="<?php echo esc_attr($theme['slug']) ?>" /> <?php echo esc_html($theme['name']) ?><br/>
            <?php endforeach; ?>

            <h2><?php esc_html_e('WordPress Version', 'toys-for-playground'); ?></h2>
            <p><?php esc_html_e('Select which WordPress version you want to have on the WordPress Playground installation that will be generated.', 'toys-for-playground'); ?></p>

            <select name="wp_version">
                <option value=""><?php esc_html_e('WP Version', 'toys-for-playground'); ?></option>
                <?php foreach($wp_versions as $version): ?>
                    <option value="<?php echo esc_attr($version) ?>"><?php echo esc_html($version) ?></option>
                <?php endforeach; ?>
            </select>

            <h2><?php esc_html_e('PHP Version', 'toys-for-playground'); ?></h2>
            <p><?php esc_html_e('Select which PHP version you want to have on the WordPress Playground installation that will be generated.', 'toys-for-playground'); ?></p>

            <select name="php_version">
                <option value=""><?php esc_html_e('PHP Version', 'toys-for-playground'); ?></option>
                <?php foreach($php_versions as $version): ?>
                    <option value="<?php echo esc_attr($version) ?>"><?php echo esc_html($version) ?></option>
                <?php endforeach; ?>
            </select>

            <h2><?php esc_html_e('Storage', 'toys-for-playground'); ?></h2>
            <p><?php esc_html_e('Select the type of storage for the WordPress Playground installation that will be generated.', 'toys-for-playground'); ?></p>
            <input type="checkbox" id="temporary" name="storage_temporary" value="temporary" checked>
            <label for="temporary"><?php esc_html_e('Temporary (The changes are lost when refreshing the page).', 'toys-for-playground'); ?></label><br>
            <input type="checkbox" id="persistent" name="storage_persistent" value="persistent">
            <label for="persistent"><?php esc_html_e('Persistent (The changes are not lost when refreshing, but they are when the tab is closed, even if the browser is still open).', 'toys-for-playground'); ?></label><br>

            <script>
                // JavaScript to handle the behaviour of storage checkboxes
                document.getElementById('temporary').addEventListener('change', function() {
                    if(this.checked) {
                        document.getElementById('persistent').checked = false;
                    } else {
                        document.getElementById('persistent').checked = true;
                    }
                });

                document.getElementById('persistent').addEventListener('change', function() {
                    if(this.checked) {
                        document.getElementById('temporary').checked = false;
                    } else {
                        document.getElementById('temporary').checked = true;
                    }
                });
            </script>

            <br/>
            <h2><?php esc_html_e('Clone in Playground', 'toys-for-playground'); ?></h2>
            <p><?php esc_html_e("This will open a new window in your browser. If it doesn't happen, allow opening pop-up windows from your browser.", 'toys-for-playground'); ?></p>
            <input type="submit" name="generate" class="button button-primary" value="Clone" />
        </form>
        <?php if (current_user_can('manage_options') && isset($_POST["generate"]) && wp_verify_nonce($_POST["toys_for_playground_nonce"], "toys_for_playground_action")): ?>
            <script>
                var url = "https://playground.wordpress.net/?";
                <?php $_POST['plugins'] = array_map('sanitize_text_field', (array) $_POST['plugins']);
                foreach($_POST['plugins'] as $plugin_slug): ?>
                    url += "plugin=<?php echo esc_js(sanitize_key($plugin_slug)) ?>&";
                <?php endforeach; ?>
                <?php $_POST['themes'] = array_map('sanitize_text_field', (array) $_POST['themes']);
                foreach($_POST['themes'] as $theme_slug): ?>
                    url += "theme=<?php echo esc_js(sanitize_key($theme_slug)) ?>&";
                <?php endforeach; ?>

                if ("<?php echo esc_html(sanitize_text_field($_POST['wp_version'])) ?>" !== "") {
                    url += "wp=<?php echo esc_js(sanitize_text_field($_POST['wp_version'])) ?>&";
                }
                if ("<?php echo esc_html(sanitize_text_field($_POST['php_version'])) ?>" !== "") {
                    url += "php=<?php echo esc_js(sanitize_text_field($_POST['php_version'])) ?>&";
                }

                url += "url=/wp-admin/index.php&mode=seamless";

                <?php if (isset($_POST['storage_persistent']) && sanitize_text_field($_POST['storage_persistent']) === 'persistent'): ?>
                    url += "&storage=persistent";
                <?php else: ?>
                    url += "&storage=temporary";
                <?php endif; ?>

                window.open(url);
            </script>
        <?php endif;
        echo ob_get_clean();
    }

    toyspg_render_admin_page();

    echo "</div>";
}

// Callback function for Generator page
function toys_generator_page() {
    // Define WordPress and PHP versions
    $wp_versions = ['5.9', '6.0', '6.1', '6.2', '6.3', 'latest'];
    $php_versions = ['5.6', '7.0', '7.1', '7.2', '7.3', '7.4', '8.0', '8.1', '8.2', 'latest'];
?>
    <div class="wrap">
        <h1><?php echo esc_html__('Generator', 'toys-for-playground'); ?></h1>

        <!-- HTML Form -->
        <form method="post" action="">
            <?php wp_nonce_field('toys_for_playground_action', 'toys_for_playground_nonce'); ?>

            <h2><?php echo esc_html__('Plugins', 'toys-for-playground'); ?></h2>
            <p><?php echo esc_html__('Enter the slugs of the plugins you want to include in WordPress Playground. Separated by comma.', 'toys-for-playground'); ?></p>
            <input type="text" name="plugins" placeholder="<?php echo esc_attr__('Enter plugin slugs', 'toys-for-playground'); ?>">

            <h2><?php echo esc_html__('Theme', 'toys-for-playground'); ?></h2>
            <p><?php echo esc_html__('Enter the slug of the theme you want to include. WordPress Playground only allows you to upload one theme per request.', 'toys-for-playground'); ?></p>
            <input type="text" name="theme" placeholder="<?php echo esc_attr__('Enter theme slug', 'toys-for-playground'); ?>">

            <h2><?php echo esc_html__('WordPress Version', 'toys-for-playground'); ?></h2>
            <p><?php echo esc_html__('Select which WordPress version you want to have on the WordPress Playground installation that will be generated.', 'toys-for-playground'); ?></p>
            <select name="wp_version">
                <option value=""><?php echo esc_html__('WP Version', 'toys-for-playground'); ?></option>
                <?php foreach ($wp_versions as $version): ?>
                    <option value="<?php echo esc_attr($version); ?>"><?php echo esc_html($version); ?></option>
                <?php endforeach; ?>
            </select>

            <h2><?php echo esc_html__('PHP Version', 'toys-for-playground'); ?></h2>
            <p><?php echo esc_html__('Select which PHP version you want to have on the WordPress Playground installation that will be generated.', 'toys-for-playground'); ?></p>
            <select name="php_version">
                <option value=""><?php echo esc_html__('PHP Version', 'toys-for-playground'); ?></option>
                <?php foreach ($php_versions as $version): ?>
                    <option value="<?php echo esc_attr($version); ?>"><?php echo esc_html($version); ?></option>
                <?php endforeach; ?>
            </select>

            <h2><?php echo esc_html__('Storage', 'toys-for-playground'); ?></h2>
            <p><?php echo esc_html__('Select the type of storage for the WordPress Playground installation that will be generated.', 'toys-for-playground'); ?></p>

            <input type="checkbox" id="temporary" name="storage_temporary" value="temporary" checked>
            <label for="temporary"><?php echo esc_html__('Temporary (The changes are lost when refreshing the page).', 'toys-for-playground'); ?></label><br>

            <input type="checkbox" id="persistent" name="storage_persistent" value="persistent">
            <label for="persistent"><?php echo esc_html__('Persistent (The changes are not lost when refreshing, but they are when the tab is closed, even if the browser is still open).', 'toys-for-playground'); ?></label><br>

            <script>
                document.getElementById('temporary').addEventListener('change', function() {
                    if(this.checked) {
                        document.getElementById('persistent').checked = false;
                    } else {
                        document.getElementById('persistent').checked = true;
                    }
                });
                
                document.getElementById('persistent').addEventListener('change', function() {
                    if(this.checked) {
                        document.getElementById('temporary').checked = false;
                    } else {
                        document.getElementById('temporary').checked = true;
                    }
                });
            </script>

            <br/>
            <h2><?php esc_html_e('Generate in Playground', 'toys-for-playground'); ?></h2>
            <p><?php esc_html_e("This will open a new window in your browser. If it doesn't happen, allow opening pop-up windows from your browser.", 'toys-for-playground'); ?></p>
            <input type="submit" name="generate" class="button button-primary" value="Generate" />
        </form>
    </div>
    
    <?php
    // Build URL using JavaScript, similar to Cloner
    if (current_user_can('manage_options') && isset($_POST["generate"]) && wp_verify_nonce($_POST["toys_for_playground_nonce"], "toys_for_playground_action")): ?>
        <script>
            var url = "https://playground.wordpress.net/?";
            <?php $_POST['plugins'] = array_map('sanitize_text_field', explode(',', $_POST['plugins']));
            foreach($_POST['plugins'] as $plugin_slug): ?>
                url += "plugin=<?php echo esc_js(sanitize_key($plugin_slug)) ?>&";
            <?php endforeach; ?>

            <?php $_POST['theme'] = sanitize_text_field($_POST['theme']); ?>
            url += "theme=<?php echo esc_js(sanitize_key($_POST['theme'])) ?>&";

            <?php $_POST['wp_version'] = sanitize_text_field($_POST['wp_version']); ?>
            if ("<?php echo $_POST['wp_version'] ?>" !== "") {
                url += "wp=<?php echo esc_js($_POST['wp_version']) ?>&";
            }

            <?php $_POST['php_version'] = sanitize_text_field($_POST['php_version']); ?>
            if ("<?php echo $_POST['php_version'] ?>" !== "") {
                url += "php=<?php echo esc_js($_POST['php_version']) ?>&";
            }

            url += "url=/wp-admin/index.php&mode=seamless";

            <?php if (isset($_POST['storage_persistent']) && sanitize_text_field($_POST['storage_persistent']) === 'persistent'): ?>
                url += "&storage=persistent";
            <?php else: ?>
                url += "&storage=temporary";
            <?php endif; ?>

            window.open(url);
        </script>
    <?php endif;
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
