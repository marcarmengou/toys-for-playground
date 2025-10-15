<?php
// Exit if uninstall not called from WordPress.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Ensure only admins can run the uninstall.
if ( ! current_user_can( 'delete_plugins' ) ) {
    return;
}

global $wpdb;

$table_name = esc_sql( $wpdb->prefix . 'enable_sharer' );

// Safely drop the custom table.
$sql = $wpdb->prepare( "DROP TABLE IF EXISTS `%s`", $table_name );

// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
$wpdb->query( $sql );

// Remove plugin options.
delete_option( 'enable_sharer' );
