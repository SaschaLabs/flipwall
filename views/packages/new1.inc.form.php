
<form id="dup-form-opts" method="post" action="?page=flipwall&tab=new2" data-validate="parsley">
	<input type="hidden" id="dup-form-opts-action" name="action" value="">
	<input type="hidden" id="dup-form-opts-hash" name="package-hash" value="<?php echo $package_hash; ?>">

	<b style="font-size:15px"><i class="fa fa-archive"></i> <?php _e('Package', 'wpflipwall') ?></b>
	<hr size="1" />

	<label for="package-name"><b><?php _e('Name', 'wpflipwall') ?>:</b> </label>
	<a href="javascript:void(0)" onclick="Flipwall.Pack.ResetName()" title="<?php _e('Create a new default name', 'wpflipwall') ?>"><i class="fa fa-undo"></i></a> <br/>
	<input id="package-name"  name="package-name" type="text" value="<?php echo $Package->Name ?>" maxlength="40"  data-required="true" data-regexp="^[0-9A-Za-z|_]+$" /> <br/>
	<label><b><?php _e('Notes', 'wpflipwall') ?>:</b></label> <br/>
	<textarea id="package-notes" name="package-notes" maxlength="300" placeholder="<?php _e('Purpose of this package', 'wpflipwall') ?>" /><?php echo $Package->Notes ?></textarea>

	<!-- META-BOX: ARCHIVE -->
	<div class="dup-box" style="margin-top:10px">
	<div class="dup-box-title">
		<i class="fa fa-file-archive-o"></i> <?php _e('Archive', 'wpflipwall') ?> &nbsp;
		<span style="font-size:13px">
			<span id="dup-archive-filter-file" title="<?php _e('File filter enabled', 'wpflipwall') ?>"><i class="fa fa-files-o"></i> <i class="fa fa-filter"></i> &nbsp;&nbsp;</span> 
			<span id="dup-archive-filter-db" title="<?php _e('Database filter enabled', 'wpflipwall') ?>"><i class="fa fa-table"></i> <i class="fa fa-filter"></i></span>	
		</span>

		<div class="dup-box-arrow"></div>
	</div>		
	<div class="dup-box-panel" id="dup-pack-archive-panel" style="<?php echo $ui_css_archive?>">

		<label for="archive-format"><?php _e("Format", 'wpflipwall') ?>: </label> &nbsp;
		<select name="archive-format" id="archive-format">
			<option value="ZIP">Zip</option>
			<!--option value="TAR"></option>
			<option value="TAR-GZIP"></option-->
		</select>
		<!-- NESTED TABS -->
		<div class="categorydiv" id="dup-pack-opts-tabs">
			<ul class="category-tabs">
				<li class="tabs"><a href="javascript:void(0)" onclick="Flipwall.Pack.ToggleOptTabs(1, this)"><?php _e('Files', 'wpflipwall') ?></a></li>
				<li><a href="javascript:void(0)"onclick="Flipwall.Pack.ToggleOptTabs(2, this)"><?php _e('Database', 'wpflipwall') ?></a></li>
			</ul>

			<!-- TAB1: PACKAGE -->
			<div class="tabs-panel" id="dup-pack-opts-tabs-panel-1">
				<!-- FILTERS -->
				<?php
					$uploads = wp_upload_dir();
					$upload_dir = SLFW_Util::SafePath($uploads['basedir']);
				?>
				<fieldset>
					<legend><b> <i class="fa fa-filter"></i> <?php _e("Filters", 'wpflipwall') ?></b></legend>
					
					<div class="dup-enable-filters">
						<input type="checkbox" id="filter-on" name="filter-on" onclick="Flipwall.Pack.ToggleFileFilters()" <?php echo ($Package->Archive->FilterOn) ? "checked='checked'" : ""; ?> />	
						<label for="filter-on"><?php _e("Enable File Filters", 'wpflipwall') ?></label>
					</div>

					<div id="dup-file-filter-items">
						<label for="filter-dirs" title="<?php _e("Separate all filters by semicolon", 'wpflipwall'); ?>"><?php _e("Directories", 'wpflipwall') ?>: </label>
						<div class='dup-quick-links'>
							<a href="javascript:void(0)" onclick="Flipwall.Pack.AddExcludePath('<?php echo rtrim(FLIPWALL_WPROOTPATH, '/'); ?>')">[<?php _e("root path", 'wpflipwall') ?>]</a>
							<a href="javascript:void(0)" onclick="Flipwall.Pack.AddExcludePath('<?php echo rtrim($upload_dir , '/'); ?>')">[<?php _e("wp-uploads", 'wpflipwall') ?>]</a>
							<a href="javascript:void(0)" onclick="jQuery('#filter-dirs').val('')"><?php _e("(clear)", 'wpflipwall') ?></a>
						</div>
						<textarea name="filter-dirs" id="filter-dirs" placeholder="/full_path/exclude_path1;/full_path/exclude_path2;"><?php echo esc_textarea($Package->Archive->FilterDirs); ?></textarea><br/>
						<label class="no-select" title="<?php _e("Separate all filters by semicolon", 'wpflipwall'); ?>"><?php _e("File extensions", 'wpflipwall') ?>:</label>
						<div class='dup-quick-links'>
							<a href="javascript:void(0)" onclick="Flipwall.Pack.AddExcludeExts('avi;mov;mp4;mpeg;mpg;swf;wmv;aac;m3u;mp3;mpa;wav;wma')">[<?php _e("media", 'wpflipwall') ?>]</a>
							<a href="javascript:void(0)" onclick="Flipwall.Pack.AddExcludeExts('zip;rar;tar;gz;bz2;7z')">[<?php _e("archive", 'wpflipwall') ?>]</a>
							<a href="javascript:void(0)" onclick="jQuery('#filter-exts').val('')"><?php _e("(clear)", 'wpflipwall') ?></a>
						</div>
						<textarea name="filter-exts" id="filter-exts" placeholder="ext1;ext2;ext3;"><?php echo esc_textarea($Package->Archive->FilterExts); ?></textarea>
					
						<div class="dup-tabs-opts-help">
							<?php _e("The directory paths and extensions above will be be excluded from the archive file if enabled is checked.", 'wpflipwall'); ?> <br/>
							<?php _e("Use the full path for directories and semicolons to separate all items.", 'wpflipwall'); ?>
						</div>
						
					</div>
				</fieldset>
			</div>

			<!-- TAB2: DATABASE -->
			<div class="tabs-panel" id="dup-pack-opts-tabs-panel-2" style="display: none;">
				<fieldset>
					<legend><b><i class="fa fa-filter"></i> <?php _e('Filters', 'wpflipwall'); ?></b></legend>
					
					<div class="dup-enable-filters">						
						<table>
							<tr>
								<td><input type="checkbox" id="dbfilter-on" name="dbfilter-on" onclick="Flipwall.Pack.ToggleDBFilters()" <?php echo ($Package->Database->FilterOn) ? "checked='checked'" : ""; ?> /></td>
								<td><label for="dbfilter-on"><?php _e("Enable Table Filters", 'wpflipwall') ?> &nbsp;</label> </td>
								<td><div class="dup-tabs-opts-help" style="margin:5px 0px 0px 0px"><?php _e("checked tables are excluded", 'wpflipwall') ?></div></td>
							</tr>
						</table>
					</div>
					<div id="dup-db-filter-items">
						<a href="javascript:void(0)" id="dball" onclick="jQuery('#dup-dbtables .checkbox').prop('checked', true).trigger('click');">[ <?php _e( 'Include All', 'wpflipwall' ); ?> ]</a> &nbsp; 
						<a href="javascript:void(0)" id="dbnone" onclick="jQuery('#dup-dbtables .checkbox').prop('checked', false).trigger('click');">[ <?php _e( 'Exclude All', 'wpflipwall' ); ?> ]</a>
						<div style="font-stretch:ultra-condensed; font-family: Calibri; white-space: nowrap">
							<?php
							$tables = $wpdb->get_results( "SHOW FULL TABLES FROM `" . DB_NAME . "` WHERE Table_Type = 'BASE TABLE' ", ARRAY_N );
							$num_rows = count( $tables );
							echo '<table id="dup-dbtables"><tr><td valign="top">';
							$next_row = round( $num_rows / 3, 0 );
							$counter = 0;
							$tableList = explode(',', $Package->Database->FilterTables);
							foreach ( $tables as $table ) {
								if (in_array($table[0], $tableList)) {
									$checked =   'checked="checked"';
									$css =  'text-decoration:line-through';
								} else {
									$checked =   '';
									$css = '';
								}
								echo "<label for='dbtables-{$table[0]}' style='{$css}'><input class='checkbox dbtable' $checked type='checkbox' name='dbtables[]' id='dbtables-{$table[0]}' value='{$table[0]}' onclick='Flipwall.Pack.ExcludeTable(this)' />&nbsp;{$table[0]}</label><br />";
								$counter++;
								if ($next_row <= $counter) {
									echo '</td><td valign="top">';
									$counter = 0;
								}
							}
							echo '</td></tr></table>';
						?>
						</div>
						<div class="dup-tabs-opts-help">
							<?php _e("Checked tables will not be added to the database script.  Excluding certain tables can possibly cause your site or plugins to not work correctly after install!", 'wpflipwall'); ?>
						</div>	
					</div>
				</fieldset>
			</div>
		</div>		
	</div>
	</div><br/>
	<!-- end meta-box options  -->
	
	
	<!-- META-BOX: INSTALLER -->
	<div class="dup-box">
	<div class="dup-box-title">
		<i class="fa fa-bolt"></i> <?php _e('Installer', 'wpflipwall') ?>
		<div class="dup-box-arrow"></div>
	</div>			
		
	<div class="dup-box-panel" id="dup-pack-installer-panel" style="<?php echo $ui_css_installer ?>">
		<div class="dup-installer-header-1"><?php echo _e('STEP 1 - INPUTS'); ?></div><br/>
		<table class="dup-installer-tbl">
			<tr>
				<td colspan="2"><div class="dup-installer-header-2"><?php _e("MySQL Server", 'wpflipwall') ?></div></td>
			</tr>
			<tr>
				<td style="width:130px"><?php _e("Host", 'wpflipwall') ?></td>
				<td><input type="text" name="dbhost" id="dbhost" value="<?php echo $Package->Installer->OptsDBHost ?>"  maxlength="200" placeholder="localhost"/></td>
			</tr>
			<tr>
				<td><?php _e("Database", 'wpflipwall') ?></td>
				<td><input type="text" name="dbname" id="dbname" value="<?php echo $Package->Installer->OptsDBName ?>" maxlength="100" placeholder="mydatabaseName" /></td>
			</tr>							
			<tr>
				<td><?php _e("User", 'wpflipwall') ?></td>
				<td><input type="text" name="dbuser" id="dbuser" value="<?php echo $Package->Installer->OptsDBUser ?>"  maxlength="100" placeholder="databaseUserName" /></td>
			</tr>
			<tr>
				<td colspan="2"><div class="dup-installer-header-2"><?php _e("Advanced Options", 'wpflipwall') ?></div></td>
			</tr>						
			<tr>
				<td colspan="2">
					<table>
						<tr>
							<td style="width:130px"><?php _e("SSL", 'wpflipwall') ?></td>
							<td style="padding-right: 20px; white-space: nowrap">
								<input type="checkbox" name="ssl-admin" id="ssl-admin" <?php echo ($Package->Installer->OptsSSLAdmin) ? "checked='checked'" : ""; ?>  />
								<label class="chk-labels" for="ssl-admin"><?php _e("Enforce on Admin", 'wpflipwall') ?></label>
							</td>
							<td>
								<input type="checkbox" name="ssl-login" id="ssl-login" <?php echo ($Package->Installer->OptsSSLLogin) ? "checked='checked'" : ""; ?>  />
								<label class="chk-labels" for="ssl-login"><?php _e("Enforce on Logins", 'wpflipwall') ?></label>
							</td>
						</tr>
						<tr>
							<td><?php _e("Cache", 'wpflipwall') ?></td>									
							<td style="padding-right: 20px; white-space: nowrap">
								<input type="checkbox" name="cache-wp" id="cache-wp" <?php echo ($Package->Installer->OptsCacheWP) ? "checked='checked'" : ""; ?>  />
								<label class="chk-labels" for="cache-wp"><?php _e("Keep Enabled", 'wpflipwall') ?></label>	
							</td>
							<td>
								<input type="checkbox" name="cache-path" id="cache-path" <?php echo ($Package->Installer->OptsCachePath) ? "checked='checked'" : ""; ?>  />
								<label class="chk-labels" for="cache-path"><?php _e("Keep Home Path", 'wpflipwall') ?></label>			
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table><br />

		<div class="dup-installer-header-1"><?php echo _e('STEP 2 - INPUTS'); ?></div>

		<table class="dup-installer-tbl">
			<tr>
				<td style="width:130px"><?php _e("New URL", 'wpflipwall') ?></td>
				<td><input type="text" name="url-new" id="url-new" value="<?php echo $Package->Installer->OptsURLNew ?>" placeholder="http://mynewsite.com" /></td>
			</tr>
		</table>


		<div class="dup-tabs-opts-help">
			<?php _e("The installer can have these fields pre-filled at install time.", 'wpflipwall'); ?> <b><?php _e('All values are optional.', 'wpflipwall'); ?></b>
		</div>		

	</div>		
	</div><br/>
	<!-- end meta-box: installer  -->


	<div class="dup-button-footer">
		<input type="button" value="<?php _e("Reset", 'wpflipwall') ?>" class="button button-large" <?php echo ($SLFW_tests['Success']) ? '' : 'disabled="disabled"';?> onclick="Flipwall.Pack.ResetSettings()" />
		<input type="submit" value="<?php _e("Next", 'wpflipwall') ?> &#9658;" class="button button-primary button-large" <?php echo ($SLFW_tests['Success']) ? '' : 'disabled="disabled"';?> />
	</div>
	
</form>

<script>	
jQuery(document).ready(function($) {
	var SLFW_NAMEDEFAULT = '<?php echo $default_name ?>';
	var SLFW_NAMELAST = $('#package-name').val();
	
	Flipwall.Pack.ResetSettings = function () {
		var key = 'FLIPWALL_package_active';
		var result = confirm('<?php _e("This will reset all of the current package settings.  Would you like to continue?", "wpflipwall"); ?>');
		if (! result) 	return;
		
		jQuery('#dup-form-opts-action').val(key);
		jQuery('#dup-form-opts').attr('action', '?page=flipwall&tab=new1')
		jQuery('#dup-form-opts').submit();
	}
	
	Flipwall.Pack.ResetName = function () {
		var current = $('#package-name').val(); 
		$('#package-name').val( (current == SLFW_NAMELAST) ? SLFW_NAMEDEFAULT : SLFW_NAMELAST)
	}
	
	Flipwall.Pack.ExcludeTable = function (check) {
		var $cb =  $(check);
		if ($cb.is(":checked")) {
			$cb.closest("label").css('textDecoration', 'line-through');
		} else {
			$cb.closest("label").css('textDecoration', 'none');
		}
	}
	
});	
</script>
