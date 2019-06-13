(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$(document).ready(function() {

		// On License form submit.
		$( 'form#one-click-order-re-order-license-form' ).on( 'submit', function(e) {

			e.preventDefault();

			$( 'div#one-click-order-re-order-ajax-loading-gif' ).css( 'display', 'inline-block' );

			var license_key =  $( 'input#one-click-order-re-order-license-key' ).val();

			one_click_order_re_order_license_request( license_key );		
		});

		// License Ajax request.
		function one_click_order_re_order_license_request( license_key ) {

			$.ajax({

		        type:'POST',
		        dataType: 'json',
	    		url: license_ajax_object.ajaxurl,

		        data: {
		        	'action': 'one_click_order_re_order_license',
		        	'one_click_order_re_order_purchase_code': license_key,
		        	'one-click-order-re-order-license-nonce': license_ajax_object.license_nonce,
		        },

		        success:function( data ) {

		        	$( 'div#one-click-order-re-order-ajax-loading-gif' ).hide();

		        	if ( false === data.status ) {

	                    $( "p#one-click-order-re-order-license-activation-status" ).css( "color", "#ff3333" );
	                }

	                else {

	                	$( "p#one-click-order-re-order-license-activation-status" ).css( "color", "#42b72a" );
	                }

		        	$( 'p#one-click-order-re-order-license-activation-status' ).html( data.msg );

		        	if ( true === data.status ) {

	                    setTimeout(function() {
	                    	window.location = license_ajax_object.reloadurl;
	                    }, 500);
	                }
		        }
			});
		}
		if ( $( '.mwb_ocor_enable_basket_for' ).length > 0 ) {
			$( '.mwb_ocor_enable_basket_for' ).select2();
	    }
	     if ($(document).find("#radio2").prop("checked")) {
              $(document).find('#mwb_ocor_basket_section').parents('tr').hide();
			$(document).find('#selected_page').parents('tr').hide();
            }
		$( document ).on( 'click', '.mwb_ocor_enabling_basket', function( event ) {
		var $this = $( this ),
		basket_section = $( '#mwb_ocor_basket_section' );
		if ( $this.val() == 'on' ) {
			
			$(document).find('#mwb_ocor_basket_section').parents('tr').show();
			$(document).find('.mwb_ocor_columns_content').parents('tr').show();
			//basket_section.removeClass( 'mwb_cng_hide' );
		} else {
			$(document).find('#mwb_ocor_basket_section').parents('tr').hide();
			$(document).find('#selected_page').parents('tr').hide();
			//basket_section.addClass( 'mwb_cng_hide' );
		}
	});
		$( document ).on( 'click', '#mwb_ocor_icon_for_basket', function() {
		tb_show( 'Upload custom icon image for basket', 'media-upload.php?type=image&amp;TB_iframe=true');
		window.original_send_to_editor = window.send_to_editor;
		window.send_to_editor = function( html ) {
			if( html ) {
				var attchUrl = $( html ).attr( 'src' );
				$( '#mwb_ocor_attachment_section img' ).attr( 'src', attchUrl );
				$( '#mwb_ocor_saved_icon_url' ).val( attchUrl );
			} else {
				window.original_send_to_editor( html );
			}
			tb_remove();
		};
		return false;
	});

	$( document ).on( 'click', '.mwb_ocor_enabling_basket', function( event ) {
		var $this = $( this ),
		basket_section = $('#mwb_ocor_basket_section' );
		if ( $this.val() == 'on' ) {
			basket_section.removeClass( 'mwb_cng_hide' );
		} else {
			basket_section.addClass( 'mwb_cng_hide' );
		}
	});


	});

})( jQuery );
