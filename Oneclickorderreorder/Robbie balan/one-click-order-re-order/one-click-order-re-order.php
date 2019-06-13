<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://makewebbetter.com/
 * @since             1.0.0
 * @package           One_Click_Order_Re_order
 *
 * @wordpress-plugin
 * Plugin Name:       One Click Order Re-Order
 * Plugin URI:        https://makewebbetter.com/product/one-click-order-re-order/
 * Description:       This extension is used to place the previous order again while order status is completed or not.
 * Version:           1.0.0
 * Author:            MakeWebBetter
 * Author URI:        https://makewebbetter.com/
 * Text Domain:       one-click-order-re-order
 * Domain Path:       /languages
 *
 * Requires at least: 4.6
 * Tested up to: 	  4.9.5
 * Tested up to:      3.4.5
 * License:           Software License Agreement
 * License URI:       https://makewebbetter.com/license-agreement.txt
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
define ( 'MWB_PREFIX', 'mwb_cng' );
define ( 'MWB_VERSION', '1.0.0' );
define ( 'MAKEWEBBETTER_CNG_ORDER', plugin_dir_path ( __FILE__ ) );
define ( 'MAKEWEBBETTER_CNG_ORDER_URL', plugin_dir_url ( __FILE__ ) );

$activated = true;
if (function_exists ( 'is_multisite' ) && is_multisite ()) {
	include_once (ABSPATH . 'wp-admin/includes/plugin.php');
	if (! is_plugin_active ( 'woocommerce/woocommerce.php' )) {
		$activated = false;


	}
} 
else {
	if (! in_array ( 'woocommerce/woocommerce.php', apply_filters ( 'active_plugins', get_option ( 'active_plugins' ) ) )) {
		$activated = false;
	}
}


// Define plugin constants.
if($activated)
{  
			function define_one_click_order_re_order_constants() {

				one_click_order_re_order_constants( 'ONE_CLICK_ORDER_RE_ORDER_VERSION', '1.0.0' );
				one_click_order_re_order_constants( 'ONE_CLICK_ORDER_RE_ORDER_DIR_PATH', plugin_dir_path( __FILE__ ) );
				one_click_order_re_order_constants( 'ONE_CLICK_ORDER_RE_ORDER_DIR_URL', plugin_dir_url( __FILE__ ) );

			// For License Validation.
				one_click_order_re_order_constants( 'ONE_CLICK_ORDER_RE_ORDER_SPECIAL_SECRET_KEY', '59f32ad2f20102.74284991' );
				one_click_order_re_order_constants( 'ONE_CLICK_ORDER_RE_ORDER_SERVER_URL', 'https://makewebbetter.com' );
				one_click_order_re_order_constants( 'ONE_CLICK_ORDER_RE_ORDER_ITEM_REFERENCE', 'One Click Order Re-Order' );
			}

		// Callable function for defining plugin constants.
			function one_click_order_re_order_constants( $key, $value ) {

				if( ! defined( $key ) ) {

					define( $key, $value );
				}
			}

		/**
		 * The code that runs during plugin activation.
		 * This action is documented in includes/class-one-click-order-re-order-activator.php
		 */
		function activate_one_click_order_re_order() {
			require_once plugin_dir_path( __FILE__ ) . 'includes/class-one-click-order-re-order-activator.php';
			One_Click_Order_Re_order_Activator::activate();
		}

		/**
		 * The code that runs during plugin deactivation.
		 * This action is documented in includes/class-one-click-order-re-order-deactivator.php
		 */
		

		register_activation_hook( __FILE__, 'activate_one_click_order_re_order' );
		

		/**
		 * The core plugin class that is used to define internationalization,
		 * admin-specific hooks, and public-facing site hooks.
		 */
		require plugin_dir_path( __FILE__ ) . 'includes/class-one-click-order-re-order.php';

		/**
		 * Begins execution of the plugin.
		 *
		 * Since everything within the plugin is registered via hooks,
		 * then kicking off the plugin from this point in the file does
		 * not affect the page life cycle.
		 *
		 * @since    1.0.0
		 */
		function one_click_order_re_order_auto_update() {
			
			$license_key = get_option( 'one_click_order_re_order_lcns_key', '' );
			define( 'ONE_CLICK_ORDER_RE_ORDER_LICENSE_KEY', $license_key );
			define( 'ONE_CLICK_ORDER_RE_ORDER_BASE_FILE', __FILE__ );
			$update_check = "https://makewebbetter.com/pluginupdates/one-click-order-re-order/update.php";
			require_once( 'one-click-order-re-order-update.php' );
		 }

		function run_one_click_order_re_order() {

			define_one_click_order_re_order_constants();
			
			one_click_order_re_order_auto_update();

			$plugin = new One_Click_Order_Re_order();
			$plugin->run();

		}
		run_one_click_order_re_order();

		// Add settings link on plugin page.
		add_filter( 'plugin_action_links_'.plugin_basename(__FILE__), 'one_click_order_re_order_settings_link' );

		// Settings link.
		function one_click_order_re_order_settings_link( $links ) {

			$my_link = array(
				'<a href="' . admin_url( 'admin.php?page=one_click_order_re_order_menu' ) . '">' . __( 'Settings', 'one-click-order-re-order' ) . '</a>',
				);
			return array_merge( $my_link, $links );
		}

		// Plugin Auto Update.
		
}
else {
	function mwb_cng_plugin_error_notice() {
		?>
		<style type="text/css">
		#message{display: none;}</style>
		<div class="error notice is-dismissible">
			<p><?php _e( 'WooCommerce is not activated. Please install WooCommerce first, to use the One Click Order Re-Order plugin !!!', 'one-click-order-re-order' ); ?></p>
		</div>
	<?php
	}
	
	add_action ( 'admin_init','mwb_cng_plugin_deactivate' );
	function mwb_cng_plugin_deactivate() {
		deactivate_plugins ( plugin_basename ( __FILE__ ) );
		add_action ( 'admin_notices','mwb_cng_plugin_error_notice' );
	}
}


