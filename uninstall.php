<?php
// Exit if uninstall not called from WordPress.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Ensure only admins can run the uninstall.
if ( ! current_user_can( 'delete_plugins' ) ) {
	return;
}

// Remove plugin options.
delete_option( 'enable_sharer' );
