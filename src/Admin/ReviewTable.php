<?php

declare(strict_types=1);

namespace Lagostechboy\EditorialWorkflow\Admin;

use WP_List_Table;
use Lagostechboy\EditorialWorkflow\Database\ReviewLogRepository;

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class ReviewTable extends \WP_List_Table
{
    private ReviewLogRepository $repository;
    private int $per_page = 10;

    public function __construct()
    {
        parent::__construct([
            'singular' => __('Review', 'content-flow-manager'),
            'plural'   => __('Reviews', 'content-flow-manager'),
            'ajax'     => false,
        ]);

        $this->repository = new ReviewLogRepository();
    }

    /** Define columns */
    public function get_columns(): array
    {
        return [
            'post'      => __('Post', 'content-flow-manager'),
            'reviewer'  => __('Reviewer', 'content-flow-manager'),
            'status'    => __('Status', 'content-flow-manager'),
            'comment'   => __('Comment', 'content-flow-manager'),
            'date'      => __('Date', 'content-flow-manager'),
        ];
    }

    /** Define sortable columns */
    protected function get_sortable_columns(): array
    {
        return [
            'post'     => ['post', false],
            'reviewer' => ['reviewer', false],
            'status'   => ['status', false],
            'date'     => ['created_at', false],
        ];
    }

    /** Prepare table items */
    public function prepare_items(): void
    {
        $columns = $this->get_columns();
        $hidden = [];
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = [$columns, $hidden, $sortable];

        $current_page = $this->get_pagenum();
        $per_page = $this->per_page;

        // Sanitize and validate orderby parameter
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $orderby = isset( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : 'created_at';
        $orderby = in_array( $orderby, [ 'created_at', 'action', 'reviewer_name', 'post_title' ], true ) ? $orderby : 'created_at';
        
        // Sanitize and validate order parameter
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $order = isset( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : 'DESC';
        $order = in_array( strtoupper( $order ), [ 'ASC', 'DESC' ], true ) ? strtoupper( $order ) : 'DESC';

        // Fetch all review logs
        $all_items = $this->repository->getAll();

        // Map reviewer names and post titles
        $all_items = array_map(function ($item) {
            // Get reviewer name
            $user = get_userdata((int)($item['user_id'] ?? 0));
            $item['reviewer_name'] = $user ? $user->display_name : __('Unknown', 'content-flow-manager');

            // Get post title
            $post = get_post((int)($item['post_id'] ?? 0));
            $item['post_title'] = $post ? $post->post_title : __('Unknown', 'content-flow-manager');

            return $item;
        }, $all_items);

        // Sort items
        usort($all_items, function ($a, $b) use ($orderby, $order) {
            $valA = $a[$orderby] ?? '';
            $valB = $b[$orderby] ?? '';
            if ($valA == $valB) return 0;
            return ($order === 'ASC') ? ($valA <=> $valB) : ($valB <=> $valA);
        });

        // Pagination
        $total_items = count($all_items);
        $this->items = array_slice($all_items, ($current_page - 1) * $per_page, $per_page);

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page),
        ]);
    }

    /** Render post column */
    protected function column_post($item): string
    {
        $title = $item['post_title'] ?? '';
        $post_id = (int)($item['post_id'] ?? 0);

        if ($post_id) {
            return sprintf(
                '<a href="%s">%s</a>',
                esc_url(get_edit_post_link($post_id)),
                esc_html($title)
            );
        }

        return esc_html($title);
    }

    /** Render reviewer column */
    protected function column_reviewer($item): string
    {
        return esc_html($item['reviewer_name'] ?? '');
    }

    /** Render status column */
    protected function column_status($item): string
    {
        return esc_html(ucfirst($item['action'] ?? ''));
    }

    /** Render comment column */
    protected function column_comment($item): string
    {
        $comment = $item['comment'] ?? '';
        if ($comment) {
            return '<span title="' . esc_attr($comment) . '">' . esc_html(substr($comment, 0, 50)) . (strlen($comment) > 50 ? '...' : '') . '</span>';
        }
        return '<em>' . esc_html__('No comment', 'content-flow-manager') . '</em>';
    }

    /** Render date column */
    protected function column_date($item): string
    {
        $date = $item['created_at'] ?? '';
        if ($date) {
            return esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($date)));
        }
        return '';
    }

    /** Default fallback */
    protected function column_default($item, $column_name): string
    {
        return esc_html($item[$column_name] ?? '');
    }
}
