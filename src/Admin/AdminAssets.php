<?php

declare(strict_types=1);

namespace Lagostechboy\EditorialWorkflow\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AdminAssets
{
    public function register(): void
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueue']);
    }

    public function enqueue(string $hook): void
    {
        // Only load on post editor
        if (!in_array($hook, ['post.php', 'post-new.php'], true)) {
            return;
        }

        $script_url = \WPCR_URL . 'assets/js/admin-review-actions.js';

        wp_enqueue_script(
            'wpcr-admin-review',
            $script_url,
            [],
            \WPCR_VERSION,
            true
        );

        // Pass the correct admin-post.php URL to JavaScript
        wp_localize_script('wpcr-admin-review', 'WPCR', [
            'adminPostUrl' => admin_url('admin-post.php'),
        ]);
    }
}
