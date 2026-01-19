<?php

namespace Lagostechboy\EditorialWorkflow\Database;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Manages database schema for the content review plugin.
 */
class Schema {

    /**
     * Database table name (without prefix).
     */
    private const TABLE_NAME = 'content_review_logs';

    /**
     * Create or update database tables.
     */
    public static function createTables(): void {
        global $wpdb;

        $table_name      = self::tableName();
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            post_id BIGINT(20) UNSIGNED NOT NULL,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            action VARCHAR(50) NOT NULL,
            comment TEXT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY post_id (post_id),
            KEY user_id (user_id),
            KEY action (action)
        ) ENGINE=InnoDB {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }

    /**
     * Get the fully qualified table name.
     */
    public static function tableName(): string {
        global $wpdb;

        return $wpdb->prefix . self::TABLE_NAME;
    }
}
