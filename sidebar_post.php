<?php
/*
Plugin Name: Sidebar Posting
Plugin URI: http://www.janvierdesigns.com
Description: This plugin places a posting form in the sidebar
Version: 1.1.0
Author: Janvier M @ JanvierDesigns
Author URI: http://www.janvierdesigns.com 


    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
function spost_loaded() {
	do_action( 'spost_loaded' );
}
add_action( 'plugins_loaded', 'spost_loaded', 20 );
global $shortname;
$shortname = "spost_";
define('SPOST_VERSION','1.1.0');
define('SPOST_DATABASE_VERSION','1.1.0');
define('SPOST_BUILD','05172014');
define('SOFTWARE_NAME','Sidebar Post');
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
}
 function spost_script_enqueue_js_css() {

	  wp_register_script( 'spost-validate', SPOST_PLUGIN_URL . '/js/validate.js');
	  wp_enqueue_script( 'spost-validate' );
  	wp_register_style( 'spost',  SPOST_PLUGIN_URL . '/css/spost.css',  array(), '', 'screen' );
    wp_enqueue_style( 'spost' );	
	 
	 }add_action('wp_enqueue_scripts', 'spost_script_enqueue_js_css'); 
add_action('wp_head', 'curPageName');

function sidebar_post_widget() { if ( is_user_logged_in() ){

 	global $current_user;
     			get_currentuserinfo();
				$user_level=$current_user->user_level . "\n";
				$user_id=$current_user->ID . "\n";
				$user_login=$current_user->user_login . "\n";
       			$user_email =$current_user->user_email . "\n";
				$user_firstname=$current_user->user_firstname . "\n";
				$user_lastname=$current_user->user_lastname . "\n";
				$display_name = $current_user->display_name . "\n";
			?>
<div id="wp_sidebarpost_main">
	<form action="" method="post" onsubmit="return validate_form(this)">
    <input type="hidden" name="submit-post" value="yes" />
	  <p>
	    <label><strong><?php _e('Yout Post Title','spost'); ?></strong><br>
	    <input name="title" type="text" style="width: 90%; background:#EFEFEF" value="">
	    </label>
</p>
	  <p>
	    <label> <strong><?php _e('Content of the post','spost'); ?> </strong><br>
	    <textarea name="content" style="width: 90%; background: #EFEFEF"></textarea>
		</label>
	    <br>
		<label><strong><?php _e('Category','spost'); ?></strong>
			<?php
				$select = wp_dropdown_categories('show_option_none=Select a category&show_count=1&orderby=name&echo=0');
				$select = preg_replace("#<select([^>]*)>#", "<select$1 >", $select);
				echo $select;
			?>
				
		</label>
		<input type="hidden" name="owner" value="<?php echo $user_id;?>"  />
	    <input type="submit" name="send_post" value="Send for review" style="width: 150px; height: 40px; background:#EFEFEF" />
        </p>	
	</form>


</div>
<?php }else{ 
$site_home = get_option('home');
echo '<div style="border:thick dotted #FF0000;"><form action="'.$site_home.'/wp-login.php" method="post">
<input type="hidden" name="redirect_to" value="'.curPageName().'" />
<input type="submit" style="background:url('.SPOST_PLUGIN_URL.'/images/not_logged_in.gif); width: 200px; height: 60px;" name="login" value="" />
</form>
</div>';} }

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

add_action('admin_menu', 'sidebar_posting_menu');

function sidebar_posting_menu() {
	add_options_page('SidebarPosting', 'Sidebar Post', 'manage_options', 'sidebar-posting', 'sidebar_post_fx');
}
function sidebar_post_fx(){}
?>