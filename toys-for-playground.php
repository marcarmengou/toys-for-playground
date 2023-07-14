<?php
/*
Plugin Name: Toys for Playground
Plugin URI: https://wordpress.org/plugins/toys-for-playground/
Description: Useful, and fun, toys to enjoy a day at WordPress Playground. At this moment we have the "Cloner" and "Generator" toys.
Version: 1.0.5
Requires at least: 5.9
Tested up to: 6.2
Requires PHP: 5.6
Tested up to PHP: 8.2
Author: Marc Armengou
Author URI: https://www.marcarmengou.com/
Text Domain: toys-for-playground
License: GPL2
*/

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
        <h1>Toys for Playground</h1>

        <div class="tool-box">
            <h2>Cloner</h2>
            <p>Clone your site with all its plugins and themes to the WordPress Playground.</p>
            <a href="admin.php?page=toys-cloner" class="button">Play with Cloner</a>
        </div>

        <div class="tool-box">
            <h2>Generator</h2>
            <p>Create a WordPress Playground from scratch, with the plugins and themes you want.</p>
            <a href="admin.php?page=toys-generator" class="button">Play with Generator</a>
        </div>
    </div>
    <?php
}


// Callback function for Cloner page
function toys_cloner_page() {
    echo "<div class='wrap'>";
    echo "<h1>Cloner</h1>";

    // Retrieve all plugins and themes, both active and inactive.
    function wpg_get_all_plugins() {
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

    function wpg_get_all_themes() {
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
    function wpg_render_admin_page() {
        $plugins = wpg_get_all_plugins();
        $themes = wpg_get_all_themes();

        // Define WordPress and PHP versions.
        $wp_versions = ['5.9', '6.0', '6.1', '6.2', 'latest'];
        $php_versions = ['5.6', '7.0', '7.1', '7.2', '7.3', '7.4', '8.0', '8.1', '8.2', 'latest'];

        ob_start(); ?>

        <div class="notice notice-warning">
            <p>Alert: Uncheck all non-repository plugins or themes (e.g. premium or custom). WordPress Playground cannot install these, causing potential issues.</p>
        </div>

        <form method="post" action="">
            <h2>Active Plugins</h2>
            <p>This list shows all the <strong>active plugins</strong> of your WordPress installation, which will appear checked by default.</p>
            <?php foreach($plugins['active'] as $plugin): ?>
                <input type="checkbox" name="plugins[]" value="<?= esc_attr($plugin['slug']) ?>" checked /> <?= esc_html($plugin['Name']) ?><br/>
            <?php endforeach; ?>

            <h2>Inactive Plugins</h2>
            <p>This list shows all the <strong>inactive plugins</strong> of your WordPress installation, which will appear unchecked by default.</p>
            <?php foreach($plugins['inactive'] as $plugin): ?>
                <input type="checkbox" name="plugins[]" value="<?= esc_attr($plugin['slug']) ?>" /> <?= esc_html($plugin['Name']) ?><br/>
            <?php endforeach; ?>

            <h2>Active Theme</h2>
            <p>This list shows all the <strong>active themes</strong> of your WordPress installation, which will appear checked by default.</p>
            <?php foreach($themes['active'] as $theme): ?>
                <input type="checkbox" name="themes[]" value="<?= esc_attr($theme['slug']) ?>" checked /> <?= esc_html($theme['name']) ?><br/>
            <?php endforeach; ?>

            <h2>Inactive Themes</h2>
            <p>This list shows all the <strong>inactive themes</strong> of your WordPress installation, which will appear unchecked by default.</p>
            <?php foreach($themes['inactive'] as $theme): ?>
                <input type="checkbox" name="themes[]" value="<?= esc_attr($theme['slug']) ?>" /> <?= esc_html($theme['name']) ?><br/>
            <?php endforeach; ?>

            <h2>WordPress Version</h2>
            <p>Select which WordPress version you want to have on the WordPress Playground installation that will be generated.</p>
            <select name="wp_version">
                <option value="">WP Version</option>
                <?php foreach($wp_versions as $version): ?>
                    <option value="<?= esc_attr($version) ?>"><?= esc_html($version) ?></option>
                <?php endforeach; ?>
            </select>

            <h2>PHP Version</h2>
            <p>Select which PHP version you want to have on the WordPress Playground installation that will be generated.</p>
            <select name="php_version">
                <option value="">PHP Version</option>
                <?php foreach($php_versions as $version): ?>
                    <option value="<?= esc_attr($version) ?>"><?= esc_html($version) ?></option>
                <?php endforeach; ?>
            </select>

            <h2>Storage</h2>
            <p>Select the type of storage for the WordPress Playground installation that will be generated.</p>
            <input type="checkbox" id="temporary" name="storage_temporary" value="temporary" checked>
            <label for="temporary">Temporary (The changes are lost when refreshing the page).</label><br>
            <input type="checkbox" id="persistent" name="storage_persistent" value="persistent">
            <label for="persistent">Persistent (The changes are not lost when refreshing, but they are when the tab is closed, even if the browser is still open).</label><br>

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
            <h2>Clone in Playground</h2>
            <p>This will open a new window in your browser. If it doesn't happen, <strong>allow opening pop-up windows from your browser</strong>.</p>
            <input type="submit" name="generate" class="button button-primary" value="Clone" />
        </form>
        <?php if(isset($_POST['generate'])): ?>
            <script>
                var url = "https://playground.wordpress.net/?";
                <?php foreach((array)$_POST['plugins'] as $plugin_slug): ?>
                    url += "plugin=<?= esc_js(sanitize_key($plugin_slug)) ?>&";
                <?php endforeach; ?>
                <?php foreach((array)$_POST['themes'] as $theme_slug): ?>
                    url += "theme=<?= esc_js(sanitize_key($theme_slug)) ?>&";
                <?php endforeach; ?>

                if ("<?= sanitize_text_field($_POST['wp_version']) ?>" !== "") {
                    url += "wp=<?= esc_js(sanitize_text_field($_POST['wp_version'])) ?>&";
                }
                if ("<?= sanitize_text_field($_POST['php_version']) ?>" !== "") {
                    url += "php=<?= esc_js(sanitize_text_field($_POST['php_version'])) ?>&";
                }

                url += "url=/wp-admin/index.php&mode=seamless";

                <?php if (isset($_POST['storage_persistent']) && $_POST['storage_persistent'] === 'persistent'): ?>
                    url += "&storage=persistent";
                <?php else: ?>
                    url += "&storage=temporary";
                <?php endif; ?>

                window.open(url);
            </script>
        <?php endif;
        echo ob_get_clean();
    }

    wpg_render_admin_page();

    echo "</div>";
}

// Callback function for Generator page
function toys_generator_page() {
    $plugins = get_plugins();
    $themes = wp_get_themes();
    $wp_versions = ['5.9', '6.0', '6.1', '6.2', 'latest'];
    $php_versions = ['5.6', '7.0', '7.1', '7.2', '7.3', '7.4', '8.0', '8.1', '8.2', 'latest'];
    $storage_temporary = true;
    $storage_persistent = false;

    if (isset($_POST['generate'])) {
        $selected_plugins = isset($_POST['plugins']) ? explode(',', $_POST['plugins'][0]) : [];
        $selected_themes = isset($_POST['themes']) ? explode(',', $_POST['themes'][0]) : [];
        $selected_wp_version = isset($_POST['wp_version']) ? $_POST['wp_version'] : '';
        $selected_php_version = isset($_POST['php_version']) ? $_POST['php_version'] : '';
        $selected_storage_temporary = isset($_POST['storage_temporary']) ? true : false;
        $selected_storage_persistent = isset($_POST['storage_persistent']);

        $url = 'https://playground.wordpress.net/?';

        // Add selected plugins to the URL
        foreach ($selected_plugins as $plugin_slug) {
            $url .= 'plugin=' . urlencode(trim($plugin_slug)) . '&';
        }

        // Add selected themes to the URL
        foreach ($selected_themes as $theme_slug) {
            $url .= 'theme=' . urlencode(trim($theme_slug)) . '&';
        }

        // Add selected WordPress version to the URL
        if (!empty($selected_wp_version)) {
            $url .= 'wp=' . urlencode(sanitize_text_field($selected_wp_version)) . '&';
        }

        // Add selected PHP version to the URL
        if (!empty($selected_php_version)) {
            $url .= 'php=' . urlencode(sanitize_text_field($selected_php_version)) . '&';
        }

        // Add seamless mode parameter
        $url .= 'url=/wp-admin/index.php&mode=seamless';

        // Add selected storage option to the URL
        if ($selected_storage_persistent) {
            $url .= '&storage=persistent';
        } else {
            $url .= '&storage=temporary';
        }

        // Open the URL in a new window
        echo "<script>window.open('$url');</script>";
    }

    ?>
    <div class="wrap">
        <h1>Generator</h1>

        <div class="tool-box">
            <h2>Plugins</h2>
            <p>Enter the slugs of the plugins you want to include in WordPress Playground. Separated by comma. No spaces.</p>
            <form method="post" action="">
                <input type="text" name="plugins[]" placeholder="Enter plugin slug" /><br/>
                <!-- Add more input fields if needed for additional plugins -->

                <h2>Themes</h2>
                <p>Enter the slugs of the themes you want to include in WordPress Playground. Separated by comma. No spaces.</p>
                <input type="text" name="themes[]" placeholder="Enter theme slug" /><br/>
                <!-- Add more input fields if needed for additional themes -->

                <h2>WordPress Version</h2>
                <p>Select the WordPress version for the WordPress Playground.</p>
                <select name="wp_version">
                    <option value="">WP version</option>
                    <?php foreach ($wp_versions as $version): ?>
                        <option value="<?= esc_attr($version) ?>"><?= esc_html($version) ?></option>
                    <?php endforeach; ?>
                </select>

                <h2>PHP Version</h2>
                <p>Select the PHP version for the WordPress Playground.</p>
                <select name="php_version">
                    <option value="">Select PHP version</option>
                    <?php foreach ($php_versions as $version): ?>
                        <option value="<?= esc_attr($version) ?>"><?= esc_html($version) ?></option>
                    <?php endforeach; ?>
                </select>

                <h2>Storage</h2>
                <p>Select the type of storage for the WordPress Playground.</p>
                <input type="checkbox" id="temporary" name="storage_temporary" value="temporary" <?= $storage_temporary ? 'checked' : '' ?>>
                <label for="temporary">Temporary (The changes are lost when refreshing the page)</label><br>
                <input type="checkbox" id="persistent" name="storage_persistent" value="persistent" <?= $storage_persistent ? 'checked' : '' ?>>
                <label for="persistent">Persistent (The changes are not lost when refreshing, but they are when the tab is closed, even if the browser is still open)</label><br>
                <br/>

                <script>
                // JavaScript to handle the behavior of storage checkboxes
                document.getElementById('temporary').addEventListener('change', function() {
                    if (this.checked) {
                        document.getElementById('persistent').checked = false;
                    }
                });

                document.getElementById('persistent').addEventListener('change', function() {
                    if (this.checked) {
                        document.getElementById('temporary').checked = false;
                    }
                });
                </script>
                <h2>Generate in Playground</h2>
                <p>This will open a new window in your browser. If it doesn't happen, <strong>allow opening pop-up windows from your browser</strong>.</p>
                <input type="submit" name="generate" class="button button-primary" value="Generate" />
            </form>
        </div>
    </div>
    <?php
}
