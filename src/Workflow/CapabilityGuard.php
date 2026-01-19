<?php

namespace Lagostechboy\EditorialWorkflow\Workflow;

use Lagostechboy\EditorialWorkflow\Database\ReviewLogRepository;
use Lagostechboy\EditorialWorkflow\Workflow\ReviewManager;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Class CapabilityGuard
 * 
 * Centralizes permission checks for workflow actions.
 */

class CapabilityGuard {

    /**
     * Map actions to WordPress capabilities per plugin spec:
     * - Authors: edit_posts (create/edit/submit for review)
     * - Reviewers: edit_others_posts (approve/reject content)
     * 
     * @var array
     */
    private $actionCapabilities = [
        'submit_for_review' => 'edit_posts',      // Authors submit their content
        'approve_content'   => 'edit_others_posts', // Reviewers approve others' content
        'reject_content'    => 'edit_others_posts', // Reviewers reject others' content
        'publish_approved'  => 'edit_others_posts', // Reviewers publish approved content
    ];

    /**
     * Check if a user can perform an action using WordPress capabilities.
     * 
     * @param string $action
     * @param int|null $userId
     * 
     * @return bool
     */
    public function can( string $action, int $userId = null ): bool {
        $userId = $userId ?? get_current_user_id();

        if ( ! isset( $this->actionCapabilities[ $action ])) {
            return false;
        }

        return user_can( $userId, $this->actionCapabilities[ $action ]);
    }

    /**
     * Convenience helper for publish permission checks.
     * 
     * @param int|null $userId
     * @return bool
     */
    public function canPublish( int $userId = null ): bool {
        return $this->can( 'publish_approved', $userId );
    }
}