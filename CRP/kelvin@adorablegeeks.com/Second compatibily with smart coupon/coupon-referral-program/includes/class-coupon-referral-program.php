<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://woocommerce.com/
 * @since      1.0.0
 *
 * @package    coupon-referral-program
 * @subpackage coupon-referral-program/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    coupon-referral-program
 * @subpackage coupon-referral-program/includes
 * @author     WooCommerce
 */
class Coupon_Referral_Program {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      coupon-referral-program-loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		
		if ( defined( 'COUPON_REFERRAL_PROGRAM_VERSION' ) ) {

			$this->version = COUPON_REFERRAL_PROGRAM_VERSION;
		} 
		
		else {

			$this->version = '1.0.0';
		}

		$this->plugin_name = 'coupon-referral-program';

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->init();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Coupon_Referral_Program_Loader. Orchestrates the hooks of the plugin.
	 * - Coupon_Referral_Program_i18n. Defines internationalization functionality.
	 * - Coupon_Referral_Program_Admin. Defines all hooks for the admin area.
	 * - Coupon_Referral_Program_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-coupon-referral-program-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-coupon-referral-program-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-coupon-referral-program-public.php';

		$this->loader = new Coupon_Referral_Program_Loader();

	}

	public function init(){
		add_filter( 'woocommerce_email_classes', array($this, 'crp_woocommerce_email_classes' ));
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Coupon_Referral_Program_Admin( $this->get_plugin_name(), $this->get_version() );
		
		$this->id = 'crp-referral_setting';
		// All admin actions and filters goes here.

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_filter( 'woocommerce_settings_tabs_array', $plugin_admin,'woocommerce_settings_tabs_option', 50 );
		$this->loader->add_action( 'woocommerce_settings_tabs_' . $this->id, $plugin_admin, 'crp_referral_settings_tab' );
		$this->loader->add_action( 'woocommerce_settings_save_' . $this->id, $plugin_admin, 'crp_referral_setting_save' );
		$this->loader->add_action( 'woocommerce_sections_' . $this->id, $plugin_admin, 'crp_output_sections' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Coupon_Referral_Program_Public( $this->get_plugin_name(), $this->get_version() );

		// All public actions and filters after License Validation goes here.

		// Check if plugin is enabled.
		$enable = get_option( 'mwb_crp_plugin_enable', false );

		if($enable =='yes') {

			$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
			$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
			$this->loader->add_action( 'wp_footer', $plugin_public, 'mwb_crp_load_html' );
			$this->loader->add_action( 'woocommerce_created_customer',$plugin_public,'woocommerce_created_customer_discount' );
			$this->loader->add_action( 'wp_loaded', $plugin_public, 'wp_loaded_set_referral_key' );
			// $this->loader->add_action( 'woocommerce_order_status_changed',$plugin_public, 'woocommerce_order_status_changed_discount',10,3 );
			$this->loader->add_action( 'woocommerce_account_dashboard',$plugin_public,'woocommerce_account_dashboard_social_sharing' );
			$this->loader->add_action( 'wp_head',$plugin_public,'woocommerce_referral_button_show' );
			$this->loader->add_action('init',$plugin_public,'woocommerce_register_shortcode');

			// $this->loader->add_action('woocommerce_account_wc-smart-coupons_endpoint', $plugin_public, 'test');
			$this->loader->add_filter('wc_smart_coupons_global_coupons', $plugin_public, 'mwb_crp_wsc_compatibility');

		}

	}

	public function crp_woocommerce_email_classes($emails){
		$emails['crp_signup_email'] = include COUPON_REFERRAL_PROGRAM_DIR_PATH . 'emails/class-coupon-referral-program-emails.php';
		$emails['crp_order_email'] = include COUPON_REFERRAL_PROGRAM_DIR_PATH . 'emails/class-coupon-referral-program-order-emails.php';
        return $emails;
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Coupon_Referral_Program_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
