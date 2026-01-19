<?php

declare(strict_types=1);

namespace Lagostechboy\EditorialWorkflow\Workflow;

use Lagostechboy\EditorialWorkflow\Database\ReviewLogRepository;
use Lagostechboy\EditorialWorkflow\Support\Nonce;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Orchestrates content review workflow transitions.
 */
class ReviewManager {

    private StateMachine $stateMachine;
    private CapabilityGuard $capabilityGuard;
    private ReviewLogRepository $logRepository;

    public function __construct(
        
        ?StateMachine $stateMachine = null,
        ?CapabilityGuard $capabilityGuard = null,
        ?ReviewLogRepository $logRepository = null
    ) {

        $this->stateMachine    = $stateMachine ?? new StateMachine();
        $this->capabilityGuard = $capabilityGuard ?? new CapabilityGuard();
        $this->logRepository   = $logRepository ?? new ReviewLogRepository();
    }

    /**
     * Register POST handlers.
     */
    public function register(): void {
        add_action( 'admin_post_wpcr_review_action', [ $this, 'handleReviewAction' ] );
        add_action( 'admin_post_nopriv_wpcr_review_action', [ $this, 'handleReviewAction' ] );
    }

    /**
     * Handle approve / reject submission.
     */

    public function handleReviewAction(): void {
        // Verify nonce first for security
        if ( ! Nonce::verify( 'wpcr_review_action' ) ) {
            wp_die( 'Nonce verification failed. Security check failed.', 403 );
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Missing
        $postId = isset( $_POST['post_id'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['post_id'] ) ) : 0;
        // phpcs:ignore WordPress.Security.NonceVerification.Missing
        $action = isset( $_POST['review_action'] ) ? sanitize_text_field( wp_unslash( $_POST['review_action'] ) ) : '';
        // phpcs:ignore WordPress.Security.NonceVerification.Missing
        $note   = isset( $_POST['review_note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['review_note'] ) ) : '';

        if ( ! $postId ) {
            wp_die( 'Missing post ID', 400 );
        }
        
        if ( ! $action ) {
            wp_die( 'Missing action', 400 );
        }

        // Map UI action → (post_status, log_action, capability_action)
        $map = [
            'approve' => [
                'post_status' => 'publish',
                'log_action'  => 'approved',
                'cap_action'  => 'approve_content',
            ],
            'reject'  => [
                'post_status' => 'draft',
                'log_action'  => 'rejected',
                'cap_action'  => 'reject_content',
            ],
        ];

        if ( ! isset( $map[ $action ] ) ) {
            wp_die( 'Invalid review action.' );
        }

        $nextStatus     = $map[ $action ]['post_status'];
        $logAction      = $map[ $action ]['log_action'];
        $requiredAction = $map[ $action ]['cap_action'];

        // Perform transition
        $success = $this->transition( $postId, $nextStatus, $note, $logAction, $requiredAction );

        if ( ! $success ) {
            wp_die( 'Transition failed', 400 );
        }

        // Redirect back to editor
        $redirect = add_query_arg(
            [
                'post'   => $postId,
                'action' => 'edit',
                'wpcr'   => 'success',
            ],
            admin_url( 'post.php' )
        );

        wp_safe_redirect( $redirect );
        exit;
    }

    /**
     * Transition post state and log it.
     */
    public function transition( int $postId, string $nextState, ?string $comment = null, ?string $logAction = null, ?string $requiredAction = null ): bool {

        $post = get_post( $postId );
        if ( ! $post ) {
            return false;
        }

        $currentState = $post->post_status;

        if ( ! $this->stateMachine->canTransition( $currentState, $nextState ) ) {
            return false;
        }

        // Capability check: requiredAction if provided, otherwise fallback to mapping by nextState
        if ( $requiredAction ) {
            $canDo = $this->capabilityGuard->can( $requiredAction, get_current_user_id() );
            if ( ! $canDo ) {
                return false;
            }
        }

        $result = wp_update_post(
            [
                'ID'          => $postId,
                'post_status' => $nextState,
            ],
            true
        );

        if ( is_wp_error( $result ) ) {
            return false;
        }

        // Log review action - use explicit logAction if provided, otherwise fall back to nextState
        $actionToLog = $logAction ?? $nextState;

        $this->logRepository->insert(
            $postId,
            get_current_user_id(),
            $actionToLog,
            $comment
        );

        return true;
    }
}
