<?php 
/**
 * Exit if accessed directly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/*
 * Membership Settings Template
 */
$current_tab = "mwb_wpr_membership_tab";
if(isset($_GET['tab']))
{
	$current_tab = $_GET['tab'];
}
if(isset($_POST['mwb_wpr_save_membership']))
{
	unset($_POST['mwb_wpr_save_membership']);
	if($current_tab=="mwb_wpr_membership_tab")
	{
		$membership_settings_array = array();
		$membership_roles_list = array();
		$mwb_wpr_no_of_section = isset($_POST['hidden_count']) ? $_POST['hidden_count'] : 0;
		$mwb_wpr_mem_enable = isset($_POST['mwb_wpr_membership_setting_enable']) ? 1 : 0;
		$exclude_sale_product = isset($_POST['exclude_sale_product']) ? 1 : 0;
		if(isset( $mwb_wpr_no_of_section ) )
		{
			$count = $mwb_wpr_no_of_section;

			for($count=0; $count<=$mwb_wpr_no_of_section; $count++)
			{
				$mwb_wpr_membersip_roles = isset($_POST['mwb_wpr_membership_level_name_'.$count]) ? $_POST['mwb_wpr_membership_level_name_'.$count] : '';
				$mwb_wpr_membersip_roles = preg_replace("/\s+/","",$mwb_wpr_membersip_roles);
				$mwb_wpr_membersip_points = isset($_POST['mwb_wpr_membership_level_value_'.$count]) ? $_POST['mwb_wpr_membership_level_value_'.$count] : '';
				$mwb_wpr_categ_list= (isset($_POST['mwb_wpr_membership_category_list_'.$count]) && !empty($_POST['mwb_wpr_membership_category_list_'.$count])) ? $_POST['mwb_wpr_membership_category_list_'.$count] : '';
				$mwb_wpr_prod_list= (isset($_POST['mwb_wpr_membership_product_list_'.$count]) && !empty($_POST['mwb_wpr_membership_product_list_'.$count])) ? $_POST['mwb_wpr_membership_product_list_'.$count] : '';
				$mwb_wpr_discount= (isset($_POST['mwb_wpr_membership_discount_'.$count]) && !empty($_POST['mwb_wpr_membership_discount_'.$count])) ? $_POST['mwb_wpr_membership_discount_'.$count] : '';
				$mwb_wpr_expnum = isset($_POST['mwb_wpr_membership_expiration_'.$count]) ? $_POST['mwb_wpr_membership_expiration_'.$count] : '';
				$mwb_wpr_expdays = isset($_POST['mwb_wpr_membership_expiration_days_'.$count]) ? $_POST['mwb_wpr_membership_expiration_days_'.$count] : '';
				$mwb_wpr_price = isset($_POST['mwb_wpr_membership_conversion_price_'.$count]) ? $_POST['mwb_wpr_membership_conversion_price_'.$count] : '';
				$mwb_wpr_points = isset($_POST['mwb_wpr_membership_conversion_points_'.$count]) ? $_POST['mwb_wpr_membership_conversion_points_'.$count] : '';
				$mwb_wpr_images = isset($_POST['mwb_wpr_membership_badge_'.$count]) ? $_POST['mwb_wpr_membership_badge_'.$count] : '';

				if(isset( $mwb_wpr_membersip_roles ) && !empty( $mwb_wpr_membersip_roles ))
				{
					$membership_roles_list[$mwb_wpr_membersip_roles] = array(
						'Points'=>$mwb_wpr_membersip_points,
						'Prod_Categ'=>$mwb_wpr_categ_list,
						'Product'=>$mwb_wpr_prod_list,
						'Discount'=>$mwb_wpr_discount,
						'Exp_Number'=>$mwb_wpr_expnum,
						'Exp_Days'=>$mwb_wpr_expdays,
						'price_con'   =>$mwb_wpr_price,
						'points_con'  =>$mwb_wpr_points,
						'badges'     =>	$mwb_wpr_images
						);	
				}
			}
		}
		$membership_settings_array['mwb_wpr_mem_enable'] = $mwb_wpr_mem_enable;
		$membership_settings_array['membership_roles'] = $membership_roles_list;
		$membership_settings_array['exclude_sale_product'] = $exclude_sale_product;
		if(is_array($membership_settings_array))
			update_option('mwb_wpr_membership_settings',$membership_settings_array);
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
if(isset($_GET['action']) && $_GET['action'] == 'view_membership_log'){
    include_once MWB_WPR_DIRPATH.'/includes/admin/settings/templates/mwb_wpr_membership_log_table.php';
    
}
else{
	if(isset($_POST['set_expiry_to_old_users'])){
	$today_date = date_i18n("Y-m-d h:i:sa");
	$args['meta_query'] = array(
			'relation' => 'AND',                        
            array(
                'key' => 'membership_level',
                'compare' => 'EXISTS' 
            ),
            array(
                'key' => 'membership_expiration',
                'compare' => 'NOT EXISTS' 
            )
        );
	$args['role__in'] =array('subscriber' , 'customer');
    $user_data = new WP_User_Query($args);
    $user_data = $user_data->get_results();
    foreach ($user_data as $key => $value) {
    		$user_id = $value->data->ID;
    		$expiration_date= date('Y-m-d', strtotime($today_date. ' +6 months'));
    		update_user_meta($user_id,'membership_expiration',$expiration_date);
    	}
    	update_option('mwb_wpr_set_expiry_for_old_users','true');
    	?>
    	<div class="notice notice-success is-dismissible">
	    <p><strong><?php _e('Membership Expiration For Old Membership Users has been Updated Successfully. ',MWB_WPR_Domain); ?></strong></p>
	    <button type="button" class="notice-dismiss">
	        <span class="screen-reader-text"><?php _e('Dismiss this notices.',MWB_WPR_Domain); ?></span>
	    </button>
	</div>
<?php    	
}
?>
<?php $membership_settings_array = get_option('mwb_wpr_membership_settings',true); ?>
<?php if(!is_array($membership_settings_array)): $membership_settings_array = array(); endif;
$mwbsetexpiry = get_option("mwb_wpr_set_expiry_for_old_users", false);    
if($mwbsetexpiry == false)
{                
    ?>
    <div id= "mwb_wpr_set_expiry_to_old_users"> <p class="description" style="color: #7F527D;"><?php _e('Click on this button if you want to set 6 months expiry for all users who had  upgraded their user level to any "Membership Level" but do not have any expiration for that level,else they would not be able to use related discounts.',MWB_WPR_Domain);?></p>
    <input type="submit" name="set_expiry_to_old_users" value="<?php _e(' Expiry for Old Users',MWB_WPR_Domain);?>" class="set_expiry_to_old_users mwb_wpr_save_changes button">
   </div>
    <?php
}
?>
	<div class="mwb_wpr_wrap_table">
	<table class="form-table mwb_wpr_membership_setting mwp_wpr_settings">
		<?php 
			$mwb_wpr_mem_enable = isset($membership_settings_array['mwb_wpr_mem_enable']) ? intval($membership_settings_array['mwb_wpr_mem_enable']) : 0;
			$mwb_wpr_membership_roles = isset($membership_settings_array['membership_roles']) && !empty($membership_settings_array['membership_roles']) ? $membership_settings_array['membership_roles'] : array();
			$exclude_sale_product = isset($membership_settings_array['exclude_sale_product']) ? intval($membership_settings_array['exclude_sale_product']) : 0;
		?>	
		<tbody>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="mwb_wpr_membership_setting_enable"><?php _e('Enable Membership', MWB_WPR_Domain)?></label>
				</th>
				<td class="forminp forminp-text">
					<?php 
					$attribute_description = __('Check this box to enable the Membership Feature', MWB_WPR_Domain);
					echo wc_help_tip( $attribute_description );
					?>
					<label for="mwb_wpr_membership_setting_enable">
						<input type="checkbox" <?php checked($mwb_wpr_mem_enable,1);?> name="mwb_wpr_membership_setting_enable" id="mwb_wpr_membership_setting_enable" class="input-text"> <?php _e('Enable Membership',MWB_WPR_Domain);?>
					</label><a href="<?php echo MWB_WPR_HOME_URL.'admin.php?page=mwb-wpr-setting&tab=mwb_wpr_membership_tab&action=view_membership_log';?>" class="mwb_wpr_membership_log"><?php _e('Membership Log',MWB_WPR_Domain) ;?></a>
				</td>
				
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="exclude_sale_product"><?php _e('Exclude Sale Products', MWB_WPR_Domain)?></label>
				</th>
				<td class="forminp forminp-text">
					<?php 
					$attribute_description = __('Check this box to do not apply the membership discount on sale products', MWB_WPR_Domain);
					echo wc_help_tip( $attribute_description );
					?>
					<label for="exclude_sale_product">
						<input type="checkbox" <?php checked($exclude_sale_product,1);?> name="exclude_sale_product" id="exclude_sale_product" class="input-text"> <?php _e('Exclude Sale Products for Membership Discount',MWB_WPR_Domain);?>
					</label>
				</td>
				
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="mwb_wpr_membership_create_section"><?php _e('Create Member', MWB_WPR_Domain)?></label>
				</th>
				<td class="forminp forminp-text">
					<div class="parent_of_div">
					<?php 
					$count = 0;
					if(is_array($mwb_wpr_membership_roles) && !empty($mwb_wpr_membership_roles))
					{

						foreach($mwb_wpr_membership_roles as $role => $values)
						{
							?>
							<div id ="mwb_wpr_parent_repeatable_<?php echo $count; ?>" data-id="<?php echo $count;?>" class="mwb_wpr_repeat">
								<table class="mwb_wpr_repeatable_section">
									<tr valign="top">
										<th scope="row" class="titledesc">
											<label for="mwb_wpr_membership_level_name"><?php _e('Enter Level', MWB_WPR_Domain)?></label>
										</th>
										<td class="forminp forminp-text">
											<?php 
											$attribute_description = __('Entered text will be name of the level for membership', MWB_WPR_Domain);
											echo wc_help_tip( $attribute_description );
											?>
											<label for="mwb_wpr_membership_level_name">
												<input type="text" name="mwb_wpr_membership_level_name_<?php echo $count; ?>" value="<?php echo $role; ?>" id="mwb_wpr_membership_level_name_<?php echo $count; ?>" class="text_points" required><?php _e('Enter the Name of the Level', MWB_WPR_Domain)?>
											</label>
											<input type="button" value='<?php _e("Remove",MWB_WPR_Domain); ?>' class="button-primary woocommerce-save-button mwb_wpr_remove_button" id="<?php echo $count; ?>">				
										</td>
									</tr>
									<tr valign="top">
										<th scope="row" class="titledesc">
											<label for="mwb_wpr_membership_level_value"><?php _e('Enter Points', MWB_WPR_Domain)?></label>
										</th>
										<td class="forminp forminp-text">
											<?php 
											$attribute_description = __('Entered points need to be reached for this level', MWB_WPR_Domain);
											echo wc_help_tip( $attribute_description );
											?>
											<label for="mwb_wpr_membership_level_value">
												<input type="number" min="1" value="<?php echo $values['Points'];?>" name="mwb_wpr_membership_level_value_<?php echo $count; ?>" id="mwb_wpr_membership_level_value_<?php echo $count; ?>" class="input-text" required>
											</label>			
										</td>
									</tr>
									<tr valign="top">
										<th scope="row" class="titledesc">
											<label for="mwb_wpr_membership_expiration"><?php _e('Expiration Period', MWB_WPR_Domain)?></label>
										</th>
										<td class="forminp forminp-text">
											<?php 
											$attribute_description = __('Select the days,week,month or year for expiartion of current level', MWB_WPR_Domain);
											echo wc_help_tip( $attribute_description );
											$exp_num = isset($values['Exp_Number']) ? $values['Exp_Number'] : '';
											?>
												<input type="number" min="1" value="<?php echo $exp_num;?>" name="mwb_wpr_membership_expiration_<?php echo $count; ?>" id="mwb_wpr_membership_expiration_<?php echo $count; ?>" class="input-text">
												<select id="mwb_wpr_membership_expiration_days_<?php echo $count; ?>" name="mwb_wpr_membership_expiration_days_<?php echo $count; ?>">
												<option value="days"<?php if(isset($values['Exp_Days'])){ if($values['Exp_Days'] == "days"){?>selected="selected"<?php }}?>><?php _e('Days',MWB_WPR_Domain);?></option>
												<option value="weeks"<?php if(isset($values['Exp_Days'])){ if($values['Exp_Days'] == "weeks"){?>selected="selected"<?php }}?>><?php _e('Weeks',MWB_WPR_Domain);?></option>
												<option value="months"<?php if(isset($values['Exp_Days'])){ if($values['Exp_Days'] == "months"){?>selected="selected"<?php }}?>><?php _e('Months',MWB_WPR_Domain);?></option>
												<option value="years"<?php if(isset($values['Exp_Days'])){ if($values['Exp_Days'] == "years"){?>selected="selected"<?php }}?>><?php _e('Years',MWB_WPR_Domain);?></option>	
												</select>		
										</td>
									</tr>
									<tr valign="top">
										<th scope="row" class="titledesc">
											<label for="mwb_wpr_membership_category_list"><?php _e('Select Product Category', MWB_WPR_Domain)?></label>
										</th>
										<td class="forminp forminp-text">
											<?php 
											$attribute_description = __('Select', MWB_WPR_Domain);
											echo wc_help_tip( $attribute_description );
											?>
											<select id="mwb_wpr_membership_category_list_<?php echo $count;?>" required="true" multiple="multiple" class="mwb_wpr_common_class_categ" data-id="<?php echo $count;?>" name="mwb_wpr_membership_category_list_<?php echo $count; ?>[]">
												<?php 
												$args = array('taxonomy'=>'product_cat');
												$categories = get_terms($args);
												if(isset($categories) && !empty($categories))
												{
													foreach($categories as $category)
													{
														$catid = $category->term_id;
														$catname = $category->name;
														$catselect = "";
													
														if(is_array($values['Prod_Categ']) && in_array($catid, $values['Prod_Categ']))
														{
															$catselect = "selected='selected'";
														}
													
														?>
														<option value="<?php echo $catid;?>" <?php echo $catselect;?>><?php echo $catname;?></option>
														<?php 
													}
													
												}	
												?>
											</select>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row" class="titledesc">
											<label for="mwb_wpr_membership_product_list"><?php _e('Select Product', MWB_WPR_Domain)?></label>
										</th>
										<td class="forminp forminp-text">
											<?php 
											$attribute_description = __('Product of selected category will get displayed in Select Product Section', MWB_WPR_Domain);
											echo wc_help_tip( $attribute_description );
											?>
											<select id="mwb_wpr_membership_product_list_<?php echo $count;?>" multiple="multiple" name="mwb_wpr_membership_product_list_<?php echo $count; ?>[]">
											<?php 
												$tax_queries = array();
												$tax_query['taxonomy'] = 'product_cat';
									            $tax_query['field'] = 'id';
									            $tax_query['terms'] = $values['Prod_Categ'];
									            $tax_queries[] = $tax_query;
									            $args = array( 'post_type' => 'product', 'posts_per_page' => -1, 'tax_query' => $tax_queries, 'orderby' => 'rand' );
									            $loop = new WP_Query( $args );
									            while ( $loop->have_posts() ) : $loop->the_post();
									            $productselect='';
									            $productid = $loop->post->ID;
									            $productitle = $loop->post->post_title;
									           
													if(is_array($values['Product']) && in_array($productid, $values['Product']))
													{
														
														$productselect = "selected='selected'";
													}	
												
													?>
													<option value="<?php echo $productid;?>" <?php echo $productselect;?>><?php echo $productitle;?></option>
													<?php 
												endwhile;
											
												?>
											</select>
										</td>
									</tr>
									 <tr>
										<th scope="row" class="titledesc">
											<label for="mwb_wpr_coupon_conversion_value_membership">
											<?php _e('Upload Badge',MWB_WPR_Domain);?>
										</th>
										<td>
											<?php 
											$attribute_description = __('Select the Badge which you want to to upload', MWB_WPR_Domain);
											echo wc_help_tip( $attribute_description );
											?>
											<label for="mwb_wpr_coupon_conversion__membership_value">
											<input type="text"  value="<?php if(!empty($values['badges'])):echo $values['badges']; endif; ?>" name="mwb_wpr_membership_badge_<?php echo $count; ?>" id="mwb_wpr_membership_badge_<?php echo $count; ?>" class="input-text wc_input_price mwb_wpr_new_woo_ver_style_text">
											<div>
											 <button class="mwb_wpr_upload_badge button" data-id="<?php echo $count ?>"
											 id="mwb_wpr_upload_badge_<?php echo $count; ?>"><?php _e('Upload',MWB_WPR_Domain); ?></button>
											 <!-- <button class="mwb_wpr_reset_badge button"
											 id="mwb_wpr_upload_badge_<?php echo $count; ?>"><?php _e('Reset',MWB_WPR_Domain); ?></button>
											 </div> -->
											 <img id="mwb_wpr_image_display_<?php echo $count; ?>"src="<?php if(!empty($values['badges'])):echo $values['badges']; endif; ?>" width="100" height="100" src="">
											
										</td>
									</tr> 
									<tr valign="top">
										<th scope="row" class="titledesc">
											<label for="mwb_wpr_membership_coin"><?php _e('Enter Discount (%)', MWB_WPR_Domain)?></label>
										</th>
										<td class="forminp forminp-text">
											<?php 
											$attribute_description = __('Entered Discount will be applied on above selected products', MWB_WPR_Domain);
											echo wc_help_tip( $attribute_description );
											?>
											<label for="mwb_wpr_membership_discount">
												<input type="number" min="1" value="<?php echo $values['Discount'];?>" name="mwb_wpr_membership_discount_<?php echo $count; ?>" id="mwb_wpr_membership_discount_<?php echo $count; ?>" class="input-text" required>
											</label>			
										</td>
										<input type = "hidden" value="<?php echo $count; ?>" name="hidden_count">
									</tr>
									<tr>
										<th scope="row" class="titledesc">
											<label for="mwb_wpr_coupon_conversion_value_membership"><?php _e('Per ', MWB_WPR_Domain)?><?php echo get_woocommerce_currency_symbol(); ?><?php _e(' Points Conversion', MWB_WPR_Domain)?></label>
										</th>
										<td>
											<?php 
											$attribute_description = __('Enter the redeem price for points.(i.e., how much amounts will be equivalent to the points)', MWB_WPR_Domain);
											echo wc_help_tip( $attribute_description );
											?>
											<label for="mwb_wpr_coupon_conversion__membership_value">
											<?php echo get_woocommerce_currency_symbol(); ?>
											<input type="text"  value="<?php echo $values['price_con'];?>" name="mwb_wpr_membership_conversion_price_<?php echo $count; ?>" id="mwb_wpr_membership_conversion_price_<?php echo $count; ?>" class="input-text wc_input_price mwb_wpr_new_woo_ver_style_text">=
											<input type="number" min="1" step="0.1" value="<?php echo $values['points_con'];?>" name="mwb_wpr_membership_conversion_points_<?php echo $count; ?>" id="mwb_wpr_membership_conversion_points_<?php echo $count; ?>" class="input-text wc_input_price mwb_wpr_new_woo_ver_style_text">
											<?php echo __("Points ", MWB_WPR_Domain); ?>
										</td>
									</tr>
								</table>
							</div>
					<?php
						$count++;		
					}
				}
				else
				{
					?>
					<div id ="mwb_wpr_parent_repeatable_<?php echo $count; ?>" data-id="<?php echo $count;?>" class="mwb_wpr_repeat">
								<table class="mwb_wpr_repeatable_section">
									<tr valign="top">
										<th scope="row" class="titledesc">
											<label for="mwb_wpr_membership_level_name"><?php _e('Enter Level', MWB_WPR_Domain)?></label>
										</th>
										<td class="forminp forminp-text">
											<?php 
											$attribute_description = __('Entered text will be name of the level for membership', MWB_WPR_Domain);
											echo wc_help_tip( $attribute_description );
											?>
											<label for="mwb_wpr_membership_level_name">
												<input type="text" name="mwb_wpr_membership_level_name_<?php echo $count; ?>" value="" id="mwb_wpr_membership_level_name_<?php echo $count; ?>" class="text_points" required><?php _e('Enter the Name of the Level', MWB_WPR_Domain)?>
											</label>						
										</td>
									</tr>
									<tr valign="top">
										<th scope="row" class="titledesc">
											<label for="mwb_wpr_membership_level_value"><?php _e('Enter Points', MWB_WPR_Domain)?></label>
										</th>
										<td class="forminp forminp-text">
											<?php 
											$attribute_description = __('Entered points need to be reached for this level', MWB_WPR_Domain);
											echo wc_help_tip( $attribute_description );
											?>
											<label for="mwb_wpr_membership_level_value">
												<input type="number" min="1" value="" name="mwb_wpr_membership_level_value_<?php echo $count; ?>" id="mwb_wpr_membership_level_value_<?php echo $count; ?>" class="input-text" required>
											</label>			
										</td>
									</tr>
									<tr valign="top">
										<th scope="row" class="titledesc">
											<label for="mwb_wpr_membership_expiration"><?php _e('Expiration Period', MWB_WPR_Domain)?></label>
										</th>
										<td class="forminp forminp-text">
											<?php 
											$attribute_description = __('Select the days,week,month or year for expiartion of current level', MWB_WPR_Domain);
											echo wc_help_tip( $attribute_description );
											?>
												<input type="number" min="1" value="" name="mwb_wpr_membership_expiration_<?php echo $count; ?>" id="mwb_wpr_membership_expiration_<?php echo $count; ?>" class="input-text" required>
												<select id="mwb_wpr_membership_expiration_days_<?php echo $count; ?>" name="mwb_wpr_membership_expiration_days_<?php echo $count; ?>">
												<option value="days"><?php _e('Days',MWB_WPR_Domain);?></option>
												<option value="weeks"><?php _e('Weeks',MWB_WPR_Domain);?></option>
												<option value="months"><?php _e('Months',MWB_WPR_Domain);?></option>
												<option value="years"><?php _e('Years',MWB_WPR_Domain);?></option>	
												</select>		
										</td>
									</tr>
									<tr valign="top">
										<th scope="row" class="titledesc">
											<label for="mwb_wpr_membership_category_list"><?php _e('Select Product Category', MWB_WPR_Domain)?></label>
										</th>
										<td class="forminp forminp-text">
											<?php 
											$attribute_description = __('Select', MWB_WPR_Domain);
											echo wc_help_tip( $attribute_description );
											?>
											<select id="mwb_wpr_membership_category_list_<?php echo $count;?>" required="true"  multiple="multiple" class="mwb_wpr_common_class_categ" data-id="<?php echo $count;?>" name="mwb_wpr_membership_category_list_<?php echo $count; ?>[]">
												<?php 
												$args = array('taxonomy'=>'product_cat');
												$categories = get_terms($args);
												if(isset($categories) && !empty($categories))
												{
													foreach($categories as $category)
													{
														$catid = $category->term_id;
														$catname = $category->name;
														$catselect = "";
													
														?>
														<option value="<?php echo $catid;?>" <?php echo $catselect;?>><?php echo $catname;?></option>
														<?php 
													}
													
												}	
												?>
											</select>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row" class="titledesc">
											<label for="mwb_wpr_membership_product_list"><?php _e('Select Product', MWB_WPR_Domain)?></label>
										</th>
										<td class="forminp forminp-text">
											<?php 
											$attribute_description = __('Product of selected category will get displayed in Select Product Section', MWB_WPR_Domain);
											echo wc_help_tip( $attribute_description );
											?>
											<select id="mwb_wpr_membership_product_list_<?php echo $count;?>" multiple="multiple" name="mwb_wpr_membership_product_list_<?php echo $count; ?>[]">
											</select>
										</td>
									</tr>
									<tr>
										<th scope="row" class="titledesc">
											<label for="mwb_wpr_coupon_conversion_value_membership">
											<?php _e('Upload Badge',MWB_WPR_Domain);?>
										</th>
										<td>
											<?php 
											$attribute_description = __('Select the Badge which you want to to upload', MWB_WPR_Domain);
											echo wc_help_tip( $attribute_description );
											?>
											<label for="mwb_wpr_coupon_conversion__membership_value">
											<input type="text"  value="" name="mwb_wpr_membership_badge_<?php echo $count; ?>" id="mwb_wpr_membership_badge_<?php echo $count; ?>" class="input-text wc_input_price mwb_wpr_new_woo_ver_style_text">
											<div>
											 <button class="mwb_wpr_upload_badge button" data-id="<?php echo $count ?>"
											 id="mwb_wpr_upload_badge_<?php echo $count; ?>"><?php _e('Upload',MWB_WPR_Domain); ?></button>
											 <!-- <button class="mwb_wpr_reset_badge button"
											 id="mwb_wpr_upload_badge_<?php echo $count; ?>"><?php _e('Reset',MWB_WPR_Domain); ?></button>
											 </div> -->
											 <img id="mwb_wpr_image_display_<?php echo $count; ?>" width="100" height="100" src="">
											
										</td>
									</tr> 
									<tr valign="top">
										<th scope="row" class="titledesc">
											<label for="mwb_wpr_membership_discount"><?php _e('Enter Discount (%)', MWB_WPR_Domain)?></label>
										</th>
										<td class="forminp forminp-text">
											<?php 
											$attribute_description = __('Entered Discount will be applied on above selected products', MWB_WPR_Domain);
											echo wc_help_tip( $attribute_description );
											?>
											<label for="mwb_wpr_membership_discount">
												<input type="number" min="1" value="" name="mwb_wpr_membership_discount_<?php echo $count; ?>" id="mwb_wpr_membership_discount_<?php echo $count; ?>" class="input-text" required>
											</label>			
										</td>
										<input type = "hidden" value="<?php echo $count; ?>" name="hidden_count">
									</tr>
									<tr>
										<th scope="row" class="titledesc">
											<label for="mwb_wpr_coupon_conversion_value_membership"><?php _e('Per ', MWB_WPR_Domain)?><?php echo get_woocommerce_currency_symbol(); ?><?php _e(' Points Conversion', MWB_WPR_Domain)?></label>
										</th>
										<td>
											<?php 
											$attribute_description = __('Enter the redeem price for points.(i.e., how much amounts will be equivalent to the points)', MWB_WPR_Domain);
											echo wc_help_tip( $attribute_description );
											?>
											<label for="mwb_wpr_coupon_conversion__membership_value">
											<?php echo get_woocommerce_currency_symbol(); ?>
											<input type="text"  value="<?php echo $values['price_con'];?>" name="mwb_wpr_membership_conversion_price_<?php echo $count; ?>" id="mwb_wpr_membership_conversion_price_<?php echo $count; ?>" class="input-text wc_input_price mwb_wpr_new_woo_ver_style_text">=
											<input type="number" min="1" step="0.1" value="<?php echo $values['points_con'];?>" name="mwb_wpr_membership_conversion_points_<?php echo $count; ?>" id="mwb_wpr_membership_conversion_points_<?php echo $count; ?>" class="input-text wc_input_price mwb_wpr_new_woo_ver_style_text">
											<?php echo __("Points ", MWB_WPR_Domain); ?>
										</td>
									</tr>
								</table>
							</div>
				

				<?php }	?>		
					</div>
					<input type="button" value='<?php _e("Add Another",MWB_WPR_Domain); ?>' class="button-primary woocommerce-save-button mwb_wpr_repeat_button">
					<p class= "description"><?php _e('Please do not change the "Level Name" once it will be saved, as it become the key for the Membership User',MWB_WPR_Domain);?></p>
				</td>
			</tr>		
		</tbody>
	</table>
	</div>
<p class="submit">
	<input type="submit" value='<?php _e("Save changes",MWB_WPR_Domain); ?>' class="button-primary woocommerce-save-button mwb_wpr_save_changes" name="mwb_wpr_save_membership">
</p>
<?php
}