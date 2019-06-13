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
 * @package           coupon-referral-program
 *
 * Plugin Name:       Coupon Referral Program
 * Plugin URI:        https://woocommerce.com/products/coupon-referral-program/
 * Description:       This extension is mainly to install a referral program on your site and share the discount coupons in return.
 * Version:           1.1.0
 * Author:            MakeWebBetter
 * Author URI:        https://makewebbetter.com/
 * Developer: 		  makewebbetter
 * Developer URI:	  https://makewebbetter.com/
 * Text Domain:       coupon-referral-program
 * Domain Path:       /languages
 * Woo: 3820066:337863f09a287f1aaa7ad10d885a170e
 * Requires at least:        4.6
 * Tested up to: 	         5.0.0
 * WC requires at least:     3.0
 * WC tested up to:          3.5.0
 *
 * Copyright: 		  © 2009-2018 WooCommerce.
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// To Activate plugin only when WooCommerce is active.
$activated = true;

// Check if WooCommerce is active.
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

	$activated = false;
}

if ( $activated ) {

	// Define plugin constants.
	function define_coupon_referral_program_constants() {

		coupon_referral_program_constants( 'COUPON_REFERRAL_PROGRAM_VERSION', '1.0.0' );
		coupon_referral_program_constants( 'COUPON_REFERRAL_PROGRAM_DIR_PATH', plugin_dir_path( __FILE__ ) );
		coupon_referral_program_constants( 'COUPON_REFERRAL_PROGRAM_DIR_URL', plugin_dir_url( __FILE__ ) );
	}

	// Callable function for defining plugin constants.
	function coupon_referral_program_constants( $key, $value ) {

		if( ! defined( $key ) ) {
			
			define( $key, $value );
		}
	}

	/**
	 * The code that runs during plugin activation.
	 */
	function activate_coupon_referral_program() {
		// Create transient data.
    	set_transient( 'coupon_referral_program_transient_user_exp_notice', true, 5 );
	}

	// Add admin notice only on plugin activation.
	add_action( 'admin_notices', 'coupon_referral_program_user_exp_notice' );	

	// Facebook setup notice on plugin activation.
	function coupon_referral_program_user_exp_notice() {

		/**
		 * Check transient.
		 * If transient available display notice.
		 */
		if( get_transient( 'coupon_referral_program_transient_user_exp_notice' ) ):

			?>

			<div class="notice notice-info is-dismissible">
				<p><strong><?php _e( 'Welcome to Coupon Referral Program –', 'coupon-referral-program' ); ?></strong><?php _e( ' To get started, enable the plugin on the', 'coupon-referral-program' ); ?> <a href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=crp-referral_setting' ); ?>"><?php _e('settings page','coupon-referral-program');?></a>.</p>
				<p class="mwb_cpr_submit"><a href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=crp-referral_setting' ); ?>" class="button-primary"><?php _e( 'Go to Settings', 'coupon-referral-program' ); ?></a></p>
			</div>

			<?php

			delete_transient( 'coupon_referral_program_transient_user_exp_notice' );

		endif;
	}

	register_activation_hook( __FILE__, 'activate_coupon_referral_program' );

	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	require plugin_dir_path( __FILE__ ) . 'includes/class-coupon-referral-program.php';

	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since    1.0.0
	 */
	function run_coupon_referral_program() {

		define_coupon_referral_program_constants();

		$plugin = new Coupon_Referral_Program();
		$plugin->run();

	}
	add_action( 'plugins_loaded', 'run_coupon_referral_program');

	// Add settings link on plugin page.
	add_filter( 'plugin_action_links_'.plugin_basename(__FILE__), 'coupon_referral_program_settings_link' );

	// Settings link.
	function coupon_referral_program_settings_link( $links ) {

	    $setting_link = array(
	    		'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=crp-referral_setting' ) . '">' . __( 'Settings', 'coupon-referral-program' ) . '</a>',
	    	);
	    return array_merge( $setting_link, $links );
	}
     //SIGNUPNOTIFICATION
	add_shortcode('signup_notification', 'mwb_crp_signup_notification' );

	function mwb_crp_signup_notification(){
		$public_obj = new Coupon_Referral_Program_Public('Coupon Referral Program', '1.0.0'); 
		if($public_obj->check_signup_is_enable() && !is_user_logged_in()){
			$mwb_cpr_coupon_amount = $public_obj->mwb_get_coupon_amount();
			$mwb_cpr_discount_type = $public_obj->mwb_get_discount_type();
			$mwb_formatted_amount = mwb_signup_discount_amount( $mwb_cpr_coupon_amount,$mwb_cpr_discount_type )
			?>
			<div class="woocommerce-message">
				<?php 
					echo  __( 'Signup Yourself and Get Discount Coupon of ', 'coupon-referral-program' ) .$mwb_formatted_amount;
				 ?>
			</div>
		<?php
		}
	}

	function mwb_signup_discount_amount($mwb_cpr_coupon_amount ,$mwb_cpr_discount_type){

		if($mwb_cpr_discount_type == "mwb_cpr_fixed"){
			$mwb_signup_discount_amount = '<span style="font-weight: bold;">'.wc_price($mwb_cpr_coupon_amount).'</span>';
		}else{
			$mwb_signup_discount_amount = '<span style="font-weight: bold;">'.$mwb_cpr_coupon_amount.'%</span>';
		}
		return $mwb_signup_discount_amount;
	}

	/**
	 * Generate random number referral key.
	 *
	 * @since 1.0.0
	 * @return referral key
	 */
	function generate_referral_key() {
		$length = get_option('mwb_referral_length',10);
		$pkey = '';
		$alphabets = range('A','Z');
		$numbers = range('0','9');
		$final_array = array_merge($alphabets,$numbers);
		while($length--)
		{
			$key = array_rand($final_array);
			$pkey .= $final_array[$key];
		}
		return $pkey;
	}

	/**
	 * Check whether the Coupon Referral Program is enabled or not
	 *
	 * @since 1.0.0
	 * @param $order_id, $old_status, $new_status
	 */

	function is_enable_coupon_referral_program() {
		$enable = get_option( 'mwb_crp_plugin_enable', false );
		if(get_option( 'mwb_crp_plugin_enable', false ) == 'yes'){
			$enable = true;
		}
		else{
			$enable = false;
		}
		return $enable;
	}

	add_action( 'plugins_loaded', 'crp_load_plugin_textdomain' );

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */

	function crp_load_plugin_textdomain() {
	

		load_plugin_textdomain(
			'coupon-referral-program',
			false,
			dirname( plugin_basename( __FILE__ ) )  . '/languages/'
		);
	}

}

else {

	// WooCommerce is not active so deactivate this plugin.
	add_action( 'admin_init', 'coupon_referral_program_activation_failure' );

	// Deactivate this plugin.
	function coupon_referral_program_activation_failure() {

		deactivate_plugins( plugin_basename( __FILE__ ) );
	}

	// Add admin error notice.
	add_action( 'admin_notices', 'coupon_referral_program_activation_failure_admin_notice' );

	// This function is used to display admin error notice when WooCommerce is not active.
	function coupon_referral_program_activation_failure_admin_notice() {

		// to hide Plugin activated notice.
		unset( $_GET['activate'] );

	    ?>

	    <div class="notice notice-error is-dismissible">
	        <p><?php _e( 'WooCommerce is not activated, Please activate WooCommerce first to activate Coupon Referral Program.','coupon-referral-program' ); ?></p>
	    </div>

	    <?php
	}
}
