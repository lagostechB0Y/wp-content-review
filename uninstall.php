<?php
/**
 * Uninstall WP Content Review
 *
 * This file is executed when the plugin is deleted via WordPress admin.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

global $wpdb;

/**
 * 1. Delete plugin options
 */
delete_option( 'wpcr_settings' );
delete_option( 'wpcr_version' );

/**
 * 2. Remove custom database tables
 */
$wpcr_table_name = $wpdb->prefix . 'content_review_logs';
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.SchemaChange,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter
$wpdb->query( "DROP TABLE IF EXISTS {$wpcr_table_name}" );

/**
 * 3. Clear scheduled cron events
 */
$wpcr_timestamp = wp_next_scheduled( 'wpcr_review_reminder_event' );
if ( $wpcr_timestamp ) {
    wp_unschedule_event( $wpcr_timestamp, 'wpcr_review_reminder_event' );
}
