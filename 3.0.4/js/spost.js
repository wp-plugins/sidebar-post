/*
	spost.js
	
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
	
	Copyright: (c) 2014 JANVIER Manishimwe http://www.janvierdesigns.com
*/

jQuery(document).ready(function($) {
function IsEmail(email) {
  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
}
	var AJAX_URL = $("#AjaxUrl").val(); 
	var user_email = $("#spost_poster_email").val();
	var spost_title = $("#spost_title").val();
	var spost_content = $("#spost_content").val();
	var spost_poster_name = $("#spost_poster_name").val();
	/************************************* AJAX AND FORM STUFF ******************************/
	$("#send_post").on( "click", function( event ) {
	event.preventDefault();		
	  console.log( $(  "#SidebarPost" ).serialize() ); //serialize form on client side
		if(spost_poster_name==''){
			$("#spost_poster_name").addClass('spostError');
 
		}else if(!IsEmail(user_email)){
			$("#spost_poster_email").addClass('spostError');
 
		}else if(spost_title==''){
			$("#spost_title").addClass('spostError');
 
		}else if(spost_content==''){
			$("#spost_content").addClass('spostError');
 
		}else{
		
	  var pdata = {
		 action: "spostPublish",
		 curret_user: $("#current_user_id").val(),
		 visitor_email: $("#spost_poster_email").val(),
		 visitor_name: $("#spost_poster_name").val(),
		 spost_title: $("#spost_title").val(),
		 spost_content: $("#spost_content").val(),
		 spost_category: $("#spost_category").val(),
	  }
	  $.post(AJAX_URL, pdata, function( data ) {
		$("#spostMessage").html(data).show("slow").delay(5000).hide("slow");
		//$("#SidebarPost").hide();
		
	  });				
				
	}

	  
	});
	/************************************* /AJAX AND FORM STUFF ******************************/
	
	/************************************* LOGIN/REGISTER SLIDING ******************************/
 
	/************************************* /LOGIN/REGISTER SLIDING  ****************************/
	
});