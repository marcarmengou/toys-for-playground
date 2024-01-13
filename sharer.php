<?php
// Check if the current user has permission to view this page
if (current_user_can('manage_options')) {
    // Get all installed plugins and active plugins
    $all_plugins = get_plugins();
    $active_plugins = get_option('active_plugins', []);
    $plugins_for_frontend = [];

    foreach ($all_plugins as $plugin_path => $plugin_data) {
        if (in_array($plugin_path, $active_plugins)) {
            $slug = explode('/', $plugin_path)[0];
            $plugins_for_frontend[] = ['slug' => $slug, 'name' => $plugin_data['Name']];
        }
    }

    // Safely convert to JSON format
    $plugins_json = wp_json_encode($plugins_for_frontend);

    // Identify if it's a WordPress native page based on exceptions
    $current_url = esc_url($_SERVER['REQUEST_URI']);
    $url_parts = explode('?', $current_url)[0];
    $full_url = basename($url_parts) . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '');

    // List of exceptions - WordPress native pages
    $exceptions = [
        'index.php',
        'update-core.php',
        'edit.php',
        'edit.php?post_type=page',
        'post-new.php',
        'post-new.php?post_type=page',
        'edit-tags.php?taxonomy=category',
        'edit-tags.php?taxonomy=post_tag',
        'upload.php',
        'media-new.php',
        'edit-comments.php',
        'edit-comments.php?comment_status=all',
        'edit-comments.php?comment_status=mine&user_id=1',
        'edit-comments.php?comment_status=moderated',
        'edit-comments.php?comment_status=approved',
        'edit-comments.php?comment_status=spam',
        'edit-comments.php?comment_status=trash',
        'themes.php',
        'theme-install.php',
        'theme-install.php?browse=popular',
        'theme-install.php?browse=new',
        'theme-install.php?browse=block-themes',
        'theme-install.php?browse=favorites',
        'theme-editor.php',
        'widgets.php',
        'nav-menus.php',
        'plugins.php',
        'plugin-install.php',
        'plugin-editor.php',
        'users.php',
        'user-new.php',
        'profile.php',
        'tools.php',
        'import.php',
        'export.php',
        'site-health.php',
        'export-personal-data.php',
        'erase-personal-data.php',
        'options-general.php',
        'options-writing.php',
        'options-reading.php',
        'options-discussion.php',
        'options-media.php',
        'options-permalink.php',
        'options-privacy.php'
    ];

    $is_wp_page = in_array($full_url, $exceptions);
}
?>
<script type="text/javascript">
    // Pass the list of plugins to the frontend
    var allPlugins = <?php echo $plugins_json; ?>;
    var sharerEnabled = <?php echo json_encode(boolval(get_option('enable_sharer'))); ?>;
    var isWPPage = <?php echo json_encode($is_wp_page); ?>;
</script>

<style>
    /* CSS for the button and the modal */
    #sharer-button, #pluginModal {
        position: fixed;
        bottom: 40px;
        right: 20px;
        background-color: #0073aa;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        display: none;
        z-index: 9999;
    }
    #pluginModal {
    max-height: 80vh;  /* Occupies up to 80% of the browser window height */
    height: auto;  /* The height will be automatic */
    overflow-y: auto;  /* Allows vertical displacement if necessary */
    overflow-x: hidden;  /* Disables horizontal scrolling */
    }
</style>
<div id="sharer-button"><?php _e('Share it on Playground', 'toys-for-playground'); ?></div>
<div id="pluginModal"></div>

<script type="text/javascript">
    // Localization strings
    var myLocalizedData = {
        internalPluginPageMessage: "<?php echo esc_js(__('Which plugin does this internal page? Check the box and click on "Share..." or "Copy...".', 'toys-for-playground')); ?>",
        shareButtonText: "<?php echo esc_js(__('Share it on Playground', 'toys-for-playground')); ?>",
        copyButtonText: "<?php echo esc_js(__('Copy Playground URL', 'toys-for-playground')); ?>",
        cancelButtonText: "<?php echo esc_js(__('Cancel', 'toys-for-playground')); ?>"
    };
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
    if (sharerEnabled) {
        var sharerButton = document.getElementById('sharer-button');
        var copyButton = document.getElementById('copy-button');
        var pluginModal = document.getElementById('pluginModal');
        
        if (sharerButton) {
            sharerButton.style.display = 'block';
        }

        sharerButton.addEventListener('click', function() {
            if (isWPPage) {
                // If it's a WordPress page, build the Playground URL directly
                var currentUrl = new URL(window.location.href);
                var currentPathname = currentUrl.pathname;
                var searchParams = currentUrl.search;
                var playgroundUrl = 'https://playground.wordpress.net/?url=' + currentPathname + searchParams + '&mode=seamless&storage=browser';
                window.open(playgroundUrl, '_blank');
                return;
            }
            
            // If it is a plugin page
            var modalContent = myLocalizedData.internalPluginPageMessage + '<br><br>';
            allPlugins.forEach(function(plugin) {
                var sanitizedPluginName = plugin.name.replace(/</g, "&lt;").replace(/>/g, "&gt;");
                modalContent += '<input type="radio" name="plugin" value="' + plugin.slug + '"> ' + sanitizedPluginName + '<br><br>';
            });
            modalContent += '<button id="goButton" class="button button-secondary">' + myLocalizedData.shareButtonText + '</button>';
            modalContent += '<button id="copyToClipboardButton" class="button button-secondary" style="margin-left: 10px;">' + myLocalizedData.copyButtonText + '</button>';
            modalContent += '<button id="cancelButton" class="button button-secondary" style="margin-left: 10px;">' + myLocalizedData.cancelButtonText + '</button>';
            
            pluginModal.innerHTML = modalContent;
            pluginModal.style.display = 'block';

            document.getElementById('goButton').addEventListener('click', function() {
                var selectedPlugin = document.querySelector('input[name="plugin"]:checked').value;
                var currentUrl = new URL(window.location.href);
                var currentPathname = currentUrl.pathname;
                var searchParams = currentUrl.search;
                var mode = currentPathname.indexOf('/wp-admin/') !== -1 ? 'seamless' : 'other_mode';
                
                var playgroundUrl = 'https://playground.wordpress.net/?plugin=' + selectedPlugin + '&url=' + currentPathname + searchParams + '&mode=' + mode + '&storage=browser';
                
                window.open(playgroundUrl, '_blank');
                pluginModal.style.display = 'none';
            });

            // Handle "Copy" button click event to copy URL to clipboard
            document.getElementById('copyToClipboardButton').addEventListener('click', function() {
                var selectedPlugin = document.querySelector('input[name="plugin"]:checked').value;
                var currentUrl = new URL(window.location.href);
                var currentPathname = currentUrl.pathname;
                var searchParams = currentUrl.search;
                var mode = currentPathname.indexOf('/wp-admin/') !== -1 ? 'seamless' : 'other_mode';
                
                var playgroundUrl = 'https://playground.wordpress.net/?plugin=' + selectedPlugin + '&url=' + currentPathname + searchParams + '&mode=' + mode + '&storage=browser';
                
                // Logic for copying the generated URL to the clipboard
                copyToClipboard(playgroundUrl);
            });

            // Handling the "Cancel" button click event
            document.getElementById('cancelButton').addEventListener('click', function() {
                pluginModal.style.display = 'none';
            });
        });
    }
});

// Copy to clipboard function
function copyToClipboard(text) {
    var textarea = document.createElement("textarea");
    textarea.value = text;
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);
}

</script>
