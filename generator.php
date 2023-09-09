<?php
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
            <p><?php echo esc_html__('Enter plugin slugs for Playground, separated by commas.', 'toys-for-playground'); ?></p>
            <input type="text" name="plugins" placeholder="<?php echo esc_attr__('Enter plugin slugs', 'toys-for-playground'); ?>">

            <h2><?php echo esc_html__('Theme', 'toys-for-playground'); ?></h2>
            <p><?php echo esc_html__('Enter the theme slug. API allow only one theme per Playground request.', 'toys-for-playground'); ?></p>
            <input type="text" name="theme" placeholder="<?php echo esc_attr__('Enter theme slug', 'toys-for-playground'); ?>">

            <h2><?php echo esc_html__('WordPress Version', 'toys-for-playground'); ?></h2>
            <p><?php echo esc_html__('Select the WordPress version for your generated Playground.', 'toys-for-playground'); ?></p>
            <select name="wp_version">
                <option value=""><?php echo esc_html__('WP Version', 'toys-for-playground'); ?></option>
                <?php foreach ($wp_versions as $version): ?>
                    <option value="<?php echo esc_attr($version); ?>"><?php echo esc_html($version); ?></option>
                <?php endforeach; ?>
            </select>

            <h2><?php echo esc_html__('PHP Version', 'toys-for-playground'); ?></h2>
            <p><?php echo esc_html__('Select the PHP version for your generated Playground.', 'toys-for-playground'); ?></p>
            <select name="php_version">
                <option value=""><?php echo esc_html__('PHP Version', 'toys-for-playground'); ?></option>
                <?php foreach ($php_versions as $version): ?>
                    <option value="<?php echo esc_attr($version); ?>"><?php echo esc_html($version); ?></option>
                <?php endforeach; ?>
            </select>

            <h2><?php echo esc_html__('Storage', 'toys-for-playground'); ?></h2>
            <p><?php echo esc_html__('Select the storage type for your generated Playground.', 'toys-for-playground'); ?></p>

            <input type="checkbox" id="temporary" name="storage_temporary" value="temporary" checked>
            <label for="temporary"><?php echo esc_html__('Temporary (Changes lost on page refresh).', 'toys-for-playground'); ?></label><br>

            <input type="checkbox" id="persistent" name="storage_persistent" value="persistent">
            <label for="persistent"><?php echo esc_html__('Persistent (Can page refresh, but changes lost on tab closes).', 'toys-for-playground'); ?></label><br>

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
            <p><?php esc_html_e("Opens in a new window. Enable pop-ups in your browser if it doesn't.", 'toys-for-playground'); ?></p>
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
                url += "&storage=opfs-browser";
            <?php else: ?>
                url += "&storage=temporary";
            <?php endif; ?>

            window.open(url);
        </script>
    <?php endif;
