<?php
/**
 * Animal Osteopathy Appointment Management Plugin for WordPress.
 * 
 * @package osteo-book
 * @linl https://github.com/UnknownChick/Osteo-Book
 * @author Alexandre Ferreira
 * @copyright 2026 Alexandre Ferreira
 * @license GPL v2 or later
 * 
 * Plugin Name: OsteoBook
 * Plugin URI: https://github.com/UnknownChick/Osteo-Book
 * Description: A plugin to manage animal osteopathy appointments.
 * Version: 0.1.0
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * Author: Alexandre Ferreira
 * Author URI: https://alexandre-ferreira.fr
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: osteo-book
 * Requires Plugins: secure-custom-fields
 */

if ( ! defined( 'ABSPATH' ) ) exit;

require_once __DIR__ . '/vendor/autoload.php';

define( 'OSTEOBOOK_FILE',    __FILE__ );
define( 'OSTEOBOOK_PATH',    plugin_dir_path( __FILE__ ) );
define( 'OSTEOBOOK_URL',     plugin_dir_url( __FILE__ ) );
define( 'OSTEOBOOK_VERSION', '0.1.0' );

add_action( 'plugins_loaded', function () {
    ( new OsteoBook\Plugin() )->boot();
} );
