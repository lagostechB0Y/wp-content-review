<?php

declare(strict_types=1);

namespace Lagostechboy\EditorialWorkflow\Hooks;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AdminQueryHook {

    public function register(): void {
        add_action( 'pre_get_posts', [ $this, 'includeCustomStatuses' ] );
    }

    public function includeCustomStatuses( $query ): void {

        if ( ! is_admin() || ! $query->is_main_query() ) {
            return;
        }

        $screen = function_exists( 'get_current_screen' )
            ? get_current_screen()
            : null;

        if ( ! $screen || $screen->base !== 'edit' ) {
            return;
        }

        $query->set( 'post_status', [
            'publish',
            'draft',
            'pending',
            'pending_review',
            'approved',
        ] );
    }
}
