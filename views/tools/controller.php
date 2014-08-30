<?php
	global $wpdb;
	
	//COMMON HEADER DISPLAY
	require_once(FLIPWALL_PLUGIN_PATH . '/views/javascript.php'); 
	require_once(FLIPWALL_PLUGIN_PATH . '/views/inc.header.php'); 
	$current_tab = isset($_REQUEST['tab']) ? esc_html($_REQUEST['tab']) : 'logging';
?>
<div class="wrap">
	<!-- h2 required here for general system messages  -->
	<h2 style='display:none'></h2>
	
	<?php FLIPWALL_header(__("Tools", 'wpflipwall') ) ?>
	
	<h2 class="nav-tab-wrapper">  
		<a href="?page=flipwall-tools" class="nav-tab <?php echo ($current_tab == 'logging') ? 'nav-tab-active' : '' ?>"> <?php _e('Logging', 'wpflipwall'); ?></a>  
		<a href="?page=flipwall-tools&tab=cleanup" class="nav-tab <?php echo ($current_tab != 'logging') ? 'nav-tab-active' : '' ?>"> <?php _e('Cleanup', 'wpflipwall'); ?></a>  
	</h2> 	
	
	<?php
		switch ($current_tab) {
			case 'logging':	include('logging.php');	break;
			case 'cleanup':	include('cleanup.php');	break;
		}	
	?>
</div>
