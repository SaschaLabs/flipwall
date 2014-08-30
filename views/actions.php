<?php
/**
 *  FLIPWALL_PACKAGE_SCAN
 *  Returns a json scan report object which contains data about the system
 *  
 *  @return json   json report object
 *  @example	   to test: /wp-admin/admin-ajax.php?action=FLIPWALL_package_scan
 */
function FLIPWALL_package_scan() {
	
	@set_time_limit(0);
	$errLevel = error_reporting();
	error_reporting(E_ERROR);
	SLFW_Util::InitSnapshotDirectory();
	
	$Package = SLFW_Package::GetActive();
	$report = $Package->Scan();
	$Package->SaveActiveItem('ScanFile', $Package->ScanFile);
	$json_response = json_encode($report);
	
	SLFW_Package::TmpCleanup();
	error_reporting($errLevel);
    die($json_response);
}

/**
 *  FLIPWALL_package_build
 *  Returns the package result status
 *  
 *  @return json   json object of package results
 */
function FLIPWALL_package_build() {
	
	@set_time_limit(0);
	$errLevel = error_reporting();
	error_reporting(E_ERROR);
	SLFW_Util::InitSnapshotDirectory();

	$Package = SLFW_Package::GetActive();
	
	if (!is_readable(FLIPWALL_SSDIR_PATH_TMP . "/{$Package->ScanFile}")) {
		die("The scan result file was not found.  Please run the scan step before building the package.");
	}
	
	$Package->Build();
	
	//JSON:Debug Response
	//Pass = 1, Warn = 2, Fail = 3
	$json = array();
	$json['Status']   = 1;
	$json['Package']  = $Package;
	$json['Runtime']  = $Package->Runtime;
	$json['ExeSize']  = $Package->ExeSize;
	$json['ZipSize']  = $Package->ZipSize;
	$json_response = json_encode($json);
	
	error_reporting($errLevel);
    die($json_response);
}


function FLIPWALL_package_report() {
	
	$scanReport = $_GET['scanfile'];
	header('Content-Type: application/json');
	header("Location: " . FLIPWALL_SSDIR_URL . "/tmp/" . $scanReport);
	echo FLIPWALL_SSDIR_URL . "/tmp/" . $scanReport;
	
    die();
}

/**
 *  FLIPWALL_PACKAGE_DELETE
 *  Deletes the files and database record entries
 *
 *  @return json   A json message about the action.  
 *				   Use console.log to debug from client
 */
function FLIPWALL_package_delete() {
	
    try {
		global $wpdb;
		$json		= array();
		$post		= stripslashes_deep($_POST);
		$tblName	= $wpdb->prefix . 'FLIPWALL_packages';
		$postIDs	= isset($post['FLIPWALL_delid']) ? $post['FLIPWALL_delid'] : null;
		$list		= explode(",", $postIDs);
		$delCount	= 0;
		
        if ($postIDs != null) {
            
            foreach ($list as $id) {
				$getResult = $wpdb->get_results("SELECT name, hash FROM `{$tblName}` WHERE id = {$id}", ARRAY_A);
				if ($getResult) {
					$row		=  $getResult[0];
					$nameHash	= "{$row['name']}_{$row['hash']}";
					$delResult	= $wpdb->query("DELETE FROM `{$tblName}` WHERE id = {$id}");
					if ($delResult != 0) {
						//Perms
						@chmod(SLFW_Util::SafePath(FLIPWALL_SSDIR_PATH_TMP . "/{$nameHash}_archive.zip"), 0644);
						@chmod(SLFW_Util::SafePath(FLIPWALL_SSDIR_PATH_TMP . "/{$nameHash}_database.sql"), 0644);
						@chmod(SLFW_Util::SafePath(FLIPWALL_SSDIR_PATH_TMP . "/{$nameHash}_installer.php"), 0644);						
						@chmod(SLFW_Util::SafePath(FLIPWALL_SSDIR_PATH . "/{$nameHash}_archive.zip"), 0644);
						@chmod(SLFW_Util::SafePath(FLIPWALL_SSDIR_PATH . "/{$nameHash}_database.sql"), 0644);
						@chmod(SLFW_Util::SafePath(FLIPWALL_SSDIR_PATH . "/{$nameHash}_installer.php"), 0644);
						@chmod(SLFW_Util::SafePath(FLIPWALL_SSDIR_PATH . "/{$nameHash}_scan.json"), 0644);
						@chmod(SLFW_Util::SafePath(FLIPWALL_SSDIR_PATH . "/{$nameHash}.log"), 0644);
						//Remove
						@unlink(SLFW_Util::SafePath(FLIPWALL_SSDIR_PATH_TMP . "/{$nameHash}_archive.zip"));
						@unlink(SLFW_Util::SafePath(FLIPWALL_SSDIR_PATH_TMP . "/{$nameHash}_database.sql"));
						@unlink(SLFW_Util::SafePath(FLIPWALL_SSDIR_PATH_TMP . "/{$nameHash}_installer.php"));
						@unlink(SLFW_Util::SafePath(FLIPWALL_SSDIR_PATH . "/{$nameHash}_archive.zip"));
						@unlink(SLFW_Util::SafePath(FLIPWALL_SSDIR_PATH . "/{$nameHash}_database.sql"));
						@unlink(SLFW_Util::SafePath(FLIPWALL_SSDIR_PATH . "/{$nameHash}_installer.php"));
						@unlink(SLFW_Util::SafePath(FLIPWALL_SSDIR_PATH . "/{$nameHash}_scan.json"));
						@unlink(SLFW_Util::SafePath(FLIPWALL_SSDIR_PATH . "/{$nameHash}.log"));
						//Unfinished Zip files
						$tmpZip = FLIPWALL_SSDIR_PATH_TMP . "/{$nameHash}_archive.zip.*";
						array_map('unlink', glob($tmpZip));
						@unlink(SLFW_Util::SafePath());
						$delCount++;
					} 
				}
            }
        }

    } catch (Exception $e) {
		$json['error'] = "{$e}";
        die(json_encode($json));
    }
	
	$json['ids'] = "{$postIDs}";
	$json['removed'] = $delCount;
    die(json_encode($json));
}

//DO NOT ADD A CARRIAGE RETURN BEYOND THIS POINT (headers issue)!!
?>