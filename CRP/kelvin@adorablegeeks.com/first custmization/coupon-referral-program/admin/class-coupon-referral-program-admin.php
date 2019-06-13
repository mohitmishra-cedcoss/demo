<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://woocommerce.com/
 * @since      1.0.0
 *
 * @package    Coupon_Referral_Program
 * @subpackage Coupon_Referral_Program/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Coupon_Referral_Program
 * @subpackage Coupon_Referral_Program/admin
 * @author     Makewebbetter
 */
class Coupon_Referral_Program_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles( $hook ) {

		// Enqueue styles only on this plugin's menu page.
		if($hook == 'woocommerce_page_wc-settings') {

			wp_enqueue_style( $this->plugin_name, COUPON_REFERRAL_PROGRAM_DIR_URL . 'admin/css/coupon-referral-program-admin.css', array(), $this->version, 'all' );

			// Enqueue style for using WooCommerce Tooltip.
			wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );

		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts( $hook ) {

		// Enqueue scripts only on this plugin's menu page.
		if($hook == 'woocommerce_page_wc-settings') {
			wp_enqueue_script( $this->plugin_name . 'admin-js', COUPON_REFERRAL_PROGRAM_DIR_URL . 'admin/js/crp-admin.js', $this->version, false );

			// Enqueue and Localize script for using WooCommerce Tooltip.

			wp_enqueue_script( 'woocommerce_admin', WC()->plugin_url() . '/assets/js/admin/woocommerce_admin.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip', 'wc-enhanced-select' ), WC_VERSION );
			$params = array(
				'strings' => '',
				'urls' => '',
				);
			$translation = array('img_url'=>COUPON_REFERRAL_PROGRAM_DIR_URL . 'public/images/bg.png');
			wp_enqueue_media();

			wp_localize_script( 'woocommerce_admin', 'woocommerce_admin', $params );wp_localize_script( $this->plugin_name . 'admin-js', 'woocommerce_img', $translation );
		}

	}

	/**
	 * Adding settings menu for Coupon Referral Program in Woocommerce Settings Page.
	 *
	 * @since    1.0.0
	 * @return array of settings.
	 */

	public function woocommerce_settings_tabs_option( $settings_tabs ) {
		$settings_tabs['crp-referral_setting'] = __( 'Referrals', 'coupon-referral-program' );
		return $settings_tabs;
	}


	/**
	 * Display the html of each setting page
	 *
	 * @since    1.0.0
	 *
	 */

	public function crp_referral_settings_tab() {

		global $current_section;
		
		woocommerce_admin_fields( self::crp_get_settings( $current_section ) );
	}

	/**
	 * Display the html of each sections using Setting API
	 *
	 * @since    1.0.0
	 *
	 */

	function crp_get_settings( $current_section ) {

		$settings = array();
		if ( $current_section == '') {
			$settings = array(

				array(
					'title' => __( 'General referrals ', 'coupon-referral-program' ),
					'type' 	=> 'title',
					),

				array(
					'title'         => __( 'Enable/Disable ', 'coupon-referral-program' ),
					'desc'          => __( 'Enable/Disable coupon referral program ', 'coupon-referral-program' ),
					'default'       => 'no',
					'type'          => 'checkbox',
					'id' 			=> 'mwb_crp_plugin_enable',
					),

				array(
					'title'         => __( 'Referral key length', 'coupon-referral-program' ),
					'default'       => 7,
					'type'          => 'number',					
					'custom_attributes'   => array('min'=>'7', 'max' => '10'),
					'id' 			=> 'mwb_referral_length',
					'class' 			=> 'mwb_crp_input_val',
					'desc_tip' 		=> __('Set the length for referral key. The minimum & maximum length a referral key can have are 7 & 10.', 'coupon-referral-program'),
					),

				/*array(
					'title'         => __( 'Referral discount', 'coupon-referral-program' ),
					'default'       => 1,
					'type'          => 'number',					
					'custom_attributes'   => array('min'=>'1'),
					'id' 			=> 'referral_discount_on_order',
					'class' 			=> 'mwb_crp_input_val',
					'desc_tip' 		=> __('Enter the discount value you want to give your customers, who have referred other users on your site. This value will be calculated (in %) over the order total of your referred users.', 'coupon-referral-program'),
					),*/

				/*array(
					'title'         => __( 'Required no. of orders', 'coupon-referral-program' ),
					'default'       => 1,
					'type'          => 'number',
					'custom_attributes'   => array('min'=>'1'),
					'id' 			=> 'restrict_no_of_order',
					'class' 			=> 'mwb_crp_input_val',
					'desc_tip' 		=> __( 'Set the minimum number of orders customers required to get the referral discount.'),
					),*/

				
				array(
					'title'         => __( 'Pop-up image', 'coupon-referral-program' ),
					'type'          => 'text',
					'default'       => self::get_selected_image(),
					'id' 			=> 'mwb_cpr_image',
					'desc_tip' 		=> __('Select the background image for your popup','coupon-referral-program'),
					'desc'			=> '<div class="mwb_crp_image"><button class="mwb_crp_image_button button ">'.__( 'Upload', 'coupon-referral-program' ).'</button><button class="mwb_crp_image_resetbtn button">'.__( 'Reset', 'coupon-referral-program' ).'</button></div><div class="mwb_cpr_image_display"><img id="mwb_cpr_image_display" width="100" height="100" src="'.self::get_selected_image().'"></div>',
					),

				array(
					'type' 	=> 'sectionend',
					),

				);
		}

		if ($current_section == 'social' ) {
			$settings = array(
				array(
					'title' => __( 'Social sharing', 'coupon-referral-program' ),
					'type' 	=> 'title',
					),

				array(
					'title'         => __( 'Enable/Disable social sharing', 'coupon-referral-program' ),
					'desc'          => __( 'Enable/Disable social sharing', 'coupon-referral-program' ),
					'default'       => 'no',
					'type'          => 'checkbox',
					'id' 			=> 'mwb_cpr_social_enable'
					),

				array(
					'title'         => __( 'Facebook', 'coupon-referral-program' ),
					'default'       => 'no',
					'type'          => 'checkbox',
					'id' 			=> 'mwb_cpr_facebook'
					),

				array(
					'title'         => __( 'Twitter', 'coupon-referral-program' ),
					'default'       => 'no',
					'type'          => 'checkbox',
					'id' 			=> 'mwb_cpr_twitter'
					),

				array(
					'title'         => __( 'Email', 'coupon-referral-program' ),
					'default'       => 'no',
					'type'          => 'checkbox',
					'id' 			=> 'mwb_cpr_email'
					),
				array(
					'type' 	=> 'sectionend',
					),
				);
		}
		if ($current_section == 'display' ) {
			$settings = array(
				array(
					'title' => __( 'Display configuration', 'coupon-referral-program' ),
					'type' 	=> 'title',
					),

				array(
					'title'         => __( 'Enable/Disable', 'coupon-referral-program' ),
					'desc'          => __( 'Enable/Disable popup button', 'coupon-referral-program' ),
					'default'       => 'yes',
					'type'          => 'checkbox',
					'id' 			=> 'mwb_cpr_button_enable'
					),

				array(
					'title'         => __( 'Enable/Disable animation', 'coupon-referral-program' ),
					'desc'          => __( 'Enable this checkbox if you want animation over the referral button', 'coupon-referral-program' ),
					'default'       => 'no',
					'type'          => 'checkbox',
					'id' 			=> 'mwb_crp_animation',
					),

				array(
					'title'         => __( 'Button text', 'coupon-referral-program' ),
					'type'          => 'text',
					'default'       => __('Referral program','coupon-referral-program'),
					'id' 			=> 'referral_button_text',
					'class' 			=> 'mwb_crp_input_val',
					'desc_tip' 		=> __('Enter the text you want to display on the button.','coupon-referral-program'),
					),

				array(
					'title'         => __( 'Button color', 'coupon-referral-program' ),
					'type'          => 'color',
					'default'       => '#E85E54',
					'id' 			=> 'referral_button_color',
					'desc_tip' 		=> __('Select the color you want to have on the button and on the popup','coupon-referral-program'),
					'desc'			=> '<div class="mwb_crp_preview_div" style="background-color:'.self::get_selected_color().';">'.self::get_visible_text().'</div>',
					),

				array(
					'title'         => __( 'Custom css', 'coupon-referral-program' ),
					'type'          => 'textarea',
					'id' 			=> 'referral_button_custom_css',
					'class' 			=> 'mwb_crp_input_val',
					'desc_tip' 		=> __('Enter the css you want to apply on the button','coupon-referral-program'),
					),

				array(
					'title'         => __( 'Select Position', 'coupon-referral-program' ),
					'type'          => 'select',
					'id' 			=> 'referral_button_positioning',
					'class' 			=> 'wc-enhanced-select',
					'desc'          => __('Use this shortcode [crp_popup_button] to display pop-up button.','coupon-referral-program'),
					'desc_tip' 		=> __('Select whether you want to display the Referral Button left, right, or want to use a shortcode.  If you select the shortcode [crp_popup_button] then the referral button will work only on the page you use this code. Note- If you select shortcode then Select Pages will not work plus you can’t change the position of the Button.','coupon-referral-program'),
					'options'      =>array(
						'right_bottom'=>__('Right Bottom','coupon-referral-program'),
						'left_bottom'=>__('Left Bottom','coupon-referral-program'),
						'top_left' =>__('Top Left','coupon-referral-program'),
						'top_right' =>__('Top Right','coupon-referral-program'),
						'shortcode'=>__('shortcode','coupon-referral-program'),
						),
					),

				array(
					'title'         => __( 'Select pages', 'coupon-referral-program' ),
					'type'          => 'multiselect',
					'id' 			=> 'referral_button_page',
					'class' 			=> 'wc-enhanced-select',
					'desc_tip' 		=> __('Select the page where you want to display the button, leave blank if you want to display on all the pages.','coupon-referral-program'),
					'options'      =>self::get_pages(),
					),
				array(
					'title' => __( 'Use this shortcode for the referral link', 'coupon-referral-program' ),
					'type' 	=> 'text',
					'desc_tip' 		=> __('You can use the given shortcode anywhere you want, it will display the referral link of your customers','coupon-referral-program'),
					'default'       => '[crp_referral_link]',
					'id' 			=> 'mwb_crp_referral_link',
					),

				array(
					'type' 	=> 'sectionend',
					),
				);
		}
		if ($current_section == 'signup' ) {
			$settings = array(
				array(
					'title' => __( 'Sign up discount', 'coupon-referral-program' ),
					'type' 	=> 'title',
					),

				// array(
				// 	'title'         => __( 'Enable/Disable discount', 'coupon-referral-program' ),
				// 	'desc'          => __( 'Enable/Disable discount on sign up', 'coupon-referral-program' ),
				// 	'default'       => 'no',
				// 	'type'          => 'checkbox',
				// 	'id' 			=> 'mwb_crp_signup_enable'
				// 	),
				array(
					'title'         => __( 'Enter discount', 'coupon-referral-program' ),
					'default'       => 1,
					'type'          => 'number',
					'desc_tip' 		=> __('The value you enter will be set as discount coupon amount.','coupon-referral-program'),
					'id' 			=> 'signup_discount_value',
					'class' 			=> 'mwb_crp_input_val',
					'custom_attributes'=> array('min'=>1),
					'desc'          => __( 'In '.self::get_discount_type().'', 'coupon-referral-program' ),
					'desc'          => __( 'In', 'coupon-referral-program' ).self::get_discount_type(),
					),

				array(
					'type' 	=> 'sectionend',
					),
				);
		}

		if ($current_section == 'coupon' ) {

			$settings = array(
				array(
					'title' => __( 'Common coupon settings for both referral & sign up', 'coupon-referral-program' ),
					'type' 	=> 'title',
					),

				array(
					'title'         => __( 'Coupon configuration', 'coupon-referral-program' ),
					'default'       => 1,
					'type'          => 'select',
					'id' 			=> 'signup_discount_type',
					'css'			=> 'width:25%',
					'options'		=> array('mwb_cpr_fixed'=>__('Fixed','coupon-referral-program'),'mwb_cpr_percent'=>__('Percentage','coupon-referral-program')),
					'desc_tip'		=> __( 'Select the type for your discount coupon', 'coupon-referral-program' )
					),

				array(
					'title'         => __( 'Individual use of coupon', 'coupon-referral-program' ),
					'desc'			=> __( 'Permit separate use of coupons','coupon-referral-program' ),
					'default'       => 'no',
					'type'          => 'checkbox',
					'id' 			=> 'coupon_individual',
					'desc_tip'		=> __('Enable this checkbox if the coupon can’t be used in conjunction with other coupons','coupon-referral-program'),
					),

				array(
					'title'         => __( 'Free shipping', 'coupon-referral-program' ),
					'desc'          => __( 'Permit free shipping on coupons', 'coupon-referral-program' ),
					'default'       => 'no',
					'type'          => 'checkbox',
					'id' 			=> 'coupon_freeshipping',
					'desc_tip'		=> __('Enable this checkbox, if the coupon permits free shipping to customers. Note: A free shipping method must be enabled in your shipping zone and must be set to “require a valid free shipping coupon".','coupon-referral-program'),
					),
				array(
					'title'         => __( 'Enter coupon length', 'coupon-referral-program' ),
					'default'       => 5,
					'type'          => 'number',
					'id' 			=> 'coupon_length',
					'class' 			=> 'mwb_crp_input_val',
					'custom_attributes'=> array('min'=>5, 'max'=>10),
					'desc_tip'		=>  __('Set the coupon length excluding the prefix.
						(The minimum length you can set is 5)', 'coupon-referral-program'),
					),


				array(
					'title'         => __( 'Coupon expire after days', 'coupon-referral-program' ),
					'default'       => 0,
					'type'          => 'number',
					'id' 			=> 'coupon_expiry',
					'class' 			=> 'mwb_crp_input_val',
					'custom_attributes'=> array('min'=>0),
					'desc_tip'		=>  __('Enter the days after which the coupon will get expire. Set the value to “1” if the coupon will expire in one-day And set the value to “0” if the coupon has no fix expiry date.', 'coupon-referral-program'),
					),

				array(
					'title'         => __( 'No of time coupon can be use', 'coupon-referral-program' ),
					'default'       => 0,
					'type'          => 'number',
					'id' 			=> 'coupon_usage',
					'class' 			=> 'mwb_crp_input_val',				
					'desc_tip'		=>  __('How many times the coupon can be used before it gets expired.', 'coupon-referral-program'),
					),

				array(
					'title'         => __( 'Add prefix on coupon', 'coupon-referral-program' ),
					'default'       => '',
					'type'          => 'text',
					'id' 			=> 'coupon_prefix',
					'class' 			=> 'mwb_crp_input_val',
					'desc_tip'		=>  __('If you desire to add a prefix to your coupon, you can add here.', 'coupon-referral-program'),
					),


				array(
					'type' 	=> 'sectionend',
					),
				);
		}

		return apply_filters( 'crp_get_settings', $settings );
	}

	/**
	 * Save the data using Setting API
	 *
	 * @since    1.0.0
	 *
	 */

	public function crp_referral_setting_save() {

		global $current_section;
		$settings = $this->crp_get_settings( $current_section );
		WC_Admin_Settings::save_fields( $settings );
	}
    /**
	 * Get the color and set it for the Button's color(Referral Program Button)
	 *
	 * @since    1.0.0
	 *
	 */
    public static function get_pages()
    {
    	$mwb_page_title=array();
    	$mwb_pages = get_pages(); 
    	foreach ($mwb_pages as $pagedata) {
    		$mwb_page_title[$pagedata->ID] = $pagedata->post_title;
    	}
    	$mwb_page_title['details'] = 'Product Detail';
    	return $mwb_page_title;
    }
	/**
	 * Get the color and set it for the Button's color(Referral Program Button)
	 *
	 * @since    1.0.0
	 *
	 */


	public static function get_selected_color(){
		$referral_button_color = get_option('referral_button_color','#E85E54');
		return $referral_button_color;
	}
	/**
	 * Get the image url and set it for image
	 *
	 * @since    1.0.0
	 *
	 */

	public static function get_selected_image(){
		$mwb_default_image =COUPON_REFERRAL_PROGRAM_DIR_URL.'public/images/bg.png';
		$referral_image_url = get_option('mwb_cpr_image',$mwb_default_image);
		return $referral_image_url;
	}

	/**
	 * Get the text for Referral Button
	 *
	 * @since    1.0.0
	 *
	 */

	public static function get_visible_text(){
		$referral_button_text = get_option('referral_button_text','Referral Program');
		return $referral_button_text;
	}
    /**
	 * Get the discount type 
	 *
	 * @since    1.0.0
	 *
	 */

    public static function get_discount_type(){
    	$referral_discount_type = get_option('signup_discount_type','fixed');
    	if($referral_discount_type == 'mwb_cpr_percent')
    	{
    		$referral_discount_type="percentage";
    	}
    	else
    	{
    		$referral_discount_type="fixed";
    	}
    	return $referral_discount_type;
    }

	/**
	 * Print the sections
	 *
	 * @since    1.0.0
	 *
	 */

	public function crp_output_sections() {

		global $current_section;
		$sections = self::crp_get_sections();

		echo '<ul class="subsubsub">';

		$array_keys = array_keys( $sections );

		foreach ( $sections as $id => $label ) {
			echo '<li><a href="' . admin_url( 'admin.php?page=wc-settings&tab=crp-referral_setting&section=' . sanitize_title( $id ) ) . '" class="' . ( $current_section == $id ? 'current' : '' ) . '">' . $label . '</a> ' . ( end( $array_keys ) == $id ? '' : '|' ) . ' </li>';
		}
		echo '</ul><br class="clear">';
	}

	/**
	 * Set the array for each sections
	 *
	 * @since    1.0.0
	 *
	 */

	function crp_get_sections(){

		$sections = array(
			''             	=>  __( 'General referrals', 'coupon-referral-program' ),
			'social'             	=>  __( 'Social sharing', 'coupon-referral-program' ),
			'signup'             	=>  __( 'Sign up discount', 'coupon-referral-program' ),
			'coupon'             	=>  __( 'Coupon configuration', 'coupon-referral-program' ),
			'display'             	=>  __( 'Display configuration', 'coupon-referral-program' ),
			);

		return apply_filters( 'crp_get_sections', $sections );
	}

}
