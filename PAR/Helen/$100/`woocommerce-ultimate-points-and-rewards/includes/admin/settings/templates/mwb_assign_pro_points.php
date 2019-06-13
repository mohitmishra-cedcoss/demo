<?php 
/**
 * Exit if accessed directly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/*
 * Assign Points to Products Template
 */
$current_tab = "mwb_wpr_pro_points_tab";

if(isset($_GET['tab']))
{
	$current_tab = $_GET['tab'];
}
if(isset($_POST['mwb_wpr_select_all_products']))
{
	if( isset($_POST['mwb_wpr_global_product_enable']) && !empty($_POST['mwb_wpr_global_product_enable']) )
	{	
		if(isset( $_POST['mwb_wpr_pro_points_to_all'] ) && !empty($_POST['mwb_wpr_pro_points_to_all']))
		{
			$mwb_wpr_pro_points_to_all = sanitize_text_field($_POST['mwb_wpr_pro_points_to_all']);
			update_option('mwb_wpr_global_product_enable','on');
			update_option('mwb_wpr_pro_points_to_all',$mwb_wpr_pro_points_to_all);
			$args = array( 'post_type' => 'product','posts_per_page' => -1);
		    $loop = new WP_Query( $args );
		    foreach ($loop->posts as $key => $value) 
		    {
		        $product = wc_get_product($value->ID);
				if( $product->is_type( 'variable' ) && $product->has_child() ){
					$parent_id = $product->get_id();
					$parent_product = wc_get_product($parent_id);	
					foreach ($parent_product->get_children() as $child_id) {
						update_post_meta($parent_id, 'mwb_product_points_enable', 'yes');
						update_post_meta($child_id, 'mwb_wpr_variable_points', $mwb_wpr_pro_points_to_all);
					}
				}
				else{
					update_post_meta($value->ID, 'mwb_product_points_enable', 'yes');
					update_post_meta($value->ID, 'mwb_points_product_value', $mwb_wpr_pro_points_to_all);
				}
		    }
		    wp_reset_query(); ?>
		    <div class="notice notice-success is-dismissible">
				<p><strong><?php echo $mwb_wpr_pro_points_to_all; _e(' Point Assigned Successfully to All Products',MWB_WPR_Domain); ?></strong></p>
				<button type="button" class="notice-dismiss">
		    		<span class="screen-reader-text"><?php _e('Dismiss this notices.',MWB_WPR_Domain); ?></span>
				</button>
			</div>
			<?php
		}
		else
		{
		?>
			<div class="notice notice-error is-dismissible">
				<p><strong><?php _e(' Please enter some points !',MWB_WPR_Domain); ?></strong></p>
				<button type="button" class="notice-dismiss">
		    		<span class="screen-reader-text"><?php _e('Dismiss this notices.',MWB_WPR_Domain); ?></span>
				</button>
			</div>
		<?php
		}
	}
	else
	{
		update_option('mwb_wpr_global_product_enable','off');
		update_option('mwb_wpr_pro_points_to_all','');
		$args = array( 'post_type' => 'product','posts_per_page' => -1);
	    $loop = new WP_Query( $args );
	    foreach ($loop->posts as $key => $value) 
	    {
	    	update_post_meta($value->ID, 'mwb_product_points_enable', 'no');
			update_post_meta($value->ID, 'mwb_points_product_value', '');
	    }
	    wp_reset_query(); ?>
	    <div class="notice notice-success is-dismissible">
			<p><strong><?php _e('Points are removed Successfully from All Products',MWB_WPR_Domain); ?></strong></p>
			<button type="button" class="notice-dismiss">
	    		<span class="screen-reader-text"><?php _e('Dismiss this notices.',MWB_WPR_Domain); ?></span>
			</button>
		</div>
	<?php 
	}
}
$mwb_wpr_global_product_enable = get_option('mwb_wpr_global_product_enable','off');
$mwb_wpr_pro_points_to_all = get_option('mwb_wpr_pro_points_to_all','');
?>
<div class="mwb_table">
<p class="mwb_wpr_section_notice"><?php _e('This is the global setting for assigning points to all products at once', MWB_WPR_Domain); ?></p>
<table class="form-table mwb_wpr_pro_points_setting mwp_wpr_settings">
	<tbody>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wpr_global_product_enable"><?php _e('Global Product Points', MWB_WPR_Domain)?></label>
			</th>
			<td class="forminp forminp-text">
				<?php 
				$attribute_description = __('This is the global setting for Product Purchase Points, check this if you want to assign points to all products at once or uncheck if you want to remove assigned points from all products at once.', MWB_WPR_Domain);
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wpr_global_product_enable">
					<input type="checkbox" <?php echo ($mwb_wpr_global_product_enable == 'on')?"checked='checked'":""?> name="mwb_wpr_global_product_enable" id="mwb_wpr_global_product_enable" class="input-text"> <?php _e('Enable Global Product Points',MWB_WPR_Domain);?>
				</label>						
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="mwb_wpr_pro_points_to_all"><?php _e('Enter Points For All Products', MWB_WPR_Domain)?></label>
			</th>
			<td class="forminp forminp-text">
				<?php 
				$attribute_description = __('Entered Points are assigned to All Products .', MWB_WPR_Domain);
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wpr_pro_points_to_all">
					<input type="number" min="1" name="mwb_wpr_pro_points_to_all" id="mwb_wpr_pro_points_to_all" value="<?php echo $mwb_wpr_pro_points_to_all;?>" class="input-text mwb_wpr_common_width">
				</label>						
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
			</th>
			<td class="forminp forminp-text">
				<input type="submit" value='<?php _e("Save",MWB_WPR_Domain); ?>' class="button-primary woocommerce-save-button mwb_wpr_common_width" name="mwb_wpr_select_all_products" style="margin-left: 3%;">
			</td>
		</tr>
	</tbody>
</table>
<p class="mwb_wpr_section_notice"><?php _e('This is the category wise setting for assigning points to product of categories, enter some valid points for assigning, leave blank fields for removing assigned points', MWB_WPR_Domain); ?></p>
<div class="mwb_wpr_categ_details">
<table class="form-table mwb_wpr_pro_points_setting mwp_wpr_settings">
	<tbody>
	<tr>
		<th class="titledesc"><?php _e('Categories',MWB_WPR_Domain);?></th>
		<th class="titledesc"><?php _e('Enter Points',MWB_WPR_Domain);?></th>
		<th class="titledesc"><?php _e('Assign/Remove',MWB_WPR_Domain);?></th>
	</tr>
	<?php
		$args = array('taxonomy'=>'product_cat');
		$categories = get_terms($args);
		if(isset($categories) && !empty($categories))
		{	
			foreach($categories as $category)
			{
				$catid = $category->term_id;
				$catname = $category->name;
				$mwb_wpr_categ_point = get_option("mwb_wpr_points_to_per_categ_".$catid,'');
				?>
				<tr>
					<td><?php echo $catname;?></td>
					<td><input type="number" min="1" name="mwb_wpr_points_to_per_categ" id="mwb_wpr_points_to_per_categ_<?php echo $catid ;?>" value="<?php echo $mwb_wpr_categ_point; ?>" class="input-text mwb_wpr_new_woo_ver_style_text"></td>
					<td><input type="button" value='<?php _e('Submit',MWB_WPR_Domain) ?>' class="button-primary woocommerce-save-button mwb_wpr_submit_per_category" name="mwb_wpr_submit_per_category" id="<?php echo $catid ;?>"></td>
				</tr>
				<?php
			}
		}	
	?>
	</tbody>
</table>
</div>
</div>