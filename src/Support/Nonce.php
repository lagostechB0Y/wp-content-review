<?php

declare(strict_types=1);

namespace Lagostechboy\EditorialWorkflow\Support;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Nonce helper utility.
 */
class Nonce {

    /**
     * Render a nonce field.
     *
     * @param string $action
     */
    public static function render( string $action ): void {
        wp_nonce_field( $action, self::fieldName( $action ) );
    }

    /**
     * Verify a nonce value from POST data.
     *
     * @param string $action
     *
     * @return bool
     */
    public static function verify( string $action ): bool {
        $field = self::fieldName( $action );

        if ( ! isset( $_POST[ $field ] ) ) {
            return false;
        }

        return (bool) wp_verify_nonce(
            sanitize_text_field( wp_unslash( $_POST[ $field ] ) ),
            $action
        );
    }

    /**
     * Build the nonce field name.
     *
     * @param string $action
     *
     * @return string
     */
    protected static function fieldName( string $action ): string {
        return '_wpcr_nonce_' . $action;
    }
}
