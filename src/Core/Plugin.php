<?php

declare(strict_types=1);

namespace Lagostechboy\EditorialWorkflow\Core;

use Lagostechboy\EditorialWorkflow\Admin\Menus;
use Lagostechboy\EditorialWorkflow\Admin\MetaBoxes;
use Lagostechboy\EditorialWorkflow\Admin\AuthorDashboard;
use Lagostechboy\EditorialWorkflow\Core\PostStatuses;
use Lagostechboy\EditorialWorkflow\Cron\ReviewReminderJob;
use Lagostechboy\EditorialWorkflow\Hooks\AdminQueryHook;
use Lagostechboy\EditorialWorkflow\Hooks\PostStatusHooks;
use Lagostechboy\EditorialWorkflow\Workflow\CapabilityGuard;
use Lagostechboy\EditorialWorkflow\Workflow\ReviewManager;
use Lagostechboy\EditorialWorkflow\Admin\AdminAssets;



if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main plugin bootstrap class.
 */
class Plugin {

    /**
     * Register plugin services and hooks.
     */
    public function register(): void {
        add_action( 'plugins_loaded', [ $this, 'boot' ] );
    }

    public function boot(): void {

        // Create shared services ONCE
        $capabilityGuard = new CapabilityGuard();

        // Custom post statuses
        ( new PostStatuses() )->register();
        ( new AdminQueryHook() )->register();

        // Cron jobs
        ( new ReviewReminderJob() )->register();

        // Workflow & capability guards
        ( new PostStatusHooks( $capabilityGuard ) )->register();

        // Admin UI
        ( new Menus() )->register();
        (new AdminAssets())->register();
        ( new MetaBoxes() )->register();
        ( new AuthorDashboard() )->register();

        // Review workflow manager
        ( new ReviewManager() )->register();
    }
}
