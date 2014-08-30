<?php
//Prevent directly browsing to the file
if (function_exists('plugin_dir_url')) {
    define('FLIPWALL_VERSION',        '0.6.0');
    define("FLIPWALL_HOMEPAGE",       "http://www.SaschaLabs.net/labs/flipwall");
    define("FLIPWALL_GIVELINK",       "http://www.SaschaLabs.net/partner");
    define("FLIPWALL_HELPLINK",       "http://www.SaschaLabs.net/flipwall-docs");
    define("FLIPWALL_CERTIFIED",      "http://www.SaschaLabs.net/flipwall-hosts");
    define('FLIPWALL_PLUGIN_URL',     plugin_dir_url(__FILE__));
	define('FLIPWALL_SITE_URL',		get_site_url());
    

    /* Paths should ALWAYS read "/"
      uni: /home/path/file.txt
      win:  D:/home/path/file.txt
      SSDIR = SnapShot Directory */
    if (!defined('ABSPATH')) {
		define('ABSPATH', dirname(__FILE__));
    }
	
	//PATH CONSTANTS
	define("FLIPWALL_SSDIR_NAME",     'wp-snapshots');
	define('FLIPWALL_PLUGIN_PATH',    str_replace("\\", "/", plugin_dir_path(__FILE__)));
    define('FLIPWALL_WPROOTPATH',     str_replace("\\", "/", ABSPATH));
    define("FLIPWALL_SSDIR_PATH",     str_replace("\\", "/", FLIPWALL_WPROOTPATH . FLIPWALL_SSDIR_NAME));
	define("FLIPWALL_SSDIR_PATH_TMP", FLIPWALL_SSDIR_PATH . '/tmp');
	define("FLIPWALL_SSDIR_URL",      FLIPWALL_SITE_URL . "/" . FLIPWALL_SSDIR_NAME);
    define("FLIPWALL_INSTALL_PHP",    'installer.php');
	define("FLIPWALL_INSTALL_BAK",    'installer-backup.php');
    define("FLIPWALL_INSTALL_SQL",    'installer-data.sql');
    define("FLIPWALL_INSTALL_LOG",    'installer-log.txt');
	
	//RESTRAINT CONSTANTS
    define("FLIPWALL_PHP_MAX_MEMORY",  '5000M');
    define("FLIPWALL_DB_MAX_TIME",     5000);
	define("FLIPWALL_SCAN_SITE",    157286400);	//150MB
	define("FLIPWALL_SCAN_WARNFILESIZE", 4194304);//4MB
	define("FLIPWALL_SCAN_CACHESIZE", 524288);	//512K
	define("FLIPWALL_SCAN_DBSIZE",  52428800);	//50MB
	define("FLIPWALL_SCAN_DBROWS",  250000);
	define("FLIPWALL_SCAN_TIMEOUT", 300);			//Seconds
	define("FLIPWALL_SCAN_MIN_WP", "3.7.0");
	define("FLIPWALL_SCAN_USELEGACY", true);
    $GLOBALS['FLIPWALL_SERVER_LIST'] = array('Apache','LiteSpeed', 'Nginx', 'Lighttpd', 'IIS', 'WebServerX', 'uWSGI');
	$GLOBALS['FLIPWALL_OPTS_DELETE'] = array('FLIPWALL_ui_view_state', 'FLIPWALL_package_active', 'FLIPWALL_settings');
	
	/* Used to flush a response every N items. 
	 * Note: This value will cause the Zip file to double in size durning the creation process only*/
	define("FLIPWALL_ZIP_FLUSH_TRIGGER", 1000);

} else {
    error_reporting(0);
    $port = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] != "off") ? "https://" : "http://";
    $url = $port . $_SERVER["HTTP_HOST"];
    header("HTTP/1.1 404 Not Found", true, 404);
    header("Status: 404 Not Found");
    exit();
}
?>