<?php

declare(strict_types=1);

namespace Lagostechboy\EditorialWorkflow\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Registers custom post statuses used by the review workflow.
 */
class PostStatuses {

    public function register(): void
    {
        add_action('init', [$this, 'registerStatuses']);
        add_filter('display_post_states', [$this, 'displayStates'], 10, 2);
    }

    public function registerStatuses(): void {
        register_post_status( 'pending_review', [
            'label'                     => _x( 'Pending Review', 'post status', 'content-flow-manager' ),

            'public'                    => false,
            'internal'                  => false,
            'protected'                 => false,
            'private'                   => false,

            'show_in_admin_status_list' => true,
            'show_in_admin_all_list'    => true,
            'exclude_from_search'       => true,

            'capability_type'           => 'post',

            // translators: %s is the count of pending review posts
            'label_count'               => _n_noop(
                'Pending Review <span class="count">(%s)</span>',
                'Pending Review <span class="count">(%s)</span>',
                'content-flow-manager'
            ),
        ] );

        register_post_status( 'approved', [
            'label'                     => _x( 'Approved', 'post status', 'content-flow-manager' ),
            'public'                    => false,
            'internal'                  => false,
            'protected'                 => false,
            'private'                   => false,
            'show_in_admin_status_list' => true,
            'show_in_admin_all_list'    => true,
            'exclude_from_search'       => true,
            'capability_type'           => 'post',
            // translators: %s is the count of approved posts
            'label_count'               => _n_noop(
                'Approved <span class="count">(%s)</span>',
                'Approved <span class="count">(%s)</span>',
                'content-flow-manager'
            ),
        ] );
    }

    /**
     * Show labels next to post titles.
     */
    public function displayStates(array $states, \WP_Post $post): array
    {
        if ($post->post_status === 'pending_review') {
            $states[] = __('Pending Review', 'content-flow-manager');
        }

        if ($post->post_status === 'approved') {
            $states[] = __('Approved', 'content-flow-manager');
        }

        return $states;
    }
}
