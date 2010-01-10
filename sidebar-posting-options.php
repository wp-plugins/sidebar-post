<?php 
### Variables Variables Variables
$base_name = plugin_basename('sidebar-posting/sidebar-posting-options.php');
$base_page = 'admin.php?page='.$base_name;
$mode = trim($_GET['mode']);
$pagenavi_settings = array('pagenavi_options');
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo plugin_basename(__FILE__); ?>">
<input type="text" name="thank_you" id="thank_you" value="" />
<input type="submit" name="submit" value="Submit" />
</form>