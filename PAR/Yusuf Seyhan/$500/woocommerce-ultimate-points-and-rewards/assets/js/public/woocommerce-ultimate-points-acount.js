( function ( $ ) {

	$(document).ready(function(){
		$('.mwb_wpr_common_table').css('display','none');
		$('.mwb_wpr_custom_coupon').click(function(){
			var user_id = $(this).data('id');
			var user_points = $('#mwb_custom_point_num').val();
			var message = ''; var html = '';
			$("#mwb_wpr_points_notification").html("");
			user_points = parseFloat(user_points);
			if(user_points > 0 && $.isNumeric(user_points))
			{
				jQuery("#mwb_wpr_loader").show();
				var data = {
					action:'mwb_wpr_generate_custom_coupon',
					points:user_points,
					user_id:user_id,
					mwb_nonce:mwb_wpr.mwb_wpr_nonce,					
				};
		      	$.ajax({
		  			url: mwb_wpr.ajaxurl, 
		  			type: "POST",  
		  			data: data,
		  			dataType :'json',
		  			success: function(response) 
		  			{
		  				jQuery("#mwb_wpr_loader").hide();
		  				if(response.result == true)
						{	
							$('.points_log').css('display','block');
							$('.points_log').html(response.html);
							$('#mwb_wpr_points_only').html(response.points);
							message = response.message;
							html = '<b style="color:green;">'+message+'</b>';
							$('.mwb_current_points').html(response.points);
							var minimum_points=mwb_wpr.minimum_points;
							if(response.points < minimum_points)
							{
								$('#points_form').html(mwb_wpr.minimum_points_text);
							}
						}
						else
						{
							message = response.message;
							html = '<b style="color:red;">'+message+'</b>';
						}
						$("#mwb_wpr_points_notification").html(html);
		  			}
		  		});
			}
			else
			{
				html = '<b style="color:red;">'+mwb_wpr.message+'</b>';
				$("#mwb_wpr_points_notification").html(html);
			}
		});
		$('.mwb_wpr_generate_coupon').click(function(){
			var user_id = $(this).data('id');
			var message = ''; var html = '';
			$("#mwb_wpr_points_notification").html("");
			jQuery("#mwb_wpr_loader").show();
			var data = {
				action:'mwb_wpr_generate_original_coupon',
				user_id:user_id,
				mwb_nonce:mwb_wpr.mwb_wpr_nonce,				
			};
	      	
	      	$.ajax({
	  			url: mwb_wpr.ajaxurl, 
	  			type: "POST",  
	  			data: data,
	  			dataType :'json',
	  			success: function(response) 
	  			{
	  				
	  				jQuery("#mwb_wpr_loader").hide();
	  				if(response.result == true)
					{	
						$('.points_log').css('display','block');
						$('.points_log').html(response.html);
						$('#mwb_wpr_points_only').html(response.points);
						message = response.message;
						html = '<b style="color:green;">'+message+'</b>';
						$('.mwb_current_points').html(response.points);
						var minimum_points=mwb_wpr.minimum_points;
						if(response.points < minimum_points)
						{
							$('#points_form').hide();
						}
					}
					else
					{
						message = response.message;
						html = '<b style="color:red;">'+message+'</b>';
					}
					$("#mwb_wpr_points_notification").html(html);
	  			}
	  		});
			
		});
		$('#mwb_wpr_upgrade_level').click(function(){
			var mwb_wpr_selected_role = $('#mwb_wpr_membership_roles').val();
			if(confirm(mwb_wpr.confirmation_msg)){
				//$(this).closest("form#mwb_wpr_membership" ).submit();
				return true;
			}
			else{
				return false;
			}
		});
		$('.mwb_wpr_level_benefits').click(function(){

			var mwb_wpr_level = $(this).data('id');
			jQuery('#mwb_wpr_popup_wrapper_'+mwb_wpr_level).css('display', 'block');

			jQuery('.mwb_wpr_close').click(function(){

				jQuery('#mwb_wpr_popup_wrapper_'+mwb_wpr_level).css('display', 'none');
			});

		});
		var btns = document.querySelectorAll('button');
	    var clipboard = new Clipboard(btns);
	    var message = '';
	    var html = '';
	    $('#mwb_wpr_share_point').click(function(){
	    	var user_id = $(this).data('id');
			var shared_point = $('#mwb_wpr_enter_point').val();
			var email_id = $('#mwb_wpr_enter_email').val();
			$("#mwb_wpr_shared_points_notification").html("");
			if(shared_point > 0 )
			{
				jQuery("#mwb_wpr_loader").show();
				var data = {
					action:'mwb_wpr_sharing_point_to_other',
					shared_point:shared_point,
					user_id:user_id,
					email_id:email_id,
					mwb_nonce:mwb_wpr.mwb_wpr_nonce,			
				};
		      	$.ajax({
		  			url: mwb_wpr.ajaxurl, 
		  			type: "POST",  
		  			data: data,
		  			dataType :'json',
		  			success: function(response) 
		  			{
		  				jQuery("#mwb_wpr_loader").hide();
		  				if(response !== null && response.result == true)
						{	
							$('#mwb_wpr_points_only').html(response.available_points);
							message = response.message;
							html = '<b style="color:green;">'+message+'</b>';
						}
						else
						{
							message = response.message;
							html = '<b style="color:red;">'+message+'</b>';
						}
						$("#mwb_wpr_shared_points_notification").html(html);
		  			}
		  		});
			}
			else
			{
				html = '<b style="color:red;">'+mwb_wpr.message+'</b>';
				$("#mwb_wpr_shared_points_notification").html(html);
			}
	    });
	  $(document).on('change','#mwb_wpr_pro_cost_to_points',function(){
	  	//Make Readonly if selected in backend
	  	if(mwb_wpr.make_readonly == 1){
	  		$('#mwb_wpr_some_custom_points').attr('readonly',true);
	  	}
	  	if($(this).prop("checked") == true){
	  			var mwb_wpr_some_custom_points = $('#mwb_wpr_some_custom_points').val();
				$('.mwb_wpr_enter_some_points').css("display","block");
			}
			else{
				$('.mwb_wpr_enter_some_points').css("display","none");
			}
	  });
	   $(document).on('change','#mwb_wpr_some_custom_points',function(){
               var mwb_wpr_some_custom_points = $('#mwb_wpr_some_custom_points').val();
               // var mwb_wpr_pro_cost_to_points = $('#mwb_wpr_pro_cost_to_points').val();
            if(mwb_wpr_pro_cost_to_points != null){
                var mwb_wpr_deadline_custom_points = $('.mwb_wpr_hidden_points').val();
                if(mwb_wpr_some_custom_points > mwb_wpr_deadline_custom_points){
                    var html = '<b style="color:red">'+mwb_wpr.mwb_wpr_custom_notice+'</b>';
                }
                $('#mwb_wpr_pro_cost_to_points').val(mwb_wpr_some_custom_points);
            }
            else{
                $('#mwb_wpr_pro_cost_to_points').val('');
            }
       });
       var pre_variation_id = '';
	   $(document).on('change','.variation_id',function(e){

	   		e.preventDefault();
			var variation_id = $(this).val();
			if( variation_id != null && variation_id > 0 && pre_variation_id != variation_id){
				pre_variation_id = variation_id;
				block($('.summary.entry-summary'));
				var data = {
					action:'mwb_wpr_append_variable_point',
					variation_id:pre_variation_id,
					mwb_nonce:mwb_wpr.mwb_wpr_nonce,			
				};
				$.ajax({
		  			url: mwb_wpr.ajaxurl, 
		  			type: "POST",  
		  			data: data,
		  			dataType :'json',
		  			success: function(response) 
		  			{
		  				if(response.result == true && response.variable_points > 0)
						{	

							$('.mwb_wpr_variable_points').html(response.variable_points);
							$('.mwb_wpr_product_point').css('display','block');
						}
						if(response.result_price == "html"  && response.variable_price_html != null){
							$('.woocommerce-variation-price').html(response.variable_price_html);
						}
						if(response.result_point == "product_purchased_using_point" && response.variable_points_cal_html !=null){
							$('.mwb_wpr_variable_pro_pur_using_point').html(response.variable_points_cal_html);
							$('.mwb_wpr_variable_pro_pur_using_point').css('display','block');
						}
						//MWB CUSTOM CODE
						if(response.purchase_pro_pnts_only == "purchased_pro_points" && response.price_html !=null){
							$('.woocommerce-variation-price').html(response.price_html+" Points");
							//$('.woocommerce-Price-amount').html(response.price_html+" Points");
						}
						//MWB CUSTOM CODE
						
		  			},
		  			complete: function() 
					{
						unblock( $( '.summary.entry-summary' ) );
					}
		  		});
			}
			else if(variation_id != null && variation_id > 0){
				block($('.summary.entry-summary'));
				var data = {
					action:'mwb_pro_purchase_points_only',
					variation_id:variation_id,
					mwb_nonce:mwb_wpr.mwb_wpr_nonce,			
				};
				$.ajax({
		  			url: mwb_wpr.ajaxurl, 
		  			type: "POST",  
		  			data: data,
		  			dataType :'json',
		  			success: function(response) 
		  			{
		  				
						//MWB CUSTOM CODE
						if(response.purchase_pro_pnts_only == "purchased_pro_points" && response.price_html !=null){
							$('.woocommerce-variation-price').html(response.price_html+" Points");
							//$('.woocommerce-Price-amount').html(response.price_html+" Points");
						}
						//MWB CUSTOM CODE
						
		  			},
		  			complete: function() 
					{
						unblock( $( '.summary.entry-summary' ) );
					}
		  		});
			}
		});
	   if($('input[id="mwb_wpr_pro_cost_to_points"]').prop("checked") == true){
			$('.mwb_wpr_enter_some_points').css("display","block");
		}
		else{
			$('.mwb_wpr_enter_some_points').css("display","none");
		}

		$(document).on('change','#mwb_wgm_price',function(){
			var mwb_gift_price = $(this).val();
			if(mwb_gift_price != null){
				$('.mwb_wpr_when_variable_pro').html(mwb_gift_price);
				$('#mwb_wpr_some_custom_points').val(mwb_gift_price);
				$('#mwb_wpr_pro_cost_to_points').val(mwb_gift_price);
			}
		});
		
		//Slide toggle on tables
		$(document).on('click','.mwb_wpr_common_slider',function(){
			$(this).siblings('.mwb_wpr_common_table').slideToggle("fast");
			$(this).children('.mwb_wpr_open_toggle').toggleClass('mwb_wpr_plus_icon');
		});

		//Custom Points on Cart Subtotal handling via Ajax
		$('#mwb_cart_points_apply').click(function(){
			var user_id = $(this).data('id');
			var user_total_point = $(this).data('point');
			var message = ''; var html = '';
			var mwb_wpr_cart_points_rate = mwb_wpr.mwb_wpr_cart_points_rate;
			var mwb_wpr_cart_price_rate = mwb_wpr.mwb_wpr_cart_price_rate;
			var mwb_cart_points = $('#mwb_cart_points').val();
			// var mwb_convert_points_to_money = Math.ceil((mwb_cart_points * mwb_wpr_cart_price_rate)/mwb_wpr_cart_points_rate);
			$("#mwb_wpr_cart_points_notice").html("");
			$("mwb_wpr_cart_points_success").html("");
			if(mwb_cart_points !== 'undefined' && mwb_cart_points !== '' && mwb_cart_points !== null && mwb_cart_points > 0){
				if(user_total_point !== null && user_total_point > 0 && user_total_point >= mwb_cart_points ){
					block($('.woocommerce-cart-form'));
					block($('.woocommerce-checkout'));
					var data = {
						action:'mwb_wpr_apply_fee_on_cart_subtotal',
						user_id:user_id,
						mwb_cart_points:mwb_cart_points,
						mwb_nonce:mwb_wpr.mwb_wpr_nonce,				
					};
			      	$.ajax({
			  			url: mwb_wpr.ajaxurl, 
			  			type: "POST",  
			  			data: data,
			  			dataType :'json',
			  			success: function(response) 
			  			{
			  				if(response.result == true){	
								message = response.message;
								html = message;
								$("#mwb_wpr_cart_points_success").html(html);
								$("#mwb_wpr_cart_points_success").show();
							}
							else{
								message = response.message;
								html = message;
								$("#mwb_wpr_cart_points_notice").html(html);
								$("#mwb_wpr_cart_points_notice").show();
							}
			  			},
			  			complete: function(){
			  				unblock($('.woocommerce-cart-form'));
			  				unblock($('.woocommerce-cart-form'));
			  				location.reload();
			  			}
			  		});
				}
				else{
					message = mwb_wpr.not_allowed;
					html = message;
					$("#mwb_wpr_cart_points_notice").html(html);
					$("#mwb_wpr_cart_points_notice").show();
				}
			}
			else{
				message = mwb_wpr.not_allowed;
				html = message;
				$("#mwb_wpr_cart_points_notice").html(html);
				$("#mwb_wpr_cart_points_notice").show();
			}
		});


		$(document).on('click','#mwb_wpr_remove_cart_point',function(){
			block($('.woocommerce-cart-form'));
			var data = {
				action:'mwb_wpr_remove_cart_point',
				mwb_nonce:mwb_wpr.mwb_wpr_nonce			
			};
	      	$.ajax({
	  			url: mwb_wpr.ajaxurl, 
	  			type: "POST",  
	  			data: data,
	  			dataType :'json',
	  			success: function(response) 
	  			{
	  				if(response.result == true){
	  					$('#mwb_cart_points').val('');
	  				}
	  			},
	  			complete: function(){
	  				unblock($('.woocommerce-cart-form'));
	  				location.reload();
	  			}
	  		});
		});
	});
	var block = function( $node ) {
		if ( ! is_blocked( $node ) ) {
			$node.addClass( 'processing' ).block( {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			} );
		}
	};
	var is_blocked = function( $node ) {
		return $node.is( '.processing' ) || $node.parents( '.processing' ).length;
	};
	var unblock = function( $node ) {
		$node.removeClass( 'processing' ).unblock();
	};

} ( jQuery ) );