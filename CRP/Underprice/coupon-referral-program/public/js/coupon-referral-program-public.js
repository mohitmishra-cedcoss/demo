(function( $ ) {
	'use strict';

	$(document).ready(function(){
		/** Make draggable **/
		$( "#mwb-cpr-drag" ).draggable({
			containment: "window",
			start: function( event, ui ) {
	            $(this).addClass('mwb-cpr-dragged'); 
	        }
		});
		/** Make show-hide popup **/
  		$("#mwb-cpr-mobile-close-popup").hide();

  		/* Check if the animation is enabled then add it*/
  		var mwb_crp_animation = mwb_crp.mwb_crp_animation;
  		if(mwb_crp_animation == 'yes'){

	  		$("#mwb-cpr-drag").addClass("fadeInDownBig");
	  		setTimeout(function () { 
	  			$("#mwb-cpr-drag").removeClass("fadeInDownBig");
	  			$("#mwb-cpr-drag").addClass("rubberBand");
	  		}, 1000);
	  		setTimeout(function () { 
	  			$("#mwb-cpr-drag").removeClass("rubberBand");
	  		}, 2000);

  		}
  		/* End of Animation Section */

  		/** For desktops **/
		$(document).on('click','#notify_user_gain_tab',function(){
			$('#notify_user_gain_tab').addClass('active');
			$('#notify_user_redeem').removeClass('active');
			$('#notify_user_earn_more_section').css('display','block');
			$('#notify_user_redeem_section').css('display','none');
		});
		$(document).on('click','#notify_user_redeem',function(){
			$('#notify_user_gain_tab').removeClass('active');
			$('#notify_user_redeem').addClass('active');
			$('#notify_user_earn_more_section').css('display','none');
			$('#notify_user_redeem_section').css('display','block');
		});

		/** Copy the referral Link via clipboard js **/
		var btns = document.querySelectorAll('button');
	    var clipboard = new Clipboard(btns);

	    $(".mwb_cpr_btn_copy").click(function(){
	    	$(".mwb_cpr_btn_copy").addClass("mwb_copied");
	    });

 	});

})( jQuery );
