<?php
	global $wpdb;
	
	//COMMON HEADER DISPLAY
	require_once(FLIPWALL_PLUGIN_PATH . '/views/javascript.php'); 
	require_once(FLIPWALL_PLUGIN_PATH . '/views/inc.header.php'); 
	$current_tab = isset($_REQUEST['tab']) ? esc_html($_REQUEST['tab']) : 'general';
?>

<style>

</style>

<div class="wrap">
	<!-- h2 required here for general system messages  -->
	<h2 style='display:none'></h2>
	
	<?php FLIPWALL_header(__("Settings", 'wpflipwall') ) ?>
	
	<h2 class="nav-tab-wrapper">  
		<a href="?page=flipwall-settings" class="nav-tab <?php echo ($current_tab == 'general') ? 'nav-tab-active' : '' ?>"> <?php _e('General', 'wpflipwall'); ?></a>  
		<a href="?page=flipwall-settings&tab=diagnostics" class="nav-tab <?php echo ($current_tab != 'general') ? 'nav-tab-active' : '' ?>"> <?php _e('Diagnostics', 'wpflipwall'); ?></a>  
	</h2> 	
	
	<?php
		switch ($current_tab) {
			case 'general':	include('general.php');	break;
			case 'diagnostics':	include('diagnostics.php');	break;
		}	
	?>
</div>
