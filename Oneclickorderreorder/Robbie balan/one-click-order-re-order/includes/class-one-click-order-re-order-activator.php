<?php

/**
 * Fired during plugin activation
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    One_Click_Order_Re_order
 * @subpackage One_Click_Order_Re_order/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    One_Click_Order_Re_order
 * @subpackage One_Click_Order_Re_order/includes
 * @author     makewebbetter <webmaster@makewebbetter.com>
 */
class One_Click_Order_Re_order_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        global $wpdb;
        if (is_multisite()) 
		{
            if (is_plugin_active_for_network(__FILE__)) 
			{
				$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

				foreach ($blogids as $blog_id) 
				{

				  switch_to_blog($blog_id);
				  
				  $timestamp = get_option( 'one_click_order_re_order_lcns_thirty_days', 'not_set' );

				  if('not_set' === $timestamp ){

					$current_time = current_time( 'timestamp' );

					$thirty_days =  strtotime( '+30 days', $current_time );

					update_option( 'one_click_order_re_order_lcns_thirty_days', $thirty_days );
				    }
					wp_schedule_event( time(), 'daily', 'one_click_order_re_order_license_daily' );

					restore_current_blog();
				
			  }
			}
			else
			{
				$timestamp = get_option( 'one_click_order_re_order_lcns_thirty_days', 'not_set' );

				  if('not_set' === $timestamp ){

					$current_time = current_time( 'timestamp' );

					$thirty_days =  strtotime( '+30 days', $current_time );

					update_option( 'one_click_order_re_order_lcns_thirty_days', $thirty_days );
				}
					wp_schedule_event( time(), 'daily', 'one_click_order_re_order_license_daily' );

			      
              }

		// Validate license daily cron.
			
         }
         else
         {
         	$timestamp = get_option( 'one_click_order_re_order_lcns_thirty_days', 'not_set' );

				  if('not_set' === $timestamp ){

					$current_time = current_time( 'timestamp' );

					$thirty_days =  strtotime( '+30 days', $current_time );

					update_option( 'one_click_order_re_order_lcns_thirty_days', $thirty_days );
				}
					wp_schedule_event( time(), 'daily', 'one_click_order_re_order_license_daily' );

			      
			  }
         
	}

}
