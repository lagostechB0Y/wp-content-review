<?php

declare(strict_types=1);

namespace Lagostechboy\EditorialWorkflow\Admin;

use Lagostechboy\EditorialWorkflow\Support\Nonce;
use WP_Post;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MetaBoxes
{
    public function register(): void
    {
        add_action( 'add_meta_boxes', [ $this, 'addMetaBox' ] );
    }

    public function addMetaBox(): void
    {
        add_meta_box(
            'content-review-workflow',
            __( 'Content Review', 'content-flow-manager' ),
            [ $this, 'renderMetaBox' ],
            [ 'post', 'page' ],
            'side',
            'high'
        );
    }

    public function renderMetaBox( WP_Post $post ): void
    {
        // Only editors/admins who can review (edit_others_posts)
        if ( ! current_user_can( 'edit_others_posts' ) ) {
            echo '<p>' . esc_html__( 'This content is not pending review.', 'content-flow-manager' ) . '</p>';
            return;
        }

        // Only pending_review posts
        if ( $post->post_status !== 'pending_review' ) {
            echo '<p>' . esc_html__( 'This content is not pending review.', 'content-flow-manager' ) . '</p>';
            return;
        }

        echo '<div id="wpcr-review-box">';

        echo '<p>' . esc_html__( 'Review this content and take action.', 'content-flow-manager' ) . '</p>';

        if ( current_user_can( 'edit_others_posts' ) ) {
            // NOTE: We cannot use a nested <form> tag here because metaboxes render 
            // inside the main WordPress post edit form. Nested forms are invalid HTML.
            // Instead, we use hidden inputs + buttons with JavaScript to submit via
            // a dynamically created form outside the main form.

            echo '<input type="hidden" name="wpcr_post_id" id="wpcr_post_id" value="' . esc_attr( (string) $post->ID ) . '">';
            echo '<input type="hidden" name="wpcr_review_action" id="wpcr_review_action" value="">';

            Nonce::render( 'wpcr_review_action' );

            echo '<textarea
                    id="wpcr_review_note"
                    name="wpcr_review_note"
                    style="width:100%; min-height:60px;"
                    placeholder="' . esc_attr__( 'Optional review comment', 'content-flow-manager' ) . '"
                ></textarea>';

            echo '<p style="margin-top:10px;">';

            echo '<button
                    type="button"
                    class="button button-primary"
                    id="wpcr-approve-btn"
                    data-wpcr-action="approve">
                    ' . esc_html__( 'Approve', 'content-flow-manager' ) . '
                  </button> ';

            echo '<button
                    type="button"
                    class="button"
                    id="wpcr-reject-btn"
                    data-wpcr-action="reject">
                    ' . esc_html__( 'Reject', 'content-flow-manager' ) . '
                  </button>';

            echo '</p>';
        } else {
            echo '<p><em>' . esc_html__( 'You do not have permission to approve or reject this content.', 'content-flow-manager' ) . '</em></p>';
        }
    }
}
