<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    One_Click_Order_Re_order
 * @subpackage One_Click_Order_Re_order/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    One_Click_Order_Re_order
 * @subpackage One_Click_Order_Re_order/public
 * @author     makewebbetter <webmaster@makewebbetter.com>
 */
class One_Click_Order_Re_order_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, ONE_CLICK_ORDER_RE_ORDER_DIR_URL . 'public/css/one-click-order-re-order-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
   {

      global $woocommerce, $wp_scripts;

      wp_enqueue_script( 'jquery-ui-draggable' );
	  wp_enqueue_script( 'jquery-ui-core' );
	  wp_enqueue_script( $this->plugin_name, ONE_CLICK_ORDER_RE_ORDER_DIR_URL . 'public/js/one-click-order-re-order-public.js', array( 'jquery','jquery-ui-draggable','jquery-ui-core','jquery-touch-punch'), $this->version, false );
	
	  $mwb_atbBtnText = __( 'Add to basket', 'one-click-order-re-order' );
	  $mwb_rfbBtnText = __( 'Remove from basket','one-click-order-re-order' );
	  $mwb_settings = get_option('one_click_order_re_order_enable_plug',false);
		
	   if ( ! empty( $mwb_settings ) ) 
	   {

		    if ( array_key_exists( 'button_text', $mwb_settings ) and ! empty( $mwb_settings[ 'button_text' ] ) )
		       {

					$mwb_atbBtnText = $mwb_settings['button_text' ];
			    }

				if ( array_key_exists( 'button_remove_text', $mwb_settings ) and ! empty( $mwb_settings[ 'button_remove_text' ] ) ) 
				{

					$mwb_rfbBtnText = $mwb_settings['button_remove_text' ];
				}
		}

			// Localize the script with new data
			$myaccount_page_id = get_option ( 'woocommerce_myaccount_page_id' );
			if ( $myaccount_page_id )
		    {
				$myaccount_page_url = get_permalink ( $myaccount_page_id );
			}
			$checkout_url = wc_get_page_permalink( 'checkout' );
			$ajax_nonce = wp_create_nonce( "mwb-cng-ajax-seurity-nonce" );

			$translation_array = array (
				'ajaxurl' 				=> admin_url ( 'admin-ajax.php' ),
				'plugi_dir_url' 		=> MAKEWEBBETTER_CNG_ORDER_URL,
				'cart_url' 				=> wc_get_cart_url(),
				'account_url'			=> $myaccount_page_url,
				'checkouturl'			=> $checkout_url,
				'ajax_nonce'			=> $ajax_nonce,
				'product_not_exist' 	=> __( 'All products of this order is no longer exist in our store.', 'one-click-order-re-order' ),
				'exclude_products_head' => __( 'Exclude products from this order', 'one-click-order-re-order' ),
				'exc_basket_item_head' 	=> __( 'Exclude products from your basket', 'one-click-order-re-order' ),
				'exclude' 				=> __( 'Exclude', 'one-click-order-re-order' ),
				'image' 				=> __( 'Image', 'one-click-order-re-order' ),
				'out_of_stock_desc' 	=> __( 'This product is out of stock, so it would be excluded from order.', 'one-click-order-re-order' ),
				'exclude_desc' 			=> __( 'Click on this checkbox to exclude this product from reordering.', 'one-click-order-re-order' ),
				'product_name' 			=> __( 'Product name', 'one-click-order-re-order' ),
				'stock' 				=> __( 'Stock', 'one-click-order-re-order' ),
				'quantity' 				=> __( 'Quantity', 'one-click-order-re-order' ),
				'submit' 				=> __( 'Checkout', 'one-click-order-re-order' ),
				'atc' 					=> __( 'Add to cart', 'one-click-order-re-order' ),
				'close' 				=> __( 'Close', 'one-click-order-re-order' ),
				'no_items' 				=> __( 'No items found.', 'one-click-order-re-order' ),
				'atbBtnText'			=> $mwb_atbBtnText,
				'rfbBtnText'			=> $mwb_rfbBtnText
			);
			wp_localize_script ( $this->plugin_name, 'global_var', $translation_array );
			

	}
	/**
	 * Add Re-order and Place same order button in the User Order Section.
	 *
	 * @since    1.0.0
	 */
	public function mwb_cng_add_button( $mwb_actions, $mwb_order ) 
    {
			$mwb_button_text = __ ( 'Re-Order', 'one-click-order-re-order' );
			
			if(WC()->version<'3.0.0')
				{

			     $mwb_actions[ 'mwb_my_account_reorder' ] = array (
				'url' => $mwb_order->id,
				'name' => apply_filters ( 'mwb_change_button_text', $mwb_button_text ) 
			     );
			   }

			else
			{

				$mwb_actions[ 'mwb_my_account_reorder' ] = array (
				'url' => $mwb_order->get_id(),
				'name' => apply_filters ( 'mwb_change_button_text', $mwb_button_text ) 
			   );
			}
			$mwb_settings = get_option('one_click_order_re_order_enable_plug',false);
			if ( !empty( $mwb_settings ) )
			 {
				if ( $mwb_settings[ 'enable_plugin' ] == 'on' )
				 {
					$btn_text = __ ( 'Place same order', 'one-click-order-re-order' );

					if(WC()->version<'3.0.0')
				    {	

						$mwb_actions[ 'mwb_my_account_place_same_order' ] = array (
							'url' => $mwb_order->id,
							'name' => apply_filters ( 'mwb_change_button_text', $btn_text )
						);
				    }
				else
				    {
					   $mwb_actions[ 'mwb_my_account_place_same_order' ] = array (
						'url' => $mwb_order->get_id(),
						'name' => apply_filters ( 'mwb_change_button_text', $btn_text )
					   );
				   }
				}
			}
			
			return $mwb_actions;
	}

      /**
	  * Handles Ajax Request and Fetches Prvious orders done By a user,
	  *after that adds to cart again of previous order
	  * @since    1.0.0
	 */
	 public	function mwb_cng_prefix_ajax_get_order_cart() {

			$check_ajax = check_ajax_referer( 'mwb-cng-ajax-seurity-nonce', 'nonce_check' );
			if ( !$check_ajax ) {
				exit( 'failed' );
			}

			$order_id = $_POST[ 'order_id' ];
			if ( WC ()->cart->get_cart_contents_count() ) {
				WC ()->cart->empty_cart ();
			}
			$error = array();
			$order = new WC_Order ( trim ( $order_id ) );
			if(WC()->version < '3.0.0')
			{
				$order_iddd =  $order->id;
				if ( empty ( $order_iddd ) ) {
					exit( 'failed' );
				}
			}
			else
			{
				$order_iddd =  $order->get_id();
				if ( empty ($order_iddd ) ) {
					exit( 'failed' );
				}

			}

			foreach ( $order->get_items() as $product_info ) {
				$product_id = ( int ) apply_filters ( 'woocommerce_add_to_cart_product_id', $product_info ['product_id'] );
				$qty = ( int ) $product_info ['qty'];
				$all_variations = array ();
				$variation_id = ( int ) $product_info[ 'variation_id' ];
			
				$cart_product_data = apply_filters ( 'woocommerce_order_again_cart_item_data', array (), $product_info, $order );
				foreach ( $product_info ['item_meta'] as $product_meta_name => $product_meta_value ) {
					if ( taxonomy_is_product_attribute( $product_meta_name ) ) {
						$all_variations [$product_meta_name] = $product_meta_value[0];
					} else {
						if ( meta_is_product_attribute( $product_meta_name, $product_meta_value[0], $product_id ) ) {
							$all_variations[ $product_meta_name ] = $product_meta_value[0];
						}
					}
				}
			
				// Add to cart validation
				if (! apply_filters ( 'woocommerce_add_to_cart_validation', true, $product_id, $qty, $variation_id, $all_variations, $cart_product_data )) {
					continue;
				}
			
				// Checks availability of products
				$array = wc_get_product( $product_id );
			
				// Add to cart order products
				$add_to_cart = WC ()->cart->add_to_cart ( $product_id, $qty, $variation_id, $all_variations, $cart_product_data );
			}
			// Checks for success or errors
			if ( $add_to_cart ) {
				// Message to be shown when items added to cart
				$success 	= __ ( 'The items are added to cart from your previous order .', 'one-click-order-re-order' );
				$notice 	= wc_add_notice ( apply_filters ( 'cng_added_to_cart_msg', $success ) );
				exit( 'success' );
			} else { 
				// Message to be shown when items not added to cart
				$error 		= __ ( 'Something went wrong, items couldn\'t added to cart ', 'one-click-order-re-order' );
				$notice 	= wc_add_notice ( apply_filters ( 'cng_atc_error', $error ), 'error' );
				exit( 'failed' );
			}
	}
      /**
	  * Adds Re-Order button at Order's Detail Page
	  *
	  * @since    1.0.0
	  */
	 public	function mwb_cng_add_edit_order_button( $mwb_order ) 
	 {
		if(WC()->version<'3.0.0')
				{ 	
					if ( ! $mwb_order->has_status( 'completed' ) )
				   {
				?>
						<p>
							<a class="button mwb_my_account_reorder" href="javascript:void(0);" data-order_id="<?php echo $mwb_order->id;?>">
								<?php _e( 'Re-Order', 'one-click-order-re-order' );?>
							</a>
						</p>
						<?php 
		                $mwb_settings = get_option('one_click_order_re_order_enable_plug',false);
				
					if ( !empty( $mwb_settings ) ) 
					{
					 if ( $mwb_settings[ 'enable_plugin' ] == 'on' ) 
					   {  ?>
							<p>
								<a class="button mwb_my_account_place_same_order" href="javascript:void(0);" data-order_id="<?php echo $mwb_order->id;?>">
									<?php _e('Place Same Order','one-click-order-re-order');?>
								</a>
							</p>
						<?php 
					   }
				   }
				?>
			<?php
		    	}
           }
		else {

			if ( ! $mwb_order->has_status( 'completed' ) ) 
			{
				?>
				<p>
					<a class="button mwb_my_account_reorder" href="javascript:void(0);" data-order_id="<?php echo $mwb_order->get_id();?>">
						<?php _e( 'Re-Order', 'one-click-order-re-order' );?>
					</a>
				</p>
				<?php 

				$mwb_settings = get_option('one_click_order_re_order_enable_plug',false);
				if ( !empty( $mwb_settings ) ) 
				{
					if ( $mwb_settings['enable_plugin' ] == 'on' ) 
						{  ?>
						<p>
							<a class="button mwb_my_account_place_same_order" href="javascript:void(0);" data-order_id="<?php echo $mwb_order->get_id();?>">
								<?php _e('Place Same Order','one-click-order-re-order');?>
							</a>
						</p>
					<?php 
					    }
				}
				?>
			<?php
			}
		}	
    } 
		
		
   public function mwb_ocor_close_know_more_email()
		{
			if(isset($_GET["mwb_ocor_close"]) && $_GET["mwb_ocor_close"]==true)
			{
				unset($_GET["mwb_ocor_close"]);
				if(!session_id())
					session_start();
				$_SESSION["mwb_ocor_hide_email"]=true;
			}
		}

       /**
	   * Fetches all products from an order.
	   *
	   * @since    1.0.0
	   */

 public function mwb_cng_get_oreder_products() 
	{
			$mwb_order_id = $_POST[ 'order_id' ];
			$mwb_order = new WC_Order ( trim ( $mwb_order_id ) );

		if(WC()->version < '3.0.0')
		{

			$mwb_order_idd =  $mwb_order->id;
			if ( empty ( $mwb_order_idd ) ) 
			{
				exit( 'failed' );
			}
		}
			else
			{
				$mwb_order_idd =  $mwb_order->get_id();
				if ( empty ($mwb_order_idd ) )
				{
					exit( 'failed' );
				}

			}

   
			$mwb_order_items = array();
			foreach( $mwb_order->get_items() as $mwb_product_info ) 
			{
				$product_id = ( int ) apply_filters ( 'woocommerce_add_to_cart_product_id', $mwb_product_info ['product_id'] );  
				$mwb_items 		= wc_get_product ( $product_id );
				
				if ( ! $mwb_items || empty ( $mwb_items ) ) 
				{
					$mwb_order_items[ $product_id ][ 'availability' ] = 'not_exist';
					continue;
				}
				 else if ( $mwb_items->post->post_status != 'publish' ) 
				{
					$mwb_order_items[ $product_id ][ 'availability' ] = 'not_availale';
				} else
				{
					$mwb_order_items[ $product_id ][ 'availability' ] = 'available';
				}

				$mwb_order_items[ $product_id ][ 'title' ] 		= get_the_title( $product_id );
				$mwb_order_items[ $product_id ][ 'permalink' ] 	= get_the_permalink( $product_id );
				$mwb_order_items[ $product_id ][ 'qty' ] 		= ( int ) $mwb_product_info[ 'qty' ];
				$mwb_order_items[ $product_id ][ 'image' ] 		= $mwb_items->get_image();
				if ( ! $mwb_items->is_in_stock () )
				{
					$mwb_order_items[ $product_id ][ 'stock' ] = 'out_of_stock';
				} else 
				{
					$mwb_order_items[ $product_id ][ 'stock' ] = 'in_stock';
				}
			}
			
			echo json_encode( 
				array(
					'status' => 'ok',
					'prodcuts' => $mwb_order_items,
				)
			);
			die();
	}

      /**
	  * Adds Basket Items into the cart
	  *
	  * @since    1.0.0
	  */
	public function mwb_ocor_add_basket_items_to_cart()
	{
			$check_ajax = check_ajax_referer( 'mwb-cng-ajax-seurity-nonce', 'ajax_nonce' );
			if ( !$check_ajax )
			{
				exit( 'failed' );
			}
			
			$excluded_items = isset( $_POST[ 'excluded_items' ] ) ? $_POST[ 'excluded_items' ] : array();
			$quantities 	= isset( $_POST[ 'quantities' ] ) ? $_POST[ 'quantities' ] : array();

			$foundItems 	= array();
			$errorInAtc 	= false;
			$basketItemsIds = get_user_meta( get_current_user_id(), 'mwb_ocor_basket_info', true );
			if ( empty( $basketItemsIds ) or ! is_array( $basketItemsIds ) ) 
			{
				wp_send_json_error( __( "No items found in you basket.", 'one-click-order-re-order' ) );
				wp_die();
			}

			if ( WC ()->cart->get_cart_contents_count() ) {
				WC ()->cart->empty_cart ();
			}

			foreach ( $basketItemsIds as $item_id => $item ) 
			{
				if ( empty( $item_id ) or empty( $item ) ) 
				{
					continue;
				}

				if ( in_array( $item_id, $excluded_items ) )
				{
					continue;
				}

				$qty = $item[ 'qty' ];
				if ( !empty( $quantities ) )
				{
					if ( array_key_exists( $item_id, $quantities ) ) 
					{
						$qty = $quantities[ $item_id ];
					}
				}
				
				if ( $item[ 'type' ] == 'variable' )
				{
					foreach ( $item[ 'variations' ] as $var_id => $variation_array )
					{
						if ( empty( $var_id ) or empty( $variation_array ) ) 
						{
							continue;
						}

						$productVariation 	= new WC_Product_Variation( $var_id );
						$all_variations 	=  $productVariation->get_variation_attributes();
						$item_qty 			= isset( $qty ) ? $qty : $variation_array[ 'qty' ];
						$added_to_cart 		= WC()->cart->add_to_cart( $item_id, $item_qty, $var_id, $all_variations );
					}
				} 
				else
				{
					$added_to_cart = WC ()->cart->add_to_cart( $item_id, $qty );
				}
				if ( ! $added_to_cart )
				{
					$errorInAtc = true;
				}
			}
			
			wp_send_json_success( __( 'Successfully added to cart.', 'one-click-order-re-order' ) );
			wp_die();
	}
       /**
	  * Adds Same order's Items into the cart
	  *
	  * @since    1.0.0
	  */
	public  function mwb_cng_prefix_ajax_get_same_order_cart() {
      
			$check_ajax_nonce = check_ajax_referer( 'mwb-cng-ajax-seurity-nonce', 'ajax_nonce' );
			if ( ! $check_ajax_nonce )
			 {
				exit( 'failed' );
			}
			$order_id 			= $_POST[ 'order_id' ];
			$ajax_nonce 		= $_POST[ 'ajax_nonce' ];
			$excluded_products 	= $_POST[ 'excluded_products' ];
			$quantities 		= $_POST[ 'quantities' ];
			$error 				= false;
			$added_names 		= '';
			$failed_names 		= '';
			$order = new WC_Order( trim( $order_id ) );
			if ( WC ()->cart->get_cart_contents_count() ) {
				WC ()->cart->empty_cart();
			}

			foreach ( $order->get_items() as $product_info ) {
				$product_id = ( int ) apply_filters ( 'woocommerce_add_to_cart_product_id', $product_info[ 'product_id' ] );
				if ( !empty( $excluded_products ) ) {
					if ( in_array( $product_id, $excluded_products ) ) {
						continue;
					}
				}

				$qty = ! empty( $quantities ) ? ( int ) $quantities[ $product_id ] : ( int ) $product_info[ 'qty' ];
				$all_variations = array ();
				$variation_id = ( int ) $product_info[ 'variation_id' ];
				$cart_product_data = apply_filters ( 'woocommerce_order_again_cart_item_data', array(), $product_info, $order );
		
				foreach ( $product_info[ 'item_meta' ] as $product_meta_name => $product_meta_value ) {
					if ( taxonomy_is_product_attribute( $product_meta_name ) ) {
						$all_variations[ $product_meta_name ] = $product_meta_value[0];
					} else {
						if ( meta_is_product_attribute( $product_meta_name, $product_meta_value [0], $product_id ) ) {
							$all_variations[ $product_meta_name ] = $product_meta_value[0];
						}
					}
				}

				if ( ! apply_filters ( 'woocommerce_add_to_cart_validation', true, $product_id, $qty, $variation_id, $all_variations, $cart_product_data ) ) {
					continue;
				}

				$item_array = wc_get_product ( $product_id );

				$add_to_cart = WC ()->cart->add_to_cart ( $product_id, $qty, $variation_id, $all_variations, $cart_product_data );
				if( !$add_to_cart ) {
					$error = true;
				}
			}
			if ( !$error ) {
				// Message to be shown when items added to cart
				$success = __ ( 'The items are added to cart from your previous order. You can place this order now.', 'one-click-order-re-order' );
				$notice = wc_add_notice ( apply_filters ( 'cng_added_to_cart_msg', $success ) );
				exit( 'success' );
			}
		}
      /**
	  * Dispaly Basket On the  Page
	  *
	  * @since    1.0.0
	  */
	 public function mwb_ocor_initialize_frontend ()
	 {
	 	if ( ! is_user_logged_in() ) {
				return;
			}
 			$current_user =	wp_get_current_user();
			if ( empty( $current_user ) ) {
				return;
			}
			$general_settings = get_option('one_click_order_re_order_enable_plug',false);
			if(!empty($general_settings['selectedUsers'])&&is_array($general_settings['selectedUsers']))
			$current_user->roles = array_values( $current_user->roles );
			$general_settings = get_option('one_click_order_re_order_enable_plug',false);

			if (( $general_settings[ 'basket' ] == 'on' )&&!empty($general_settings['selectedUsers']) && (is_array($general_settings['selectedUsers']) && in_array( $current_user->roles[0], $general_settings['selectedUsers'] ))) {

				add_action( 'woocommerce_after_shop_loop_item', array( $this, 'mwb_ocor_add_to_basket_button' ) );
				add_action( 'woocommerce_after_single_variation', array( $this, 'mwb_ocor_add_to_basket_button' ) );
			}
			else if (( $general_settings[ 'basket' ] == 'on' ) && (current_user_can( 'administrator' ))) {
				add_action( 'woocommerce_after_shop_loop_item', array( $this, 'mwb_ocor_add_to_basket_button' ) );
				add_action( 'woocommerce_after_single_variation', array( $this, 'mwb_ocor_add_to_basket_button' ) );


			}
	 }
          /**
	      * Dispaly Add to Basket and Remove from Basket Button
	      *
	      * @since    1.0.0
	      */
	 		public function mwb_ocor_add_to_basket_button() {
			global $product;
            $atbBtnText = __( 'Add to basket', 'one-click-order-re-order' );
			$rfbBtnText = __( 'Remove from basket', 'one-click-order-re-order' );
			$atbDisable = is_product() ? "disabled" : "";
			$rfbhidden 	= is_product() ? "mwb_cng_hide" : "";


			$settings = get_option('one_click_order_re_order_enable_plug',false);

			if ( ! empty( $settings ) )
			{
				if ( array_key_exists( 'button_text', $settings ) and ! empty( $settings[ 'button_text' ] ) ) {
					$atbBtnText = $settings[ 'button_text' ];
				}

				if ( array_key_exists( 'button_remove_text', $settings ) and ! empty( $settings[ 'button_remove_text' ] ) ) {
					$rfbBtnText = $settings[ 'button_remove_text' ];
				}
			}

			if ( ! is_product() ) {
				if ( $product->get_type() == 'variable' ) {
					return;
				}
			}

			$oldBasket = get_user_meta( get_current_user_id(), 'mwb_ocor_basket_info', true );
			
          if(WC()->version<'3.0.0')
				{
			if ( is_product() ) {
				$rfbhidden = 'mwb_cng_hide';
				$atbhidden = 'mwb_cng_hide';
				if ( !empty( $oldBasket ) and is_array( $oldBasket ) ) {
					if ( array_key_exists( $product->id, $oldBasket ) and $oldBasket[ $product->id ][ 'type' ] == 'variable' and !empty( $oldBasket[ $product->id ][ 'variations' ] ) ) {
						foreach ( $oldBasket[ $product->id ][ 'variations' ] as $var_id => $variation_array ) {
							if ( empty( $var_id ) ) {
								continue;
							}

							$rfb_html = '<p class="mwb_ocor_basket">';
								$rfb_html .= sprintf(
									'<a rel="nofollow" class="mwb_ocor_rfb button %s" id="%s" href="javascript:void(0);" data-id="%s" data-sku="%s" data-user_id="%s" data-type="%s" title="%s" data-variation_id="%s">%s</a>',
									esc_attr( esc_html( $rfbhidden ) ),
									esc_attr( esc_html( 'mwb_ocor_rfb_btn_' . $product->id ) ),
									esc_attr( esc_html( $product->id ) ),
									esc_attr( esc_html( $product->get_sku() ) ),
									esc_attr( esc_html( get_current_user_id() ) ),
									esc_attr( esc_html( $product->get_type() ) ),
									esc_attr( esc_html( apply_filters( 'mwb_ocor_rfb_button_text', $rfbBtnText ) ) ),
									esc_attr( esc_html( $var_id ) ),
									esc_html( apply_filters( 'mwb_ocor_rfb_button_text', $rfbBtnText ) )
								);
							echo $rfb_html .= '</p>';
						}
					}

					$atb_html = '<p class="mwb_ocor_basket">';
						$atb_html .= sprintf(
							'<a rel="nofollow" class="mwb_ocor_atb button %s" id="%s" href="javascript:void(0);" data-id="%s" data-user_id="%s" data-type="%s" title="%s">%s</a>',
							esc_attr( esc_html( $atbhidden ) ),
							esc_attr( esc_html( 'mwb_ocor_atb_btn_' . $product->id ) ),
							esc_attr( esc_html( $product->id ) ),
							esc_attr( esc_html( get_current_user_id() ) ),
							esc_attr( esc_html( $product->get_type() ) ),
							esc_attr( esc_html( apply_filters( 'mwb_ocor_atb_button_text', $atbBtnText ) ) ),
							esc_html( apply_filters( 'mwb_ocor_atb_button_text', $atbBtnText ) )
						);
					$atb_html .= '</p>';
					echo $atb_html;
					return;
				} else {
					$atbhidden = 'mwb_cng_hide';
					$atb_html = '<p class="mwb_ocor_basket">';
						$atb_html .= sprintf(
							'<a rel="nofollow" class="mwb_ocor_atb button %s" id="%s" href="javascript:void(0);" data-id="%s" data-user_id="%s" data-type="%s" title="%s">%s</a>',
							esc_attr( esc_html( $atbhidden ) ),
							esc_attr( esc_html( 'mwb_ocor_atb_btn_' . $product->id ) ),
							esc_attr( esc_html( $product->id ) ),
							esc_attr( esc_html( get_current_user_id() ) ),
							esc_attr( esc_html( $product->get_type() ) ),
							esc_attr( esc_html( apply_filters( 'mwb_ocor_atb_button_text', $atbBtnText ) ) ),
							esc_html( apply_filters( 'mwb_ocor_atb_button_text', $atbBtnText ) )
						);
					$atb_html .= '</p>';
					echo $atb_html;
					return;
				}
			} else {
				if ( !empty( $oldBasket ) and is_array( $oldBasket ) ) {
					if ( array_key_exists( $product->id, $oldBasket ) ) {
						$rfb_html = '<p class="mwb_ocor_basket">';
							$rfb_html .= sprintf(
								'<a rel="nofollow" class="mwb_ocor_rfb button %s" id="%s" href="javascript:void(0);" data-id="%s" data-sku="%s" data-user_id="%s" data-type="%s" title="%s">%s</a>',
								esc_attr( esc_html( $rfbhidden ) ),
								esc_attr( esc_html( 'mwb_ocor_rfb_btn_' . $product->id ) ),
								esc_attr( esc_html( $product->id ) ),
								esc_attr( esc_html( $product->get_sku() ) ),
								esc_attr( esc_html( get_current_user_id() ) ),
								esc_attr( esc_html( $product->get_type() ) ),
								esc_attr( esc_html( apply_filters( 'mwb_ocor_rfb_button_text', $rfbBtnText ) ) ),
								esc_html( apply_filters( 'mwb_ocor_rfb_button_text', $rfbBtnText ) )
							);
						echo $rfb_html .= '</p>';
						return;
					}
				}

				$atb_html = '<p class="mwb_ocor_basket">';
					$atb_html .= sprintf(
						'<a rel="nofollow" class="mwb_ocor_atb button %s" id="%s" href="javascript:void(0);" data-id="%s" data-user_id="%s" data-type="%s" title="%s">%s</a>',
						esc_attr( esc_html( $atbDisable ) ),
						esc_attr( esc_html( 'mwb_ocor_atb_btn_' . $product->id ) ),
						esc_attr( esc_html( $product->id ) ),
						esc_attr( esc_html( get_current_user_id() ) ),
						esc_attr( esc_html( $product->get_type() ) ),
						esc_attr( esc_html( apply_filters( 'mwb_ocor_atb_button_text', $atbBtnText ) ) ),
						esc_html( apply_filters( 'mwb_ocor_atb_button_text', $atbBtnText ) )
					);
				$atb_html .= '</p>';
				echo $atb_html;
			}
}			
else{

	if ( is_product() ) {
				$rfbhidden = 'mwb_cng_hide';
				$atbhidden = 'mwb_cng_hide';
				if ( !empty( $oldBasket ) and is_array( $oldBasket ) ) {
					if ( array_key_exists( $product->get_id(), $oldBasket ) and $oldBasket[ $product->get_id() ][ 'type' ] == 'variable' and !empty( $oldBasket[ $product->get_id() ][ 'variations' ] ) ) {
						foreach ( $oldBasket[ $product->get_id() ][ 'variations' ] as $var_id => $variation_array ) {
							if ( empty( $var_id ) ) {
								continue;
							}

							$rfb_html = '<p class="mwb_ocor_basket">';
								$rfb_html .= sprintf(
									'<a rel="nofollow" class="mwb_ocor_rfb button %s" id="%s" href="javascript:void(0);" data-id="%s" data-sku="%s" data-user_id="%s" data-type="%s" title="%s" data-variation_id="%s">%s</a>',
									esc_attr( esc_html( $rfbhidden ) ),
									esc_attr( esc_html( 'mwb_ocor_rfb_btn_' . $product->get_id() ) ),
									esc_attr( esc_html( $product->get_id() ) ),
									esc_attr( esc_html( $product->get_sku() ) ),
									esc_attr( esc_html( get_current_user_id() ) ),
									esc_attr( esc_html( $product->get_type() ) ),
									esc_attr( esc_html( apply_filters( 'mwb_ocor_rfb_button_text', $rfbBtnText ) ) ),
									esc_attr( esc_html( $var_id ) ),
									esc_html( apply_filters( 'mwb_ocor_rfb_button_text', $rfbBtnText ) )
								);
							echo $rfb_html .= '</p>';
						}
					}

					$atb_html = '<p class="mwb_ocor_basket">';
						$atb_html .= sprintf(
							'<a rel="nofollow" class="mwb_ocor_atb button %s" id="%s" href="javascript:void(0);" data-id="%s" data-user_id="%s" data-type="%s" title="%s">%s</a>',
							esc_attr( esc_html( $atbhidden ) ),
							esc_attr( esc_html( 'mwb_ocor_atb_btn_' . $product->get_id() ) ),
							esc_attr( esc_html( $product->get_id() ) ),
							esc_attr( esc_html( get_current_user_id() ) ),
							esc_attr( esc_html( $product->get_type() ) ),
							esc_attr( esc_html( apply_filters( 'mwb_ocor_atb_button_text', $atbBtnText ) ) ),
							esc_html( apply_filters( 'mwb_ocor_atb_button_text', $atbBtnText ) )
						);
					$atb_html .= '</p>';
					echo $atb_html;
					return;
				} else {
					$atbhidden = 'mwb_cng_hide';
					$atb_html = '<p class="mwb_ocor_basket">';
						$atb_html .= sprintf(
							'<a rel="nofollow" class="mwb_ocor_atb button %s" id="%s" href="javascript:void(0);" data-id="%s" data-user_id="%s" data-type="%s" title="%s">%s</a>',
							esc_attr( esc_html( $atbhidden ) ),
							esc_attr( esc_html( 'mwb_ocor_atb_btn_' . $product->get_id() ) ),
							esc_attr( esc_html( $product->get_id() ) ),
							esc_attr( esc_html( get_current_user_id() ) ),
							esc_attr( esc_html( $product->get_type() ) ),
							esc_attr( esc_html( apply_filters( 'mwb_ocor_atb_button_text', $atbBtnText ) ) ),
							esc_html( apply_filters( 'mwb_ocor_atb_button_text', $atbBtnText ) )
						);
					$atb_html .= '</p>';
					echo $atb_html;
					return;
				}
			} else {
				if ( !empty( $oldBasket ) and is_array( $oldBasket ) ) {
					if ( array_key_exists( $product->get_id(), $oldBasket ) ) {
						$rfb_html = '<p class="mwb_ocor_basket">';
							$rfb_html .= sprintf(
								'<a rel="nofollow" class="mwb_ocor_rfb button %s" id="%s" href="javascript:void(0);" data-id="%s" data-sku="%s" data-user_id="%s" data-type="%s" title="%s">%s</a>',
								esc_attr( esc_html( $rfbhidden ) ),
								esc_attr( esc_html( 'mwb_ocor_rfb_btn_' . $product->get_id() ) ),
								esc_attr( esc_html( $product->get_id() ) ),
								esc_attr( esc_html( $product->get_sku() ) ),
								esc_attr( esc_html( get_current_user_id() ) ),
								esc_attr( esc_html( $product->get_type() ) ),
								esc_attr( esc_html( apply_filters( 'mwb_ocor_rfb_button_text', $rfbBtnText ) ) ),
								esc_html( apply_filters( 'mwb_ocor_rfb_button_text', $rfbBtnText ) )
							);
						echo $rfb_html .= '</p>';
						return;
					}
				}

				$atb_html = '<p class="mwb_ocor_basket">';
					$atb_html .= sprintf(
						'<a rel="nofollow" class="mwb_ocor_atb button %s" id="%s" href="javascript:void(0);" data-id="%s" data-user_id="%s" data-type="%s" title="%s">%s</a>',
						esc_attr( esc_html( $atbDisable ) ),
						esc_attr( esc_html( 'mwb_ocor_atb_btn_' . $product->get_id() ) ),
						esc_attr( esc_html( $product->get_id() ) ),
						esc_attr( esc_html( get_current_user_id() ) ),
						esc_attr( esc_html( $product->get_type() ) ),
						esc_attr( esc_html( apply_filters( 'mwb_ocor_atb_button_text', $atbBtnText ) ) ),
						esc_html( apply_filters( 'mwb_ocor_atb_button_text', $atbBtnText ) )
					);
				$atb_html .= '</p>';
				echo $atb_html;	
		}

		}
}   
          /**
	      * Show Basket Icon in the cart
	      *
	      * @since    1.0.0
	      */
    public function mwb_ocor_footer_stuffs() {
			if ( ! is_user_logged_in() ) {
				return;
			}
			$settings = get_option('one_click_order_re_order_enable_plug',false);
			if ( empty( $settings ) ) {
				return;
			}

			if ( empty( $settings[ 'selectedUsers' ] ) and ! current_user_can( 'administrator' ) ) {
				return;
			}

			$current_user =	wp_get_current_user();
			if ( empty( $current_user ) ) {
				return;
			}

			$current_user->roles = array_values( $current_user->roles );
			if(is_array($settings[ 'selectedUsers' ]))
			if (! in_array( $current_user->roles[0], $settings[ 'selectedUsers' ] ) and ! current_user_can( 'administrator' ) ) {
				return;
			}

			if ( empty( $settings[ 'basket' ] ) or $settings[ 'basket' ] == 'off' ) {
				return;
			}

			if ( empty( $settings[ 'selectedPage' ] ) ) {
				return;
			}

			$found = false;
			foreach ( $settings[ 'selectedPage' ] as $page ) {
				if ( !$page ) {
					continue;
				}

				switch ( $page ) {
					case 'detail':
						if ( is_product() ) {
							$found = true;
						}
						break;
					
					case 'shop':
						if ( is_shop() ) {
							$found = true;
						}
						break;
					
					case 'cart':
						if ( is_cart() ) {
							$found = true;
						}
						break;
					
					case 'account':
						if ( is_account_page() ) {
							$found = true;
						}
						break;
					
					default:
						if ( ! $found ) {
							return ;
						}
						break;
				}
				if ( $found ) {
					break;
				}
			}

			if ( ! $found ) {
				return ;
			}

			$basketTotal 	= 0;
			$basket 		= get_user_meta( get_current_user_id(), 'mwb_ocor_basket_info', true );
			if ( ! empty( $basket ) ) {
				foreach ( $basket as $item_id => $item ) {
					if( empty( $item ) )
						continue;

					if ( $item[ 'type' ] == 'variable' ) {
						foreach ( $item[ 'variations' ] as $var_id ) {
							if ( empty( $var_id ) )
								continue;

							$basketTotal++;			
						}
					} else {
						$basketTotal++;
					}
				}
			}

			$image = MAKEWEBBETTER_CNG_ORDER_URL . 'admin/images/shopping-bag.png';
			if ( !empty( $settings[ 'image' ] ) ) {
				$image = $settings[ 'image' ];
			}
			?>
			<a class="mwb_ocor_floating_basket_wrapper">
				<img src="<?php echo $image;?>" alt="<?php _e( "Shopping Basket", 'one-click-order-re-order' );?>" class="mwb_ocor_shopping_basket">
				<span class="mwb_ocor_basket_item_count" data-total="<?php echo $basketTotal;?>"><?php echo $basketTotal;?></span>
			</a>
		<?php 
		}
		/**
	      * Add items into the Basket
	      *
	      * @since    1.0.0
	      */
	public function	mwb_ocor_add_to_basket()
	{
		$check_ajax = check_ajax_referer( 'mwb-cng-ajax-seurity-nonce', 'ajax_nonce' );
			if ( !$check_ajax ) {
				exit( 'failed' );
			}
			$item_id 		= isset( $_POST[ 'item_id' ] ) ? $_POST[ 'item_id' ] : '';
			$user_id 		= isset( $_POST[ 'user_id' ] ) ? $_POST[ 'user_id' ] : '';
			$qty 			= isset( $_POST[ 'qty' ] ) ? $_POST[ 'qty' ] : 1;
			$item_type 		= isset( $_POST[ 'type' ] ) ? $_POST[ 'type' ] : '';
			$variation_id 	= isset( $_POST[ 'variation_id' ] ) ? $_POST[ 'variation_id' ] : '';
			if ( get_current_user_id() == $user_id ) {
				$updated = false;
				$basket = get_user_meta( $user_id, 'mwb_ocor_basket_info', true );
				if ( empty( $basket ) ) {
					$basket = array();
					$basket[ $item_id ] = array('type' => $item_type);
					if ( $item_type == 'variable' and $variation_id != '' ) {
						$basket[ $item_id ][ 'variations' ][ $variation_id ] = array(
							'qty'			=> $qty,
							'variation_id'	=> $variation_id,
						);
					} else {
						$basket[ $item_id ][ 'qty' ] = $qty;
					}

					$updated = update_user_meta( $user_id, 'mwb_ocor_basket_info', $basket );
				} else {
					$basket[ $item_id ][ 'type' ] = $item_type;
					if ( $item_type == 'variable' and $variation_id != '' ) {
						$basket[ $item_id ][ 'variations' ][ $variation_id ] = array(
							'qty'			=> $qty,
							'variation_id'	=> $variation_id,
						);
					} else {
						$basket[ $item_id ][ 'qty' ] = $qty;
					}
					$updated = update_user_meta( $user_id, 'mwb_ocor_basket_info', $basket );
				}

				if ( $updated ) {
					wp_send_json_success( __( 'Added to basket.', 'one-click-order-re-order' ) );
					wp_die();
				} else {
					wc_add_notice( __( "Couldn't added to basket.", 'one-click-order-re-order' ), 'error' );
					wp_send_json_error( __( "Couldn't added to basket.", 'one-click-order-re-order' ) );
					wp_die();
				}
			}
	}
	/**
	 * Remove items from Basket
	 *
	 * @since    1.0.0
	 */
	public function mwb_ocor_remove_from_basket() {
			$check_ajax = check_ajax_referer( 'mwb-cng-ajax-seurity-nonce', 'ajax_nonce' );
			if ( !$check_ajax ) {
				exit( 'failed' );
			}

			$item_id 		= isset( $_POST[ 'item_id' ] ) ? $_POST[ 'item_id' ] : '';
			$user_id 		= isset( $_POST[ 'user_id' ] ) ? $_POST[ 'user_id' ] : '';
			$type 			= isset( $_POST[ 'type' ] ) ? $_POST[ 'type' ] : '';
			$variation_id 	= isset( $_POST[ 'variation_id' ] ) ? $_POST[ 'variation_id' ] : '';
			if ( get_current_user_id() == $user_id ) {
				$updated = false;
				$basket = get_user_meta( $user_id, 'mwb_ocor_basket_info', true );
				if ( ! empty( $basket ) ) {
					if ( $type == 'variable' ) {
						foreach ( $basket[ $item_id ][ 'variations' ] as $var_id => $variation_array ) {
							if ( empty( $var_id ) ) {
								continue;
							}

							if ( $variation_id != $var_id ) {
								continue;
							}

							unset( $basket[ $item_id ][ 'variations' ][$var_id] );
						}
						if ( empty( $basket[ $item_id ] ) ) {
							unset( $basket[ $item_id ] );
						}
					} else {
						unset( $basket[ $item_id ] );
					}
					
					$updated = update_user_meta( $user_id, 'mwb_ocor_basket_info', $basket );
				}

				if ( $updated ) {
					wp_send_json_success( __( 'Removed from basket.', 'one-click-order-re-order' ) );
					wp_die();
				} else {
					wc_add_notice( __( "Couldn't removed from basket.", 'one-click-order-re-order' ) );
					wp_send_json_error( __( "Couldn't removed from basket.", 'one-click-order-re-order' ) );
					wp_die();
				}
			}
		}
	 /**
	 * Fetch all items in basket and their informations.
	 *
	 * @since    1.0.0
	 */
		public function mwb_ocor_get_basket_items() {
			$check_ajax = check_ajax_referer( 'mwb-cng-ajax-seurity-nonce', 'ajax_nonce' );
			if ( !$check_ajax ) {
				wc_add_notice( __( "Ajax nonce couldn't match.", 'one-click-order-re-order' ) );
				wp_send_json_error( __( "Ajax nonce couldn't match.", 'one-click-order-re-order' ) );
				wp_die();
			}

			if ( ! is_user_logged_in() ) {
				wc_add_notice( __( "Please login first.", 'one-click-order-re-order' ), 'error' );
				wp_send_json_error( __( "Sorry please login first.", 'one-click-order-re-order' ) );
				wp_die();
			}

			$foundItems 	= array();
			$basketItemsIds = get_user_meta( get_current_user_id(), 'mwb_ocor_basket_info', true );
			if ( empty( $basketItemsIds ) or ! is_array( $basketItemsIds ) ) {
				wc_add_notice( __( "No items found in you basket.", 'one-click-order-re-order' ), 'error' );
				wp_send_json_error( __( "No items found in you basket.", 'one-click-order-re-order' ) );
				wp_die();
			}

			foreach ( $basketItemsIds as $item_id => $item ) {
				if ( empty( $item_id ) or empty( $item ) ) {
					continue;
				}

				$product 	= wc_get_product( $item_id );
				if ( empty( $product ) ) {
					continue;
				}
				$key = $item_id;
				if ( $item[ 'type' ] == 'variable' ) {
					foreach ( $item[ 'variations' ] as $var_id => $variation_array ) {
						$key = "{$item_id}_{$var_id}";
						$productVariation 	= new WC_Product_Variation( $var_id );
						$foundItems[ $key ][ 'type' ] 			=  $item[ 'type' ];
						$foundItems[ $key ][ 'attributes' ] 	=  $productVariation->get_formatted_variation_attributes();
						$foundItems[ $key ][ 'item_id' ] 		= $item_id;
						$foundItems[ $key ][ 'title' ] 			= $product->post->post_title;
						$foundItems[ $key ][ 'image' ] 			= $product->get_image();
						$foundItems[ $key ][ 'availability' ] 	= $product->post->post_status != 'publish' ? 'not_availale' : 'available';
						$foundItems[ $key ][ 'qty' ] 			= $variation_array[ 'qty' ];
						$foundItems[ $key ][ 'stock' ] 			= $product->is_in_stock() ? "in_stock" : 'out_of_stock' ;
						$foundItems[ $key ][ 'permalink' ] 		= $product->get_permalink( $var_id );
					}
				} else {
					$foundItems[ $key ][ 'type' ] 			=  $item[ 'type' ];
					$foundItems[ $key ][ 'item_id' ] 		= $item_id;
					$foundItems[ $key ][ 'title' ] 			= $product->post->post_title;
					$foundItems[ $key ][ 'image' ] 			= $product->get_image();
					$foundItems[ $key ][ 'availability' ] 	= $product->post->post_status != 'publish' ? 'not_availale' : 'available';
					$foundItems[ $key ][ 'qty' ] 			= $item[ 'qty' ];
					$foundItems[ $key ][ 'stock' ] 			= $product->is_in_stock() ? "in_stock" : 'out_of_stock' ;
					$foundItems[ $key ][ 'permalink' ] 		= $product->get_permalink();
				}
			}
			if( empty( $foundItems ) ) {
				wc_add_notice( __( 'No items found in your basket.', 'one-click-order-re-order' ), 'error' );
				wp_send_json_error( __( 'No items found.', 'one-click-order-re-order' ) );
				wp_die();
			}

			wp_send_json_success( $foundItems );
			wp_die();
		}
      /**
	 * Rest Api for Order Re-order plugin.
	 *
	 * @since    1.0.0
	 */
	  public function mwb_rest_callback()
	  {
	  	register_rest_route('cng/v1','/order/(?P<id>\d+)/(?P<uid>\d+)',array(
              "method"=>'GET',
              'callback' => array($this,'get_items'),
          	 'permission_callback' => array( $this, 'get_items_permissions_check' ),
              
	  		));
	  }

	  public function get_items_permissions_check( $request ) {
		//return true; <--use to make readable by all
		$user_info = get_userdata($request['uid']);
		 if(!empty($user_info))
		 {
		 	return true;
		 }
		 else
		 {
		 	return false;
		 }
		
	}
     /**
	 * Function for Order Re-order Api callback.
	 *
	 * @since    1.0.0
	 */
	  public function get_items($data)
	  {
	  	//$uid=get_header();
          // global $woocommerce;
          	$mwb_order_id =$data['id'];
          	
			if ( WC ()->cart->get_cart_contents_count() ) 
			{
				WC ()->cart->empty_cart();
			}
			$mwb_error = array();
			$mwb_order = new WC_Order ( trim ( $mwb_order_id ) );
			if(WC()->version < '3.0.0')
			{
				$mwb_order_iddd =  $mwb_order->id;
				if ( empty ( $mwb_order_iddd ) ) 
				{
					exit( 'failed' );
				}
			}
			else
			{
				$mwb_order_iddd =  $mwb_order->get_id();
				if ( empty ($mwb_order_iddd ) )
				 {
					exit( 'failed' );
				}
			}
			foreach ( $mwb_order->get_items() as $mwb_product_info ) 
			{
             
            
				$mwb_product_id = ( int ) apply_filters ( 'woocommerce_add_to_cart_product_id', $mwb_product_info ['product_id'] );
				$mwb_qty = ( int ) $mwb_product_info ['qty'];
				$mwb_all_variations = array ();
				$mwb_variation_id = ( int ) $mwb_product_info[ 'variation_id' ];
			
				$mwb_cart_product_data = apply_filters ( 'woocommerce_order_again_cart_item_data', array (), $mwb_product_info, $mwb_order );
				foreach ( $mwb_product_info ['item_meta'] as $mwb_product_meta_name => $mwb_product_meta_value ) 
				{
					if ( taxonomy_is_product_attribute( $mwb_product_meta_name ) )
					 {
						$mwb_all_variations [$product_meta_name] = $mwb_product_meta_value[0];
					 } 
					else 
					{
						if ( meta_is_product_attribute( $mwb_product_meta_name, $mwb_product_meta_value[0], $mwb_product_id ) ) 
						{
							$mwb_all_variations[ $mwb_product_meta_name ] = $mwb_product_meta_value[0];
						}
					}
				}
				// Add to cart validation
				if (! apply_filters ( 'woocommerce_add_to_cart_validation', true, $mwb_product_id, $mwb_qty, $mwb_variation_id, $mwb_all_variations, $mwb_cart_product_data )) 
				{
					continue;
				}
			 
				// Checks availability of products
				$mwb_array = wc_get_product( $mwb_product_id );
			
				// Add to cart order products
                 
				$mwb_add_to_cart = WC ()->cart->add_to_cart ( $mwb_product_id, $mwb_qty, $mwb_variation_id, $mwb_all_variations, $mwb_cart_product_data );
			}
             

            $cart_data= get_user_meta($data['uid'], '_woocommerce_persistent_cart_'. get_current_blog_id(), true) ;
	      return new WP_REST_Response($cart_data, 200 );
	
	  }
	  public function get_all_order_data_array($orders_array) {
	  	$mwb_order_data_array = array();
	  	if(is_array($orders_array) && !empty($orders_array)) {
	  		$order = wc_get_order($order_id);
	  		$mwb_order_data_array['view_url'] = $order->get_view_order_url();
	  		$mwb_order_data_array['order_date']	= $order->order_date;
	  		$mwb_order_number['order_number'] = $order->get_order_number();

	  	}

	  }
	  /**
	 * Function For showing latest two.
	 *
	 * @since    1.0.0
	 */
	  public function mwb_fetch_latest_order() {

	  	$mwb_hide ='';
	  	if(!null == WC()->session->get('hide')) {

	  	  $mwb_hide  = WC()->session->get('hide');
	  	}
	  	$mwb_current_user = get_current_user_id();
	  	$query = new WC_Order_Query( array(
	  		'limit' => 2,
	  		'orderby' => 'date',
	  		'order' => 'DESC',
	  		'return' => 'ids',
	  		'customer_id'=>$mwb_current_user,
	  		) );
	  	$orders = $query->get_orders();
	  	if(is_front_page() && is_user_logged_in() && $mwb_hide != '1' ) {
	  	?>
	  	<div id="mwb_cng_prodcts_exclude" class="mwb_cng_popup-wrapper">
	  		<div class="mwb_cng_popup-overlay">
	  			<div class="mwb_cng_popup-container mwb_cng_no_content">
	  				<div class="mwb_cng_popup-heading">
	  					<h1><strong><?php _e("YOUR PREVIOUS TRUMAN’S ORDER","one-click-order-re-order") ?></strong></h1>
	  					<a class="mwb_cng_close mwb_cng_close_wrapper mwb_oco_popup">×</a>
	  				</div>
	  				<div class="mwb_cng_popup-content">
	  					
	  					<div class="mwb_cng_popup-content mwb_cng_tbl_body">
	  						<table>
	  								<thead>
	  									<th><?php _e('ORDER','one-click-order-re-order');?></th>
	  									<th><?php _e('DATE','one-click-order-re-order');?></th>
	  									<th><?php _e('ITEMS','one-click-order-re-order');?></th>
	  									<th><?php _e('QTY','one-click-order-re-order');?></th>
	  									<th><?php _e('Action','one-click-order-re-order');?></th>
	  								</thead>
	  							<?php 
	  							if(is_array($orders) && !empty($orders)) {

	  							foreach ($orders as $key => $order_id) {
	  								?>
	  								<tr>
	  								<?php 
	  								 $order = wc_get_order($order_id);
	  								 $item_count = $order->get_item_count();
	  								?>
	  								<td>
	  									<a href="<?php echo esc_url( $order->get_view_order_url() ); ?>">
	  										<?php echo _x( '#', 'hash before order number', 'woocommerce' ) . $order->get_order_number(); ?>
	  									</a>
	  								</td>
	  								<td>
	  									<time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></time>
	  								</td>
	  								<td>
	  									<!-- <?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?> -->
	  									<?php
	  										foreach( $order->get_items() as $item_id => $item ){
	  											?>
	  											<div><p><?php echo $item->get_name(); ?></p></div>
	  											<?php
	  										}
	  									 ?>
	  								</td>
	  								<td><!-- <?php
	  									/* translators: 1: formatted order total 2: total order items */
	  									//printf( _n( '%1$s for %2$s item', '%1$s for %2$s items', $item_count, 'woocommerce' ), $order->get_formatted_order_total(), $item_count );
	  									?> -->
	  									<?php
	  									 foreach( $order->get_items() as $item_id => $item ){
	  											?>
	  											<div><p><?php echo $item->get_quantity(); ?></p></div>
	  											<?php
	  										}
	  									?>
	  								</td>
	  								<td>
	  									<?php
	  									$actions = wc_get_account_orders_actions( $order );
	  									if ( ! empty( $actions ) ) {

	  										foreach ( $actions as $key => $action ) {
	  											if($key == "mwb_my_account_reorder"
	  												){
	  												$action['name'] = "ADD ALL ITEMSTO CART";
	  											echo '<a href="' . esc_url( $action['url'] ) . '" class="woocommerce-button button ' . sanitize_html_class( $key ) . '">' . esc_html( $action['name'] ) . '</a>';
	  											}
	  											if($key == "mwb_my_account_place_same_order") {
	  												$action['name'] = "SELECT RE-ORDER";
	  												echo '<a href="' . esc_url( $action['url'] ) . '" class="woocommerce-button button ' . sanitize_html_class( $key ) . '">' . esc_html( $action['name'] ) . '</a>';
	  											}
	  										}
	  									}
	  									?>
	  								</td>
	  								</tr>
	  								<?php
	  									
	  								}
	  								

	  								
	  							}
	  							else {
	  								echo "<tr><td>No orders</td></tr>";
	  							}
	  							?>
	  							
	  							
	  						</table>
	  					</div>
	  				</div>
	  				<!-- <div class="mwb_cng_popup-buttons"><a id="mwb_cng_popup-submit-btn" class="mwb_cng_popup-btn" data-order_id="236"><span>Checkout</span></a>
	  			</div> -->
	  		</div>
	  	</div>
	  </div>
	  	<?php
	     }
	  	// print_r($orders); die("hello");
	  }
	 public function is_selected_page() {

		global $wp_query;

		$is_selected = false;
		$general_settings = get_option('one_click_order_re_order_enable_plug',false);
		$mwb_selected_pages = $general_settings['selectedPage_popup'];


		if( empty($mwb_selected_pages)) {

			$is_selected = true;
		}
		elseif (is_single()&&!empty($mwb_selected_pages)) {

			$page_id ='details';
			if(in_array($page_id, $mwb_selected_pages)){

				$is_selected = true;
			}
		}
		elseif(empty($mwb_selected_pages)) {

			$is_selected = true;
		}
		elseif(!is_shop()&&!is_home() &&!empty($mwb_selected_pages)) {

			$page = $wp_query->get_queried_object();
			if(isset($page->ID)) {
				$page_id = $page->ID;

				if(in_array($page_id, $mwb_selected_pages)) {

					$is_selected = true;
				}
			}	
		}
		elseif (is_shop()&&!empty($mwb_selected_pages)) {
			$page_id = wc_get_page_id('shop');

			if(in_array($page_id, $mwb_selected_pages)) {

				$is_selected = true;
			}

		}
		
		else {

			$is_selected = false;
		}

		return $is_selected;
	}
	public function mwb_cng_hide_popup() {
		$check_ajax = check_ajax_referer( 'mwb-cng-ajax-seurity-nonce', 'ajax_nonce' );
		$mwb_hide = $_POST['mwb_hide'];
		WC()->session->set('hide', $mwb_hide);
		echo "hello";
		wp_die();

	}
	public function mwb_cng_clear_session() {
	 WC()->session->__unset( 'hide' );
	}
}
