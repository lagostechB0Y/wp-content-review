<?php

namespace Lagostechboy\EditorialWorkflow\Database;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Class ReviewLogRepository
 *
 * Handles CRUD operations for review logs in the database.
 */
class ReviewLogRepository {

    /**
     * Allowed review actions.
     *
     * @var string[]
     */
    private array $allowedActions = [
        'approved',
        'rejected',
        'commented',
    ];

    /**
     * Inserts a new review log entry.
     *
     * @param int         $postId
     * @param int         $userId
     * @param string      $action
     * @param string|null $comment
     *
     * @return bool
     */
    public function insert(
        int $postId,
        int $userId,
        string $action,
        ?string $comment = null
    ): bool {
        global $wpdb;

        // Validate action
        if ( ! in_array( $action, $this->allowedActions, true ) ) {
            return false;
        }

        $data = [
            'post_id'    => $postId,
            'user_id'    => $userId,
            'action'     => $action,
            'created_at' => current_time( 'mysql' ),
        ];

        $formats = [
            '%d',
            '%d',
            '%s',
            '%s',
        ];

        if ( null !== $comment ) {
            $data['comment'] = $comment;
            $formats[] = '%s';
        }

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        $result = $wpdb->insert(
            $wpdb->prefix . 'content_review_logs',
            $data,
            $formats
        );

        if ( false === $result ) {
            return false;
        }

        return true;
    }

    /**
     * Fetches review logs for a specific post.
     *
     * @param int $postId
     *
     * @return array<int, array<string, mixed>>
     */
    public function getByPostId( int $postId ): array {
        global $wpdb;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
        $sql = $wpdb->prepare(
            "
            SELECT 
                id,
                post_id,
                user_id,
                action,
                comment,
                created_at
            FROM " . $wpdb->prefix . "content_review_logs
            WHERE post_id = %d
            ORDER BY created_at DESC
            ",
            $postId
        );

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.NotPrepared
        return $wpdb->get_results( $sql, ARRAY_A ) ?: [];
    }

    /**
     * Fetches all review logs.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAll(): array {
        global $wpdb;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
        // Query has no variable placeholders, using simple SELECT statement
        $sql = "
            SELECT 
                id,
                post_id,
                user_id,
                action,
                comment,
                created_at
            FROM " . $wpdb->prefix . "content_review_logs
            ORDER BY created_at DESC
        ";

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.NotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter
        return $wpdb->get_results( $sql, ARRAY_A ) ?: [];
    }
}
