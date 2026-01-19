<?php
declare(strict_types=1);

namespace Lagostechboy\EditorialWorkflow\Hooks;

use Lagostechboy\EditorialWorkflow\Workflow\CapabilityGuard;
use WP_Post;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Guards post status transitions based on editorial workflow rules.
 */
class PostStatusHooks {

    protected CapabilityGuard $capabilityGuard;

    public function __construct( CapabilityGuard $capabilityGuard ) {
        $this->capabilityGuard = $capabilityGuard;
    }

    /**
     * Register hooks.
     */
    public function register(): void {
        add_filter( 'wp_insert_post_data', [ $this, 'guardPostStatus' ], 10, 2 );
        add_filter( 'redirect_post_location', [ $this, 'addBlockedNotice' ] );
    }

    /**
     * Block publish attempts at data level.
     */
    public function guardPostStatus( array $data, array $postarr ): array {

        // Skip autosaves & revisions
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $data;
        }

        if ( isset( $postarr['ID'] ) && wp_is_post_revision( (int) $postarr['ID'] ) ) {
            return $data;
        }

        // Only intercept new or updated posts
        if (!in_array($data['post_status'], ['publish', 'pending'], true)) {
            return $data;
        }

        // Allow editors/admins
        if ( $this->capabilityGuard->canPublish( get_current_user_id() ) ) {
            return $data;
        }

        // Force pending review
        $data['post_status'] = 'pending_review';

        return $data;
    }

    /**
     * Append admin notice flag.
     */
    public function addBlockedNotice( string $location ): string {
        return add_query_arg(
            'content_review_blocked',
            '1',
            $location
        );
    }
}
