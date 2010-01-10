<?php
/*
Plugin Name: Sidebar Posting
Plugin URI: http://www.janvierdesigns.com
Description: This plugin places a posting form in the sidebar
Version: 1.0
Author: Janvier M @ JanvierDesigns
Author URI: http://www.janvierdesigns.com 
*/

/*  Copyright 2009  JANVIER DESIGNS .COM  (email : janvier@janvierdesigns.com)

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
$site_home = get_option('home');
?>
<?php
function curPageName() {
 return substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
}

?>
<?php function add_validator_js(){
echo "<script type='text/javascript' src='".get_option('home')."/wp-content/plugins/sidebar-posting/js/validate.js'></script>";
}
add_action('wp_head', 'add_validator_js');
add_action('wp_head', 'curPageName');
?>
<?php 
function ishimwe_inkuru_widget() { if ( is_user_logged_in() ){?>
		<?php  	global $current_user;
     			get_currentuserinfo();
				$user_level=$current_user->user_level . "\n";
				$user_id=$current_user->ID . "\n";
				$user_login=$current_user->user_login . "\n";
       			$user_email =$current_user->user_email . "\n";
				$user_firstname=$current_user->user_firstname . "\n";
				$user_lastname=$current_user->user_lastname . "\n";
				$display_name = $current_user->display_name . "\n";
			?>
<div style="border:thick dotted #A8E500; background: url(<?php echo get_option('home'); ?>/wp-content/plugins/sidebar-posting/images/arrow_form.jpg) no-repeat left">
	<form action="<?php echo $site_home;?>/wp-content/plugins/sidebar-posting/post_post.php" method="post" onsubmit="return validate_form(this)">
	  <p>
	    <label><strong>Title</strong><br>
	    <input name="title" type="text" style="width: 90%; background:#EFEFEF" value="">
	    </label>
</p>
	  <p>
	    <label> <strong>Content of the post </strong><br>
	    <textarea name="content" style="width: 90%; background: #EFEFEF"></textarea>
		</label>
	    <br>
		<label><strong>Category</strong>
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
<input type="submit" style="background:url('.$site_home.'/wp-content/plugins/sidebar-posting/images/not_logged_in.gif); width: 200px; height: 60px;" name="login" value="" />
</form>
</div>';} } ?>
<?php 
function widget_sidebar_post_widget($args) {
  extract($args);
  echo $before_widget;
  echo $before_title;?>New Post<?php echo $after_title;
  ishimwe_inkuru_widget();
  echo $after_widget;
}

function sidebar_post_widget_init()
{
  register_sidebar_widget(__('NEW POST'), 'widget_sidebar_post_widget');    
}
add_action("plugins_loaded", "sidebar_post_widget_init");
?>
<?php
### Function: Page Navigation Option Menu
add_action('admin_menu', 'sidebar_posting');
function sidebar_posting() {
	if (function_exists('add_options_page')) {
		add_options_page(__('SidebarPosting', 'sidebar-post'), __('SidebarPosting', 'sidebar-post'), 'manage_options', 'sidebar-posting/sidebar-posting-options.php') ;
	}
}
?>
