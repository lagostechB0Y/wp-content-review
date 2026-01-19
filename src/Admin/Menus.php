<?php

declare(strict_types=1);

namespace Lagostechboy\EditorialWorkflow\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Menus
{
    public function register(): void
    {
        add_action( 'admin_menu', [ $this, 'addAdminMenu' ] );
    }

    public function addAdminMenu(): void
    {
        // Double safety check (recommended)
        if ( ! current_user_can( 'edit_others_posts' ) ) {
            return;
        }

        add_menu_page(
            __( 'Content Reviews', 'content-flow-manager' ),
            __( 'Content Reviews', 'content-flow-manager' ),
            'edit_others_posts',
            'content-reviews-workflow',
            [ $this, 'renderPage' ],
            'dashicons-visibility',
            25
        );
    }

    public function renderPage(): void
    {
        if ( ! current_user_can( 'edit_others_posts' ) ) {
            wp_die(
                esc_html__( 'You do not have sufficient permissions to access this page.', 'content-flow-manager' )
            );
        }

        echo '<div class="wrap">';
        echo '<h1>' . esc_html__( 'Content Review Logs', 'content-flow-manager' ) . '</h1>';

        $table = new ReviewTable();
        $table->prepare_items();
        $table->display();

        echo '</div>';
    }
}
