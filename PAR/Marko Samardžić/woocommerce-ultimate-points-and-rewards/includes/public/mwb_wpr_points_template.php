<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
Declarations
*/
if(isset($_POST['mwb_wpr_save_level']))
{
	$selected_role = isset($_POST['mwb_wpr_membership_roles']) ? $_POST['mwb_wpr_membership_roles'] : '' ;
	$user_id = get_current_user_id();
	$user=get_user_by('ID',$user_id);
	$user_email=$user->user_email;
	$get_points = (int)get_user_meta($user_id, 'mwb_wpr_points', true);
	$membership_detail = get_user_meta($user_id, 'points_details',true);
	$today_date = date_i18n("Y-m-d h:i:sa");
	$expiration_date = '';
	$membership_settings_array = get_option('mwb_wpr_membership_settings',true);
	$mwb_wpr_membership_roles = isset($membership_settings_array['membership_roles']) && !empty($membership_settings_array['membership_roles']) ? $membership_settings_array['membership_roles'] : array();
	$mwb_wpr_notificatin_array=get_option('mwb_wpr_notificatin_array',true);

	foreach( $mwb_wpr_membership_roles as $roles => $values)
	{	
		if( $selected_role == $roles && ($values['Points'] == $get_points || $values['Points'] < $get_points ) )
		{	
			$remaining_points = $get_points - $values['Points'];
			if(isset($membership_detail['membership']) && !empty($membership_detail['membership']) )
			{
				$membership_arr = array();
			
				$membership_arr= array(
						'membership'=>$values['Points'],
						'date'=>$today_date);
				$membership_detail['membership'][] = $membership_arr;
			}
			else
			{
				$membership_arr = array();
				$membership_arr= array(
						'membership'=>$values['Points'],
						'date'=>$today_date);
				$membership_detail['membership'][]= $membership_arr;
			}
			if(isset($values['Exp_Number']) && !empty($values['Exp_Number']) && isset($values['Exp_Days']) && !empty($values['Exp_Days']))
			{
				$expiration_date= date_i18n('Y-m-d', strtotime($today_date. ' +'.$values['Exp_Number'].' '.$values['Exp_Days']));
			}
			update_user_meta($user_id,'mwb_wpr_points',$remaining_points);
			update_user_meta( $user_id , 'points_details' , $membership_detail);
			update_user_meta($user_id,'membership_level',$selected_role);
			update_user_meta($user_id,'membership_expiration',$expiration_date);
			if(is_array($mwb_wpr_notificatin_array) && !empty($mwb_wpr_notificatin_array))
			{
				$mwb_wpr_notificatin_enable=isset($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) ? intval($mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable']) : 0;
				$mwb_wpr_email_subject=isset($mwb_wpr_notificatin_array['mwb_wpr_membership_email_subject'])? $mwb_wpr_notificatin_array['mwb_wpr_membership_email_subject'] :'';
				$mwb_wpr_email_discription=isset($mwb_wpr_notificatin_array['mwb_wpr_membership_email_discription_custom_id']) ? $mwb_wpr_notificatin_array['mwb_wpr_membership_email_discription_custom_id'] :'';
				$mwb_wpr_email_discription=str_replace("[USERLEVEL]",$selected_role,$mwb_wpr_email_discription);
				if($mwb_wpr_notificatin_enable)
				{	
					$headers = array('Content-Type: text/html; charset=UTF-8');
					wc_mail($user_email,$mwb_wpr_email_subject,$mwb_wpr_email_discription,$headers);
				}
			}
		}
	}
}
$user_id = get_current_user_id();
$get_points = (int)get_user_meta($user_id, 'mwb_wpr_points', true);
$user_level = get_user_meta($user_id,'membership_level',true);
$general_settings = get_option('mwb_wpr_settings_gallery',true);
$membership_settings_array = get_option('mwb_wpr_membership_settings',true);
$coupon_settings = get_option('mwb_wpr_coupons_gallery',true);
$get_points = (int)get_user_meta($user_id, 'mwb_wpr_points', true);
$coupon_redeem_price = (isset($coupon_settings['coupon_redeem_price']) && $coupon_settings['coupon_redeem_price'] != null) ? $coupon_settings['coupon_redeem_price'] : 1;
$coupon_redeem_points = (isset($coupon_settings['coupon_redeem_points']) && $coupon_settings['coupon_redeem_points'] != null) ? intval($coupon_settings['coupon_redeem_points']) : 1;
$enable_mwb_refer = isset($general_settings['enable_mwb_refer']) ? intval($general_settings['enable_mwb_refer']) : 0;
$mwb_refer_value = isset($general_settings['mwb_refer_value']) ? intval($general_settings['mwb_refer_value']) : 1;
$mwb_refer_min = isset($general_settings['mwb_refer_min']) ? intval($general_settings['mwb_refer_min']) : 1;
$mwb_wpr_mem_enable = isset($membership_settings_array['mwb_wpr_mem_enable']) ? intval($membership_settings_array['mwb_wpr_mem_enable']) : 0;
$mwb_text_points_value = isset($general_settings['mwb_text_points_value']) ? $general_settings['mwb_text_points_value'] : __('My Points',MWB_WPR_Domain);
$mwb_per_currency_spent_price= isset($coupon_settings['mwb_wpr_coupon_conversion_price']) ? intval($coupon_settings['mwb_wpr_coupon_conversion_price']) : 1;
$mwb_per_currency_spent_points = isset($coupon_settings['mwb_wpr_coupon_conversion_points']) ? intval($coupon_settings['mwb_wpr_coupon_conversion_points']) : 1;
$mwb_comment_value = isset($general_settings['mwb_comment_value']) ? intval($general_settings['mwb_comment_value']) : 1;
$mwb_refer_value_disable = isset($general_settings['mwb_wpr_general_refer_value_disable']) ? intval($general_settings['mwb_wpr_general_refer_value_disable']) : 0;

$mwb_user_point_expiry = get_user_meta($user_id,'mwb_wpr_points_expiration_date',true);
$get_referral = get_user_meta($user_id, 'mwb_points_referral', true);
$get_referral_invite = get_user_meta($user_id, 'mwb_points_referral_invite', true);
$mwb_ways_to_gain_points_value = isset($general_settings['mwb_ways_to_gain_points_value']) ? $general_settings['mwb_ways_to_gain_points_value'] : '';

if(!is_array($coupon_settings)): $coupon_settings = array(); endif; ?>
<div class="mwb_wpr_points_wrapper_with_exp">
	<div class="mwb_wpr_points_only"><p class="mwb_wpr_heading_para" >
		<span class="mwb_wpr_heading"><?php echo $mwb_text_points_value.':'; ?></span></p>
		<?php
		$get_points = (int)get_user_meta($user_id, 'mwb_wpr_points', true);
		$get_point = get_user_meta($user_id, 'points_details', true); 
		?>
		<span class="mwb_wpr_heading" id="mwb_wpr_points_only">
			<?php echo (isset($get_points) && $get_points != null)? $get_points:0;?>
		</span>
	</div>
	<?php if(isset($mwb_user_point_expiry) && !empty($mwb_user_point_expiry) && $get_points > 0){
		$mwb_wpr_points_exp_onmyaccount = get_option('mwb_wpr_points_exp_onmyaccount','off');
			if($mwb_wpr_points_exp_onmyaccount == 'on'){
				$date_format = get_option( 'date_format' );
				$expiry_date_timestamp = strtotime($mwb_user_point_expiry);
				$expirydate_format = date_i18n( $date_format, $expiry_date_timestamp );
				echo '<p class=mwb_wpr_points_expiry> '.__('Get Expired: ',MWB_WPR_Domain).$expirydate_format.'</p>';
			}
		}
		?>
</div>		
	<span class="mwb_wpr_view_log">
		<a href="<?php echo get_permalink().'view-log/'.$user_id ?>"><?php _e("View Point Log",MWB_WPR_Domain);?></a>
	</span>
<?php
if(isset($mwb_ways_to_gain_points_value) && !empty($mwb_ways_to_gain_points_value)){ ?>
	<div class ="mwb_ways_to_gain_points_section">
	<p class="mwb_wpr_heading"><?php echo __( 'Ways to gain more points:', MWB_WPR_Domain ); ?></p>
			<?php 
				$mwb_ways_to_gain_points_value=str_replace("[Comment Points]",$mwb_comment_value,$mwb_ways_to_gain_points_value);
				$mwb_ways_to_gain_points_value=str_replace("[Refer Points]",$mwb_refer_value,$mwb_ways_to_gain_points_value);
				$mwb_ways_to_gain_points_value=str_replace("[Per Currency Spent Points]",$mwb_per_currency_spent_points,$mwb_ways_to_gain_points_value);
				$mwb_ways_to_gain_points_value=str_replace("[Per Currency Spent Price]",$mwb_per_currency_spent_price,$mwb_ways_to_gain_points_value);
		
			 	echo '<fieldset class="mwb_wpr_each_section">'.$mwb_ways_to_gain_points_value.'</fieldset>'; 
			 ?>
		
	</div>
<?php 
}
if($mwb_wpr_mem_enable)
{
	$enable_drop = false;
	$mwb_wpr_membership_roles = isset($membership_settings_array['membership_roles']) && !empty($membership_settings_array['membership_roles']) ? $membership_settings_array['membership_roles'] : array();
?>	
	<p class="mwb_wpr_heading"><?php _e('Membership List', MWB_WPR_Domain); ?></p>
		<?php if(isset($user_level) && !empty($user_level)) 
		{
			?>
			<span class="mwb_wpr_upgrade_level"><?php _e('Your level has been upgraded to ', MWB_WPR_Domain); echo $user_level; ?></span>
		<?php 
		}
		?>	
			<table class="woocommerce-MyAccount-points shop_table my_account_points account-points-table mwb_wpr_membership_with_img">
				<thead>
					<tr>
						<th class="points-points">
							<span class="nobr"><?php echo __( 'Level', MWB_WPR_Domain ); ?></span>
						</th>
						<th class="points-code">
							<span class="nobr"><?php echo __( 'Required Points', MWB_WPR_Domain ); ?></span>
						</th>
					</tr>
				</thead>
				<tbody>
				<?php
				if(is_array($mwb_wpr_membership_roles) && !empty($mwb_wpr_membership_roles)){
				foreach($mwb_wpr_membership_roles as $role => $values)
				{?>
				<tr>
					<td><?php echo $role.'<br/><a class = "mwb_wpr_level_benefits" data-id = "'.$role.'" href="javascript:;">'.__( 'View Benefits',MWB_WPR_Domain ).'</a>'; ?></td>
						<div class="mwb_wpr_popup_wrapper" style="display: none;" id="mwb_wpr_popup_wrapper_<?php echo $role;?>">
							<div class="mwb_wpr_popup_content_section">
								<div class="mwb_wpr_popup_content">
									<div class="mwb_wpr_popup_notice_section">					
										<p>
											<span class="mwb_wpr_intro_text"><?php _e('You will get ',MWB_WPR_Domain); echo $values['Discount'];_e('% discount on below products or categories',MWB_WPR_Domain);?>
											</span>
											<span class="mwb_wpr_close">
												<a href="javascript:;"><img src="<?php echo MWB_WPR_URL;?>/assets/images/cancel.png" alt=""></a>
											</span>
										</p>
									</div>
									<div class="mwb_wpr_popup_thumbnail_section">
										<ul>
										<?php
											if(is_array($values['Product']) && !empty($values['Product'])){
												foreach($values['Product'] as $key => $pro_id){
													$pro_img = wp_get_attachment_image_src( get_post_thumbnail_id($pro_id), 'single-post-thumbnail' );
													$_product = wc_get_product( $pro_id );
													$price = $_product->get_price();
													$pro_url = get_permalink( $pro_id );
													if(empty($pro_img[0])){
															$pro_img[0] = MWB_WPR_URL.'/assets/images/placeholder.png';
														}
											?>
												<li>
													<a href="<?php echo $pro_url;?>">
														<span class="mwb_wpr_thumbnail_img_wrap"><img src="<?php echo $pro_img[0];?>" alt=""></span>
														<span class="mwb_wpr_thumbnail_price_wrap"><?php echo wc_price($price);?></span>
													</a>
												</li>		
											<?php		
												} ?>
											</ul>
											<?php 	
											}
											else
											{
												if(is_array($values['Prod_Categ']) && !empty($values['Prod_Categ'])){?>
												<div class="mwb_wpr_popup_cat">

													<?php foreach($values['Prod_Categ'] as $key => $cat_id){
														$thumbnail_id = get_woocommerce_term_meta( $cat_id, 'thumbnail_id', true );
														$cat_img = wp_get_attachment_url( $thumbnail_id );
														$category_title = get_term( $cat_id, 'product_cat' );
														$category_link = get_category_link( $cat_id );
														if(empty($cat_img)){
															$cat_img = MWB_WPR_URL.'/assets/images/placeholder.png';
														}
														?>
															<div class="mwb_wpr_cat_wrapper">
																<img src="<?php echo $cat_img;?>" alt="" style="height: 100px;width: 100px;">
																<a href="<?php echo $category_link;?>" class="mwb_wpr_cat_list"><?php echo $category_title->name;?></a>
															</div>
													<?php
													}?>
												</div>
												<?php
												}
											}
										?>						
									</div>								
								</div>
							</div>
						</div>
					
					<td><?php echo $values['Points']; 
					if($role == $user_level){
						echo '<img class="mwb_wpr_tick" src = "'.MWB_WPR_URL.'assets/images/tick.png">';
					}
					?></td>
				</tr>	
				<?php

				if($values['Points'] == $get_points || $values['Points'] < $get_points )
					$enable_drop = true;
				}
			}
				?>
				</tbody>
			</table>
<?php
}
	if(isset($enable_drop) && $enable_drop)
	{
	?>	<p class="mwb_wpr_heading"><?php echo __( 'Upgrade User Level', MWB_WPR_Domain ); ?></p>
		<fieldset class="mwb_wpr_each_section">	
		<span class="mwb_wpr_membership_message"><?php echo __('Upgrade Your User Level: ',MWB_WPR_Domain);?></span>
		<form action="" method="post" id="mwb_wpr_membership">
		<select id="mwb_wpr_membership_roles" class="mwb_wpr_membership_roles" name="mwb_wpr_membership_roles">
		<option><?php echo __('Select Roles',MWB_WPR_Domain);?></option>
		<?php
		if(isset($user_level) && !empty($user_level) && array_key_exists($user_level, $mwb_wpr_membership_roles))
				{	
					unset($mwb_wpr_membership_roles[$user_level]);
				}
			foreach($mwb_wpr_membership_roles as $role => $values)
			{	
				if($values['Points'] == $get_points || $values['Points'] < $get_points )
				{

			?>	
				<option value="<?php echo $role; ?>"><?php echo $role; ?></option>
		<?php
				}
			}
		?>
		</select>
		<input type="submit" id = "mwb_wpr_upgrade_level" value='<?php _e("Upgrade Level",MWB_WPR_Domain); ?>' class="button-primary woocommerce-save-button mwb_wpr_save_changes" name="mwb_wpr_save_level">
		</form></fieldset>	
<?php		
	}
$mwb_wpr_disable_coupon_generation = get_option("mwb_wpr_disable_coupon_generation",0);
if($mwb_wpr_disable_coupon_generation == 0){
?>
<p class="mwb_wpr_heading"><?php echo __( 'Points Conversion', MWB_WPR_Domain ); ?></p>
<fieldset class="mwb_wpr_each_section">
<p>
	<?php echo __( 'Points Conversion: ', MWB_WPR_Domain ); ?>
	<?php echo $coupon_redeem_points.__(' points = ',MWB_WPR_Domain).get_woocommerce_currency_symbol().$coupon_redeem_price; ?>
</p>
<form id="points_form" enctype="multipart/form-data" action="" method="post">
<?php 
				
	$mwb_minimum_points_value = isset($coupon_settings['mwb_minimum_points_value']) ? intval($coupon_settings['mwb_minimum_points_value']) : 50;
	if(is_numeric($mwb_minimum_points_value))
	{
		if($mwb_minimum_points_value <= $get_points)
		{
			
			$enable_custom_convert_point = isset($coupon_settings['enable_custom_convert_point']) ? intval($coupon_settings['enable_custom_convert_point']) : 0;
			
			if($enable_custom_convert_point )
			{
				?>
					<p class="woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide">
						<label for="mwb_custom_text">
							<?php _e('Enter your points:',MWB_WPR_Domain); ?>
						</label>
						<p id="mwb_wpr_points_notification"></p>
						<input type="number" class="woocommerce-Input woocommerce-Input--number input-number" name="mwb_custom_number" min="1" id="mwb_custom_point_num" style="width: 160px;">
						
						<input type="button" name="mwb_wpr_custom_coupon" class="mwb_wpr_custom_coupon button" value="<?php _e('Generate Coupon',MWB_WPR_Domain);?>" data-id="<?php echo $user_id; ?>">
					</p>
				<?php
			}
			else
			{
				_e('Convert Points To Coupon',MWB_WPR_Domain);
				?>
					<p id="mwb_wpr_points_notification"></p>
					<input type="button" name="mwb_wpr_generate_coupon" class="mwb_wpr_generate_coupon button" value="<?php _e('Generate Coupon',MWB_WPR_Domain);?>" data-id="<?php echo $user_id; ?>">
				<?php
			}
		}
		else
		{	
			_e('Minimum points required to convert points to coupon is ', MWB_WPR_Domain);echo $mwb_minimum_points_value;
		}
	}

	$user_log = get_user_meta( $user_id, 'mwb_wpr_user_log', true);

?>
</form>
</fieldset><?php } ?>
<br>
	<?php
	if(isset($user_log) && is_array($user_log) && !empty($user_log))
	{
	?>	
	<p class="mwb_wpr_heading"><?php echo __( 'Coupon Details', MWB_WPR_Domain ); ?></p>
	<div class="points_log">
		<table class="woocommerce-MyAccount-points shop_table my_account_points account-points-table mwb_wpr_coupon_details">
			<thead>
				<tr>
					<th class="points-points">
						<span class="nobr"><?php echo __( 'Points', MWB_WPR_Domain ); ?></span>
					</th>
					<th class="points-code">
						<span class="nobr"><?php echo __( 'Coupon Code', MWB_WPR_Domain ); ?></span>
					</th>
					<th class="points-amount">
						<span class="nobr"><?php echo __( 'Coupon Amount', MWB_WPR_Domain ); ?></span>
					</th>
					<th class="points-left">
						<span class="nobr"><?php echo __( 'Amount Left', MWB_WPR_Domain ); ?></span>
					</th>
					<th class="points-expiry">
						<span class="nobr"><?php echo __( 'Expiry', MWB_WPR_Domain ); ?></span>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $user_log as $key => $mwb_user_log ) : ?>

					<tr class="points">
						<?php foreach ( $mwb_user_log as $column_id => $column_name ) : ?>
							<td class="points-<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_id ); ?>" >
								<?php

									if($column_id == 'left'){
										$mwb_split = explode("#",$key);
										$column_name = get_post_meta( $mwb_split[1], 'coupon_amount',true);
										echo get_woocommerce_currency_symbol().$column_name;
									}
									elseif($column_id == 'expiry'){
										$column_name = get_post_meta( $mwb_split[1], 'expiry_date',true );
										echo $column_name;
									}
									else
									{
										echo $column_name;
									}
								 ?>
							</td>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		</div>			
	<?php
	}else{ ?>
		<div class="points_log" style="display: none"></div>
	<?php
	}	
if($enable_mwb_refer)
{
	if(empty($get_referral) && empty($get_referral_invite))
	{
	
		$referral_key = mwb_wpr_create_referral_code();
		$referral_invite = 0;
		
		update_user_meta($user_id, 'mwb_points_referral', $referral_key);
		update_user_meta($user_id, 'mwb_points_referral_invite', $referral_invite);
	}
	elseif(isset($get_referral) && $get_referral !=null && isset($get_referral_invite) && $get_referral_invite !=null && $get_referral_invite >= $mwb_refer_min )
	{	
		//check whether the Links are needs to be permanent or not?
		$mwb_wpr_referral_link_permanent = get_option("mwb_wpr_referral_link_permanent",0);
		if($mwb_wpr_referral_link_permanent == 0){
			$referral_key = mwb_wpr_create_referral_code();
			update_user_meta($user_id, 'mwb_points_referral', $referral_key);
		}
		//update the invites as soon as user got the referral rewards
		$referral_invite = 0;
		update_user_meta($user_id, 'mwb_points_referral_invite', $referral_invite);
	}
	$get_referral = get_user_meta($user_id, 'mwb_points_referral', true);
	$get_referral_invite = get_user_meta($user_id, 'mwb_points_referral_invite', true);
	$site_url = site_url();
	?>
<div class="mwb_account_wrapper">
	<p class="mwb_wpr_heading"><?php echo __( 'Referral Link', MWB_WPR_Domain ); ?></p>
	<fieldset class="mwb_wpr_each_section">

		<div class="mwb_wpr_refrral_code_copy">
			<p id="mwb_wpr_copy"><code><?php echo $site_url.'?pkey='.$get_referral; ?></code></p>
			<button class="mwb_wpr_btn_copy mwb_tooltip" data-clipboard-target="#mwb_wpr_copy" aria-label="copied">
			<span class="mwb_tooltiptext"><?php _e('Copy',MWB_WPR_Domain) ;?></span>
			<img src="<?php echo MWB_WPR_URL.'assets/images/copy.png';?>" alt="Copy to clipboard"></button>
		</div>
	<?php if(!$mwb_refer_value_disable){ ?>
		<p class="mwb_wpr_message">
		<?php
			_e( 'Minimum ',MWB_WPR_Domain);echo $mwb_refer_min; _e(' invites are required to get a reward of ',MWB_WPR_Domain);echo $mwb_refer_value; _e(' points',MWB_WPR_Domain); ?>
		</p>
		<p> 
			<?php if($mwb_refer_min > 1){
				echo __( 'Current Invites: ', MWB_WPR_Domain ).$get_referral_invite;
			} ?>
		</p>
		<?php
	}
	else
	{ ?>
		<p><?php echo __( 'Invite Users to get some reward points on their purchasing', MWB_WPR_Domain );?>

	<?php
	}
	$general_settings = get_option('mwb_wpr_settings_gallery',true);
	$enable_mwb_social = isset($general_settings['enable_mwb_social']) ? intval($general_settings['enable_mwb_social']) : 0;
	$mwb_social_selection = isset($general_settings['mwb_social_selection']) ? $general_settings['mwb_social_selection'] : array();
	$page_permalink = wc_get_page_permalink('myaccount');
		if($enable_mwb_social){
			$user_reference_key =  get_user_meta($user_id, 'mwb_points_referral', true);
			$content = '';
			$content = $content.'<div class="mwb_wpr_wrapper_button">';
	        $share_button = '<div class="mwb_wpr_btn mwb_wpr_common_class"><a class="twitter-share-button" href="https://twitter.com/intent/tweet?text='.$page_permalink.'?pkey='.$user_reference_key.'" target="_blank"><img src ="'.MWB_WPR_URL.'/assets/images/twitter.png">'.__("Tweet",MWB_WPR_Domain).'</a></div>';

	        $fb_button = '<div id="fb-root"></div>
							<script>(function(d, s, id) {
  							var js, fjs = d.getElementsByTagName(s)[0];
  							if (d.getElementById(id)) return;
  							js = d.createElement(s); js.id = id;
  							js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.9";
  							fjs.parentNode.insertBefore(js, fjs);
						}(document, "script", "facebook-jssdk"));</script>
						<div class="fb-share-button mwb_wpr_common_class" data-href="'.$page_permalink.'?pkey='.$user_reference_key.'" data-layout="button_count" data-size="small" data-mobile-iframe="true"><a class="fb-xfbml-parse-ignore" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fdevelopers.facebook.com%2Fdocs%2Fplugins%2F&amp;src=sdkpreparse">'.__("Share",MWB_WPR_Domain).'</a></div>';
			$mail = '<a class="mwb_wpr_mail_button mwb_wpr_common_class" href="mailto:enteryour@addresshere.com?subject=Click on this link &body=Check%20this%20out:%20'.$page_permalink.'?pkey='.$user_reference_key.'" rel="nofollow"><img src ="'.MWB_WPR_URL.'assets/images/email.png"></a>';

	        $google = '<div class="google mwb_wpr_common_class"><script src="https://apis.google.com/js/platform.js" async defer></script><div class="g-plus google-plus-button" data-action="share" data-height="24" data-href="'.$page_permalink.'?pkey='.$user_reference_key.'"></div></div>';

	        if( isset($mwb_social_selection['Facebook']) && $mwb_social_selection['Facebook'] == 1){
	        	
	        	$content =  $content.$fb_button;
	        }
	        if( isset($mwb_social_selection['Twitter']) && $mwb_social_selection['Twitter'] == 1){
	        	
	        	$content =  $content.$share_button;
	        }
	        if( isset($mwb_social_selection['Google']) && $mwb_social_selection['Google'] == 1){
	        	
	        	$content =  $content.$google;
	        }
	        if( isset($mwb_social_selection['Email']) && $mwb_social_selection['Email'] == 1){
	        	$content =  $content.$mail;
	        }
	        $content = $content.'</div>';
	        echo $content;
		}
	}
	?>
	</fieldset>
<?php 
$mwb_wpr_user_can_send_point = get_option("mwb_wpr_user_can_send_point",0);
if($mwb_wpr_user_can_send_point){
?>
	<p class="mwb_wpr_heading"><?php echo __( 'Share Points', MWB_WPR_Domain ); ?></p>
	<fieldset class="mwb_wpr_each_section">
		<p id="mwb_wpr_shared_points_notification"></p>
		<input type="email" style="width: 45%;" id="mwb_wpr_enter_email" placeholder="<?php _e('Enter Email',MWB_WPR_Domain);?>">
		<input type="number" id="mwb_wpr_enter_point" placeholder="<?php _e('Points',MWB_WPR_Domain);?>" style="width: 20%;">
		<input id="mwb_wpr_share_point" data-id="<?php echo $user_id; ?>"type="button" name="mwb_wpr_share_point" value="<?php _e('GO',MWB_WPR_Domain);?>">
	</fieldset>	
	<div id="mwb_wpr_loader" style="display: none;">
		<img src="<?php echo MWB_WPR_URL;?>/assets/images/loading.gif">
	</div>
<?php
}
?>	
</div>