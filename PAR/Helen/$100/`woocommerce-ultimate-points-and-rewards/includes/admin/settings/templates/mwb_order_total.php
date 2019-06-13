<?php
if(isset($_POST['mwb_wpr_save_order_totalsettings'])) {
	///===========custmizatin=================
	if(!isset($_POST['mwb_wpr_thankyouorder_enable']))
	{
		$_POST['mwb_wpr_thankyouorder_enable'] = 'off';
	}
	if (isset($_POST['mwb_wpr_thankyouorder_enable']) && !empty($_POST['mwb_wpr_thankyouorder_enable'])) {
		update_option('mwb_wpr_thankyouorder_enable',$_POST['mwb_wpr_thankyouorder_enable']);
	}if(!isset($_POST['mwb_wpr_thankyouorder_minimum']))
	{
		$_POST['mwb_wpr_thankyouorder_minimum'] = array();
	}
	elseif (isset($_POST['mwb_wpr_thankyouorder_minimum']) && !empty($_POST['mwb_wpr_thankyouorder_minimum'])) {
		update_option('mwb_wpr_thankyouorder_minimum',$_POST['mwb_wpr_thankyouorder_minimum']);
	}if(!isset($_POST['mwb_wpr_thankyouorder_maximum']))
	{
		$_POST['mwb_wpr_thankyouorder_maximum'] = array();
	}
	elseif (isset($_POST['mwb_wpr_thankyouorder_maximum']) && !empty($_POST['mwb_wpr_thankyouorder_maximum'])) {
		update_option('mwb_wpr_thankyouorder_maximum',$_POST['mwb_wpr_thankyouorder_maximum']);
	}if(!isset($_POST['mwb_wpr_thankyouorder_current_type']))
	{
		$_POST['mwb_wpr_thankyouorder_current_type'] = array();
	}
	elseif (isset($_POST['mwb_wpr_thankyouorder_current_type']) && !empty($_POST['mwb_wpr_thankyouorder_current_type'])) {
		update_option('mwb_wpr_thankyouorder_current_type',$_POST['mwb_wpr_thankyouorder_current_type']);
	}
	?>
	<div class="notice notice-success is-dismissible">
	    <p><strong><?php _e('Settings saved.',MWB_WPR_Domain); ?></strong></p>
	    <button type="button" class="notice-dismiss">
	        <span class="screen-reader-text"><?php _e('Dismiss this notices.',MWB_WPR_Domain); ?></span>
	    </button>
	</div>
	<?php

	
	///===========custmizatin=================
}
////======================custmiation=========================================


	$thankyouorder_min = get_option("mwb_wpr_thankyouorder_minimum", array());
	
   $thankyouorder_max = get_option("mwb_wpr_thankyouorder_maximum", array());

   $thankyouorder_value = get_option("mwb_wpr_thankyouorder_current_type", array());

	$thankyouorder_enable = get_option("mwb_wpr_thankyouorder_enable", false);
////======================custmiation========================================= 
?>
<div class="mwb_table">
	<table class="form-table mwb_wpr_general_setting mwp_wpr_settings">
		<tbody>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="mwb_wpr_thankyouorder_enable"><?php _e('Enable the settings for the orders', MWB_WPR_Domain)?></label>
				</th>
				<td class="forminp forminp-text">
					<?php 
					$attribute_description = __('Check this box to enable gift coupon for those customers who had placed orders in your site', 'woocommerce-ultimate-gift-card');
					echo wc_help_tip( $attribute_description );
					?>
					<label for="mwb_wpr_thankyouorder_enable">
						<input type="checkbox" <?php echo ($thankyouorder_enable == 'on')?"checked='checked'":""?> name="mwb_wpr_thankyouorder_enable" id="mwb_wpr_thankyouorder_enable" class="input-text"> <?php _e('Enable Points on order total.', 'woocommerce-ultimate-gift-card');?>
					</label>						
				</td>
			</tr>
			<tr valign="top" class="mwb_wpr_thankyouorder_row" >
				<th>
					<label for="mwb_wpr_thankyouorder_fields"><?php _e('Enter Points  within Order Range', 'woocommerce-ultimate-gift-card')?></label>
				</th>
				<td class="forminp forminp-text">
					<table class="form-table wp-list-table widefat fixed striped">
						<tbody class="mwb_wpr_thankyouorder_tbody">	
							<tr valign="top">
								<th><?php _e('Minimum', MWB_WPR_Domain); ?></th>
								<th><?php _e('Maximum', MWB_WPR_Domain); ?></th>

								<th><?php _e('Points', MWB_WPR_Domain); ?></th>
								<th class="mwb_wpr_remove_thankyouorder_content"><?php _e('Action', 'woocommerce-ultimate-gift-card'); ?></th>
							</tr>
							<?php 
							if( isset($thankyouorder_min) && $thankyouorder_min !=null && isset($thankyouorder_max) && $thankyouorder_max !=null && isset($thankyouorder_value) && $thankyouorder_value !=null) {
								$mwb_wpr_no = 1;
								if(count($thankyouorder_min) == count($thankyouorder_max) && count($thankyouorder_max) == count($thankyouorder_value) ) {
									foreach ($thankyouorder_min as $key => $value) {
										?>
										<tr valign="top">
											<td class="forminp forminp-text">
												<label for="mwb_wpr_thankyouorder_minimum">
													<input type="text" name="mwb_wpr_thankyouorder_minimum[]" class="mwb_wpr_thankyouorder_minimum input-text wc_input_price" required="" placeholder = "No minimum" value="<?php echo $thankyouorder_min[$key]; ?>">
												</label>
											</td>
											<td class="forminp forminp-text">
												<label for="mwb_wpr_thankyouorder_maximum">
													<input type="text" name="mwb_wpr_thankyouorder_maximum[]" class="mwb_wpr_thankyouorder_maximum"  placeholder = "No maximum" value="<?php echo $thankyouorder_max[$key]; ?>">
												</label>
											</td>
											<td class="forminp forminp-text">
												<label for="mwb_wpr_thankyouorder_current_type">
													<input type="text" name="mwb_wpr_thankyouorder_current_type[]" class="mwb_wpr_thankyouorder_current_type input-text wc_input_price" required=""  value="<?php echo $thankyouorder_value[$key]; ?>">
												</label>
											</td>							
											<td class="mwb_wpr_remove_thankyouorder_content forminp forminp-text">
												<input type="button" value="<?php _e('Remove', MWB_WPR_Domain); ?>" class="mwb_wpr_remove_thankyouorder button" >
											</td>
										</tr>
										<?php
									}
								}
							}
							else {
								?>
								<tr valign="top">
									<td class="forminp forminp-text">
										<label for="mwb_wpr_thankyouorder_minimum">
											<input type="text" name="mwb_wpr_thankyouorder_minimum[]" class="mwb_wpr_thankyouorder_minimum input-text wc_input_price" required="">
										</label>
									</td>
									<td class="forminp forminp-text">
										<label for="mwb_wpr_thankyouorder_maximum">
											<input type="text" name="mwb_wpr_thankyouorder_maximum[]" class="mwb_wpr_thankyouorder_maximum input-text wc_input_price" required="">
										</label>
									</td>
									<td class="forminp forminp-text">
										<label for="mwb_wpr_thankyouorder_current_type">
											<input type="text" name="mwb_wpr_thankyouorder_current_type[]" class="mwb_wpr_thankyouorder_current_type input-text wc_input_price" required="">
										</label>
									</td>							
									<td class="mwb_wpr_remove_thankyouorder_content forminp forminp-text">
										<input type="button" value="<?php _e('Remove', MWB_WPR_Domain); ?>" class="mwb_wpr_remove_thankyouorder button" >
									</td>
								</tr>
								<?php 
							}							
							?>
						</tbody>
					</table>
					<input type="button" value="<?php _e('Add More', MWB_WPR_Domain); ?>" class="mwb_wpr_add_more button" id="mwb_wpr_add_more">
				</td>
			</tr>
		</tbody>
	</table></div>
	<p class="submit">
		<input type="submit" value='<?php _e("Save changes",MWB_WPR_Domain); ?>' class="button-primary woocommerce-save-button mwb_wpr_save_changes" name="mwb_wpr_save_order_totalsettings">
	</p>