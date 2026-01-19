<?php
/**
 * Plugin Name:       ContentFlow Manager
 * Description:       Adds an editorial content review workflow to WordPress.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            Lagostechboy
 * Author URI:        https://lagostechboy.com
 * Text Domain:       content-flow-manager
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Plugin constants.
 */
define( 'WPCR_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPCR_URL', plugin_dir_url( __FILE__ ) );
define( 'WPCR_VERSION', '1.0.0' );

/**
 * PSR-4 style autoloader for the Lagostechboy\EditorialWorkflow\ namespace.
 */
spl_autoload_register(
    static function ( string $class ): void {
        $prefix   = 'Lagostechboy\\EditorialWorkflow\\';
        $base_dir = WPCR_PATH . 'src/';

        $len = strlen( $prefix );
        if ( strncmp( $prefix, $class, $len ) !== 0 ) {
            return;
        }

        $relative_class = substr( $class, $len );
        $file           = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

        if ( file_exists( $file ) ) {
            require $file;
        }
    }
);

// Explicitly load critical bootstrap classes
require_once WPCR_PATH . 'src/Core/Activator.php';
require_once WPCR_PATH . 'src/Core/Deactivator.php';
require_once WPCR_PATH . 'src/Core/Plugin.php';

use Lagostechboy\EditorialWorkflow\Core\Activator;
use Lagostechboy\EditorialWorkflow\Core\Deactivator;
use Lagostechboy\EditorialWorkflow\Core\Plugin;

/**
 * Plugin activation hook.
 */
register_activation_hook(
    __FILE__,
    [ Activator::class, 'activate' ]
);

/**
 * Plugin deactivation hook.
 */
register_deactivation_hook(
    __FILE__,
    [ Deactivator::class, 'deactivate' ]
);

/**
 * Bootstrap the plugin.
 */
if ( ! function_exists( 'wpcr_bootstrap' ) ) {
    function wpcr_bootstrap(): void {
        $plugin = new Plugin();
        $plugin->register();
    }
}

wpcr_bootstrap();
