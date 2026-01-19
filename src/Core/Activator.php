<?php

declare(strict_types=1);

namespace Lagostechboy\EditorialWorkflow\Core;

use Lagostechboy\EditorialWorkflow\Database\Schema;
use Lagostechboy\EditorialWorkflow\Cron\ReviewReminderJob;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Plugin activation handler.
 */
class Activator {

    /**
     * Fired on plugin activation.
     */
    public static function activate(): void {

        $stored_version = get_option( 'wpcr_version' );

        // Fresh install or version upgrade.
        if ( $stored_version !== WPCR_VERSION ) {

            // Create or update database tables.
            Schema::createTables();

            // Store current plugin version.
            update_option( 'wpcr_version', WPCR_VERSION, false );
        }

        // Schedule cron job if not already scheduled.
        ReviewReminderJob::schedule();
    }
}
