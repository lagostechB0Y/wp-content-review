<?php

declare(strict_types=1);

namespace Lagostechboy\EditorialWorkflow\Hooks;

use Lagostechboy\EditorialWorkflow\Database\ReviewLogRepository;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Handles admin notices related to content review workflow.
 */
class AdminNotices {

    /**
     * Register admin notice hooks.
     */
    public function register(): void {
        add_action( 'admin_notices', [ $this, 'showNotice' ] );
    }

    /**
     * Display notices for review status and publication blocks.
     */
    public function showNotice(): void {

        // Show approval/rejection status
        $this->showReviewStatusNotice();

        // Show publication block notice
        if ( ! isset( $_GET['content_review_blocked'] ) ) {
            return;
        }

        // Check nonce for security
        if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'content_review_blocked' ) ) {
            return;
        }

        // limit notice to users who can edit posts
        if ( ! current_user_can( 'edit_posts' ) ) {
            return;
        }

        echo '<div class="notice notice-warning is-dismissible">';
        echo '<p>';
        echo esc_html__(
            'This content requires editorial approval before it can be published.',
            'content-flow-manager'
        );
        echo '</p>';
        echo '</div>';
    }

    /**
     * Show approval or rejection status with notes.
     */
    private function showReviewStatusNotice(): void {
        // Only show on the post edit screen (post.php). Limiting to post.php reduces
        // the surface area for probing via edit.php where an arbitrary post ID could be supplied.
        global $pagenow, $post;
        
        if ( 'post.php' !== $pagenow ) {
            return;
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if ( ! isset( $_GET['post'] ) && ! isset( $post ) ) {
            return;
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $post_id = isset( $_GET['post'] ) ? (int) sanitize_text_field( wp_unslash( $_GET['post'] ) ) : (isset( $post ) ? $post->ID : 0);
        
        if ( ! $post_id ) {
            return;
        }

        $post = get_post( $post_id );
        if ( ! $post ) {
            return;
        }

        // ensure the current user has permission to view/edit this specific post.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Get the latest review log for this post
        $repository = new ReviewLogRepository();
        $logs = $repository->getByPostId( $post_id );
        
        if ( empty( $logs ) ) {
            return;
        }

        $latest_log = $logs[0];
        $action = $latest_log['action'] ?? '';
        $comment = $latest_log['comment'] ?? '';
        $reviewer_id = (int) ( $latest_log['user_id'] ?? 0 );
        $reviewer = get_userdata( $reviewer_id );
        $reviewer_name = $reviewer ? $reviewer->display_name : __( 'Unknown Reviewer', 'content-flow-manager' );

        if ( $action === 'approved' ) {
            $notice_class = 'notice-success';
            $notice_title = __( 'Content Approved', 'content-flow-manager' );
            $icon = '✅';
        } elseif ( $action === 'rejected' ) {
            $notice_class = 'notice-error';
            $notice_title = __( 'Content Rejected', 'content-flow-manager' );
            $icon = '❌';
        } else {
            return;
        }

        echo '<div class="notice ' . esc_attr( $notice_class ) . ' is-dismissible">';
        echo '<p><strong>';
        echo wp_kses_post( $icon ) . ' ' . esc_html( $notice_title );
        echo '</strong></p>';
        echo '<p>';

        // translators: %s is the reviewer's name
        $reviewer_text = __( 'Reviewer: %s', 'content-flow-manager' );

        echo esc_html(
            sprintf(
                $reviewer_text,
                esc_html( $reviewer_name )
            )
        );
        echo '</p>';
        
        // translators: %s is the reviewer's note or comment
        $color = ( $action === 'approved' ? '#00a32a' : '#dc3545' );
        $note_label = esc_html__( 'Reviewer\'s Note:', 'content-flow-manager' );
        
        echo '<p style="padding: 10px; background: #f0f0f0; border-left: 4px solid ' . esc_attr( $color ) . '; margin: 10px 0;">';
        echo '<strong>' . wp_kses_post( $note_label ) . '</strong><br>';
        echo wp_kses_post( nl2br( $comment ) );
        echo '</p>';
        
        echo '</div>';
    }
}
