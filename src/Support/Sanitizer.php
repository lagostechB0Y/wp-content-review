<?php

declare(strict_types=1);

namespace Lagostechboy\EditorialWorkflow\Support;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Sanitization helper utilities.
 */
class Sanitizer {

    /**
     * Sanitize a text value.
     *
     * Intended for user input.
     */
    public static function text( ?string $value ): string {
        if ( $value === null ) {
            return '';
        }

        return sanitize_text_field( $value );
    }

    /**
     * Sanitize a key value.
     */
    public static function key( ?string $value ): string {
        if ( $value === null ) {
            return '';
        }

        return sanitize_key( $value );
    }

    /**
     * Sanitize an integer value.
     */
    public static function int( mixed $value ): int {
        return absint( $value );
    }
}
