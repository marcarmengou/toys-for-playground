<?php
// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Check if the current user has permission to delete plugins
if ( ! current_user_can( 'delete_plugins' ) ) {
    return;
}

global $wpdb;

// Define the name of the table to be deleted
$table_name = $wpdb->prefix . 'enable_sharer';

// Execute the query to drop the table
$wpdb->query( "DROP TABLE IF EXISTS {$table_name}" );

// Remove any additional options and custom tables
delete_option( 'enable_sharer' );
