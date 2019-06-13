<?php 
/**
 * Exit if accessed directly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$generaltab = "";
$pointstab = "";
$couponstab = "";
$Notificationtab="";
$othersettingtab="";
$ordertotaltab = "";
$ordertotaltabactive = "";
$pointsexpirationtab = "";
$licensetab = "";
$propurchasepointstab="";
$generaltabactive = false;
$pointstabactive = false;
$couponstabactive = false;
$Notificationtab=false;
$membershiptab=false;
$propointstab=false;
$propurchasepointstab=false;
$propurchasepointstabactive=false;
$propointsactivetab=false;
$Notificationtabactive=false;
$membershiptabactive=false;
$othersettingtabactive=false;
$pointsexpirationtabactive = false;
$licensetabactive = false;
$new_addon = false;
$new_addonactive = false;
if(isset($_GET['tab']) && !empty($_GET['tab'])){
	$tab = $_GET['tab'];
	if($tab == 'mwb_wpr_general_setting'){
		$generaltab = "nav-tab-active";
		$generaltabactive = true;
	}
	if($tab == 'mwb_wpr_points_table'){
		$pointstab = "nav-tab-active";
		$pointstabactive = true;
	}
	if($tab == 'mwb_wpr_coupons_tab'){
		$couponstab = "nav-tab-active";
		$couponstabactive = true;
	}
	if($tab == 'mwb_wpr_notificatin_tab'){
		$Notificationtab = "nav-tab-active";
		$Notificationtabactive = true;
	}
	if($tab == 'mwb_wpr_membership_tab'){
		$membershiptab = "nav-tab-active";
		$membershiptabactive = true;
	}
	if($tab == 'mwb_wpr_pro_points_tab'){
		$propointstab = "nav-tab-active";
		$propointsactivetab = true;
	}
	if($tab == 'mwb_wpr_purchase_points_only'){
		$propurchasepointstab = "nav-tab-active";
		$propurchasepointstabactive = true;
	}
	if($tab == 'mwb_wpr_othersetting_tab'){
		$othersettingtab = "nav-tab-active";
		$othersettingtabactive = true;
	}
	if($tab == 'mwb_wpr_point_expiration_tab'){
		$pointsexpirationtab = 'nav-tab-active';
		$pointsexpirationtabactive = true;
	}
	if($tab == 'mwb_validate_license'){
		$licensetab = 'nav-tab-active';
		$licensetabactive = true;
	}
	if($tab == 'mwb_wpr_new_addon'){
		$new_addon = 'nav-tab-active';
		$new_addonactive = true;
	}
	if($tab == 'mwb_wpr_ordertotalsetting_tab'){
		$ordertotaltab = 'nav-tab-active';
		$ordertotaltabactive = true;
	}

}
if(empty($tab)){
	$generaltab = "nav-tab-active";
	$generaltabactive = true;
}
$mwb_wpr_activated_time = get_option('mwb_wpr_activation_date_time',false);
// $mwb_wpr_activated_time = date_i18n('Y-m-d',$mwb_wpr_activated_time);
$mwb_wpr_after_month = strtotime('+ 30 days', $mwb_wpr_activated_time);
$mwb_wpr_currenttime = current_time('timestamp');
$mwb_wpr_time_difference = $mwb_wpr_after_month - $mwb_wpr_currenttime;
$mwb_wpr_days_left = floor($mwb_wpr_time_difference/(60*60*24));

if( !get_option('mwb_wpr_plugin_verified')){
	$mwb_wpr_msg = __('Activate the License Key before ',MWB_WPR_Domain);
	?>
	<div class="update-nag">
		<?php echo '<strong>'.$mwb_wpr_msg; ?><a href="<?php get_admin_url()?>admin.php?page=mwb-wpr-setting&tab=mwb_validate_license"><?php echo $mwb_wpr_days_left;?></a><?php _e(' days of activation - You might risk losing data by then, and you will not be able to use the plugin !',MWB_WPR_Domain); ?><a href="<?php get_admin_url()?>admin.php?page=mwb-wpr-setting&tab=mwb_validate_license"><?php _e(' Activate now ',MWB_WPR_Domain); ?></strong></a>
	</div>
	<?php
}
?>
<div class="wrap woocommerce" id="mwb_wpr_settings_wrapper">
	<div style="display: none;" class="loading-style-bg" id="mwb_wpr_loader">
		<img src="<?php echo MWB_WPR_URL;?>/assets/images/loading.gif">
	</div>
	<form enctype="multipart/form-data" action="" id="mainform" method="post">
		<h1 class="mwb_wpr_setting"><?php _e('Points and Rewards Settings', MWB_WPR_Domain )?></h1>
		<nav class="nav-tab-wrapper woo-nav-tab-wrapper" id="mwb_wpr_nav_tab">
			<a class="nav-tab <?php echo $generaltab;?>" href="<?php echo MWB_WPR_HOME_URL?>admin.php?page=mwb-wpr-setting&tab=mwb_wpr_general_setting"><?php _e('General', MWB_WPR_Domain);?></a>
			<a class="nav-tab <?php echo $couponstab;?>" href="<?php echo MWB_WPR_HOME_URL?>admin.php?page=mwb-wpr-setting&tab=mwb_wpr_coupons_tab"><?php _e('Coupon Settings', MWB_WPR_Domain);?></a>
			<a class="nav-tab <?php echo $pointstab;?>" href="<?php echo MWB_WPR_HOME_URL?>admin.php?page=mwb-wpr-setting&tab=mwb_wpr_points_table"><?php _e('Points Table', MWB_WPR_Domain);?></a>
			<a class="nav-tab <?php echo $Notificationtab;?>" href="<?php echo MWB_WPR_HOME_URL?>admin.php?page=mwb-wpr-setting&tab=mwb_wpr_notificatin_tab"><?php _e('Points Notification', MWB_WPR_Domain);?></a>
			<a class="nav-tab <?php echo $membershiptab;?>" href="<?php echo MWB_WPR_HOME_URL?>admin.php?page=mwb-wpr-setting&tab=mwb_wpr_membership_tab"><?php _e('Membership', MWB_WPR_Domain);?></a>
			<a class="nav-tab <?php echo $propointstab;?>" href="<?php echo MWB_WPR_HOME_URL?>admin.php?page=mwb-wpr-setting&tab=mwb_wpr_pro_points_tab"><?php _e('Assign Product Points', MWB_WPR_Domain);?></a>
			<a class="nav-tab <?php echo $propurchasepointstab;?>" href="<?php echo MWB_WPR_HOME_URL?>admin.php?page=mwb-wpr-setting&tab=mwb_wpr_purchase_points_only"><?php _e('Product Purchase Points', MWB_WPR_Domain);?></a>
			<a class="nav-tab <?php echo $othersettingtab;?>" href="<?php echo MWB_WPR_HOME_URL?>admin.php?page=mwb-wpr-setting&tab=mwb_wpr_othersetting_tab"><?php _e('Other Settings', MWB_WPR_Domain);?></a>
			<a class="nav-tab <?php echo $ordertotaltab;?>" href="<?php echo MWB_WPR_HOME_URL?>admin.php?page=mwb-wpr-setting&tab=mwb_wpr_ordertotalsetting_tab"><?php _e('Order Total Points', MWB_WPR_Domain);?></a>
			<a class="nav-tab <?php echo $pointsexpirationtab;?>" href="<?php echo MWB_WPR_HOME_URL?>admin.php?page=mwb-wpr-setting&tab=mwb_wpr_point_expiration_tab"><?php _e('Points Expiration', MWB_WPR_Domain);?></a>				
			<a class="nav-tab <?php echo $new_addon;?>" href="<?php echo MWB_WPR_HOME_URL?>admin.php?page=mwb-wpr-setting&tab=mwb_wpr_new_addon"><?php _e('Addons', MWB_WPR_Domain);?></a>
			<?php 
			if(!get_option('mwb_wpr_plugin_verified',false)){
				?>
				<a class="nav-tab <?php echo $licensetab;?>" href="<?php echo MWB_WPR_HOME_URL?>admin.php?page=mwb-wpr-setting&tab=mwb_validate_license"><?php _e('Add License', MWB_WPR_Domain);?></a>
			<?php
			}
			do_action('mwb_wpr_setting_tab');
			?>	
		
		</nav>
		<?php 
		if($generaltabactive == true){
			include_once MWB_WPR_DIRPATH.'/includes/admin/settings/templates/mwb_general-settings.php';
		}
		if($pointstabactive == true){	
			include_once MWB_WPR_DIRPATH.'/includes/admin/settings/templates/mwb_points_table.php';
		}
		if($couponstabactive == true){	
			include_once MWB_WPR_DIRPATH.'/includes/admin/settings/templates/mwb_coupon_settings.php';
		}
		if($Notificationtabactive == true){
			include_once MWB_WPR_DIRPATH.'/includes/admin/settings/templates/mwb_points_notification_settings.php';
		}
		if($membershiptabactive == true){
			include_once MWB_WPR_DIRPATH.'/includes/admin/settings/templates/mwb_membership_settings.php';
		}
		if($propointsactivetab == true){
			include_once MWB_WPR_DIRPATH.'/includes/admin/settings/templates/mwb_assign_pro_points.php';
		}		
		if($propurchasepointstabactive == true){
			include_once MWB_WPR_DIRPATH.'/includes/admin/settings/templates/mwb_purchase_points_only.php';
		}
		if($othersettingtabactive == true){
			include_once MWB_WPR_DIRPATH.'/includes/admin/settings/templates/mwb_other_setting.php';
		}
		if($pointsexpirationtabactive == true){
			include_once MWB_WPR_DIRPATH.'/includes/admin/settings/templates/mwb_point_expiration.php';
		}
		if($licensetabactive == true){
			include_once MWB_WPR_DIRPATH.'/includes/admin/settings/templates/mwb_license_verify.php';
		}
		if($new_addonactive == true){
			include_once MWB_WPR_DIRPATH.'/includes/admin/settings/templates/mwb_addon.php';
		}
		if($ordertotaltabactive == true){
			include_once MWB_WPR_DIRPATH.'/includes/admin/settings/templates/mwb_order_total.php';
		}		
		?>
	</form>
</div>