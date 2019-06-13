<?php
$mwb_about_us_data = wp_remote_get('https://demo.makewebbetter.com/api/about_us.json');

$mwb_description = $mwb_detailed_description = $mwb_our_plans = $mwb_our_plans_url = $mwb_our_microservices = $mwb_our_microservices_url = '';
if(array_key_exists('body', $mwb_about_us_data) && !empty($mwb_about_us_data['body']))
{
	$mwb_about_us_data = json_decode($mwb_about_us_data['body']);
	$mwb_description = $mwb_about_us_data->description;
	$mwb_detailed_description = $mwb_about_us_data->detailed_description;
	$mwb_our_plans = $mwb_about_us_data->our_plans;
	$mwb_our_plans_url = $mwb_about_us_data->our_plans_url;
	$mwb_our_microservices = $mwb_about_us_data->our_microservices;
	$mwb_our_microservices_url = $mwb_about_us_data->our_microservices_url;
}
?>
<div class="one_click_order_re_order_about_wrap">
	<div class="one_click_order_re_order_about_logo_desc">
		<div class="one_click_order_re_order_about_logo">
			<img class="one_click_order_re_order_logo" src="<?php echo ONE_CLICK_ORDER_RE_ORDER_DIR_URL.'admin/images/logo.png' ?>" alt="">
		</div>
		<div class="one_click_order_re_order_about_full_desc">
			<p><?php
				if($mwb_description != '')
				{
					_e($mwb_description,'one-click-order-re-order');
				}
				else
				{
					?>
					<?php _e('Our professional team deals with throughout analysis of the inquiry published, support team maintains a detailed report on the inquiry extended by the customer. Then a meeting gets scheduled with our project manager with who guidance a roadmap gets managed and according to it, work starts off! Apart from this, you will get 24x7 support service and quick response','one-click-order-re-order'); ?>.
					
					<?php
				}
			?></p>
		</div>
	</div>
	<div class="one_click_order_re_order_about_more_desc">
	<?php
		if($mwb_detailed_description != '')
		{
			_e($mwb_detailed_description,'one-click-order-re-order');
		}
		else
		{
			?>
			<p><?php _e("Our team of trendsetters largely comprise of 5+ years of experience holder professionals from the industry who well know the Knitty-gritty of the industry, designers, content visualizers, developers and other creative professionals at large. We have surely spent much time designing great solutions for your web's smooth functioning all from scratch and thus we came out as great creators","one-click-order-re-order"); ?>.</p>
			<p><?php _e('In next 20 years, we will try to come out with something more valuable yet outstanding digital result for the new world which is ready to knock our doors!','one-click-order-re-order'); ?></p>
			<?php
		} ?>
	</div>
	<div class="one_click_order_re_order_subscription_wrap">
		<h3><?php _e('our plans','one-click-order-re-order'); ?></h3>
		<p><?php
			if($mwb_our_plans != '')
			{
				_e($mwb_our_plans,'one-click-order-re-order');
			}
			else
			{ ?>
				<?php _e('We are always trying to help our clients with all possible manners thats why we are came with monthly/yearly basis plan so purchase a plan now and we will take care of all things','one-click-order-re-order'); ?>.

			<?php } ?>
		</p>
		<a href="<?php echo $mwb_our_plans_url; ?>" target="_blank">
			<div class="one_click_order_re_order_subscription">
				<span><?php _e('Monthly/Yearly Plan','one-click-order-re-order'); ?></span>
			</div>
		</a>

	</div>
	<div class="one_click_order_re_order_subscription_wrap">
		<h3><?php _e('our microservices','one-click-order-re-order'); ?></h3>
		<p><?php
			if($mwb_our_microservices != '')
			{
				_e($mwb_our_microservices,'one-click-order-re-order');;
			}
			else
			{ ?>
				<?php _e('We are also provide micro services for taking sort term of services and our experts will boost your site so please take a look of our micro-services','one-click-order-re-order'); ?>.
			<?php } ?>
		</p>
		<a href="<?php echo $mwb_our_microservices_url; ?>" target="_blank">
			<div class="one_click_order_re_order_subscription">
				<span><?php _e('Check Our Microservices','one-click-order-re-order'); ?></span>
			</div>
		</a>
	</div>
	<?php

	$one_click_order_re_order_plugins_data = wp_remote_get('https://demo.makewebbetter.com/api/feed.json',array('plugin_id'=>'mwb_rnx'));
	if(is_array($one_click_order_re_order_plugins_data) && !empty($one_click_order_re_order_plugins_data))
	{
		if(array_key_exists('body', $one_click_order_re_order_plugins_data))
		{
			if($one_click_order_re_order_plugins_data['body'] != '')
			{
				$one_click_order_re_order_plugins_data = json_decode($one_click_order_re_order_plugins_data['body']); ?>
				<div class="one_click_order_re_order_featured_wrap">
					<h3><?php _e('our featured plugins','one-click-order-re-order'); ?></h3><?php
					foreach ($one_click_order_re_order_plugins_data as $one_click_order_re_order_data) 
					{ 
						if($one_click_order_re_order_data->image_link == '')
						{
							$mwb_standard_image_link = ONE_CLICK_ORDER_RE_ORDER_DIR_URL.'admin/images/placeholder.png';
						}
						else
						{
							$mwb_standard_image_link = $one_click_order_re_order_data->image_link;
						}
					?>
					<div class="one_click_order_re_order_featured_plugin">
						<img class="one_click_order_re_order_image" src="<?php echo $mwb_standard_image_link ?>" alt="">
						<div class="one_click_order_re_order_desc">
							<p class="one_click_order_re_order_title"><?php echo $one_click_order_re_order_data->plugin_name ?></p>
							<?php if(round($one_click_order_re_order_data->ratting) > 0) : 
							$one_click_order_re_order_counter = 0; ?>
							<div class="one_click_order_re_order_rating">
								<?php while ( $one_click_order_re_order_counter < round($one_click_order_re_order_data->ratting)) : ?>
									<img class="one_click_order_re_order_star" src="<?php echo ONE_CLICK_ORDER_RE_ORDER_DIR_URL.'admin/images/star.png' ?>" alt="">
								<?php $one_click_order_re_order_counter++;
								 endwhile; ?>
							</div>
							<?php endif; ?>
							<span class="one_click_order_re_order_price"><?php echo $one_click_order_re_order_data->price ?></span>

							<span class="one_click_order_re_order_buy_now"><a href="<?php echo $one_click_order_re_order_data->landing_page ?>" target="_blank"><?php _e('Buy Now','one-click-order-re-order'); ?></a></span>
						</div>
					</div><?php
				}
				?></div><?php		
			}
		}
	}
	?>
</div>