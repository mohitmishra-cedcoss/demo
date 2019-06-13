<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    One_Click_Order_Re_order
 * @subpackage One_Click_Order_Re_order/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    One_Click_Order_Re_order
 * @subpackage One_Click_Order_Re_order/admin
 * @author     makewebbetter <webmaster@makewebbetter.com>
 */
class One_Click_Order_Re_order_Admin {

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
        $this->basket_pages = apply_filters( 
				'mwb_ocor_basket_pages', 
				array(
					'shop' 		=> __( 'Shop', 'one-click-order-re-order' ),
					'detail' 	=> __( 'Product Detail', 'one-click-order-re-order' ),
					'cart' 		=> __( 'Cart', 'one-click-order-re-order' ),
					'account' 	=> __( 'My Account', 'one-click-order-re-order' )
				) 
			);
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles( $hook ) {

		// Enqueue styles only on this plugin's menu page.
		if( $hook != 'toplevel_page_one_click_order_re_order_menu' ){

			return;
		}

		wp_enqueue_style( $this->plugin_name, ONE_CLICK_ORDER_RE_ORDER_DIR_URL . 'admin/css/one-click-order-re-order-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'select2-css', WP_CONTENT_URL . '/plugins/woocommerce/assets/css/select2.css', array(), $this->version, 'all' );
		

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts( $hook ) {

		// Enqueue scripts only on this plugin's menu page.
		if( $hook != 'toplevel_page_one_click_order_re_order_menu' ){

			return;
		}
        
		wp_enqueue_script( $this->plugin_name . 'admin-js', ONE_CLICK_ORDER_RE_ORDER_DIR_URL . 'admin/js/one-click-order-re-order-admin.js', array( 'jquery','select2' ), $this->version, false );
		wp_localize_script( $this->plugin_name . 'admin-js', 'license_ajax_object', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'reloadurl' => admin_url( 'admin.php?page=one_click_order_re_order_menu' ),
			'license_nonce' => wp_create_nonce( 'one-click-order-re-order-license-nonce-action' ),
			) 
		);

	}

	/**
	 * Adding Settings Menu for One Click Order Re-Order.
	 *
	 * @since    1.0.0
	 */
	public function add_options_page() {

		add_menu_page(
			__( 'One Click Order Re-Order', 'one-click-order-re-order' ),
			__( 'One Click Order Re-Order', 'one-click-order-re-order' ),
			'manage_options',
			'one_click_order_re_order_menu',
			array( $this, 'options_menu_html' ),
			'',
			85
			);
	}
  public function mwb_cng_register_thick_box()
  {
  	add_thickbox();
  }
	/**
	 * One Click Order Re-Order admin menu page.
	 *
	 * @since    1.0.0
	 */
	public function options_menu_html() {

		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {

			return;
		}

		$callname_lic = One_Click_Order_Re_order::$lic_callback_function;
		$callname_lic_initial = One_Click_Order_Re_order::$lic_ini_callback_function;
		$day_count = One_Click_Order_Re_order::$callname_lic_initial();

		?>

		<div class="one-click-order-re-order-wrap">

			<?php

			// Condition for Warning notification.
			if ( ! One_Click_Order_Re_order::$callname_lic() && 0 <= $day_count ):

				$day_count_warning = floor( $day_count );

			$day_string = sprintf( _n( '%s day', '%s days', $day_count_warning, 'one-click-order-re-order' ), number_format_i18n( $day_count_warning ) );

			$day_string = '<span id="one-click-order-re-order-day-count" >'.$day_string.'</span>';

			?>

			<div id="one-click-order-re-order-thirty-days-notify" class="notice notice-warning">
				<p>
					<strong><a href="?page=one_click_order_re_order_menu&tab=license"><?php _e( 'Activate', 'one-click-order-re-order' ); ?></a><?php printf( __( ' the license key before %s or you may risk losing data and the plugin will also become dysfunctional.', 'one-click-order-re-order' ), $day_string ); ?></strong>
				</p>
			</div>

			<?php

			endif;

			?>

			<h2><?php _e('One Click Order Re-Order', 'one-click-order-re-order' ); ?></h2>

			<?php

			// Condition for validating.
			if( One_Click_Order_Re_order::$callname_lic() || 0 <= $day_count ) {

				$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general';

		     	// Redirect to default when tab value is not one of the valid ones.
				if( $active_tab != 'general' && $active_tab != 'license' && $active_tab != 'about_us' && $active_tab != 'help') {

					wp_redirect( admin_url( 'admin.php?page=one_click_order_re_order_menu' ) );
					exit;
				}

				?>

				<h2 class="nav-tab-wrapper">

					<a href="?page=one_click_order_re_order_menu&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>"><?php _e('General Options', 'one-click-order-re-order' ); ?></a>

					<?php if( ! One_Click_Order_Re_order::$callname_lic() ): ?>

						<a href="?page=one_click_order_re_order_menu&tab=license" class="nav-tab <?php echo $active_tab == 'license' ? 'nav-tab-active' : ''; ?>"><?php _e('License Activation', 'one-click-order-re-order' ); ?></a>

					<?php endif; ?>

					<a href="?page=one_click_order_re_order_menu&tab=help" class="nav-tab <?php echo $active_tab == 'help' ? 'nav-tab-active' : ''; ?>"><?php _e('Help', 'one-click-order-re-order' ); ?></a>

					<a href="?page=one_click_order_re_order_menu&tab=about_us" class="nav-tab <?php echo $active_tab == 'about_us' ? 'nav-tab-active' : ''; ?>"><?php _e('About Us', 'one-click-order-re-order' ); ?></a>

				</h2>

				<?php

				if( $active_tab == 'general' ) {

		    		// Menu HTML and PHP code for General Options goes here.

					echo '<form action="options.php" method="post">';

					settings_errors();

					settings_fields( 'one_click_order_re_order_gen_menu' );

					do_settings_sections( 'one_click_order_re_order_gen_menu' );

					submit_button( __('Save Options', 'one-click-order-re-order' ) );

					echo '</form>';

				}// endif General Options tab.

				elseif( $active_tab == 'help' ) {

		    		// Menu HTML and PHP code for Help Section goes here.

					require_once ONE_CLICK_ORDER_RE_ORDER_DIR_PATH . 'admin/partials/one-click-order-re-order-admin-help.php';

				}// endif Help Section tab.

				elseif( $active_tab == 'about_us' ) {

		    		// Menu HTML and PHP code for COntact Us Section goes here.

					require_once ONE_CLICK_ORDER_RE_ORDER_DIR_PATH . 'admin/partials/one-click-order-re-order-admin-about_us.php';

				}// endif Contact Us Section tab.

				elseif( $active_tab == 'license' && ! One_Click_Order_Re_order::$callname_lic() ) {

		    		// Menu HTML and PHP code for License Activation goes here.

					require_once ONE_CLICK_ORDER_RE_ORDER_DIR_PATH . 'admin/partials/one-click-order-re-order-admin-license.php';

				}// endif License Activation tab.

			}

			else{

				require_once ONE_CLICK_ORDER_RE_ORDER_DIR_PATH . 'admin/partials/one-click-order-re-order-admin-license.php';
			}

			?>

		</div> <!-- one-click-order-re-order-wrap -->

		<?php
	}

	/**
	 * Using Settings API for settings menu.
	 *
	 * @since    1.0.0
	 */
	public function settings_api() {

		register_setting( 'one_click_order_re_order_gen_menu', 'one_click_order_re_order_enable_plug' );

		add_settings_section(
			'one_click_order_re_order_gen_menu_sec',
			null,
			null,
			'one_click_order_re_order_gen_menu'
			);


		add_settings_field(
			'one_click_order_re_order_enable',
			__( 'Enable/Disable Place Same Order Button:', 'one-click-order-re-order' ),
			array( $this, 'enable_plugin_cb' ),
			'one_click_order_re_order_gen_menu',
			'one_click_order_re_order_gen_menu_sec',
			'plugin'
			);

		add_settings_field(
			'one_click_order_re_order_text',
			__( 'Enable/Disable Basket', 'one-click-order-re-order' ),
			array( $this, 'enable_plugin_cb' ),
			'one_click_order_re_order_gen_menu',
			'one_click_order_re_order_gen_menu_sec',
			'basket'
			);
		add_settings_field(
			'one_click_order_re_order_add_button_text',
			__( 'Basket add button text:', 'one-click-order-re-order' ),
			array( $this, 'enable_plugin_cb' ),
			'one_click_order_re_order_gen_menu',
			'one_click_order_re_order_gen_menu_sec',
			'button_text'
			);
		add_settings_field(
			'one_click_order_re_order_remove_button_text',
			__( 'Basket remove button text:', 'one-click-order-re-order' ),
			array( $this, 'enable_plugin_cb' ),
			'one_click_order_re_order_gen_menu',
			'one_click_order_re_order_gen_menu_sec',
			'button_remove_text'
			);
		add_settings_field(
			'one_click_order_re_order_upload_icon',
			__( '	Upload an icon image for basket icon:', 'one-click-order-re-order' ),
			array( $this, 'enable_plugin_cb' ),
			'one_click_order_re_order_gen_menu',
			'one_click_order_re_order_gen_menu_sec',
			'mwb_upload_icon'
			);
		add_settings_field(
			'one_click_order_re_order_role',
			__( 'Enable basket feature for:', 'one-click-order-re-order' ),
			array( $this, 'enable_plugin_cb' ),
			'one_click_order_re_order_gen_menu',
			'one_click_order_re_order_gen_menu_sec',
			'mwb_selected_role'
			);
		add_settings_field(
			'one_click_order_re_order_page',
			__( 'Choose pages to show the basket icon:', 'one-click-order-re-order' ),
			array( $this, 'enable_plugin_cb' ),
			'one_click_order_re_order_gen_menu',
			'one_click_order_re_order_gen_menu_sec',
			'mwb_selected_page'
			);
		add_settings_field(
			'one_click_order_re_order_page_popup',
			__( 'Enable this to show the latest order:', 'one-click-order-re-order' ),
			array( $this, 'enable_plugin_cb' ),
			'one_click_order_re_order_gen_menu',
			'one_click_order_re_order_gen_menu_sec',
			'mwb_enable_page_popup'
			);



	}

    /**
	 * Callback for Enable Plugin option.
	 *
	 * @since    1.0.0
	 */
    public function enable_plugin_cb($param) {

    	$general_settings = get_option('one_click_order_re_order_enable_plug',false);
    	if(isset($param) && $param == "mwb_enable_page_popup") {
    		?>
    		<!-- <label><?php _e("Enable this to use popup on homepage","one-click-order-re-order")?></label> -->
    		<input type="checkbox" <?php (isset($general_settings['enable_popup']))?checked( $general_settings['enable_popup'], 1 ):''?>name="one_click_order_re_order_enable_plug[enable_popup]" value="1"></input>
    		<?php
    	}
    	if(isset($param) && $param == 'plugin'){
    		?>
    		<div class="one-click-order-re-order-option-sec">

    			<label for="one_click_order_re_order_enable_plug">
    				<input type="radio" name="one_click_order_re_order_enable_plug[enable_plugin]"<?php if(isset($general_settings['enable_plugin'])&&!empty($general_settings['enable_plugin'])){checked('on',$general_settings['enable_plugin']);} ?>>
    				<?php _e( 'Enable.', 'one-click-order-re-order' ); ?>
    				<input type="radio" value="off" name="one_click_order_re_order_enable_plug[enable_plugin]" <?php
    				if(isset($general_settings['enable_plugin'])&&!empty($general_settings['enable_plugin'])){ checked('off',$general_settings['enable_plugin']); }?>>
    				<?php _e( 'Disable.', 'one-click-order-re-order' ); ?>	
    			</label>
    			
    		</div>
    		<?php
    	}
    	if(isset($param) && $param == 'basket'){
    		?>
    		<div class="one-click-order-re-order-option-sec">

    			<label for="one_click_order_re_order_enable_plug">
    				<input type="radio" id='radio1'class="mwb_ocor_enabling_basket"  name="one_click_order_re_order_enable_plug[basket]" <?php if(isset($general_settings['basket'])&&!empty($general_settings['basket'])){ checked('on',$general_settings['basket']);} ?>>
    				<?php _e( 'Enable.', 'one-click-order-re-order' ); ?>
    				<input  type="radio" id="radio2" class="mwb_ocor_enabling_basket" value="off" name="one_click_order_re_order_enable_plug[basket]" <?php if(isset($general_settings['basket'])&&!empty($general_settings['basket'])){checked('off',$general_settings['basket']); }?>>
    				<?php _e( 'Disable.', 'one-click-order-re-order' ); ?>	
    			</label>
    		</div>
    		<?php
    	}
    	if(isset($param) && $param == 'button_text'){
    		?>
    		<div class="one-click-order-re-order-option-sec">

    			<label for="one_click_order_re_order_enable_plug">
    				<input type="text" name="one_click_order_re_order_enable_plug[button_text]" value="<?php if(is_array($general_settings)&&!empty($general_settings['button_text'])){echo $general_settings['button_text'];}else{echo'Add to Basket';}?>" >
    			</label>
    		</div>
    		<?php
    	}

    	if(isset($param) && $param == 'button_remove_text'){
    		?>
    		<div class="one-click-order-re-order-option-sec">

    			<label for="one_click_order_re_order_enable_plug">
    				<input type="text" name="one_click_order_re_order_enable_plug[button_remove_text]" value="<?php if(is_array($general_settings)&&!empty($general_settings['button_remove_text'])){echo $general_settings['button_remove_text'];}else{echo'Remove From Basket';}?>" >
    			</label>
    		</div>
    		<?php
    	}
    	if(isset($param)&&$param =='mwb_upload_icon')
    	{
    		?>
    		<div id="mwb_ocor_attachment_section" class="mwb_ocor_columns mwb_ocor_columns_content">
						<div class="mwb_ocor_attachment_wrapper">
							<?php 
							$image = MAKEWEBBETTER_CNG_ORDER_URL . 'admin/images/shopping-bag.png';
							
							?>
							<img src="<?php if(is_array($general_settings)&&!empty($general_settings['image'])){echo $general_settings['image'];}else{echo $image;}?>" alt="<?php _e( 'Basket icon', 'one-click-order-re-order' );?>">
							<input type="hidden" name="one_click_order_re_order_enable_plug[image]" id="mwb_ocor_saved_icon_url" value="<?php if(is_array($general_settings)&&!empty($general_settings['image'])){echo $general_settings['image'];}else{ echo esc_url( $image );}?>">
						</div>
						<div class="mwb_ocor_attachment_action">
							<input type="button" id="mwb_ocor_icon_for_basket" class="button" value="<?php _e( 'Upload', 'one-click-order-re-order' ); ?>">
						</div>
					</div>
					<?php
    	}
    	if(isset($param)&&$param=='mwb_selected_role'){
    		?>  
					<div id="mwb_ocor_basket_section" class="<?php echo $basketSectionClass;?>">
						<!-- <div class="mwb_ocor_row"> -->
							<div class="mwb_ocor_columns mwb_ocor_columns_content">
								<?php 
								$roles = get_editable_roles();
								if ( ! empty( $roles ) ) {
									echo '<select name="one_click_order_re_order_enable_plug[selectedUsers][]" id="mwb_ocor_enable_basket_for_users" class="mwb_ocor_enable_basket_for" multiple="multiple" placeholder="'. __( 'Select user roles to show them basket feature.', 'one-click-order-re-order' ) .'">';
										
										$mwb_selectedUsers=$general_settings['selectedUsers'];
										foreach ( $roles as $role => $caps ) {
											if ( $role =='administrator' ) {
												continue;
											}

											$selected = '';
											if ( !empty( $mwb_selectedUsers ) ) {
												if ( in_array( $role, $mwb_selectedUsers ) ) {
													echo $selected = 'selected';
												}
											}

											echo '<option value="'. esc_attr( $role ) .'" '. esc_attr( $selected ) .'>'. $caps[ 'name' ] .'</option>';
										}

									echo  '</select>';
								}
								?>
							</div>
							</div>
					

    		<?php
    	}
    	if(isset($param)&&$param=='mwb_selected_page'){
    		?>
    		<?php  ?>
    		<div id="selected_page"class="mwb_ocor_columns mwb_ocor_columns_content">
								<?php 
								if ( !empty( $this->basket_pages ) and is_array( $this->basket_pages ) ) {
									echo '<select id="mwb_ocor_enable_basket_for_pages" class="mwb_ocor_enable_basket_for" name="one_click_order_re_order_enable_plug[selectedPage][]" multiple="multiple" placeholder="'. __( 'Select pages to show the basket.', 'one-click-order-re-order' ) .'">';
									$mwb_selectedPage=$general_settings['selectedPage'];
                                    

										foreach ( $this->basket_pages as $page => $pageName ) {
											if ( empty( $page ) or empty( $pageName ) ) {
												continue;
											}
											$select ='';
											if ( !empty($mwb_selectedPage ) ) {
												if ( in_array( $page, $mwb_selectedPage ) ) {
													$select = 'selected';
												}
											}
											echo '<option value="'. esc_attr( $page ) .'" '. esc_attr( $select ) .'>'. esc_html( $pageName ) .'</option>';
										}

									echo '</select>';
								}?>
							</div>
            
    		<?php 
    		
    	}
    }
    /**
	 * Get the color and set it for the Button's color(Referral Program Button)
	 *
	 * @since    1.0.0
	 *
	 */
    public static function get_pages() {
    	$mwb_page_title=array();
    	$mwb_pages = get_pages(); 
    	foreach ($mwb_pages as $pagedata) {
    		$mwb_page_title[$pagedata->ID] = $pagedata->post_title;
    	}
    	$mwb_page_title['details'] = 'Product Detail';
    	return $mwb_page_title;
    }


	/**
	 * Validate license.
	 *
	 * @since    1.0.0
	 */
	public function validate_license_handle() {

		// First check the nonce, if it fails the function will break
		check_ajax_referer( 'one-click-order-re-order-license-nonce-action', 'one-click-order-re-order-license-nonce' );

		$mwb_license_key = !empty( $_POST['one_click_order_re_order_purchase_code'] ) ? sanitize_text_field( $_POST['one_click_order_re_order_purchase_code'] ) : '';

    	// API query parameters
		$api_params = array(
			'slm_action' => 'slm_activate',
			'secret_key' => ONE_CLICK_ORDER_RE_ORDER_SPECIAL_SECRET_KEY,
			'license_key' => $mwb_license_key,
			'registered_domain' => $_SERVER['SERVER_NAME'],
			'item_reference' => urlencode( ONE_CLICK_ORDER_RE_ORDER_ITEM_REFERENCE ),
			'product_reference' => 'MWBPK-15814'
			);

		// Send query to the license manager server
		$query = esc_url_raw( add_query_arg( $api_params, ONE_CLICK_ORDER_RE_ORDER_SERVER_URL ) );

		$response = wp_remote_get( $query, array( 'timeout' => 20, 'sslverify' => false ) );

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if( isset( $license_data->result ) && 'success' === $license_data->result ) {

			update_option( 'one_click_order_re_order_lcns_key', $mwb_license_key );
			update_option( 'one_click_order_re_order_lcns_status', 'true' );

			echo json_encode( array( 'status' => true, 'msg' =>__( 'Successfully Verified...', 'one-click-order-re-order' ) ) );
		}
		else{

			$error_message = !empty( $license_data->message ) ? $license_data->message : __( 'License Verification Failed.', 'one-click-order-re-order' );

			echo json_encode( array( 'status' => false, 'msg' => $error_message ) );
		}

		wp_die();
	}

    /**
     * Validate License daily.
     *
     * @since 1.0.0
     */
    public function validate_license_daily() {

    	$mwb_license_key = get_option( 'one_click_order_re_order_lcns_key', '' );

		// API query parameters
    	$api_params = array(
    		'slm_action' => 'slm_check',
    		'secret_key' => ONE_CLICK_ORDER_RE_ORDER_SPECIAL_SECRET_KEY,
    		'license_key' => $mwb_license_key,
    		'registered_domain' => $_SERVER['SERVER_NAME'],
    		'item_reference' => urlencode( ONE_CLICK_ORDER_RE_ORDER_ITEM_REFERENCE ),
    		'product_reference' => 'MWBPK-15814'
    		);

    	$query = esc_url_raw( add_query_arg( $api_params, ONE_CLICK_ORDER_RE_ORDER_SERVER_URL ) );

    	$mwb_response = wp_remote_get( $query, array( 'timeout' => 20, 'sslverify' => false ) );

    	$license_data = json_decode( wp_remote_retrieve_body( $mwb_response ) );

    	if( isset( $license_data->result ) && 'success' === $license_data->result && isset( $license_data->status ) && 'active' === $license_data->status ) {

    		update_option( 'one_click_order_re_order_lcns_key', $mwb_license_key );
    		update_option( 'one_click_order_re_order_lcns_status', 'true' );
    	}

    	else {

    		delete_option( 'one_click_order_re_order_lcns_key' );
    		update_option( 'one_click_order_re_order_lcns_status', 'false' );
    	}

    }

}
