<?php

declare(strict_types=1);

namespace Lagostechboy\EditorialWorkflow\Cron;

use WP_Query;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ReviewReminderJob
{
    public const CRON_HOOK = 'wpcr_review_reminder_event';
    private const LOCK_KEY = 'wpcr_review_reminder_lock';

    public function register(): void
    {
        add_action( self::CRON_HOOK, [ $this, 'run' ] );
    }

    public static function schedule(): void
    {
        if ( ! wp_next_scheduled( self::CRON_HOOK ) ) {
            wp_schedule_event(
                time(),
                'hourly',
                self::CRON_HOOK
            );
        }
    }

    public static function unschedule(): void
    {
        $timestamp = wp_next_scheduled( self::CRON_HOOK );

        if ( $timestamp ) {
            wp_unschedule_event( $timestamp, self::CRON_HOOK );
        }
    }

    public function run(): void
    {
        // Prevent overlapping executions
        if ( get_transient( self::LOCK_KEY ) ) {
            return;
        }

        set_transient( self::LOCK_KEY, 1, 10 * MINUTE_IN_SECONDS );

        $query = new WP_Query( [
            // Look for the plugin-specific pending status. Keep 'pending' if we want to include both.
            'post_status'            => [ 'pending_review', 'pending' ],
            'post_type'              => [ 'post', 'page' ],
            'posts_per_page'         => 5,
            'no_found_rows'          => true,
            'ignore_sticky_posts'    => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
            'date_query'             => [
                [
                    'column' => 'post_modified_gmt',
                    'before' => gmdate(
                        'Y-m-d H:i:s',
                        time() - DAY_IN_SECONDS
                    ),
                ],
            ],
        ] );

        if ( ! $query->have_posts() ) {
            delete_transient( self::LOCK_KEY );
            return;
        }

        /**
         * Fires when pending content requires editorial attention.
         *
         * @param \WP_Post[] $posts
         */
        do_action( 'wpcr_pending_review_reminder', $query->posts );

        wp_reset_postdata();
        delete_transient( self::LOCK_KEY );
    }
}
