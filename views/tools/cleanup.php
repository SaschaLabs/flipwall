<?php
	require_once(FLIPWALL_PLUGIN_PATH . '/views/javascript.php'); 
	require_once(FLIPWALL_PLUGIN_PATH . '/views/inc.header.php'); 

	$_GET['action'] = isset($_GET['action']) ? $_GET['action'] : 'display';
	switch ($_GET['action']) {
		case 'installer' : 	
			$action_response = __('Installer File Cleanup Ran.', 'wpflipwall');		
			break;		
		case 'legacy': 
			SLFW_Settings::LegacyClean();			
			$action_response = __('Legacy data removed.', 'wpflipwall');
			break;
		case 'tmp-cache': 
			SLFW_Package::TmpCleanup(true);
			$action_response = __('Build cache removed.', 'wpflipwall');
			break;		
	} 
	
?>

<style type="text/css">
	div.success {color:#4A8254}
	div.failed {color:red}
	table.dup-reset-opts td:first-child {font-weight: bold}
	table.dup-reset-opts td {padding:4px}
	form#dup-settings-form {padding: 0px 10px 0px 10px}
</style>


<form id="dup-settings-form" action="?page=flipwall-tools&tab=cleanup" method="post">
	<?php wp_nonce_field( 'FLIPWALL_cleanup_page' ); ?>
	
	<?php if ($_GET['action'] != 'display')  :	?>
		<div id="message" class="updated below-h2">
			<p><?php echo $action_response; ?></p>
			<?php if ( $_GET['action'] == 'installer') :  ?>
			
			<?php	
				$html = "";
				$installer_file 	= FLIPWALL_WPROOTPATH . FLIPWALL_INSTALL_PHP;
				$installer_bak		= FLIPWALL_WPROOTPATH . FLIPWALL_INSTALL_BAK;
				$installer_sql  	= FLIPWALL_WPROOTPATH . FLIPWALL_INSTALL_SQL;
				$installer_log  	= FLIPWALL_WPROOTPATH . FLIPWALL_INSTALL_LOG;
				$package_name   	= (isset($_GET['package'])) ? FLIPWALL_WPROOTPATH . esc_html($_GET['package']) : '';
				
				$html .= (@unlink($installer_file)) ?  "<div class='success'>Successfully removed {$installer_file}</div>"	:  "<div class='failed'>Does not exsist or unable to remove file: {$installer_file}</div>";
				$html .= (@unlink($installer_bak))  ?  "<div class='success'>Successfully removed {$installer_bak}</div>"	:  "<div class='failed'>Does not exsist or unable to remove file: {$installer_bak}</div>";
				$html .= (@unlink($installer_sql))  ?  "<div class='success'>Successfully removed {$installer_sql}</div>"  	:  "<div class='failed'>Does not exsist or unable to remove file: {$installer_sql}</div>";
				$html .= (@unlink($installer_log))  ?  "<div class='success'>Successfully removed {$installer_log}</div>"	:  "<div class='failed'>Does not exsist or unable to remove file: {$installer_log}</div>";

				$path_parts = pathinfo($package_name);
				$path_parts = (isset($path_parts['extension'])) ? $path_parts['extension'] : '';
				if ($path_parts  == "zip"  && ! is_dir($package_name)) {
					$html .= (@unlink($package_name))   
						?  "<div class='success'>Successfully removed {$package_name}</div>"   
						:  "<div class='failed'>Does not exsist or unable to remove file: {$package_name}</div>";
				} else {
					$html .= "<div class='failed'>Does not exsist or unable to remove file '{$package_name}'.  Validate that an archive file exists.</div>";
				}
				echo $html;
			 ?>
			
			<i> <br/>
			 <?php _e('If the installer files did not successfully get removed, then you WILL need to remove them manually', 'wpflipwall')?>. <br/>
			 <?php _e('Please remove all installer files to avoid leaving open security issues on your server', 'wpflipwall')?>. <br/><br/>
			</i>
			
		<?php endif; ?>
		</div>
	<?php endif; ?>	
	

	<h3><?php _e('Data Cleanup', 'wpflipwall')?><hr size="1"/></h3>
	<table class="dup-reset-opts">
		<tr>
			<td><a href="?page=flipwall-tools&tab=cleanup&action=installer"><?php _e("Delete Reserved Files", 'wpflipwall'); ?></a></td>
			<td><?php _e("Removes all installer files from a previous install", 'wpflipwall'); ?></td>
		</tr>
		<tr>
			<td><a href="javascript:void(0)" onclick="Flipwall.Tools.DeleteLegacy()"><?php _e("Delete Legacy Data", 'wpflipwall'); ?></a></td>
			<td><?php _e("Removes all legacy data and settings prior to version", 'wpflipwall'); ?> [<?php echo FLIPWALL_VERSION ?>].</td>
		</tr>
				<tr>
			<td><a href="javascript:void(0)" onclick="Flipwall.Tools.ClearBuildCache()"><?php _e("Clear Build Cache", 'wpflipwall'); ?></a></td>
			<td><?php _e("Removes all build data from:", 'wpflipwall'); ?> [<?php echo FLIPWALL_SSDIR_PATH_TMP ?>].</td>
		</tr>	
	</table>

	
</form>

<script>	
jQuery(document).ready(function($) {
	

   Flipwall.Tools.DeleteLegacy = function () {
	   <?php
		   $msg  = __('This action will remove all legacy settings prior to version %1$s.  ', 'wpflipwall');
		   $msg .= __('Legacy settings are only needed if you plan to migrate back to an older version of this plugin.', 'wpflipwall'); 
	   ?>
	   var result = true;
	   var result = confirm('<?php printf(__($msg, 'wpflipwall'), FLIPWALL_VERSION) ?>');
	   if (! result) 
		   return;
		
	   window.location = '?page=flipwall-tools&tab=cleanup&action=legacy';
   }
   
   Flipwall.Tools.ClearBuildCache = function () {
	   <?php
		   $msg  = __('This process will remove all build cache files.  Be sure no packages are currently building or else they will be cancelled.', 'wpflipwall');
	   ?>
	   var result = true;
	   var result = confirm('<?php echo $msg ?>');
	   if (! result) 
		   return;
	   window.location = '?page=flipwall-tools&tab=cleanup&action=tmp-cache';
   }   
  
	
});	
</script>

