<?php

declare(strict_types=1);

namespace Lagostechboy\EditorialWorkflow\Admin;

use Lagostechboy\EditorialWorkflow\Database\ReviewLogRepository;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Author Dashboard for viewing their content review status.
 */
class AuthorDashboard {

    private ReviewLogRepository $repository;

    public function __construct() {
        $this->repository = new ReviewLogRepository();
    }

    /**
     * Register the author dashboard menu.
     */
    public function register(): void {
        add_action( 'admin_menu', [ $this, 'addMenuPage' ] );
    }

    /**
     * Add menu page for authors.
     */
    public function addMenuPage(): void {
        // Only show to users who can edit posts
        if ( ! current_user_can( 'edit_posts' ) ) {
            return;
        }

        $current_user_id = get_current_user_id();

        // Get all review logs
        $all_logs = $this->repository->getAll();

        // Check if user has any reviews for their posts
        $has_reviews = false;
        foreach ( $all_logs as $log ) {
            $post = get_post( (int) $log['post_id'] );
            if ( $post && (int) $post->post_author === $current_user_id ) {
                $has_reviews = true;
                break;
            }
        }

        // Only show menu if user has reviews
        if ( ! $has_reviews ) {
            return;
        }

        add_menu_page(
            __( 'My Content Reviews', 'content-flow-manager' ),
            __( 'Content Reviews', 'content-flow-manager' ),
            'edit_posts',
            'wpcr-author-dashboard',
            [ $this, 'renderPage' ],
            'dashicons-clipboard',
            25
        );
    }

    /**
     * Render the author dashboard page.
     */
    public function renderPage(): void {
        if ( ! current_user_can( 'edit_posts' ) ) {
            wp_die( esc_html__( 'You do not have permission to access this page.', 'content-flow-manager' ) );
        }

        $current_user_id = get_current_user_id();

        // Get all review logs
        $all_logs = $this->repository->getAll();

        // Filter logs for current user's posts only
        $user_logs = array_filter( $all_logs, function ( $log ) use ( $current_user_id ) {
            $post = get_post( (int) $log['post_id'] );
            return $post && (int) $post->post_author === $current_user_id;
        } );

        // Enrich logs with post titles and reviewer names
        $user_logs = array_map( function ( $log ) {
            $post = get_post( (int) $log['post_id'] );
            $reviewer = get_userdata( (int) $log['user_id'] );

            return [
                'id'              => $log['id'],
                'post_id'         => $log['post_id'],
                'post_title'      => $post ? $post->post_title : __( 'Unknown', 'content-flow-manager' ),
                'post_status'     => $post ? $post->post_status : '',
                'action'          => $log['action'],
                'comment'         => $log['comment'],
                'reviewer_name'   => $reviewer ? $reviewer->display_name : __( 'Unknown', 'content-flow-manager' ),
                'created_at'      => $log['created_at'],
            ];
        }, $user_logs );

        // Sort by date descending
        usort( $user_logs, function ( $a, $b ) {
            return strtotime( $b['created_at'] ) - strtotime( $a['created_at'] );
        } );

        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'My Content Reviews', 'content-flow-manager' ); ?></h1>
            
            <?php if ( empty( $user_logs ) ) : ?>
                <div class="notice notice-info inline">
                    <p><?php esc_html_e( 'No review logs yet. Submit content for review to see status updates here.', 'content-flow-manager' ); ?></p>
                </div>
            <?php else : ?>
                <table class="widefat striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Post Title', 'content-flow-manager' ); ?></th>
                            <th><?php esc_html_e( 'Status', 'content-flow-manager' ); ?></th>
                            <th><?php esc_html_e( 'Reviewer', 'content-flow-manager' ); ?></th>
                            <th><?php esc_html_e( 'Comment', 'content-flow-manager' ); ?></th>
                            <th><?php esc_html_e( 'Date', 'content-flow-manager' ); ?></th>
                            <th><?php esc_html_e( 'Action', 'content-flow-manager' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $user_logs as $log ) : ?>
                            <tr>
                                <td>
                                    <strong><?php echo esc_html( $log['post_title'] ); ?></strong>
                                </td>
                                <td>
                                    <?php
                                    if ( 'approved' === $log['action'] ) {
                                        echo '<span style="color: #00a32a; font-weight: bold;">✅ ' . esc_html__( 'Approved', 'content-flow-manager' ) . '</span>';
                                    } elseif ( 'rejected' === $log['action'] ) {
                                        echo '<span style="color: #dc3545; font-weight: bold;">❌ ' . esc_html__( 'Rejected', 'content-flow-manager' ) . '</span>';
                                    } else {
                                        echo '<span>' . esc_html( ucfirst( $log['action'] ) ) . '</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php echo esc_html( $log['reviewer_name'] ); ?>
                                </td>
                                <td>
                                    <?php
                                    if ( ! empty( $log['comment'] ) ) {
                                        $comment = $log['comment'];
                                        $short_comment = substr( $comment, 0, 50 ) . ( strlen( $comment ) > 50 ? '...' : '' );
                                        echo '<span title="' . esc_attr( $comment ) . '">' . esc_html( $short_comment ) . '</span>';
                                    } else {
                                        echo '<em>' . esc_html__( 'No comment', 'content-flow-manager' ) . '</em>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $date = $log['created_at'];
                                    echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $date ) ) );
                                    ?>
                                </td>
                                <td>
                                    <a href="<?php echo esc_url( get_edit_post_link( (int) $log['post_id'] ) ); ?>" class="button button-small">
                                        <?php esc_html_e( 'Edit Post', 'content-flow-manager' ); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <?php
    }
}
