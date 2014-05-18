/*
	spost.js
	
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
	
	Copyright: (c) 2014 JANVIER Manishimwe http://www.janvierdesigns.com
*/

jQuery(document).ready(function($) {
	/************************************* AJAX AND FORM STUFF ******************************/
	$("#send_post").live( "click", function( event ) {
	var AJAX_URL = $("#AjaxUrl").val(); 
	  event.preventDefault();
	  console.log( $(  "#SidebarPost" ).serialize() ); //serialize form on client side
	  var pdata = {
		 action: "spostPublish",
		 curret_user: $("#current_user_id").val(),
		 spost_title: $("#spost_title").val(),
		 spost_content: $("#spost_content").val(),
		 spost_category: $("#spost_category").val()
	  }
	  $.post(AJAX_URL, pdata, function( data ) {
		$("#spostMessage").html(data).show("slow").delay(5000).hide("slow");
		$("#SidebarPost").hide();
		
	  });
	  
	});
	/************************************* /AJAX AND FORM STUFF ******************************/

});