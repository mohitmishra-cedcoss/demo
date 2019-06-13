<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    One_Click_Order_Re_order
 * @subpackage One_Click_Order_Re_order/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="one-click-order-re-order-license-sec">

	<h3><?php _e('Enter your License', 'one-click-order-re-order' ) ?></h3>

    <p>
    	<?php _e('This is the License Activation Panel. After purchasing extension from ', 'one-click-order-re-order' ); ?>
    	<span>
            <a href="https://makewebbetter.com/" target="_blank" ><?php _e('MakeWebBetter',  'one-click-order-re-order' ); ?></a>
        </span>&nbsp;

        <?php _e('you will get the purchase code of this extension. Please verify your purchase below so that you can use the features of this plugin.', 'one-click-order-re-order' ); ?>
    </p>

	<form id="one-click-order-re-order-license-form">

	    <label><b><?php _e('Purchase Code : ', 'one-click-order-re-order' )?></b></label>

	    <input type="text" id="one-click-order-re-order-license-key" placeholder="<?php _e('Enter your code here.', 'one-click-order-re-order' )?>" required="">

	    <div id="one-click-order-re-order-ajax-loading-gif"><img src="<?php echo 'images/spinner.gif'; ?>"></div>
	    
	    <p id="one-click-order-re-order-license-activation-status"></p>

	    <button type="submit" class="button-primary"  id="one-click-order-re-order-license-activate"><?php _e('Activate', 'one-click-order-re-order' )?></button>
	    
	    <?php wp_nonce_field( 'one-click-order-re-order-license-nonce-action', 'one-click-order-re-order-license-nonce' ); ?>

	</form>

</div>