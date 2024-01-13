<div class='wrap'>
    <h1><?php esc_html_e('Cloner', 'toys-for-playground'); ?></h1>
<?php

    // Retrieve all plugins and themes, both active and inactive.
    function toyspg_get_all_plugins() {
        $main_plugin_basename = defined('TOYSPG_MAIN_PLUGIN_BASENAME') ? TOYSPG_MAIN_PLUGIN_BASENAME : plugin_basename(__FILE__);
        $all_plugins = get_plugins();
        $active_plugins = get_option('active_plugins', []);
        $plugins_data = ['active' => [], 'inactive' => []];

        $plugin_basename = plugin_basename(__FILE__);

        foreach($all_plugins as $plugin_path => $plugin) {
            // Exclude the own plugin
            if ($plugin_path !== $main_plugin_basename) {
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
        $plugins = toyspg_get_all_plugins('toys-for-playground/toys-for-playground.php');
        $themes = toyspg_get_all_themes();

        // Define WordPress and PHP versions.
        $wp_versions = ['6.0', '6.1', '6.2', '6.3', '6.4', 'nightly', 'latest', 'beta'];
        $php_versions = ['7.0', '7.1', '7.2', '7.3', '7.4', '8.0', '8.1', '8.2', '8.3'];

        ob_start(); ?>

        <div class="notice notice-warning">
            <p><?php esc_html_e('Alert: Uncheck non-repository plugins or themes (e.g. premium or custom) for Playground to load correctly. The API can only load content from the repository.', 'toys-for-playground'); ?></p>
        </div>

        <form method="post" action="">
        <?php wp_nonce_field("toys_for_playground_action", "toys_for_playground_nonce"); ?>
            <h2><?php esc_html_e('Active Plugins', 'toys-for-playground'); ?></h2>
            <p><?php esc_html_e('Active plugins are checked by default.', 'toys-for-playground'); ?></p>

            <?php foreach($plugins['active'] as $plugin): ?>
                <label><input type="checkbox" name="plugins[]" value="<?php echo esc_attr($plugin['slug']) ?>" checked /> <?php echo esc_html($plugin['Name']) ?></label><br/>
            <?php endforeach; ?>

            <h2><?php esc_html_e('Inactive Plugins', 'toys-for-playground'); ?></h2>
            <p><?php esc_html_e('Inactive plugins are unchecked by default.', 'toys-for-playground'); ?></p>

            <?php foreach($plugins['inactive'] as $plugin): ?>
                <label><input type="checkbox" name="plugins[]" value="<?php echo esc_attr($plugin['slug']) ?>" /> <?php echo esc_html($plugin['Name']) ?></label><br/>
            <?php endforeach; ?>

            <h2><?php esc_html_e('Active Theme', 'toys-for-playground'); ?></h2>
            <p><?php esc_html_e('Active theme are checked by default. Only one theme can be uploaded per Playground API request.', 'toys-for-playground'); ?></p>

            <?php foreach($themes['active'] as $theme): ?>
                <label><input type="checkbox" name="themes[]" value="<?php echo esc_attr($theme['slug']) ?>" checked /> <?php echo esc_html($theme['name']) ?></label><br/>
            <?php endforeach; ?>

            <h2><?php esc_html_e('Inactive Themes', 'toys-for-playground'); ?></h2>
            <p><?php esc_html_e('Inactive themes are unchecked by default. Only one theme can be uploaded per Playground API request.', 'toys-for-playground'); ?></p>

            <?php foreach($themes['inactive'] as $theme): ?>
                <label><input type="checkbox" name="themes[]" value="<?php echo esc_attr($theme['slug']) ?>" /> <?php echo esc_html($theme['name']) ?></label><br/>
            <?php endforeach; ?>

            <h2><?php esc_html_e('WordPress Version', 'toys-for-playground'); ?></h2>
            <p><?php esc_html_e('Select the WordPress version for your generated Playground.', 'toys-for-playground'); ?></p>

            <select name="wp_version">
                <option value=""><?php esc_html_e('WP Version', 'toys-for-playground'); ?></option>
                <?php foreach($wp_versions as $version): ?>
                    <option value="<?php echo esc_attr($version) ?>"><?php echo esc_html($version) ?></option>
                <?php endforeach; ?>
            </select>

            <h2><?php esc_html_e('PHP Version', 'toys-for-playground'); ?></h2>
            <p><?php esc_html_e('Select the PHP version for your generated Playground.', 'toys-for-playground'); ?></p>

            <select name="php_version">
                <option value=""><?php esc_html_e('PHP Version', 'toys-for-playground'); ?></option>
                <?php foreach($php_versions as $version): ?>
                    <option value="<?php echo esc_attr($version) ?>"><?php echo esc_html($version) ?></option>
                <?php endforeach; ?>
            </select>

            <h2><?php esc_html_e('Storage', 'toys-for-playground'); ?></h2>
            <p><?php esc_html_e('Select the storage type for your generated Playground.', 'toys-for-playground'); ?></p>
            <input type="checkbox" id="none" name="storage_none" value="none" checked>
            <label for="none"><?php esc_html_e('None: changes will be lost on page refresh.', 'toys-for-playground'); ?></label><br>
            <input type="checkbox" id="browser" name="storage_browser" value="browser">
            <label for="browser"><?php esc_html_e('Browser: stored in this browser (cookies).', 'toys-for-playground'); ?></label><br>

            <script>
                // JavaScript to handle the behaviour of storage checkboxes
                document.getElementById('none').addEventListener('change', function() {
                    if(this.checked) {
                        document.getElementById('browser').checked = false;
                    } else {
                        document.getElementById('browser').checked = true;
                    }
                });

                document.getElementById('browser').addEventListener('change', function() {
                    if(this.checked) {
                        document.getElementById('none').checked = false;
                    } else {
                        document.getElementById('none').checked = true;
                    }
                });
            </script>

            <br/>
            <input type="submit" name="generate" class="button button-primary button-hero" value="<?php esc_attr_e('Clone', 'toys-for-playground'); ?>" />
            <p><?php esc_html_e("Opens in a new window. Enable pop-ups in your browser if it doesn't.", 'toys-for-playground'); ?></p>
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

                <?php if (isset($_POST['storage_browser']) && sanitize_text_field($_POST['storage_browser']) === 'browser'): ?>
                    url += "&storage=browser";
                <?php else: ?>
                    url += "&storage=none";
                <?php endif; ?>

                window.open(url);
            </script>
        <?php endif;
        echo ob_get_clean();
    }

    toyspg_render_admin_page();

    echo "</div>";
