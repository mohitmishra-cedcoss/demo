<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    One_Click_Order_Re_order
 * @subpackage One_Click_Order_Re_order/includes
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
 * @package    One_Click_Order_Re_order
 * @subpackage One_Click_Order_Re_order/includes
 * @author     makewebbetter <webmaster@makewebbetter.com>
 */
class One_Click_Order_Re_order {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      One_Click_Order_Re_order_Loader    $loader    Maintains and registers all hooks for the plugin.
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
		
		if ( defined( 'ONE_CLICK_ORDER_RE_ORDER_VERSION' ) ) {

			$this->version = ONE_CLICK_ORDER_RE_ORDER_VERSION;
		} 
		
		else {

			$this->version = '1.0.0';
		}

		$this->plugin_name = 'one-click-order-re-order';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - One_Click_Order_Re_order_Loader. Orchestrates the hooks of the plugin.
	 * - One_Click_Order_Re_order_i18n. Defines internationalization functionality.
	 * - One_Click_Order_Re_order_Admin. Defines all hooks for the admin area.
	 * - One_Click_Order_Re_order_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-one-click-order-re-order-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-one-click-order-re-order-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-one-click-order-re-order-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-one-click-order-re-order-public.php';

		$this->loader = new One_Click_Order_Re_order_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the One_Click_Order_Re_order_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new One_Click_Order_Re_order_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new One_Click_Order_Re_order_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Add settings menu for One Click Order Re-Order.
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_options_page' );
		//Add thick box
		$this->loader->add_action( 'init', $plugin_admin, 'mwb_cng_register_thick_box' );

		// Running action for ajax license.
		$this->loader->add_action( 'wp_ajax_one_click_order_re_order_license', $plugin_admin, 'validate_license_handle' );

		$callname_lic = One_Click_Order_Re_order::$lic_callback_function;
		$callname_lic_initial = One_Click_Order_Re_order::$lic_ini_callback_function;
		$day_count = One_Click_Order_Re_order::$callname_lic_initial();

		// Condition for validating.
		if( One_Click_Order_Re_order::$callname_lic() || 0 <= $day_count ) {

			// All admin actions and filters after License Validation goes here.

			// Using Settings API for settings menu.
			$this->loader->add_action( 'admin_init', $plugin_admin, 'settings_api' );

			// Daily ajax license action.
			$this->loader->add_action( 'one_click_order_re_order_license_daily', $plugin_admin, 'validate_license_daily' );
		}

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new One_Click_Order_Re_order_Public( $this->get_plugin_name(), $this->get_version() );

		$callname_lic = One_Click_Order_Re_order::$lic_callback_function;
		$callname_lic_initial = One_Click_Order_Re_order::$lic_ini_callback_function;
		$day_count = One_Click_Order_Re_order::$callname_lic_initial();

		// Condition for validating.
		if( One_Click_Order_Re_order::$callname_lic() || 0 <= $day_count ) {

			// All public actions and filters after License Validation goes here.

			// Check if plugin is enabled.
			//$enable = get_option( 'one_click_order_re_order_enable_plug', 'yes' );

			//if( 'yes' === $enable ) {

				$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
				$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
				//add filter for button text
			  $this->loader->add_filter( 'woocommerce_my_account_my_orders_actions', $plugin_public,'mwb_cng_add_button', 10, 2 );
			  //add product into cart for button text
			  $this->loader->add_action( 'wp_ajax_get_order_cart',$plugin_public,MWB_PREFIX.'_prefix_ajax_get_order_cart');
			  $this->loader->add_action( 'wp_ajax_nopriv_get_order_cart',$plugin_public,MWB_PREFIX.'_prefix_ajax_get_order_cart' );
			  $this->loader->add_action( 'woocommerce_order_details_after_order_table',$plugin_public,MWB_PREFIX.'_add_edit_order_button', 10, 1 );
			   $this->loader->add_action('after_setup_theme',$plugin_public,'mwb_ocor_close_know_more_email');

			   //Add to the basket
			   $this->loader->add_action( 'wp_ajax_mwb_ocor_add_basket_items_to_cart',$plugin_public, 'mwb_ocor_add_basket_items_to_cart' );


			/**
			 * Get all products for an order.
			 */
			$this->loader->add_action ( 'wp_ajax_get_oreder_products',$plugin_public, MWB_PREFIX . '_get_oreder_products' );
			$this->loader->add_action ( 'wp_ajax_nopriv_get_oreder_products', $plugin_public, MWB_PREFIX . '_get_oreder_products' ) ;

			 // Add Same order in the cart
			$this->loader-> add_action ( 'wp_ajax_get_same_order_cart',$plugin_public,'mwb_cng_prefix_ajax_get_same_order_cart' );
			$this->loader->add_action ( 'wp_ajax_nopriv_get_same_order_cart',$plugin_public,'mwb_cng_prefix_ajax_get_same_order_cart' );


           // Add Action for Basket Genral Setings
			$this->loader->add_action( 'wp_head',$plugin_public, 'mwb_ocor_initialize_frontend' );

          // show basket on every page
			$this->loader->add_action( 'wp_footer',$plugin_public, 'mwb_ocor_footer_stuffs' );

          //Add items into cart
			$this->loader->add_action( 'wp_ajax_mwb_ocor_add_to_basket',$plugin_public, 'mwb_ocor_add_to_basket' ) ;
			//Remove items from Basket
			$this->loader->add_action( 'wp_ajax_mwb_ocor_remove_from_basket',$plugin_public, 'mwb_ocor_remove_from_basket' );
			//Get Basket items from orders
			$this->loader-> add_action( 'wp_ajax_mwb_ocor_get_basket_items',$plugin_public, 'mwb_ocor_get_basket_items' );
			$this->loader->add_action('rest_api_init',$plugin_public,'mwb_rest_callback');
			if($this->check_popup_is_enable()) {
				$this->loader->add_action('wp_head',$plugin_public,'mwb_fetch_latest_order');
				$this->loader->add_action('wp_ajax_mwb_cng_hide_popup',$plugin_public,'mwb_cng_hide_popup');
				$this->loader->add_action('wp_logout',$plugin_public,'mwb_cng_clear_session');
			}
			
			//}
		}
		//Adding filter to add a button on my-account page

	}
	public function check_popup_is_enable() {
		$mwb_is_enable = false;
		$general_settings = get_option('one_click_order_re_order_enable_plug',false);
		if(isset($general_settings['enable_popup']) && $general_settings['enable_popup'] == 1) {
			$mwb_is_enable = true;
		}
		 return $mwb_is_enable;
		
	}
	// public static variable to be accessed in this plugin.
	public static $lic_callback_function = 'check_lcns_validity';

	// public static variable to be accessed in this plugin.
	public static $lic_ini_callback_function = 'check_lcns_initial_days';

	/**
	 * Validate the use of features of this plugin.
	 *
	 * @since    1.0.0
	 */
	public static function check_lcns_validity() {

		$one_click_order_re_order_lcns_key = get_option( 'one_click_order_re_order_lcns_key', '' );

		$one_click_order_re_order_lcns_status = get_option( 'one_click_order_re_order_lcns_status', '' );

		if( $one_click_order_re_order_lcns_key && 'true' === $one_click_order_re_order_lcns_status ) {
			
			return true;
		}

		else {

			return false;
		}
	}

	/**
	 * Validate the use of features of this plugin for initial days.
	 *
	 * @since    1.0.0
	 */
	public static function check_lcns_initial_days() {

		$thirty_days = get_option( 'one_click_order_re_order_lcns_thirty_days', 0 );

		$current_time = current_time( 'timestamp' );

		$day_count = ( $thirty_days - $current_time ) / (24 * 60 * 60);

		return $day_count;
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
	 * @return    One_Click_Order_Re_order_Loader    Orchestrates the hooks of the plugin.
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
