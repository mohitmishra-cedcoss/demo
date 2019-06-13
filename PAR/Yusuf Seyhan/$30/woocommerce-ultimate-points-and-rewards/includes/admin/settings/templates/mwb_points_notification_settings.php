<?php 
/**
 * Exit if accessed directly
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/*
 * Points notitfication Settings Template
 */
$current_tab = "mwb_wpr_notificatin_tab";
if(isset($_GET['tab']))
{
	$current_tab = $_GET['tab'];
}
if(isset($_POST['mwb_wpr_save_notification']))
{
	
	if($current_tab=="mwb_wpr_notificatin_tab")
	{
		$mwb_wpr_notificatin_array = array();
		$mwb_wpr_notificatin_enable = isset($_POST['mwb_wpr_notification_setting_enable']) ? 1 : 0;
		$mwb_wpr_email_subject = (isset($_POST['mwb_wpr_email_subject']) && $_POST['mwb_wpr_email_subject'] !=null) ? stripcslashes($_POST['mwb_wpr_email_subject']) : __("Custom Points Notification",MWB_WPR_Domain);
		$mwb_wpr_signup_email_subject = (isset($_POST['mwb_wpr_signup_email_subject']) && $_POST['mwb_wpr_signup_email_subject'] !=null) ? stripcslashes($_POST['mwb_wpr_signup_email_subject']) : __("Signup Points Notification",MWB_WPR_Domain);
		$mwb_wpr_product_email_subject = (isset($_POST['mwb_wpr_product_email_subject']) && $_POST['mwb_wpr_product_email_subject'] !=null) ? stripcslashes($_POST['mwb_wpr_product_email_subject']) : __("product Purchase Points Notification",MWB_WPR_Domain);
		$mwb_wpr_comment_email_subject = (isset($_POST['mwb_wpr_comment_email_subject']) && $_POST['mwb_wpr_comment_email_subject'] !=null) ? stripcslashes($_POST['mwb_wpr_comment_email_subject']) : __("Comment Points Notification",MWB_WPR_Domain);
		$mwb_wpr_amount_email_subject = (isset($_POST['mwb_wpr_amount_email_subject']) && $_POST['mwb_wpr_amount_email_subject'] !=null) ? stripcslashes($_POST['mwb_wpr_amount_email_subject']) : __("Order Amount Points Notification",MWB_WPR_Domain);
		$mwb_wpr_referral_email_subject = (isset($_POST['mwb_wpr_referral_email_subject']) && $_POST['mwb_wpr_referral_email_subject'] !=null) ? stripcslashes($_POST['mwb_wpr_referral_email_subject']) : __("Referral Points Notification",MWB_WPR_Domain);

		$mwb_wpr_referral_purchase_email_subject = (isset($_POST['mwb_wpr_referral_purchase_email_subject']) && $_POST['mwb_wpr_referral_purchase_email_subject'] !=null) ? stripcslashes($_POST['mwb_wpr_referral_purchase_email_subject']) : __("Referral Purchase Points Notification",MWB_WPR_Domain);
		$mwb_wpr_membership_email_subject = (isset($_POST['mwb_wpr_membership_email_subject']) && $_POST['mwb_wpr_membership_email_subject'] !=null) ? stripcslashes($_POST['mwb_wpr_membership_email_subject']) : __("Upgrade Membership Level Notification",MWB_WPR_Domain);
		$mwb_wpr_pro_pur_by_points_email_subject = (isset($_POST['mwb_wpr_pro_pur_by_points_email_subject']) && $_POST['mwb_wpr_pro_pur_by_points_email_subject'] !=null) ? stripcslashes($_POST['mwb_wpr_pro_pur_by_points_email_subject']) : __("Product Purchased Through Points Notification",MWB_WPR_Domain);
		$mwb_wpr_email_discription_custom_id = (isset($_POST['mwb_wpr_email_discription_custom_id']) && $_POST['mwb_wpr_email_discription_custom_id'] !=null) ? stripcslashes($_POST['mwb_wpr_email_discription_custom_id']) : __('Your points is updated and your total points is ',MWB_WPR_Domain).'[Total Points].';
		$mwb_wpr_signup_email_discription_custom_id = (isset($_POST['mwb_wpr_signup_email_discription_custom_id']) && $_POST['mwb_wpr_signup_email_discription_custom_id'] !=null) ? stripcslashes($_POST['mwb_wpr_signup_email_discription_custom_id']) : __("You have received ",MWB_WPR_Domain).'[Points]'.__(' points and your total points is ',MWB_WPR_Domain).'[Total Points].';
		$mwb_wpr_product_email_discription_custom_id = (isset($_POST['mwb_wpr_product_email_discription_custom_id']) && $_POST['mwb_wpr_product_email_discription_custom_id'] !=null) ? stripcslashes($_POST['mwb_wpr_product_email_discription_custom_id']) : __("You have received ",MWB_WPR_Domain).'[Points]'.__(' points and your total points is ',MWB_WPR_Domain).'[Total Points].';
		$mwb_wpr_comment_email_discription_custom_id = (isset($_POST['mwb_wpr_comment_email_discription_custom_id']) && $_POST['mwb_wpr_comment_email_discription_custom_id'] !=null) ? stripcslashes($_POST['mwb_wpr_comment_email_discription_custom_id']) : __("You have received ",MWB_WPR_Domain).'[Points]'.__(' points and your total points is ',MWB_WPR_Domain).'[Total Points].';
		$mwb_wpr_amount_email_discription_custom_id = (isset($_POST['mwb_wpr_amount_email_discription_custom_id']) && $_POST['mwb_wpr_amount_email_discription_custom_id'] !=null) ? stripcslashes($_POST['mwb_wpr_amount_email_discription_custom_id']) : __("You have received ",MWB_WPR_Domain).'[Points]'.__(' points and your total points is ',MWB_WPR_Domain).'[Total Points].';
		$mwb_wpr_referral_email_discription_custom_id = (isset($_POST['mwb_wpr_referral_email_discription_custom_id']) && $_POST['mwb_wpr_referral_email_discription_custom_id'] !=null) ? stripcslashes($_POST['mwb_wpr_referral_email_discription_custom_id']) : __("You have received ",MWB_WPR_Domain).'[Points]'.__(' points and your total points is ',MWB_WPR_Domain).'[Total Points].';
		$mwb_wpr_referral_purchase_email_discription_custom_id = (isset($_POST['mwb_wpr_referral_purchase_email_discription_custom_id']) && $_POST['mwb_wpr_referral_purchase_email_discription_custom_id'] !=null) ? stripcslashes($_POST['mwb_wpr_referral_purchase_email_discription_custom_id']) : __("You have received ",MWB_WPR_Domain).'[Points]'.__(' points and your total points is ',MWB_WPR_Domain).'[Total Points].';

		$mwb_wpr_membership_email_discription_custom_id = (isset($_POST['mwb_wpr_membership_email_discription_custom_id']) && $_POST['mwb_wpr_membership_email_discription_custom_id'] !=null) ? stripcslashes($_POST['mwb_wpr_membership_email_discription_custom_id']) : __("Your User Level has been Upgraded to ",MWB_WPR_Domain).'[USERLEVEL]'.__('. And Now You will get some offers on some products.',MWB_WPR_Domain);
		$mwb_wpr_pro_pur_by_points_discription_custom_id = (isset($_POST['mwb_wpr_pro_pur_by_points_discription_custom_id']) && $_POST['mwb_wpr_pro_pur_by_points_discription_custom_id'] !=null) ? stripcslashes($_POST['mwb_wpr_pro_pur_by_points_discription_custom_id']) : __("Product Purchased Point ",MWB_WPR_Domain).'[PROPURPOINTS]'.__(' has been deducted from your points on purchasing, and your Total Point is ',MWB_WPR_Domain).'[TOTALPOINTS].';
		$mwb_wpr_deduct_assigned_point_desciption = (isset($_POST['mwb_wpr_deduct_assigned_point_desciption']) && $_POST['mwb_wpr_deduct_assigned_point_desciption'] !=null) ? stripcslashes($_POST['mwb_wpr_deduct_assigned_point_desciption']) : __('Your ',MWB_WPR_Domain).'[DEDUCTEDPOINT]'.__(' has been deducted from your total points as you have requested for your refund, and your Total Point is ',MWB_WPR_Domain).'[TOTALPOINTS].';
		$mwb_wpr_deduct_assigned_point_subject = (isset($_POST['mwb_wpr_deduct_assigned_point_subject']) && $_POST['mwb_wpr_deduct_assigned_point_subject'] !=null) ? stripcslashes($_POST['mwb_wpr_deduct_assigned_point_subject']) : __("Your Points has been Deducted",MWB_WPR_Domain);
		$mwb_wpr_deduct_per_currency_point_description = (isset($_POST['mwb_wpr_deduct_per_currency_point_description']) && $_POST['mwb_wpr_deduct_per_currency_point_description'] !=null) ? stripcslashes($_POST['mwb_wpr_deduct_per_currency_point_description']) : __('Your ',MWB_WPR_Domain).'[DEDUCTEDPOINT]'.__(' has been deducted from your total points as you have requested for your refund, and your Total Point is ',MWB_WPR_Domain).'[TOTALPOINTS].';
		$mwb_wpr_deduct_per_currency_point_subject = (isset($_POST['mwb_wpr_deduct_per_currency_point_subject']) && $_POST['mwb_wpr_deduct_per_currency_point_subject'] !=null) ? stripcslashes($_POST['mwb_wpr_deduct_per_currency_point_subject']) : __("Your Points has been Deducted",MWB_WPR_Domain);
		$mwb_wpr_return_pro_pur_description = (isset($_POST['mwb_wpr_return_pro_pur_description']) && $_POST['mwb_wpr_return_pro_pur_description'] !=null) ? stripcslashes($_POST['mwb_wpr_return_pro_pur_description']) : __('Your ',MWB_WPR_Domain).'[RETURNPOINT]'.__(' has been returned to your point account as you have requested for your refund, and your Total Point is ',MWB_WPR_Domain).'[TOTALPOINTS].';
		$mwb_wpr_return_pro_pur_subject = (isset($_POST['mwb_wpr_return_pro_pur_subject']) && $_POST['mwb_wpr_return_pro_pur_subject'] !=null) ? stripcslashes($_POST['mwb_wpr_return_pro_pur_subject']) : __("Your Points has been Returned",MWB_WPR_Domain);	
		$mwb_wpr_point_sharing_description = (isset($_POST['mwb_wpr_point_sharing_description']) && $_POST['mwb_wpr_point_sharing_description'] !=null) ? stripcslashes($_POST['mwb_wpr_point_sharing_description']) :  __('You have received ',MWB_WPR_Domain).'[RECEIVEDPOINT]'.__(' by your one of the friend having Email Id is ',MWB_WPR_Domain).'[SENDEREMAIL]'.__(' and your Total Point is ',MWB_WPR_Domain).'[TOTALPOINTS].';
		$mwb_wpr_point_sharing_subject = (isset($_POST['mwb_wpr_point_sharing_subject']) && $_POST['mwb_wpr_point_sharing_subject'] !=null) ? stripcslashes($_POST['mwb_wpr_point_sharing_subject']) : __("Received Points Successfully!!",MWB_WPR_Domain);
		$mwb_wpr_point_on_cart_desc = (isset($_POST['mwb_wpr_point_on_cart_desc']) && $_POST['mwb_wpr_point_on_cart_desc'] !=null) ? stripcslashes($_POST['mwb_wpr_point_on_cart_desc']) :  __('Your ',MWB_WPR_Domain).'[DEDUCTCARTPOINT]'.__(' Points has been deducted from your account, now your Total Point is ',MWB_WPR_Domain).'[TOTALPOINTS].';
		$mwb_wpr_point_on_cart_subject = (isset($_POST['mwb_wpr_point_sharing_subject']) && $_POST['mwb_wpr_point_on_cart_subject'] !=null) ? stripcslashes($_POST['mwb_wpr_point_on_cart_subject']) : __("Points Deducted!!",MWB_WPR_Domain);		
		$mwb_wpr_notificatin_array['mwb_wpr_notificatin_enable'] = $mwb_wpr_notificatin_enable;
		$mwb_wpr_notificatin_array['mwb_wpr_email_subject'] = $mwb_wpr_email_subject;
		$mwb_wpr_notificatin_array['mwb_wpr_signup_email_subject'] = $mwb_wpr_signup_email_subject;
		$mwb_wpr_notificatin_array['mwb_wpr_product_email_subject'] = $mwb_wpr_product_email_subject;
		$mwb_wpr_notificatin_array['mwb_wpr_comment_email_subject'] = $mwb_wpr_comment_email_subject;
		$mwb_wpr_notificatin_array['mwb_wpr_amount_email_subject'] = $mwb_wpr_amount_email_subject;
		$mwb_wpr_notificatin_array['mwb_wpr_referral_email_subject'] = $mwb_wpr_referral_email_subject;
		$mwb_wpr_notificatin_array['mwb_wpr_referral_purchase_email_subject'] = $mwb_wpr_referral_purchase_email_subject;
		$mwb_wpr_notificatin_array['mwb_wpr_membership_email_subject'] = $mwb_wpr_membership_email_subject;
		$mwb_wpr_notificatin_array['mwb_wpr_email_discription_custom_id'] = $mwb_wpr_email_discription_custom_id;
		$mwb_wpr_notificatin_array['mwb_wpr_signup_email_discription_custom_id'] = $mwb_wpr_signup_email_discription_custom_id;
		$mwb_wpr_notificatin_array['mwb_wpr_product_email_discription_custom_id'] = $mwb_wpr_product_email_discription_custom_id;
		$mwb_wpr_notificatin_array['mwb_wpr_comment_email_discription_custom_id'] = $mwb_wpr_comment_email_discription_custom_id;
		$mwb_wpr_notificatin_array['mwb_wpr_amount_email_discription_custom_id'] = $mwb_wpr_amount_email_discription_custom_id;
		$mwb_wpr_notificatin_array['mwb_wpr_referral_email_discription_custom_id'] = $mwb_wpr_referral_email_discription_custom_id;
		$mwb_wpr_notificatin_array['mwb_wpr_referral_purchase_email_discription_custom_id'] = $mwb_wpr_referral_purchase_email_discription_custom_id;
		$mwb_wpr_notificatin_array['mwb_wpr_membership_email_discription_custom_id'] = $mwb_wpr_membership_email_discription_custom_id;
		$mwb_wpr_notificatin_array['mwb_wpr_pro_pur_by_points_email_subject'] = $mwb_wpr_pro_pur_by_points_email_subject;
		$mwb_wpr_notificatin_array['mwb_wpr_pro_pur_by_points_discription_custom_id'] = $mwb_wpr_pro_pur_by_points_discription_custom_id;
		$mwb_wpr_notificatin_array['mwb_wpr_deduct_assigned_point_subject'] = $mwb_wpr_deduct_assigned_point_subject;
		$mwb_wpr_notificatin_array['mwb_wpr_deduct_assigned_point_desciption'] = $mwb_wpr_deduct_assigned_point_desciption;
		$mwb_wpr_notificatin_array['mwb_wpr_deduct_per_currency_point_description'] = $mwb_wpr_deduct_per_currency_point_description;
		$mwb_wpr_notificatin_array['mwb_wpr_deduct_per_currency_point_subject'] = $mwb_wpr_deduct_per_currency_point_subject;
		$mwb_wpr_notificatin_array['mwb_wpr_return_pro_pur_description'] = $mwb_wpr_return_pro_pur_description;
		$mwb_wpr_notificatin_array['mwb_wpr_return_pro_pur_subject'] = $mwb_wpr_return_pro_pur_subject;
		$mwb_wpr_notificatin_array['mwb_wpr_point_sharing_description'] = $mwb_wpr_point_sharing_description ;
		$mwb_wpr_notificatin_array['mwb_wpr_point_sharing_subject'] = $mwb_wpr_point_sharing_subject;
		$mwb_wpr_notificatin_array['mwb_wpr_point_on_cart_subject'] = $mwb_wpr_point_on_cart_subject;
		$mwb_wpr_notificatin_array['mwb_wpr_point_on_cart_desc'] = $mwb_wpr_point_on_cart_desc;
		if(is_array($mwb_wpr_notificatin_array))
			update_option('mwb_wpr_notificatin_array',$mwb_wpr_notificatin_array);
	}
	?>
`	<div class="notice notice-success is-dismissible">
	    <p><strong><?php _e('Settings saved.',MWB_WPR_Domain); ?></strong></p>
	    <button type="button" class="notice-dismiss">
	        <span class="screen-reader-text"><?php _e('Dismiss this notice.',MWB_WPR_Domain); ?></span>
	    </button>
	</div>	
	<?php
}
?>
<?php $mwb_wpr_notification_settings = get_option('mwb_wpr_notificatin_array',true); ?>
<?php if(!is_array($mwb_wpr_notification_settings)): $mwb_wpr_notification_settings = array(); endif;?>
	<?php 
		$mwb_wpr_notificatin_enable = isset($mwb_wpr_notification_settings['mwb_wpr_notificatin_enable']) ? intval($mwb_wpr_notification_settings['mwb_wpr_notificatin_enable']) : 0;
		$mwb_wpr_email_subject = isset($mwb_wpr_notification_settings['mwb_wpr_email_subject']) ? $mwb_wpr_notification_settings['mwb_wpr_email_subject']:__("Custom Points Notification",MWB_WPR_Domain);
		$mwb_wpr_signup_email_subject = isset($mwb_wpr_notification_settings['mwb_wpr_signup_email_subject']) ? $mwb_wpr_notification_settings['mwb_wpr_signup_email_subject']:__("Signup Points Notification",MWB_WPR_Domain);
		$mwb_wpr_product_email_subject = isset($mwb_wpr_notification_settings['mwb_wpr_product_email_subject']) ? $mwb_wpr_notification_settings['mwb_wpr_product_email_subject']:__("Product Purchase Points Notification",MWB_WPR_Domain);
		$mwb_wpr_comment_email_subject = isset($mwb_wpr_notification_settings['mwb_wpr_comment_email_subject']) ? $mwb_wpr_notification_settings['mwb_wpr_comment_email_subject']:__("Comment Points Notification",MWB_WPR_Domain);
		$mwb_wpr_amount_email_subject = isset($mwb_wpr_notification_settings['mwb_wpr_amount_email_subject']) ? $mwb_wpr_notification_settings['mwb_wpr_amount_email_subject']:__("Order Amount Points Notification",MWB_WPR_Domain);
		$mwb_wpr_referral_email_subject = isset($mwb_wpr_notification_settings['mwb_wpr_referral_email_subject']) ? $mwb_wpr_notification_settings['mwb_wpr_referral_email_subject']:__("Referral Points Notification",MWB_WPR_Domain);
		$mwb_wpr_referral_purchase_email_subject = isset($mwb_wpr_notification_settings['mwb_wpr_referral_purchase_email_subject']) ? $mwb_wpr_notification_settings['mwb_wpr_referral_purchase_email_subject']:__("Referral Purchase Points Notification",MWB_WPR_Domain);
		$mwb_wpr_membership_email_subject = isset($mwb_wpr_notification_settings['mwb_wpr_membership_email_subject']) ? $mwb_wpr_notification_settings['mwb_wpr_membership_email_subject']:__("Upgrade Membership Level Notification",MWB_WPR_Domain);
		$mwb_wpr_pro_pur_by_points_email_subject = isset($mwb_wpr_notification_settings['mwb_wpr_pro_pur_by_points_email_subject']) ? $mwb_wpr_notification_settings['mwb_wpr_pro_pur_by_points_email_subject']:__("Product Purchased Through Points Notification",MWB_WPR_Domain);
		$mwb_wpr_email_discription_custom_id = isset($mwb_wpr_notification_settings['mwb_wpr_email_discription_custom_id']) ?$mwb_wpr_notification_settings['mwb_wpr_email_discription_custom_id'] : __('Your points is updated and your total points is ',MWB_WPR_Domain).'[Total Points].';
		$mwb_wpr_signup_email_discription_custom_id = isset($mwb_wpr_notification_settings['mwb_wpr_signup_email_discription_custom_id']) ?$mwb_wpr_notification_settings['mwb_wpr_signup_email_discription_custom_id'] :  __("You have received ",MWB_WPR_Domain).'[Points]'.__(' points and your total points is ',MWB_WPR_Domain).'[Total Points].';
		$mwb_wpr_product_email_discription_custom_id = isset($mwb_wpr_notification_settings['mwb_wpr_product_email_discription_custom_id']) ?$mwb_wpr_notification_settings['mwb_wpr_product_email_discription_custom_id'] : __("You have received ",MWB_WPR_Domain).'[Points]'.__(' points and your total points is ',MWB_WPR_Domain).'[Total Points].';
		$mwb_wpr_comment_email_discription_custom_id = isset($mwb_wpr_notification_settings['mwb_wpr_comment_email_discription_custom_id']) ?$mwb_wpr_notification_settings['mwb_wpr_comment_email_discription_custom_id'] : __("You have received ",MWB_WPR_Domain).'[Points]'.__(' points and your total points is ',MWB_WPR_Domain).'[Total Points].';
		$mwb_wpr_amount_email_discription_custom_id = isset($mwb_wpr_notification_settings['mwb_wpr_amount_email_discription_custom_id']) ?$mwb_wpr_notification_settings['mwb_wpr_amount_email_discription_custom_id'] : __("You have received ",MWB_WPR_Domain).'[Points]'.__(' points and your total points is ',MWB_WPR_Domain).'[Total Points].';
		$mwb_wpr_referral_email_discription_custom_id = isset($mwb_wpr_notification_settings['mwb_wpr_referral_email_discription_custom_id']) ?$mwb_wpr_notification_settings['mwb_wpr_referral_email_discription_custom_id'] : __("You have received ",MWB_WPR_Domain).'[Points]'.__(' points and your total points is ',MWB_WPR_Domain).'[Total Points].';
		$mwb_wpr_referral_purchase_email_discription_custom_id = isset($mwb_wpr_notification_settings['mwb_wpr_referral_purchase_email_discription_custom_id']) ?$mwb_wpr_notification_settings['mwb_wpr_referral_purchase_email_discription_custom_id'] : __("You have received ",MWB_WPR_Domain).'[Points]'.__(' points and your total points is ',MWB_WPR_Domain).'[Total Points].';
		$mwb_wpr_membership_email_discription_custom_id = isset($mwb_wpr_notification_settings['mwb_wpr_membership_email_discription_custom_id']) ? $mwb_wpr_notification_settings['mwb_wpr_membership_email_discription_custom_id'] : __("Your User Level has been Upgraded to ",MWB_WPR_Domain).'[USERLEVEL]'.__('. And Now You will get some offers on some products.',MWB_WPR_Domain);
		$mwb_wpr_pro_pur_by_points_discription_custom_id = isset($mwb_wpr_notification_settings['mwb_wpr_pro_pur_by_points_discription_custom_id']) ? $mwb_wpr_notification_settings['mwb_wpr_pro_pur_by_points_discription_custom_id'] : __("Product Purchased Point ",MWB_WPR_Domain).'[PROPURPOINTS]'.__(' has been deducted from your points on purchasing, and your Total Point is ',MWB_WPR_Domain).'[TOTALPOINTS].';
		$mwb_wpr_deduct_assigned_point_subject = isset($mwb_wpr_notification_settings['mwb_wpr_deduct_assigned_point_subject']) ? $mwb_wpr_notification_settings['mwb_wpr_deduct_assigned_point_subject'] : __("Your Points has been Deducted",MWB_WPR_Domain);	
		$mwb_wpr_deduct_assigned_point_desciption = isset($mwb_wpr_notification_settings['mwb_wpr_deduct_assigned_point_desciption']) ? $mwb_wpr_notification_settings['mwb_wpr_deduct_assigned_point_desciption'] : __('Your ',MWB_WPR_Domain).'[DEDUCTEDPOINT]'.__(' has been deducted from your total points as you have requested for your refund, and your Total Point is ',MWB_WPR_Domain).'[TOTALPOINTS].';
		$mwb_wpr_deduct_per_currency_point_subject = isset($mwb_wpr_notification_settings['mwb_wpr_deduct_per_currency_point_subject']) ? $mwb_wpr_notification_settings['mwb_wpr_deduct_per_currency_point_subject'] : __("Your Points has been Deducted",MWB_WPR_Domain);	
		$mwb_wpr_deduct_per_currency_point_description = isset($mwb_wpr_notification_settings['mwb_wpr_deduct_per_currency_point_description']) ? $mwb_wpr_notification_settings['mwb_wpr_deduct_per_currency_point_description'] : __('Your ',MWB_WPR_Domain).'[DEDUCTEDPOINT]'.__(' has been deducted from your total points as you have requested for your refund, and your Total Point is ',MWB_WPR_Domain).'[TOTALPOINTS].';
		$mwb_wpr_return_pro_pur_subject = isset($mwb_wpr_notification_settings['mwb_wpr_return_pro_pur_subject']) ? $mwb_wpr_notification_settings['mwb_wpr_return_pro_pur_subject'] : __("Your Points has been Returned",MWB_WPR_Domain);	
		$mwb_wpr_return_pro_pur_description = isset($mwb_wpr_notification_settings['mwb_wpr_return_pro_pur_description']) ? $mwb_wpr_notification_settings['mwb_wpr_return_pro_pur_description'] : __('Your ',MWB_WPR_Domain).'[RETURNPOINT]'.__(' has been returned to your point account as you have requested for your refund, and your Total Point is ',MWB_WPR_Domain).'[TOTALPOINTS].';
		$mwb_wpr_point_sharing_subject = isset($mwb_wpr_notification_settings['mwb_wpr_point_sharing_subject']) ? $mwb_wpr_notification_settings['mwb_wpr_point_sharing_subject'] : __("Received Points Successfully!!",MWB_WPR_Domain);	
		$mwb_wpr_point_sharing_description = isset($mwb_wpr_notification_settings['mwb_wpr_point_sharing_description']) ? $mwb_wpr_notification_settings['mwb_wpr_point_sharing_description'] : __('You have received ',MWB_WPR_Domain).'[RECEIVEDPOINT]'.__(' by your one of the friend having Email Id is ',MWB_WPR_Domain).'[SENDEREMAIL]'.__(' and your Total Point is ',MWB_WPR_Domain).'[TOTALPOINTS].';

		$mwb_wpr_point_on_cart_subject = isset($mwb_wpr_notification_settings['mwb_wpr_point_on_cart_subject']) ? $mwb_wpr_notification_settings['mwb_wpr_point_on_cart_subject'] : __("Points Deducted!!",MWB_WPR_Domain);	
		$mwb_wpr_point_on_cart_desc = isset($mwb_wpr_notification_settings['mwb_wpr_point_on_cart_desc']) ? $mwb_wpr_notification_settings['mwb_wpr_point_on_cart_desc'] :  __('Your ',MWB_WPR_Domain).'[DEDUCTCARTPOINT]'.__(' Points has been deducted from your account, now your Total Point is ',MWB_WPR_Domain).'[TOTALPOINTS].';
	?>
<div class="mwb_table">
	<table class="form-table mwb_wpr_notificatin_tab mwp_wpr_settings">
		<tbody>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="mwb_wpr_notification_setting_enable"><?php _e('Enable', MWB_WPR_Domain)?></label>
				</th>
				<td class="forminp forminp-text">
					<?php 
					$attribute_description = __('Check this box to enable the points notification.', MWB_WPR_Domain);
					echo wc_help_tip( $attribute_description );
					?>
					<label for="mwb_wpr_notification_setting_enable">
						<input type="checkbox" name="mwb_wpr_notification_setting_enable" <?php checked($mwb_wpr_notificatin_enable,1);?> id="mwb_wpr_notification_setting_enable" class="input-text"> <?php _e('Enable Points Notification',MWB_WPR_Domain);?>
					</label>						
				</td>
			</tr>
		</tbody>
	</table>		
		<div class="mwb_wpr_email_wrapper">
			<div class="mwb_wpr_email_wrapper_text" >
				<h2><?php _e("Points table's Custom Points Notification Settings",MWB_WPR_Domain);?></h2>
			</div>
			<div class="mwb_wpr_email_wrapper_content">
				<table class="form-table mwb_wpr_notificatin_tab mwp_wpr_settings">
				<tbody>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_wpr_email_subject"><?php _e('Email Subject', MWB_WPR_Domain)?></label>
					</th>
					<td class="forminp forminp-text">
						<?php 
						$attribute_description = __('Input subject for email.', MWB_WPR_Domain);
						echo wc_help_tip( $attribute_description );
						?>
						<label for="mwb_wpr_email_subject">
							<input type="text" name="mwb_wpr_email_subject" value="<?php echo $mwb_wpr_email_subject;?>" id="mwb_wpr_email_subject" class="input-text mwb_wpr_new_woo_ver_style_text"> 
						</label>						
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_wpr_email_discription_custom_id"><?php _e('Email Description', MWB_WPR_Domain)?></label>
						<?php 
						$attribute_description = __('Enter Email Description for user.', MWB_WPR_Domain);
						echo wc_help_tip( $attribute_description );
						?>
					</th>
					<td class="forminp forminp-text">
					<?php _e('Use ',MWB_WPR_Domain );echo '[Total Points]';_e(' shortcode in place of Total Points, ',MWB_WPR_Domain);echo '[USERNAME]';_e(' shortcode in place of username and',MWB_WPR_Domain );?><strong> <?php _e('In this section donot use',MWB_WPR_Domain);echo '[Points]'; _e(' shortcode ', MWB_WPR_Domain); ?></strong>
						<label for="mwb_wpr_email_discription_custom_id">
							<?php 
							$content=stripcslashes( $mwb_wpr_email_discription_custom_id );
							$editor_id="mwb_wpr_email_discription_custom_id";
							$settings = array(
		                            'media_buttons'    => false,
		                            'drag_drop_upload' => true,
		                            'dfw'              => true,
		                            'teeny'            => true,
		                            'editor_height'    => 200,
		                            'editor_class'       => 'mwb_wpr_new_woo_ver_style_textarea',
		                            'textarea_name'    => "mwb_wpr_email_discription_custom_id"
		                    );
							 wp_editor($content,$editor_id,$settings); ?>
						</label>						
					</td>
				</tr>
				</tbody>
				</table>
			</div>
		</div>
		<div class="mwb_wpr_email_wrapper">
			<div class="mwb_wpr_email_wrapper_text" >
				<h2><?php _e("Signup Points Notification Settings",MWB_WPR_Domain);?></h2>
			</div>
			<div class="mwb_wpr_email_wrapper_content" style="display:none">	
				<table class="form-table mwb_wpr_notificatin_tab mwp_wpr_settings">
				<tbody>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_wpr_signup_email_subject"><?php _e('Email Subject', MWB_WPR_Domain)?></label>
					</th>
					<td class="forminp forminp-text">
						<?php 
						$attribute_description = __('Input subject for email.', MWB_WPR_Domain);
						echo wc_help_tip( $attribute_description );
						?>
						<label for="mwb_wpr_signup_email_subject">
							<input type="text" name="mwb_wpr_signup_email_subject" value="<?php echo $mwb_wpr_signup_email_subject;?>" id="mwb_wpr_signup_email_subject" class="input-text mwb_wpr_new_woo_ver_style_text"> 
						</label>						
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_wpr_signup_email_discription_custom_id"><?php _e('Email Description', MWB_WPR_Domain)?></label>
						<?php 
						$attribute_description = __('Enter Email Description for user.', MWB_WPR_Domain);
						echo wc_help_tip( $attribute_description );
						?>
					</th>
					<td class="forminp forminp-text">
					<?php _e('Use ',MWB_WPR_Domain );echo '[Points]';_e(' shortcode to be placed Signup Points dynamically, ',MWB_WPR_Domain);echo '[USERNAME]';_e(' shortcode in place of username',MWB_WPR_Domain); echo '[Refer Points]'; _e(' in place of Referral points',MWB_WPR_Domain);echo '[Comment Points]';_e(' in place of comment points ',MWB_WPR_Domain);echo '[Per Currency Spent Points]';_e(' in place of Per Currency spent points and ',MWB_WPR_Domain );echo '[Total Points]';_e(' shortcode in place of Total Points.', MWB_WPR_Domain); ?>
						<label for="mwb_wpr_signup_email_discription_custom_id">
							<?php 
							$content=stripcslashes($mwb_wpr_signup_email_discription_custom_id);
							$editor_id="mwb_wpr_signup_email_discription_custom_id";
							$settings = array(
		                            'media_buttons'    => false,
		                            'drag_drop_upload' => true,
		                            'dfw'              => true,
		                            'teeny'            => true,
		                            'editor_height'    => 200,
		                            'editor_class'       => 'mwb_wpr_new_woo_ver_style_textarea',
		                            'textarea_name'    => "mwb_wpr_signup_email_discription_custom_id"
		                    );
							 wp_editor($content,$editor_id,$settings); ?>
						</label>						
					</td>
				</tr>
				</tbody>
				</table>
			</div>
		</div>
		<div class="mwb_wpr_email_wrapper">
			<div class="mwb_wpr_email_wrapper_text" >
				<h2><?php _e("Product Purchase Points Notification Settings",MWB_WPR_Domain);?></h2>
			</div>
			<div class="mwb_wpr_email_wrapper_content" style="display:none">	
				<table class="form-table mwb_wpr_notificatin_tab mwp_wpr_settings">
				<tbody>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_wpr_product_email_subject"><?php _e('Email Subject', MWB_WPR_Domain)?></label>
					</th>
					<td class="forminp forminp-text">
						<?php 
						$attribute_description = __('Input subject for email.', MWB_WPR_Domain);
						echo wc_help_tip( $attribute_description );
						?>
						<label for="mwb_wpr_product_email_subject">
							<input type="text" name="mwb_wpr_product_email_subject" value="<?php echo $mwb_wpr_product_email_subject;?>" id="mwb_wpr_product_email_subject" class="input-text mwb_wpr_new_woo_ver_style_text"> 
						</label>						
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_wpr_product_email_discription_custom_id"><?php _e('Email Description', MWB_WPR_Domain)?></label>
						<?php 
						$attribute_description = __('Enter Email Description for user.', MWB_WPR_Domain);
						echo wc_help_tip( $attribute_description );
						?>
					</th>
					<td class="forminp forminp-text">
					<?php _e('Use ',MWB_WPR_Domain );echo '[Points]';_e(' shortcode in place of Product Purchase Points ',MWB_WPR_Domain);echo '[USERNAME]';_e(' shortcode in place of username ',MWB_WPR_Domain); echo '[Refer Points]'; _e(' in place of Referral points',MWB_WPR_Domain); echo '[Comment Points]';_e(' in place of comment points ',MWB_WPR_Domain); echo '[Per Currency Spent Points]'; _e(' in place of Per Currency spent points and ',MWB_WPR_Domain);echo '[Total Points]';_e(' shortcode in place of Total Points.', MWB_WPR_Domain); ?>
						<label for="mwb_wpr_product_email_discription_custom_id">
							<?php 
							$content=stripcslashes($mwb_wpr_product_email_discription_custom_id);
							$editor_id="mwb_wpr_product_email_discription_custom_id";
							$settings = array(
		                            'media_buttons'    => false,
		                            'drag_drop_upload' => true,
		                            'dfw'              => true,
		                            'teeny'            => true,
		                            'editor_height'    => 200,
		                            'editor_class'       => 'mwb_wpr_new_woo_ver_style_textarea',
		                            'textarea_name'    => "mwb_wpr_product_email_discription_custom_id"
		                    );
							 wp_editor($content,$editor_id,$settings); ?>
						</label>						
					</td>
				</tr>
				</tbody>
				</table>	
			</div>
		</div>
		<div class="mwb_wpr_email_wrapper">
			<div class="mwb_wpr_email_wrapper_text" >
				<h2><?php _e("Comment Points Notification Settings",MWB_WPR_Domain);?></h2>
			</div>
			<div class="mwb_wpr_email_wrapper_content" style="display:none">
				<table class="form-table mwb_wpr_notificatin_tab mwp_wpr_settings">
				<tbody>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_wpr_comment_email_subject"><?php _e('Email Subject', MWB_WPR_Domain)?></label>
					</th>
					<td class="forminp forminp-text">
						<?php 
						$attribute_description = __('Input subject for email.', MWB_WPR_Domain);
						echo wc_help_tip( $attribute_description );
						?>
						<label for="mwb_wpr_comment_email_subject">
							<input type="text" name="mwb_wpr_comment_email_subject" value="<?php echo $mwb_wpr_comment_email_subject;?>" id="mwb_wpr_comment_email_subject" class="input-text mwb_wpr_new_woo_ver_style_text"> 
						</label>						
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_wpr_comment_email_discription_custom_id"><?php _e('Email Description', MWB_WPR_Domain)?></label>
						<?php 
						$attribute_description = __('Enter Email Description for user.', MWB_WPR_Domain);
						echo wc_help_tip( $attribute_description );
						?>
					</th>
					<td class="forminp forminp-text">
					<?php _e('Use ',MWB_WPR_Domain );echo '[Points]';_e(' shortcode in place of comment points ',MWB_WPR_Domain);echo '[USERNAME]';_e(' shortcode in place of username ',MWB_WPR_Domain);echo '[Refer Points]';_e(' in place of Referral points ',MWB_WPR_Domain);echo '[Per Currency Spent Points]'; _e(' in place of per currency spent points and ',MWB_WPR_Domain );echo '[Total Points]';_e(' shortcode in place of Total Points.', MWB_WPR_Domain); ?>
						<label for="mwb_wpr_comment_email_discription_custom_id">
							<?php 
							$content=stripcslashes($mwb_wpr_comment_email_discription_custom_id);
							$editor_id="mwb_wpr_comment_email_discription_custom_id";
							$settings = array(
		                            'media_buttons'    => false,
		                            'drag_drop_upload' => true,
		                            'dfw'              => true,
		                            'teeny'            => true,
		                            'editor_height'    => 200,
		                            'editor_class'       => 'mwb_wpr_new_woo_ver_style_textarea',
		                            'textarea_name'    => "mwb_wpr_comment_email_discription_custom_id"
		                    );
							 wp_editor($content,$editor_id,$settings); ?>
						</label>						
					</td>
				</tr>
				</tbody>
				</table>	
			</div>
		</div>
		<div class="mwb_wpr_email_wrapper">
			<div class="mwb_wpr_email_wrapper_text" >
				<h2><?php _e('Order Amount Points Notification Settings(Per ',MWB_WPR_Domain). get_woocommerce_currency_symbol()._e(' Spent Points)',MWB_WPR_Domain);?></h2>
			</div>
			<div class="mwb_wpr_email_wrapper_content" style="display:none">
				<table class="form-table mwb_wpr_notificatin_tab mwp_wpr_settings">
				<tbody>	
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_wpr_amount_email_subject"><?php _e('Email Subject', MWB_WPR_Domain)?></label>
					</th>
					<td class="forminp forminp-text">
						<?php 
						$attribute_description = __('Input subject for email.', MWB_WPR_Domain);
						echo wc_help_tip( $attribute_description );
						?>
						<label for="mwb_wpr_amount_email_subject">
							<input type="text" name="mwb_wpr_amount_email_subject" value="<?php echo $mwb_wpr_amount_email_subject;?>" id="mwb_wpr_amount_email_subject" class="input-text mwb_wpr_new_woo_ver_style_text"> 
						</label>						
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_wpr_amount_email_discription_custom_id"><?php _e('Email Description', MWB_WPR_Domain)?></label>
						<?php 
						$attribute_description = __('Enter Email Description for user.', MWB_WPR_Domain);
						echo wc_help_tip( $attribute_description );
						?>
					</th>
					<td class="forminp forminp-text">
					<?php _e('Use ',MWB_WPR_Domain );echo '[Points]';_e(' shortcode in place of per currency spent points',MWB_WPR_Domain);echo '[USERNAME]';_e(' shortcode in place of username ',MWB_WPR_Domain);echo '[Refer Points]';_e(' in place of Referral points',MWB_WPR_Domain);echo '[Comment Points]'; _e('in place of comment points and ',MWB_WPR_Domain );echo '[Total Points]';_e(' shortcode in place of Total Points.', MWB_WPR_Domain); ?>
						<label for="mwb_wpr_amount_email_discription_custom_id">
							<?php 
							$content=stripcslashes($mwb_wpr_amount_email_discription_custom_id);
							$editor_id="mwb_wpr_amount_email_discription_custom_id";
							$settings = array(
		                            'media_buttons'    => false,
		                            'drag_drop_upload' => true,
		                            'dfw'              => true,
		                            'teeny'            => true,
		                            'editor_height'    => 200,
		                            'editor_class'       => 'mwb_wpr_new_woo_ver_style_textarea',
		                            'textarea_name'    => "mwb_wpr_amount_email_discription_custom_id"
		                    );
							 wp_editor($content,$editor_id,$settings); ?>
						</label>						
					</td>
				</tr>
				</tbody>
				</table>	
			</div>
		</div>
		<div class="mwb_wpr_email_wrapper">
			<div class="mwb_wpr_email_wrapper_text" >
				<h2><?php _e("Referral Points Notification Settings",MWB_WPR_Domain);?></h2>
			</div>
			<div class="mwb_wpr_email_wrapper_content" style="display:none">
				<table class="form-table mwb_wpr_notificatin_tab mwp_wpr_settings">
				<tbody>	
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_wpr_referral_email_subject"><?php _e('Email Subject', MWB_WPR_Domain)?></label>
					</th>
					<td class="forminp forminp-text">
						<?php 
						$attribute_description = __('Input subject for email.', MWB_WPR_Domain);
						echo wc_help_tip( $attribute_description );
						?>
						<label for="mwb_wpr_referral_email_subject">
							<input type="text" name="mwb_wpr_referral_email_subject" value="<?php echo $mwb_wpr_referral_email_subject;?>" id="mwb_wpr_referral_email_subject" class="input-text mwb_wpr_new_woo_ver_style_text"> 
						</label>						
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_wpr_referral_email_discription_custom_id"><?php _e('Email Description', MWB_WPR_Domain)?></label>
						<?php 
						$attribute_description = __('Enter Email Description for user.', MWB_WPR_Domain);
						echo wc_help_tip( $attribute_description );
						?>
					</th>
					<td class="forminp forminp-text">
					<?php _e('Use ',MWB_WPR_Domain );echo '[Points]';_e(' shortcode in place of Referral Points',MWB_WPR_Domain);echo '[USERNAME]';_e(' shortcode in place of username ',MWB_WPR_Domain); echo '[Per Currency Spent Points]'; _e(' in place of per currency spent points',MWB_WPR_Domain); echo ' [Comment Points]'; _e(' in place of comment points and ',MWB_WPR_Domain );echo '[Total Points]';_e(' shortcode in place of Total Points.', MWB_WPR_Domain); ?>
						<label for="mwb_wpr_referral_email_discription_custom_id">
							<?php 
							$content=stripcslashes($mwb_wpr_referral_email_discription_custom_id);
							$editor_id="mwb_wpr_referral_email_discription_custom_id";
							$settings = array(
		                            'media_buttons'    => false,
		                            'drag_drop_upload' => true,
		                            'dfw'              => true,
		                            'teeny'            => true,
		                            'editor_height'    => 200,
		                            'editor_class'       => 'mwb_wpr_new_woo_ver_style_textarea',
		                            'textarea_name'    => "mwb_wpr_referral_email_discription_custom_id"
		                    );
							 wp_editor($content,$editor_id,$settings); ?>
						</label>						
					</td>
				</tr>
				</tbody>
				</table>
			</div>
		</div>
		<div class="mwb_wpr_email_wrapper">
			<div class="mwb_wpr_email_wrapper_text" >
				<h2><?php _e("Referral Purchase Points Notification Settings",MWB_WPR_Domain);?></h2>
			</div>
			<div class="mwb_wpr_email_wrapper_content" style="display:none">
				<table class="form-table mwb_wpr_notificatin_tab mwp_wpr_settings">
				<tbody>	
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_wpr_referral_purchase_email_subject"><?php _e('Email Subject', MWB_WPR_Domain)?></label>
					</th>
					<td class="forminp forminp-text">
						<?php 
						$attribute_description = __('Input subject for email.', MWB_WPR_Domain);
						echo wc_help_tip( $attribute_description );
						?>
						<label for="mwb_wpr_referral_purchase_email_subject">
							<input type="text" name="mwb_wpr_referral_purchase_email_subject" value="<?php echo $mwb_wpr_referral_purchase_email_subject;?>" id="mwb_wpr_referral_purchase_email_subject" class="input-text mwb_wpr_new_woo_ver_style_text"> 
						</label>						
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_wpr_referral_purchase_email_discription_custom_id"><?php _e('Email Description', MWB_WPR_Domain)?></label>
						<?php 
						$attribute_description = __('Enter Email Description for user.', MWB_WPR_Domain);
						echo wc_help_tip( $attribute_description );
						?>
					</th>
					<td class="forminp forminp-text">
					<?php _e('Use ',MWB_WPR_Domain );echo '[Points]'; _e(' shortcode in place of Referral Purchase Points ',MWB_WPR_Domain);echo'[Refer Points]'; _e(' in place of Referral points',MWB_WPR_Domain);echo '[Comment Points]'; _e(' in place of comment points',MWB_WPR_Domain);echo ' [Per Currency Spent Points]'; _e(' in place of Per Currency spent points and ',MWB_WPR_Domain );echo '[Total Points]';_e(' shortcode in place of Total Points.', MWB_WPR_Domain); ?>
						<label for="mwb_wpr_referral_email_discription_custom_id">
							<?php 
							$content=stripcslashes($mwb_wpr_referral_purchase_email_discription_custom_id);
							$editor_id="mwb_wpr_referral_purchase_email_discription_custom_id";
							$settings = array(
		                            'media_buttons'    => false,
		                            'drag_drop_upload' => true,
		                            'dfw'              => true,
		                            'teeny'            => true,
		                            'editor_height'    => 200,
		                            'editor_class'       => 'mwb_wpr_new_woo_ver_style_textarea',
		                            'textarea_name'    => "mwb_wpr_referral_purchase_email_discription_custom_id"
		                    );
							 wp_editor($content,$editor_id,$settings); ?>
						</label>						
					</td>
				</tr>
				</tbody>
				</table>
			</div>
		</div>
		<div class="mwb_wpr_email_wrapper">
			<div class="mwb_wpr_email_wrapper_text" >
				<h2><?php _e("Upgrade Membership Level Notification",MWB_WPR_Domain);?></h2>
			</div>
			<div class="mwb_wpr_email_wrapper_content" style="display:none">
				<table class="form-table mwb_wpr_notificatin_tab mwp_wpr_settings">
				<tbody>	
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_wpr_membership_email_subject"><?php _e('Email Subject', MWB_WPR_Domain)?></label>
					</th>
					<td class="forminp forminp-text">
						<?php 
						$attribute_description = __('Input subject for email.', MWB_WPR_Domain);
						echo wc_help_tip( $attribute_description );
						?>
						<label for="mwb_wpr_membership_email_subject">
							<input type="text" name="mwb_wpr_membership_email_subject" value="<?php echo $mwb_wpr_membership_email_subject;?>" id="mwb_wpr_membership_email_subject" class="input-text mwb_wpr_new_woo_ver_style_text"> 
						</label>						
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_wpr_membership_email_discription_custom_id"><?php _e('Email Description', MWB_WPR_Domain)?></label>
						<?php 
						$attribute_description = __('Enter Email Description for user.', MWB_WPR_Domain);
						echo wc_help_tip( $attribute_description );
						?>
					</th>
					<td class="forminp forminp-text">
					<?php _e('Use ',MWB_WPR_Domain );echo '[USERLEVEL]'; _e(' shortcode in place of User Level ',MWB_WPR_Domain);echo '[USERNAME]';_e(' shortcode in place of username ',MWB_WPR_Domain);?>
						<label for="mwb_wpr_membership_email_discription_custom_id">
							<?php 
							$content=stripcslashes($mwb_wpr_membership_email_discription_custom_id);
							$editor_id="mwb_wpr_membership_email_discription_custom_id";
							$settings = array(
		                            'media_buttons'    => false,
		                            'drag_drop_upload' => true,
		                            'dfw'              => true,
		                            'teeny'            => true,
		                            'editor_height'    => 200,
		                            'editor_class'       => 'mwb_wpr_new_woo_ver_style_textarea',
		                            'textarea_name'    => "mwb_wpr_membership_email_discription_custom_id"
		                    );
							 wp_editor($content,$editor_id,$settings); ?>
						</label>						
					</td>
				</tr>
				</tbody>
				</table>
			</div>
		</div>
		<div class="mwb_wpr_email_wrapper">
			<div class="mwb_wpr_email_wrapper_text" >
				<h2><?php _e("Purchase Products through Points Notification",MWB_WPR_Domain);?></h2>
			</div>
			<div class="mwb_wpr_email_wrapper_content" style="display:none">
				<table class="form-table mwb_wpr_notificatin_tab mwp_wpr_settings">
				<tbody>	
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_wpr_pro_pur_by_points_email_subject"><?php _e('Email Subject', MWB_WPR_Domain)?></label>
					</th>
					<td class="forminp forminp-text">
						<?php 
						$attribute_description = __('Input subject for email.', MWB_WPR_Domain);
						echo wc_help_tip( $attribute_description );
						?>
						<label for="mwb_wpr_pro_pur_by_points_email_subject">
							<input type="text" name="mwb_wpr_pro_pur_by_points_email_subject" value="<?php echo $mwb_wpr_pro_pur_by_points_email_subject;?>" id="mwb_wpr_pro_pur_by_points_email_subject" class="input-text mwb_wpr_new_woo_ver_style_text"> 
						</label>						
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_wpr_pro_pur_by_points_discription_custom_id"><?php _e('Email Description', MWB_WPR_Domain)?></label>
						<?php 
						$attribute_description = __('Enter Email Description for user.', MWB_WPR_Domain);
						echo wc_help_tip( $attribute_description );
						?>
					</th>
					<td class="forminp forminp-text">
					<?php _e('Use ',MWB_WPR_Domain );echo '[PROPURPOINTS]'; _e(' shortcode in place of product purchasing points',MWB_WPR_Domain);echo '[USERNAME]';_e(' shortcode in place of username ',MWB_WPR_Domain);echo '[TOTALPOINTS]';_e(' shortcode in place of Total Points.', MWB_WPR_Domain);?>
						<label for="mwb_wpr_pro_pur_by_points_discription_custom_id">
							<?php 
							$content=stripcslashes($mwb_wpr_pro_pur_by_points_discription_custom_id);
							$editor_id="mwb_wpr_pro_pur_by_points_discription_custom_id";
							$settings = array(
		                            'media_buttons'    => false,
		                            'drag_drop_upload' => true,
		                            'dfw'              => true,
		                            'teeny'            => true,
		                            'editor_height'    => 200,
		                            'editor_class'       => 'mwb_wpr_new_woo_ver_style_textarea',
		                            'textarea_name'    => "mwb_wpr_pro_pur_by_points_discription_custom_id"
		                    );
							 wp_editor($content,$editor_id,$settings); ?>
						</label>						
					</td>
				</tr>
				</tbody>
				</table>
			</div>
		</div>
		<div class="mwb_wpr_email_wrapper">
			<div class="mwb_wpr_email_wrapper_text" >
				<h2><?php _e("Deduct Assigned Point Notification",MWB_WPR_Domain);?></h2>
			</div>
			<div class="mwb_wpr_email_wrapper_content" style="display:none">
				<table class="form-table mwb_wpr_notificatin_tab mwp_wpr_settings">
				<tbody>	
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_wpr_deduct_assigned_point_subject"><?php _e('Email Subject', MWB_WPR_Domain)?></label>
					</th>
					<td class="forminp forminp-text">
						<?php 
						$attribute_description = __('Input subject for email.', MWB_WPR_Domain);
						echo wc_help_tip( $attribute_description );
						?>
						<label for="mwb_wpr_deduct_assigned_point_subject">
							<input type="text" name="mwb_wpr_deduct_assigned_point_subject" value="<?php echo $mwb_wpr_deduct_assigned_point_subject;?>" id="mwb_wpr_deduct_assigned_point_subject" class="input-text mwb_wpr_new_woo_ver_style_text"> 
						</label>						
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_wpr_deduct_assigned_point_desciption"><?php _e('Email Description', MWB_WPR_Domain)?></label>
						<?php 
						$attribute_description = __('Enter Email Description for user.', MWB_WPR_Domain);
						echo wc_help_tip( $attribute_description );
						?>
					</th>
					<td class="forminp forminp-text">
					<?php _e('Use ',MWB_WPR_Domain );echo '[DEDUCTEDPOINT]'; _e(' shortcode in place of points which has been deducted ',MWB_WPR_Domain);echo '[USERNAME]';_e(' shortcode in place of username ',MWB_WPR_Domain);echo '[TOTALPOINTS]';_e(' shortcode in place of Total Remaining Points.', MWB_WPR_Domain);?>
						<label for="mwb_wpr_deduct_assigned_point_desciption">
							<?php 
							$content=stripcslashes($mwb_wpr_deduct_assigned_point_desciption);
							$editor_id="mwb_wpr_deduct_assigned_point_desciption";
							$settings = array(
		                            'media_buttons'    => false,
		                            'drag_drop_upload' => true,
		                            'dfw'              => true,
		                            'teeny'            => true,
		                            'editor_height'    => 200,
		                            'editor_class'       => 'mwb_wpr_new_woo_ver_style_textarea',
		                            'textarea_name'    => "mwb_wpr_deduct_assigned_point_desciption"
		                    );
							 wp_editor($content,$editor_id,$settings); ?>
						</label>						
					</td>
				</tr>
				</tbody>
				</table>
			</div>
		</div>
		<div class="mwb_wpr_email_wrapper">
			<div class="mwb_wpr_email_wrapper_text" >
				<h2><?php _e("Deduct 'Per Currency Spent' Point Notification",MWB_WPR_Domain);?></h2>
			</div>
			<div class="mwb_wpr_email_wrapper_content" style="display:none">
				<table class="form-table mwb_wpr_notificatin_tab mwp_wpr_settings">
				<tbody>	
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_wpr_deduct_per_currency_point_subject"><?php _e('Email Subject', MWB_WPR_Domain)?></label>
					</th>
					<td class="forminp forminp-text">
						<?php 
						$attribute_description = __('Input subject for email.', MWB_WPR_Domain);
						echo wc_help_tip( $attribute_description );
						?>
						<label for="mwb_wpr_deduct_per_currency_point_subject">
							<input type="text" name="mwb_wpr_deduct_per_currency_point_subject" value="<?php echo $mwb_wpr_deduct_per_currency_point_subject;?>" id="mwb_wpr_deduct_per_currency_point_subject" class="input-text mwb_wpr_new_woo_ver_style_text"> 
						</label>						
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_wpr_deduct_per_currency_point_description"><?php _e('Email Description', MWB_WPR_Domain)?></label>
						<?php 
						$attribute_description = __('Enter Email Description for user.', MWB_WPR_Domain);
						echo wc_help_tip( $attribute_description );
						?>
					</th>
					<td class="forminp forminp-text">
					<?php _e('Use ',MWB_WPR_Domain );echo '[DEDUCTEDPOINT]'; _e(' shortcode in place of points which has been deducted ',MWB_WPR_Domain);echo '[USERNAME]';_e(' shortcode in place of username ',MWB_WPR_Domain);echo '[TOTALPOINTS]';_e(' shortcode in place of Total Remaining Points.', MWB_WPR_Domain);?>
						<label for="mwb_wpr_deduct_per_currency_point_description">
							<?php 
							$content=stripcslashes($mwb_wpr_deduct_per_currency_point_description);
							$editor_id="mwb_wpr_deduct_per_currency_point_description";
							$settings = array(
		                            'media_buttons'    => false,
		                            'drag_drop_upload' => true,
		                            'dfw'              => true,
		                            'teeny'            => true,
		                            'editor_height'    => 200,
		                            'editor_class'       => 'mwb_wpr_new_woo_ver_style_textarea',
		                            'textarea_name'    => "mwb_wpr_deduct_per_currency_point_description"
		                    );
							 wp_editor($content,$editor_id,$settings); ?>
						</label>						
					</td>
				</tr>
				</tbody>
				</table>
			</div>
		</div>
		<div class="mwb_wpr_email_wrapper">
			<div class="mwb_wpr_email_wrapper_text" >
				<h2><?php _e("Return  'Product Purchase through Point' Notification",MWB_WPR_Domain);?></h2>
			</div>
			<div class="mwb_wpr_email_wrapper_content" style="display:none">
				<table class="form-table mwb_wpr_notificatin_tab mwp_wpr_settings">
				<tbody>	
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_wpr_deduct_per_currency_point_subject"><?php _e('Email Subject', MWB_WPR_Domain)?></label>
					</th>
					<td class="forminp forminp-text">
						<?php 
						$attribute_description = __('Input subject for email.', MWB_WPR_Domain);
						echo wc_help_tip( $attribute_description );
						?>
						<label for="mwb_wpr_return_pro_pur_subject">
							<input type="text" name="mwb_wpr_return_pro_pur_subject" value="<?php echo $mwb_wpr_return_pro_pur_subject;?>" id="mwb_wpr_return_pro_pur_subject" class="input-text mwb_wpr_new_woo_ver_style_text"> 
						</label>						
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_wpr_return_pro_pur_description"><?php _e('Email Description', MWB_WPR_Domain)?></label>
						<?php 
						$attribute_description = __('Enter Email Description for user.', MWB_WPR_Domain);
						echo wc_help_tip( $attribute_description );
						?>
					</th>
					<td class="forminp forminp-text">
					<?php _e('Use ',MWB_WPR_Domain );echo '[RETURNPOINT]'; _e(' shortcode in place of points which has been returned ',MWB_WPR_Domain);echo '[USERNAME]';_e(' shortcode in place of username ',MWB_WPR_Domain);echo '[TOTALPOINTS]';_e(' shortcode in place of Total Remaining Points.', MWB_WPR_Domain);?>
						<label for="mwb_wpr_return_pro_pur_description">
							<?php 
							$content=stripcslashes($mwb_wpr_return_pro_pur_description);
							$editor_id="mwb_wpr_return_pro_pur_description";
							$settings = array(
		                            'media_buttons'    => false,
		                            'drag_drop_upload' => true,
		                            'dfw'              => true,
		                            'teeny'            => true,
		                            'editor_height'    => 200,
		                            'editor_class'       => 'mwb_wpr_new_woo_ver_style_textarea',
		                            'textarea_name'    => "mwb_wpr_return_pro_pur_description"
		                    );
							 wp_editor($content,$editor_id,$settings); ?>
						</label>						
					</td>
				</tr>
				</tbody>
				</table>
			</div>
		</div>
		<div class="mwb_wpr_email_wrapper">
			<div class="mwb_wpr_email_wrapper_text" >
				<h2><?php _e("Point Sharing Notification",MWB_WPR_Domain);?></h2>
			</div>
			<div class="mwb_wpr_email_wrapper_content" style="display:none">
				<table class="form-table mwb_wpr_notificatin_tab mwp_wpr_settings">
				<tbody>	
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_wpr_point_sharing_subject"><?php _e('Email Subject', MWB_WPR_Domain)?></label>
					</th>
					<td class="forminp forminp-text">
						<?php 
						$attribute_description = __('Input subject for email.', MWB_WPR_Domain);
						echo wc_help_tip( $attribute_description );
						?>
						<label for="mwb_wpr_point_sharing_subject">
							<input type="text" name="mwb_wpr_point_sharing_subject" value="<?php echo $mwb_wpr_point_sharing_subject;?>" id="mwb_wpr_point_sharing_subject" class="input-text mwb_wpr_new_woo_ver_style_text"> 
						</label>						
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_wpr_point_sharing_description"><?php _e('Email Description', MWB_WPR_Domain)?></label>
						<?php 
						$attribute_description = __('Enter Email Description for user.', MWB_WPR_Domain);
						echo wc_help_tip( $attribute_description );
						?>
					</th>
					<td class="forminp forminp-text">
					<?php _e('Use ',MWB_WPR_Domain );echo '[RECEIVEDPOINT]'; _e(' shortcode in place of points which has been received ',MWB_WPR_Domain);echo '[USERNAME]';_e(' shortcode in place of username ',MWB_WPR_Domain);echo '[SENDEREMAIL]'; _e(' shortcode in place of email id of Sender',MWB_WPR_Domain);echo '[TOTALPOINTS]';_e(' shortcode in place of Total Points.', MWB_WPR_Domain);?>
						<label for="mwb_wpr_point_sharing_description">
							<?php 
							$content=stripcslashes($mwb_wpr_point_sharing_description);
							$editor_id="mwb_wpr_point_sharing_description";
							$settings = array(
		                            'media_buttons'    => false,
		                            'drag_drop_upload' => true,
		                            'dfw'              => true,
		                            'teeny'            => true,
		                            'editor_height'    => 200,
		                            'editor_class'       => 'mwb_wpr_point_sharing_description',
		                            'textarea_name'    => "mwb_wpr_point_sharing_description"
		                    );
							 wp_editor($content,$editor_id,$settings); ?>
						</label>						
					</td>
				</tr>
				</tbody>
				</table>
			</div>
		</div>
		<div class="mwb_wpr_email_wrapper">
			<div class="mwb_wpr_email_wrapper_text" >
				<h2><?php _e("Points On Cart Sub-Total",MWB_WPR_Domain);?></h2>
			</div>
			<div class="mwb_wpr_email_wrapper_content" style="display:none">
				<table class="form-table mwb_wpr_notificatin_tab mwp_wpr_settings">
				<tbody>	
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_wpr_point_on_cart_subject"><?php _e('Email Subject', MWB_WPR_Domain)?></label>
					</th>
					<td class="forminp forminp-text">
						<?php 
						$attribute_description = __('Input subject for email.', MWB_WPR_Domain);
						echo wc_help_tip( $attribute_description );
						?>
						<label for="mwb_wpr_point_on_cart_subject">
							<input type="text" name="mwb_wpr_point_on_cart_subject" value="<?php echo $mwb_wpr_point_on_cart_subject;?>" id="mwb_wpr_point_on_cart_subject" class="input-text mwb_wpr_new_woo_ver_style_text"> 
						</label>						
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_wpr_point_on_cart_desc"><?php _e('Email Description', MWB_WPR_Domain)?></label>
						<?php 
						$attribute_description = __('Enter Email Description for user.', MWB_WPR_Domain);
						echo wc_help_tip( $attribute_description );
						?>
					</th>
					<td class="forminp forminp-text">
					<?php _e('Use ',MWB_WPR_Domain );echo '[DEDUCTCARTPOINT]'; _e(' shortcode in place of points which has been deducted ',MWB_WPR_Domain);echo '[USERNAME]';_e(' shortcode in place of username ',MWB_WPR_Domain);echo '[TOTALPOINTS]';_e(' shortcode in place of Total Points.', MWB_WPR_Domain);?>
						<label for="mwb_wpr_point_on_cart_desc">
							<?php 
							$content=stripcslashes($mwb_wpr_point_on_cart_desc);
							$editor_id="mwb_wpr_point_on_cart_desc";
							$settings = array(
		                            'media_buttons'    => false,
		                            'drag_drop_upload' => true,
		                            'dfw'              => true,
		                            'teeny'            => true,
		                            'editor_height'    => 200,
		                            'editor_class'       => 'mwb_wpr_point_on_cart_desc',
		                            'textarea_name'    => "mwb_wpr_point_on_cart_desc"
		                    );
							 wp_editor($content,$editor_id,$settings); ?>
						</label>						
					</td>
				</tr>
				</tbody>
				</table>
			</div>
		</div>
	</tbody>
</table>
</div>
<div class="clear"></div>
<p class="submit">
	<input type="submit" value='<?php _e("Save changes",MWB_WPR_Domain); ?>' class="button-primary woocommerce-save-button mwb_wpr_save_changes" name="mwb_wpr_save_notification">
</p>	