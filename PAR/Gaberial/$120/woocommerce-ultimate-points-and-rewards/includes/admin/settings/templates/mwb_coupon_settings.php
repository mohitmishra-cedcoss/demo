<?php 
/**
 * Exit if accessed directly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Coupons Setting Template
 */
$current_tab = "mwb_wpr_coupons_tab";

if(isset($_GET['tab']))
{
	$current_tab = $_GET['tab'];
}
if(isset($_POST['mwb_wpr_save_coupon']))
{	
	?>
	<div class="notice notice-success is-dismissible">
	    <p><strong><?php _e('Settings saved.',MWB_WPR_Domain); ?></strong></p>
	    <button type="button" class="notice-dismiss">
	        <span class="screen-reader-text"><?php _e('Dismiss this notice.',MWB_WPR_Domain); ?></span>
	    </button>
	</div><?php
	if($current_tab=="mwb_wpr_coupons_tab")
	{
		$coupon_settings_array = array();
		
		$mwb_minimum_points_value = (isset($_POST['mwb_wpr_general_minimum_value']) && $_POST['mwb_wpr_general_minimum_value'] != null) ? sanitize_post($_POST['mwb_wpr_general_minimum_value']) : 50;
		$mwb_maxminum_points_value = (isset($_POST['mwb_wpr_general_maximum_value']) && $_POST['mwb_wpr_general_maximum_value'] != null) ? sanitize_post($_POST['mwb_wpr_general_maximum_value']) : "";
		
		$mwb_wpr_coupon_conversion_price = (isset($_POST['mwb_wpr_coupon_conversion_price']) && $_POST['mwb_wpr_coupon_conversion_price'] != null) ? sanitize_post($_POST['mwb_wpr_coupon_conversion_price']) : 1;
		
		$mwb_wpr_coupon_conversion_points = (isset($_POST['mwb_wpr_coupon_conversion_points']) && $_POST['mwb_wpr_coupon_conversion_points'] != null) ? sanitize_post($_POST['mwb_wpr_coupon_conversion_points']) : 1;
		
		$mwb_wpr_coupon_conversion_enable = isset($_POST['mwb_wpr_coupon_conversion_enable']) ? 1 : 0;

		$enable_custom_convert_point = isset($_POST['mwb_wpr_general_custom_convert_enable']) ? 1 : 0;
		
		$enable_coupon_individual = isset($_POST['mwb_wpr_coupon_individual_use']) ? 1 : 0;
		
		$enable_coupon_free_shipping = isset($_POST['mwb_wpr_points_freeshipping']) ? 1 : 0;

		$mwb_coupon_length = (isset($_POST['mwb_wpr_points_coupon_length']) && $_POST['mwb_wpr_points_coupon_length'] != null) ? sanitize_post($_POST['mwb_wpr_points_coupon_length']) : 5;

		$coupon_expiry = (isset($_POST['mwb_wpr_coupon_expiry']) && $_POST['mwb_wpr_coupon_expiry'] != null) ? sanitize_post($_POST['mwb_wpr_coupon_expiry']) : 1;

		$coupon_minspend = (isset($_POST['mwb_wpr_coupon_minspend']) && $_POST['mwb_wpr_coupon_minspend'] != null) ? sanitize_post($_POST['mwb_wpr_coupon_minspend']) : 0;

		$coupon_maxspend = (isset($_POST['mwb_wpr_coupon_maxspend']) && $_POST['mwb_wpr_coupon_maxspend'] != null) ? sanitize_post($_POST['mwb_wpr_coupon_maxspend']) : 0;

		$coupon_use = (isset($_POST['mwb_wpr_coupon_use']) && $_POST['mwb_wpr_coupon_use'] != null) ? sanitize_post($_POST['mwb_wpr_coupon_use']) : 0;

		$coupon_redeem_price = (isset($_POST['mwb_wpr_coupon_redeem_price']) && $_POST['mwb_wpr_coupon_redeem_price'] != null) ? sanitize_post($_POST['mwb_wpr_coupon_redeem_price']) : 1;

		$coupon_redeem_points = (isset($_POST['mwb_wpr_coupon_redeem_points']) && $_POST['mwb_wpr_coupon_redeem_points'] != null) ? sanitize_post($_POST['mwb_wpr_coupon_redeem_points']) : 1;


		$coupon_settings_array['mwb_minimum_points_value'] = $mwb_minimum_points_value;
		$coupon_settings_array['mwb_maxminum_points_value'] = $mwb_maxminum_points_value;
		$coupon_settings_array['enable_custom_convert_point'] = $enable_custom_convert_point;
		$coupon_settings_array['enable_coupon_individual'] = $enable_coupon_individual;
		$coupon_settings_array['enable_coupon_free_shipping'] = $enable_coupon_free_shipping;
		$coupon_settings_array['mwb_coupon_length'] = $mwb_coupon_length;
		$coupon_settings_array['coupon_expiry'] = $coupon_expiry;
		$coupon_settings_array['coupon_minspend'] = $coupon_minspend;
		$coupon_settings_array['coupon_maxspend'] = $coupon_maxspend;
		$coupon_settings_array['coupon_use'] = $coupon_use;
		$coupon_settings_array['coupon_redeem_price'] = $coupon_redeem_price;
		$coupon_settings_array['coupon_redeem_points'] = $coupon_redeem_points;
		$coupon_settings_array['mwb_wpr_coupon_conversion_enable'] = $mwb_wpr_coupon_conversion_enable;
		$coupon_settings_array['mwb_wpr_coupon_conversion_price'] = $mwb_wpr_coupon_conversion_price;
		$coupon_settings_array['mwb_wpr_coupon_conversion_points'] = $mwb_wpr_coupon_conversion_points;

		if(is_array($coupon_settings_array))
			update_option('mwb_wpr_coupons_gallery',$coupon_settings_array);
	}
}
?>
<?php $coupon_settings = get_option('mwb_wpr_coupons_gallery',true); ?>
<?php if(!is_array($coupon_settings)): $coupon_settings = array(); endif;?>
<div class="mwb_table">

	<table class="form-table mwb_wpr_coupon_setting mwp_wpr_setting">
	<?php 
		$mwb_minimum_points_value = (isset($coupon_settings['mwb_minimum_points_value']) && $coupon_settings['mwb_minimum_points_value'] != null) ? intval($coupon_settings['mwb_minimum_points_value']) : 50;
		$mwb_maxminum_points_value = (isset($coupon_settings['mwb_maxminum_points_value']) && $coupon_settings['mwb_maxminum_points_value'] != null) ? intval($coupon_settings['mwb_maxminum_points_value']) :10;

		$enable_custom_convert_point = isset($coupon_settings['enable_custom_convert_point']) ? intval($coupon_settings['enable_custom_convert_point']) : 0;

		$enable_coupon_individual = isset($coupon_settings['enable_coupon_individual']) ? intval($coupon_settings['enable_coupon_individual']) : 0;

		$enable_coupon_free_shipping = isset($coupon_settings['enable_coupon_free_shipping']) ? intval($coupon_settings['enable_coupon_free_shipping']) : 0;

		$mwb_coupon_length = (isset($coupon_settings['mwb_coupon_length']) && $coupon_settings['mwb_coupon_length'] != null) ? intval($coupon_settings['mwb_coupon_length']) : 5;

		$coupon_expiry = ( isset($coupon_settings['coupon_expiry'] ) && $coupon_settings['coupon_expiry'] != null) ? intval($coupon_settings['coupon_expiry']) : 1;

		$coupon_minspend = (isset($coupon_settings['coupon_minspend']) && $coupon_settings['coupon_minspend'] != null) ? intval($coupon_settings['coupon_minspend']) : "";

		$coupon_maxspend = (isset($coupon_settings['coupon_maxspend']) && $coupon_settings['coupon_maxspend'] != null) ? intval($coupon_settings['coupon_maxspend']) : "";

		$coupon_use = (isset($coupon_settings['coupon_use']) && $coupon_settings['coupon_use'] != null) ? intval($coupon_settings['coupon_use']) : "";

		$coupon_redeem_price = (isset($coupon_settings['coupon_redeem_price']) && $coupon_settings['coupon_redeem_price'] != null) ? $coupon_settings['coupon_redeem_price'] : 1;

		$coupon_redeem_points = (isset($coupon_settings['coupon_redeem_points']) && $coupon_settings['coupon_redeem_points'] != null) ? intval($coupon_settings['coupon_redeem_points']) : 1;

		$mwb_wpr_coupon_conversion_enable = isset($coupon_settings['mwb_wpr_coupon_conversion_enable']) ? intval($coupon_settings['mwb_wpr_coupon_conversion_enable']) : 0;

		$mwb_wpr_coupon_conversion_price = (isset($coupon_settings['mwb_wpr_coupon_conversion_price']) && $coupon_settings['mwb_wpr_coupon_conversion_price'] != null) ? intval($coupon_settings['mwb_wpr_coupon_conversion_price']) : 1;

		$mwb_wpr_coupon_conversion_points = (isset($coupon_settings['mwb_wpr_coupon_conversion_points']) && $coupon_settings['mwb_wpr_coupon_conversion_points'] != null) ? intval($coupon_settings['mwb_wpr_coupon_conversion_points']) : 1;
				
	?>
		<tbody>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="mwb_wpr_coupon_redeem_value"><?php _e('Redeem Points Conversion', MWB_WPR_Domain)?></label>
				</th>
				<td class="forminp forminp-text">
					<?php 
					$attribute_description = __('Enter the redeem points for coupons.(i.e., how many points will be equivalent to the amount)', MWB_WPR_Domain);
					echo wc_help_tip( $attribute_description );
					?>
					<label for="mwb_wpr_coupon_redeem_value">
						 
						<input type="number" min="1" value="<?php echo $coupon_redeem_points;?>" name="mwb_wpr_coupon_redeem_points" id="mwb_wpr_coupon_redeem_points" class="input-text wc_input_price mwb_wpr_new_woo_ver_style_text">
						<?php echo __("Points ", MWB_WPR_Domain); ?>  =
						<?php echo get_woocommerce_currency_symbol(); ?>
						<input type="text" value="<?php echo $coupon_redeem_price;?>" name="mwb_wpr_coupon_redeem_price" id="mwb_wpr_coupon_redeem_price" class="input-text mwb_wpr_new_woo_ver_style_text wc_input_price ">
					</label>						
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="mwb_wpr_coupon_individual_use"><?php _e('Enable Per Currency Points Conversion', MWB_WPR_Domain)?></label>
				</th>
				<td class="forminp forminp-text">
					<?php 
					$attribute_description = __('Check this box if you want enable per currency points conversion.', MWB_WPR_Domain);
					echo wc_help_tip( $attribute_description );
					?>
					<label for="mwb_wpr_coupon_conversion_enable">
						<input type="checkbox" <?php checked($mwb_wpr_coupon_conversion_enable,1);?> name="mwb_wpr_coupon_conversion_enable" id="mwb_wpr_coupon_conversion_enable" class="input-text"> <?php _e('Allow per currency points conversion',MWB_WPR_Domain);?>
					</label>
					
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="mwb_wpr_coupon_conversion_value"><?php _e('Per ', MWB_WPR_Domain)?><?php echo get_woocommerce_currency_symbol(); ?><?php _e(' Points Conversion', MWB_WPR_Domain)?></label>
				</th>
				<td class="forminp forminp-text">
					<?php 
					$attribute_description = __('Enter the redeem price for points.(i.e., how much amounts will be equivalent to the points)', MWB_WPR_Domain);
					echo wc_help_tip( $attribute_description );
					?>
					<label for="mwb_wpr_coupon_conversion_value">
						 
						<?php echo get_woocommerce_currency_symbol(); ?>
						<input type="text"  value="<?php echo $mwb_wpr_coupon_conversion_price;?>" name="mwb_wpr_coupon_conversion_price" id="mwb_wpr_coupon_conversion_price" class="input-text wc_input_price mwb_wpr_new_woo_ver_style_text">=
						<input type="number" min="1" value="<?php echo $mwb_wpr_coupon_conversion_points;?>" name="mwb_wpr_coupon_conversion_points" id="mwb_wpr_coupon_conversion_points" class="input-text wc_input_price mwb_wpr_new_woo_ver_style_text">
						<?php echo __("Points ", MWB_WPR_Domain); ?>  
					</label>						
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="mwb_wpr_general_minimum_value"><?php _e('Enter Minimum Points Required For Generating Coupon', MWB_WPR_Domain)?></label>
				</th>
				<td class="forminp forminp-text">
					<?php 
					$attribute_description = __('The minimum points customer requires for converting their points to coupon.', MWB_WPR_Domain);
					echo wc_help_tip( $attribute_description );
					?>
					<label for="mwb_wpr_general_minimum_value">
						<input type="number" min="1" value="<?php echo $mwb_minimum_points_value;?>" name="mwb_wpr_general_minimum_value" id="mwb_wpr_general_minimum_value" class="input-text mwb_wpr_new_woo_ver_style_text">
					</label>						
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="mwb_wpr_general_maximum_value"><?php _e('Enter Maximum Points Generating Coupon', MWB_WPR_Domain)?></label>
				</th>
				<td class="forminp forminp-text">
					<?php 
					$attribute_description = __('The maximum points customer requires for converting their points to coupon.', MWB_WPR_Domain);
					echo wc_help_tip( $attribute_description );
					?>
					<label for="mwb_wpr_general_maximum_value">
						<input type="number" min="1" value="<?php echo $mwb_maxminum_points_value;?>" name="mwb_wpr_general_maximum_value" id="mwb_wpr_general_maximum_value" class="input-text mwb_wpr_new_woo_ver_style_text">
					</label>						
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="mwb_wpr_general_custom_convert_enable"><?php _e('Enable Custom Convert Points', MWB_WPR_Domain)?></label>
				</th>
				<td class="forminp forminp-text">
					<?php 
					$attribute_description = __('Check this box to allow customers to convert their custom points to coupon out of their total points.', MWB_WPR_Domain);
					echo wc_help_tip( $attribute_description );
					?>
					<label for="mwb_wpr_general_custom_convert_enable">
						<input type="checkbox" <?php checked($enable_custom_convert_point,1);?> name="mwb_wpr_general_custom_convert_enable" id="mwb_wpr_general_custom_convert_enable" class="input-text"> <?php _e('Enable to allow customers to convert some of the points to coupon out of their given total points.',MWB_WPR_Domain);?>
					</label>						
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="mwb_wpr_coupon_individual_use"><?php _e('Individual Use', MWB_WPR_Domain)?></label>
				</th>
				<td class="forminp forminp-text">
					<?php 
					$attribute_description = __('Check this box if the Coupon cannot be used in conjunction with other Coupons.', MWB_WPR_Domain);
					echo wc_help_tip( $attribute_description );
					?>
					<label for="mwb_wpr_coupon_individual_use">
						<input type="checkbox" <?php checked($enable_coupon_individual,1);?> name="mwb_wpr_coupon_individual_use" id="mwb_wpr_coupon_individual_use" class="input-text"> <?php _e('Allow Coupons to use Individually',MWB_WPR_Domain);?>
					</label>
					
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="mwb_wpr_points_freeshipping"><?php _e('Free Shipping', MWB_WPR_Domain)?></label>
				</th>
				<td class="forminp forminp-text">
					<?php 
					$attribute_description = __('Check this box if the coupon grants free shipping. A free shipping method must be enabled in your shipping zone and be set to require "a valid free shipping coupon" (see the "Free Shipping Requires" setting).', MWB_WPR_Domain);
					echo wc_help_tip( $attribute_description );
					?>
					<label for="mwb_wpr_points_freeshipping">
						<input type="checkbox" <?php checked($enable_coupon_free_shipping,1);?> name="mwb_wpr_points_freeshipping" id="mwb_wpr_points_freeshipping" class="input-text" value="yes"> <?php _e('Allow Coupons on Free Shipping',MWB_WPR_Domain);?>
					</label>
				</td>
			</tr>
			
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="mwb_wpr_points_coupon_length"><?php _e('Coupon Length',MWB_WPR_Domain)?></label>
				</th>
				<td class="forminp forminp-text">
					<?php 
					$attribute_description = __('Enter Coupon length excluding the prefix.(Minimum length is set to 5)', MWB_WPR_Domain);
					echo wc_help_tip( $attribute_description );
					?>
					<input type="number" min="5" max="10" value="<?php echo $mwb_coupon_length;?>" name="mwb_wpr_points_coupon_length" id="mwb_wpr_points_coupon_length" class="input-text mwb_wpr_new_woo_ver_style_text" > 	
				</td>
			</tr>
			
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="mwb_wpr_coupon_expiry"><?php _e('Coupon Expiry After Days', MWB_WPR_Domain)?></label>
				</th>
				<td class="forminp forminp-text">
					<?php 
					$attribute_description = __('Enter number of days after which Coupon will get expired. Keep value "1" for one day expiry when order is completed. Keep value "0" for no expiry.', MWB_WPR_Domain);
					echo wc_help_tip( $attribute_description );
					?>
					<input type="number" min="0" value="<?php echo $coupon_expiry;?>" name="mwb_wpr_coupon_expiry" id="mwb_wpr_coupon_expiry" class="input-text mwb_wpr_new_woo_ver_style_text" > 	
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="mwb_wpr_coupon_minspend"><?php _e('Minimum Spend', MWB_WPR_Domain)?></label>
				</th>
				<td class="forminp forminp-text">
					<?php 
					$attribute_description = __('This field allows you to set the minimum spend (subtotal, including taxes) allowed to use the coupon.', MWB_WPR_Domain);
					echo wc_help_tip( $attribute_description );
					?>
					<input type="number" min="0" value="<?php echo $coupon_minspend;?>" name="mwb_wpr_coupon_minspend" id="mwb_wpr_coupon_minspend" class="input-text mwb_wpr_new_woo_ver_style_text" > 	
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="mwb_wpr_coupon_maxspend"><?php _e('Maximum Spend', MWB_WPR_Domain)?></label>
				</th>
				<td class="forminp forminp-text">
					<?php 
					$attribute_description = __('This field allows you to set the maximum spend (subtotal, including taxes) allowed when using the Coupon.', MWB_WPR_Domain);
					echo wc_help_tip( $attribute_description );
					?>
					<input type="number" min="0" value="<?php echo $coupon_maxspend;?>" name="mwb_wpr_coupon_maxspend" id="mwb_wpr_coupon_maxspend" class="input-text mwb_wpr_new_woo_ver_style_text" > 	
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="mwb_wpr_coupon_use"><?php _e('Coupon No of time uses', MWB_WPR_Domain)?></label>
				</th>
				<td class="forminp forminp-text">
					<?php 
					$attribute_description = __('How many times this coupon can be used before Coupon is void.', MWB_WPR_Domain);
					echo wc_help_tip( $attribute_description );
					?>
					<input type="number"  min="0" value="<?php echo $coupon_use;?>" name="mwb_wpr_coupon_use" id="mwb_wpr_coupon_use" class="input-text mwb_wpr_new_woo_ver_style_text" > 	
				</td>
			</tr>
		</tbody>
	</table>
</div>
<div class="clear"></div>	
<p class="submit">
	<input type="submit" value='<?php _e("Save changes",MWB_WPR_Domain); ?>' class="button-primary woocommerce-save-button mwb_wpr_save_changes" name="mwb_wpr_save_coupon">
</p>