<?php
	global $wp_version;
	global $wpdb;
	
	$action_updated = null;
	$action_response = __("Settings Saved", 'wpflipwall');
	if (isset($_POST['action']) && $_POST['action'] == 'save') {
		//General Tab
		//Plugin
		SLFW_Settings::Set('uninstall_settings',		isset($_POST['uninstall_settings']) ? "1" : "0");
		SLFW_Settings::Set('uninstall_files',		isset($_POST['uninstall_files'])  ? "1" : "0");
		SLFW_Settings::Set('uninstall_tables',		isset($_POST['uninstall_tables']) ? "1" : "0");
		SLFW_Settings::Set('storage_htaccess_off',	isset($_POST['storage_htaccess_off']) ? "1" : "0");
		
		//Package
		SLFW_Settings::Set('package_debug',			isset($_POST['package_debug']) ? "1" : "0");
		SLFW_Settings::Set('package_zip_flush',		isset($_POST['package_zip_flush']) ? "1" : "0");
		SLFW_Settings::Set('package_mysqldump',		isset($_POST['package_mysqldump']) ? "1" : "0");
		SLFW_Settings::Set('package_mysqldump_path',	trim($_POST['package_mysqldump_path']));
		
		$action_updated  = SLFW_Settings::Save();
		SLFW_Util::InitSnapshotDirectory();
	} 

	$uninstall_settings		= SLFW_Settings::Get('uninstall_settings');
	$uninstall_files		= SLFW_Settings::Get('uninstall_files');
	$uninstall_tables		= SLFW_Settings::Get('uninstall_tables');
	$storage_htaccess_off	= SLFW_Settings::Get('storage_htaccess_off');
	
	$package_debug			= SLFW_Settings::Get('package_debug');
	$package_zip_flush		= SLFW_Settings::Get('package_zip_flush');
	$package_mysqldump		= SLFW_Settings::Get('package_mysqldump');
	$package_mysqldump_path	= trim(SLFW_Settings::Get('package_mysqldump_path'));
	
	
	$mysqlDumpPath = SLFW_Database::GetMySqlDumpPath();
	$mysqlDumpFound = ($mysqlDumpPath) ? true : false;

?>

<style>
	form#dup-settings-form input[type=text] {width: 400px; }
	input#package_mysqldump_path_found {margin-top:5px}
	div.dup-mysql-dump-found {padding:3px; border:1px solid silver; background: #f7fcfe; border-radius: 3px; width:400px; font-size: 12px}
	div.dup-mysql-dump-notfound {padding:3px; border:1px solid silver; background: #fcf3ef; border-radius: 3px; width:400px; font-size: 12px}
</style>

<form id="dup-settings-form" action="<?php echo admin_url( 'admin.php?page=flipwall-settings&tab=general' ); ?>" method="post">
	
	<?php wp_nonce_field( 'FLIPWALL_settings_page' ); ?>
	<input type="hidden" name="action" value="save">
	<input type="hidden" name="page"   value="flipwall-settings">

	<?php if($action_updated)  :	?>
		<div id="message" class="updated below-h2"><p><?php echo $action_response; ?></p></div>
	<?php endif; ?>	
	
	
	<!-- ===============================
	PLUG-IN SETTINGS -->
	<h3 class="title"><?php _e("Plugin", 'wpflipwall') ?> </h3>
	<hr size="1" />
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label><?php _e("Version", 'wpflipwall'); ?></label></th>
			<td><?php echo FLIPWALL_VERSION ?></td>
		</tr>	
		<tr valign="top">
			<th scope="row"><label><?php _e("Uninstall", 'wpflipwall'); ?></label></th>
			<td>
				<input type="checkbox" name="uninstall_settings" id="uninstall_settings" <?php echo ($uninstall_settings) ? 'checked="checked"' : ''; ?> /> 
				<label for="uninstall_settings"><?php _e("Delete Plugin Settings", 'wpflipwall') ?> </label><br/>

				<input type="checkbox" name="uninstall_files" id="uninstall_files" <?php echo ($uninstall_files) ? 'checked="checked"' : ''; ?> /> 
				<label for="uninstall_files"><?php _e("Delete Entire Storage Directory", 'wpflipwall') ?></label><br/>

			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label><?php _e("Storage", 'wpflipwall'); ?></label></th>
			<td>
				<?php _e("Full Path", 'wpflipwall'); ?>: 
				<?php echo  SLFW_Util::SafePath(FLIPWALL_SSDIR_PATH); ?><br/><br/>
				<input type="checkbox" name="storage_htaccess_off" id="storage_htaccess_off" <?php echo ($storage_htaccess_off) ? 'checked="checked"' : ''; ?> /> 
				<label for="storage_htaccess_off"><?php _e("Disable .htaccess File In Storage Directory", 'wpflipwall') ?> </label>
				<p class="description">
					<?php  _e("Disable if issues occur when downloading installer/archive files.", 'wpflipwall'); ?>
				</p>
			</td>
		</tr>	
	</table>
	
	
	<!-- ===============================
	PACKAGE SETTINGS -->
	<h3 class="title"><?php _e("Package", 'wpflipwall') ?> </h3>
	<hr size="1" />
	<table class="form-table">
		<tr>
			<th scope="row"><label><?php _e("Archive Flush", 'wpflipwall'); ?></label></th>
			<td>
				<input type="checkbox" name="package_zip_flush" id="package_zip_flush" <?php echo ($package_zip_flush) ? 'checked="checked"' : ''; ?> />
				<label for="package_zip_flush"><?php _e("Attempt Network Keep Alive", 'wpflipwall'); ?></label>
				<i style="font-size:12px">(<?php _e("recommended only for large archives", 'wpflipwall'); ?>)</i> 
				<p class="description">
					<?php _e("This will attempt to keep a network connection established for large archives.", 'wpflipwall'); ?>
				</p>
			</td>
		</tr>		
		<tr>
			<th scope="row"><label><?php _e("Database Build", 'wpflipwall'); ?></label></th>
			<td>
				
				<?php if (! SLFW_Util::IsShellExecAvailable()) :?>
					<p class="description">
						<?php 
							_e("This server does not have shell_exec configured to run.", 'wpflipwall'); echo '<br/>';
							_e("Please contact the server administrator to enable this feature.", 'wpflipwall'); 
						?>
					</p>
				<?php else : ?>
					<input type="checkbox" name="package_mysqldump" id="package_mysqldump" <?php echo ($package_mysqldump) ? 'checked="checked"' : ''; ?> />
					<label for="package_mysqldump"><?php _e("Use mysqldump", 'wpflipwall'); ?></label> &nbsp;
					<i style="font-size:12px">(<?php _e("recommended for large databases", 'wpflipwall'); ?>)</i> <br/><br/>
					
					<div style="margin:5px 0px 0px 25px">
						<?php if ($mysqlDumpFound) :?>
							<div class="dup-mysql-dump-found">
								<?php _e("Working Path:", 'wpflipwall'); ?> &nbsp;
								<i><?php echo $mysqlDumpPath ?></i>
							</div><br/>
						<?php else : ?>
							<div class="dup-mysql-dump-notfound">
								<?php 
									_e('Mysqldump was not found at its default location or the location provided.  Please enter a path to a valid location where mysqldump can run.  If the problem persist contact your server administrator.', 'wpflipwall'); 
								?>
							</div><br/>
						<?php endif; ?>

						<label><?php _e("Add Custom Path:", 'wpflipwall'); ?></label><br/>
						<input type="text" name="package_mysqldump_path" id="package_mysqldump_path" value="<?php echo $package_mysqldump_path; ?> " />
						<p class="description">
							<?php 
								_e("This is the path to your mysqldump program.", 'wpflipwall'); 
							?>
						</p>
					</div>
			
				<?php endif; ?>
			</td>
		</tr>	
		<tr>
			<th scope="row"><label><?php _e("Package Debug", 'wpflipwall'); ?></label></th>
			<td>
				<input type="checkbox" name="package_debug" id="package_debug" <?php echo ($package_debug) ? 'checked="checked"' : ''; ?> />
				<label for="package_debug"><?php _e("Show Package Debug Status in Packages Screen", 'wpflipwall'); ?></label>
			</td>
		</tr>	
		
	</table>

	<p class="submit" style="margin: 20px 0px 0xp 5px;">
		<div style="border-top: 1px solid #efefef"></div><br/>
		<input type="submit" name="submit" id="submit" class="button-primary" value="<?php _e("Save Settings", 'wpflipwall') ?>" style="display: inline-block;"/>
	</p>
</form>