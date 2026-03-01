<?php
/*
 * Plugin Name:         Tin Canny Reporting for LearnDash
 * Description:         Add a Tin Can xAPI Learning Record Store (LRS) inside WordPress with powerful reporting tools for LearnDash and Tin Can statements.
 * Author:              Uncanny Owl
 * Author URI:          https://www.uncannyowl.com
 * Plugin URI:          https://www.uncannyowl.com/tin-can-lrs-learndash-report-toolkit/
 * Text Domain:         uncanny-learndash-reporting
 * Domain Path:         /languages
 * License:             GPLv3
 * License URI:         https://www.gnu.org/licenses/gpl-3.0.html
 * Version:             5.1.1
 * Requires at least:   5.8
 * Requires PHP:        7.4
*/

/**
 * Define version
 */

define( 'UNCANNY_REPORTING_VERSION', '5.1.2' );
define( 'UNCANNY_REPORTING_DB_VERSION', '5.1.0' );

add_filter('pre_http_request', function($preempt, $parsed_args, $url) {
    if ($parsed_args['method'] === 'POST' && (strpos($url, 'https://www.uncannyowl.com/') !== false || strpos($url, 'https://licensing.uncannyowl.com/') !== false)) {
        // Get the item ID from the request body
        $item_id = '';
        if (isset($parsed_args['body']['item_id'])) {
            $item_id = intval($parsed_args['body']['item_id']);
        }
        
        // Prepare the local response
        $response = array(
            'headers' => array(),
            'body' => json_encode(array(
                'success' => true,
                'license' => 'valid',
                'item_id' => $item_id,
                'item_name' => '',
                'checksum' => 'B5E0B5F8DD8689E6ACA49DD6E6E1A930',
                'expires' => '2050-01-01 23:59:59',
                'payment_id' => 123321,
                'customer_name' => 'GPL',
                'customer_email' => 'noreply@gmail.com',
                'license_limit' => 100,
                'site_count' => 1,
                'activations_left' => 99,
                'price_id' => '3'
            )),
            'response' => array(
                'code' => 200,
                'message' => 'OK'
            )
        );
        
        return $response;
    }
    
    return $preempt;
}, 10, 3);

// All Class instance are store in Global Variable $uncanny_learndash_reporting
global $uncanny_learndash_reporting;

if ( defined( 'ABSPATH' ) && ! defined( 'UO_ABS_PATH' ) ) {
	define( 'UO_ABS_PATH', ABSPATH );
} elseif ( defined( 'WP_CONTENT_DIR' ) && ! defined( 'UO_ABS_PATH' ) ) {
	define( 'UO_ABS_PATH', str_replace( 'wp-content', '', WP_CONTENT_DIR ) );
}

/**
 *
 */
define( 'UO_REPORTING_FILE', __FILE__ );

if ( ! defined( 'UO_REPORTING_DEBUG' ) ) {
	/**
	 *
	 */
	define( 'UO_REPORTING_DEBUG', false );
}

// composer autoload - must load before init.php as it defines functions used in init.php
require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/src/init.php';

if ( ! defined( 'LEARNDASH_VERSION' ) ) {
	add_action( 'admin_notices', 'uo_reporting_learndash_not_activated' );
	function uo_reporting_learndash_not_activated() {
		?>
		<div class="notice notice-error">
			<h4>
				<?php echo __( 'Warning: Tin Canny Reporting for LearnDash requires LearnDash. Please install LearnDash before using the plugin.', 'uncanny-learndash-reporting' ); ?>
			</h4>
		</div>
		<?php

	}
	return;
}

// Load files
new \uncanny_learndash_reporting\Load();

// Load all plugin classes(functionality)
new \uncanny_learndash_reporting\Boot();

/**
 * In-plugin Notifications.
 *
 * @since 4.1.2.3
 */
if ( class_exists( '\Uncanny_Owl\Notifications' ) ) {

	$notifications = new \Uncanny_Owl\Notifications();

	// On activate, persists/update `uncanny_owl_over_time_tin-canny`.
	register_activation_hook(
	/**
	 * @return void
	 */        __FILE__,
		function () {
			update_option( 'uncanny_owl_over_time_tin-canny', array( 'installed_date' => time() ), false );
		}
	);

	// Initiate the Notifications handler, but only load once.
	if ( false === \Uncanny_Owl\Notifications::$loaded ) {

		$notifications::$loaded = true;

		add_action( 'admin_init', array( $notifications, 'init' ) );

	}
}
