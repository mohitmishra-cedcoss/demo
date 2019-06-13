<?php
/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( !class_exists( 'MWB_WPR_Admin_Manager' ) )
{

	/**
	 * This class is used to manage admin settings
	 *
	 * @name    MWB_WPR_Admin_Manager
	 * @category Class
	 * @author   makewebbetter <webmaster@makewebbetter.com>
	 */
	class MWB_WPR_Admin_Manager
	{

		/**
		 * This is construct of class where all action and filter is defined
		 * 
		 * @name __construct
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link https://www.makewebbetter.com/
		 */
		public function __construct( ) 
		{
			add_action( 'admin_menu', array ( $this, 'mwb_wpr_admin_menu' ), 10, 2 );
			add_action( 'admin_enqueue_scripts', array($this, "mwb_wpr_admin_enqueue_scripts"));
			add_action('transition_comment_status', array($this, 'mwb_wpr_give_points_on_comment'),10,3);

			add_filter('woocommerce_product_data_tabs', array($this,'mwb_wpr_add_points_tab'), 15,1);

			add_action('woocommerce_product_data_panels', array($this, 'mwb_wpr_points_input'));
			add_action( 'woocommerce_process_product_meta', array($this, 'woo_add_custom_points_fields_save') );
			add_action( 'wp_ajax_mwb_wpr_points_update', array($this, 'mwb_wpr_points_update'));
			add_action( 'wp_ajax_nopriv_mwb_wpr_points_update', array($this, 'mwb_wpr_points_update'));
			add_action( 'wp_ajax_mwb_wpr_select_category', array($this, 'mwb_wpr_select_category'));
			add_action( 'wp_ajax_nopriv_mwb_wpr_select_category', array($this, 'mwb_wpr_select_category'));
			add_action( 'wp_ajax_mwb_wpr_per_pro_category', array($this, 'mwb_wpr_per_pro_category'));
			add_action( 'wp_ajax_nopriv_mwb_wpr_per_pro_category', array($this, 'mwb_wpr_per_pro_category'));
			add_action( 'wp_ajax_mwb_wpr_per_pro_pnt_category', array($this, 'mwb_wpr_per_pro_pnt_category'));
			add_action( 'wp_ajax_nopriv_mwb_wpr_per_pro_pnt_category', array($this, 'mwb_wpr_per_pro_pnt_category'));
			add_action('woocommerce_checkout_update_order_meta',array($this,'mwb_wpr_woocommerce_checkout_update_order_meta'),10,2);
			add_action('woocommerce_order_refunded',array($this,'mwb_wpr_woocommerce_order_refunded'),10,2);			
			add_action('mwb_wpr_membership_cron_schedule', array($this, 'mwb_wpr_do_this_hourly'));
			add_action( 'widgets_init', array($this, 'mwb_wpr_custom_widgets'));
			add_action( 'woocommerce_admin_order_item_headers',array($this,'mwb_wpr_woocommerce_admin_order_item_headers') );
			add_action( 'woocommerce_admin_order_item_values',array($this,'mwb_wpr_woocommerce_admin_order_item_values'),10,3 );
			add_action( 'woocommerce_variation_options',array($this, 'mwb_wpr_woocommerce_variation_options_pricing'),10,3 );
			add_action('woocommerce_save_product_variation',array($this,'mwb_wpr_woocommerce_save_product_variation'),10,2);
			add_action( 'mwb_wpr_points_expiration_cron_schedule',array($this,'mwb_wpr_check_daily_about_points_expiration') );
			add_action( 'wp_ajax_mwb_wpr_register_license', array ( $this,'mwb_wpr_register_license'));
			add_action( 'mwb_wpr_update_json',array( $this, 'mwb_wpr_update_json_process' ) );
		}
		/**
		 * This function update points
		 * 
		 * @name mwb_wpr_points_update
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link https://www.makewebbetter.com/
		 */
		public function mwb_wpr_points_update()
		{
			check_ajax_referer( 'mwb-wpr-verify-nonce', 'mwb_nonce' );
			if(isset($_POST['points']) && is_numeric($_POST['points']))
			{
				$user_id = sanitize_post($_POST['user_id']);
				$get_points = (int)get_user_meta($user_id, 'mwb_wpr_points', true);
				$admin_points = get_user_meta($user_id, 'points_details',true);
				$today_date = date_i18n("Y-m-d h:i:sa");
				$points=sanitize_post($_POST['points']);
				$sign = sanitize_post($_POST['sign']);
				$reason = sanitize_post($_POST['reason']);				
				if($sign === '+'){
					$total_points = $get_points + $points;
				}else if($sign === '-'){
					if($points <= $get_points)
						$total_points = $get_points - $points;
					else
						$points = $get_points;
						$total_points = $get_points - $points;
				}
				if(isset($points) && !empty($points)){

					if(isset($admin_points['admin_points']) && !empty($admin_points['admin_points'])){
						$admin_array = array();
						$admin_array = array(
										'admin_points'=>$points,
										'date'=>$today_date,
										'sign'=> $sign,
										'reason'=>$reason);
						$admin_points['admin_points'][] = $admin_array;
					}
					else{
						if(!is_array($admin_points)){
							$admin_points = array();
						}
						$admin_array = array(
										'admin_points'=>$points,
										'date'=>$today_date,
										'sign'=> $sign,
										'reason'=>$reason);
						$admin_points['admin_points'][] = $admin_array;
					}
				}
				update_user_meta( $user_id,'points_details', $admin_points );
				update_user_meta( $user_id , 'mwb_wpr_points' , $total_points );
				$user=get_user_by('ID',$user_id);
				$user_email=$user->user_email;
				$user_name = $user->user_firstname;	
				$mwb_wpr_notificatin_array=get_option('mwb_wpr_notificatin_array',true);
				if(is_array($mwb_wpr_notificatin_array) && !empty($mwb_wpr_notificatin_array))
				{
					$mwb_wpr_notificatin_enable=isset($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) ? intval($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) : 0;
					$mwb_wpr_email_subject=isset($mwb_wpr_notificatin_array['mwb_wpr_email_subject'])? $mwb_wpr_notificatin_array['mwb_wpr_email_subject'] :'';
					$mwb_wpr_email_discription=isset($mwb_wpr_notificatin_array['mwb_wpr_email_discription_custom_id']) ? $mwb_wpr_notificatin_array['mwb_wpr_email_discription_custom_id'] :'';
					$mwb_wpr_email_discription=str_replace("[Total Points]",$total_points,$mwb_wpr_email_discription);
					$mwb_wpr_email_discription = str_replace("[USERNAME]",$user_name,$mwb_wpr_email_discription);
					if($mwb_wpr_notificatin_enable)
					{
						$headers = array('Content-Type: text/html; charset=UTF-8');
						wc_mail($user_email,$mwb_wpr_email_subject,$mwb_wpr_email_discription,$headers);
					}
				}
				die;
			}
		}

		/**
		 * This function update product custom points
		 * 
		 * @name woo_add_custom_points_fields_save
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link https://www.makewebbetter.com/
		 */
		public function woo_add_custom_points_fields_save($post_id){
			
			if(isset($_POST['mwb_product_hidden_field'])){
				
				$general_settings_array = array();
				$enable_product_points = isset($_POST['mwb_product_points_enable'])?sanitize_post($_POST['mwb_product_points_enable']): 'no';
				$enable_product_purchase_points = isset($_POST['mwb_product_purchase_points_only'])?sanitize_post($_POST['mwb_product_purchase_points_only']): 'no';				
				$mwb_pro_pur_by_point_disable = isset($_POST['mwb_product_purchase_through_point_disable'])?sanitize_post($_POST['mwb_product_purchase_through_point_disable']): 'no';
				$mwb_product_value = (isset($_POST['mwb_points_product_value']) && $_POST['mwb_points_product_value'] !=null )? sanitize_post($_POST['mwb_points_product_value']): 1;
				$mwb_product_purchase_value = (isset($_POST['mwb_points_product_purchase_value']) && $_POST['mwb_points_product_purchase_value'] !=null )? sanitize_post($_POST['mwb_points_product_purchase_value']): 1;
				update_post_meta($post_id, 'mwb_product_points_enable', $enable_product_points);
				update_post_meta($post_id, 'mwb_product_purchase_points_only', $enable_product_purchase_points);
				update_post_meta($post_id, 'mwb_points_product_value', $mwb_product_value);	
				update_post_meta($post_id, 'mwb_points_product_purchase_value', $mwb_product_purchase_value);	
				update_post_meta($post_id, 'mwb_product_purchase_through_point_disable', $mwb_pro_pur_by_point_disable);	
			}
		}
		
		/**
		 * This construct set products point.
		 * 
		 * @name mwb_wpr_points_input
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link https://www.makewebbetter.com/
		 */
		public function mwb_wpr_points_input(){
			global $post;
			$product_is_variable = false;	
			$product = wc_get_product($post->ID);
			if( $product->is_type( 'variable' ) && $product->has_child() ){
				$product_is_variable = true;	
			}
			?>
				<div id="points_data" class="panel woocommerce_options_panel">
					<div class="options_group">
					<?php 
						woocommerce_wp_checkbox( array( 'id' => 'mwb_product_points_enable', 'wrapper_class' => 'show_if_points', 'label' => __( 'Enable', MWB_WPR_Domain ), 'description' => __( 'Enable Points Per Product', MWB_WPR_Domain ) ) );
						if(!$product_is_variable){
								woocommerce_wp_text_input( array(
								'id'                => 'mwb_points_product_value',
								'label'             => __( 'Enter the Points', MWB_WPR_Domain ),
								'desc_tip'          => true,
								'custom_attributes'   => array('min'=>'0'),
								'description'       => __( 'Please enter the number of points for this product ', MWB_WPR_Domain ),
								'type'              => 'number',
							) );
						}
						woocommerce_wp_checkbox( array( 'id' => 'mwb_product_purchase_through_point_disable', 'wrapper_class' => 'show_if_points', 'label' => __( 'Do not allow to purchase through points', MWB_WPR_Domain ), 'description' => __( 'Do not allow to purchase purchase this product thorugh points', MWB_WPR_Domain ) ) );

						//MWB Custom Work

						woocommerce_wp_checkbox( array( 'id' => 'mwb_product_purchase_points_only', 'wrapper_class' => 'show_if_points_only', 'label' => __( 'Enable', MWB_WPR_Domain ), 'description' => __( 'Enable Purchase through points only', MWB_WPR_Domain ) ) );
						if(!$product_is_variable){
								woocommerce_wp_text_input( array(
								'id'                => 'mwb_points_product_purchase_value',
								'label'             => __( 'Enter the Points For Purchase', MWB_WPR_Domain ),
								'desc_tip'          => true,
								'custom_attributes'   => array('min'=>'0'),
								'description'       => __( 'Please enter the number of points for purchase this product ', MWB_WPR_Domain ),
								'type'              => 'number',
							) );
						}

						// End of Custom Work

					?>
						<input type="hidden" name="mwb_product_hidden_field"></input>
					</div>
				</div>
			<?php 
		
		}

		/**
		 * This construct add tab in products menu.
		 * 
		 * @name mwb_wpr_add_points_tab
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link https://www.makewebbetter.com/
		 */
		public function mwb_wpr_add_points_tab($all_tabs){
			$all_tabs['points'] =  array(
				'label'  => __( 'Points and Rewards', MWB_WPR_Domain ),
				'target' => 'points_data',
			);
			return $all_tabs;
		}

		/**
		 * This function update points on comment.
		 * 
		 * @name mwb_wpr_give_points_on_comment
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link https://www.makewebbetter.com/
		 */
		public function mwb_wpr_give_points_on_comment($new_status, $old_status, $comment )
		{
			global $current_user;
			$user_email=$comment->comment_author_email;
			
			if($new_status == 'approved'){
				$general_settings = get_option('mwb_wpr_settings_gallery',true);
			
				$enable_mwb_comment = isset($general_settings['enable_mwb_comment']) ? intval($general_settings['enable_mwb_comment']) : 0;
				
				if($enable_mwb_comment )
				{	

					$today_date = date_i18n("Y-m-d h:i:sa");
					$mwb_comment_value = isset($general_settings['mwb_comment_value']) ? intval($general_settings['mwb_comment_value']) : 1;
					
					$get_points = get_user_meta($comment->user_id, 'mwb_wpr_points', true);
					$get_detail_point = get_user_meta($comment->user_id, 'points_details', true);
					
					if(isset($get_detail_point['comment']) && !empty($get_detail_point['comment']) ){
						$comment_arr = array();
					
						$comment_arr= array(
								'comment'=>$mwb_comment_value,
								'date'=>$today_date);
						$get_detail_point['comment'][] = $comment_arr;

					}
					else{
						if(!is_array($get_detail_point)){
							$get_detail_point = array();
						}
						$comment_arr= array(
								'comment'=>$mwb_comment_value,
								'date'=>$today_date);
					
						$get_detail_point['comment'][]= $comment_arr;
					}
					update_user_meta( $comment->user_id , 'mwb_wpr_points' , $mwb_comment_value+$get_points );
			
					update_user_meta( $comment->user_id , 'points_details' , $get_detail_point);
					
					$mwb_wpr_notificatin_array=get_option('mwb_wpr_notificatin_array',true);
					if(is_array($mwb_wpr_notificatin_array) && !empty($mwb_wpr_notificatin_array))
					{
						$total_points=$mwb_comment_value+$get_points;
						$mwb_wpr_notificatin_enable=isset($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) ? intval($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) : 0;
						$mwb_wpr_email_subject=isset($mwb_wpr_notificatin_array['mwb_wpr_comment_email_subject'])? $mwb_wpr_notificatin_array['mwb_wpr_comment_email_subject'] :'';
						$mwb_wpr_email_discription=isset($mwb_wpr_notificatin_array['mwb_wpr_comment_email_discription_custom_id']) ? $mwb_wpr_notificatin_array['mwb_wpr_comment_email_discription_custom_id'] :'';
						$mwb_wpr_email_discription=str_replace("[Points]",$mwb_comment_value,$mwb_wpr_email_discription);
						$mwb_wpr_email_discription=str_replace("[Total Points]",$total_points,$mwb_wpr_email_discription);
						$user = get_user_by('email',$user_email);
						$user_name = $user->user_firstname;
						$mwb_wpr_email_discription = str_replace("[USERNAME]",$user_name,$mwb_wpr_email_discription);
						if($mwb_wpr_notificatin_enable)
						{
							$headers = array('Content-Type: text/html; charset=UTF-8');
							wc_mail($user_email,$mwb_wpr_email_subject,$mwb_wpr_email_discription,$headers);
						}
					}
				}				
			}
			
		}

		/**
		 * This function enque admin script.
		 * 
		 * @name mwb_wpr_admin_enqueue_scripts.
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link https://www.makewebbetter.com/
		 */
		public function mwb_wpr_admin_enqueue_scripts()
		{
			$screen = get_current_screen();
			
			if(isset($screen->id))
			{
				$pagescreen = $screen->id;

				if(isset($_GET['page']) && $_GET['page'] == 'mwb-wpr-setting' || $pagescreen == 'product')
				{	
					wp_register_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );
					wp_enqueue_style( 'woocommerce_admin_menu_styles' );
					wp_enqueue_style( 'woocommerce_admin_styles' );
					wp_register_script( 'woocommerce_admin', WC()->plugin_url() . '/assets/js/admin/woocommerce_admin.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip' ), WC_VERSION );
					wp_register_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.js', array( 'jquery' ), WC_VERSION, true );
					$locale  = localeconv();
					$decimal = isset( $locale['decimal_point'] ) ? $locale['decimal_point'] : '.';
					$params = array(
						/* translators: %s: decimal */
						'i18n_decimal_error'                => sprintf( __( 'Please enter in decimal (%s) format without thousand separators.', MWB_WPR_Domain ), $decimal ),
						/* translators: %s: price decimal separator */
						'i18n_mon_decimal_error'            => sprintf( __( 'Please enter in monetary decimal (%s) format without thousand separators and currency symbols.', MWB_WPR_Domain ), wc_get_price_decimal_separator() ),
						'i18n_country_iso_error'            => __( 'Please enter in country code with two capital letters.', MWB_WPR_Domain ),
						'i18_sale_less_than_regular_error'  => __( 'Please enter in a value less than the regular price.', MWB_WPR_Domain ),
						'decimal_point'                     => $decimal,
						'mon_decimal_point'                 => wc_get_price_decimal_separator(),
						'strings' => array(
							'import_products' => __( 'Import', MWB_WPR_Domain ),
							'export_products' => __( 'Export', MWB_WPR_Domain ),
						),
						'urls' => array(
							'import_products' => esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_importer' ) ),
							'export_products' => esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_exporter' ) ),
						),
					);
					wp_localize_script( 'woocommerce_admin', 'woocommerce_admin', $params );

					wp_enqueue_script( 'woocommerce_admin' );
					wp_enqueue_style("mwb_wpr_admin_style", MWB_WPR_URL."assets/css/admin/woocommerce-ultimate-points-admin.css");				
					$args_cat = array('taxonomy'=>'product_cat');
					$categories = get_terms($args_cat);
					$option_arr = array();
					if(isset($categories) && !empty($categories))
					{
						foreach($categories as $category)
						{
							$catid = $category->term_id;
							$catname = $category->name;

							$option_categ[] = array(
								'id'=>$catid,
								'cat_name'=>$catname);
						}
					}
					$url = admin_url ( 'admin.php?page=mwb-wpr-setting' );
					$mwb_wpr = array(
							'ajaxurl' => admin_url('admin-ajax.php'),
							'validpoint'=> __('Please enter valid points',MWB_WPR_Domain),
							'Labelname'=>__('Enter the Name of the Level',MWB_WPR_Domain),
							'Labeltext'=>__('Enter Level',MWB_WPR_Domain),
							'Points'=>__('Enter Points',MWB_WPR_Domain),
							'Categ_text'=>__('Select Product Category',MWB_WPR_Domain),
							'Remove_text'=>__('Remove',MWB_WPR_Domain),
							'Categ_option'=>$option_categ,
							'Prod_text'=>__('Select Product',MWB_WPR_Domain),
							'Discounttext'=>__('Enter Discount (%)',MWB_WPR_Domain),
							'error_notice'=>__('Fields cannot be empty',MWB_WPR_Domain),
							'LevelName_notice'=>__('Please Enter the Name of the Level',MWB_WPR_Domain),
							'LevelValue_notice'=>__('Please Enter valid Points',MWB_WPR_Domain),
							'CategValue_notice'=>__('Please select a category',MWB_WPR_Domain),
							'ProdValue_notice'=>__('Please select a product',MWB_WPR_Domain),
							'Discount_notice'=>__('Please enter valid discount',MWB_WPR_Domain),
							'success_assign'=>__('Points are assigned successfully!',MWB_WPR_Domain),
							'error_assign'=>__('Enter Some Valid Points!',MWB_WPR_Domain),
							'success_remove'=>__('Points are removed successfully!',MWB_WPR_Domain),
							'Days'=>__('Days',MWB_WPR_Domain),
							'Weeks'=>__('Weeks',MWB_WPR_Domain),
							'Months'=>__('Months',MWB_WPR_Domain),
							'Years'=>__('Years',MWB_WPR_Domain),
							'Exp_period'=>__('Expiration Period',MWB_WPR_Domain),
							'mwb_wpr_url' => $url,
							'reason' => __('Please enter Remark',MWB_WPR_Domain),
							'mwb_wpr_nonce' =>  wp_create_nonce( "mwb-wpr-verify-nonce" )
					);
					wp_register_script("mwb_wpr_admin_script", MWB_WPR_JS_LOAD_ADMIN,array("select2"));
					wp_localize_script('mwb_wpr_admin_script', 'mwb_wpr', $mwb_wpr );
					wp_enqueue_script('mwb_wpr_admin_script' );
					wp_enqueue_style("select2");
				}
			}
			
		}

		/**
		 * This function make woocommerce admin menu.
		 * 
		 * @name mwb_wpr_admin_menu.
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link https://www.makewebbetter.com/
		 */
		public function mwb_wpr_admin_menu() {

			//Remove the www from the Host Name
			$host_server = $_SERVER['HTTP_HOST'];
			if( strpos($host_server,'www.') == 0 ) {

				$host_server = str_replace('www.','',$host_server);
			}
			
			$mwb_wpr_license_hash = get_option('mwb_wpr_license_hash');
			$mwb_wpr_license_key = get_option('mwb_wpr_license_key');
			$mwb_wpr_license_plugin = get_option('mwb_wpr_plugin_name');
			$mwb_wpr_hash = md5($host_server.$mwb_wpr_license_plugin.$mwb_wpr_license_key);
			$mwb_wpr_activated_time = get_option('mwb_wpr_activation_date_time',false);
			if(!$mwb_wpr_activated_time){
	            $mwb_wpr_currenttime = current_time('timestamp');
	            update_option('mwb_wpr_activation_date_time',$mwb_wpr_currenttime);
	            $mwb_wpr_activated_time = $mwb_wpr_currenttime;
			}
			$mwb_wpr_after_month = strtotime('+30 days', $mwb_wpr_activated_time);
			$mwb_wpr_currenttime = current_time('timestamp');

			if( $mwb_wpr_license_hash == $mwb_wpr_hash ){
				add_submenu_page( "woocommerce", __("WooCommerce Points And Rewards Manager",MWB_WPR_Domain), __("Points And Rewards Manager",MWB_WPR_Domain), "manage_woocommerce", "mwb-wpr-setting", array($this, "mwb_wpr_admin_setting"));
				
			}
			elseif( ($mwb_wpr_license_hash == '' || $mwb_wpr_license_key == '') && ($mwb_wpr_after_month > $mwb_wpr_currenttime )){
				add_submenu_page( "woocommerce", __("WooCommerce Points And Rewards Manager",MWB_WPR_Domain), __("Points And Rewards Manager",MWB_WPR_Domain), "manage_woocommerce", "mwb-wpr-setting", array($this, "mwb_wpr_admin_setting"));
			}
			else{
				// delete_option('mwb_wpr_settings_gallery');
				$general_settings = get_option('mwb_wpr_settings_gallery',true);
				if(is_array($general_settings) && array_key_exists('enable_mwb_wpr', $general_settings)){
					$general_settings['enable_mwb_wpr'] = 0;
				}
				update_option("mwb_wpr_settings_gallery",$general_settings);
				add_submenu_page( "woocommerce", __("WooCommerce Points And Rewards Manager",MWB_WPR_Domain), __("Activate Points And Rewards Manager",MWB_WPR_Domain), "manage_woocommerce", "mwb-wpr-setting", array($this, "mwb_wpr_admin_setting_activation"));
			}
		}

		/**
		 * This function make woocommerce admin menu settings.
		 * 
		 * @name mwb_wpr_admin_setting.
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link https://www.makewebbetter.com/
		 */
		public function mwb_wpr_admin_setting(){
			include_once MWB_WPR_DIRPATH.'/includes/admin/settings/woocommerce-ultimate-points-and-rewards-settings.php';
		}


		/**
		 * This function make woocommerce admin menu settings.
		 * 
		 * @name mwb_wpr_admin_setting_activation.
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link https://www.makewebbetter.com/
		 */
		public function mwb_wpr_admin_setting_activation(){
			include_once MWB_WPR_DIRPATH.'/includes/admin/settings/templates/mwb_license_verify.php';
		}
		/**
		 * This function append the option field after selecting Product category through ajax
		 * 
		 * @name mwb_wpr_select_category.
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link https://www.makewebbetter.com/
		 */
		public function mwb_wpr_select_category(){

			check_ajax_referer( 'mwb-wpr-verify-nonce', 'mwb_nonce' );
			$mwb_wpr_categ_list = $_POST['mwb_wpr_categ_list'];
			$response['result'] = __('Fail due to an error',MWB_WPR_Domain);
			if(isset($_POST['mwb_wpr_categ_list']))
	        {
	            $products = array();
	            $selected_cat = $_POST['mwb_wpr_categ_list'];
	            $tax_query['taxonomy'] = 'product_cat';
	            $tax_query['field'] = 'id';
	            $tax_query['terms'] = $selected_cat;
	            $tax_queries[] = $tax_query;
	            $args = array( 'post_type' => 'product', 'posts_per_page' => -1, 'tax_query' => $tax_queries, 'orderby' => 'rand' );
	            $loop = new WP_Query( $args );
	            while ( $loop->have_posts() ) : $loop->the_post(); global $product;
	            
	            $product_id = $loop->post->ID;
	            $product_title = $loop->post->post_title;
	            $products[$product_id] = $product_title;
	            endwhile;
	            
	            $response['data'] = $products;
	            $response['result'] = 'success';
	            echo json_encode($response);
	            wp_die();
	            
	        }
		}
		/**
		 * This function append the option field after selecting Product category through ajax in Assign Product Points Tab
		 * 
		 * @name mwb_wpr_select_category.
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link https://www.makewebbetter.com/
		 */
		public function mwb_wpr_per_pro_category(){
			check_ajax_referer( 'mwb-wpr-verify-nonce', 'mwb_nonce' );
			$mwb_wpr_categ_id = sanitize_post($_POST['mwb_wpr_categ_id']);
			$mwb_wpr_categ_point = sanitize_post($_POST['mwb_wpr_categ_point']);
			$response['result'] = __('Fail due to an error',MWB_WPR_Domain);
			if(isset($mwb_wpr_categ_id) && !empty($mwb_wpr_categ_id))
	        {
	            $products = array();
	            $selected_cat = $mwb_wpr_categ_id;
	            $tax_query['taxonomy'] = 'product_cat';
	            $tax_query['field'] = 'id';
	            $tax_query['terms'] = $selected_cat;
	            $tax_queries[] = $tax_query;
	            $args = array( 'post_type' => 'product', 'posts_per_page' => -1, 'tax_query' => $tax_queries, 'orderby' => 'rand' );
	            $loop = new WP_Query( $args );
	            while ( $loop->have_posts() ) : $loop->the_post(); global $product;
	            
	            $product_id = $loop->post->ID;
	            $product_title = $loop->post->post_title;
	            if(isset($mwb_wpr_categ_point) && !empty($mwb_wpr_categ_point))
				{	
					$product = wc_get_product($product_id);
					if( $product->is_type( 'variable' ) && $product->has_child() ){
						$parent_id = $product->get_id();
						$parent_product = wc_get_product($parent_id);	
						foreach ($parent_product->get_children() as $child_id) {
							update_post_meta($parent_id, 'mwb_product_points_enable', 'yes');
							update_post_meta($child_id, 'mwb_wpr_variable_points', $mwb_wpr_categ_point);
						}

					}
					else{
						update_post_meta($product_id, 'mwb_product_points_enable', 'yes');
						update_post_meta($product_id, 'mwb_points_product_value', $mwb_wpr_categ_point);
						update_option("mwb_wpr_points_to_per_categ_".$mwb_wpr_categ_id, $mwb_wpr_categ_point);
					}
				}
				else
				{
					update_post_meta($product_id, 'mwb_product_points_enable', 'no');
					update_post_meta($product_id, 'mwb_points_product_value', '');
					update_option("mwb_wpr_points_to_per_categ_".$mwb_wpr_categ_id, $mwb_wpr_categ_point);
				}
	            endwhile;
	            $response['category_id'] = $mwb_wpr_categ_id;
	            $response['categ_point'] = $mwb_wpr_categ_point;
	            $response['result'] = 'success';
	            echo json_encode($response);
	            wp_die();
	        }
		}

		/**
		 * This function append the option field after selecting Product category through ajax in Product Purchase Points Tab
		 * 
		 * @name mwb_wpr_per_pro_pnt_category.
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link https://www.makewebbetter.com/
		 */
		public function mwb_wpr_per_pro_pnt_category(){
			check_ajax_referer( 'mwb-wpr-verify-nonce', 'mwb_nonce' );
			$mwb_wpr_categ_id = sanitize_post($_POST['mwb_wpr_categ_id']);
			$mwb_wpr_categ_point = sanitize_post($_POST['mwb_wpr_categ_point']);
			$response['result'] = __('Fail due to an error',MWB_WPR_Domain);
			if(isset($mwb_wpr_categ_id) && !empty($mwb_wpr_categ_id))
	        {
	            $products = array();
	            $selected_cat = $mwb_wpr_categ_id;
	            $tax_query['taxonomy'] = 'product_cat';
	            $tax_query['field'] = 'id';
	            $tax_query['terms'] = $selected_cat;
	            $tax_queries[] = $tax_query;
	            $args = array( 'post_type' => 'product', 'posts_per_page' => -1, 'tax_query' => $tax_queries, 'orderby' => 'rand' );
	            $loop = new WP_Query( $args );
	            while ( $loop->have_posts() ) : $loop->the_post(); global $product;
	            
	            $product_id = $loop->post->ID;
	            $product_title = $loop->post->post_title;
	            if(isset($mwb_wpr_categ_point) && !empty($mwb_wpr_categ_point))
				{	
					$product = wc_get_product($product_id);
					if( $product->is_type( 'variable' ) && $product->has_child() ){
						$parent_id = $product->get_id();
						$parent_product = wc_get_product($parent_id);	
						foreach ($parent_product->get_children() as $child_id) {
							update_post_meta($parent_id, 'mwb_product_purchase_points_only','yes');
							update_post_meta($child_id, 'mwb_wpr_variable_points_purchase', $mwb_wpr_categ_point);							
						}

					}
					else{
						update_post_meta($product_id, 'mwb_product_purchase_points_only','yes');
						update_post_meta($product_id, 'mwb_points_product_purchase_value', $mwb_wpr_categ_point);
						update_option("mwb_wpr_purchase_points_cat".$mwb_wpr_categ_id, $mwb_wpr_categ_point);											}
					}
				else
				{
					update_post_meta($product_id, 'mwb_product_purchase_points_only', 'no');
					update_post_meta($product_id, 'mwb_points_product_purchase_value', '');
					update_option("mwb_wpr_purchase_points_cat".$mwb_wpr_categ_id, $mwb_wpr_categ_point);
				}
	            endwhile;
	            $response['category_id'] = $mwb_wpr_categ_id;
	            $response['categ_point'] = $mwb_wpr_categ_point;
	            $response['result'] = 'success';
	            echo json_encode($response);
	            wp_die();
	        }
		}
		/**
		 * This function will update the user points as they purchased products through points
		 * 
		 * @name mwb_wpr_woocommerce_checkout_update_order_meta.
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link https://www.makewebbetter.com/
		 */
		public function mwb_wpr_woocommerce_checkout_update_order_meta($order_id,$data)
		{	
			$user_id = get_current_user_id();
			$user=get_user_by('ID',$user_id);
			$user_email=$user->user_email;
			$woo_ver = WC()->version;
			$deduct_point = '';
			$points_deduct = 0;
			$mwb_wpr_is_pnt_fee_applied = false;
			$mwb_wpr_notificatin_array=get_option('mwb_wpr_notificatin_array',true);
			$get_points = (int)get_user_meta($user_id, 'mwb_wpr_points', true);
			$pur_by_points = get_user_meta($user_id, 'points_details',true);
			$general_settings = get_option('mwb_wpr_settings_gallery',true);
			$mwb_wpr_purchase_points = (isset($general_settings['mwb_wpr_purchase_points']) && $general_settings['mwb_wpr_purchase_points'] != null) ? $general_settings['mwb_wpr_purchase_points'] : 1;
			$mwb_wpr_product_purchase_price = (isset($general_settings['mwb_wpr_product_purchase_price']) && $general_settings['mwb_wpr_product_purchase_price'] != null) ? intval($general_settings['mwb_wpr_product_purchase_price']) : 1;
			if( empty( $pur_by_points ) || !isset( $pur_by_points )  ){
				$pur_by_points = array();
			}
			$today_date = date_i18n("Y-m-d h:i:sa");
			$total_points = '';
			$order = wc_get_order( $order_id );
			$line_items_fee = $order->get_items( 'fee' );
			if(!empty($line_items_fee)){
				foreach ($line_items_fee as $item_id => $item) {
                      if($woo_ver < "3.0.0"){
                          $mwb_wpr_fee_name = $item['name'];
                      }else{
                          $mwb_wpr_fee_name = $item->get_name();
                      }
                    if($mwb_wpr_fee_name == 'Point Discount'){
                        $mwb_wpr_is_pnt_fee_applied = true;
                        $fee_amount = $item->get_amount();
                    }
                }
			}
			$woo_ver = WC()->version;
			if($woo_ver < "3.0.0"){
				foreach( $order->get_items() as $item_id => $item )
				{
					$item_quantity = $order->get_item_meta($item_id, '_qty', true);
					if(isset($item['item_meta']['Purchasing Option']) && !empty($item['item_meta']['Purchasing Option']))
					{
						if($mwb_wpr_is_pnt_fee_applied){
							$deduct_point += (int)$item['item_meta']['Purchasing Option'][0];
							$total_points = $get_points - $deduct_point;
							if(isset($pur_by_points['pur_by_points']) && !empty($pur_by_points['pur_by_points'])){	
								$pur_by_point_arr = array(
												'pur_by_points'=>$deduct_point,
												'date'=>$today_date);
								$pur_by_points['pur_by_points'][] = $pur_by_point_arr;
							}
							else{	
								$pur_by_point_arr = array();
								$pur_by_point_arr = array(
												'pur_by_points'=>$deduct_point,
												'date'=>$today_date);
							$pur_by_points['pur_by_points'][] = $pur_by_point_arr;
							}
							update_user_meta($user_id,'mwb_wpr_points',$total_points);
							update_user_meta($user_id,'points_details',$pur_by_points);
							if(is_array($mwb_wpr_notificatin_array) && !empty($mwb_wpr_notificatin_array)){
								$mwb_wpr_notificatin_enable=isset($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) ? intval($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) : 0;
								$mwb_wpr_email_subject=isset($mwb_wpr_notificatin_array['mwb_wpr_pro_pur_by_points_email_subject'])? $mwb_wpr_notificatin_array['mwb_wpr_pro_pur_by_points_email_subject'] :'';
								$mwb_wpr_email_discription=isset($mwb_wpr_notificatin_array['mwb_wpr_pro_pur_by_points_discription_custom_id']) ? $mwb_wpr_notificatin_array['mwb_wpr_pro_pur_by_points_discription_custom_id'] :'';
								$mwb_wpr_email_discription=str_replace("[PROPURPOINTS]",$deduct_point,$mwb_wpr_email_discription);
								$mwb_wpr_email_discription=str_replace("[TOTALPOINTS]",$total_points,$mwb_wpr_email_discription);
								$user = get_user_by('email',$user_email);
								$user_name = $user->user_firstname;
								$mwb_wpr_email_discription = str_replace("[USERNAME]",$user_name,$mwb_wpr_email_discription);
								if($mwb_wpr_notificatin_enable){
									$headers = array('Content-Type: text/html; charset=UTF-8');
									wc_mail($user_email,$mwb_wpr_email_subject,$mwb_wpr_email_discription,$headers);
								}
							}
						}
					}
				}
			}
			else{
				if($mwb_wpr_is_pnt_fee_applied){
					$fee_amount = -($fee_amount);
					$fee_to_point = ceil(($mwb_wpr_purchase_points * $fee_amount)/$mwb_wpr_product_purchase_price);
					$points_deduct = $fee_to_point;
					$total_points = $get_points - $points_deduct;
					if(isset($pur_by_points['pur_by_points']) && !empty($pur_by_points['pur_by_points'])){	
						$pur_by_point_arr = array(
										'pur_by_points'=>$points_deduct,
										'date'=>$today_date);
						$pur_by_points['pur_by_points'][] = $pur_by_point_arr;
					}
					else{	
						$pur_by_point_arr = array();
						$pur_by_point_arr = array(
										'pur_by_points'=>$points_deduct,
										'date'=>$today_date);
					$pur_by_points['pur_by_points'][] = $pur_by_point_arr;
					}
					update_user_meta($user_id,'mwb_wpr_points',$total_points);
					update_user_meta($user_id,'points_details',$pur_by_points);
					if(is_array($mwb_wpr_notificatin_array) && !empty($mwb_wpr_notificatin_array)){
						$mwb_wpr_notificatin_enable=isset($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) ? intval($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) : 0;
						$mwb_wpr_email_subject=isset($mwb_wpr_notificatin_array['mwb_wpr_pro_pur_by_points_email_subject'])? $mwb_wpr_notificatin_array['mwb_wpr_pro_pur_by_points_email_subject'] :'';
						$mwb_wpr_email_discription=isset($mwb_wpr_notificatin_array['mwb_wpr_pro_pur_by_points_discription_custom_id']) ? $mwb_wpr_notificatin_array['mwb_wpr_pro_pur_by_points_discription_custom_id'] :'';
						$mwb_wpr_email_discription=str_replace("[PROPURPOINTS]",$points_deduct,$mwb_wpr_email_discription);
						$mwb_wpr_email_discription=str_replace("[TOTALPOINTS]",$total_points,$mwb_wpr_email_discription);
						$user = get_user_by('email',$user_email);
						$user_name = $user->user_firstname;
						$mwb_wpr_email_discription = str_replace("[USERNAME]",$user_name,$mwb_wpr_email_discription);
						if($mwb_wpr_notificatin_enable){
							$headers = array('Content-Type: text/html; charset=UTF-8');
							wc_mail($user_email,$mwb_wpr_email_subject,$mwb_wpr_email_discription,$headers);
						}
					}
				}	
			}
			// Add the Cart Discount amount for particular order_id along with
			if(isset($order) && !empty($order)){
				$order_fees = $order->get_fees();
				if(!empty($order_fees)){
					foreach ( $order_fees as $fee_item_id => $fee_item ){
						$fee_id = $fee_item_id;
						$fee_name = $fee_item->get_name();
						$fee_amount = $fee_item->get_amount();
						if(isset($fee_name) && !empty($fee_name) && $fee_name == 'Cart Discount'){
							update_post_meta($order_id,'mwb_cart_discount#$fee_id',$fee_amount);

							//Now deduct the fee amount from user account as the point
							$get_points = (int)get_user_meta($user_id , 'mwb_wpr_points', true);
							$cart_subtotal_point_arr = get_user_meta($user_id,'points_details',true);
							$mwb_wpr_cart_points_rate = get_option("mwb_wpr_cart_points_rate",1);
							$mwb_wpr_cart_price_rate = get_option("mwb_wpr_cart_price_rate",1);
							$fee_amount = -($fee_amount);
							$fee_to_point = ceil(($mwb_wpr_cart_points_rate * $fee_amount)/$mwb_wpr_cart_price_rate);
							$remaining_point = $get_points - $fee_to_point;
							if(isset($cart_subtotal_point_arr['cart_subtotal_point']) && !empty($cart_subtotal_point_arr['cart_subtotal_point'])){
								$cart_array = array(
												'cart_subtotal_point'=>$fee_to_point,
												'date'=>$today_date);
								$cart_subtotal_point_arr['cart_subtotal_point'][] = $cart_array;
							}
							else{
								if(!is_array($cart_subtotal_point_arr)){
									$cart_subtotal_point_arr = array();
								}
								$cart_array = array(
												'cart_subtotal_point'=>$fee_to_point,
												'date'=>$today_date);
								$cart_subtotal_point_arr['cart_subtotal_point'][] = $cart_array;
							}
							update_user_meta($user_id,'mwb_wpr_points',$remaining_point);
							update_user_meta($user_id,'points_details',$cart_subtotal_point_arr);
							if(isset($_SESSION['mwb_cart_points']) && !empty($_SESSION['mwb_cart_points'])){
								unset($_SESSION['mwb_cart_points']);
							}
							if(is_array($mwb_wpr_notificatin_array) && !empty($mwb_wpr_notificatin_array))
							{
								$mwb_wpr_notificatin_enable=isset($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) ? intval($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) : 0;
								$mwb_wpr_email_subject=isset($mwb_wpr_notificatin_array['mwb_wpr_point_on_cart_subject'])? $mwb_wpr_notificatin_array['mwb_wpr_point_on_cart_subject'] :'';
								$mwb_wpr_email_discription=isset($mwb_wpr_notificatin_array['mwb_wpr_point_on_cart_desc']) ? $mwb_wpr_notificatin_array['mwb_wpr_point_on_cart_desc'] :'';
								$mwb_wpr_email_discription=str_replace("[DEDUCTCARTPOINT]",$fee_to_point,$mwb_wpr_email_discription);
								$mwb_wpr_email_discription=str_replace("[TOTALPOINTS]",$remaining_point,$mwb_wpr_email_discription);
								$user = get_user_by('email',$user_email);
								$user_name = $user->user_firstname;
								$mwb_wpr_email_discription = str_replace("[USERNAME]",$user_name,$mwb_wpr_email_discription);
								if($mwb_wpr_notificatin_enable)
								{	
									$headers = array('Content-Type: text/html; charset=UTF-8');
									wc_mail($user_email,$mwb_wpr_email_subject,$mwb_wpr_email_discription,$headers);
								}
							}
						}
					}
				}
			}
			//mwb 
			$product_points = 0;
			$product_purchased_pnt_only = false;
			foreach( $order->get_items() as $item_id => $item ) {
				$mwb_wpr_items=$item->get_meta_data();
				foreach ($mwb_wpr_items as $key => $mwb_wpr_value) {
					if(isset($mwb_wpr_value->key) && !empty($mwb_wpr_value->key) && ($mwb_wpr_value->key=='Purchased By Points') ){
						$product_points += (int)$mwb_wpr_value->value;
						$product_purchased_pnt_only = true;
					}
				}
			}
			//$user_id =  $order->get_user_id();
			$pur_pro_pnt_only = get_user_meta($user_id,'points_details',true);
			if($product_purchased_pnt_only && isset($user_id) && $user_id > 0){
				if($get_points >= $product_points){
					$total_points_only = $get_points - $product_points;
					if(isset($pur_pro_pnt_only['pur_pro_pnt_only']) && !empty($pur_pro_pnt_only['pur_pro_pnt_only'])){
						$point_only_array = array(
										'pur_pro_pnt_only'=>$product_points,
										'date'=>$today_date);
						$pur_pro_pnt_only['pur_pro_pnt_only'][] = $point_only_array;
					}
					else{
						if(!is_array($pur_pro_pnt_only)){
							$pur_pro_pnt_only = array();
						}
						$point_only_array = array(
										'pur_pro_pnt_only'=>$product_points,
										'date'=>$today_date);
						$pur_pro_pnt_only['pur_pro_pnt_only'][] = $point_only_array;
					}
					update_user_meta($user_id, 'mwb_wpr_points', $total_points_only);
					update_user_meta($user_id, 'points_details', $pur_pro_pnt_only);
				}
			}

		}
		/**
		 * Return/Deduct Points when any user makes a Refund Request
		 * 
		 * @name mwb_wpr_woocommerce_order_refunded.
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link https://www.makewebbetter.com/
		 */
		public function mwb_wpr_woocommerce_order_refunded( $order_id, $refund_id )
		{	
			$order = wc_get_order( $order_id );
			$user_id = $order->get_user_id();
			$user=get_user_by('ID',$user_id);
			$user_email=$user->user_email;
			$mwb_wpr_notificatin_array=get_option('mwb_wpr_notificatin_array',true);
			$general_settings = get_option('mwb_wpr_settings_gallery',true);
			$mwb_wpr_coupon_conversion_value=get_option('mwb_wpr_coupons_gallery',array());
			$mwb_wpr_coupon_conversion_price=$mwb_wpr_coupon_conversion_value['mwb_wpr_coupon_conversion_price'];
			$mwb_wpr_coupon_conversion_points=$mwb_wpr_coupon_conversion_value['mwb_wpr_coupon_conversion_points'];
			$mwb_wpr_purchase_points = (isset($general_settings['mwb_wpr_purchase_points']) && $general_settings['mwb_wpr_purchase_points'] != null) ? $general_settings['mwb_wpr_purchase_points'] : 1;
			$mwb_wpr_product_purchase_price = (isset($general_settings['mwb_wpr_product_purchase_price']) && $general_settings['mwb_wpr_product_purchase_price'] != null) ? intval($general_settings['mwb_wpr_product_purchase_price']) : 1;
			$deduction_of_points = get_user_meta($user_id, 'points_details',true);
			$today_date = date_i18n("Y-m-d h:i:sa");
			$total_points = '';

			foreach( $order->get_items() as $item_id => $item )
			{	
				$woo_ver = WC()->version;
				if( $woo_ver < "3.0.0" )
				{
					$item_quantity = $order->get_item_meta($item_id, '_qty', true);
					if(isset($item['item_meta']['Points']) && !empty($item['item_meta']['Points']))
					{
						$is_refunded = wc_get_order_item_meta($item_id, 'refund_points', true);
						if( !isset( $is_refunded ) || $is_refunded != 'yes' )
						{	
							$get_points = (int)get_user_meta($user_id, 'mwb_wpr_points', true);
							$deduct_point = $item['item_meta']['Points'][0];
							$total_points = $get_points - $deduct_point;
							if(isset($deduction_of_points['deduction_of_points']) && !empty($deduction_of_points['deduction_of_points']))
							{	

								$deduction_arr = array();
								$deduction_arr = array(
												'deduction_of_points'=>$deduct_point,
												'date'=>$today_date);
								$deduction_of_points['deduction_of_points'][] = $deduction_arr;
							}
							else
							{	
								if(!is_array($deduction_of_points)){
									$deduction_of_points = array();
								}
								$deduction_arr = array(
												'deduction_of_points'=>$deduct_point,
												'date'=>$today_date);
								$deduction_of_points['deduction_of_points'][] = $deduction_arr;
							}
							update_user_meta($user_id,'mwb_wpr_points',$total_points);
							update_user_meta($user_id,'points_details',$deduction_of_points);
							wc_update_order_item_meta( $item_id, 'refund_points', 'yes', $prev_value = '' );
							if(is_array($mwb_wpr_notificatin_array) && !empty($mwb_wpr_notificatin_array))
							{
								$mwb_wpr_notificatin_enable=isset($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) ? intval($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) : 0;
								$mwb_wpr_email_subject=isset($mwb_wpr_notificatin_array['mwb_wpr_deduct_assigned_point_subject'])? $mwb_wpr_notificatin_array['mwb_wpr_deduct_assigned_point_subject'] :'';
								$mwb_wpr_email_discription=isset($mwb_wpr_notificatin_array['mwb_wpr_deduct_assigned_point_desciption']) ? $mwb_wpr_notificatin_array['mwb_wpr_deduct_assigned_point_desciption'] :'';
								$mwb_wpr_email_discription=str_replace("[DEDUCTEDPOINT]",$deduct_point,$mwb_wpr_email_discription);
								$mwb_wpr_email_discription=str_replace("[TOTALPOINTS]",$total_points,$mwb_wpr_email_discription);
								$user = get_user_by('email',$user_email);
								$user_name = $user->user_firstname;
								$mwb_wpr_email_discription = str_replace("[USERNAME]",$user_name,$mwb_wpr_email_discription);
								if($mwb_wpr_notificatin_enable)
								{
									$headers = array('Content-Type: text/html; charset=UTF-8');
									wc_mail($user_email,$mwb_wpr_email_subject,$mwb_wpr_email_discription,$headers);
								}
							}
						}
					}
					if(isset($item['item_meta']['Purchasing Option']) && !empty($item['item_meta']['Purchasing Option']))
					{	
						$is_refunded = wc_get_order_item_meta($item_id, 'refund_purchased_point', true);
						if( !isset( $is_refunded ) || $is_refunded != 'yes' )
						{
							$get_points = (int)get_user_meta($user_id, 'mwb_wpr_points', true);	
							$return_pur_point = $item['item_meta']['Purchasing Option'][0];
							// $return_pur_point = $item_quantity * $return_pur_point;
							$total_return_points = $get_points + $return_pur_point;
							if(isset($deduction_of_points['return_pur_points']) && !empty($deduction_of_points['return_pur_points']))
							{	
								$return_arr = array();
								$return_arr = array(
												'return_pur_points'=>$return_pur_point,
												'date'=>$today_date);
								$deduction_of_points['return_pur_points'][] = $return_arr;
							}
							else
							{	
								if(!is_array($deduction_of_points)){
									$deduction_of_points = array();
								}
								$return_arr = array(
												'return_pur_points'=>$return_pur_point,
												'date'=>$today_date);
								$deduction_of_points['return_pur_points'][] = $return_arr;
							}
							update_user_meta( $user_id,'mwb_wpr_points',$total_return_points );
							update_user_meta( $user_id,'points_details',$deduction_of_points );
							wc_update_order_item_meta( $item_id, 'refund_purchased_point', 'yes', $prev_value = '' );
							if(is_array($mwb_wpr_notificatin_array) && !empty($mwb_wpr_notificatin_array))
							{
								$mwb_wpr_notificatin_enable=isset($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) ? intval($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) : 0;
								$mwb_wpr_email_subject=isset($mwb_wpr_notificatin_array['mwb_wpr_return_pro_pur_subject'])? $mwb_wpr_notificatin_array['mwb_wpr_return_pro_pur_subject'] :'';
								$mwb_wpr_email_discription=isset($mwb_wpr_notificatin_array['mwb_wpr_return_pro_pur_description']) ? $mwb_wpr_notificatin_array['mwb_wpr_return_pro_pur_description'] :'';
								$mwb_wpr_email_discription=str_replace("[RETURNPOINT]",$return_pur_point,$mwb_wpr_email_discription);
								$mwb_wpr_email_discription=str_replace("[TOTALPOINTS]",$total_return_points,$mwb_wpr_email_discription);
								$user = get_user_by('email',$user_email);
								$user_name = $user->user_firstname;
								$mwb_wpr_email_discription = str_replace("[USERNAME]",$user_name,$mwb_wpr_email_discription);
								if($mwb_wpr_notificatin_enable)
								{
									$headers = array('Content-Type: text/html; charset=UTF-8');
									wc_mail($user_email,$mwb_wpr_email_subject,$mwb_wpr_email_discription,$headers);
								}
							}
						}
						
					}
				}
				else
				{
					$item_quantity = wc_get_order_item_meta($item_id, '_qty', true);
					$mwb_wpr_items=$item->get_meta_data();
					if(is_array($mwb_wpr_items) && !empty($mwb_wpr_items))
					{
						foreach ($mwb_wpr_items as $key => $mwb_wpr_value) 
						{	
							if(isset($mwb_wpr_value->key) && !empty($mwb_wpr_value->key) && ($mwb_wpr_value->key=='Points'))
							{	
								$is_refunded = wc_get_order_item_meta($item_id, 'refund_points', true);
								if( !isset( $is_refunded ) || $is_refunded != 'yes' )
								{	
									$get_points = (int)get_user_meta($user_id, 'mwb_wpr_points', true);
									$deduct_point = $mwb_wpr_value->value;
									$total_points = $get_points - $deduct_point;
									if(isset($deduction_of_points['deduction_of_points']) && !empty($deduction_of_points['deduction_of_points']))
									{	

										$deduction_arr = array();
										$deduction_arr = array(
														'deduction_of_points'=>$deduct_point,
														'date'=>$today_date);
										$deduction_of_points['deduction_of_points'][] = $deduction_arr;
									}
									else
									{	
										$deduction_arr = array();
										$deduction_arr = array(
														'deduction_of_points'=>$deduct_point,
														'date'=>$today_date);
										$deduction_of_points['deduction_of_points'][] = $deduction_arr;
									}
									update_user_meta($user_id,'mwb_wpr_points',$total_points);
									update_user_meta($user_id,'points_details',$deduction_of_points);
									wc_update_order_item_meta( $item_id, 'refund_points', 'yes', $prev_value = '' );
									if(is_array($mwb_wpr_notificatin_array) && !empty($mwb_wpr_notificatin_array))
									{
										$mwb_wpr_notificatin_enable=isset($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) ? intval($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) : 0;
										$mwb_wpr_email_subject=isset($mwb_wpr_notificatin_array['mwb_wpr_deduct_assigned_point_subject'])? $mwb_wpr_notificatin_array['mwb_wpr_deduct_assigned_point_subject'] :'';
										$mwb_wpr_email_discription=isset($mwb_wpr_notificatin_array['mwb_wpr_deduct_assigned_point_desciption']) ? $mwb_wpr_notificatin_array['mwb_wpr_deduct_assigned_point_desciption'] :'';
										$mwb_wpr_email_discription=str_replace("[DEDUCTEDPOINT]",$deduct_point,$mwb_wpr_email_discription);
										$mwb_wpr_email_discription=str_replace("[TOTALPOINTS]",$total_points,$mwb_wpr_email_discription);
										$user = get_user_by('email',$user_email);
										$user_name = $user->user_firstname;
										$mwb_wpr_email_discription = str_replace("[USERNAME]",$user_name,$mwb_wpr_email_discription);
										if($mwb_wpr_notificatin_enable)
										{
											$headers = array('Content-Type: text/html; charset=UTF-8');
											wc_mail($user_email,$mwb_wpr_email_subject,$mwb_wpr_email_discription,$headers);
										}
									}
								}
							}
						}
					}
				}
			}

			$item_conversion_id_set = get_post_meta($order_id, "$order_id#item_conversion_id", false);
			$order_total=$order->get_total();
			$order_total = str_replace( wc_get_price_decimal_separator(), '.', strval( $order_total ) );
			$points_calculation = ceil(($order_total*$mwb_wpr_coupon_conversion_points)/$mwb_wpr_coupon_conversion_price);
			if( isset( $item_conversion_id_set[0] ) && !empty( $item_conversion_id_set[0] ) && $item_conversion_id_set[0]=='set' )
			{	
				$get_points = (int)get_user_meta($user_id, 'mwb_wpr_points', true);
				$all_refunds = $order->get_refunds();
				$refund_item = $all_refunds[0];
				$refund_amount = $refund_item->get_amount();
				$refund_amount = ceil(($refund_amount*$mwb_wpr_coupon_conversion_points)/$mwb_wpr_coupon_conversion_price);
				$deduct_currency_spent = $refund_amount;
				$remaining_points = $get_points - $deduct_currency_spent;
				if(isset($deduction_of_points['deduction_currency_spent']) && !empty($deduction_of_points['deduction_currency_spent']))
				{	
					$currency_arr = array();
					$currency_arr = array(
									'deduction_currency_spent'=>$deduct_currency_spent,
									'date'=>$today_date);
					$deduction_of_points['deduction_currency_spent'][] = $currency_arr;
				}
				else
				{	
					$currency_arr = array();
					$currency_arr = array(
									'deduction_currency_spent'=>$deduct_currency_spent,
									'date'=>$today_date);
				$deduction_of_points['deduction_currency_spent'][] = $currency_arr;
				}
				update_user_meta($user_id,'mwb_wpr_points',$remaining_points);
				update_user_meta($user_id,'points_details',$deduction_of_points);
				if(is_array($mwb_wpr_notificatin_array) && !empty($mwb_wpr_notificatin_array))
				{
					$mwb_wpr_notificatin_enable=isset($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) ? intval($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) : 0;
					$mwb_wpr_email_subject=isset($mwb_wpr_notificatin_array['mwb_wpr_deduct_per_currency_point_subject'])? $mwb_wpr_notificatin_array['mwb_wpr_deduct_per_currency_point_subject'] :'';
					$mwb_wpr_email_discription=isset($mwb_wpr_notificatin_array['mwb_wpr_deduct_per_currency_point_description']) ? $mwb_wpr_notificatin_array['mwb_wpr_deduct_per_currency_point_description'] :'';
					$mwb_wpr_email_discription=str_replace("[DEDUCTEDPOINT]",$deduct_currency_spent,$mwb_wpr_email_discription);
					$mwb_wpr_email_discription=str_replace("[TOTALPOINTS]",$remaining_points,$mwb_wpr_email_discription);
					$user = get_user_by('email',$user_email);
					$user_name = $user->user_firstname;
					$mwb_wpr_email_discription = str_replace("[USERNAME]",$user_name,$mwb_wpr_email_discription);
					if($mwb_wpr_notificatin_enable)
					{	
						$headers = array('Content-Type: text/html; charset=UTF-8');
						wc_mail($user_email,$mwb_wpr_email_subject,$mwb_wpr_email_discription,$headers);
					}
				}
			}
			//Refund amount of Point Discount
			if(isset($order) && !empty($order)){
				$is_refunded = get_post_meta($order_id, 'mwb_point_discount', true);
				if(!isset($is_refunded) || $is_refunded != 'Refunded'){
					$general_settings = get_option('mwb_wpr_settings_gallery',true);
					$mwb_wpr_purchase_points = (isset($general_settings['mwb_wpr_purchase_points']) && $general_settings['mwb_wpr_purchase_points'] != null) ? $general_settings['mwb_wpr_purchase_points'] : 1;
					$mwb_wpr_product_purchase_price = (isset($general_settings['mwb_wpr_product_purchase_price']) && $general_settings['mwb_wpr_product_purchase_price'] != null) ? intval($general_settings['mwb_wpr_product_purchase_price']) : 1;
					$deduction_of_points = get_user_meta($user_id, 'points_details',true);
					$order_fees = $order->get_fees();
					$get_points = (int)get_user_meta($user_id , 'mwb_wpr_points', true);
					if(!empty($order_fees)){
						foreach ( $order_fees as $fee_item_id => $fee_item ){
							$fee_id = $fee_item_id;
							$fee_name = $fee_item->get_name();
							$fee_amount = $fee_item->get_amount();
							if(isset($fee_name) && !empty($fee_name) && $fee_name == 'Point Discount'){
								update_post_meta($order_id,'mwb_point_discount#$fee_id',$fee_amount);
								//Now deduct the fee amount from user account as the point
								$fee_amount = -($fee_amount);
								$fee_to_point = ceil(($mwb_wpr_purchase_points * $fee_amount)/$mwb_wpr_product_purchase_price);
								$total_point = $get_points + $fee_to_point;
								if(isset($deduction_of_points['return_pur_points']) && !empty($deduction_of_points['return_pur_points'])){
									$return_arr = array(
													'return_pur_points'=>$fee_to_point,
													'date'=>$today_date);
									$deduction_of_points['return_pur_points'][] = $return_arr;
								}
								else{	
									if(!is_array($deduction_of_points)){
										$deduction_of_points = array();
									}
									$return_arr = array(
													'return_pur_points'=>$fee_to_point,
													'date'=>$today_date);
									$deduction_of_points['return_pur_points'][] = $return_arr;
								}
								update_user_meta($user_id,'mwb_wpr_points',$total_point);
								update_user_meta($user_id,'points_details',$deduction_of_points);
								update_post_meta($order_id,'mwb_point_discount','Refunded');
								if(is_array($mwb_wpr_notificatin_array) && !empty($mwb_wpr_notificatin_array)){
									$mwb_wpr_notificatin_enable=isset($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) ? intval($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) : 0;
									$mwb_wpr_email_subject=isset($mwb_wpr_notificatin_array['mwb_wpr_return_pro_pur_subject'])? $mwb_wpr_notificatin_array['mwb_wpr_return_pro_pur_subject'] :'';
									$mwb_wpr_email_discription=isset($mwb_wpr_notificatin_array['mwb_wpr_return_pro_pur_description']) ? $mwb_wpr_notificatin_array['mwb_wpr_return_pro_pur_description'] :'';
									$mwb_wpr_email_discription=str_replace("[RETURNPOINT]",$fee_to_point,$mwb_wpr_email_discription);
									$mwb_wpr_email_discription=str_replace("[TOTALPOINTS]",$total_point,$mwb_wpr_email_discription);
									$user = get_user_by('email',$user_email);
									$user_name = $user->user_firstname;
									$mwb_wpr_email_discription = str_replace("[USERNAME]",$user_name,$mwb_wpr_email_discription);
									if($mwb_wpr_notificatin_enable)
									{
										$headers = array('Content-Type: text/html; charset=UTF-8');
										wc_mail($user_email,$mwb_wpr_email_subject,$mwb_wpr_email_discription,$headers);
									}
								}
							}
						}
					}
				}				
			}
		}		
		/**
		 * Runs a cron for notifying the users who have any memberhip level and which is going to be expired in next two weeks.
		 * 
		 * @name mwb_wpr_do_this_hourly.
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link https://www.makewebbetter.com/
		 */

		public function mwb_wpr_do_this_hourly()
		{	
			$today_date = date_i18n("Y-m-d");
			$args['meta_query'] = array(						
						array(
							'key'=>'membership_level'
						)
					);
			$user_data= get_users( $args );
			if(is_array($user_data) && !empty($user_data)){
				foreach ($user_data as $key => $value) {
					$user_id = $value->data->ID;
					$user_email=$value->data->user_email;
					if(isset($user_id) && !empty($user_id)){
						$mwb_wpr_mem_expr = get_user_meta($user_id,'membership_expiration',true);
						$user_level = get_user_meta($user_id,'membership_level',true);
						if(isset($mwb_wpr_mem_expr) && !empty($mwb_wpr_mem_expr)){
							$notification_date= date('Y-m-d', strtotime($mwb_wpr_mem_expr. ' -1 weeks'));
							if($today_date == $notification_date)
							{
								$subject = __('Membership Expiration Alert!',MWB_WPR_Domain);
								$message = __('Your User Level ',MWB_WPR_Domain).$user_level.__(' is going to expired on date of ',MWB_WPR_Domain).$mwb_wpr_mem_expr.__(' You can upgrade your level or can renew that level again after expiration.',MWB_WPR_Domain);
								wc_mail($user_email,$subject,$message);
							}
							$expired_date= date('Y-m-d', strtotime($mwb_wpr_mem_expr));
							if($today_date > $expired_date)
							{
								delete_user_meta($user_id,'membership_level');
								$subject = __('No Longer Membership User',MWB_WPR_Domain);
								$message = __('Your User Level ',MWB_WPR_Domain).$user_level.__(' has been expired. You can upgrade your level to another or can renew that level again ',MWB_WPR_Domain);
								wc_mail($user_email,$subject,$message);
							}
						}
					}
				}
			}
		}

		/**
		 * This function is used to add the Custom Widget for Points and Reward
		 * 
		 * @name mwb_wpr_custom_widgets.
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link https://www.makewebbetter.com/
		 */
		public function mwb_wpr_custom_widgets()
		{
			include_once MWB_WPR_DIRPATH.'/includes/admin/class-mwb-wpr-custom-widget.php';
		}

		/**
		 * This function is used to add the Points inside the Orders(if any)
		 * 
		 * @name mwb_wpr_woocommerce_admin_order_item_headers.
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link https://www.makewebbetter.com/
		 */
		public function mwb_wpr_woocommerce_admin_order_item_headers($order){

			foreach( $order->get_items() as $item_id => $item ){
				$woo_ver = WC()->version;
   				if($woo_ver < "3.0.0"){
   					if(isset($item['item_meta']['Points']) && !empty($item['item_meta']['Points'])){
   						?>
						<th class="quantity sortable"><?php _e( 'Points', MWB_WPR_Domain ); ?></th>
						<?php
   					}
				}
				else{
					$mwb_wpr_items=$item->get_meta_data();
					foreach ($mwb_wpr_items as $key => $mwb_wpr_value){
						if(isset($mwb_wpr_value->key) && !empty($mwb_wpr_value->key) && ($mwb_wpr_value->key=='Points') ){
							?>
						<th class="quantity sortable"><?php _e( 'Points', MWB_WPR_Domain ); ?></th>
						<?php
						}
					}
				}
			}
		}

		/**
		 * This function is used to add the Points inside the Orders(if any)
		 * 
		 * @name mwb_wpr_woocommerce_admin_order_item_values.
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link https://www.makewebbetter.com/
		 */
		public function mwb_wpr_woocommerce_admin_order_item_values($product,$item,$item_id){
			$woo_ver = WC()->version;
			if($woo_ver < "3.0.0"){
				if(isset($item['item_meta']['Points']) && !empty($item['item_meta']['Points'])){
					$item_points = (int)$item['item_meta']['Points'][0];
					?>
					<td class="item_cost" width="1%" data-sort-value="<?php echo $item_points;?>">
					<div class="view">
						<?php
							echo $item_points;
						?>
					</div>
				</td>
				<?php
				}
			}
			else{
				$mwb_wpr_items=$item->get_meta_data();
				foreach ($mwb_wpr_items as $key => $mwb_wpr_value){
					if(isset($mwb_wpr_value->key) && !empty($mwb_wpr_value->key) && ($mwb_wpr_value->key=='Points') ){
						$item_points = (int)$mwb_wpr_value->value;
						
						?>
						<td class="item_cost" width="1%" data-sort-value="<?php echo $item_points;?>">
						<div class="view">
							<?php
								echo $item_points;
							?>
						</div>
					</td>
						<?php
					}
				}
			}
		}
		/**
		 * This function is used to add the textbox for variable products
		 * 
		 * @name mwb_wpr_woocommerce_variation_options_pricing
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link https://www.makewebbetter.com/
		 */
		public function mwb_wpr_woocommerce_variation_options_pricing( $loop, $variation_data, $variation ){
			$woo_ver = WC()->version;
           	if($woo_ver < "3.0.0"){
				$mwb_wpr_variable_points = get_post_meta($variation->ID,'mwb_wpr_variable_points',true);
           	}else{
           		if(isset($variation_data['mwb_wpr_variable_points'][0])){
					$mwb_wpr_variable_points = $variation_data['mwb_wpr_variable_points'][0];
				}else{
					$mwb_wpr_variable_points = '';
				}
           	}
			
			?>
			<?php
			if(is_admin()){
					woocommerce_wp_text_input( array(
					'id'            => "mwb_wpr_variable_points_{$loop}",
					'name'          => "mwb_wpr_variable_points_{$loop}",
					'value'         => $mwb_wpr_variable_points,
					'label'         => __( 'Enter Point', MWB_WPR_Domain ),
					'data_type'     => 'price',
					'wrapper_class' => 'form-row form-row-first',
					'placeholder'   => __( 'Product Point', MWB_WPR_Domain ),
				) );
			}
			//MWB Custom Work
			
       		if(isset($variation_data['mwb_wpr_variable_points_purchase'][0])){
				$mwb_wpr_variable_points_purchase = $variation_data['mwb_wpr_variable_points_purchase'][0];
			}else{
				$mwb_wpr_variable_points_purchase = '';
			}
			
			if(is_admin()){
					woocommerce_wp_text_input( array(
					'id'            => "mwb_wpr_variable_points_purchase_{$loop}",
					'name'          => "mwb_wpr_variable_points_purchase_{$loop}",
					'value'         => $mwb_wpr_variable_points_purchase,
					'label'         => __( 'Enter Point for purchase', MWB_WPR_Domain ),
					'data_type'     => 'price',
					'wrapper_class' => 'form-row form-row-first',
					'placeholder'   => __( 'Product Point for purchase', MWB_WPR_Domain ),
				) );
			}
           	


			//End Of Custom Work
		}

		/**
		 * This function is used to save the product variation points
		 * 
		 * @name mwb_wpr_woocommerce_save_product_variation
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link https://www.makewebbetter.com/
		 */
		public function mwb_wpr_woocommerce_save_product_variation($variation_id, $i){
			if(isset($_POST['mwb_wpr_variable_points_'.$i]))
			{
				$mwb_wpr_points = $_POST['mwb_wpr_variable_points_'.$i];
				update_post_meta($variation_id, 'mwb_wpr_variable_points', $mwb_wpr_points);
			}

			// MWB Custom Work
			if(isset($_POST['mwb_wpr_variable_points_purchase_'.$i]))
			{
				$mwb_wpr_points_purchase = $_POST['mwb_wpr_variable_points_purchase_'.$i];	

				update_post_meta($variation_id, 'mwb_wpr_variable_points_purchase', $mwb_wpr_points_purchase);
			}
			// End of Custom Work
		}

		/**
		 * This function is used for run the cron for points expiration and handles accordingly
		 * 
		 * @name mwb_wpr_check_daily_about_points_expiration
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link https://www.makewebbetter.com/
		 */
		public function mwb_wpr_check_daily_about_points_expiration(){
            $message = '';
            $mwb_wpr_points_expiration_enable = get_option('mwb_wpr_points_expiration_enable','off');
            $mwb_wpr_email_tpl = file_get_contents( MWB_WPR_DIRPATH.'/includes/admin/mwb_wpr_points_expiration_email_template.php');
            $mwb_wpr_points_expiration_email = get_option('mwb_wpr_points_expiration_email','');
            if($mwb_wpr_points_expiration_enable == 'on'){
                $mwb_wpr_points_expiration_threshold = get_option('mwb_wpr_points_expiration_threshold','');
                $mwb_wpr_points_expiration_time_num = get_option('mwb_wpr_points_expiration_time_num','');
                $mwb_wpr_points_expiration_time_drop = get_option('mwb_wpr_points_expiration_time_drop','days');
                $today_date = date_i18n("Y-m-d");
                $args['meta_query'] = array(                        
                            array(
                                'key'=>'mwb_wpr_points'
                            )
                        );
                $user_data= get_users( $args );
                if(is_array($user_data) && !empty($user_data)){
                    foreach ($user_data as $key => $value) {
                        $user_id = $value->data->ID;
                        $user_email=$value->data->user_email;
                        if(isset($user_id) && !empty($user_id)){
                            $get_points = get_user_meta($user_id,'mwb_wpr_points',true);
                            if($get_points == $mwb_wpr_points_expiration_threshold || $get_points > $mwb_wpr_points_expiration_threshold){
                            	$get_expiration_date = get_user_meta($user_id,'mwb_wpr_points_expiration_date',true);
                            	if(!isset($get_expiration_date) || empty($get_expiration_date)){
                            		$expiration_date= date_i18n('Y-m-d', strtotime($today_date. ' +'.$mwb_wpr_points_expiration_time_num.' '.$mwb_wpr_points_expiration_time_drop));
	                                update_user_meta($user_id,'mwb_wpr_points_expiration_date',$expiration_date);
	                                $headers = array('Content-Type: text/html; charset=UTF-8');
	                                //Expiration Date has been set to User
	                                $subject = __('Redeem your Points before it will get expired!',MWB_WPR_Domain);
	                                $mwb_wpr_threshold_notif = get_option('mwb_wpr_threshold_notif','You have reached your Threshold and your Total Point is: [TOTALPOINT], which will get expired on [EXPIRYDATE]');
	                                $message = $mwb_wpr_email_tpl;
	                                $message = str_replace('[CUSTOMMESSAGE]', $mwb_wpr_threshold_notif, $message);
	                                $sitename = get_bloginfo();
	                                $message = str_replace('[SITENAME]', $sitename, $message);
	                                $message = str_replace('[TOTALPOINT]', $get_points, $message);
	                                $message = str_replace('[EXPIRYDATE]', $expiration_date, $message);
	                                wc_mail($user_email,$subject,$message,$headers);
                            	}
                            }
                            $get_expiration_date = get_user_meta($user_id,'mwb_wpr_points_expiration_date',true);
                            if(isset($get_expiration_date) && !empty($get_expiration_date)){
                                $send_notification_date= date_i18n('Y-m-d', strtotime($get_expiration_date. ' -'.$mwb_wpr_points_expiration_email.' days'));
                                if(isset($send_notification_date) && !empty($send_notification_date)){
                                    if($today_date == $send_notification_date){
                                        $mwb_user_point_expiry = get_user_meta($user_id,'mwb_wpr_points_expiration_date',true);
                                        $headers = array('Content-Type: text/html; charset=UTF-8');
                                        $subject = __('Hurry!! Points Expiration has just a few days',MWB_WPR_Domain);
                                        $mwb_wpr_re_notification = get_option('mwb_wpr_re_notification','Do not forget to redeem your points([TOTALPOINT]) before it will get expired on [EXPIRYDATE]');
                                        $message = $mwb_wpr_email_tpl;
                                        $message = str_replace('[CUSTOMMESSAGE]', $mwb_wpr_re_notification, $message);
                                        $sitename = get_bloginfo();
                                        $message = str_replace('[SITENAME]', $sitename, $message);
                                        $message = str_replace('[TOTALPOINT]', $get_points, $message);
                                        $message = str_replace('[EXPIRYDATE]', $mwb_user_point_expiry, $message);
                                        //expiration email before one week
                                        wc_mail($user_email,$subject,$message,$headers);
                                    }
                                }
                                if($today_date == $get_expiration_date && $get_points > 0){
		                            $expired_detail_points = get_user_meta($user_id, 'points_details',true);
									if(isset($expired_detail_points['expired_details']) && !empty($expired_detail_points['expired_details'])){
										
										$exp_array = array(
											'expired_details'=>$get_points,
											'date'=>$today_date
										);
										$expired_detail_points['expired_details'][] = $exp_array;
									}
									else{
										if(!is_array($expired_detail_points)){
											$expired_detail_points = array();
										}
										$exp_array = array(
											'expired_details'=>$get_points,
											'date'=>$today_date
										);
										$expired_detail_points['expired_details'][] = $exp_array;
									}
                                    update_user_meta($user_id,'mwb_wpr_points',0);
                                    update_user_meta($user_id,'points_details',$expired_detail_points);
                                    delete_user_meta($user_id,'mwb_wpr_points_expiration_date');
                                    $headers = array('Content-Type: text/html; charset=UTF-8');
                                    $subject = __('Points has been Expired!',MWB_WPR_Domain);
                                    $mwb_wpr_expired_notification = get_option('mwb_wpr_expired_notification','Your Points has been expired, you may earn more Points and use the benefit more');
                                    $message = $mwb_wpr_email_tpl;
                                    $sitename = get_bloginfo();
                                    $message = str_replace('[SITENAME]', $sitename, $message);
                                    $message = str_replace('[CUSTOMMESSAGE]', $mwb_wpr_expired_notification, $message);
                                    //points has been expired
                                    wc_mail($user_email,$subject,$message,$headers);
                                }
                            }
                        }
                    }
                }
            }
        }
        
        /**
		 * This function is used for verifying the licensing code
		 * 
		 * @name mwb_wpr_register_license
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link https://www.makewebbetter.com/
		 */
        public function mwb_wpr_register_license(){
        	check_ajax_referer( 'mwb-wpr-verify-nonce', 'mwb_nonce' );

        	//Remove the www from the Host Name
			$host_server = $_SERVER['HTTP_HOST'];
			if( strpos($host_server,'www.') == 0 ) {

				$host_server = str_replace('www.','',$host_server);
			}

			$mwb_license_key = sanitize_text_field( $_POST['license_key'] );
			$mwb_admin_name = '';
			$mwb_admin_email = get_option( 'admin_email', null );
			$mwb_admin_details = get_user_by('email', $mwb_admin_email);
			if(isset($mwb_admin_details->data)){
				if(isset($mwb_admin_details->data->display_name)){
					$mwb_admin_name = $mwb_admin_details->data->display_name;
				}
			}
			$mwb_license_arr = array(
				'license_key' => $mwb_license_key,
				'domain_name' => $host_server,
				'admin_name' => $mwb_admin_name,
				'admin_email' => $mwb_admin_email,
				'plugin_name' => 'WooCommerce Ultimate Points And Rewards'
				);
            $postdata['body'] = $mwb_license_arr;
            $postdata['sslverify'] = false;
            $response = wp_remote_post( "https://makewebbetter.com/codecanyon/validate_license.php", $postdata );
            $mwb_res = array();
            if(isset($response['body'])){
                $mwb_res = $response['body'];
                $mwb_res = json_decode($mwb_res,true);
            }
            if(isset($mwb_res['status'])){    
                if( $mwb_res['status'] == true ){
                    update_option('mwb_wpr_license_hash',$mwb_res['hash']);
                    update_option('mwb_wpr_plugin_name','WooCommerce Ultimate Points And Rewards');
                    update_option('mwb_wpr_license_key',$mwb_res['mwb_key']);
                    update_option('mwb_wpr_plugin_verified',true);
                    echo json_encode( array('status'=>true,'msg'=>__('Successfully Verified',MWB_WPR_Domain) ) );
                }
                else if( $mwb_res['status'] == false ){	
                	update_option('mwb_wpr_plugin_verified',false);
                    echo json_encode( array('status'=>false,'msg'=> $mwb_res['msg']) );
                }
            }
            else{
                echo json_encode( array('status'=>false,'msg'=> "Please Try Again!") );
            }    
            wp_die();
        }

        /**
		 * This function is used on cron run for updating the content of json file from makewebbetter server.
		 * 
		 * @name mwb_wpr_update_json_process
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link https://www.makewebbetter.com/
		 */
        
        public function mwb_wpr_update_json_process(){

        	$uploadDirPath = wp_upload_dir()["basedir"].'/mwb_wpr_json';
			if(!is_dir($uploadDirPath)) {
				wp_mkdir_p($uploadDirPath);
				chmod($uploadDirPath,0755);
			}
	    	$data = file_get_contents('https://makewebbetter.com/pluginupdates/addons/par/api.json');
	    	$handle = fopen(wp_upload_dir()["basedir"].'/mwb_wpr_json/api.json', 'w');
			fwrite($handle,$data);
			fclose($handle);
        }
	}
	new MWB_WPR_Admin_Manager();
}
?>