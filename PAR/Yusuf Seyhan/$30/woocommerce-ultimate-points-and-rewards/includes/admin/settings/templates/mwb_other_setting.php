<?php 
/**
 * Exit if accessed directly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/*
 * General Settings Template
 */
$current_tab = "mwb_wpr_othersetting_tab";

if(isset($_GET['tab']))
{
	$current_tab = $_GET['tab'];
}
if(isset($_POST['mwb_wpr_save_othersetting']))
{	
	$_POST['mwb_wpr_disable_coupon_generation'] = isset($_POST['mwb_wpr_disable_coupon_generation']) ? 1: 0;
	$_POST['mwb_wpr_user_can_send_point'] = isset($_POST['mwb_wpr_user_can_send_point']) ? 1: 0;
	$_POST['mwb_wpr_referral_link_permanent'] = isset($_POST['mwb_wpr_referral_link_permanent']) ? 1 : 0;
	$_POST['mwb_wpr_ref_link_expiry'] = isset($_POST['mwb_wpr_ref_link_expiry']) ? sanitize_text_field($_POST['mwb_wpr_ref_link_expiry']) : 1 ;
	$_POST['mwb_wpr_custom_points_on_cart'] = isset($_POST['mwb_wpr_custom_points_on_cart']) ? 1 : 0;
	$_POST['mwb_wpr_cart_points_rate'] = isset($_POST['mwb_wpr_cart_points_rate']) && !empty($_POST['mwb_wpr_cart_points_rate']) ? sanitize_text_field($_POST['mwb_wpr_cart_points_rate']) : 1;
	$_POST['mwb_wpr_cart_price_rate'] = isset($_POST['mwb_wpr_cart_price_rate']) && !empty($_POST['mwb_wpr_cart_price_rate']) ? sanitize_text_field($_POST['mwb_wpr_cart_price_rate']) : 1;
	$_POST['mwb_wpr_apply_points_checkout'] = isset($_POST['mwb_wpr_apply_points_checkout']) ? 1 : 0;
	$_POST['mwb_wpr_make_readonly'] = isset($_POST['mwb_wpr_make_readonly']) ? 1 : 0;
	$_POST['mwb_wpr_notification_color'] = (isset($_POST['mwb_wpr_notification_color']) && $_POST['mwb_wpr_notification_color'] != null) ? $_POST['mwb_wpr_notification_color'] : '#55b3a5';
	$postdata = $_POST;
	foreach($postdata as $key=>$value){	
		$value = stripcslashes($value);
		$value = sanitize_text_field($value);
		if(isset($_POST['mwb_wpr_custom_points_on_cart']) && $_POST['mwb_wpr_custom_points_on_cart'] == 1){
			$general_settings = get_option('mwb_wpr_settings_gallery',true);
			if(array_key_exists('enable_purchase_points', $general_settings)){
				$general_settings['enable_purchase_points'] = 0;
			}
			update_option("mwb_wpr_settings_gallery",$general_settings);
		}
		update_option($key,$value);
	}
	?>
	<div class="notice notice-success is-dismissible">
	    <p><strong><?php _e('Settings saved.',MWB_WPR_Domain); ?></strong></p>
	    <button type="button" class="notice-dismiss">
	        <span class="screen-reader-text"><?php _e('Dismiss this notices.',MWB_WPR_Domain); ?></span>
	    </button>
	</div>
<?php
}
$mwb_wpr_shortcode_text_point = get_option("mwb_wpr_shortcode_text_point","Your Current Point");
$mwb_wpr_shortcode_text_membership = get_option("mwb_wpr_shortcode_text_membership","Your Current Level");
$mwb_wpr_thnku_order_msg_usin_points = get_option("mwb_wpr_thnku_order_msg_usin_points",false);
$mwb_wpr_thnku_order_msg = get_option("mwb_wpr_thnku_order_msg",false);
$mwb_wpr_disable_coupon_generation = get_option("mwb_wpr_disable_coupon_generation",0);
$mwb_wpr_user_can_send_point = get_option("mwb_wpr_user_can_send_point",0);
$mwb_wpr_referral_link_permanent = get_option("mwb_wpr_referral_link_permanent",0);
$mwb_wpr_ref_link_expiry = get_option("mwb_wpr_ref_link_expiry",1);
$mwb_wpr_cart_points_rate = get_option("mwb_wpr_cart_points_rate",1);
$mwb_wpr_cart_price_rate = get_option("mwb_wpr_cart_price_rate",1);
$mwb_wpr_custom_points_on_cart = get_option("mwb_wpr_custom_points_on_cart",0);
$mwb_wpr_apply_points_checkout = get_option("mwb_wpr_apply_points_checkout",0);
$mwb_wpr_make_readonly = get_option("mwb_wpr_make_readonly",0);
$mwb_wpr_notification_color = get_option("mwb_wpr_notification_color","#55b3a5");
?><div class="mwb_table">
	<table class="form-table mwb_wpr_general_setting mwp_wpr_settings">
		<tbody>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="mwb_wpr_other_shortcodes"><?php _e('Shortcodes', MWB_WPR_Domain)?></label>
				</th>
				<td class="forminp forminp-text">
						<p class="description"><?php _e('Use shortcode [MYCURRENTUSERLEVEL] for displaying current Membership Level of Users',MWB_WPR_Domain);?></p>
						<p class="description"><?php _e('Use shortcode [MYCURRENTPOINT] for displaying current Points of Users',MWB_WPR_Domain);?></p>
						<p class="description"><?php _e('Use shortcode [SIGNUPNOTIFICATION] for displaying notification anywhere on site',MWB_WPR_Domain);?></p>	
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="mwb_wpr_other_shortcode_text"><?php _e('Enter the text which you want to display with shortcode [MYCURRENTPOINT]', MWB_WPR_Domain)?></label>
				</th>
				<td class="forminp forminp-text">
					<?php 
					$attribute_description = __('Entered text will get displayed along with [MYCURRENTPOINT] shortcode', MWB_WPR_Domain);
					echo wc_help_tip( $attribute_description );
					?>
						<label for="mwb_wpr_shortcode_text_point">
						<input type="text" name="mwb_wpr_shortcode_text_point" value="<?php echo $mwb_wpr_shortcode_text_point;?>" id="mwb_wpr_shortcode_text_point" class="text_points mwb_wpr_new_woo_ver_style_text"></label>
						<p class="description"><?php _e('Entered text will get displayed along with [MYCURRENTPOINT] shortcode', MWB_WPR_Domain)?></p>	
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="mwb_wpr_general_signup"><?php _e('Enter the text which you want to display with shortcode [MYCURRENTUSERLEVEL]', MWB_WPR_Domain)?></label>
				</th>
				<td class="forminp forminp-text">
					<?php 
					$attribute_description = __('Entered text will get displayed along with [MYCURRENTUSERLEVEL] shortcode', MWB_WPR_Domain);
					echo wc_help_tip( $attribute_description );
					?>
						<label for="mwb_wpr_shortcode_text_membership">
						<input type="text" name="mwb_wpr_shortcode_text_membership" value="<?php echo $mwb_wpr_shortcode_text_membership;?>" id="mwb_wpr_shortcode_text_membership" class="text_points mwb_wpr_new_woo_ver_style_text"></label>
						<p class="description"><?php _e('Entered text will get displayed along with [MYCURRENTUSERLEVEL] shortcode', MWB_WPR_Domain)?></p>				
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="mwb_wpr_thnku_order_msg"><?php _e('Enter Thankyou Order Message When your customer gain some points', MWB_WPR_Domain)?></label>
				</th>
				<td class="forminp forminp-text">
					<?php 
					$attribute_description = __('Entered Message will appears at thankyou page when any order item is having some of the points', MWB_WPR_Domain);
					echo wc_help_tip( $attribute_description );
					?>
					<textarea cols="35" rows="5" name="mwb_wpr_thnku_order_msg" id="mwb_wpr_thnku_order_msg" class="input-text" ><?php echo $mwb_wpr_thnku_order_msg;?></textarea>
					<p class="description"><?php _e('Use these shortcodes for providing an appropriate message for your customers ', MWB_WPR_Domain);?><?php echo '[POINTS]'; _e(' for product points ',MWB_WPR_Domain); echo ' [TOTALPOINT]';_e(' for their Total Points ',MWB_WPR_Domain); ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="mwb_wpr_thnku_order_msg_usin_points"><?php _e('Enter Thankyou Order Message When your customer spent some of the points', MWB_WPR_Domain)?></label>
				</th>
				<td class="forminp forminp-text">
					<?php 
					$attribute_description = __('Entered Message will appears at thankyou page when any item has been purchased through points', MWB_WPR_Domain);
					echo wc_help_tip( $attribute_description );
					?>
					<textarea cols="35" rows="5" name="mwb_wpr_thnku_order_msg_usin_points" id="mwb_wpr_thnku_order_msg_usin_points" class="input-text" ><?php echo $mwb_wpr_thnku_order_msg_usin_points;?></textarea>
					<p class="description"><?php _e('Use these shortcodes for providing an appropriate message for your customers ', MWB_WPR_Domain);?><?php echo '[POINTS]'; _e(' for product points ',MWB_WPR_Domain); echo ' [TOTALPOINT]';_e(' for their Total Points ',MWB_WPR_Domain); ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="mwb_wpr_disable_coupon_generation"><?php _e('Disable Points Conversion', MWB_WPR_Domain)?></label>
				</th>
				<td class="forminp forminp-text">
					<?php 
					$attribute_description = __('Check this box if you want to disable the coupon generation functionality for customers', MWB_WPR_Domain);
					echo wc_help_tip( $attribute_description );
					?>
					<label for="mwb_wpr_disable_coupon_generation">
						<input type="checkbox" <?php checked($mwb_wpr_disable_coupon_generation,1);?> name="mwb_wpr_disable_coupon_generation" id="mwb_wpr_disable_coupon_generation" class="input-text"> <?php _e('Disable Points Conversion Fields',MWB_WPR_Domain);?>
					</label>						
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="mwb_wpr_user_can_send_point"><?php _e('Point Sharing', MWB_WPR_Domain)?></label>
				</th>
				<td class="forminp forminp-text">
					<?php 
					$attribute_description = __('Check this box if you want to let your customers to send some of the points from his/her account to any other user', MWB_WPR_Domain);
					echo wc_help_tip( $attribute_description );
					?>
					<label for="mwb_wpr_user_can_send_point">
						<input type="checkbox" <?php checked($mwb_wpr_user_can_send_point,1);?> name="mwb_wpr_user_can_send_point" id="mwb_wpr_user_can_send_point" class="input-text"> <?php _e('Enable Point Sharing',MWB_WPR_Domain);?>
					</label>						
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="mwb_wpr_referral_link_permanent"><?php _e('Static Referral Link', MWB_WPR_Domain)?></label>
				</th>
				<td class="forminp forminp-text">
					<?php 
					$attribute_description = __('Check this box if you want to make your referral link permanent.', MWB_WPR_Domain);
					echo wc_help_tip( $attribute_description );
					?>
					<label for="mwb_wpr_referral_link_permanent">
						<input type="checkbox" <?php checked($mwb_wpr_referral_link_permanent,1);?> name="mwb_wpr_referral_link_permanent" id="mwb_wpr_referral_link_permanent" class="input-text"> <?php _e('Make Referral Link Permanent',MWB_WPR_Domain);?>
					</label>						
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="mwb_wpr_ref_link_expiry"><?php _e('Referral Link Expiry', MWB_WPR_Domain)?></label>
				</th>
				<td class="forminp forminp-text">
					<?php 
					$attribute_description = __('Set the number of days after that the system will not able to remember the reffered user anymore', MWB_WPR_Domain);
					echo wc_help_tip( $attribute_description );
					?>
					<label for="mwb_wpr_ref_link_expiry">
						<input type="number"  name="mwb_wpr_ref_link_expiry" id="mwb_wpr_ref_link_expiry" value="<?php echo $mwb_wpr_ref_link_expiry;?>" class="input-text mwb_wpr_new_woo_ver_style_text" required> <?php _e('Days',MWB_WPR_Domain);?>
					</label>						
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="mwb_wpr_custom_points_on_cart"><?php _e('Redemption Over Cart Sub-Total', MWB_WPR_Domain)?></label>
				</th>
				<td class="forminp forminp-text">
					<?php 
					$attribute_description = __('Check this box if you want to let your customers to redeem their earned points for the cart subtotal, there would be no relation with product purchase through point feature', MWB_WPR_Domain);
					echo wc_help_tip( $attribute_description );
					?>
					<label for="mwb_wpr_custom_points_on_cart">
						<input type="checkbox" <?php checked($mwb_wpr_custom_points_on_cart,1);?> name="mwb_wpr_custom_points_on_cart" id="mwb_wpr_custom_points_on_cart" class="input-text"> <?php _e('No relation with Purchase Product Through Point Feature',MWB_WPR_Domain);?>
					</label>						
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="mwb_wpr_cart_points_rate"><?php _e('Conversion rate for Cart Sub-Total Redemption', MWB_WPR_Domain)?></label>
				</th>
				<td class="forminp forminp-text">
					<?php 
					$attribute_description = __('Enter the redeem points for cart sub-total. (i.e., how many points will be equivalent to your currency)', MWB_WPR_Domain);
					echo wc_help_tip( $attribute_description );
					?>
					<label for="mwb_wpr_cart_points_rate">
						 
						<input type="number" min="1" value="<?php echo $mwb_wpr_cart_points_rate;?>" name="mwb_wpr_cart_points_rate" id="mwb_wpr_cart_points_rate" class="input-text wc_input_price mwb_wpr_new_woo_ver_style_text">
						<?php echo __("Points ", MWB_WPR_Domain); ?>  =
						<?php echo get_woocommerce_currency_symbol(); ?>
						<input type="text" value="<?php echo $mwb_wpr_cart_price_rate;?>" name="mwb_wpr_cart_price_rate" id="mwb_wpr_cart_price_rate" class="input-text mwb_wpr_new_woo_ver_style_text wc_input_price ">
					</label>						
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="mwb_wpr_apply_points_checkout"><?php _e('Enable apply points during checkout', MWB_WPR_Domain)?></label>
				</th>
				<td class="forminp forminp-text">
					<?php 
					$attribute_description = __('Check this box if you want that customer can apply also apply points on checkout', MWB_WPR_Domain);
					echo wc_help_tip( $attribute_description );
					?>
					<label for="mwb_wpr_apply_points_checkout">
						<input type="checkbox" <?php checked($mwb_wpr_apply_points_checkout,1);?> name="mwb_wpr_apply_points_checkout" id="mwb_wpr_apply_points_checkout" class="input-text"> <?php _e('Allow customers to apply points during checkout also',MWB_WPR_Domain);?>
					</label>						
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="mwb_wpr_make_readonly"><?php _e('Make "Per Product Redemption" Readonly', MWB_WPR_Domain)?></label>
				</th>
				<td class="forminp forminp-text">
					<?php 
					$attribute_description = __('Check this box if you want to make the redemption box readonly(where end user can enter the number of points they want to redeem)', MWB_WPR_Domain);
					echo wc_help_tip( $attribute_description );
					?>
					<label for="mwb_wpr_make_readonly">
						<input type="checkbox" <?php checked($mwb_wpr_make_readonly,1);?> name="mwb_wpr_make_readonly" id="mwb_wpr_make_readonly" class="input-text"> <?php _e('Readonly for Enter Number of Points for Redemption',MWB_WPR_Domain);?>
					</label>						
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="mwb_wpr_notification_color"><?php _e('Select Color Notification Bar', MWB_WPR_Domain)?></label>
				</th>
				<td class="forminp forminp-text">
					<?php 
					$attribute_description = __('You can also choose the color for your Notification Bar.', MWB_WPR_Domain);
					echo wc_help_tip( $attribute_description );
					?>						
					<input type="color" id="mwb_wpr_notification_color" name="mwb_wpr_notification_color" value="<?php echo $mwb_wpr_notification_color;?>">				
					
				</td>				
			</tr>
		</tbody>
	</table></div>
<p class="submit">
	<input type="submit" value='<?php _e("Save changes",MWB_WPR_Domain); ?>' class="button-primary woocommerce-save-button mwb_wpr_save_changes" name="mwb_wpr_save_othersetting">
</p>
