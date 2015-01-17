<?php
/*
Plugin Name: Sidebar Posting
Plugin URI: http://www.wpcoding.ca
Description: This plugin places a posting form in the sidebar
Version: 3.0.1
Author: Janvier M @ WpCoding .Ca
Author URI: http://www.wpcoding.ca 
Text Domain: spost
 
*/
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
function spost_loaded() {
	do_action( 'spost_loaded' );
}
add_action( 'plugins_loaded', 'spost_loaded', 20 );
global $shortname;
$shortname = "spost_";
define('SPOST_VERSION','3.0.1');
define('SPOST_DATABASE_VERSION','3.0.1');
define('SPOST_BUILD','01132015');
define('SOFTWARE_NAME',__('Sidebar Post','spost'));
define( 'SPOST_PLUGIN_DIR', WP_PLUGIN_DIR . '/sidebar-post' );
define( 'SPOST_PLUGIN_URL', WP_PLUGIN_URL . '/sidebar-post' );

$site_home = get_option('home');
function spost_loader_activate() {
	do_action( 'spost_loader_activate' );
}
register_activation_hook( 'sidebar-post/sidebar_post.php', 'spost_loader_activate' );

function update_db_options(){
	update_option("spost_db_version",SPOST_VERSION);
	update_option("spost_db_build",SPOST_BUILD);
}add_action("spost_loader_activate", "update_db_options");
add_action('init', 'spost_theme_setup');
function spost_theme_setup(){
    load_theme_textdomain('spost', SPOST_PLUGIN_URL . '/languages');
}
function curPageName() {
 return substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
}add_action('wp_head', 'curPageName');
function spost_dynamic_styles(){?>
	<style>
    #spostLogin{ background-color: <?php if(get_option('spost_login_button_background')==''){ echo '#3b8dbd';}else{ echo get_option('spost_login_button_background');}?>; 
	color: <?php if(get_option('spost_login_button_text_color')==''){ echo '#FFF';}else{ echo get_option('spost_login_button_text_color');}?>}
	#spostRegister{
	background-color: <?php if(get_option('spost_register_button_background')==''){ echo '#000';}else{ echo get_option('spost_register_button_background');}?>; 
		color: <?php if(get_option('spost_register_button_text_color')==''){ echo '#FFF';}else{ echo get_option('spost_register_button_text_color');}?>
		}
	#or{background-color: <?php if(get_option('spost_or_button_background')==''){ echo '#000';}else{ echo get_option('spost_or_button_background');}?>; 
		color: <?php if(get_option('spost_or_button_text_color')==''){ echo '#FFF';}else{ echo get_option('spost_or_button_text_color');}?>}
    </style>
<?php }add_action('wp_head','spost_dynamic_styles');
 function spost_script_enqueue_js_css() {
	wp_register_script( 'spost-validate', SPOST_PLUGIN_URL . '/js/validate.js');
	wp_register_script( 'spost', SPOST_PLUGIN_URL . '/js/spost.js');
	wp_enqueue_script( 'spost-validate' );
  /* $handle = 'jquery-ui-core';
   $list = 'registered';
     if (wp_script_is( $handle, $list )) {
       wp_enqueue_script( 'jquery-ui-core' );
     } else {
		wp_register_script( 'jquery-ui-core', get_bloginfo('home') . '/wp-includes/js/jquery/ui/jquery.ui.core.min.js'); 	
       wp_enqueue_script( 'jquery-ui-core' );
     }*/

	wp_enqueue_script( 'spost' );
  	wp_register_style( 'spost',  SPOST_PLUGIN_URL . '/css/spost.css',  array(), '', 'screen' );
    wp_enqueue_style( 'spost' );	
	 
	 }add_action('wp_enqueue_scripts', 'spost_script_enqueue_js_css'); 
function postingForm($thecase){
/*********************************************************************************************************************/
/**************************************** IF POSTING HAS BEEN DISABLED ***********************************************/
/*********************************************************************************************************************/

if($thecase=='none'){ echo '<div id="spostError">'.__('Posting is disabled by the site admin','spost').'</div>';}

/*********************************************************************************************************************/
/**************************************** else IF POSTING is for LOGGED IN USERS ONLY  *******************************/
/*********************************************************************************************************************/
elseif($thecase=='loggedin'){?>
<div id="wp_sidebarpost_main">
<div id="loginRegisterHidden"></div>
<div id="spostMessage"></div>
    <?php if(is_user_logged_in()): ?>

	<form action="" method="post" onsubmit="return validate_form(this)" id="SidebarPost">
    <input type="hidden" name="submit-post" value="yes" />
    <input type="hidden" name="current_user_id" id="current_user_id" value="<?php echo get_current_user_id(); ?>" />
    <input type="hidden" name="AjaxUrl" id="AjaxUrl" value="<?php bloginfo('home') ?>/wp-admin/admin-ajax.php" />
	<p>
    <?php $current_user = wp_get_current_user(); ?>
	    <label><strong><?php _e('Yout E-mail','spost'); ?></strong><br>
	    <input name="spost_poster_email" id="spost_poster_email" type="text" style="width: 90%; background: #E5E5E5" value="<?php echo $current_user->user_email; ?>" readonly>
	    </label>
    </p>
	<p>
	    <label><strong><?php _e('Yout Post Title','spost'); ?></strong><br>
	    <input name="title" id="spost_title" type="text" class="white" style="width: 90%; " value="">
	    </label>
    </p>
	  <p>
	    <label> <strong><?php _e('Content of the post','spost'); ?> </strong><br>
	    <textarea id="spost_content" name="spost_content" style="width: 90%;"></textarea>
		</label>
	    <br>
		<label><strong><?php _e('Category','spost'); ?></strong>
			<?php
			$taxonomy = get_option('spost_posting_taxonomy');
			if($taxonomy=='category' || $taxonomy=='category'){
$select = wp_dropdown_categories('show_option_none='.__('Select a category','spost').'&show_count=1&hide_empty=0&orderby=name&echo=0&id=spost_category');
			}else{
$select = wp_dropdown_categories('show_option_none='.__('Select a category','spost').'&show_count=1&hide_empty=0&orderby=name&echo=0&id=spost_category&taxonomy='.get_option('spost_posting_taxonomy'));
			}
				
				$select = preg_replace("#<select([^>]*)>#", "<select$1 >", $select);
				echo $select;
			?>	
		</label><p>
		<input type="hidden" name="owner" value="<?php echo $user_id;?>"  /></p>
	    <input type="submit" name="send_post"  id="send_post" value="Send for review" style="width: 150px; height: 40px; background:#EFEFEF" />
        </p>	
	</form>
    <?php else: ?>
	<div class="mustLog">
		<button name="spostLogin" id="spostLogin" onclick="location.href='<?php echo get_bloginfo('home'); ?>/wp-login.php'"><?php _e('Login','spost'); ?></button>
    </div><div class="clearall"></div>
	<div class="separatorHorizontal">
		<div id="or"><?php _e('Or','spost'); ?></div>
    </div><div class="clearall"></div>
	<div class="mustLogRegister">
		<button name="spostRegister" id="spostRegister" onclick="location.href='<?php echo get_bloginfo('home'); ?>/wp-login.php?action=register'"><?php _e('Register','spost'); ?></button>
    </div><div class="clearall"></div>
    <?php endif; ?>
</div>
<?php
}
/*********************************************************************************************************************/
/**************************************** IF POSTING IS ALLOWED FOR ALL***********************************************/
/*********************************************************************************************************************/
elseif($thecase=='everybody'){ ?>
<div id="wp_sidebarpost_main">
<div id="loginRegisterHidden"></div>
<div id="spostMessage"></div>
    <?php if(is_user_logged_in()): ?>

	<form action="" method="post" onsubmit="return validate_form(this)" id="SidebarPost">
    <input type="hidden" name="submit-post" value="yes" />
    <input type="hidden" name="current_user_id" id="current_user_id" value="<?php echo get_current_user_id(); ?>" />
    <input type="hidden" name="AjaxUrl" id="AjaxUrl" value="<?php bloginfo('home') ?>/wp-admin/admin-ajax.php" />
	<p>
    <?php $current_user = wp_get_current_user(); ?>
	    <label><strong><?php _e('Yout E-mail','spost'); ?></strong><br>
	    <input name="spost_poster_email" id="spost_poster_email" type="text" style="width: 90%; background: #E5E5E5" value="<?php echo $current_user->user_email; ?>" readonly>
	    </label>
    </p>
	<p>
	    <label><strong><?php _e('Yout Post Title','spost'); ?></strong><br>
	    <input name="title" id="spost_title" type="text" class="white" style="width: 90%; " value="">
	    </label>
    </p>
	  <p>
	    <label> <strong><?php _e('Content of the post','spost'); ?> </strong><br>
	    <textarea id="spost_content" name="spost_content" style="width: 90%;"></textarea>
		</label>
	    <br>
		<label><strong><?php _e('Category','spost'); ?></strong>
			<?php
			$taxonomy = get_option('spost_posting_taxonomy');
			if($taxonomy=='category' || $taxonomy==''){
$select = wp_dropdown_categories('show_option_none='.__('Select a category','spost').'&show_count=1&hide_empty=0&orderby=name&echo=0&id=spost_category');
			}else{
$select = wp_dropdown_categories('show_option_none='.__('Select a category','spost').'&show_count=1&hide_empty=0&orderby=name&echo=0&id=spost_category&taxonomy='.get_option('spost_posting_taxonomy'));
			}
				
				$select = preg_replace("#<select([^>]*)>#", "<select$1 >", $select);
				echo $select;
			?>	
		</label><p>
		<input type="hidden" name="owner" value="<?php echo $user_id;?>"  /></p>
	    <input type="submit" name="send_post"  id="send_post" value="Send for review" style="width: 150px; height: 40px; background:#EFEFEF" />
        </p>	
	</form>
    <?php else: ?>
	<form action="" method="post" onsubmit="return validate_form(this)" id="SidebarPost">
    <input type="hidden" name="submit-post" value="yes" />
    <input type="hidden" name="current_user_id" id="current_user_id" value="<?php echo get_current_user_id(); ?>" />
    <input type="hidden" name="AjaxUrl" id="AjaxUrl" value="<?php bloginfo('home') ?>/wp-admin/admin-ajax.php" />

    

     <p>
	    <label><strong><?php _e('Your Name','spost'); ?></strong><br>
	    <input name="spost_poster_name" id="spost_poster_name" type="text" style="width: 90%; background:#EFEFEF" value="">
	    </label>
     </p>
	 <p>
	    <label><strong><?php _e('Your email','spost'); ?></strong><br>
	    <input name="spost_poster_email" id="spost_poster_email" type="text" style="width: 90%; background:#EFEFEF" value="">
	    </label>
     </p>
	 <p>
	    <label><strong><?php _e('Your Post Title','spost'); ?></strong><br>
	    <input name="title" id="spost_title" type="text" style="width: 90%; background:#EFEFEF" value="">
	    </label>
     </p>
	  <p>
	    <label> <strong><?php _e('Content of the post','spost'); ?> </strong><br>
	    <textarea id="spost_content" name="spost_content" rows="10" style="width: 90%; background: #EFEFEF"></textarea>
		</label>
	    <br>
		<label><strong><?php _e('Category','spost'); ?></strong>
			<?php
			$taxonomy = get_option('spost_posting_taxonomy');
			if($taxonomy=='category' || $taxonomy==''){
$select = wp_dropdown_categories('show_option_none='.__('Select a category','spost').'&show_count=1&hide_empty=0&orderby=name&echo=0&id=spost_category');
			}else{
$select = wp_dropdown_categories('show_option_none='.__('Select a category','spost').'&show_count=1&hide_empty=0&orderby=name&echo=0&id=spost_category&taxonomy='.get_option('spost_posting_taxonomy'));
			}
				
				$select = preg_replace("#<select([^>]*)>#", "<select$1 >", $select);
				echo $select;
			?>	
		</label><p>
		<input type="hidden" name="owner" value="<?php echo $user_id;?>"  /></p>
	    <input type="submit" name="send_post"  id="send_post" value="Send for review" style="width: 150px; height: 40px; background:#EFEFEF" />
        </p>	
	</form>
    <div id="spostMessage"></div>
    <?php endif; ?>
</div>
<?php 
}

 }
function sidebar_post_widget() {
	$allowed_level = get_option('spost_allowed_level');
	
	if($allowed_level=='everybody' || $allowed_level=='' ){
		postingForm('everybody');
	}
	elseif($allowed_level=='loggedin' || $allowed_level=='' ){
		 postingForm('loggedin');
	}
	elseif($allowed_level=='none' || $allowed_level=='' ){
		postingForm('none');
	}
}
class wp_sidebar_post_plugin extends WP_Widget {

	// constructor
	function wp_sidebar_post_plugin() {
		/* ... */
		parent::WP_Widget(false, $name = __('Sidebar Post Widget', 'spost') );
	}

	// widget form creation
	function form($instance) {
	
	// Check values
	if( $instance) {
		 $title = esc_attr($instance['title']);
		 //$text = esc_attr($instance['text']);
		 $textarea = esc_textarea($instance['textarea']);
	} else {
		 $title = '';
		 //$text = '';
		 $textarea = '';
	}
	?>
	
	<p>
	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title', 'spost'); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
	</p>
	
	<!--<p>
	<label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Text:', 'spost'); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>" type="text" value="<?php echo $text; ?>" />
	</p>-->
	
	<p>
	<label for="<?php echo $this->get_field_id('textarea'); ?>"><?php _e('Posting Instructions:', 'spost'); ?></label>
	<textarea class="widefat" id="<?php echo $this->get_field_id('textarea'); ?>" name="<?php echo $this->get_field_name('textarea'); ?>"><?php echo $textarea; ?></textarea>
	</p>
	<?php
	}

	// update widget
	function update($new_instance, $old_instance) {
		  $instance = $old_instance;
		  // Fields
		  $instance['title'] = strip_tags($new_instance['title']);
		  $instance['text'] = strip_tags($new_instance['text']);
		  $instance['textarea'] = $new_instance['textarea'];
		 return $instance;
	}

	// display widget
	function widget($args, $instance) {
	   extract( $args );
	   // these are the widget options
	   $title = apply_filters('widget_title', $instance['title']);
	   $text = $instance['text'];
	   $textarea = $instance['textarea'];
	   echo $before_widget;
	   // Display the widget
	   echo '<div class="widget-text spost_box">';
	
	   // Check if title is set
	   if ( $title ) {
		  echo $before_title . $title . $after_title;
	   }
	
	   // Check if text is set
	   if( $text ) {
		  echo '<p class="spost_text">'.$text.'</p>';
	   }
	   // Check if textarea is set
	   if( $textarea ) {
		 echo '<p class="spost_textarea">'.$textarea.'</p>';
	   }
	   sidebar_post_widget();
	   echo '</div>';
	   echo $after_widget;
	}
}

// register widget
add_action('widgets_init', create_function('', 'return register_widget("wp_sidebar_post_plugin");'));

/*add_action('admin_menu', 'sidebar_posting_menu');

function sidebar_posting_menu() {
	add_options_page('SidebarPosting', 'Sidebar Post', 'manage_options', 'sidebar-posting', 'sidebar_post_fx');
}*/
function spost_admin_actions() {
  
    $one = add_menu_page(__('Sidebar Post','spost'),__('Sidebar Post','spost'),'switch_themes', "spost", "spost_admin_fx", SPOST_PLUGIN_URL.'/images/icon.png', '5.4');
	$mainsettings = add_submenu_page('spost', __('Main Settings','spost'), __('Main Settings','spost'), 'switch_themes', 'spost-main-settings', 'spost_main_options' );
	$loginattemps = add_submenu_page('spost', __('Documentation','spost'), __('Documentation','spost'), 'switch_themes', 'spost-documentation', 'spost_documentation_fx' );
  	
add_action( 'admin_print_styles-' . $one, 'spost_plugin_enqueue_scripts' );
add_action( 'admin_print_styles-' . $mainsettings, 'spost_plugin_enqueue_scripts' );
add_action( 'admin_print_styles-' . $loginattemps, 'spost_plugin_enqueue_scripts' );  

add_action( 'admin_head-'. $one, 'spost_header_admin' );
add_action( 'admin_head-'. $mainsettings, 'spost_header_admin' );
add_action( 'admin_head-'. $loginattemps, 'spost_header_admin' );

}add_action('admin_menu', 'spost_admin_actions'); 

function spost_plugin_enqueue_scripts( $hook_suffix ) {
	wp_enqueue_style( 'spost-admin-style', SPOST_PLUGIN_URL . '/css/spost-admin.css', false, SPOST_VERSION );
	wp_enqueue_script( 'spost-admin-js', SPOST_PLUGIN_URL . '/js/spost-admin.js', false, SPOST_VERSION );	
	wp_enqueue_script( 'spost-colorPicker-js',SPOST_PLUGIN_URL . '/js/color-picker.js', array( 'wp-color-picker' ), false, true);	
	  // first check that $hook_suffix is appropriate for your admin page
    wp_enqueue_style( 'wp-color-picker' );
}
function spost_admin_fx(){?>
<div id="spostContainer">
<form id="spostForm1" enctype="multipart/form-data" method="post" action="">
	<div class="spostRow" id="theHeader">
    	<h1><?php _e('Sidebar Post - Main Settings','spost'); ?></h1> 
    </div><div class="clearall"></div>
	<div class="spostRow" data-spostid="spost_allowed_level">
    	<div class="inputTitle"><?php _e('Allow Posting','spost'); ?>?</div>
        <div class="inputContent">
        <select id="spost_allowed_level" name="spost_allowed_level">
        	<option value="everybody" <?php if(get_option('spost_allowed_level')=='everybody'){echo 'selected="selected"';} ?>><?php _e('Everybody','spost'); ?></option>
            <option value="loggedin" <?php if(get_option('spost_allowed_level')=='loggedin'){echo 'selected="selected"';} ?>><?php _e('Only logged in','spost'); ?></option>
            <option value="none" <?php if(get_option('spost_allowed_level')=='none'){echo 'selected="selected"';} ?>><?php _e('Do NOT allow','spost'); ?></option>
        </select></div>
        <div class="spostLegend"></div><div class="clearall"></div>
    </div><div class="clearall"></div>
	<div class="spostRow" data-spostid="spost_require_email">
    	<div class="inputTitle"><?php _e('Require Email to post','spost'); ?></div>
        <div class="inputContent">
        <div class="<?php if(get_option('spost_require_email')=='1' || get_option('spost_require_email')==''){_e('yes','spost');}else{_e('no','spost');} ?>" data-toggleyesno="spost_require_email"><?php if(get_option('spost_require_email')=='1' || get_option('spost_require_email')==''){_e('Yes','spost');}else{_e('No','spost');} ?></div>
        <input type="hidden" name="spost_require_email" id="spost_require_email" value="<?php echo get_option('spost_require_email'); ?>" />
        </div>
        <div class="spostLegend"></div><div class="clearall"></div>
    </div><div class="clearall"></div>
	<div class="spostRow" data-spostid="spost_posting_posttype">
    	<div class="inputTitle"><?php _e('Target Post type','spost'); ?></div>
        <div class="inputContent">
        <select name="spost_posting_posttype" id="spost_posting_posttype">
        <?php
			$post_types = get_post_types( '', 'names' ); 
			
			foreach ( $post_types as $post_type ) {
			if(get_option('spost_posting_posttype')==$post_type){
			   echo '<option value="' . $post_type . '" selected="selected">' . $post_type . '</option>';
				}else{
			   echo '<option value="' . $post_type . '">' . $post_type . '</option>';
				}
			}

		 ?>
         </select>
        </div>
        <div class="spostLegend"></div><div class="clearall"></div>
    </div><div class="clearall"></div>
	<div class="spostRow" data-spostid="spost_posting_taxonomy">
    	<div class="inputTitle"><?php _e('Target Taxonomy','spost'); ?></div>
        <div class="inputContent">
        <select name="spost_posting_taxonomy" id="spost_posting_taxonomy">
			<?php 
            $taxonomies = get_taxonomies(); 
            foreach ( $taxonomies as $taxonomy ) {
			if(get_option('spost_posting_taxonomy')==$taxonomy){
			   echo '<option value="' . $post_type . '" selected="selected">' . $taxonomy . '</option>';
				}else{
			   echo '<option value="' . $taxonomy . '">' . $taxonomy . '</option>';
				}
            }
            ?>
        </select>
        </div>
        <div class="spostLegend"></div><div class="clearall"></div>
    </div><div class="clearall"></div>
<div class="spostRow" data-spostid="spost_posting_status">
    	<div class="inputTitle"><?php _e('Default Post Status','spost'); ?></div>
        <div class="inputContent">
        <select name="spost_posting_status" id="spost_posting_status">
			<option value="draft" <?php if(get_option('spost_posting_status')=='draft'){echo 'selected="selected"';} ?>><?php _e('Draft','spost'); ?></option>
            <option value="publish" <?php if(get_option('spost_posting_status')=='publish'){echo 'selected="selected"';} ?>><?php _e('Publish','spost'); ?></option>
            
        </select>
        </div>
        <div class="spostLegend"></div><div class="clearall"></div>
    </div><div class="clearall"></div>
<div class="spostRow" data-spostid="spost_posting_taxonomy">
    <input type="submit" name="save_settings" id="save_settings" style="margin-left:auto; margin-right:auto; border: none; background:#333; color: #FFF; border-radius: 15px; padding:5px 25px; cursor: pointer;" value="<?php _e('Save Settings','spost'); ?>"/>	 <div id="spostMessages"></div>
</div><div class="clearall"></div>
</form>

</div><div class="clearall"></div>
<?php }
function spost_header_admin(){?>
<script type="text/javascript">
jQuery(document).ready(function($) {
	/************************************* AJAX AND FORM STUFF ******************************/
function toggle_yesno(){
	var yesText = '<?php _e('Yes','spost'); ?>';
	var noText = '<?php _e('No','spost'); ?>';
	$('.inputContent div').on("click", function(event){
		var theElement = $(this).closest( ".spostRow" ).attr( "data-spostid" );
		var yesno = $(this).attr("class");
		if(yesno=='yes'){
			$(this).removeClass('yes');
			$(this).addClass('no');
			$('#'+theElement).val('0');
			$(this).html(noText);
			
		}else if(yesno=='no'){
			$(this).removeClass('no');
			$(this).addClass('yes');
			$('#'+theElement).val('1');
			$(this).html(yesText);
		}
		
	})
}
toggle_yesno();

	$('#save_settings').on("click",function(event){
		event.preventDefault();
 
	  console.log( $(  "#spostForm1" ).serialize() ); //serialize form on client side
	  var pdata = {
		 action: "spostSaveSettings",
		 postdata: $("#spostForm1").serialize(),
	  }
	  $.post(ajaxurl , pdata, function( response ) {
		//$("#spostMessage").html(data).show("slow").delay(5000).hide("slow");
		//$("#SidebarPost").hide();
		 var obj = JSON.parse(response);
			if(obj.updatestatus=='success'){
				$('#spostMessages').fadeIn().delay(5000).hide("slow");;
				$('#spostMessages').html(obj.updatecontext);
				$('#spostMessages').addClass('success');
			}else{
				$('#spostMessages').fadeIn().delay(5000).hide("slow");;
				$('#spostMessages').html(obj.updatecontext);
				$('#spostMessages').addClass('failure');
			}
	  });
	  
	}) 
 $('#spost_custom_login_page option').attr('disabled','disabled');
 $('#spost_custom_login_page').attr('disabled','disabled');
	/************************************* /AJAX AND FORM STUFF ******************************/

});
</script>
<?php }
function sidebar_post_fx(){
	
}
function spost_main_options(){?>
<div id="spostContainer">
<form id="spostForm1" enctype="multipart/form-data" method="post" action="">
	<div class="spostRow" id="theHeader">
    	<h1><?php _e('Sidebar Post - Admin Main Settings','spost'); ?></h1>
        <div class="clearall"></div>
    </div><div class="clearall"></div>
	<div class="spostRow" data-spostid="spost_admin_email">
    	<div class="inputTitle"><?php _e('Admin Notification email','spost'); ?>?</div>
        <div class="inputContent">
        <input type="text" name="spost_admin_email" id="spost_admin_email" value="<?php if(get_option('spost_admin_email')==''){echo get_option('admin_email');}else{ echo get_option('spost_admin_email');} ?>" /></div>
        <div class="spostLegend"><?php _e('The email address to be notified when posts are created','spost'); ?></div><div class="clearall"></div>
    </div><div class="clearall"></div>
	<div class="spostRow" data-spostid="spost_send_poster_email">
    	<div class="inputTitle"><?php _e('Send an email to the poster','spost'); ?></div>
        <div class="inputContent">
        <div class="<?php if(get_option('spost_send_poster_email')=='1' || get_option('spost_send_poster_email')==''){_e('yes','spost');}else{_e('no','spost');} ?>" data-toggleyesno="spost_send_poster_email"><?php if(get_option('spost_send_poster_email')=='1' || get_option('spost_send_poster_email')==''){_e('Yes','spost');}else{_e('No','spost');} ?></div>
        <input type="hidden" name="spost_send_poster_email" id="spost_send_poster_email" value="<?php echo get_option('spost_send_poster_email'); ?>" />
        </div>
        <div class="spostLegend"></div><div class="clearall"></div>
    </div><div class="clearall"></div>
	<div class="spostRow premium" data-spostid="spost_custom_login_page">
    	<div class="inputTitle"><?php _e('Advanced Login/Register','spost'); ?></div>
		<div class="inputContent">
        <?php wp_dropdown_pages(array(
				'name'                  => 'spost_custom_login_page',
				'show_option_none'      => '- - - '.__('Select One','spost').' - - - ', // string
				'selected'              => get_option('spost_custom_login_page'),
				'depth'                 => 0,
				'id'                    => 'spost_custom_login_page', // string
		
		)); ?>
        </div>
        <div class="spostLegend"></div><div class="clearall"></div>
    </div><div class="clearall"></div>
	<div class="spostRow premium" data-spostid="spost_interval_between_posts">
    	<div class="inputTitle"><?php _e('Interval between Posts','spost'); ?></div>
        <div class="inputContent">
        <select name="spost_interval_between_posts" id="spost_interval_between_posts" disabled="disabled">
			<?php 
            $i=0;
			$intervals = array('1'=>'5','2'=>'10','3'=>'15','4'=>'20','5'=>'25','6'=>'30','7'=>'35','8'=>'40','9'=>'45','10'=>'50','11'=>'55');
            foreach ( $intervals as $key=>$value ) {
			if(get_option('spost_interval_between_posts')==$value){
			   echo '<option value="' . $value . '" selected="selected">' . $value . '</option>';
				}else{
			   echo '<option value="' . $value . '">' . $value . '</option>';
				}
			$i++;
            }
            ?>
        </select>
        </div>
        <div class="spostLegend"><?php _e('The amount of time between a user is allowed to post again, in minutes','spost'); ?></div><div class="clearall"></div>
    </div><div class="clearall"></div>
	<div class="spostRow" data-spostid="spost_log_user_ip">
    	<div class="inputTitle"><?php _e('Log user IP','spost'); ?></div>
        <div class="inputContent">
        <div class="<?php if(get_option('spost_log_user_ip')=='1' || get_option('spost_log_user_ip')==''){_e('yes','spost');}else{_e('no','spost');} ?>" data-toggleyesno="spost_log_user_ip"><?php if(get_option('spost_log_user_ip')=='1' || get_option('spost_log_user_ip')==''){_e('Yes','spost');}else{_e('No','spost');} ?></div>
        <input type="hidden" name="spost_log_user_ip" id="spost_log_user_ip" value="<?php echo get_option('spost_log_user_ip'); ?>" />
        </div>
        <div class="spostLegend"><?PHP _e('Whether or not to log the user&apos;s IP ddress',''); ?></div><div class="clearall"></div>
    </div><div class="clearall"></div>
    <div class="spostRow" data-spostid="spost_login_button_background">
            <div class="inputTitle"><?php _e('Login Button Background','spost'); ?></div>
            <div class="inputContent"> 
			<input type="text" value="<?php echo get_option('spost_login_button_background'); ?>" name="spost_login_button_background" class="spost_login_button_background" data-default-color="<?php echo get_option('spost_login_button_background'); ?>" />
            </div>
            <div class="spostLegend"><?PHP _e('The background color of the Login button','spost'); ?></div><div class="clearall">		    </div>
        </div><div class="clearall"></div>
    <div class="spostRow" data-spostid="spost_login_button_text_color">
            <div class="inputTitle"><?php _e('Login Button Text Color','spost'); ?></div>
            <div class="inputContent">
            <input type="text" value="<?php echo get_option('spost_login_button_text_color'); ?>" name="spost_login_button_text_color" class="spost_login_button_text_color" data-default-color="<?php echo get_option('spost_login_button_text_color'); ?>" />
            </div>
            <div class="spostLegend"><?PHP _e('The text color of the Login button','spost'); ?></div><div class="clearall"></div>
     </div><div class="clearall"></div>
	<div class="spostRow" data-spostid="spost_register_button_background">
            <div class="inputTitle"><?php _e('Register Button Background','spost'); ?></div>
            <div class="inputContent"> 
			<input type="text" value="<?php echo get_option('spost_register_button_background'); ?>" name="spost_register_button_background" class="spost_register_button_background" data-default-color="<?php echo get_option('spost_register_button_background'); ?>" />
            </div>
            <div class="spostLegend"><?PHP _e('The background color of the Register button','spost'); ?></div>
            <div class="clearall"></div>
        </div><div class="clearall"></div>
    <div class="spostRow" data-spostid="spost_register_button_text_color">
            <div class="inputTitle"><?php _e('Register Button Text Color','spost'); ?></div>
            <div class="inputContent">
            <input type="text" value="<?php echo get_option('spost_register_button_text_color'); ?>" name="spost_register_button_text_color" class="spost_register_button_text_color" data-default-color="<?php echo get_option('spost_register_button_text_color'); ?>" />
            </div>
            <div class="spostLegend"><?PHP _e('The text color of the Register button','spost'); ?></div>
            <div class="clearall"></div>
     </div><div class="clearall"></div>
	<div class="spostRow" data-spostid="spost_or_button_background">
            <div class="inputTitle"><?php _e('OR separator Button Background','spost'); ?></div>
            <div class="inputContent"> 
			<input type="text" value="<?php echo get_option('spost_or_button_background'); ?>" name="spost_or_button_background" class="spost_or_button_background" data-default-color="<?php echo get_option('spost_or_button_background'); ?>" />
            </div>
            <div class="spostLegend"><?PHP _e('The background color of the Or button','spost'); ?></div>
            <div class="clearall"></div>
        </div><div class="clearall"></div>
    <div class="spostRow" data-spostid="spost_or_button_text_color">
            <div class="inputTitle"><?php _e('Or Button Text Color','spost'); ?></div>
            <div class="inputContent">
            <input type="text" value="<?php echo get_option('spost_or_button_text_color'); ?>" name="spost_or_button_text_color" class="spost_or_button_text_color" data-default-color="<?php echo get_option('spost_or_button_text_color'); ?>" />
            </div>
            <div class="spostLegend"><?PHP _e('The text color of the Or button','spost'); ?></div>
            <div class="clearall"></div>
     </div><div class="clearall"></div>
<div class="spostRow" data-spostid="spost_posting_taxonomy">
    <input type="submit" name="save_settings" id="save_settings" style="margin-left:auto; margin-right:auto; border: none; background:#333; color: #FFF; border-radius: 15px; padding:5px 25px; cursor: pointer;" value="<?php _e('Save Settings','spost'); ?>"/>	 <div id="spostMessages"></div>
</div><div class="clearall"></div>
</form>

</div><div class="clearall"></div><?php }
function spost_documentation_fx(){?>
<div id="spostContainer">
<form id="spostForm1" enctype="multipart/form-data" method="post" action="">
	<div class="spostRow" id="theHeader">
    	<h1><?php _e('Sidebar Post - Documentation','spost'); ?></h1> 
    </div><div class="clearall"></div>
	<div class="spostRow" data-spostid="spost_allowed_level">
    	<p><h2><?php _e('Introduction','spost'); ?></h2></p>
    	<p><?php _e('We are thrilled that you installed our Plugin.','spost'); ?></p>
        <p><?php _e('We hope is meets your needs and boosts our website or blog to another user engagement level','spost'); ?></p>
        <p><?php _e('As you will see, this documentation is just in its infancy, but we will continue to improve upon it as we go.','spost'); ?></p>
        <p><?php _e('You can also visit the plugin page if you have any question at ','spost'); ?><a href="http://www.wpcoding.ca/forums/forum/plugins/sidebar-post/">http://www.wpcoding.ca/forums/forum/plugins/sidebar-post/</a></p>
        <p><h2><?php _e('Usage','spost'); ?></h2></p>
        <p><?php _e('We made this plugin as simple to use as it could be, by making it into a widget, and also providing a shortcode you can use to insert in pages and posts on your website. ','spost'); ?></p>
        <p><?php _e('Simply go to the widget area, and add the <b>`Sidebar Post Widget`</b> whereyou want it to appear','spost'); ?></p>
        <p><?php echo __('Alternatively, you can insert the <b>[sidebarpost]</b> shortcode in a page or post','spost'); ?>
    </div><div class="clearall"></div>
</form>
</div>
<?php }
function create_spost_ajax(){
	$cat_= $_POST['spost_category'];
	$owner = $_POST['current_user_id'];
	$title = $_POST['spost_title'];
	$content = $_POST['spost_content'];
	  
	   // Create post object
  $sPost = array();
  $sPost['post_title'] = $title;
  $sPost['post_content'] = $content;
  $sPost['post_status'] = get_option('spost_posting_status');
  $sPost['post_author'] = $owner;
  $sPost['post_type'] = get_option('spost_posting_posttype');
  $sPost['post_category'] = array($cat_);
  
  if(is_user_logged_in()){
// Insert the post into the database
	$successful_post = wp_insert_post($sPost);	
	  if($successful_post){
		  _e('Thank you for submitting your post. It will be reviewed and once approved, published','');
$spost_admin_email = get_option('spost_admin_email'); 

if($spost_admin_email==''){$admin_email =  get_option('admin_email');}else{$admin_email = $spost_admin_email;}
add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));
wp_mail($admin_email, __('New Post created on website','spost'), '<p>A New post created. '.'<p>Please moderate...<p><a href="'.get_option('home').'/wp-admin/post.php?post='.$successful_post.'&action=edit">'.get_option('home').'/wp-admin/post.php?post='.$successful_post.'&action=edit</a>');

// Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
remove_filter( 'wp_mail_content_type', 'set_html_content_type' );

  }
}
	die();
	
}
add_action('wp_ajax_spostPublish', 'create_spost_ajax');
add_action('wp_ajax_nopriv_spostPublish', 'create_spost_ajax');//for users that are not logged in.
//[foobar]
function spostShortened( $atts ){
	return sidebar_post_widget();
}
add_shortcode( 'sidebarpost', 'spostShortened' );
function save_spost_settings(){
$myData = $_POST['postdata']; //The Data we get  from  jQuery (querystring)
$responsible = get_current_user_id(); // We get the user who is submitting the form NOT from the array
$arr = array(); 
parse_str( $myData, $arr); //We parse it into an associative array
if(current_user_can('administrator','update_plugins')){
	foreach ($arr as $key => $value){
				update_option($key,$value); 
			}
$settingsStuff = array('updatestatus'=>'success','updatecontext'=>__('Setting Saved successfully','msregister'));	
}else{
$settingsStuff = array('updatestatus'=>'error','updatecontext'=>__('There was an error','msregister'));	
}
	echo json_encode($settingsStuff);
	
	die();
}
add_action('wp_ajax_spostSaveSettings', 'save_spost_settings');

?>