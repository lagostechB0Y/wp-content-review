<?php

declare(strict_types=1);

namespace Lagostechboy\EditorialWorkflow\Core;

use Lagostechboy\EditorialWorkflow\Cron\ReviewReminderJob;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Plugin deactivation handler.
 */
class Deactivator {

    /**
     * Fired on plugin deactivation.
     */
    public static function deactivate(): void {
        // Unschedule cron jobs.
        ReviewReminderJob::unschedule();
    }
}
