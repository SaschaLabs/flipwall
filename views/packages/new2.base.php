<?php
	require_once (FLIPWALL_PLUGIN_PATH . 'classes/package.php');
	global $wp_version;
	$Package = new SLFW_Package();
	$Package->SaveActive($_POST);
	$Package = SLFW_Package::GetActive();
	
	$package_mysqldump	= SLFW_Settings::Get('package_mysqldump');
	$mysqlDumpPath = SLFW_Database::GetMySqlDumpPath();
	$build_mode = ($mysqlDumpPath && $package_mysqldump) ? 'mysqldump (fast)' : 'PHP (slow)';
	
	
?>

<style>
	/* -----------------------------
	PROGRESS ARES-CHECKS */
	div#dup-progress-area {text-align:center; max-width:650px; min-height:200px; margin:0px auto 0px auto; padding:0px;}
	div#dup-msg-success {color:#18592A; padding:5px; text-align: left}	
	div#dup-msg-success-subtitle {font-style: italic; margin:7px 0px}	
	div#dup-msg-error {color:#A62426; padding:5px; max-width: 790px;}
	div#dup-msg-error-response-text { max-height:350px; overflow-y:scroll; border:1px solid silver; border-radius: 3px; padding:8px;background:#fff}

	div.dup-panel {margin-bottom: 25px}
	div.dup-scan-filter-status {display:inline; float: right; font-size:11px; margin-right:10px; color:#AF0000; font-style: italic}
	/* 	SERVER-CHECKS */
	div.dup-scan-title {display:inline-block;  padding:1px; font-weight: bold;}
	div.dup-scan-title a {display:inline-block; width:225px; padding:1px; }
	div.dup-scan-title div {display:inline-block;  }
	div.dup-scan-info {display:none;}
	div.dup-scan-good {display:inline-block; color:green;font-weight: bold;}
	div.dup-scan-warn {display:inline-block; color:#AF0000;font-weight: bold;}
	span.dup-toggle {float:left; margin:0 2px 2px 0; }
	/*DATABASE*/
	table#dup-scan-db-details {line-height: 14px; margin:15px 0px 0px 5px;  width:98%}
	table#dup-scan-db-details td {padding:0px;}
	table#dup-scan-db-details td:first-child {font-weight: bold;  white-space: nowrap; width:90px}
	div#dup-scan-db-info {margin:0px 0px 0px 10px}
	div#data-db-tablelist {max-height: 300px; overflow-y: scroll}
	div#data-db-tablelist div{padding:0px 0px 0px 15px;}
	div#data-db-tablelist span{display:inline-block; min-width: 75px}
	div#data-db-size1 {display: inline-block; float:right; font-size:11px; margin-right: 15px; font-style: italic}
	/*FILES */
	div#data-arc-size1 {display: inline-block; float:right; font-size:11px; margin-right: 15px; font-style: italic}
	div#data-arc-names-data, div#data-arc-big-data
		{word-wrap: break-word;font-size:10px; border:1px dashed silver; padding:5px; display: none}
	
	/*Footer*/
	div.dup-button-footer {text-align:center; margin:5px 0px 0px 0px}
	button.button {font-size:15px !important; height:30px !important; font-weight:bold; padding:3px 5px 5px 5px !important;}
</style>

<!-- =========================================
WIZARD STEP TABS -->
<div id="dup-wiz">
	<div id="dup-wiz-steps">
		<div class="completed-step"><a><span>1</span> <?php _e('Setup', 'wpflipwall'); ?></a></div>
		<div class="active-step"><a><span>2</span> <?php _e('Scan', 'wpflipwall'); ?> </a></div>
		<div><a><span>3</span> <?php _e('Build', 'wpflipwall'); ?> </a></div>
	</div>
	<div id="dup-wiz-title">
		<?php _e('Step 2: System Scan', 'wpflipwall'); ?>
	</div> <hr />
</div>	

<form id="form-flipwall" method="post" action="?page=flipwall&tab=new3">
<div id="dup-progress-area">
	<!--  PROGRESS BAR -->
	<div id="dup-progress-bar-area">
		<h2><i class="fa fa-spinner fa-spin"></i> <?php _e('Scanning Site', 'wpflipwall'); ?></h2>
		<div id="dup-progress-bar"></div>
		<b><?php _e('Please Wait...', 'wpflipwall'); ?></b>
	</div>

	<!--  SUCCESS MESSAGE -->
	<div id="dup-msg-success" style="display:none">
		<div style="text-align:center">
			<div class="dup-hdr-success"><i class="fa fa-check-square-o fa-lg"></i> <?php _e('Scan Complete', 'wpflipwall'); ?></div>
			<div id="dup-msg-success-subtitle">
				<?php _e("Scan checks are not required to pass, however they could cause issues on some systems.", 'wpflipwall'); ?><br/>
				<?php _e("Process Time:", 'wpflipwall'); ?> <span id="data-rpt-scantime"></span>
			</div>
		</div><br/>
		
		<!-- ================================================================
		META-BOX: SERVER
		================================================================ -->
		<div class="dup-panel">
		<div class="dup-panel-title">
			<i class="fa fa-hdd-o"></i> <?php 	_e("Server", 'wpflipwall');	?>
			<div style="float:right; margin:-1px 10px 0px 0px">
				<small><a href="?page=flipwall-settings&tab=diagnostics" target="_blank"><?php _e('Diagnostics', 'wpflipwall');?></a>  </small>	
				<!--span style="font-size:11px">|</span>
				<small><a id="data-rpt-scanfile" href="admin-ajax.php?action=FLIPWALL_package_report" target="_blank"><?php _e('Report', 'wpflipwall');?></a></small-->
			</div>
		
		</div>
		<div class="dup-panel-panel">
			<!-- -------------------
			WEB SERVER: 103 -->
			<div>
				<div class='dup-scan-title'>
					<a><?php _e('Web Server', 'wpflipwall');?></a> <div id="data-srv-webserver"></div>
				</div>
				<div class='dup-scan-info dup-info-box'>
					<?php 
						$web_servers = implode(', ', $GLOBALS['FLIPWALL_SERVER_LIST']);
						printf("<b>%s:</b> [%s]<br/> %s {$web_servers}",
							__("Web Server", 'wpflipwall'),
							$_SERVER['SERVER_SOFTWARE'],
							__("The Flipwall currently works with these web servers:", 'wpflipwall')
						);
					?>
				</div>
			</div>				
			<!-- -------------------
			PHP SETTINGS: 100 -->
			<div>
				<div class='dup-scan-title'>
					<a><?php _e('PHP Settings', 'wpflipwall');?></a> <div id="data-srv-phpserver"></div>
				</div>
				<div class='dup-scan-info dup-info-box'>
					<?php 
						//OPEN BASE DIR
						$test = ini_get("open_basedir");
						echo '<b>' . __('Open Base Dir', 'wpflipwall') . ':</b> ';
						echo (empty($test)) ? 'Off' : 'On';  echo '<br/><br/>';
						_e('The Flipwall may have issues when [open_basedir] is enabled. Please work with your server administrator to disable this value in the php.ini file if you’re having issues building a package.', 'wpflipwall');
						echo "&nbsp;<i><a href='http://www.php.net/manual/en/ini.core.php#ini.open-basedir' target='_blank'>[" . __('more info', 'wpflipwall')  . "]</a></i><br/>";
						
						//TIMEOUT SETTINGS
						$test = ini_get("max_execution_time");		
						echo '<hr size="1" /><br/> <b>' . __('Max Execution Time', 'wpflipwall') . ':</b> ';
						echo (empty($test)) ? 'Off' : "{$test}";  
						echo '<br/><br/>';
						
						printf(__('The Flipwall will have issues when the [max_execution_time] value in the php.ini is low.  Timeouts effect how long a process is allowed to run.  The recommended timeout is "%1$s" seconds. An attempt is made to override this value if the server allows it.  Please work with your server administrator to make sure there are no restrictions for how long a PHP process is allowed to run.', 'wpflipwall'), FLIPWALL_SCAN_TIMEOUT); 
						echo '<br/>';
						echo '<small>';
						_e('Note: Timeouts can also be set at the web server layer, so if the PHP max timeout passes and you still see a build interrupt messages, then your web server could be killing the process.   If you are limited on processing time, consider using the database or file filters to shrink the size of your overall package.   However use caution as excluding the wrong resources can cause your install to not work properly.', 'wpflipwall');
						echo "&nbsp;<i><a href='http://www.php.net/manual/en/info.configuration.php#ini.max-execution-time' target='_blank'>[" . __('more info', 'wpflipwall')  . "]</a></i>";
						echo '</small>';
						
					?>
				</div>
			</div>
			<!-- -------------------
			WORDPRESS SETTINGS: 101 -->
			<div>
				<div class='dup-scan-title'>
					<a><?php _e('WordPress Settings', 'wpflipwall');?></a> <div id="data-srv-wpsettings"></div>
				</div>
				<div class='dup-scan-info dup-info-box'>
					<?php 
						//VERSION CHECK
						printf("<b>%s:</b> [%s]<br/><br/> %s %s",
							__("WordPress Version", 'wpflipwall'),
							$wp_version,
							__("It is recommended to have a version of WordPress that is greater that ", 'wpflipwall'),
							FLIPWALL_SCAN_MIN_WP
						);
						
						//CORE FILES
						echo "<hr size='1' /><br/>";
						$core_test = file_exists(SLFW_Util::SafePath(FLIPWALL_WPROOTPATH .  '/wp-config.php')) ? __('Found', 'wpflipwall') : __('Missing', 'wpflipwall');
						printf("<b>%s:</b> [%s]<br/><br/> %s",
							__("Core Files", 'wpflipwall'),
							$core_test,
							__("If the scanner is unable to locate the wp-config.php file in the root directory, then you will need to manually copy it to its new location.", 'wpflipwall')
						);
					
						//CACHE DIR
						echo "<hr size='1' /><br/>";
						$cache_path = $cache_path = SLFW_Util::SafePath(WP_CONTENT_DIR) .  '/cache';
						$cache_size = SLFW_Util::ByteSize(SLFW_Util::GetDirectorySize($cache_path));
						echo '<b>' . __('Cache Path', 'wpflipwall') . ":</b> {$cache_path} ({$cache_size})<br/><br/>";
						_e("Cached data will lead to issues at install time and increases your archive size. It is recommended to empty your cache directory at build time. Use caution when removing data from the cache directory. If you have a cache plugin review the documentation for how to empty it; simply removing files might cause errors on your site.", 'wpflipwall');
						_e("The cache size minimum threshold is currently set at ", 'wpflipwall');
						echo SLFW_Util::ByteSize(FLIPWALL_SCAN_CACHESIZE) . '.';
						
					?>
				</div>
			</div>
		</div><!-- end .dup-panel -->
		</div><!-- end .dup-panel-panel -->
		<br/>
	
		<h2 style="font-size:18px; font-weight:bold"><i class="fa fa-file-archive-o"></i>&nbsp;<?php _e('Archive', 'wpflipwall');?> </h2>
		
		<!-- ================================================================
		FILES
		================================================================ -->
		<div class="dup-panel">
			<div class="dup-panel-title">
				<i class="fa fa-files-o"></i>
				<?php _e("Files", 'wpflipwall'); ?> 
				<div id="data-arc-size1"></div>
				<div class="dup-scan-filter-status">
					<?php 
						if ($Package->Archive->FilterOn) {
							echo '<i class="fa fa-filter"></i> '; _e('Enabled', 'wpflipwall');
						} 
					?> 
				</div>
			</div>
			<div class="dup-panel-panel">

				<!-- -------------------
				TOTAL SIZE: CHK-FILE-100 -->
				<div>
					<div class='dup-scan-title'>
						<a><?php _e('Total Size', 'wpflipwall');?></a> <div id="data-arc-status-size"></div>
					</div>
					<div class='dup-scan-info  dup-info-box'>
						<b><?php _e('Size', 'wpflipwall');?>:</b> <span id="data-arc-size2"></span>  &nbsp; | &nbsp;
						<b><?php _e('File Count', 'wpflipwall');?>:</b> <span id="data-arc-files"></span>  &nbsp; | &nbsp;
						<b><?php _e('Directory Count', 'wpflipwall');?>:</b> <span id="data-arc-dirs"></span> <br/><br/>
						<?php 
							printf(__('Total size reprents all files minus any filters that have been setup.  The current thresholds that trigger warnings are %1$s for the entire site and %2$s for large files.', 'wpflipwall'), SLFW_Util::ByteSize(FLIPWALL_SCAN_SITE), SLFW_Util::ByteSize(FLIPWALL_SCAN_WARNFILESIZE));
						?>
					</div>
				</div>		

				<!-- -------------------
				FILE NAME LENGTHS: CHK-FILE-101 -->
				<div>
					<div class='dup-scan-title'>
						<a><?php _e('Invalid Names', 'wpflipwall');?></a> <div id="data-arc-status-names"></div>
					</div>
					<div class='dup-scan-info dup-info-box'>
						<?php 
							_e('Invalid file or folder names can cause issues when extracting an archive across different environments.  Invalid file names consist of lengths over 250 characters and illegal characters that may not work on all operating systems such as * ? > < : / \ |  .  It is recommended to remove or filter these files before building the archive or else you might have issues at install time.', 'wpflipwall');
						?><br/><br/>
						<a href="javascript:void(0)" onclick="jQuery('#data-arc-names-data').toggle()">[<?php _e('Show Paths', 'wpflipwall');?>]</a>
						<div id="data-arc-names-data"></div>
					</div>
				</div>		

				<!-- -------------------
				LARGE FILES: CHK-FILE-102 -->
				<div>
					<div class='dup-scan-title'>
						<a><?php _e('Large Files', 'wpflipwall');?></a> <div id="data-arc-status-big"></div>
					</div>
					<div class='dup-scan-info  dup-info-box'>
						<?php 
							printf(__('Large files such as movies or other backuped data can cause issues with timeouts.  The current check for large files is %1$s per file.  If your having issues creating a package consider excluding these files with the files filter and manually moving them to your new location.', 'wpflipwall'), SLFW_Util::ByteSize(FLIPWALL_SCAN_WARNFILESIZE));
						?><br/><br/>
						<a href="javascript:void(0)" onclick="jQuery('#data-arc-big-data').toggle()">[<?php _e('Show Paths', 'wpflipwall');?>]</a>
						<div id="data-arc-big-data"></div>
					</div>
				</div>	
				
				<!-- -------------------
				VIEW FILTERS -->
				
				<?php if ($Package->Archive->FilterOn) : ?>
					<div>
						<div class='dup-scan-title'>
							<a><?php _e('View Filters', 'wpflipwall');?></a> 
						</div>
						<div class='dup-scan-info  dup-info-box'>
							<?php _e('Below is a list of the directories and file extension that will be excluded from the archive.', 'wpflipwall'); ?>
							<br/><br/>
							
							<b>[<?php _e('Directories', 'wpflipwall');?>]</b><br/>
							<?php
								if (strlen( $Package->Archive->FilterDirs)) {
									echo str_replace(";", "<br/>", $Package->Archive->FilterDirs); 
								} else {
									_e('No directory filters have been set.', 'wpflipwall');
								}
							?>
							<br/>
							
							<b>[<?php _e('File Extensions', 'wpflipwall');?>]</b><br/>
							<?php
								if (strlen( $Package->Archive->FilterExts)) {
									echo $Package->Archive->FilterExts; 
								} else {
									_e('No file extension filters have been set.', 'wpflipwall');
								}
							?>								
						</div>
					</div>	
				<?php endif;  ?>	

			</div><!-- end .dup-panel -->
			<br/>

			<!-- ================================================================
			DATABASE
			================================================================ -->
			<div class="dup-panel-title">
				<i class="fa fa-table"></i>
				<?php _e("Database", 'wpflipwall');	?>
				<div id="data-db-size1"></div>
				<div class="dup-scan-filter-status">
					<?php 
						if ($Package->Database->FilterOn) {
							echo '<i class="fa fa-filter"></i> '; _e('Enabled', 'wpflipwall');
						} 
					?> 
				</div>
			</div>
			<div class="dup-panel-panel" id="dup-scan-db">

				<!-- -------------------
				TOTAL SIZE: 100 -->
				<div>
					<div class='dup-scan-title'>
						<a><?php _e('Total Size', 'wpflipwall');?></a>
						<div id="data-db-status-size1"></div>
					</div>
					<div class='dup-scan-info  dup-info-box'>
						<b><?php _e('Tables', 'wpflipwall');?>:</b> <span id="data-db-tablecount"></span> &nbsp; | &nbsp;
						<b><?php _e('Records', 'wpflipwall');?>:</b> <span id="data-db-rows"></span> &nbsp; | &nbsp;
						<b><?php _e('Size', 'wpflipwall');?>:</b> <span id="data-db-size2"></span> <br/><br/>
						<?php 
							$lnk = '<a href="maint/repair.php" target="_blank">' . __('repair and optimization', 'wpflipwall') . '</a>';
							printf(__('Total size and row count for all database tables are approximate values.  The thresholds that trigger warnings are %1$s and %2$s records.  Large databases take time to process and can cause issues with server timeout and memory settings.  Running a %3$s on your database can also help improve the overall size and performance.  If your server supports shell_exec and mysqldump you can try to enable this option from the settings menu.', 'wpflipwall'), 
									SLFW_Util::ByteSize(FLIPWALL_SCAN_DBSIZE), 
									number_format(FLIPWALL_SCAN_DBROWS),
									$lnk);
						?>
					</div>
				</div>

				<!-- -------------------
				TABLE DETAILS -->
				<div>
					<div class='dup-scan-title'>
						<a><?php _e('Table Details', 'wpflipwall');?></a>
						<div id="data-db-status-size2"></div>
					</div>
					<div class='dup-scan-info dup-info-box'>
						<div id="dup-scan-db-info">
							<div id="data-db-tablelist"></div>
						</div>
					</div>
				</div>

				<table id="dup-scan-db-details">
					<tr><td><b><?php _e('Name:', 'wpflipwall');?></b></td><td><?php echo DB_NAME ;?> </td></tr>
					<tr><td><b><?php _e('Host:', 'wpflipwall');?></b></td><td><?php echo DB_HOST ;?> </td></tr>
					<tr><td><b><?php _e('Build Mode:', 'wpflipwall');?></b></td><td><a href="?page=flipwall-settings" target="_blank"><?php echo $build_mode ;?></a> </td></tr>
				</table>	

			</div><!-- end .dup-panel -->
		</div><!-- end .dup-panel-panel -->
		
		
	</div>

	<!--  ERROR MESSAGE -->
	<div id="dup-msg-error" style="display:none">
		<div class="dup-hdr-error"><i class="fa fa-exclamation-circle"></i> <?php _e('Scan Error', 'wpflipwall'); ?></div>
		<i><?php _e('Please try again!', 'wpflipwall'); ?></i><br/>
		<div style="text-align:left">
			<b><?php _e("Server Status:", 'wpflipwall'); ?></b> &nbsp;
			<div id="dup-msg-error-response-status" style="display:inline-block"></div><br/>

			<b><?php _e("Error Message:", 'wpflipwall'); ?></b>
			<div id="dup-msg-error-response-text"></div>
		</div>
	</div>			

</div> <!-- end #dup-progress-area -->
<div class="dup-button-footer" style="display:none">
	<input type="button" value="&#9668; <?php _e("Back", 'wpflipwall') ?>" onclick="window.location.assign('?page=flipwall&tab=new1')" class="button button-large" />
	<input type="button" value="<?php _e("Rescan", 'wpflipwall') ?>" onclick="Flipwall.Pack.Rescan()" class="button button-large" />
	<input type="submit" value="<?php _e("Build", 'wpflipwall') ?> &#9658" class="button button-primary button-large" />
	<!-- Used for iMacros testing do not remove -->
	<div id="dup-automation-imacros"></div>
</div>
</form>

<script type="text/javascript">
jQuery(document).ready(function($) {
		
	/*	----------------------------------------
	*	METHOD: Performs Ajax post to create check system  */
	Flipwall.Pack.Scan = function() {
		var data = {action : 'FLIPWALL_package_scan'}

		$.ajax({
			type: "POST",
			url: ajaxurl,
			dataType: "json",
			timeout: 10000000,
			data: data,
			complete: function() {$('.dup-button-footer').show()},
			success:    function(data) { 
				Flipwall.Pack.LoadScanData(data)
				//Imacros testing required
				$('#dup-automation-imacros').html('<input type="hidden" id="dup-finished" value="done" />');
			},
			error: function(data) { 
				$('#dup-progress-bar-area').hide(); 
				var status = data.status + ' -' + data.statusText;
				$('#dup-msg-error-response-status').html(status)
				$('#dup-msg-error-response-text').html(data.responseText);
				$('#dup-msg-error').show(200);
				console.log(data);
			}
		});
	}
	
	Flipwall.Pack.Rescan = function() {
		$('#dup-msg-success,#dup-msg-error,.dup-button-footer').hide();
		$('#dup-progress-bar-area').show(); 
		Flipwall.Pack.Scan();
	}
	
	/*	----------------------------------------
	*	METHOD:    */
	Flipwall.Pack.LoadScanData = function(data) {
		
		var errMsg = "unable to read";
		$('#dup-progress-bar-area').hide(); 
		$('#dup-msg-success').show();
		
		//****************
		//REPORT
		var base = $('#data-rpt-scanfile').attr('href');
		$('#data-rpt-scanfile').attr('href',  base + '&scanfile=' + data.RPT.ScanFile);
		$('#data-rpt-scantime').text(data.RPT.ScanTime || 0);
		
		//****************
		//SERVER
		$('#data-srv-phpserver').text(data.SRV.PHPServer || errMsg);
		$('#data-srv-wpsettings').text(data.SRV.WPSettings   || errMsg);
		$('#data-srv-webserver').text(data.SRV.WebServer || errMsg);
		
		//****************
		//DATABASE
		var html = "";
		if (data.DB.Status.Success) {
			$('#data-db-status-size1').text(data.DB.Status.Size || errMsg);
			$('#data-db-status-size2').text(data.DB.Status.Size || errMsg);
			$('#data-db-size1').text(data.DB.Size || errMsg);
			$('#data-db-size2').text(data.DB.Size || errMsg);
			$('#data-db-rows').text(data.DB.Rows || errMsg);
			$('#data-db-tablecount').text(data.DB.TableCount || errMsg);
			//Table Details
			if (data.DB.TableList == undefined || data.DB.TableList.length == 0) {
				html = '<?php _e("Unable to report on any tables", 'wpflipwall') ?>';
			} else {
				$.each(data.DB.TableList, function(i) {
					html += '<b>' + i  + '</b><br/>';
					$.each(data.DB.TableList[i], function(key,val) {html += '<div><span>' + key  + ':</span>' + val + '</div>'; })
				});					
			}
			$('#data-db-tablelist').append(html);
		} else {
			html = '<?php _e("Unable to report on database stats", 'wpflipwall') ?>';
			$('#dup-scan-db').html(html);
		}
		
		//****************
		//ARCHIVE
		$('#data-arc-status-size').text(data.ARC.Status.Size || errMsg);
		$('#data-arc-status-names').text(data.ARC.Status.Names|| errMsg);
		$('#data-arc-status-big').text(data.ARC.Status.Big || errMsg);
		$('#data-arc-size1').text(data.ARC.Size || errMsg);
		$('#data-arc-size2').text(data.ARC.Size || errMsg);
		$('#data-arc-files').text(data.ARC.FileCount || errMsg);
		$('#data-arc-dirs').text(data.ARC.DirCount || errMsg);
	
		//Invalid Names
		html = '<?php _e("No name length issues.", 'wpflipwall') ?>';
		if (data.ARC.WarnFileName != undefined && data.ARC.WarnFileName.length > 0) {
			html = '';
			$.each(data.ARC.WarnFileName, function(key, val) {html += '<?php _e("FILE", 'wpflipwall') ?> ' + key + ':<br/>[' + val  + ']<br/>';});
		}
		$('#data-arc-names-data').html(html);
		
		//Large Files
		html = '<?php _e("No large files found.", 'wpflipwall') ?>';
		if (data.ARC.WarnFileSize != undefined && data.ARC.WarnFileSize.length > 0) {
			html = '';
			$.each(data.ARC.WarnFileSize, function(key, val) {html += '<?php _e("FILE", 'wpflipwall') ?> ' + key + ':<br/>' + val  + '<br/>' ;});	
		}
		$('#data-arc-big-data').html(html);
		

		//Color Code Good/Warn
		$('.dup-scan-title div').each(function() {
			$(this).addClass( ( $(this).text() == 'Good') ? 'dup-scan-good' : 'dup-scan-warn');
		});
	}
	
	//Page Init:
	Flipwall.UI.AnimateProgressBar('dup-progress-bar');
	Flipwall.Pack.Scan();
	
	//Init: Toogle for system requirment detial links
	$('.dup-scan-title a').each(function() {
		$(this).attr('href', 'javascript:void(0)');
		$(this).click({selector : '.dup-scan-info'}, Flipwall.Pack.ToggleSystemDetails);
		$(this).prepend("<span class='ui-icon ui-icon-triangle-1-e dup-toggle' />");
	});
	
});
</script>