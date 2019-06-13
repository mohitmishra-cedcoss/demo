<?php
/**
 * Exit if accessed directly
 */
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if( !class_exists( 'MWB_WPR_Front_End' ) )
{
	/**
	 * This is class for managing front end points and reward functionality
	 *
	 * @name    MWB_WPR_Front_End
	 * @category Class
	 * @author   makewebbetter <webmaster@makewebbetter.com>
	 */
	class MWB_WPR_Front_End{
		/**
		 * This is construct of class where all action and filter is defined
		 * 
		 * @name __construct
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function __construct() 
		{
			add_action('plugins_loaded',array($this,'mwb_wpr_load_woocommerce'));
			

		}

		/**
		 * This load plugin after load woocommerce.
		 * 
		 * @name mwb_wpr_load_woocommerce
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wpr_load_woocommerce()
		{
			if(function_exists('WC'))
			{
				$general_settings = get_option('mwb_wpr_settings_gallery',true);
				
				$enable_mwb_wpr = isset($general_settings['enable_mwb_wpr']) ? intval($general_settings['enable_mwb_wpr']) : 0;
				if($enable_mwb_wpr)
				{
					$this->add_hooks_and_filters();
				}
			}
		}

		/**
		 * This contains hooks and filter of plugin.
		 * 
		 * @name add_hooks_and_filters
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function add_hooks_and_filters()
		{

			add_action( 'mwb_wpr_digit_registration' , array( $this, 'mwb_wpr_new_customer_digit_registered' ));
			add_action( 'woocommerce_created_customer' , array( $this, 'mwb_wpr_new_customer_registerd' ), 10, 3 );
			add_action('woocommerce_single_product_summary',array($this,'mwb_display_product_points'),7);
			add_filter( 'mwb_wgm_price_meta_data',array($this, 'mwb_wpr_wgm_price_meta_data'), 10, 4);
			add_filter( 'woocommerce_add_cart_item_data', array($this, 'mwb_wpr_woocommerce_add_cart_item_data'), 10, 3);
			add_filter( 'woocommerce_get_item_data', array ($this, 'mwb_wpr_woocommerce_get_item_data' ), 10, 2 );
			add_action( 'woocommerce_order_status_changed', array ($this, 'mwb_wpr_woocommerce_order_status_changed' ), 10, 3 );
			add_filter('woocommerce_account_menu_items',array($this, 'mwb_wpr_points_dashboard'));
			add_action( 'woocommerce_account_points_endpoint', array($this, 'mwb_wpr_account_points' ));
			add_action( 'woocommerce_account_view-log_endpoint', array($this, 'mwb_wpr_account_viewlog' ));
			if(WC()->version<'3.0.0')
			{
				add_action( 'woocommerce_add_order_item_meta', array ( $this, 'mwb_wpr_woocommerce_add_order_item_meta' ), 10, 2 );
				add_action( 'woocommerce_order_add_coupon', array ( $this, 'mwb_wpr_woocommerce_order_add_coupon' ), 10, 5 );
			}
			else
			{
				add_action( 'woocommerce_new_order_item', array ( $this, 'mwb_wpr_woocommerce_order_add_coupon_woo_latest_version' ),10,2);
				add_action('woocommerce_checkout_create_order_line_item',array($this,'mwb_wpr_woocommerce_add_order_item_meta_version_3'),10,4);
			}
			add_action( 'init', array($this,'mwb_wpr_add_my_account_endpoint') );
			add_filter('woocommerce_product_review_comment_form_args',array($this,'mwb_wpr_woocommerce_comment_point'),10,1);
			add_action('woocommerce_before_customer_login_form',array($this,'mwb_wpr_woocommerce_signup_point'));
			add_action( 'wp_enqueue_scripts', array ( $this, 'mwb_wpr_wp_enqueue_scripts' ), 10, 1 );
			add_action( 'wp_ajax_mwb_wpr_generate_custom_coupon', array($this, 'mwb_wpr_generate_custom_coupon'));
			add_action( 'wp_ajax_nopriv_mwb_wpr_generate_custom_coupon', array($this, 'mwb_wpr_generate_custom_coupon'));
			add_action( 'wp_ajax_mwb_wpr_generate_original_coupon', array($this, 'mwb_wpr_generate_original_coupon'));
			add_action( 'wp_ajax_nopriv_mwb_wpr_generate_original_coupon', array($this, 'mwb_wpr_generate_original_coupon'));
			add_action('comment_post', array($this,'mwb_comment_insert_into_database'),10,3);
			add_filter('woocommerce_update_cart_action_cart_updated', array($this, 'mwb_update_cart_points'));
			//add_filter( 'the_content', array($this, 'mwb_wpr_add_social_links'), 10,1 );
			add_filter( 'woocommerce_get_price_html', array($this, 'mwb_wpr_user_level_discount_on_price'), 10,2);
			add_action( 'woocommerce_before_calculate_totals', array ($this, 'mwb_wpr_woocommerce_before_calculate_totals' ), 10, 1 );
			add_action( 'woocommerce_before_add_to_cart_button', array($this, "mwb_wpr_woocommerce_before_add_to_cart_button"), 10, 1);
			add_filter('woocommerce_thankyou_order_received_text',array($this,'mwb_wpr_woocommerce_thankyou'),10,2);
			add_filter( 'woocommerce_order_item_display_meta_key',array($this,'mwb_wpr_woocommerce_order_item_display_meta_key'),10,1 );
			add_filter( 'woocommerce_hidden_order_itemmeta', array($this,'mwb_wpr_woocommerce_hidden_order_itemmeta'),10,1 );
			add_action( 'wp_ajax_mwb_wpr_sharing_point_to_other', array($this, 'mwb_wpr_sharing_point_to_other'));
			add_action( 'wp_ajax_nopriv_mwb_wpr_sharing_point_to_other', array($this, 'mwb_wpr_sharing_point_to_other'));
			add_action( 'wp_ajax_mwb_wpr_append_variable_point', array($this, 'mwb_wpr_append_variable_point'));
			add_action( 'wp_ajax_nopriv_mwb_wpr_append_variable_point', array($this, 'mwb_wpr_append_variable_point'));
			add_action( 'wp_ajax_mwb_pro_purchase_points_only', array($this, 'mwb_pro_purchase_points_only'));
			add_action( 'wp_ajax_nopriv_mwb_pro_purchase_points_only', array($this, 'mwb_pro_purchase_points_only'));

			add_action('woocommerce_cart_calculate_fees',array($this,'mwb_wpr_woocommerce_cart_calculate_fees'));
			add_action('wp_loaded' , array($this,'mwb_wpr_referral_link_using_cookie'));
			add_action('woocommerce_cart_actions',array($this,'mwb_wpr_woocommerce_cart_coupon'));
			add_action('wp_ajax_mwb_wpr_apply_fee_on_cart_subtotal',array($this,'mwb_wpr_apply_fee_on_cart_subtotal'));
			add_action('woocommerce_cart_calculate_fees',array($this,'mwb_wpr_woocommerce_cart_custom_points'));
			add_action('woocommerce_before_cart_contents',array($this,'mwb_wpr_woocommerce_before_cart_contents'));
			add_filter('woocommerce_cart_totals_fee_html',array($this,'mwb_wpr_woocommerce_cart_totals_fee_html'),10,2);
			add_action('wp_ajax_mwb_wpr_remove_cart_point',array($this,'mwb_wpr_remove_cart_point'));
			add_action( 'woocommerce_before_main_content',array($this,'mwb_wpr_woocommerce_before_main_content') );
			add_action( 'woocommerce_order_status_changed', array ($this, 'mwb_wpr_woocommerce_order_status_cancel' ), 10, 3 );
			add_filter('woocommerce_cart_totals_get_fees_from_cart_taxes', array ($this, 'mwb_wpr_fee_tax_calculation' ), 10, 3 );
			//MWB Custom Work
			add_filter( 'woocommerce_add_to_cart_validation', array($this, 'mwb_wpr_woocommerce_add_to_cart_validation' ),10, 3);
			add_filter( 'woocommerce_variable_price_html', array( $this, 'mwb_woocommerce_variable_price_html'),10,2);
			add_filter('wc_get_template',array($this,'mwb_overwrite_form_temp'),10,2);
			add_filter( 'woocommerce_cart_item_price',array($this, 'mwb_change_cart_table_price_display'), 30, 3 );
			

		}
		/**
		 * This function use to display the discount on the cart page.
		 * 
		 * @name mwb_wpr_woocommerce_hidden_order_itemmeta
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		*/	
		public function mwb_change_cart_table_price_display($price, $value, $cart_item_key) {

			$user_id = get_current_user_ID();
			$new_price = '';
			$today_date = date_i18n("Y-m-d");
			$user_level = get_user_meta($user_id,'membership_level',true);
			$mwb_wpr_mem_expr = get_user_meta($user_id,'membership_expiration',true);
			$get_points = (int)get_user_meta($user_id, 'mwb_wpr_points', true);
			$membership_settings_array = get_option('mwb_wpr_membership_settings',true);
			$mwb_wpr_membership_roles = isset($membership_settings_array['membership_roles']) && !empty($membership_settings_array['membership_roles']) ? $membership_settings_array['membership_roles'] : array();

			if(isset($value)) {

				$product_id = $value['product_id'];
				$pro_quant = $value['quantity'];
				$_product = wc_get_product( $product_id );
				$product_type = $_product->get_type();				
				$product_is_variable = $this->mwb_wpr_check_whether_product_is_variable($_product);

				$reg_price = $_product->get_price();
				if(isset($value['variation_id']) && !empty($value['variation_id'])) {
					$variation_id = $value['variation_id'];
					$variable_product = wc_get_product( $variation_id );
					$variable_price = $variable_product->get_price();
				}
				if( isset( $mwb_wpr_mem_expr ) && !empty( $mwb_wpr_mem_expr ) && $today_date <= $mwb_wpr_mem_expr ) {
					if( isset($user_level) && !empty($user_level) ) {
						foreach($mwb_wpr_membership_roles as $roles => $values) {	
							if($user_level == $roles) {	

								if(is_array($values['Product']) && !empty($values['Product'])) {
									if(in_array($product_id, $values['Product']) && !$this->check_exclude_sale_products($_product) ) {	
										if(!$product_is_variable) {
											$new_price = $reg_price - ($reg_price * $values['Discount'])/100;
											$price =$this->get_mwb_price_html($reg_price,$new_price,$values['Discount']);

										}
										elseif($product_is_variable) {
											$new_price = $variable_price - ($variable_price * $values['Discount'])/100;
											$price =$this->get_mwb_price_html($variable_price,$new_price,$values['Discount']);

										}
									}
								}
								else if(!$this->check_exclude_sale_products($_product)) {
									$terms = get_the_terms ( $product_id, 'product_cat' );
									if(is_array($terms) && !empty($terms)){
										foreach ( $terms as $term ) {
											$cat_id = $term->term_id;
											$parent_cat = $term->parent;
											if(in_array($cat_id, $values['Prod_Categ']) || in_array($parent_cat, $values['Prod_Categ'])){	
												if(!$product_is_variable){
													$new_price = $reg_price - ($reg_price * $values['Discount'])/100;
													$price =$this->get_mwb_price_html($reg_price,$new_price,$values['Discount']);

												}
												elseif($product_is_variable){
													$new_price = $variable_price - ($variable_price * $values['Discount'])/100;

													$price =$this->get_mwb_price_html($variable_price,$new_price,$values['Discount']);
													
												}
											}
										}
									}
								}	
							}
						}
					}
				}	
				
			   
			}
			return $price;
		}
		/**
		 * This function use to display the discount on the cart page.
		 * 
		 * @name mwb_wpr_woocommerce_hidden_order_itemmeta
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		*/	
		public function get_mwb_price_html($reg_price,$new_price,$discount) {

			$discount = $reg_price-$new_price;
			$Discount_tag = __('Discount  -',MWB_WPR_Domain);
			$price = '<span class="price"><del>'.wc_price($reg_price).'</del> <ins>'.wc_price($new_price).'</ins></span><p>'.$Discount_tag.wc_price($discount).'</p>';
			return $price;
		}
		/**
		 * This function is used to hide the order Item Meta
		 * 
		 * @name mwb_wpr_woocommerce_hidden_order_itemmeta
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		*/	
		public function mwb_wpr_woocommerce_hidden_order_itemmeta( $order_items ) {
			array_push($order_items, 'Purchasing Option');
			return $order_items;
		}

    	/**
		 * This function is used to establish the compatibility between PAR and GC
		 * 
		 * @name mwb_wpr_wgm_price_meta_data
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		*/	
    	public function mwb_wpr_wgm_price_meta_data($item_meta, $the_cart_data, $product_id, $variation_id){
    		if(isset($_REQUEST['quantity']) && $_REQUEST['quantity'] && $_REQUEST['quantity'] != null){
    			$quantity = (int)$_REQUEST['quantity'];
    		}
    		else{
    			$quantity = 1;
    		}
    		$user_id = get_current_user_ID();
    		$get_points = (int)get_user_meta($user_id,'mwb_wpr_points',true);
    		$product_types = wp_get_object_terms( $product_id, 'product_type' );
    		$check_enable = get_post_meta($product_id, 'mwb_product_points_enable', 'no');
    		$general_settings = get_option('mwb_wpr_settings_gallery',true);
    		$mwb_wpr_set_preferences = isset($general_settings['mwb_wpr_set_preferences']) ? $general_settings['mwb_wpr_set_preferences'] : 'to_both';
    		$enable_purchase_points = isset($general_settings['enable_purchase_points']) ? intval($general_settings['enable_purchase_points']) : 0;

    		if($check_enable == 'yes')
    		{	
    			if( $mwb_wpr_set_preferences == 'to_assign_point' || $mwb_wpr_set_preferences == 'to_both' )
    			{
    				if(isset($variation_id) && !empty($variation_id) && $variation_id > 0){
    					$get_product_points = get_post_meta($variation_id, 'mwb_wpr_variable_points', 1);
    					$item_meta['mwb_wpm_points'] = (int)$get_product_points*(int)$quantity;
    				}else{
    					$get_product_points = get_post_meta($product_id, 'mwb_points_product_value', 1);
    					$item_meta['mwb_wpm_points'] = (int)$get_product_points*(int)$quantity;
    				}
    			}

    		}	

    		if( $enable_purchase_points )
    		{
    			if(isset($_POST['mwb_wpr_pro_cost_to_points']) && !empty($_POST['mwb_wpr_pro_cost_to_points']) && $_POST['mwb_wpr_pro_cost_to_points'])
    			{	
    				if( $_POST['mwb_wpr_pro_cost_to_points'] > $get_points )
    				{
    					$item_meta['pro_purchase_by_points'] = $get_points;
    				}
    				else{
    					$item_meta['pro_purchase_by_points'] = $_POST['mwb_wpr_pro_cost_to_points'];
    				}
    			}
    		}
    		return $item_meta;

    	}

		/**
		 * This function is used to edit comment template for points
		 * 
		 * @name mwb_wpr_woocommerce_signup_point
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		*/
		public function mwb_wpr_woocommerce_signup_point()
		{
			$mwb_wpr_notification_color = get_option('mwb_wpr_notification_color','#55b3a5');
			$mwb_wpr_get_general_settings = get_option('mwb_wpr_settings_gallery',true);
			if(isset($mwb_wpr_get_general_settings['enable_mwb_signup']) && isset($mwb_wpr_get_general_settings['enable_mwb_signup'])==1)
			{
				$mwb_wpr_signup_value = isset($mwb_wpr_get_general_settings['mwb_signup_value']) ? intval($mwb_wpr_get_general_settings['mwb_signup_value']) : 1;
				$enable_mwb_signup = isset($mwb_wpr_get_general_settings['enable_mwb_signup']) ? intval($mwb_wpr_get_general_settings['enable_mwb_signup']) : 0;
				if($enable_mwb_signup)
				{
					?>
					<div class="woocommerce-message">
						<?php 
						echo  __( 'You will get ', MWB_WPR_Domain ) .$mwb_wpr_signup_value.__(' points for SignUp',MWB_WPR_Domain);
						?>
					</div>
					<?php
				}
			}
		}

		/**
		 * This function is used to edit comment template for points
		 * 
		 * @name mwb_wpr_woocommerce_comment_point
		 * @return array
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		*/
		public function mwb_wpr_woocommerce_comment_point($comment_data)
		{
			$mwb_wpr_get_general_settings = get_option('mwb_wpr_settings_gallery',true);
			$user_id = get_current_user_ID();
			if(isset($mwb_wpr_get_general_settings['enable_mwb_comment']) && $mwb_wpr_get_general_settings['enable_mwb_comment']==1 && isset($user_id) && !empty($user_id))
			{
				$mwb_wpr_comment_value = isset($mwb_wpr_get_general_settings['mwb_comment_value']) ? intval($mwb_wpr_get_general_settings['mwb_comment_value']) : 1;
				$comment_data['comment_field'].='<p class="comment-mwb-wpr-points-comment"><label>' . __( 'You will get ', MWB_WPR_Domain ) .$mwb_wpr_comment_value.__(' points for product review',MWB_WPR_Domain).'</p>';
			}
			return $comment_data;
		}

		/**
		 * This function is used to update cart points.
		 * 
		 * @name mwb_update_cart_points
		 * @return array
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		*/
		public function mwb_update_cart_points( $cart_updated )
		{

			if($cart_updated)
			{    
				$cart = WC()->session->get('cart');
				$user_id = get_current_user_ID();
				$get_points = (int)get_user_meta($user_id,'mwb_wpr_points',true);
				if(isset($_POST['cart']) && $_POST['cart'] != null && isset($cart) && $cart !=null)
				{
					$cart_update = sanitize_post($_POST['cart']);

					foreach ($cart_update as $key => $value)
					{
						if(isset(WC()->cart->cart_contents[$key]['product_meta']))
						{
							if(isset(WC()->cart->cart_contents[$key]['product_meta']['meta_data']['mwb_wpm_points']))
							{
								$product = wc_get_product($cart[$key]['product_id']);
								if(isset($product) && !empty($product)){
									if($this->mwb_wpr_check_whether_product_is_variable($product)){
										if( isset($cart[$key]['variation_id']) && !empty($cart[$key]['variation_id'])){
											$get_product_points = get_post_meta($cart[$key]['variation_id'], 'mwb_wpr_variable_points', 1);
										}
									}
									else{
										if(isset($cart[$key]['product_id']) && !empty($cart[$key]['product_id'])){
											$get_product_points = get_post_meta($cart[$key]['product_id'], 'mwb_points_product_value', 1);
										}
									}    
								}
								WC()->cart->cart_contents[$key]['product_meta']['meta_data']['mwb_wpm_points'] = (int)$get_product_points * (int)$value['qty'];
							}
                            //MWB CUSTOM CODE
							if(isset(WC()->cart->cart_contents[$key]['product_meta']['meta_data']['mwb_wpr_purchase_point_only'])) {

								$product = wc_get_product($cart[$key]['product_id']);
								if($this->mwb_wpr_check_whether_product_is_variable($product)){

									if(isset($cart[$key]['variation_id']) && !empty($cart[$key]['variation_id'])){

										$mwb_variable_purchase_value = get_post_meta($cart[$key]['variation_id'], 'mwb_wpr_variable_points_purchase',true);
									}
									$total_pro_pnt = (int)$mwb_variable_purchase_value * (int)$value['qty'];

									if(isset($total_pro_pnt) && !empty($total_pro_pnt) && $get_points >= $total_pro_pnt){
										WC()->cart->cart_contents[$key]['product_meta']['meta_data']['mwb_wpr_purchase_point_only'] = $total_pro_pnt;
									}
									else{

										wc_add_notice(__('You cant purchase that much quantity for Free',MWB_WPR_Domain),'error');
										WC()->cart->cart_contents[$key]['product_meta']['meta_data']['mwb_wpr_purchase_point_only'] = 0;
									}
								}
								else{

									if(isset($cart[$key]['product_id']) && !empty($cart[$key]['product_id'])){
										$get_product_points = get_post_meta($cart[$key]['product_id'], 'mwb_points_product_purchase_value', true);
									}
									$total_pro_pnt = (int)$get_product_points * (int)$value['qty'];
									if(isset($total_pro_pnt) && !empty($total_pro_pnt) && $get_points >= $total_pro_pnt){
										WC()->cart->cart_contents[$key]['product_meta']['meta_data']['mwb_wpr_purchase_point_only'] = (int)$get_product_points * (int)$value['qty'];
									}else{

										wc_add_notice(__('You cant purchase that much quantity for Free',MWB_WPR_Domain),'error');
										WC()->cart->cart_contents[$key]['product_meta']['meta_data']['mwb_wpr_purchase_point_only'] = 0;
									}
								}
							}
						}
					}                
				}                
			}
			return $cart_updated;
		}


		/**
		 * This function is used to save comment points.
		 * 
		 * @name mwb_comment_insert_into_database
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		*/
		public function mwb_comment_insert_into_database($comment_ID, $comment_approved, $commentdata)
		{	
			global $post;
			if($comment_approved)
			{	
				$general_settings = get_option('mwb_wpr_settings_gallery',true);
				$coupon_settings_array=get_option('mwb_wpr_coupons_gallery',array());
				$enable_mwb_comment = isset($general_settings['enable_mwb_comment']) ? intval($general_settings['enable_mwb_comment']) : 0;
				if($enable_mwb_comment )
				{
					$today_date = date_i18n("Y-m-d h:i:sa");
					$mwb_comment_value = isset($general_settings['mwb_comment_value']) ? intval($general_settings['mwb_comment_value']) : 1;
					$mwb_refer_value = isset($general_settings['mwb_refer_value']) ? intval($general_settings['mwb_refer_value']) : 1;
					$mwb_per_currency_spent_value = isset($coupon_settings_array['mwb_wpr_coupon_conversion_points']) ? intval($coupon_settings_array['mwb_wpr_coupon_conversion_points']) : 1;
					$get_points = (int)get_user_meta($commentdata['user_ID'], 'mwb_wpr_points', true);
					$get_detail_point = get_user_meta($commentdata['user_ID'], 'points_details', true);
					
					if(isset($get_detail_point['comment']) && !empty($get_detail_point['comment']) ){
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
					update_user_meta( $commentdata['user_ID'] , 'mwb_wpr_points' , $mwb_comment_value+$get_points );
					update_user_meta( $commentdata['user_ID'] , 'points_details' , $get_detail_point);
					$user=get_user_by('ID',$commentdata['user_ID']);
					$user_email=$user->user_email;
					$mwb_wpr_notificatin_array=get_option('mwb_wpr_notificatin_array',true);
					if(is_array($mwb_wpr_notificatin_array) && !empty($mwb_wpr_notificatin_array))
					{
						$total_points=$mwb_comment_value+$get_points;
						$mwb_wpr_notificatin_enable=isset($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) ? intval($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) : 0;
						$mwb_wpr_email_subject=isset($mwb_wpr_notificatin_array['mwb_wpr_comment_email_subject'])? $mwb_wpr_notificatin_array['mwb_wpr_comment_email_subject'] :'';
						$mwb_wpr_email_discription=isset($mwb_wpr_notificatin_array['mwb_wpr_comment_email_discription_custom_id']) ? $mwb_wpr_notificatin_array['mwb_wpr_comment_email_discription_custom_id'] :'';
						$mwb_wpr_email_discription=str_replace("[Points]",$mwb_comment_value,$mwb_wpr_email_discription);
						$mwb_wpr_email_discription=str_replace("[Total Points]",$total_points,$mwb_wpr_email_discription);
						$mwb_wpr_email_discription=str_replace("[Refer Points]",$mwb_refer_value,$mwb_wpr_email_discription);
						$mwb_wpr_email_discription=str_replace("[Per Currency Spent Points]",$mwb_per_currency_spent_value,$mwb_wpr_email_discription);
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
		 * This function is used to generate coupon of total points.
		 * 
		 * @name mwb_wpr_generate_original_coupon
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		*/
		public function mwb_wpr_generate_original_coupon()
		{
			check_ajax_referer( 'mwb-wpr-verify-nonce', 'mwb_nonce' );
			$response['result'] = false;
			$response['message'] = __("Coupon generation error.",MWB_WPR_Domain);
			if(isset($_POST['user_id']) && !empty($_POST['user_id']))
			{
				$user_id = sanitize_post($_POST['user_id']);
				$get_points = (int)get_user_meta($user_id, 'mwb_wpr_points', true);
				$coupon_settings = get_option('mwb_wpr_coupons_gallery',true);
				$mwb_coupon_length = (isset($coupon_settings['mwb_coupon_length']) && $coupon_settings['mwb_coupon_length'] != null) ? intval($coupon_settings['mwb_coupon_length']) : 5;
				$tot_points = (isset($get_points) && $get_points != null)? (int)$get_points: 0;
				if($tot_points)
				{
					$couponnumber = mwb_wpr_coupon_generator($mwb_coupon_length);
					
					$coupon_redeem_price = (isset($coupon_settings['coupon_redeem_price']) && $coupon_settings['coupon_redeem_price'] != null) ? $coupon_settings['coupon_redeem_price'] : 1;

					$coupon_redeem_points = (isset($coupon_settings['coupon_redeem_points']) && $coupon_settings['coupon_redeem_points'] != null) ? intval($coupon_settings['coupon_redeem_points']) : 1;

					$coupon_redeem_price = str_replace( wc_get_price_decimal_separator(), '.', strval( $coupon_redeem_price ) );

					$couponamont = ($get_points * $coupon_redeem_price)/$coupon_redeem_points;
					
					
					$couponamont = str_replace( '.',wc_get_price_decimal_separator(), strval( $couponamont ) );

					
					if($this->mwb_wpr_create_points_coupon($couponnumber, $couponamont, $user_id, $get_points))
					{
						$user_log = get_user_meta( $user_id, 'mwb_wpr_user_log', true);
						$response['html'] = '<table class="woocommerce-MyAccount-points shop_table shop_table_responsive my_account_points account-points-table">
						<thead>
							<tr>
								<th class="points-points">
									<span class="nobr">'.__( "Points", MWB_WPR_Domain ).'</span>
								</th>
								<th class="points-code">
									<span class="nobr">'.__( "Coupon Code", MWB_WPR_Domain ).'</span>
								</th>
								<th class="points-amount">
									<span class="nobr">'.__( "Coupon Amount", MWB_WPR_Domain ).'</span>
								</th>
								<th class="points-left">
									<span class="nobr">'.__( "Amount Left", MWB_WPR_Domain ).'</span>
								</th>
								<th class="points-expiry">
									<span class="nobr">'.__( "Expiry", MWB_WPR_Domain ).'</span>
								</th>
							</tr>
						</thead>
						<tbody>';

							foreach ( $user_log as $key => $mwb_user_log )
							{
								$response['html'].='<tr class="points">';
								foreach ( $mwb_user_log as $column_id => $column_name )
								{
									$response['html'].= '<td class="points-'.esc_attr( $column_id ).'" >';
									if($column_id == 'left')
									{
										$mwb_split = explode("#",$key);
										$column_name = get_post_meta( $mwb_split[1], 'coupon_amount',true);
										$response['html'].=get_woocommerce_currency_symbol().$column_name;
									}
									else
									{
										$response['html'].=$column_name;
									}
									$response['html'].='</td>';
								}
								$response['html'].='</tr>';
							}
							$response['html'].='</tbody>
						</table>';
						$response['result'] = true;
						$response['message'] = __('Your points are converted to coupon', MWB_WPR_Domain);
						$get_points = (int)get_user_meta($user_id, 'mwb_wpr_points', true);
						$response['points'] = $get_points;
					}
				}
			}
			echo json_encode($response);
			wp_die();
		}

		/**
		 * This function is used to generate coupon for custom points.
		 * 
		 * @name mwb_wpr_generate_custom_coupon
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		*/
		public function mwb_wpr_generate_custom_coupon()
		{
			check_ajax_referer( 'mwb-wpr-verify-nonce', 'mwb_nonce' );
			$response['result'] = false;
			$response['message'] = __("Coupon generation error.",MWB_WPR_Domain);
			if(isset($_POST['points']) && !empty($_POST['points']))
			{
				$user_id = sanitize_post($_POST['user_id']);
				$get_points = (int)get_user_meta($user_id, 'mwb_wpr_points', true);
				$custom_points = sanitize_post($_POST['points']);
				
				$coupon_settings = get_option('mwb_wpr_coupons_gallery',true);
				$mwb_coupon_length = (isset($coupon_settings['mwb_coupon_length']) && $coupon_settings['mwb_coupon_length'] != null) ? intval($coupon_settings['mwb_coupon_length']) : 5;
				
				if( $custom_points <= $get_points )
				{

					$couponnumber = mwb_wpr_coupon_generator($mwb_coupon_length);

					$coupon_redeem_price = (isset($coupon_settings['coupon_redeem_price']) && $coupon_settings['coupon_redeem_price'] != null) ? $coupon_settings['coupon_redeem_price'] : 1;

					$coupon_redeem_points = (isset($coupon_settings['coupon_redeem_points']) && $coupon_settings['coupon_redeem_points'] != null) ? intval($coupon_settings['coupon_redeem_points']) : 1;

					$coupon_redeem_price = str_replace( wc_get_price_decimal_separator(), '.', strval( $coupon_redeem_price ) );

					$couponamont = ($custom_points * $coupon_redeem_price)/$coupon_redeem_points;
					
					
					$couponamont = str_replace( '.',wc_get_price_decimal_separator(), strval( $couponamont ) );
					if($this->mwb_wpr_create_points_coupon($couponnumber, $couponamont, $user_id, $custom_points))
					{
						$user_log = get_user_meta( $user_id, 'mwb_wpr_user_log', true);
						$response['html'] = '<table class="woocommerce-MyAccount-points shop_table shop_table_responsive my_account_points account-points-table">
						<thead>
							<tr>
								<th class="points-points">
									<span class="nobr">'.__( "Points", MWB_WPR_Domain ).'</span>
								</th>
								<th class="points-code">
									<span class="nobr">'.__( "Coupon Code", MWB_WPR_Domain ).'</span>
								</th>
								<th class="points-amount">
									<span class="nobr">'.__( "Coupon Amount", MWB_WPR_Domain ).'</span>
								</th>
								<th class="points-left">
									<span class="nobr">'.__( "Amount Left", MWB_WPR_Domain ).'</span>
								</th>
								<th class="points-expiry">
									<span class="nobr">'.__( "Expiry", MWB_WPR_Domain ).'</span>
								</th>
							</tr>
						</thead>
						<tbody>';		
							foreach ( $user_log as $key => $mwb_user_log )
							{
								$response['html'].='<tr class="points">';
								foreach ( $mwb_user_log as $column_id => $column_name )
								{
									$response['html'].= '<td class="points-'.esc_attr( $column_id ).'" >';
									if($column_id == 'left')
									{
										$mwb_split = explode("#",$key);
										$column_name = get_post_meta( $mwb_split[1], 'coupon_amount',true);
										$response['html'].=get_woocommerce_currency_symbol().$column_name;
									}
									else
									{
										$response['html'].=$column_name;
									}
									$response['html'].='</td>';
								}
								$response['html'].='</tr>';
							}
							$response['html'].='</tbody>
						</table>';
						$response['result'] = true;
						$response['message'] = __('Your points are converted to coupon', MWB_WPR_Domain);
						$get_points = (int)get_user_meta($user_id, 'mwb_wpr_points', true);
						$response['points'] = $get_points;
					}
				}
				else
				{
					$response['result'] = false;
					$response['message'] = __('Points cannot be greater than your current points', MWB_WPR_Domain);
				}
			}
			echo json_encode($response);
			wp_die();
		}

		/**
		 * This function is used to enque scripts.
		 * 
		 * @name mwb_wpr_wp_enqueue_scripts
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		*/
		public function mwb_wpr_wp_enqueue_scripts()
		{
			$coupon_settings = get_option('mwb_wpr_coupons_gallery',array());
			$mwb_minimum_points_value = isset($coupon_settings['mwb_minimum_points_value']) ? $coupon_settings['mwb_minimum_points_value'] : 50;
			$mwb_wpr_cart_points_rate = get_option("mwb_wpr_cart_points_rate",1);
			$mwb_wpr_cart_price_rate = get_option("mwb_wpr_cart_price_rate",1);
			$mwb_wpr_make_readonly = get_option("mwb_wpr_make_readonly",0);
			$mwb_wpr = array(
				'ajaxurl' => admin_url('admin-ajax.php'),
				'message' => __('Please enter valid points',MWB_WPR_Domain),
				'minimum_points'=>$mwb_minimum_points_value,
				'confirmation_msg'=>__('Do you really want to upgrade your user level as this process will deduct the required points from your account?',MWB_WPR_Domain),
				'minimum_points_text'=>__('Minimum Points Require To Convert Points To Coupon is ',MWB_WPR_Domain).$mwb_minimum_points_value,
				'mwb_wpr_custom_notice'=>__('Number of Point you had entered will get deducted from your Account',MWB_WPR_Domain),
				'mwb_wpr_nonce' =>  wp_create_nonce( "mwb-wpr-verify-nonce" ),
				'mwb_wpr_cart_points_rate' => $mwb_wpr_cart_points_rate,
				'mwb_wpr_cart_price_rate' => $mwb_wpr_cart_price_rate,
				'make_readonly' => $mwb_wpr_make_readonly,
				'not_allowed' => __('Please enter some valid points!',MWB_WPR_Domain),
				);
			wp_register_script("mwb_wpr_clipboard", MWB_WPR_URL."assets/js/dist/clipboard.min.js", array('jquery'));
			wp_localize_script('mwb_wpr_clipboard', 'mwb_wpr', $mwb_wpr );
			wp_enqueue_script('mwb_wpr_clipboard' );
			wp_register_script("mwb_wpr_points_acounts", MWB_WPR_JS_LOAD_PUBLIC, array('jquery'));
			wp_localize_script('mwb_wpr_points_acounts', 'mwb_wpr', $mwb_wpr );
			wp_enqueue_script('mwb_wpr_points_acounts' );
			wp_enqueue_style('mwb_wpr_front_end_css', MWB_WPR_URL."assets/css/public/woocommerce-ultimate-points-public.css" );
			wp_enqueue_style('mwb_wpr_front_end_css', MWB_WPR_URL."assets/css/public/font-awesome.css" );
		}

		/**
		 * This function is used to maintain coupon value of latest version of woocommerce.
		 * 
		 * @name mwb_wpr_woocommerce_order_add_coupon_woo_latest_version
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		*/
		public function mwb_wpr_woocommerce_order_add_coupon_woo_latest_version($item_id,$item)
		{
			if(get_class($item)=='WC_Order_Item_Coupon')
			{
				$mwp_wpr_coupon_code=$item->get_code();
				$the_coupon = new WC_Coupon($mwp_wpr_coupon_code);
				if(isset($the_coupon))
				{
					$mwp_wpr_discount_amount=$item->get_discount();
					$mwp_wpr_discount_amount_tax=$item->get_discount_tax();
					$mwp_wpr_coupon_id = $the_coupon->get_id();
					$pointscoupon = get_post_meta( $mwp_wpr_coupon_id, 'mwb_wpr_points_coupon', true );
					if(!empty($pointscoupon))
					{
						$amount = get_post_meta( $mwp_wpr_coupon_id, 'coupon_amount', true );
						$total_discount =$mwp_wpr_discount_amount+$mwp_wpr_discount_amount_tax;
						if( $amount < $total_discount )
						{
							$remaining_amount = 0;
						}
						else
						{
							$remaining_amount = $amount - $total_discount;
						}				
						update_post_meta( $mwp_wpr_coupon_id, 'coupon_amount', $remaining_amount );
					}
				}
			}
		}

		/**
		 * This function is used to maintain coupon according to woocommerce previous version.
		 * 
		 * @name mwb_wpr_woocommerce_order_add_coupon
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		*/
		public function mwb_wpr_woocommerce_order_add_coupon( $order_id, $item_id, $coupon_code, $discount_amount, $discount_amount_tax )
		{
			$the_coupon = new WC_Coupon( $coupon_code );
			if(isset($the_coupon->id))
			{
				$coupon_id = $the_coupon->id;
				$pointscoupon = get_post_meta( $coupon_id, 'mwb_wpr_points_coupon', true );
				if( !empty($pointscoupon) )
				{
					$amount = get_post_meta( $coupon_id, 'coupon_amount', true );
					$total_discount = $discount_amount+$discount_amount_tax;
					if( $amount < $total_discount )
					{
						$remaining_amount = 0;
					}
					else
					{
						$remaining_amount = $amount - $total_discount;
					}				
					update_post_meta( $coupon_id, 'coupon_amount', $remaining_amount );
				}
			}
		}

		/**
		 * This function is used to generate coupon according to points.
		 * 
		 * @name mwb_wpr_create_points_coupon
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		*/
		public function mwb_wpr_create_points_coupon($couponnumber, $couponamont, $user_id, $points){
			
			$coupon_code = $couponnumber; // Code
			$amount = $couponamont; // Amount
			$discount_type = 'fixed_cart'; 
			$coupon_description =  __("Points And Reward - User ID#",MWB_WPR_Domain).$user_id;

			$coupon = array(
				'post_title' => $coupon_code,
				'post_content' => $coupon_description,
				'post_excerpt' => $coupon_description,
				'post_status' => 'publish',
				'post_author' => $user_id,
				'post_type'		=> 'shop_coupon'
				);

			$new_coupon_id = wp_insert_post( $coupon );
			$coupon_settings = get_option('mwb_wpr_coupons_gallery',true);
			$individual_use = isset($coupon_settings['enable_coupon_individual']) ? intval($coupon_settings['enable_coupon_individual']) : 0;
			if($individual_use)
			{
				$individual_use = "yes";
			}
			else
			{
				$individual_use = "no";
			}

			$free_shipping = isset($coupon_settings['enable_coupon_free_shipping']) ? intval($coupon_settings['enable_coupon_free_shipping']) : 0;
			if($free_shipping)
			{
				$free_shipping = "yes";
			}
			else
			{
				$free_shipping = "no";
			}

			$mwb_coupon_length = (isset($coupon_settings['mwb_coupon_length']) && $coupon_settings['mwb_coupon_length'] != null) ? intval($coupon_settings['mwb_coupon_length']) : 5;

			$expiry_date = (isset($coupon_settings['coupon_expiry']) && $coupon_settings['coupon_expiry'] != null) ? intval($coupon_settings['coupon_expiry']) : 1;

			$minimum_amount = (isset($coupon_settings['coupon_minspend']) && $coupon_settings['coupon_minspend'] != null) ? intval($coupon_settings['coupon_minspend']) : "";

			$maximum_amount = (isset($coupon_settings['coupon_maxspend']) && $coupon_settings['coupon_maxspend'] != null) ? intval($coupon_settings['coupon_maxspend']) : "";

			$usage_limit = (isset($coupon_settings['coupon_use']) && $coupon_settings['coupon_use'] != null) ? intval($coupon_settings['coupon_use']) : 0;
			$todaydate = date_i18n("Y-m-d");

			if($expiry_date > 0 ){
				$expirydate = date_i18n( "Y-m-d", strtotime( "$todaydate +$expiry_date day" ) );
			}
			else{
				$expirydate = "";
			}
			$user = get_user_by('ID',$user_id);
			$user_email=$user->user_email;
			update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
			update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
			update_post_meta( $new_coupon_id, 'individual_use', $individual_use );
			update_post_meta( $new_coupon_id, 'usage_limit', $usage_limit );
			update_post_meta( $new_coupon_id, 'expiry_date', $expirydate );
			update_post_meta( $new_coupon_id, 'free_shipping', $free_shipping );
			update_post_meta( $new_coupon_id, 'minimum_amount', $minimum_amount );
			update_post_meta( $new_coupon_id, 'maximum_amount', $maximum_amount );
			update_post_meta( $new_coupon_id, 'customer_email', $user_email);
			update_post_meta( $new_coupon_id, 'mwb_wpr_points_coupon', $user_id );
			if( empty($expirydate ) ){
				$expirydate = __('No Expiry', MWB_WPR_Domain);
			}

			$get_points = (int)get_user_meta($user_id, 'mwb_wpr_points', true);

			$available_points = $get_points - $points;
			$coupon_point_detail = get_user_meta($user_id, 'points_details',true);

			$today_date = date_i18n("Y-m-d h:i:sa");

			if(isset($coupon_point_detail['Coupon_details']) && !empty($coupon_point_detail['Coupon_details'])){
				$coupon_array = array(
					'Coupon_details'=>$points,
					'date'=>$today_date);
				$coupon_point_detail['Coupon_details'][] = $coupon_array;
			}
			else{
				if(!is_array($coupon_point_detail)){
					$coupon_point_detail = array();
				}
				$coupon_array = array(
					'Coupon_details'=>$points,
					'date'=>$today_date);
				$coupon_point_detail['Coupon_details'][] = $coupon_array;
			}
			update_user_meta( $user_id , 'mwb_wpr_points' , $available_points );
			update_user_meta( $user_id, 'points_details', $coupon_point_detail );
			$user_log = get_user_meta( $user_id, 'mwb_wpr_user_log',true);
			if(empty($user_log)){
				$user_log = array();
				$user_log['mwb_wpr_'.$coupon_code."#".$new_coupon_id] = array(
					'points'=> $points,
					'code'=> $coupon_code,
					'camount' => get_woocommerce_currency_symbol().$amount,
					'left'=> get_woocommerce_currency_symbol().$amount,
					'expiry' => $expirydate);
			}
			else{
				$user_log['mwb_wpr_'.$coupon_code."#".$new_coupon_id] = array(
					'points'=> $points,
					'code'=> $coupon_code,
					'camount' => get_woocommerce_currency_symbol().$amount,
					'left'=> get_woocommerce_currency_symbol().$amount,
					'expiry' => $expirydate
					);
			}
			update_user_meta( $user_id ,'mwb_wpr_user_log', $user_log );
			return true;
		}

		/**
		 * This function is used to cunstruct Points Tab in MY ACCOUNT Page.
		 * 
		 * @name mwb_wpr_add_my_account_endpoint
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		*/
		function mwb_wpr_add_my_account_endpoint(){
			add_rewrite_endpoint( 'points', EP_PAGES );
			add_rewrite_endpoint( 'view-log', EP_PAGES );
		}
		
		/**
		 * This function is used to get user_id to get points in MY ACCOUNT Page Points Tab.
		 * 
		 * @name mwb_wpr_account_points
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		*/
		public function mwb_wpr_account_points(){
			$user_ID = get_current_user_ID();
			$user = new WP_User( $user_ID );
			
			if(in_array('subscriber', $user->roles) || in_array('customer', $user->roles)){

				require plugin_dir_path( __FILE__ ) . 'mwb_wpr_points_template.php';
			}
		}

		/**
		 * This function is used to include the working of View_point_log
		 * 
		 * @name mwb_wpr_account_viewlog
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		*/
		public function mwb_wpr_account_viewlog(){
			$user_ID = get_current_user_ID();
			$user = new WP_User( $user_ID );
			
			if(in_array('subscriber', $user->roles) || in_array('customer', $user->roles)){

				require plugin_dir_path( __FILE__ ) . 'mwb_wpr_points_log_template.php';
			}
		}

		/**
		 * This function is used to set User Role to see Points Tab in MY ACCOUNT Page.
		 * 
		 * @name mwb_wpr_points_dashboard
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		*/
		public function mwb_wpr_points_dashboard($items){	
			$user_ID = get_current_user_ID();
			$user = new WP_User( $user_ID );
			$general_settings = get_option('mwb_wpr_settings_gallery',true);
			$mwb_wpr_points_tab_text = isset($general_settings['mwb_wpr_points_tab_text']) ? $general_settings['mwb_wpr_points_tab_text'] : __( 'Points', MWB_WPR_Domain );
			if(empty($mwb_wpr_points_tab_text)){
				$mwb_wpr_points_tab_text = __( 'Points', MWB_WPR_Domain );
			}
			if(in_array('subscriber', $user->roles) || in_array('customer', $user->roles)){

				$logout = $items['customer-logout'];
				unset( $items['customer-logout'] );
				$items['points'] = $mwb_wpr_points_tab_text;
				$items['customer-logout'] = $logout;
			}
			return $items;
		}

		/**
		 * This function is used to give product points to user if order status of Product is complete and processing.
		 * 
		 * @name mwb_wpr_woocommerce_order_status_changed
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		*/
		public function mwb_wpr_woocommerce_order_status_changed($order_id, $old_status, $new_status){
			if($old_status != $new_status)
			{	
				$mwb_wpr_one_email = false;
				$points_key_priority_high = false;
				$item_points = 0;
				$today_date = date_i18n("Y-m-d h:i:sa");
				$general_settings = get_option('mwb_wpr_settings_gallery',true);
				$coupon_settings_array=get_option('mwb_wpr_coupons_gallery',array());
				$mwb_wpr_coupon_conversion_enable = $this->is_order_conversion_enabled();
				$mwb_comment_value = isset($general_settings['mwb_comment_value']) ? intval($general_settings['mwb_comment_value']) : 1;
				$mwb_refer_value = isset($general_settings['mwb_refer_value']) ? intval($general_settings['mwb_refer_value']) : 1;
				$mwb_per_currency_spent_value = isset($coupon_settings_array['mwb_wpr_coupon_conversion_points']) ? intval($coupon_settings_array['mwb_wpr_coupon_conversion_points']) : 1;
				$mwb_wpr_set_preferences = isset($general_settings['mwb_wpr_set_preferences']) ? $general_settings['mwb_wpr_set_preferences'] : 'to_both';
				$mwb_referral_purchase_limit = isset($general_settings['mwb_referral_purchase_limit']) ? intval($general_settings['mwb_referral_purchase_limit']) : 0;
				$mwb_wpr_general_referal_order_limit = isset($general_settings['mwb_wpr_general_referal_order_limit']) ? intval($general_settings['mwb_wpr_general_referal_order_limit']) : 1;
				$mwb_wpr_notificatin_array=get_option('mwb_wpr_notificatin_array',true);
				if($new_status == 'completed')
				{

					$order = wc_get_order( $order_id );
					$user_id = absint( $order->get_user_id() );
					$user = get_user_by('ID',$user_id);
					$user_email=$user->user_email;
					if(isset($user_id) && !empty($user_id))
					{

						$mwb_wpr_ref_noof_order = (int)get_user_meta($user_id,'mwb_wpr_no_of_orders',true);
						if(isset($mwb_wpr_ref_noof_order) && !empty($mwb_wpr_ref_noof_order))
						{
							$mwb_wpr_ref_noof_order += 1;
							update_user_meta($user_id,'mwb_wpr_no_of_orders',$mwb_wpr_ref_noof_order);
						}
						else
						{
							update_user_meta($user_id,'mwb_wpr_no_of_orders',1);
						}
					}
					foreach( $order->get_items() as $item_id => $item )
					{
						$woo_ver = WC()->version;
						if($woo_ver < "3.0.0")
						{
							if(isset($item['item_meta']['Points']) && !empty($item['item_meta']['Points']))
							{
								$itempointsset = get_post_meta($order_id, "$order_id#$item_id#set", true);
								if($itempointsset == "set")
								{
									continue;
								}
								
								$item_points += (int)$item['item_meta']['Points'][0];
								$product = $order->get_product_from_item( $item );
								
								$product_id = $product->id;
								$check_enable = get_post_meta($product_id, 'mwb_product_points_enable', 'no');

								if( $mwb_wpr_set_preferences == 'to_assign_point' || $mwb_wpr_set_preferences == 'to_both' )
								{
									$mwb_wpr_one_email = true;
								}
								if($mwb_wpr_coupon_conversion_enable){
									$points_key_priority_high = true;
								}							
							}
							$mwb_referral_purchase_enable = isset($general_settings['mwb_referral_purchase_enable']) ? intval($general_settings['mwb_referral_purchase_enable']) : 0;
							$mwb_referral_purchase_value = isset($general_settings['mwb_referral_purchase_value']) ? intval($general_settings['mwb_referral_purchase_value']) : 1;
							if($mwb_referral_purchase_enable)
							{		
								$user_id =  $order->get_user_id();
								$mwb_wpr_ref_noof_order = get_user_meta($user_id,'mwb_wpr_no_of_orders',true);
								$refer_id = get_user_meta($user_id,'user_visit_through_link',true);
								$refer_user=get_user_by('ID',$refer_id);
								$refer_user_email=$refer_user->user_email;

								if($mwb_referral_purchase_limit == 0)
								{
									if(isset($refer_id) && !empty($refer_id)){
										$prev_points_of_ref_userid = (int)get_user_meta($refer_id , 'mwb_wpr_points', true);
										
										$update_points = $prev_points_of_ref_userid + $mwb_referral_purchase_value;
										$ref_product_detail_points = get_user_meta($refer_id, 'points_details',true);
										
										if(isset($ref_product_detail_points['ref_product_detail']) && !empty($ref_product_detail_points['ref_product_detail']))
										{		
											$ref_pro_array = array(
												'ref_product_detail'=>$mwb_referral_purchase_value,
												'date'=>$today_date,
												'refered_user'=>$user_id
												);
											$ref_product_detail_points['ref_product_detail'][] = $ref_pro_array;
										}			
										else
										{
											if(!is_array($ref_product_detail_points)){
												$ref_product_detail_points = array();
											}	
											$ref_pro_array = array(
												'ref_product_detail'=>$mwb_referral_purchase_value,
												'date'=>$today_date,
												'refered_user'=>$user_id
												);
											$ref_product_detail_points['ref_product_detail'][] = $ref_pro_array;
										}
										update_user_meta( $refer_id , 'points_details' , $ref_product_detail_points );
										update_user_meta( $refer_id  , 'mwb_wpr_points' , $update_points );
										if(is_array($mwb_wpr_notificatin_array) && !empty($mwb_wpr_notificatin_array))
										{
											$mwb_wpr_notificatin_enable=isset($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) ? intval($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) : 0;
											$mwb_wpr_email_subject=isset($mwb_wpr_notificatin_array['mwb_wpr_referral_purchase_email_subject'])? $mwb_wpr_notificatin_array['mwb_wpr_referral_purchase_email_subject'] :'';
											$mwb_wpr_email_discription=isset($mwb_wpr_notificatin_array['mwb_wpr_referral_purchase_email_discription_custom_id']) ? $mwb_wpr_notificatin_array['mwb_wpr_referral_purchase_email_discription_custom_id'] :'';
											$mwb_wpr_email_discription=str_replace("[Points]",$mwb_referral_purchase_value,$mwb_wpr_email_discription);
											$mwb_wpr_email_discription=str_replace("[Total Points]",$update_points,$mwb_wpr_email_discription);
											$mwb_wpr_email_discription=str_replace("[Refer Points]",$mwb_refer_value,$mwb_wpr_email_discription);
											$mwb_wpr_email_discription=str_replace("[Comment Points]",$mwb_comment_value,$mwb_wpr_email_discription);
											$mwb_wpr_email_discription=str_replace("[Per Currency Spent Points]",$mwb_per_currency_spent_value,$mwb_wpr_email_discription);
											$user = get_user_by('email',$refer_user_email);
											$user_name = $user->user_firstname;
											$mwb_wpr_email_discription = str_replace("[USERNAME]",$user_name,$mwb_wpr_email_discription);
											if($mwb_wpr_notificatin_enable)
											{	
												$headers = array('Content-Type: text/html; charset=UTF-8');
												wc_mail($refer_user_email,$mwb_wpr_email_subject,$mwb_wpr_email_discription,$headers);
											}
										}
									}
								}
								else
								{
									if($mwb_wpr_ref_noof_order <= $mwb_wpr_general_referal_order_limit)
									{
										if(isset($refer_id) && !empty($refer_id)){
											$prev_points_of_ref_userid = (int)get_user_meta($refer_id , 'mwb_wpr_points', true);
											
											$update_points = $prev_points_of_ref_userid + $mwb_referral_purchase_value;
											$ref_product_detail_points = get_user_meta($refer_id, 'points_details',true);
											
											if(isset($ref_product_detail_points['ref_product_detail']) && !empty($ref_product_detail_points['ref_product_detail']))
											{		
												$ref_pro_array = array(
													'ref_product_detail'=>$mwb_referral_purchase_value,
													'date'=>$today_date
													);
												$ref_product_detail_points['ref_product_detail'][] = $ref_pro_array;
											}													
											else
											{
												if(!is_array($ref_product_detail_points)){
													$ref_product_detail_points = array();
												}	
												$ref_pro_array = array(
													'ref_product_detail'=>$mwb_referral_purchase_value,
													'date'=>$today_date
													);
												$ref_product_detail_points['ref_product_detail'][] = $ref_pro_array;
											}
											
											update_user_meta( $refer_id , 'points_details' , $ref_product_detail_points );
											update_user_meta( $refer_id  , 'mwb_wpr_points' , $update_points );
											if(is_array($mwb_wpr_notificatin_array) && !empty($mwb_wpr_notificatin_array))
											{
												$mwb_wpr_notificatin_enable=isset($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) ? intval($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) : 0;
												$mwb_wpr_email_subject=isset($mwb_wpr_notificatin_array['mwb_wpr_referral_purchase_email_subject'])? $mwb_wpr_notificatin_array['mwb_wpr_referral_purchase_email_subject'] :'';
												$mwb_wpr_email_discription=isset($mwb_wpr_notificatin_array['mwb_wpr_referral_purchase_email_discription_custom_id']) ? $mwb_wpr_notificatin_array['mwb_wpr_referral_purchase_email_discription_custom_id'] :'';
												$mwb_wpr_email_discription=str_replace("[Points]",$mwb_referral_purchase_value,$mwb_wpr_email_discription);
												$mwb_wpr_email_discription=str_replace("[Total Points]",$update_points,$mwb_wpr_email_discription);
												$mwb_wpr_email_discription=str_replace("[Refer Points]",$mwb_refer_value,$mwb_wpr_email_discription);
												$mwb_wpr_email_discription=str_replace("[Comment Points]",$mwb_comment_value,$mwb_wpr_email_discription);
												$mwb_wpr_email_discription=str_replace("[Per Currency Spent Points]",$mwb_per_currency_spent_value,$mwb_wpr_email_discription);
												$user = get_user_by('email',$refer_user_email);
												$user_name = $user->user_firstname;
												$mwb_wpr_email_discription = str_replace("[USERNAME]",$user_name,$mwb_wpr_email_discription);
												if($mwb_wpr_notificatin_enable)
												{	
													$headers = array('Content-Type: text/html; charset=UTF-8');
													wc_mail($refer_user_email,$mwb_wpr_email_subject,$mwb_wpr_email_discription,$headers);
												}
											}
										}
									}
								}
							}
						}
						else
						{	
							$mwb_wpr_items=$item->get_meta_data();
							foreach ($mwb_wpr_items as $key => $mwb_wpr_value) 
							{
								if( $mwb_wpr_set_preferences == 'to_assign_point' || $mwb_wpr_set_preferences == 'to_both' )
								{
									if(isset($mwb_wpr_value->key) && !empty($mwb_wpr_value->key) && ($mwb_wpr_value->key=='Points') )
									{								
										
										$itempointsset = get_post_meta($order_id, "$order_id#$mwb_wpr_value->id#set", true);
										if($itempointsset == "set")
										{
											continue;
										}
										$item_points += (int)$mwb_wpr_value->value;
										$mwb_wpr_one_email = true;
										$product_id = $item->get_product_id();
										$check_enable = get_post_meta($product_id, 'mwb_product_points_enable', 'no');
										if($check_enable == 'yes'){
											update_post_meta($order_id, "$order_id#$mwb_wpr_value->id#set", "set");
										}
										if($mwb_wpr_coupon_conversion_enable){
											$points_key_priority_high = true;
										}
									}
								}
							}
						}
					}
					if($mwb_wpr_one_email && $check_enable == 'yes' && isset($item_points) && !empty($item_points))
					{
						$get_product_points = get_post_meta($product_id, 'mwb_points_product_value', 1);
						$user_id = absint( $order->get_user_id() );
						if (!empty($user_id)) 
						{
							$user=get_user_by('ID',$user_id);
							$user_email=$user->user_email;
							$get_points = (int)get_user_meta($user_id , 'mwb_wpr_points', true);
							$product_detail_points = get_user_meta($user_id, 'points_details',true);
							if(isset($product_detail_points['product_details']) && !empty($product_detail_points['product_details'])){
								
								$pro_array = array(
									'product_details'=>$item_points,
									'date'=>$today_date
									);
								$product_detail_points['product_details'][] = $pro_array;
							}
							else{
								if(!is_array($product_detail_points)){
									$product_detail_points = array();
								}
								$pro_array = array(
									'product_details'=>$item_points,
									'date'=>$today_date
									);
								$product_detail_points['product_details'][] = $pro_array;
							}
							$total_points = $get_points + $item_points;
							update_user_meta( $user_id  , 'mwb_wpr_points' , $total_points );
							update_user_meta( $user_id,'points_details', $product_detail_points );
							if(is_array($mwb_wpr_notificatin_array) && !empty($mwb_wpr_notificatin_array))
							{
								$mwb_wpr_notificatin_enable=isset($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) ? intval($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) : 0;
								$mwb_wpr_email_subject=isset($mwb_wpr_notificatin_array['mwb_wpr_product_email_subject'])? $mwb_wpr_notificatin_array['mwb_wpr_product_email_subject'] :'';
								$mwb_wpr_email_discription=isset($mwb_wpr_notificatin_array['mwb_wpr_product_email_discription_custom_id']) ? $mwb_wpr_notificatin_array['mwb_wpr_product_email_discription_custom_id'] :'';
								$mwb_wpr_email_discription=str_replace("[Points]",$item_points,$mwb_wpr_email_discription);
								$mwb_wpr_email_discription=str_replace("[Total Points]",$total_points,$mwb_wpr_email_discription);
								$mwb_wpr_email_discription=str_replace("[Comment Points]",$mwb_comment_value,$mwb_wpr_email_discription);
								$mwb_wpr_email_discription=str_replace("[Refer Points]",$mwb_refer_value,$mwb_wpr_email_discription);
								$mwb_wpr_email_discription=str_replace("[Per Currency Spent Points]",$mwb_per_currency_spent_value,$mwb_wpr_email_discription);
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
					$general_settings = get_option('mwb_wpr_settings_gallery',true);
					$mwb_referral_purchase_value = isset($general_settings['mwb_referral_purchase_value']) ? intval($general_settings['mwb_referral_purchase_value']) : 1;
					$order_total=$order->get_total();
					$order_total = str_replace( wc_get_price_decimal_separator(), '.', strval( $order_total ) );
					if($mwb_wpr_coupon_conversion_enable)
					{
						if( $mwb_wpr_set_preferences == 'to_per_currency' || $mwb_wpr_set_preferences == 'to_both' || !$points_key_priority_high)
						{
							$item_conversion_id_set = get_post_meta($order_id, "$order_id#item_conversion_id", true);
							if($item_conversion_id_set != 'set')
							{
								$mwb_wpr_coupon_conversion_price=$coupon_settings_array['mwb_wpr_coupon_conversion_price'];
								$mwb_wpr_coupon_conversion_points=$coupon_settings_array['mwb_wpr_coupon_conversion_points'];
								$mwb_comment_value = isset($general_settings['mwb_comment_value']) ? intval($general_settings['mwb_comment_value']) : 1;
								$mwb_refer_value = isset($general_settings['mwb_refer_value']) ? intval($general_settings['mwb_refer_value']) : 1;
								$user_id = $order->get_user_id();
								$get_points = (int)get_user_meta($user_id , 'mwb_wpr_points', true);
								$pro_conversion_points = get_user_meta($user_id, 'points_details',true);
								$today_date = date_i18n("Y-m-d h:i:sa");
								$user=get_user_by('ID',$user_id);
								$user_email=$user->user_email;
								// $points_calculation =ceil(($order_total*$mwb_wpr_coupon_conversion_points)/$mwb_wpr_coupon_conversion_price);
								$points_calculation =$this->get_points_calculation($user_id,$order_total);
								$total_points=intval($points_calculation+$get_points);
								if(isset($pro_conversion_points['pro_conversion_points']) && !empty($pro_conversion_points['pro_conversion_points'])){
									$pro_array = array();
									$pro_array = array(
										'pro_conversion_points'=>$points_calculation,
										'date'=>$today_date);
									$pro_conversion_points['pro_conversion_points'][] = $pro_array;
								}
								else{
									if(!is_array($pro_conversion_points)){
										$pro_conversion_points = array();
									}
									$pro_array = array(
										'pro_conversion_points'=>$points_calculation,
										'date'=>$today_date);
									$pro_conversion_points['pro_conversion_points'][] = $pro_array;
								}
								update_user_meta( $user_id  , 'mwb_wpr_points' , $total_points );

								update_user_meta( $user_id,'points_details', $pro_conversion_points );
								update_post_meta($order_id, "$order_id#item_conversion_id", "set");
								if(is_array($mwb_wpr_notificatin_array) && !empty($mwb_wpr_notificatin_array))
								{
									$mwb_wpr_notificatin_enable=isset($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) ? intval($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) : 0;
									$mwb_wpr_email_subject=isset($mwb_wpr_notificatin_array['mwb_wpr_amount_email_subject'])? $mwb_wpr_notificatin_array['mwb_wpr_amount_email_subject'] :'';
									$mwb_wpr_email_discription=isset($mwb_wpr_notificatin_array['mwb_wpr_amount_email_discription_custom_id']) ? $mwb_wpr_notificatin_array['mwb_wpr_amount_email_discription_custom_id'] :'';
									$mwb_wpr_email_discription=str_replace("[Points]",$points_calculation,$mwb_wpr_email_discription);
									$mwb_wpr_email_discription=str_replace("[Total Points]",$total_points,$mwb_wpr_email_discription);
									$mwb_wpr_email_discription=str_replace("[Refer Points]",$mwb_refer_value,$mwb_wpr_email_discription);
									$mwb_wpr_email_discription=str_replace("[Comment Points]",$mwb_comment_value,$mwb_wpr_email_discription);
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

					//Referral Purchase Point Feature Conditions
					$mwb_referral_purchase_enable = isset($general_settings['mwb_referral_purchase_enable']) ? intval($general_settings['mwb_referral_purchase_enable']) : 0;
					$mwb_referral_purchase_value = isset($general_settings['mwb_referral_purchase_value']) ? intval($general_settings['mwb_referral_purchase_value']) : 1;
					if($mwb_referral_purchase_enable)
					{
						$user_id = $order->get_user_id();
						$mwb_wpr_ref_noof_order = get_user_meta($user_id,'mwb_wpr_no_of_orders',true);
						$refer_id = get_user_meta($user_id,'user_visit_through_link',true);
						$refer_user=get_user_by('ID',$refer_id);
						if(!empty($refer_user))
							$refer_user_email=$refer_user->user_email;

						if($mwb_referral_purchase_limit == 0)
						{
							if(isset($refer_id) && !empty($refer_id)){

								$today_date = date_i18n("Y-m-d h:i:sa");
								$prev_points_of_ref_userid = (int)get_user_meta($refer_id , 'mwb_wpr_points', true);
								
								$update_points = $prev_points_of_ref_userid + $mwb_referral_purchase_value;
								$ref_product_detail_points = get_user_meta($refer_id, 'points_details',true);
								
								if(isset($ref_product_detail_points['ref_product_detail']) && !empty($ref_product_detail_points['ref_product_detail']))
								{		
									$ref_pro_array = array(
										'ref_product_detail'=>$mwb_referral_purchase_value,
										'date'=>$today_date,
										'refered_user'=>$user_id
										);
									$ref_product_detail_points['ref_product_detail'][] = $ref_pro_array;
								}													
								else
								{
									if(!is_array($ref_product_detail_points)){
										$ref_product_detail_points = array();
									}	
									$ref_pro_array = array(
										'ref_product_detail'=>$mwb_referral_purchase_value,
										'date'=>$today_date,
										'refered_user'=>$user_id
										);
									$ref_product_detail_points['ref_product_detail'][] = $ref_pro_array;
								}
								
								update_user_meta( $refer_id , 'points_details' , $ref_product_detail_points );
								update_user_meta( $refer_id  , 'mwb_wpr_points' , $update_points );
								if(is_array($mwb_wpr_notificatin_array) && !empty($mwb_wpr_notificatin_array))
								{
									$mwb_wpr_notificatin_enable=isset($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) ? intval($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) : 0;
									$mwb_wpr_email_subject=isset($mwb_wpr_notificatin_array['mwb_wpr_referral_purchase_email_subject'])? $mwb_wpr_notificatin_array['mwb_wpr_referral_purchase_email_subject'] :'';
									$mwb_wpr_email_discription=isset($mwb_wpr_notificatin_array['mwb_wpr_referral_purchase_email_discription_custom_id']) ? $mwb_wpr_notificatin_array['mwb_wpr_referral_purchase_email_discription_custom_id'] :'';
									$mwb_wpr_email_discription=str_replace("[Points]",$mwb_referral_purchase_value,$mwb_wpr_email_discription);
									$mwb_wpr_email_discription=str_replace("[Total Points]",$update_points,$mwb_wpr_email_discription);
									$mwb_wpr_email_discription=str_replace("[Refer Points]",$mwb_refer_value,$mwb_wpr_email_discription);
									$mwb_wpr_email_discription=str_replace("[Comment Points]",$mwb_comment_value,$mwb_wpr_email_discription);
									$mwb_wpr_email_discription=str_replace("[Per Currency Spent Points]",$mwb_per_currency_spent_value,$mwb_wpr_email_discription);
									$user = get_user_by('email',$refer_user_email);
									$user_name = $user->user_firstname;
									$mwb_wpr_email_discription = str_replace("[USERNAME]",$user_name,$mwb_wpr_email_discription);
									if($mwb_wpr_notificatin_enable)
									{	
										$headers = array('Content-Type: text/html; charset=UTF-8');
										wc_mail($refer_user_email,$mwb_wpr_email_subject,$mwb_wpr_email_discription,$headers);
									}
								}
							}
						}
						else
						{

							if(isset($mwb_wpr_ref_noof_order) && !empty($mwb_wpr_ref_noof_order) && $mwb_wpr_ref_noof_order <= $mwb_wpr_general_referal_order_limit)
							{
								if(isset($refer_id) && !empty($refer_id)){

									$today_date = date_i18n("Y-m-d h:i:sa");
									$prev_points_of_ref_userid = (int)get_user_meta($refer_id , 'mwb_wpr_points', true);
									
									$update_points = $prev_points_of_ref_userid + $mwb_referral_purchase_value;
									$ref_product_detail_points = get_user_meta($refer_id, 'points_details',true);
									
									if(isset($ref_product_detail_points['ref_product_detail']) && !empty($ref_product_detail_points['ref_product_detail']))
									{		
										$ref_pro_array = array(
											'ref_product_detail'=>$mwb_referral_purchase_value,
											'date'=>$today_date,
											'refered_user'=>$user_id
											);
										$ref_product_detail_points['ref_product_detail'][] = $ref_pro_array;
									}													
									else
									{
										if(!is_array($ref_product_detail_points)){
											$ref_product_detail_points = array();
										}
										$ref_pro_array = array(
											'ref_product_detail'=>$mwb_referral_purchase_value,
											'date'=>$today_date,
											'refered_user'=>$user_id
											);
										$ref_product_detail_points['ref_product_detail'][] = $ref_pro_array;
									}
									
									update_user_meta( $refer_id , 'points_details' , $ref_product_detail_points );
									update_user_meta( $refer_id  , 'mwb_wpr_points' , $update_points );
									if(is_array($mwb_wpr_notificatin_array) && !empty($mwb_wpr_notificatin_array))
									{
										$mwb_wpr_notificatin_enable=isset($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) ? intval($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) : 0;
										$mwb_wpr_email_subject=isset($mwb_wpr_notificatin_array['mwb_wpr_referral_purchase_email_subject'])? $mwb_wpr_notificatin_array['mwb_wpr_referral_purchase_email_subject'] :'';
										$mwb_wpr_email_discription=isset($mwb_wpr_notificatin_array['mwb_wpr_referral_purchase_email_discription_custom_id']) ? $mwb_wpr_notificatin_array['mwb_wpr_referral_purchase_email_discription_custom_id'] :'';
										$mwb_wpr_email_discription=str_replace("[Points]",$mwb_referral_purchase_value,$mwb_wpr_email_discription);
										$mwb_wpr_email_discription=str_replace("[Total Points]",$update_points,$mwb_wpr_email_discription);
										$mwb_wpr_email_discription=str_replace("[Refer Points]",$mwb_refer_value,$mwb_wpr_email_discription);
										$mwb_wpr_email_discription=str_replace("[Comment Points]",$mwb_comment_value,$mwb_wpr_email_discription);
										$mwb_wpr_email_discription=str_replace("[Per Currency Spent Points]",$mwb_per_currency_spent_value,$mwb_wpr_email_discription);
										$user = get_user_by('email',$refer_user_email);
										$user_name = $user->user_firstname;
										$mwb_wpr_email_discription = str_replace("[USERNAME]",$user_name,$mwb_wpr_email_discription);
										if($mwb_wpr_notificatin_enable)
										{	
											$headers = array('Content-Type: text/html; charset=UTF-8');
											wc_mail($refer_user_email,$mwb_wpr_email_subject,$mwb_wpr_email_discription,$headers);
										}
									}
								}
							}
						}
					}
				}
			}
		}
		public function get_points_calculation($user_id,$order_total) {

			$membership_detail=get_user_meta( $user_id , 'points_details' ,true);
			$selected_role=get_user_meta($user_id,'membership_level',true);
			if(!empty($selected_role) && isset($selected_role)) {

				$membership_settings_array = get_option('mwb_wpr_membership_settings',true);
				$mwb_wpr_membership_roles = isset($membership_settings_array['membership_roles']) && !empty($membership_settings_array['membership_roles']) ? $membership_settings_array['membership_roles'] : array();	
				if(!empty($mwb_wpr_membership_roles) && is_array($mwb_wpr_membership_roles)) {

					foreach( $mwb_wpr_membership_roles as $roles => $values) {	

						if($roles == $selected_role) {

							$mwb_wpr_coupon_conversion_points = $values['points_con'];
							$mwb_wpr_coupon_conversion_price = $values['price_con'];
						}
					}
				}
				$points_calculation =ceil(($order_total*$mwb_wpr_coupon_conversion_points)/$mwb_wpr_coupon_conversion_price);
			}
			else {
				$coupon_settings_array=get_option('mwb_wpr_coupons_gallery',array());
				$mwb_wpr_coupon_conversion_points=$coupon_settings_array['mwb_wpr_coupon_conversion_points'];
				$mwb_wpr_coupon_conversion_price=$coupon_settings_array['mwb_wpr_coupon_conversion_price'];
				$points_calculation =ceil(($order_total*$mwb_wpr_coupon_conversion_points)/$mwb_wpr_coupon_conversion_price);
			}
			return $points_calculation;
		}
		/**
		 * This function is used to save item points in time of order according to woocommerce 3.0.
		 * 
		 * @name mwb_wpr_woocommerce_add_order_item_meta_version_3
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		*/
		function mwb_wpr_woocommerce_add_order_item_meta_version_3($item,$cart_key,$values,$order)
		{
			if (isset ( $values['product_meta'] ))
			{
				foreach ($values['product_meta'] ['meta_data'] as $key => $val )
				{
					$order_val = stripslashes( $val );
					if($val)
					{
						if($key == 'mwb_wpm_points')
						{
							$item->add_meta_data('Points',$order_val);
						}
						if($key == 'pro_purchase_by_points')
						{
							$item->add_meta_data('Purchasing Option',$order_val);
						}
						if($key == 'mwb_wpr_purchase_point_only')
						{
							$item->add_meta_data('Purchased By Points',$order_val);
						}
					}
				}
			}
		}

		/**
		 * This function is used to save item points in time of order according to previous woocommerce version.
		 * 
		 * @name mwb_wpr_woocommerce_add_order_item_meta
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		*/
		public function mwb_wpr_woocommerce_add_order_item_meta($item_id, $cart_item){
			if (isset ( $cart_item ['product_meta'] ))
			{ 
				foreach ( $cart_item ['product_meta'] ['meta_data'] as $key => $val )
				{
					$order_val = stripslashes( $val );
					if($val)
					{
						if($key == 'mwb_wpm_points')
						{
							wc_add_order_item_meta ( $item_id, 'Points', $order_val );
						}
						if($key == 'pro_purchase_by_points')
						{
							wc_add_order_item_meta ( $item_id, 'Purchasing Option', $order_val );
						}
					}
				}
			}
		}
		
		/**
		 * This function is used to show item poits in time of order .
		 * 
		 * @name mwb_wpr_woocommerce_get_item_data
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		*/
		public function mwb_wpr_woocommerce_get_item_data($item_meta, $existing_item_meta)
		{
			if(isset($existing_item_meta ['product_meta']['meta_data']))
			{
				if ($existing_item_meta ['product_meta']['meta_data'])
				{
					foreach ($existing_item_meta['product_meta'] ['meta_data'] as $key => $val )
					{
						if($key == 'mwb_wpm_points')
						{
							//$img = "<img src = ".MWB_WPR_URL."/assets/images/medal_img.png>";
							/*$val = "<span class=mwb_wpr_item_meta>".$val.$img."</span>";*/
							$item_meta [] = array (
								'name' => __('Points',MWB_WPR_Domain),
								'value' => stripslashes( $val ),
								);
						}
						if($key == 'mwb_wpr_purchase_point_only')
						{
							
							$item_meta [] = array (
								'name' => __('Purchased By Points',MWB_WPR_Domain),
								'value' => stripslashes( $val ),
								);
						}
					}
					$item_meta = apply_filters('mwb_wpm_product_item_meta', $item_meta, $key, $val);
				}
			}
			return $item_meta;
		}

		/**
		 * This function is used to save points in add to cart session0.
		 * 
		 * @name mwb_wpr_woocommerce_add_cart_item_data
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		*/
		public function mwb_wpr_woocommerce_add_cart_item_data($the_cart_data, $product_id, $variation_id)
		{	
			if(isset($_REQUEST['quantity']) && $_REQUEST['quantity'] && $_REQUEST['quantity'] != null)
			{
				$quantity = (int)$_REQUEST['quantity'];
			}
			else
			{
				$quantity = 1;
			}
			$user_id = get_current_user_ID();
			$get_points = (int)get_user_meta($user_id,'mwb_wpr_points',true);
			$product_types = wp_get_object_terms( $product_id, 'product_type' );
			$check_enable = get_post_meta($product_id, 'mwb_product_points_enable', 'no');
			$general_settings = get_option('mwb_wpr_settings_gallery',true);
			$mwb_wpr_set_preferences = isset($general_settings['mwb_wpr_set_preferences']) ? $general_settings['mwb_wpr_set_preferences'] : 'to_both';
			$enable_purchase_points = isset($general_settings['enable_purchase_points']) ? intval($general_settings['enable_purchase_points']) : 0;

			if($check_enable == 'yes')
			{	
				if( $mwb_wpr_set_preferences == 'to_assign_point' || $mwb_wpr_set_preferences == 'to_both' )
				{
					if(isset($variation_id) && !empty($variation_id) && $variation_id > 0){
						$get_product_points = get_post_meta($variation_id, 'mwb_wpr_variable_points', 1);
						$item_meta['mwb_wpm_points'] = (int)$get_product_points*(int)$quantity;
					}else{
						$get_product_points = get_post_meta($product_id, 'mwb_points_product_value', 1);
						$item_meta['mwb_wpm_points'] = (int)$get_product_points*(int)$quantity;
					}
					$the_cart_data ['product_meta'] = array('meta_data' => $item_meta);
				}

			}	
			
			if( $enable_purchase_points )
			{
				if(isset($_POST['mwb_wpr_pro_cost_to_points']) && !empty($_POST['mwb_wpr_pro_cost_to_points']) && $_POST['mwb_wpr_pro_cost_to_points'])
				{	
					if( $_POST['mwb_wpr_pro_cost_to_points'] > $get_points )
					{
						$item_meta['pro_purchase_by_points'] = $get_points;
					}
					else{
						$item_meta['pro_purchase_by_points'] = $_POST['mwb_wpr_pro_cost_to_points'];
					}
					$the_cart_data ['product_meta'] = array('meta_data' => $item_meta);
				}
			}

			//MWB Custom Work
			$_product = wc_get_product($product_id);
			$enable_product_purchase_points = get_post_meta($product_id, 'mwb_product_purchase_points_only', true);
			$mwb_product_purchase_value = get_post_meta($product_id, 'mwb_points_product_purchase_value', true);
			$prod_type = $_product->get_type();
       		//$user = wp_get_current_user();
       		//$user_id = $user->ID;
       		//$get_points = (int)get_user_meta($user_id, 'mwb_wpr_points', true);        		
			if(isset($enable_product_purchase_points) && $enable_product_purchase_points == 'yes')
			{
				if(isset($mwb_product_purchase_value) && !empty($mwb_product_purchase_value ) && ($prod_type == 'simple'))
				{
					if ($mwb_product_purchase_value < $get_points)
					{	
						$item_meta['mwb_wpr_purchase_point_only'] = $mwb_product_purchase_value*(int)$quantity;
						$the_cart_data ['product_meta'] = array('meta_data' => $item_meta);       				
					}

				}
			}
       		// Custom Work for Variable Product
			if ($this->mwb_wpr_check_whether_product_is_variable($_product)) {		       		    				
				$mwb_wpr_parent_id = wp_get_post_parent_id($variation_id);
				$enable_product_purchase_points = get_post_meta($mwb_wpr_parent_id, 'mwb_product_purchase_points_only',true);
				$mwb_product_purchase_value = get_post_meta($variation_id, 'mwb_wpr_variable_points_purchase',true);
				if(isset($enable_product_purchase_points) && $enable_product_purchase_points == 'yes'){

					if(isset($mwb_product_purchase_value) && !empty($mwb_product_purchase_value))
					{
						if (is_user_logged_in())
						{
							if ($mwb_product_purchase_value < $get_points)
							{	
								$item_meta['mwb_wpr_purchase_point_only'] = $mwb_product_purchase_value*(int)$quantity;			       				
								$the_cart_data ['product_meta'] = array('meta_data' => $item_meta);       				
							}
						}
					}

				}
			}

			//End of Custom Work
			return $the_cart_data;
		}

		/**
		 * This function is used to show item points in product discription page.   .
		 * 
		 * @name mwb_display_product_points
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		*/
		public function mwb_display_product_points(){
			global $post;
			$mwb_wpr_notification_color = get_option('mwb_wpr_notification_color','#55b3a5');
			$product = wc_get_product($post->ID);
			$product_is_variable = $this->mwb_wpr_check_whether_product_is_variable($product);	
			$check_enable = get_post_meta($post->ID, 'mwb_product_points_enable', 'no');
			$general_settings = get_option('mwb_wpr_settings_gallery',true);
			$mwb_wpr_set_preferences = isset($general_settings['mwb_wpr_set_preferences']) ? $general_settings['mwb_wpr_set_preferences'] : 'to_both';
			$mwb_wpr_assign_pro_text = isset($general_settings['mwb_wpr_assign_pro_text']) ? $general_settings['mwb_wpr_assign_pro_text'] : __('Earn',MWB_WPR_Domain);
			if($check_enable == 'yes' )
			{
				if( $mwb_wpr_set_preferences == 'to_assign_point' || $mwb_wpr_set_preferences == 'to_both' )
				{
					//$img = "<img src = ".MWB_WPR_URL."/assets/images/medal_img.png>";
					if(!$product_is_variable){
						$get_product_points = get_post_meta($post->ID, 'mwb_points_product_value', 1);

						echo "<span class=mwb_wpr_product_point style=background-color:".$mwb_wpr_notification_color.">".$mwb_wpr_assign_pro_text.": ".$get_product_points;_e(' Points',MWB_WPR_Domain);
						//echo $img;
						echo "</span>";
					}
					elseif($product_is_variable){
						$get_product_points = "<span class=mwb_wpr_variable_points></span>";
						echo "<span class=mwb_wpr_product_point style='display:none;background-color:".$mwb_wpr_notification_color."'>".$mwb_wpr_assign_pro_text.": ".$get_product_points;_e(' Points',MWB_WPR_Domain);
						//echo $img;
						echo "</span>";
					}
				}
			}
		}
		
		/**
		 * Points update in time of new user registeration.
		 * 
		 * @name mwb_wpr_new_customer_registerd
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		*/
		public function mwb_wpr_new_customer_registerd( $customer_id, $new_customer_data, $password_generated ){	
			if(get_user_by('ID',$customer_id)){
				$general_settings = get_option('mwb_wpr_settings_gallery',true);
				$coupon_settings_array=get_option('mwb_wpr_coupons_gallery',array());
				$enable_mwb_signup = isset($general_settings['enable_mwb_signup']) ? intval($general_settings['enable_mwb_signup']) : 0;
				
				if($enable_mwb_signup )
				{	
					$mwb_signup_value = isset($general_settings['mwb_signup_value']) ? intval($general_settings['mwb_signup_value']) : 1;
					$mwb_comment_value = isset($general_settings['mwb_comment_value']) ? intval($general_settings['mwb_comment_value']) : 1;
					$mwb_refer_value = isset($general_settings['mwb_refer_value']) ? intval($general_settings['mwb_refer_value']) : 1;
					$mwb_per_currency_spent_value = isset($coupon_settings_array['mwb_wpr_coupon_conversion_points']) ? intval($coupon_settings_array['mwb_wpr_coupon_conversion_points']) : 1;
					$today_date = date_i18n("Y-m-d h:i:sa");
					$points_details['registration'][] = array(
						'registration'=> $mwb_signup_value,
						'date' => $today_date);
					update_user_meta( $customer_id , 'mwb_wpr_points' , $mwb_signup_value );
					update_user_meta( $customer_id, 'points_details', $points_details);
					$mwb_wpr_notificatin_array=get_option('mwb_wpr_notificatin_array',true);
					if(is_array($mwb_wpr_notificatin_array) && !empty($mwb_wpr_notificatin_array))
					{
						$mwb_wpr_notificatin_enable=isset($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) ? intval($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) : 0;
						$mwb_wpr_email_subject=isset($mwb_wpr_notificatin_array['mwb_wpr_signup_email_subject'])? $mwb_wpr_notificatin_array['mwb_wpr_signup_email_subject'] :'';
						$mwb_wpr_email_discription=isset($mwb_wpr_notificatin_array['mwb_wpr_signup_email_discription_custom_id']) ? $mwb_wpr_notificatin_array['mwb_wpr_signup_email_discription_custom_id'] :'';
						$mwb_wpr_email_discription=str_replace("[Points]",$mwb_signup_value,$mwb_wpr_email_discription);
						$mwb_wpr_email_discription=str_replace("[Total Points]",$mwb_signup_value,$mwb_wpr_email_discription);
						$mwb_wpr_email_discription=str_replace("[Comment Points]",$mwb_comment_value,$mwb_wpr_email_discription);
						$mwb_wpr_email_discription=str_replace("[Refer Points]",$mwb_refer_value,$mwb_wpr_email_discription);
						$mwb_wpr_email_discription=str_replace("[Per Currency Spent Points]",$mwb_per_currency_spent_value,$mwb_wpr_email_discription);
						$user = get_user_by('email',$new_customer_data['user_email']);
						$user_name = $user->user_firstname;
						$mwb_wpr_email_discription = str_replace("[USERNAME]",$user_name,$mwb_wpr_email_discription);
						if($mwb_wpr_notificatin_enable)
						{	
							$headers = array('Content-Type: text/html; charset=UTF-8');
							wc_mail($new_customer_data['user_email'],$mwb_wpr_email_subject,$mwb_wpr_email_discription,$headers);
						}
					}
				}
				else
				{
					update_option("mwb_wpr_user_imported", false);
					//for retreiving those users on Points Table who do not have any signup Points
					// update_user_meta( $customer_id , 'mwb_wpr_points' , 0 );
					update_user_meta( $customer_id , 'mwb_wpr_signup' , 'disable' );
				}
				$enable_mwb_refer = isset($general_settings['enable_mwb_refer']) ? intval($general_settings['enable_mwb_refer']) : 0;
				$mwb_comment_value = isset($general_settings['mwb_comment_value']) ? intval($general_settings['mwb_comment_value']) : 1;
				
				$mwb_per_currency_spent_value = isset($coupon_settings_array['mwb_wpr_coupon_conversion_points']) ? intval($coupon_settings_array['mwb_wpr_coupon_conversion_points']) : 1;
				$mwb_refer_value = isset($general_settings['mwb_refer_value']) ? intval($general_settings['mwb_refer_value']) : 1;
				$mwb_refer_min = isset($general_settings['mwb_refer_min']) ? intval($general_settings['mwb_refer_min']) : 1;
				$mwb_referral_purchase_enable = isset($general_settings['mwb_referral_purchase_enable']) ? intval($general_settings['mwb_referral_purchase_enable']) : 0;
				$mwb_refer_value_disable = isset($general_settings['mwb_wpr_general_refer_value_disable']) ? intval($general_settings['mwb_wpr_general_refer_value_disable']) : 0;

				if($enable_mwb_refer)
				{	
					$cookie_val = isset($_COOKIE['mwb_wpr_cookie_set']) ? $_COOKIE['mwb_wpr_cookie_set'] : '';
					$retrive_data = $cookie_val;			
					if(!empty($retrive_data))
					{				
						$args['meta_query'] = array(						
							array(
								'key'=>'mwb_points_referral',
								'value'=>trim($retrive_data),
								'compare'=>'=='
								)
							);		
						$user_data= get_users( $args );
						

						$user_id = $user_data[0]->data->ID;
						$user = get_user_by('ID',$user_id);
						$user_email = $user->user_email;
						$get_referral = get_user_meta($user_id, 'mwb_points_referral', true);
						$get_referral_invite = get_user_meta($user_id, 'mwb_points_referral_invite', true);

						//custom work by MWB
						$custom_ref_pnt = get_user_meta($user_id,'mwb_custom_points_referral_invite',true);
						//end of custom work
						if(isset($get_referral) && $get_referral !=null && isset($get_referral_invite) && $get_referral_invite !=null )
						{
							if($get_referral_invite < $mwb_refer_min)
							{
								$get_referral_invite = (int)$get_referral_invite;
								update_user_meta($user_id, 'mwb_points_referral_invite', $get_referral_invite+1);
								update_user_meta($customer_id, 'user_visit_through_link', $user_id);
								$custom_ref_pnt = (int)$custom_ref_pnt;
								update_user_meta($user_id,'mwb_custom_points_referral_invite',$custom_ref_pnt+1);
								$this->mwb_wpr_destroy_cookie();
							}
							$get_referral_invite = get_user_meta($user_id, 'mwb_points_referral_invite', true);
							if($get_referral_invite == $mwb_refer_min)
							{
								//as soon as user get the Points, counter will re-initialised
								update_user_meta($user_id, 'mwb_points_referral_invite', 0);
								if(!$mwb_refer_value_disable)
								{
									$today_date = date_i18n("Y-m-d h:i:sa");
									$get_points = (int)get_user_meta($user_id , 'mwb_wpr_points', true);
									$get_referral_detail = get_user_meta($user_id, 'points_details', true);
									if(isset($get_referral_detail['reference_details']) && !empty($get_referral_detail['reference_details'])){
										$custom_array = array(
											'reference_details'=> $mwb_refer_value,
											'date' => $today_date,
											'refered_user' => $customer_id);
										$get_referral_detail['reference_details'][] = $custom_array;
									}else{
										if(!is_array($get_referral_detail)){
											$get_referral_detail = array();
										}
										$get_referral_detail['reference_details'][] = array(
											'reference_details'=> $mwb_refer_value,
											'date' => $today_date,
											'refered_user' => $customer_id);
									}
									$total_points = $get_points + $mwb_refer_value;
									
									update_user_meta( $user_id  , 'mwb_wpr_points' , $total_points );
									update_user_meta( $user_id  , 'points_details' , $get_referral_detail );
									$mwb_wpr_notificatin_array=get_option('mwb_wpr_notificatin_array',true);
									if(is_array($mwb_wpr_notificatin_array) && !empty($mwb_wpr_notificatin_array))
									{
										$$mwb_wpr_notificatin_enable=isset($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) ? intval($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) : 0;
										$mwb_wpr_email_subject=isset($mwb_wpr_notificatin_array['mwb_wpr_referral_email_subject'])? $mwb_wpr_notificatin_array['mwb_wpr_referral_email_subject'] :'';
										$mwb_wpr_email_discription=isset($mwb_wpr_notificatin_array['mwb_wpr_referral_email_discription_custom_id']) ? $mwb_wpr_notificatin_array['mwb_wpr_referral_email_discription_custom_id'] :'';
										$mwb_wpr_email_discription=str_replace("[Points]",$mwb_refer_value,$mwb_wpr_email_discription);
										$mwb_wpr_email_discription=str_replace("[Total Points]",$total_points,$mwb_wpr_email_discription);
										$mwb_wpr_email_discription=str_replace("[Comment Points]",$mwb_comment_value,$mwb_wpr_email_discription);

										$mwb_wpr_email_discription=str_replace("[Per Currency Spent Points]",$mwb_per_currency_spent_value,$mwb_wpr_email_discription);
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
		}

		/**
		 * Compatible with Digits Plugin
		 * 
		 * @name mwb_wpr_new_customer_digit_registered
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		*/
		public function mwb_wpr_new_customer_digit_registered( $customer_id )
		{
			$current_user = get_userdata( $customer_id );
			$current_user_email = $current_user->data->user_email;
			$general_settings = get_option('mwb_wpr_settings_gallery',true);
			
			$coupon_settings_array=get_option('mwb_wpr_coupons_gallery',array());
			$enable_mwb_signup = isset($general_settings['enable_mwb_signup']) ? intval($general_settings['enable_mwb_signup']) : 0;
			
			if($enable_mwb_signup )
			{
				$mwb_signup_value = isset($general_settings['mwb_signup_value']) ? intval($general_settings['mwb_signup_value']) : 1;
				$mwb_comment_value = isset($general_settings['mwb_comment_value']) ? intval($general_settings['mwb_comment_value']) : 1;
				$mwb_refer_value = isset($general_settings['mwb_refer_value']) ? intval($general_settings['mwb_refer_value']) : 1;
				$mwb_per_currency_spent_value = isset($coupon_settings_array['mwb_wpr_coupon_conversion_points']) ? intval($coupon_settings_array['mwb_wpr_coupon_conversion_points']) : 1;
				$today_date = date_i18n("Y-m-d h:i:sa");
				$points_details['registration'][] = array(
					'registration'=> $mwb_signup_value,
					'date' => $today_date);
				update_user_meta( $customer_id , 'mwb_wpr_points' , $mwb_signup_value );
				update_user_meta( $customer_id, 'points_details', $points_details);
				$mwb_wpr_notificatin_array=get_option('mwb_wpr_notificatin_array',true);
				if(is_array($mwb_wpr_notificatin_array) && !empty($mwb_wpr_notificatin_array))
				{
					$mwb_wpr_notificatin_enable=isset($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) ? intval($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) : 0;
					$mwb_wpr_email_subject=isset($mwb_wpr_notificatin_array['mwb_wpr_signup_email_subject'])? $mwb_wpr_notificatin_array['mwb_wpr_signup_email_subject'] :'';
					$mwb_wpr_email_discription=isset($mwb_wpr_notificatin_array['mwb_wpr_signup_email_discription_custom_id']) ? $mwb_wpr_notificatin_array['mwb_wpr_signup_email_discription_custom_id'] :'';
					$mwb_wpr_email_discription=str_replace("[Points]",$mwb_signup_value,$mwb_wpr_email_discription);
					$mwb_wpr_email_discription=str_replace("[Total Points]",$mwb_signup_value,$mwb_wpr_email_discription);
					$mwb_wpr_email_discription=str_replace("[Comment Points]",$mwb_comment_value,$mwb_wpr_email_discription);
					$mwb_wpr_email_discription=str_replace("[Refer Points]",$mwb_refer_value,$mwb_wpr_email_discription);
					$mwb_wpr_email_discription=str_replace("[Per Currency Spent Points]",$mwb_per_currency_spent_value,$mwb_wpr_email_discription);
					$user = get_user_by('email',$user_email);
					$user_name = $user->user_firstname;
					$mwb_wpr_email_discription = str_replace("[USERNAME]",$user_name,$mwb_wpr_email_discription);
					if($mwb_wpr_notificatin_enable)
					{	
						$headers = array('Content-Type: text/html; charset=UTF-8');
						wc_mail($current_user_email,$mwb_wpr_email_subject,$mwb_wpr_email_discription,$headers);
					}
				}
			}
			else
			{
				update_option("mwb_wpr_user_imported", false);
			}
			$enable_mwb_refer = isset($general_settings['enable_mwb_refer']) ? intval($general_settings['enable_mwb_refer']) : 0;
			$mwb_comment_value = isset($general_settings['mwb_comment_value']) ? intval($general_settings['mwb_comment_value']) : 1;
			
			$mwb_per_currency_spent_value = isset($coupon_settings_array['mwb_wpr_coupon_conversion_points']) ? intval($coupon_settings_array['mwb_wpr_coupon_conversion_points']) : 1;
			$mwb_refer_value = isset($general_settings['mwb_refer_value']) ? intval($general_settings['mwb_refer_value']) : 1;
			$mwb_refer_min = isset($general_settings['mwb_refer_min']) ? intval($general_settings['mwb_refer_min']) : 1;
			$mwb_referral_purchase_enable = isset($general_settings['mwb_referral_purchase_enable']) ? intval($general_settings['mwb_referral_purchase_enable']) : 0;
			$mwb_refer_value_disable = isset($general_settings['mwb_wpr_general_refer_value_disable']) ? intval($general_settings['mwb_wpr_general_refer_value_disable']) : 0;

			if($enable_mwb_refer)
			{				
				if(isset($_GET['pkey']) && !empty($_GET['pkey']))
				{				
					$args['meta_query'] = array(						
						array(
							'key'=>'mwb_points_referral',
							'value'=>trim($_GET['pkey']),
							'compare'=>'=='
							)
						);		
					$user_data= get_users( $args );
					

					$user_id = $user_data[0]->data->ID;
					$user=get_user_by('ID',$user_id);
					$user_email=$user->user_email;
					$get_referral = get_user_meta($user_id, 'mwb_points_referral', true);
					$get_referral_invite = get_user_meta($user_id, 'mwb_points_referral_invite', true);
					if(isset($get_referral) && $get_referral !=null && isset($get_referral_invite) && $get_referral_invite !=null )
					{
						if($get_referral_invite < $mwb_refer_min)
						{
							$get_referral_invite = (int)$get_referral_invite;
							update_user_meta($user_id, 'mwb_points_referral_invite', $get_referral_invite+1);
							update_user_meta($customer_id, 'user_visit_through_link', $user_id);
						}
						$get_referral_invite = get_user_meta($user_id, 'mwb_points_referral_invite', true);
						if($get_referral_invite == $mwb_refer_min)
						{
							if(!$mwb_refer_value_disable)
							{
								$today_date = date_i18n("Y-m-d h:i:sa");
								$get_points = (int)get_user_meta($user_id , 'mwb_wpr_points', true);
								$get_referral_detail = get_user_meta($user_id, 'points_details', true);
								
								$get_referral_detail['reference_details'][] = array(
									'reference_details'=> $mwb_refer_value,
									'date' => $today_date);

								$total_points = $get_points + $mwb_refer_value;
								
								update_user_meta( $user_id  , 'mwb_wpr_points' , $total_points );
								update_user_meta( $user_id  , 'points_details' , $get_referral_detail );
								$mwb_wpr_notificatin_array=get_option('mwb_wpr_notificatin_array',true);
								if(is_array($mwb_wpr_notificatin_array) && !empty($mwb_wpr_notificatin_array))
								{
									$$mwb_wpr_notificatin_enable=isset($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) ? intval($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) : 0;
									$mwb_wpr_email_subject=isset($mwb_wpr_notificatin_array['mwb_wpr_referral_email_subject'])? $mwb_wpr_notificatin_array['mwb_wpr_referral_email_subject'] :'';
									$mwb_wpr_email_discription=isset($mwb_wpr_notificatin_array['mwb_wpr_referral_email_discription_custom_id']) ? $mwb_wpr_notificatin_array['mwb_wpr_referral_email_discription_custom_id'] :'';
									$mwb_wpr_email_discription=str_replace("[Points]",$mwb_refer_value,$mwb_wpr_email_discription);
									$mwb_wpr_email_discription=str_replace("[Total Points]",$total_points,$mwb_wpr_email_discription);
									$mwb_wpr_email_discription=str_replace("[Comment Points]",$mwb_comment_value,$mwb_wpr_email_discription);

									$mwb_wpr_email_discription=str_replace("[Per Currency Spent Points]",$mwb_per_currency_spent_value,$mwb_wpr_email_discription);
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
		 * This function will add discounted price for selected products in any 	Membership Level.
		 * 
		 * @name mwb_wpr_user_level_discount_on_price
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		*/
		public function mwb_wpr_user_level_discount_on_price($price, $product_data)
		{
			$today_date = date_i18n("Y-m-d");
			$user_id = get_current_user_ID();
			$new_price = '';
			$product_id = $product_data->get_id();
			$_product = wc_get_product( $product_id );
			$product_is_variable = $this->mwb_wpr_check_whether_product_is_variable($_product);
			$reg_price = $_product->get_price();
			$prod_type = $_product->get_type();
			$user_level = get_user_meta($user_id,'membership_level',true);
			$mwb_wpr_mem_expr = get_user_meta($user_id,'membership_expiration',true);
			$membership_settings_array = get_option('mwb_wpr_membership_settings',true);
			$mwb_wpr_membership_roles = isset($membership_settings_array['membership_roles']) && !empty($membership_settings_array['membership_roles']) ? $membership_settings_array['membership_roles'] : array();
			if( isset( $user_level ) && !empty( $user_level ) )
			{
				if( isset( $mwb_wpr_mem_expr ) && !empty( $mwb_wpr_mem_expr ) && $today_date <= $mwb_wpr_mem_expr )
				{
					if(is_array($mwb_wpr_membership_roles) && !empty($mwb_wpr_membership_roles))
					{
						foreach($mwb_wpr_membership_roles as $roles => $values)
						{	
							
							if($user_level == $roles)
							{	
								if(is_array($values['Product']) && !empty($values['Product']))
								{
									if(in_array($product_id, $values['Product']) && !$product_is_variable && !$this->check_exclude_sale_products($product_data))
									{	
										$new_price = $reg_price - ($reg_price * $values['Discount'])/100;
										$price = '<del>'.wc_price( $reg_price ) . $product_data->get_price_suffix().'</del><ins>'.wc_price( $new_price ) . $product_data->get_price_suffix().'</ins>';
									}

								}
								else if(!$this->check_exclude_sale_products($product_data))
								{
									$terms = get_the_terms ( $product_id, 'product_cat' );
									if(is_array($terms) && !empty($terms) && !$product_is_variable)
									{
										foreach ( $terms as $term ) 
										{
											$cat_id = $term->term_id;
											$parent_cat = $term->parent;
											if(in_array($cat_id, $values['Prod_Categ']) || in_array($parent_cat, $values['Prod_Categ'])) {	
												if(!empty($reg_price)){

													$new_price = $reg_price - ($reg_price * $values['Discount'])/100;
													$price = '<del>'.wc_price( $reg_price ) . $product_data->get_price_suffix().'</del><ins>'.wc_price( $new_price ) . $product_data->get_price_suffix().'</ins>';
												}
											}
										}
									}

								}	
							}
						}
					}
				}
			}
			//MWB CUSTOM WORK
			$enable_product_purchase_points = get_post_meta($product_id, 'mwb_product_purchase_points_only',true);
			$mwb_product_purchase_value = get_post_meta($product_id, 'mwb_points_product_purchase_value',true);

			if(isset($enable_product_purchase_points) && $enable_product_purchase_points == 'yes'){
				if(isset($mwb_product_purchase_value) && !empty($mwb_product_purchase_value) && ($prod_type == 'simple') )
				{	
					$price = $mwb_product_purchase_value.' Points';
				}
			}
			
			//END OF CUSTOM WORK
			return $price;
		}

		/**
		 * This function will add discounted price in cart page.
		 * 
		 * @name mwb_wpr_woocommerce_before_calculate_totals
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		*/
		public function mwb_wpr_woocommerce_before_calculate_totals($cart)
		{	
			$woo_ver = WC()->version;
			$user_id = get_current_user_ID();
			$new_price = '';
			$today_date = date_i18n("Y-m-d");
			$user_level = get_user_meta($user_id,'membership_level',true);
			$mwb_wpr_mem_expr = get_user_meta($user_id,'membership_expiration',true);
			$get_points = (int)get_user_meta($user_id, 'mwb_wpr_points', true);
			$membership_settings_array = get_option('mwb_wpr_membership_settings',true);
			$mwb_wpr_membership_roles = isset($membership_settings_array['membership_roles']) && !empty($membership_settings_array['membership_roles']) ? $membership_settings_array['membership_roles'] : array();
			$general_settings = get_option('mwb_wpr_settings_gallery',true);
			$enable_purchase_points = isset($general_settings['enable_purchase_points']) ? intval($general_settings['enable_purchase_points']) : 0;
			$mwb_wpr_restrict_pro_by_points = isset($general_settings['mwb_wpr_restrict_pro_by_points']) ? intval($general_settings['mwb_wpr_restrict_pro_by_points']) : 0;
			$mwb_wpr_purchase_product_text = isset($general_settings['mwb_wpr_purchase_product_text']) ? $general_settings['mwb_wpr_purchase_product_text'] : __('Use your Points for purchasing this Product',MWB_WPR_Domain);
			$mwb_wpr_purchase_points = (isset($general_settings['mwb_wpr_purchase_points']) && $general_settings['mwb_wpr_purchase_points'] != null) ? $general_settings['mwb_wpr_purchase_points'] : 1;

			$mwb_wpr_product_purchase_price = (isset($general_settings['mwb_wpr_product_purchase_price']) && $general_settings['mwb_wpr_product_purchase_price'] != null) ? intval($general_settings['mwb_wpr_product_purchase_price']) : 1;
			foreach ( $cart->cart_contents as $key => $value )
			{	
				if(isset($value)){
					$product_id = $value['product_id'];
					$pro_quant = $value['quantity'];
					$_product = wc_get_product( $product_id );
					$product_type = $_product->get_type();				
					//$_product   = apply_filters( 'woocommerce_cart_item_product', $value['data'], $value, $key );
					$product_is_variable = $this->mwb_wpr_check_whether_product_is_variable($_product);
					$reg_price = $_product->get_price();
					if(isset($value['variation_id']) && !empty($value['variation_id'])){
						$variation_id = $value['variation_id'];
						$variable_product = wc_get_product( $variation_id );
						$variable_price = $variable_product->get_price();
					}
					if( isset( $mwb_wpr_mem_expr ) && !empty( $mwb_wpr_mem_expr ) && $today_date <= $mwb_wpr_mem_expr ){
						if( isset($user_level) && !empty($user_level) ){
							foreach($mwb_wpr_membership_roles as $roles => $values){	
								if($user_level == $roles){	
									if(is_array($values['Product']) && !empty($values['Product'])){
										if(in_array($product_id, $values['Product']) && !$this->check_exclude_sale_products($_product) ){	
											if(!$product_is_variable){
												$new_price = $reg_price - ($reg_price * $values['Discount'])/100;
												if($woo_ver < "3.0.0"){
													$value['data']->price = $new_price;
												}
												else{
													$value['data']->set_price($new_price);
												}
											}
											elseif($product_is_variable){
												$new_price = $variable_price - ($variable_price * $values['Discount'])/100;
												if($woo_ver < "3.0.0"){
													$value['data']->price = $new_price;
												}
												else{
													$value['data']->set_price($new_price);
												}
											}
										}
									}
									else if(!$this->check_exclude_sale_products($_product)){
										$terms = get_the_terms ( $product_id, 'product_cat' );
										if(is_array($terms) && !empty($terms)){
											foreach ( $terms as $term ){
												$cat_id = $term->term_id;
												$parent_cat = $term->parent;
												if(in_array($cat_id, $values['Prod_Categ']) || in_array($parent_cat, $values['Prod_Categ'])){	
													if(!$product_is_variable){
														$new_price = $reg_price - ($reg_price * $values['Discount'])/100;
														if($woo_ver < "3.0.0"){
															$value['data']->price = $new_price;
														}
														else{
															$value['data']->set_price($new_price);
														}
													}
													elseif($product_is_variable){
														$new_price = $variable_price - ($variable_price * $values['Discount'])/100;
														if($woo_ver < "3.0.0"){
															$value['data']->price = $new_price;
														}
														else{
															$value['data']->set_price($new_price);
														}
													}
												}
											}
										}
									}	
								}
							}
						}
					}	
					//MWB Custom Work
					$enable_product_purchase_points = get_post_meta($product_id, 'mwb_product_purchase_points_only', true);
					$mwb_product_purchase_value = get_post_meta($product_id, 'mwb_points_product_purchase_value', true);
					if(isset($enable_product_purchase_points) && $enable_product_purchase_points == 'yes' && !empty($enable_product_purchase_points) )
					{ 
						if(isset($mwb_product_purchase_value) && !empty($mwb_product_purchase_value) && ($product_type == 'simple') )
						{	
							if (is_user_logged_in())
							{	
								if (($mwb_product_purchase_value*$pro_quant) < $get_points)
								{	
									$value['data']->set_price(0);        				
								}			       				
							}
						}
					}
					if ($this->mwb_wpr_check_whether_product_is_variable($_product)) {

						$mwb_wpr_parent_id = wp_get_post_parent_id($variation_id);
						$enable_product_purchase_points = get_post_meta($mwb_wpr_parent_id, 'mwb_product_purchase_points_only',true);
						$mwb_product_purchase_value = get_post_meta($variation_id, 'mwb_wpr_variable_points_purchase',true);
						if(isset($enable_product_purchase_points) && $enable_product_purchase_points == 'yes'){

							if(isset($mwb_product_purchase_value) && !empty($mwb_product_purchase_value))
							{
								if (is_user_logged_in())
								{
									if (($mwb_product_purchase_value*$pro_quant) < $get_points)
									{  
										$value['data']->set_price(0);        				
									}
								}
							}

						}
					}

					//End of Custom Work .....> value['data']->set_price(0);						
				}
			}
		}

		/**
		 * This function will add checkbox for purchase the products through points
		 * 
		 * @name mwb_wpr_woocommerce_before_add_to_cart_button
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		*/
		public function mwb_wpr_woocommerce_before_add_to_cart_button($product)
		{	
			global $product;
			if( $product->is_type( 'variable' ) && $product->has_child() ){

			}
			$woo_ver = WC()->version;
			if($woo_ver < "3.0.0"){
				$product_id = $product->id;
			}
			else{
				$product_id = $product->get_id();
			}
			$today_date = date_i18n("Y-m-d");
			$check_disbale = get_post_meta($product_id, 'mwb_product_purchase_through_point_disable', 'no');
			if(empty($check_disbale)){
				$check_disbale = 'no';
			}
			$_product = wc_get_product( $product_id );
			$product_is_variable = $this->mwb_wpr_check_whether_product_is_variable($_product);	
			$price = $_product->get_price();
			$user_ID = get_current_user_ID();
			$get_points = (int)get_user_meta($user_ID, 'mwb_wpr_points', true);
			$user_level = get_user_meta($user_ID,'membership_level',true);
			$mwb_wpr_mem_expr = get_user_meta($user_ID,'membership_expiration',true);
			$general_settings = get_option('mwb_wpr_settings_gallery',true);
			$enable_purchase_points = isset($general_settings['enable_purchase_points']) ? intval($general_settings['enable_purchase_points']) : 0;
			$mwb_wpr_restrict_pro_by_points = isset($general_settings['mwb_wpr_restrict_pro_by_points']) ? intval($general_settings['mwb_wpr_restrict_pro_by_points']) : 0;
			$mwb_wpr_purchase_product_text = isset($general_settings['mwb_wpr_purchase_product_text']) ? $general_settings['mwb_wpr_purchase_product_text'] : __('Use your Points for purchasing this Product',MWB_WPR_Domain);
			$mwb_wpr_purchase_points = (isset($general_settings['mwb_wpr_purchase_points']) && $general_settings['mwb_wpr_purchase_points'] != null) ? $general_settings['mwb_wpr_purchase_points'] : 1;
			$new_price = 1;
			$mwb_wpr_product_purchase_price = (isset($general_settings['mwb_wpr_product_purchase_price']) && $general_settings['mwb_wpr_product_purchase_price'] != null) ? intval($general_settings['mwb_wpr_product_purchase_price']) : 1;
			$membership_settings_array = get_option('mwb_wpr_membership_settings',true);
			$mwb_wpr_membership_roles = isset($membership_settings_array['membership_roles']) && !empty($membership_settings_array['membership_roles']) ? $membership_settings_array['membership_roles'] : array();
			$mwb_wpr_categ_list = is_array($general_settings['mwb_wpr_restrictions_for_purchasing_cat']) && !empty($general_settings['mwb_wpr_restrictions_for_purchasing_cat']) ? $general_settings['mwb_wpr_restrictions_for_purchasing_cat'] : array();
			if( $enable_purchase_points && !$product_is_variable)
			{	
				if(!$mwb_wpr_restrict_pro_by_points && $check_disbale == 'no'){
					if(isset($user_level) && !empty($user_level)){	
						if( isset( $mwb_wpr_mem_expr ) && !empty( $mwb_wpr_mem_expr ) && $today_date <= $mwb_wpr_mem_expr ){
							if(is_array($mwb_wpr_membership_roles[$user_level]) && !empty($mwb_wpr_membership_roles[$user_level])){	
								if(is_array($mwb_wpr_membership_roles[$user_level]['Product']) && !empty($mwb_wpr_membership_roles[$user_level]['Product'])){
									if(in_array($product_id, $mwb_wpr_membership_roles[$user_level]['Product']) && !$this->check_exclude_sale_products($_product)){	
										$new_price = $price - ($price * $mwb_wpr_membership_roles[$user_level]['Discount'])/100;
									}
									else{	
										$new_price = $_product->get_price();
									}
								}
								else
								{
									$terms = get_the_terms ( $product_id, 'product_cat' );
									if(is_array($terms) && !empty($terms)){
										foreach ( $terms as $term ) {
											$cat_id = $term->term_id;
											$parent_cat = $term->parent;
											if( (in_array( $cat_id, $mwb_wpr_membership_roles[$user_level]['Prod_Categ'] ) || in_array( $parent_cat, $mwb_wpr_membership_roles[$user_level]['Prod_Categ'] )) && !$this->check_exclude_sale_products($_product) ){	
												$new_price = $price - ($price * $mwb_wpr_membership_roles[$user_level]['Discount'])/100;
												break;
											}
											else{
												$new_price = $_product->get_price();
											}
										}
									}
								}
								$points_calculation =ceil(($new_price*$mwb_wpr_purchase_points)/$mwb_wpr_product_purchase_price);
								if($points_calculation <= $get_points){
									?>	
									<label for="mwb_wpr_pro_cost_to_points">
										<input type="checkbox" name="mwb_wpr_pro_cost_to_points" id="mwb_wpr_pro_cost_to_points" class="input-text" value="<?php echo $points_calculation;?>"> <?php echo $mwb_wpr_purchase_product_text;?>
									</label>
									<input type="hidden" name="mwb_wpr_hidden_points" class="mwb_wpr_hidden_points" value="<?php echo $points_calculation;?>">
									<p class="mwb_wpr_purchase_pro_point"><?php _e('Spend ',MWB_WPR_Domain); echo '<span class=mwb_wpr_when_variable_pro>'.$points_calculation.'</span>'; _e(' Points for Purchasing this Product for Single Quantity',MWB_WPR_Domain)?></p>
									<span class="mwb_wpr_notice"></span>
									<div class="mwb_wpr_enter_some_points" style="display: none;">
										<input type="number" name="mwb_wpr_some_custom_points" id="mwb_wpr_some_custom_points" value="<?php echo $points_calculation;?>">
									</div>
									<?php
								}
								else{
									$extra_need = $points_calculation - $get_points; ?>

									<p class="mwb_wpr_purchase_pro_point"><?php _e('You need extra ',MWB_WPR_Domain); echo '<span class=mwb_wpr_when_variable_pro>'.$extra_need.'</span>'; _e(' Points for get this product for free',MWB_WPR_Domain)?></p>
									<?php	
								}		
							}
						}
						else{
							$points_calculation =ceil(($price*$mwb_wpr_purchase_points)/$mwb_wpr_product_purchase_price);
							if($points_calculation <= $get_points){
								?>
								<label for="mwb_wpr_pro_cost_to_points">
									<input type="checkbox" name="mwb_wpr_pro_cost_to_points" id="mwb_wpr_pro_cost_to_points" class="input-text" value="<?php echo $points_calculation;?>"> <?php echo $mwb_wpr_purchase_product_text;?>
								</label>
								<p class="mwb_wpr_purchase_pro_point"><?php _e('Spend ',MWB_WPR_Domain); echo '<span class=mwb_wpr_when_variable_pro>'.$points_calculation.'</span>'; _e(' Points for Purchasing this Product for Single Quantity',MWB_WPR_Domain)?></p>
								<input type="hidden" name="mwb_wpr_hidden_points" class="mwb_wpr_hidden_points" value="<?php echo $points_calculation;?>">
								<span class="mwb_wpr_notice"></span>
								<div class="mwb_wpr_enter_some_points" style="display: none;">
									<input type="number" name="mwb_wpr_some_custom_points" id="mwb_wpr_some_custom_points" value="<?php echo $points_calculation;?>">
								</div>
								<?php
							}
							else{
								$extra_need = $points_calculation - $get_points; ?>
								<p class="mwb_wpr_purchase_pro_point"><?php _e('You need extra  ',MWB_WPR_Domain); echo '<span class=mwb_wpr_when_variable_pro>'.$extra_need.'</span>'; _e(' Points for get this product for free',MWB_WPR_Domain)?></p>
								<?php	
							}
						}
					}
					else{
						$points_calculation =ceil(($price*$mwb_wpr_purchase_points)/$mwb_wpr_product_purchase_price);
						if($points_calculation <= $get_points){
							?>
							<label for="mwb_wpr_pro_cost_to_points">
								<input type="checkbox" name="mwb_wpr_pro_cost_to_points" id="mwb_wpr_pro_cost_to_points" class="input-text" value="<?php echo $points_calculation;?>"> <?php echo $mwb_wpr_purchase_product_text;?>
							</label>
							<p class="mwb_wpr_purchase_pro_point"><?php _e('Spend ',MWB_WPR_Domain); echo '<span class=mwb_wpr_when_variable_pro>'.$points_calculation.'</span>'; _e(' Points for Purchasing this Product for Single Quantity',MWB_WPR_Domain)?></p>
							<input type="hidden" name="mwb_wpr_hidden_points" class="mwb_wpr_hidden_points" value="<?php echo $points_calculation;?>">
							<span class="mwb_wpr_notice"></span>
							<div class="mwb_wpr_enter_some_points" style="display: none;">
								<input type="number" name="mwb_wpr_some_custom_points" id="mwb_wpr_some_custom_points" value="<?php echo $points_calculation;?>">
							</div>
							<?php
						}
						else{
							$extra_need = $points_calculation - $get_points; ?>
							<p class="mwb_wpr_purchase_pro_point"><?php _e('You need extra  ',MWB_WPR_Domain); echo '<span class=mwb_wpr_when_variable_pro>'.$extra_need.'</span>'; _e(' Points for get this product for free',MWB_WPR_Domain)?></p>
							<?php	
						}
					}
				}
				else{	
					if($check_disbale == 'no'){
						$terms = get_the_terms ( $product_id, 'product_cat' );
						if(is_array($terms) && !empty($terms)){
							foreach ( $terms as $term ) {
								$cat_id = $term->term_id;
								$parent_cat = $term->parent;
								if(in_array($cat_id, $mwb_wpr_categ_list) || in_array($parent_cat, $mwb_wpr_categ_list)){	
									if(isset($user_level) && !empty($user_level)){	
										if( isset( $mwb_wpr_mem_expr ) && !empty( $mwb_wpr_mem_expr ) && $today_date <= $mwb_wpr_mem_expr ){
											if(is_array($mwb_wpr_membership_roles[$user_level]) && !empty($mwb_wpr_membership_roles[$user_level])){	
												if(is_array($mwb_wpr_membership_roles[$user_level]['Product']) && !empty($mwb_wpr_membership_roles[$user_level]['Product'])){
													if(in_array($product_id, $mwb_wpr_membership_roles[$user_level]['Product']) && !$this->check_exclude_sale_products($_product)){	
														
														$new_price = $price - ($price * $mwb_wpr_membership_roles[$user_level]['Discount'])/100;
													}
													else{	
														$new_price = $_product->get_price();
													}
												}
												else {
													$terms = get_the_terms ( $product_id, 'product_cat' );
													if(is_array($terms) && !empty($terms))
													{
														// print_r($terms);
														foreach ( $terms as $term ) 
														{
															$cat_id = $term->term_id;
															$parent_cat = $term->parent;
															if( (in_array($cat_id, $mwb_wpr_membership_roles[$user_level]['Prod_Categ']) || in_array($parent_cat, $mwb_wpr_membership_roles[$user_level]['Prod_Categ'])) && !$this->check_exclude_sale_products($_product) ){	
																$new_price = $price - ($price * $mwb_wpr_membership_roles[$user_level]['Discount'])/100;
																break;
															}
															else{
																$new_price = $_product->get_price();
															}
														}
													}
												}
												$points_calculation =ceil(($new_price*$mwb_wpr_purchase_points)/$mwb_wpr_product_purchase_price);
												
												if($points_calculation <= $get_points)
												{
													
													?>
													<label for="mwb_wpr_pro_cost_to_points">
														<input type="checkbox" name="mwb_wpr_pro_cost_to_points" id="mwb_wpr_pro_cost_to_points" class="input-text" value="<?php echo $points_calculation;?>"> <?php echo $mwb_wpr_purchase_product_text;?>
													</label>
													<p class="mwb_wpr_purchase_pro_point"><?php _e('Spend ',MWB_WPR_Domain); echo '<span class=mwb_wpr_when_variable_pro>'.$points_calculation.'</span>'; _e(' Points for Purchasing this Product for Single Quantity',MWB_WPR_Domain)?></p>
													<input type="hidden" name="mwb_wpr_hidden_points" class="mwb_wpr_hidden_points" value="<?php echo $points_calculation;?>">
													<span class="mwb_wpr_notice"></span>
													<div class="mwb_wpr_enter_some_points" style="display: none;">
														<input type="number" name="mwb_wpr_some_custom_points" id="mwb_wpr_some_custom_points" value="<?php echo $points_calculation;?>">
													</div>
													<?php
												}
												else
												{
													$extra_need = $points_calculation - $get_points; ?>

													<p class="mwb_wpr_purchase_pro_point"><?php _e('You need extra ',MWB_WPR_Domain); echo '<span class=mwb_wpr_when_variable_pro>'.$extra_need.'</span>'; _e(' Points for get this product for free',MWB_WPR_Domain)?></p>
													<?php	
												}		
											}
										}
										else
										{
											$points_calculation =ceil(($price*$mwb_wpr_purchase_points)/$mwb_wpr_product_purchase_price);
											if($points_calculation <= $get_points)
											{
												?>
												<label for="mwb_wpr_pro_cost_to_points">
													<input type="checkbox" name="mwb_wpr_pro_cost_to_points" id="mwb_wpr_pro_cost_to_points" class="input-text" value="<?php echo $points_calculation;?>"> <?php echo $mwb_wpr_purchase_product_text;?>
												</label>
												<p class="mwb_wpr_purchase_pro_point"><?php _e('Spend ',MWB_WPR_Domain); echo '<span class=mwb_wpr_when_variable_pro>'.$points_calculation.'</span>'; _e(' Points for Purchasing this Product for Single Quantity',MWB_WPR_Domain)?></p>
												<input type="hidden" name="mwb_wpr_hidden_points" class="mwb_wpr_hidden_points" value="<?php echo $points_calculation;?>">
												<span class="mwb_wpr_notice"></span>
												<div class="mwb_wpr_enter_some_points" style="display: none;">
													<input type="number" name="mwb_wpr_some_custom_points" id="mwb_wpr_some_custom_points" value="<?php echo $points_calculation;?>">
												</div>
												<?php
											}
											else
											{
												$extra_need = $points_calculation - $get_points; ?>
												<p class="mwb_wpr_purchase_pro_point"><?php _e('You need extra ',MWB_WPR_Domain); echo '<span class=mwb_wpr_when_variable_pro>'.$extra_need.'</span>'; _e(' Points for get this product for free',MWB_WPR_Domain)?></p>
												<?php	
											}
										}
										
									}
									else
									{
										$points_calculation =ceil(($price*$mwb_wpr_purchase_points)/$mwb_wpr_product_purchase_price);
										if($points_calculation <= $get_points)
										{
											?>
											<label for="mwb_wpr_pro_cost_to_points">
												<input type="checkbox" name="mwb_wpr_pro_cost_to_points" id="mwb_wpr_pro_cost_to_points" class="input-text" value="<?php echo $points_calculation;?>"> <?php echo $mwb_wpr_purchase_product_text;?>
											</label>
											<p class="mwb_wpr_purchase_pro_point"><?php _e('Spend ',MWB_WPR_Domain); echo '<span class=mwb_wpr_when_variable_pro>'.$points_calculation.'</span>'; _e(' Points for Purchasing this Product for Single Quantity',MWB_WPR_Domain)?></p>
											<input type="hidden" name="mwb_wpr_hidden_points" class="mwb_wpr_hidden_points" value="<?php echo $points_calculation;?>">
											<span class="mwb_wpr_notice"></span>
											<div class="mwb_wpr_enter_some_points" style="display: none;">
												<input type="number" name="mwb_wpr_some_custom_points" id="mwb_wpr_some_custom_points" value="<?php echo $points_calculation;?>">
											</div>
											<?php
										}
										else
										{
											$extra_need = $points_calculation - $get_points; ?>
											<p class="mwb_wpr_purchase_pro_point"><?php _e('You need extra ',MWB_WPR_Domain); echo '<span class=mwb_wpr_when_variable_pro>'.$extra_need.'</span>'; _e(' Points for get this product for free',MWB_WPR_Domain)?></p>
											<?php	
										}
									}
									break;
								}
							}
						}
					}
				}
			}else if($enable_purchase_points && $product_is_variable){
				echo '<div class="mwb_wpr_variable_pro_pur_using_point" style="display: none;"></div>';
			}	
		}

		/**
		 * The function for appends the required/custom message for users to let them know how many points they are going to earn/deduct
		 * 
		 * @name mwb_wpr_woocommerce_thankyou
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wpr_woocommerce_thankyou($thankyou_msg, $order)
		{	
			$mwb_wpr_thnku_order_msg = get_option("mwb_wpr_thnku_order_msg",false);
			$mwb_wpr_thnku_order_msg_usin_points = get_option("mwb_wpr_thnku_order_msg_usin_points",false);
			$item_points = 0;
			$purchasing_points = 0;
			$mwb_wpr_coupon_conversion_value=get_option('mwb_wpr_coupons_gallery',array());
			$mwb_wpr_coupon_conversion_price=isset($mwb_wpr_coupon_conversion_value['mwb_wpr_coupon_conversion_price']) ? $mwb_wpr_coupon_conversion_value['mwb_wpr_coupon_conversion_price'] : 1 ;
			$mwb_wpr_coupon_conversion_points=isset($mwb_wpr_coupon_conversion_value['mwb_wpr_coupon_conversion_points']) ? $mwb_wpr_coupon_conversion_value['mwb_wpr_coupon_conversion_points'] : 1 ;
			$order_id = $order->get_order_number();
			$user_id = $order->get_user_id();
			$get_points = (int)get_user_meta($user_id, 'mwb_wpr_points', true);
			foreach( $order->get_items() as $item_id => $item )
			{
				$woo_ver = WC()->version;
				if($woo_ver < "3.0.0")
				{	
					$item_quantity = $order->get_item_meta($item_id, '_qty', true);
					if(isset($item['item_meta']['Points']) && !empty($item['item_meta']['Points'])){
						$item_points = (int)$item['item_meta']['Points'][0];
						$thankyou_msg .= $mwb_wpr_thnku_order_msg;
						$thankyou_msg = str_replace('[POINTS]', $item_points, $thankyou_msg);
						$thankyou_msg = str_replace('[TOTALPOINT]', $get_points, $thankyou_msg);
					}
					if(isset($item['item_meta']['Purchasing Option']) && !empty($item['item_meta']['Purchasing Option'])){
						$purchasing_points = (int)$item['item_meta']['Purchasing Option'][0] * $item_quantity ;
						$thankyou_msg .= $mwb_wpr_thnku_order_msg_usin_points;
						$thankyou_msg = str_replace('[POINTS]', $purchasing_points, $thankyou_msg);
						$thankyou_msg = str_replace('[TOTALPOINT]', $get_points, $thankyou_msg);
					}
				}
				else
				{	
					$item_quantity = wc_get_order_item_meta($item_id, '_qty', true);
					$mwb_wpr_items=$item->get_meta_data();
					foreach ($mwb_wpr_items as $key => $mwb_wpr_value) 
					{
						if(isset($mwb_wpr_value->key) && !empty($mwb_wpr_value->key) && ($mwb_wpr_value->key=='Points') )
						{	
							$item_points += (int)$mwb_wpr_value->value;
							$thankyou_msg .= $mwb_wpr_thnku_order_msg;
							$thankyou_msg = str_replace('[POINTS]', $item_points, $thankyou_msg);
							$thankyou_msg = str_replace('[TOTALPOINT]', $get_points, $thankyou_msg);
						}
						if(isset($mwb_wpr_value->key) && !empty($mwb_wpr_value->key) && ($mwb_wpr_value->key=='Purchasing Option') )
						{	
							$purchasing_points += (int)$mwb_wpr_value->value * $item_quantity ;
							$thankyou_msg .= $mwb_wpr_thnku_order_msg_usin_points;
							$thankyou_msg = str_replace('[POINTS]', $purchasing_points, $thankyou_msg);
							$thankyou_msg = str_replace('[TOTALPOINT]', $get_points, $thankyou_msg);
						}
					}
				}
			}
			$item_conversion_id_set = get_post_meta($order_id, "$order_id#item_conversion_id", false);
			$order_total=$order->get_total();
			$order_total = str_replace( wc_get_price_decimal_separator(), '.', strval( $order_total ) );
			$points_calculation =ceil(($order_total*$mwb_wpr_coupon_conversion_points)/$mwb_wpr_coupon_conversion_price);
			if( isset( $item_conversion_id_set[0] ) && !empty( $item_conversion_id_set[0] ) && $item_conversion_id_set[0]=='set' )
			{
				$item_points += $points_calculation;
				$thankyou_msg .= $mwb_wpr_thnku_order_msg;
				$thankyou_msg = str_replace('[POINTS]', $item_points, $thankyou_msg);
				$thankyou_msg = str_replace('[TOTALPOINT]', $get_points, $thankyou_msg);
			}

			return $thankyou_msg;
		}

		/**
		 * The function is for let the meta keys translatable
		 * 
		 * @name mwb_wpr_woocommerce_order_item_display_meta_key
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wpr_woocommerce_order_item_display_meta_key($display_key){
			if($display_key == 'Points'){
				$display_key = __('Points',MWB_WPR_Domain);
			}
			if($display_key == 'Purchasing Option'){
				$display_key = __('Purchasing Option',MWB_WPR_Domain);
			}

			return $display_key;
		}
		/**
		 * The function is for share the points to other member for same site
		 * 
		 * @name mwb_wpr_sharing_point_to_other
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wpr_sharing_point_to_other(){
			check_ajax_referer( 'mwb-wpr-verify-nonce', 'mwb_nonce' );
			$response['result'] = false;
			$response['message'] = __("Error during Point Sharing. Try Again!",MWB_WPR_Domain);
			$mwb_wpr_shared_point = (int)sanitize_post($_POST['shared_point']);
			$user_id = sanitize_post($_POST['user_id']);
			$user=get_user_by('ID',$user_id);
			$sender_email=$user->user_email;
			$mwb_wpr_email = sanitize_post($_POST['email_id']);
			if(isset($user_id) && !empty($user_id)  && isset($mwb_wpr_shared_point) && !empty($mwb_wpr_shared_point)){
				if(isset($mwb_wpr_email) && !empty($mwb_wpr_email)){
					$providers_points = (int)get_user_meta($user_id,'mwb_wpr_points',true);
					$mwb_wpr_receiver = get_user_by( 'email', $mwb_wpr_email );
					$mwb_wpr_receiver_id = $mwb_wpr_receiver->data->ID;
					if(isset($mwb_wpr_receiver) && !empty($mwb_wpr_receiver)){
						if($providers_points >= $mwb_wpr_shared_point){
							
							$receivers_points = (int)get_user_meta($mwb_wpr_receiver_id,'mwb_wpr_points',true);
							$receivers_updated_point = $receivers_points + $mwb_wpr_shared_point;
							$receiver_point_detail = get_user_meta($mwb_wpr_receiver_id, 'points_details',true);

							$today_date = date_i18n("Y-m-d h:i:sa");

							if(isset($receiver_point_detail['Receiver_point_details']) && !empty($receiver_point_detail['Receiver_point_details'])){
								$receiver_array = array();
								$receiver_array = array(
									'Receiver_point_details'=>$mwb_wpr_shared_point,
									'date'=>$today_date,
									'received_by'=>$user_id);
								$receiver_point_detail['Receiver_point_details'][] = $receiver_array;
							}
							else{
								if(!is_array($receiver_point_detail)){
									$receiver_point_detail = array();
								}
								$receiver_array = array(
									'Receiver_point_details'=>$mwb_wpr_shared_point,
									'date'=>$today_date,
									'received_by'=>$user_id);
								$receiver_point_detail['Receiver_point_details'][] = $receiver_array;
							}
							update_user_meta( $mwb_wpr_receiver_id, 'points_details', $receiver_point_detail );
							update_user_meta($mwb_wpr_receiver_id,'mwb_wpr_points',$receivers_updated_point);
							$providers_updated_point = $providers_points - $mwb_wpr_shared_point;
							$sender_point_detail = get_user_meta($user_id, 'points_details',true);
							if(isset($sender_point_detail['Sender_point_details']) && !empty($sender_point_detail['Sender_point_details'])){
								$sender_array = array();
								$sender_array = array(
									'Sender_point_details'=>$mwb_wpr_shared_point,
									'date'=>$today_date,
									'give_to'=>$mwb_wpr_receiver_id);
								$sender_point_detail['Sender_point_details'][] = $sender_array;
							}
							else{
								if(!is_array($sender_point_detail)){
									$sender_point_detail = array();
								}
								$sender_array = array(
									'Sender_point_details'=>$mwb_wpr_shared_point,
									'date'=>$today_date,
									'give_to'=>$mwb_wpr_receiver_id);
								$sender_point_detail['Sender_point_details'][] = $sender_array;
							}
							update_user_meta( $user_id, 'points_details', $sender_point_detail );
							update_user_meta($user_id,'mwb_wpr_points',$providers_updated_point);
							$available_points = get_user_meta($user_id,'mwb_wpr_points',true);
							$mwb_wpr_notificatin_array=get_option('mwb_wpr_notificatin_array',true);
							if(is_array($mwb_wpr_notificatin_array) && !empty($mwb_wpr_notificatin_array))
							{
								$total_points=$receivers_updated_point;
								$mwb_wpr_notificatin_enable=isset($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) ? intval($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) : 0;
								$mwb_wpr_email_subject=isset($mwb_wpr_notificatin_array['mwb_wpr_point_sharing_subject'])? $mwb_wpr_notificatin_array['mwb_wpr_point_sharing_subject'] :__('Received Points Successfully!!',MWB_WPR_Domain);
								$mwb_wpr_email_discription=isset($mwb_wpr_notificatin_array['mwb_wpr_point_sharing_description']) ? $mwb_wpr_notificatin_array['mwb_wpr_point_sharing_description'] :__('You have received point [RECEIVEDPOINT]',MWB_WPR_Domain);
								$mwb_wpr_email_discription=str_replace("[RECEIVEDPOINT]",$mwb_wpr_shared_point,$mwb_wpr_email_discription);
								$mwb_wpr_email_discription=str_replace("[SENDEREMAIL]",$sender_email,$mwb_wpr_email_discription);
								$mwb_wpr_email_discription=str_replace("[TOTALPOINTS]",$total_points,$mwb_wpr_email_discription);
								$user = get_user_by('email',$mwb_wpr_email);
								$user_name = $user->user_firstname;
								$mwb_wpr_email_discription = str_replace("[USERNAME]",$user_name,$mwb_wpr_email_discription);
								if($mwb_wpr_notificatin_enable)
								{	
									$headers = array('Content-Type: text/html; charset=UTF-8');
									wc_mail($mwb_wpr_email,$mwb_wpr_email_subject,$mwb_wpr_email_discription,$headers);
								}
							}
							$response['result'] = true;
							$response['message'] = __("Points assigned succesfuly",MWB_WPR_Domain);
							$response['available_points'] = $available_points;
							
						}else{
							
							$response['result'] = false;
							$response['message'] = __("Entered Point should be less than your Total Point",MWB_WPR_Domain);
						}
					}else{
						
						$response['result'] = false;
						$response['message'] = __("Please Enter Valid Email",MWB_WPR_Domain);
					}
				}
				else{
					
					$response['result'] = false;
					$response['message'] = __("Please Enter Email",MWB_WPR_Domain);
				}
			}
			else{
				
				$response['result'] = false;
				$response['message'] = __("Please fill Required feilds",MWB_WPR_Domain);
			}
			echo json_encode($response);
			wp_die();
		}

		/**
		 * The function is convert the points and add this to in the ofrm of Fee(add_fee)
		 * 
		 * @name mwb_wpr_woocommerce_cart_calculate_fees
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wpr_woocommerce_cart_calculate_fees($cart){
			$woo_ver = WC()->version;
			$general_settings = get_option('mwb_wpr_settings_gallery',true);
			$enable_purchase_points = isset($general_settings['enable_purchase_points']) ? intval($general_settings['enable_purchase_points']) : 0;
			$mwb_wpr_purchase_points = (isset($general_settings['mwb_wpr_purchase_points']) && $general_settings['mwb_wpr_purchase_points'] != null) ? $general_settings['mwb_wpr_purchase_points'] : 1;
			$new_price = 1;
			$mwb_wpr_discount_bcz_pnt = 0;
			$mwb_wpr_pnt_fee_added = false;
			$points_calculation = 0;
			$mwb_wpr_product_purchase_price = (isset($general_settings['mwb_wpr_product_purchase_price']) && $general_settings['mwb_wpr_product_purchase_price'] != null) ? intval($general_settings['mwb_wpr_product_purchase_price']) : 1;
			$user_id = get_current_user_ID();
			$mwb_wpr_mem_expr = get_user_meta($user_id,'membership_expiration',true);
			$get_points = (int)get_user_meta($user_id, 'mwb_wpr_points', true);
			if(!empty($cart))
			{	
				foreach ( $cart->cart_contents as $key => $value ){
					if(!empty($value)){
						$new_price = '';
						$today_date = date_i18n("Y-m-d");
						$product_id = $value['product_id'];
						$pro_quant = $value['quantity'];
						$_product = wc_get_product( $product_id );
						$reg_price = $_product->get_price();
						if($enable_purchase_points){
							if(isset($value['product_meta']['meta_data']['pro_purchase_by_points']) && !empty($value['product_meta']['meta_data']['pro_purchase_by_points']))
							{	
								$original_price = $_product->get_price();
								$original_price = $pro_quant * $original_price;
								$points_calculation +=ceil(($original_price*$mwb_wpr_purchase_points)/$mwb_wpr_product_purchase_price);
								$mwb_wpr_about_to_pay = ($value['product_meta']['meta_data']['pro_purchase_by_points']/$mwb_wpr_purchase_points*$mwb_wpr_product_purchase_price);
								$mwb_wpr_discount_bcz_pnt = $mwb_wpr_discount_bcz_pnt + $mwb_wpr_about_to_pay;
								$mwb_wpr_pnt_fee_added = true;
							}
						}
					}

				}
				if($get_points > 0 && $mwb_wpr_pnt_fee_added)
				{	
					//Testing
					$convert_in_point = ($mwb_wpr_discount_bcz_pnt * $mwb_wpr_purchase_points)/$mwb_wpr_product_purchase_price;
					if($convert_in_point > $get_points){
						$mwb_wpr_about_to_pay = (int)($get_points/$mwb_wpr_purchase_points*$mwb_wpr_product_purchase_price);
						$cart->add_fee( 'Point Discount', -$mwb_wpr_about_to_pay, true, '' );
					}
					else{
						$cart->add_fee( 'Point Discount', -$mwb_wpr_discount_bcz_pnt, true, '' );
					}
				}
			}
		}
		
		/**
		 * The function is used for append the variable point to the single product page as well as variable product support for purchased through points and for membership product
		 * 
		 * @name mwb_wpr_append_variable_point
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wpr_append_variable_point(){
			$response['result'] = false;
			$response['message'] = __("Error during various variation handling. Try Again!",MWB_WPR_Domain);
			$mwb_wpr_proceed_for_purchase_throgh_point = false;
			$points_calculation = '';$price = '';
			$variation_id = sanitize_post($_POST['variation_id']);
			//print_r($variation_id);
			$general_settings = get_option('mwb_wpr_settings_gallery',true);
			$mwb_wpr_restrict_pro_by_points = isset($general_settings['mwb_wpr_restrict_pro_by_points']) ? intval($general_settings['mwb_wpr_restrict_pro_by_points']) : 0;
			$mwb_wpr_categ_list = is_array($general_settings['mwb_wpr_restrictions_for_purchasing_cat']) && !empty($general_settings['mwb_wpr_restrictions_for_purchasing_cat']) ? $general_settings['mwb_wpr_restrictions_for_purchasing_cat'] : array();
			$user_id = get_current_user_ID();
			if(!empty($variation_id)){
				$user_level = get_user_meta($user_id,'membership_level',true);
				$get_points = (int)get_user_meta($user_id, 'mwb_wpr_points', true);
				$mwb_wpr_mem_expr = get_user_meta($user_id,'membership_expiration',true);
				$membership_settings_array = get_option('mwb_wpr_membership_settings',true);
				$mwb_wpr_membership_roles = isset($membership_settings_array['membership_roles']) && !empty($membership_settings_array['membership_roles']) ? $membership_settings_array['membership_roles'] : array();
				$today_date = date_i18n("Y-m-d");
				$mwb_wpr_purchase_product_text = isset($general_settings['mwb_wpr_purchase_product_text']) ? $general_settings['mwb_wpr_purchase_product_text'] : __('Use your Points for purchasing this Product',MWB_WPR_Domain);
				$mwb_wpr_parent_id = wp_get_post_parent_id($variation_id);
				$general_settings = get_option('mwb_wpr_settings_gallery',true);
				$enable_purchase_points = isset($general_settings['enable_purchase_points']) ? intval($general_settings['enable_purchase_points']) : 0;
				$mwb_wpr_purchase_points = (isset($general_settings['mwb_wpr_purchase_points']) && $general_settings['mwb_wpr_purchase_points'] != null) ? $general_settings['mwb_wpr_purchase_points'] : 1;
				$new_price = 1;
				$mwb_wpr_product_purchase_price = (isset($general_settings['mwb_wpr_product_purchase_price']) && $general_settings['mwb_wpr_product_purchase_price'] != null) ? intval($general_settings['mwb_wpr_product_purchase_price']) : 1;
				$mwb_wpr_restrict_pro_by_points = isset($general_settings['mwb_wpr_restrict_pro_by_points']) ? intval($general_settings['mwb_wpr_restrict_pro_by_points']) : 0;
				if(!empty($mwb_wpr_parent_id) && $mwb_wpr_parent_id > 0){
					$check_enable = get_post_meta($mwb_wpr_parent_id, 'mwb_product_points_enable', 'no');
					$check_disbale = get_post_meta($mwb_wpr_parent_id, 'mwb_product_purchase_through_point_disable', 'no');
					if(empty($check_disbale)){
						$check_disbale = 'no';
					}
					if($check_enable == 'yes'){
						$mwb_wpr_variable_points = (int)get_post_meta( $variation_id,'mwb_wpr_variable_points',true);
						if($mwb_wpr_variable_points > 0){
							$response['result'] = true;
							$response['variable_points'] = $mwb_wpr_variable_points;
							$response['message'] = __("Successfully Assigned!",MWB_WPR_Domain);
							// echo json_encode($response);
							// wp_die();
						}
					}
					if($enable_purchase_points){
						if($mwb_wpr_restrict_pro_by_points){
							$terms = get_the_terms ( $mwb_wpr_parent_id, 'product_cat' );
							if(is_array($terms) && !empty($terms)){
								foreach ( $terms as $term ) {
									$cat_id = $term->term_id;
									$parent_cat = $term->parent;
									if(isset($mwb_wpr_categ_list) && !empty($mwb_wpr_categ_list)){
										if(in_array($cat_id, $mwb_wpr_categ_list) || in_array($parent_cat, $mwb_wpr_categ_list)){
											$mwb_wpr_proceed_for_purchase_throgh_point = true;
											break;
										}
									}
									else{
										$mwb_wpr_proceed_for_purchase_throgh_point = false;
									}
								}
							}							
						}
						else{
							$mwb_wpr_proceed_for_purchase_throgh_point = true;
						}	
					}
					
					$variable_product = wc_get_product( $variation_id );
					$variable_price = $variable_product->get_price();
					if(isset($user_level) && !empty($user_level)){
						if( isset( $mwb_wpr_mem_expr ) && !empty( $mwb_wpr_mem_expr ) && $today_date <= $mwb_wpr_mem_expr )
						{
							if(is_array($mwb_wpr_membership_roles) && !empty($mwb_wpr_membership_roles))
							{
								foreach($mwb_wpr_membership_roles as $roles => $values)
								{	
									if($user_level == $roles)
									{	
										if(is_array($values['Product']) && !empty($values['Product']))
										{
											if(in_array($mwb_wpr_parent_id, $values['Product']) && !$this->check_exclude_sale_products($variable_product))
											{	
												$new_price = $variable_price - ($variable_price * $values['Discount'])/100;
												$price = '<span class="price"><del><span class="woocommerce-Price-amount amount">'.wc_price( $variable_price ).'</del> <ins><span class="woocommerce-Price-amount amount">'.wc_price($new_price).'</span></ins></span>';
												$points_calculation =ceil(($new_price*$mwb_wpr_purchase_points)/$mwb_wpr_product_purchase_price);
											}
											$response['result_price'] = "html";
											$response['variable_price_html'] = $price;
											$mwb_wpr_variable_pro_pur_pnt = '<label for="mwb_wpr_pro_cost_to_points"><input type="checkbox" name="mwb_wpr_pro_cost_to_points" id="mwb_wpr_pro_cost_to_points" class="input-text" value="'.$points_calculation.'">'.$mwb_wpr_purchase_product_text.'</label><input type="hidden" name="mwb_wpr_hidden_points" class="mwb_wpr_hidden_points" value="'.$points_calculation.'"><p class="mwb_wpr_purchase_pro_point">'.__('Spend ',MWB_WPR_Domain).$points_calculation.__(' Points for Purchasing this Product for Single Quantity',MWB_WPR_Domain).'</p><span class="mwb_wpr_notice"></span><div class="mwb_wpr_enter_some_points" style="display: none;"><input type="number" name="mwb_wpr_some_custom_points" id="mwb_wpr_some_custom_points" value="'.$points_calculation.'"></div>';
											if($enable_purchase_points && $mwb_wpr_proceed_for_purchase_throgh_point && $check_disbale == 'no'){
												if($get_points >= $points_calculation){
													$response['result_point'] = "product_purchased_using_point";
													$response['variable_points_cal_html'] = $mwb_wpr_variable_pro_pur_pnt;
												}
												elseif($points_calculation > $get_points){
													$extra_need = $points_calculation - $get_points;
													$mwb_wpr_variable_pro_pur_pnt = '<p class="mwb_wpr_purchase_pro_point">'.__('You need extra ',MWB_WPR_Domain).$extra_need.__(' Points for get this product for free',MWB_WPR_Domain).'</p>';
													$response['result_point'] = "product_purchased_using_point";
													$response['variable_points_cal_html'] = $mwb_wpr_variable_pro_pur_pnt;
												}
											}
										}
										else if(!$this->check_exclude_sale_products($variable_product))
										{
											$terms = get_the_terms ( $mwb_wpr_parent_id, 'product_cat' );
											if(is_array($terms) && !empty($terms))
											{
												foreach ( $terms as $term ) 
												{
													$cat_id = $term->term_id;
													$parent_cat = $term->parent;
													if(in_array($cat_id, $values['Prod_Categ']) || in_array($parent_cat, $values['Prod_Categ'])){	
														$new_price = $variable_price - ($variable_price * $values['Discount'])/100;
														$price = '<span class="price"><del><span class="woocommerce-Price-amount amount">'.wc_price( $variable_price ).'</del> <ins><span class="woocommerce-Price-amount amount">'.wc_price($new_price).'</span></ins></span>';
														$points_calculation =ceil(($new_price*$mwb_wpr_purchase_points)/$mwb_wpr_product_purchase_price);
														

														$response['result_price'] = "html";
														$response['variable_price_html'] = $price;
														$mwb_wpr_variable_pro_pur_pnt = '<label for="mwb_wpr_pro_cost_to_points"><input type="checkbox" name="mwb_wpr_pro_cost_to_points" id="mwb_wpr_pro_cost_to_points" class="input-text" value="'.$points_calculation.'">'.$mwb_wpr_purchase_product_text.'</label><input type="hidden" name="mwb_wpr_hidden_points" class="mwb_wpr_hidden_points" value="'.$points_calculation.'"><p class="mwb_wpr_purchase_pro_point">'.__('Spend ',MWB_WPR_Domain).$points_calculation.__(' Points for Purchasing this Product for Single Quantity',MWB_WPR_Domain).'</p><span class="mwb_wpr_notice"></span><div class="mwb_wpr_enter_some_points" style="display: none;"><input type="number" name="mwb_wpr_some_custom_points" id="mwb_wpr_some_custom_points" value="'.$points_calculation.'"></div>';
														break;
													}
													if($enable_purchase_points && $mwb_wpr_proceed_for_purchase_throgh_point && $check_disbale == 'no'){
														if($get_points >= $points_calculation){
															$response['result_point'] = "product_purchased_using_point";
															$response['variable_points_cal_html'] = $mwb_wpr_variable_pro_pur_pnt;
														}
														elseif($enable_purchase_points && $points_calculation > $get_points){
															$extra_need = $points_calculation - $get_points;
															$mwb_wpr_variable_pro_pur_pnt = '<p class="mwb_wpr_purchase_pro_point">'.__('You need extra ',MWB_WPR_Domain).$extra_need.__(' Points for get this product for free',MWB_WPR_Domain).'</p>';
															$response['result_point'] = "product_purchased_using_point";
															$response['variable_points_cal_html'] = $mwb_wpr_variable_pro_pur_pnt;
														}
													}
												}
											}
										}	
									}
								}
							}
						}
					}
					else{
						$points_calculation =ceil(($variable_price*$mwb_wpr_purchase_points)/$mwb_wpr_product_purchase_price);
						$mwb_wpr_variable_pro_pur_pnt = '<label for="mwb_wpr_pro_cost_to_points"><input type="checkbox" name="mwb_wpr_pro_cost_to_points" id="mwb_wpr_pro_cost_to_points" class="input-text" value="'.$points_calculation.'">'.$mwb_wpr_purchase_product_text.'</label><input type="hidden" name="mwb_wpr_hidden_points" class="mwb_wpr_hidden_points" value="'.$points_calculation.'"><p class="mwb_wpr_purchase_pro_point">'.__('Spend ',MWB_WPR_Domain).$points_calculation.__(' Points for Purchasing this Product for Single Quantity',MWB_WPR_Domain).'</p><span class="mwb_wpr_notice"></span><div class="mwb_wpr_enter_some_points" style="display: none;"><input type="number" name="mwb_wpr_some_custom_points" id="mwb_wpr_some_custom_points" value="'.$points_calculation.'"></div>';
						if($enable_purchase_points && $mwb_wpr_proceed_for_purchase_throgh_point && $check_disbale == 'no'){
							if($get_points >= $points_calculation){
								$response['result_point'] = "product_purchased_using_point";
								$response['variable_points_cal_html'] = $mwb_wpr_variable_pro_pur_pnt;
							}
							elseif($points_calculation > $get_points){
								$extra_need = $points_calculation - $get_points;
								$mwb_wpr_variable_pro_pur_pnt = '<p class="mwb_wpr_purchase_pro_point">'.__('You need extra ',MWB_WPR_Domain).$extra_need.__(' Points for get this product for free',MWB_WPR_Domain).'</p>';
								$response['result_point'] = "product_purchased_using_point";
								$response['variable_points_cal_html'] = $mwb_wpr_variable_pro_pur_pnt;
							}
						}
					}
				}

				//MWB CUSTOM CODE

				$enable_product_purchase_points = get_post_meta($mwb_wpr_parent_id, 'mwb_product_purchase_points_only',true);
				$mwb_product_purchase_value = get_post_meta($variation_id, 'mwb_wpr_variable_points_purchase',true);

				if(isset($enable_product_purchase_points) && $enable_product_purchase_points == 'yes'){
					if(isset($mwb_product_purchase_value) && !empty($mwb_product_purchase_value))
					{	

						//$price = $mwb_product_purchase_value.' Points';
						$response['purchase_pro_pnts_only'] = "purchased_pro_points";
						$response['price_html'] = $mwb_product_purchase_value;

					}
				}			

				//END OF MWB CUSTOM CODE
			}
			echo json_encode($response);
			wp_die();
		}

		// MWB Custom Code
		public function mwb_pro_purchase_points_only(){
			$response['result'] = false;
			$response['message'] = __("Error during various variation handling. Try Again!",MWB_WPR_Domain);
			$variation_id = sanitize_post($_POST['variation_id']);
			$mwb_wpr_parent_id = wp_get_post_parent_id($variation_id);
			$enable_product_purchase_points = get_post_meta($mwb_wpr_parent_id, 'mwb_product_purchase_points_only',true);
			$mwb_product_purchase_value = get_post_meta($variation_id, 'mwb_wpr_variable_points_purchase',true);

			if(isset($enable_product_purchase_points) && $enable_product_purchase_points == 'yes'){
				if(isset($mwb_product_purchase_value) && !empty($mwb_product_purchase_value))
				{	
					$response['result'] = true;
					$response['purchase_pro_pnts_only'] = "purchased_pro_points";
					$response['price_html'] = $mwb_product_purchase_value;
				}
			}
			echo json_encode($response);
			wp_die();

		}

		//	End of MWB Custome Code

		/**
		 * The function is used for checking the product is variable or not?
		 * 
		 * @name mwb_wpr_check_whether_product_is_variable
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wpr_check_whether_product_is_variable($product){
			if(isset($product) && !empty($product)){
				if( $product->is_type( 'variable' ) && $product->has_child() ){
					return true;	
				}
				else{
					return false;
				}
			}
		}

		/**
		 * The function is used for set the cookie for referee
		 * 
		 * @name mwb_wpr_referral_link_using_cookie
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wpr_referral_link_using_cookie(){
			if(!is_admin()){
				if(!is_user_logged_in()){
					$mwb_wpr_ref_link_expiry = get_option("mwb_wpr_ref_link_expiry",1);
					if(isset($_GET['pkey']) && !empty($_GET['pkey'])){
						$referral_link = trim($_GET['pkey']);
						if(isset($mwb_wpr_ref_link_expiry) && !empty($mwb_wpr_ref_link_expiry) && !empty($referral_link)){
							setcookie( 'mwb_wpr_cookie_set', $referral_link, time() + (86400 * $mwb_wpr_ref_link_expiry), "/" );
						}
					}
				}
			}
		}

		/**
		 * The function is used for destroy the cookie
		 * 
		 * @name mwb_wpr_destroy_cookie
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function mwb_wpr_destroy_cookie(){
			if(isset($_COOKIE['mwb_wpr_cookie_set']) && !empty($_COOKIE['mwb_wpr_cookie_set'])){
				setcookie('mwb_wpr_cookie_set', '', time()-3600, '/');
			}
		}

		/**
		 * This function is used to add the html boxes for "Redemption on Cart sub-total"
		 * @name mwb_wgm_woocommerce_cart_coupon
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wpr_woocommerce_cart_coupon(){
			$mwb_wpr_custom_points_on_cart = get_option("mwb_wpr_custom_points_on_cart",0);
			if($mwb_wpr_custom_points_on_cart == 1){
				$user_id = get_current_user_ID();
				$get_points = (int)get_user_meta($user_id,'mwb_wpr_points',true);

				if(isset($user_id) && !empty($user_id)){
					?>
					<div class="mwb_wpr_apply_custom_points">
						<input type="number" name="mwb_cart_points" class="input-text" id="mwb_cart_points" value="" placeholder="<?php esc_attr_e( 'Points', MWB_WPR_Domain ); ?>"/>
						<input type="button" name="mwb_cart_points_apply" data-point="<?php echo $get_points;?>" data-id="<?php echo $user_id;?>" class="button mwb_cart_points_apply" id="mwb_cart_points_apply" value="<?php _e('Apply Points',MWB_WPR_Domain);?>"/>
					</div>	
					<?php
				}
			}
		}

		/**
		 * This function is used to apply fee on cart total
		 * @name mwb_wpr_apply_fee_on_cart_subtotal
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function mwb_wpr_apply_fee_on_cart_subtotal(){
			check_ajax_referer( 'mwb-wpr-verify-nonce', 'mwb_nonce' );
			$response['result'] = false;
			$response['message'] = __("Can not redeem!",MWB_WPR_Domain);
			$user_id = sanitize_post($_POST['user_id']);
			$mwb_cart_points = sanitize_post($_POST['mwb_cart_points']);
			if(isset($user_id) && !empty($user_id)){ 
				$cart = WC()->cart;
				if (session_status() == PHP_SESSION_NONE) {
					session_start();
				}
				if(isset($mwb_cart_points) && !empty($mwb_cart_points)){
					$_SESSION['mwb_cart_points'] = $mwb_cart_points;
					$response['result'] = true;
					$response['message'] = __("Custom Point has been applied Successfully!",MWB_WPR_Domain);
				}else{
					$response['result'] = false;
					$response['message'] = __("Please enter some valid points!",MWB_WPR_Domain);
				}
			}
			echo json_encode($response);
			wp_die();
		}
		
		/**
		 * This function is used to apply custom points on Cart Total
		 * @name mwb_wpr_woocommerce_cart_custom_points
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */		
		public function mwb_wpr_woocommerce_cart_custom_points($cart){
			$user_id = get_current_user_ID();
			$mwb_wpr_custom_points_on_cart = get_option("mwb_wpr_custom_points_on_cart",0);
			if(isset($user_id) && !empty($user_id) && $mwb_wpr_custom_points_on_cart == 1){
				$mwb_wpr_cart_points_rate = get_option("mwb_wpr_cart_points_rate",1);
				$mwb_wpr_cart_price_rate = get_option("mwb_wpr_cart_price_rate",1); 
				
				if(isset($_SESSION['mwb_cart_points']) && !empty($_SESSION['mwb_cart_points'])){
					$_SESSION['mwb_cart_points'] = intval($_SESSION['mwb_cart_points']);
					$mwb_fee_on_cart = ($_SESSION['mwb_cart_points'] * $mwb_wpr_cart_price_rate / $mwb_wpr_cart_points_rate);
					$cart->add_fee( 'Cart Discount', -$mwb_fee_on_cart, true, '' );
				}	
			}
		}

		/**
		 * This function is used to add notices over cart page
		 * @name mwb_wpr_woocommerce_before_cart_contents
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */	
		public function mwb_wpr_woocommerce_before_cart_contents(){
			$mwb_wpr_custom_points_on_cart = get_option("mwb_wpr_custom_points_on_cart",0);
			$mwb_wpr_notification_color = get_option('mwb_wpr_notification_color','#55b3a5');
			$mwb_wpr_cart_points_rate = get_option("mwb_wpr_cart_points_rate",1);
			$mwb_wpr_cart_price_rate = get_option("mwb_wpr_cart_price_rate",1);
			$user_id = get_current_user_ID();
			if($mwb_wpr_custom_points_on_cart == 1 && isset($user_id) && !empty($user_id)){
				?>
				<div class="woocommerce-message"><?php _e('Here is the Discount Rule for applying your points to Cart sub-total',MWB_WPR_Domain) ;?>
					<ul>
						<li><?php echo wc_price($mwb_wpr_cart_price_rate). ' = '.$mwb_wpr_cart_points_rate.__(' Points',MWB_WPR_Domain);?></li>
					</ul>
				</div>
				<div class="woocommerce-error" id="mwb_wpr_cart_points_notice" style="display: none;"></div>
				<div class="woocommerce-message" id="mwb_wpr_cart_points_success" style="display: none;"></div>
				<?php
			}
			if($this->is_order_conversion_enabled()){
				$order_conversion_rate = $this->order_conversion_rate();
				?>
				<div class="woocommerce-message" id="mwb_wpr_order_notice" style="background-color: <?php echo $mwb_wpr_notification_color; ?>">
					<!-- <span class="mwb_wpr_reward"><img src="<?php //echo MWB_WPR_URL.'assets/images/medal_img.png';?>"></span> -->
					<?php _e("Place Order And Earn Something in Return",MWB_WPR_Domain)?>
					
					<p style="background-color: <?php echo $mwb_wpr_notification_color; ?>"><?php _e('Conversion Rate: '); echo wc_price($order_conversion_rate['Value'])." = ".$order_conversion_rate['Points']; _e(' Points',MWB_WPR_Domain);?></p>
					<!-- <span class="mwb_wpr_reward"><img src="<?php //echo MWB_WPR_URL.'assets/images/medal_img.png';?>"></span> -->
				</div>
				<?php
			}
		}

		/**
		 * This function is used to add Remove button along with Cart Discount Fee
		 * @name mwb_wpr_woocommerce_cart_totals_fee_html
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */	
		public function mwb_wpr_woocommerce_cart_totals_fee_html($cart_totals_fee_html, $fee){
			if(isset($fee) && !empty($fee)){
				$fee_name = $fee->name;
				if(isset($fee_name) && $fee_name == 'Cart Discount'){
					$cart_totals_fee_html = $cart_totals_fee_html.'<a href="javascript:;" id="mwb_wpr_remove_cart_point">[Remove]</a>';
				}
			}
			return $cart_totals_fee_html;
		}

		/**
		 * This function is used to Remove Cart Discount Fee
		 * @name mwb_wpr_remove_cart_point
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */	
		public function mwb_wpr_remove_cart_point(){
			check_ajax_referer( 'mwb-wpr-verify-nonce', 'mwb_nonce' );
			$response['result'] = false;
			$response['message'] = __("Failed to Remove Cart Disocunt",MWB_WPR_Domain);
			if(isset($_SESSION['mwb_cart_points']) && $_SESSION['mwb_cart_points']){
				unset($_SESSION['mwb_cart_points']);
				$response['result'] = true;
				$response['message'] = __("Successfully Removed Cart Disocunt",MWB_WPR_Domain);
			}
			echo json_encode($response);
			wp_die();
		}

		/**
		 * This function is used to show the notices for Order Total Points fetaure
		 * @name mwb_wpr_woocommerce_before_main_content
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */	
		public function mwb_wpr_woocommerce_before_main_content(){
			if($this->is_order_conversion_enabled()){
				$mwb_wpr_notification_color = get_option('mwb_wpr_notification_color','#55b3a5');
				$order_conversion_rate = $this->order_conversion_rate();
				?>
				<div class="woocommerce-message" id="mwb_wpr_order_notice" style="background-color:<?php echo $mwb_wpr_notification_color;?>">
					<!-- <span class="mwb_wpr_reward"><img src="<?php //echo MWB_WPR_URL.'assets/images/medal_img.png';?>"></span> -->
					<?php _e("Place Order And Earn Something in Return",MWB_WPR_Domain)?>
					
					<p style="background-color:<?php echo $mwb_wpr_notification_color;?>"><?php _e('Conversion Rate: '); echo wc_price($order_conversion_rate['Value'])." = ".$order_conversion_rate['Points']; _e(' Points',MWB_WPR_Domain);?></p>
					<!-- <span class="mwb_wpr_reward"><img src="<?php //echo MWB_WPR_URL.'assets/images/medal_img.png';?>"></span> -->
				</div>
				<?php
			}
		}

		/**
		 * This function is used to check the order conversion feature is enabled or not
		 * @name is_order_conversion_enabled
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */	
		function is_order_conversion_enabled(){
			$enable = false;
			$coupon_settings_array=get_option('mwb_wpr_coupons_gallery',array());
			$is_order_conversion_enable = isset($coupon_settings_array['mwb_wpr_coupon_conversion_enable']) ? $coupon_settings_array['mwb_wpr_coupon_conversion_enable'] : 0;
			if($is_order_conversion_enable){
				$enable = true;
			}
			return $enable;
		}

		/**
		 * This function is used to return you the conversion rate of Order Total
		 * @name order_conversion_rate
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */	
		function order_conversion_rate() {
			$user_id = get_current_user_id();
			$selected_role=get_user_meta($user_id,'membership_level',true);
			if(!empty($selected_role) && isset($selected_role)) {
				$membership_settings_array = get_option('mwb_wpr_membership_settings',true);
				$mwb_wpr_membership_roles = isset($membership_settings_array['membership_roles']) && !empty($membership_settings_array['membership_roles']) ? $membership_settings_array['membership_roles'] : array();
				if(!empty($mwb_wpr_membership_roles) && is_array($mwb_wpr_membership_roles)) {

					foreach( $mwb_wpr_membership_roles as $roles => $values) {
						if($roles == $selected_role) {

							$order_conversion_rate_points = $values['points_con'];
							$order_conversion_rate_value = $values['price_con'];
						}
					}
				}
				
			}
			else {

				$coupon_settings_array=get_option('mwb_wpr_coupons_gallery',array());
				$order_conversion_rate_value=isset($coupon_settings_array['mwb_wpr_coupon_conversion_price']) ? $coupon_settings_array['mwb_wpr_coupon_conversion_price'] : 1 ;
				$order_conversion_rate_points=isset($coupon_settings_array['mwb_wpr_coupon_conversion_points']) ? $coupon_settings_array['mwb_wpr_coupon_conversion_points'] : 1 ;
			}

			$order_conversion_rate = array('Value'=>$order_conversion_rate_value,
				'Points' => $order_conversion_rate_points);
			return $order_conversion_rate;
		}

		/**
		 * This function is used to handle the point on cancellation of orders dynamically
		 * @name mwb_wpr_woocommerce_order_status_cancel
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */	
		public function mwb_wpr_woocommerce_order_status_cancel($order_id, $old_status, $new_status){
			if($old_status != $new_status){
				if($new_status == 'cancelled'){
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
					$deduct_bcz_cancel = get_user_meta($user_id, 'points_details',true);
					$today_date = date_i18n("Y-m-d h:i:sa");
					$total_points = '';

					foreach( $order->get_items() as $item_id => $item ){	
						$woo_ver = WC()->version;
						$item_quantity = wc_get_order_item_meta($item_id, '_qty', true);
						$mwb_wpr_items = $item->get_meta_data();
						if(is_array($mwb_wpr_items) && !empty($mwb_wpr_items))
						{
							foreach ($mwb_wpr_items as $key => $mwb_wpr_value) 
							{	
								if(isset($mwb_wpr_value->key) && !empty($mwb_wpr_value->key) && ($mwb_wpr_value->key == 'Points'))
								{	
									$is_cancelled = wc_get_order_item_meta($item_id, 'cancel points', true);
									if( !isset( $is_cancelled ) || $is_cancelled != 'yes' )
									{	
										$get_points = (int)get_user_meta($user_id, 'mwb_wpr_points', true);
										$deduct_point = $mwb_wpr_value->value;
										$total_points = $get_points - $deduct_point;
										if(isset($deduct_bcz_cancel['deduct_bcz_cancel']) && !empty($deduct_bcz_cancel['deduct_bcz_cancel']))
										{
											$deduction_arr = array(
												'deduct_bcz_cancel'=>$deduct_point,
												'date'=>$today_date);
											$deduct_bcz_cancel['deduct_bcz_cancel'][] = $deduction_arr;
										}
										else
										{	
											if(!is_array($deduct_bcz_cancel)){
												$deduct_bcz_cancel = array();
											}
											$deduction_arr = array(
												'deduct_bcz_cancel'=>$deduct_point,
												'date'=>$today_date);
											$deduct_bcz_cancel['deduct_bcz_cancel'][] = $deduction_arr;
										}
										update_user_meta($user_id,'mwb_wpr_points',$total_points);
										update_user_meta($user_id,'points_details',$deduct_bcz_cancel);
										wc_update_order_item_meta( $item_id, 'cancel points', 'yes', $prev_value = '' );
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
					// for Per Currency Spent Point Conversion (Cancellation)	
					$item_conversion_id_set = get_post_meta($order_id, "$order_id#item_conversion_id", false);
					$is_cancelled = get_post_meta($order_id, 'points_returned', true);
					if( !isset( $is_cancelled ) || $is_cancelled != 'yes' ){
						$order_total = $order->get_total();
						$order_total = str_replace( wc_get_price_decimal_separator(), '.', strval( $order_total ) );
						$points_calculation = ceil(($order_total*$mwb_wpr_coupon_conversion_points)/$mwb_wpr_coupon_conversion_price);
						if( isset( $item_conversion_id_set[0] ) && !empty( $item_conversion_id_set[0] ) && $item_conversion_id_set[0]=='set' )
						{	
							$get_points = (int)get_user_meta($user_id, 'mwb_wpr_points', true);
							$order_total = $order->get_total();
							$refund_amount = $order_total;
							$refund_amount = ceil(($refund_amount*$mwb_wpr_coupon_conversion_points)/$mwb_wpr_coupon_conversion_price);
							$deduct_currency_spent = $refund_amount;
							$remaining_points = $get_points - $deduct_currency_spent;
							if(isset($deduct_bcz_cancel['deduct_currency_pnt_cancel']) && !empty($deduct_bcz_cancel['deduct_currency_pnt_cancel']))
							{	
								$currency_arr = array(
									'deduct_currency_pnt_cancel'=>$deduct_currency_spent,
									'date'=>$today_date);
								$deduct_bcz_cancel['deduct_currency_pnt_cancel'][] = $currency_arr;
							}
							else{	
								if(!is_array($deduct_bcz_cancel)){
									$deduct_bcz_cancel = array();
								}
								$currency_arr = array(
									'deduct_currency_pnt_cancel'=>$deduct_currency_spent,
									'date'=>$today_date);
								$deduct_bcz_cancel['deduct_currency_pnt_cancel'][] = $currency_arr;
							}
							update_user_meta($user_id,'mwb_wpr_points',$remaining_points);
							update_user_meta($user_id,'points_details',$deduct_bcz_cancel);
							update_post_meta( $order_id, 'points_returned', 'yes');
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
					}
					//Return Points on Cancellation Order (Product Purchasedby using Points)
					if(isset($order) && !empty($order)){
						$is_refunded = get_post_meta($order_id, 'points_discount', true);
						if(!isset($is_refunded) || $is_refunded != 'Refunded'){
							$general_settings = get_option('mwb_wpr_settings_gallery',true);
							$mwb_wpr_purchase_points = (isset($general_settings['mwb_wpr_purchase_points']) && $general_settings['mwb_wpr_purchase_points'] != null) ? $general_settings['mwb_wpr_purchase_points'] : 1;
							$mwb_wpr_product_purchase_price = (isset($general_settings['mwb_wpr_product_purchase_price']) && $general_settings['mwb_wpr_product_purchase_price'] != null) ? intval($general_settings['mwb_wpr_product_purchase_price']) : 1;
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
										if(isset($deduct_bcz_cancel['pur_points_cancel']) && !empty($deduct_bcz_cancel['pur_points_cancel']))
										{	
											$return_arr = array(
												'pur_points_cancel'=>$fee_to_point,
												'date'=>$today_date);
											$deduct_bcz_cancel['pur_points_cancel'][] = $return_arr;
										}
										else
										{	
											if(!is_array($deduct_bcz_cancel)){
												$deduct_bcz_cancel = array();
											}
											$return_arr = array(
												'pur_points_cancel'=>$fee_to_point,
												'date'=>$today_date);
											$deduct_bcz_cancel['pur_points_cancel'][] = $return_arr;
										}
										update_user_meta($user_id,'mwb_wpr_points',$total_point);
										update_user_meta($user_id,'points_details',$deduct_bcz_cancel);
										update_post_meta($order_id,'points_discount','Refunded');
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
			}
		}
		/**
		 * This function is used to handle the tax calculation when Fee is applying on Cart
		 * @name mwb_wpr_fee_tax_calculation
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */	
		public function mwb_wpr_fee_tax_calculation( $fee_taxes, $fee, $object){
			if($fee->object->id == 'point-discount' || $fee->object->id == 'cart-discount')
			{
				foreach ($fee_taxes as $key => $value) {
					$fee_taxes[$key] = 0;
				}
			}
			return $fee_taxes;
		}
       // Mwb Custom Work 

		public function mwb_wpr_woocommerce_add_to_cart_validation($validate, $product_id, $quantity)
		{	
			$_product = wc_get_product($product_id);       		
			$enable_product_purchase_points = get_post_meta($product_id, 'mwb_product_purchase_points_only', true);
			$mwb_product_purchase_value = (int)get_post_meta($product_id, 'mwb_points_product_purchase_value', true);
			$prod_type = $_product->get_type();
			$user = wp_get_current_user();
			$user_id = $user->ID;
			$get_points = (int)get_user_meta($user_id, 'mwb_wpr_points', true);       		

			if(isset($enable_product_purchase_points) && $enable_product_purchase_points == 'yes') {
				if(isset($mwb_product_purchase_value) && !empty($mwb_product_purchase_value) && ($prod_type == 'simple'))
				{
					if (!is_user_logged_in())
					{
						$validate = false;
						wc_add_notice( __( 'You must Logged in to purchase this product', MWB_WPR_Domain ), 'error' );
						return $validate;
					}
					else if (($mwb_product_purchase_value*$quantity) > $get_points)
					{	
						$validate = false;
						wc_add_notice( __( "You don't have sufficiant point to purchase this product", MWB_WPR_Domain ), 'error' ); 
						return $validate;      					
					}
				}
				elseif ($this->mwb_wpr_check_whether_product_is_variable($_product)) {
					$variation_id = sanitize_post($_POST['variation_id']);
					$mwb_wpr_parent_id = wp_get_post_parent_id($variation_id);
					$enable_product_purchase_points = get_post_meta($mwb_wpr_parent_id, 'mwb_product_purchase_points_only',true);
					$mwb_product_purchase_value = get_post_meta($variation_id, 'mwb_wpr_variable_points_purchase',true);
					if(isset($enable_product_purchase_points) && $enable_product_purchase_points == 'yes'){

						if(isset($mwb_product_purchase_value) && !empty($mwb_product_purchase_value))
						{
							if (!is_user_logged_in())
							{
								$validate = false;
								wc_add_notice( __( 'You must Logged in to purchase this product', MWB_WPR_Domain ), 'error' );
								return $validate;
							}
							else if (($mwb_product_purchase_value*$quantity) > $get_points)
							{	
								$validate = false;
								wc_add_notice( __( "You don't have sufficiant point to purchase this product", MWB_WPR_Domain ), 'error' ); 
								return $validate;      					
							}

						}

					}
				}
			}
			$cart_content = WC()->cart->get_cart();
			$purchas_meta_point = 0;
			if( !empty( $cart_content ) ){
				foreach(WC()->cart->get_cart() as $cart_item_key => $cart_item) {

					if(isset($cart_item['product_meta']['meta_data']['mwb_wpr_purchase_point_only']) && !empty($cart_item['product_meta']['meta_data']['mwb_wpr_purchase_point_only'])){

						$purchas_meta_point += $cart_item['product_meta']['meta_data']['mwb_wpr_purchase_point_only'];
						$total_point_to_purchased = $purchas_meta_point + ($mwb_product_purchase_value*$quantity);
						if($total_point_to_purchased > $get_points){

							$validate = false;
							wc_add_notice( __( "You have already used your points, Now you don't have much!", MWB_WPR_Domain ), 'error' ); 
							return $validate;
						}
					}
				}
			}

			return $validate;
		}   
		public function mwb_woocommerce_variable_price_html( $val_price, $product ) {


			$variations = $product->get_available_variations();		       
			$var_data = [];
			
			foreach ($variations as $key=> $variation) {
				
				foreach ($variation as $key => $variation_id) 
				{
					if($key=='variation_id')
					{
						$mwb_wpr_parent_id = wp_get_post_parent_id($variation_id);
						$enable_product_purchase_points = get_post_meta($mwb_wpr_parent_id, 'mwb_product_purchase_points_only',true);
						$mwb_product_purchase_value = get_post_meta($variation_id, 'mwb_wpr_variable_points_purchase',true);
						if(isset($enable_product_purchase_points) && $enable_product_purchase_points == 'yes'){

							if(isset($mwb_product_purchase_value) && !empty($mwb_product_purchase_value))
							{

			       				//echo $mwb_product_purchase_value."<br>";
								$var_data[]=$mwb_product_purchase_value;
							}

						}
					}
				}
			}		

			if(isset($var_data)&&!empty($var_data))
			{
				$min_value=min($var_data);
				$max_value=max($var_data);
			}
			
			if(isset($min_value)&&isset($max_value)&&!empty($max_value)&&!empty($min_value))
			{
				if($min_value==$max_value)
				{

					return $val_price =  '<p class="price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol"></span>'.$min_value.'</span><span> Points</span></p>';
				}
				else
				{
					return $val_price =  '<p class="price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol"></span>'.$min_value.'</span><span> Points</span> – <span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol"></span>'.$max_value.'</span><span> Points</span></p>';

				}

			}
			else
			{
				return $val_price;
			}


		}  
       // End of Custom Work

        /**
		 * This function is used to allow customer can apply points during checkout
		 * @name mwb_overwrite_form_temp
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */

        public function mwb_overwrite_form_temp($path, $template_name) {
        	$user_id = get_current_user_ID();
        	$mwb_wpr_apply_points_checkout = get_option("mwb_wpr_apply_points_checkout",0);
        	$user_level = get_user_meta($user_id,'membership_level',true);
        	if($mwb_wpr_apply_points_checkout == 1){
        		if( $template_name == 'checkout/form-coupon.php') {
        			return MWB_WPR_DIRPATH.'woocommerce/checkout/form-coupon.php';
        		}
        	}
        	if($template_name == 'checkout/review-order.php' && !empty($user_level)) {
        		return MWB_WPR_DIRPATH.'woocommerce/checkout/review-order.php';
        	}
        	return $path;
        }


       	/**
		 * This function is used to check whether the exclude product is enable or not for Membership Discount { if enable then sale product will not be having the membership discount anymore as they are already having some discounts }
		 * @name check_exclude_sale_products
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */

       	public function check_exclude_sale_products($products) {

       		$membership_settings_array = get_option('mwb_wpr_membership_settings',true);
       		$exclude_sale_product = isset($membership_settings_array['exclude_sale_product']) ? intval($membership_settings_array['exclude_sale_product']) : 0;

       		$exclude = false;

       		if($exclude_sale_product && $products->is_on_sale()) {
       			$exclude = true;
       		}
       		else {
       			$exclude = false;
       		}

       		return $exclude;
       	}

       }
       $GLOBALS['MWB_WPR_Front_End'] = new MWB_WPR_Front_End();
   }
