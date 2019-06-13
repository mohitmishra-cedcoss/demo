<?php
/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if(isset($_POST['one_click_order_re_order_send_suggestion']))
{
	$one_click_order_re_order_mail_subject = isset($_POST['one_click_order_re_order_mail_subject']) ? sanitize_text_field($_POST['one_click_order_re_order_mail_subject']) : 'WooCommerce Refund & Exchange Client Needed Help';
	$one_click_order_re_order_mail_content = isset($_POST['one_click_order_re_order_mail_content']) ? sanitize_text_field( $_POST['one_click_order_re_order_mail_subject'] ) : 'This is default messege please contact me fast so i will feel free from this stress.';

	$one_click_order_re_order_admin_email = get_option('admin_email');

	$status = wp_mail('support@makewebbetter.com',$one_click_order_re_order_mail_subject,$one_click_order_re_order_mail_content);
	if($status)
	{
		$messege = __('Your request is submitted successfully. Our team will respond as soon as possible.','one-click-order-re-order');
		$class = "one_click_order_re_order_mail_success_messege";
	}
	else
	{
		$messege = __('Your request is not submitted. Please try again.','one-click-order-re-order');
		$class = "one_click_order_re_order_mail_unsuccess_messege";
	}
}
?>
<div class="one_click_order_re_order_help_wrapper one_click_order_re_order_help_second_wrapper">
		<div class="one_click_order_re_order_form">
		<form method="POST" action="">
			<div class="one-click-order-re-order-suggestion">
			<?php if(isset($messege)){
					?><div class="<?php echo $class ; ?>"> <?php echo $messege; ?></div><?php 
				} ?>
				<h2><?php _e('Suggestion or Query','one-click-order-re-order'); ?></h2>

				<div class="one_click_order_re_order_help_input-wrap">
					<label><?php _e('Enter Suggestion or Query title here','one-click-order-re-order'); ?></label>
					<div class="one_click_order_re_order_help_input">
						<input class="one_click_order_re_order_help_form-control text-field" type="text" name='one_click_order_re_order_mail_subject'>
					</div>
				</div>
				<div class="one_click_order_re_order_help_input-wrap">
					<label><?php _e('Enter Suggestion or Query detail here','one-click-order-re-order'); ?></label>
					<div class="one_click_order_re_order_help_input">
						<textarea  class="one_click_order_re_order_help_form-control" name="one_click_order_re_order_mail_content"></textarea>
					</div>
				</div>
				<div class="one_click_order_re_order_help-send-suggetion-button-wrap">
					<input type="submit" name="one_click_order_re_order_send_suggestion" value="<?php _e('Send Suggestion','one-click-order-re-order'); ?>" class="button-primary one_click_order_re_order_help-send-suggetion-button">
				</div>
			</div>
		</form>
	</div>
	<?php
	$one_click_order_re_order_help_data = wp_remote_get('https://demo.makewebbetter.com/api/help.json');

	if(array_key_exists('body', $one_click_order_re_order_help_data) && !empty($one_click_order_re_order_help_data['body']))
	{
		$one_click_order_re_order_help_data = json_decode($one_click_order_re_order_help_data['body']);
		if(isset($one_click_order_re_order_help_data->one_click_order_re_order))
		{	
			$one_click_order_re_order_plugin_help_detail = $one_click_order_re_order_help_data->one_click_order_re_order;
		 ?>
			<div class="one_click_order_re_order_doc_video_section">
				<ul class="one_click_order_re_order_help-link-wrap">
					<li>
						<a class="one_click_order_re_order_help-link" href="<?php echo $one_click_order_re_order_plugin_help_detail->documentation;  ?>" target="_blank">
							<img class="one_click_order_re_order_icon" src="<?php echo ONE_CLICK_ORDER_RE_ORDER_DIR_URL.'admin/images/documentation.png' ?>" alt="">
							<?php _e('Documentation','one-click-order-re-order'); ?>
						</a>
					</li>
					<li>
						<a class="one_click_order_re_order_help-link" href="<?php echo $one_click_order_re_order_plugin_help_detail->faq;  ?>" target="_blank">
							<img class="one_click_order_re_order_icon" src="<?php echo ONE_CLICK_ORDER_RE_ORDER_DIR_URL.'admin/images/faq.png' ?>" alt="">
							<?php _e('FAQ','one-click-order-re-order'); ?>
						</a>
					</li>
				</ul><?php 
				if(is_array($one_click_order_re_order_plugin_help_detail->video_iframe_src) && !empty($one_click_order_re_order_plugin_help_detail->video_iframe_src)) :
					foreach ($one_click_order_re_order_plugin_help_detail->video_iframe_src as $video_data) : 
						?>
						<div class="one_click_order_re_order_video_content">
							<div class="one_click_order_re_order_video_section">
								<iframe src="<?php echo $video_data->iframe_src; ?>" allowfullscreen="" width="100%" height="315" frameborder="0"></iframe>
								<div class="one_click_order_re_order_vedio_feature_name"><?php _e($video_data->feature_name,'one-click-order-re-order'); ?></div>
							</div>
						</div> <?php
					endforeach;
				endif; ?> 
			</div>
		<?php
		}
	}?>
</div>
