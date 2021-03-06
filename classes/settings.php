<?php
if ( ! defined( 'FLIPWALL_VERSION' ) ) exit; // Exit if accessed directly


class SLFW_Settings
{
	
	const OPT_SETTINGS = 'FLIPWALL_settings';

	public static $Data;
	public static $Version = FLIPWALL_VERSION;

	/**
	*  Class used to manage all the settings for the plugin
	*/
	static function init() {
		self::$Data = get_option(self::OPT_SETTINGS);

		//when the plugin updated, this will be true
		if (empty(self::$Data) || self::$Version > self::$Data['version']){
			self::SetDefaults();
		}
	}

	/**
	*  Find the setting value
	*  @param string $key	The name of the key to find
	*  @return The value stored in the key returns null if key does not exist
	*/
	public static function Get($key = '') {
		return isset(self::$Data[$key]) ? self::$Data[$key] : null;
	}

	/**
	*  Set the settings value in memory only
	*  @param string $key		The name of the key to find
	*  @param string $value		The value to set
	*  remarks:	 The Save() method must be called to write the Settings object to the DB
	*/
	public static function Set($key = '', $value) {
		if (isset(self::$Data[$key])) {
			self::$Data[$key] = ($value == null) ? '' : $value;
		} elseif (!empty($key)) {
			self::$Data[$key] = ($value == null) ? '' : $value;
		}
	}

	/**
	*  Saves all the setting values to the database
	*  @return True if option value has changed, false if not or if update failed.
	*/
	public static function Save() {
		return update_option(self::OPT_SETTINGS, self::$Data);
	}

	/**
	*  Deletes all the setting values to the database
	*  @return True if option value has changed, false if not or if update failed.
	*/
	public static function Delete() {
		return delete_option(self::OPT_SETTINGS);
	}

	/**
	*  Sets the defaults if they have not been set
	*  @return True if option value has changed, false if not or if update failed.
	*/
	public static function SetDefaults() {
		$default = array();
		$default['version'] = self::$Version;

		//Flag used to remove the wp_options value FLIPWALL_settings which are all the settings in this class
		$default['uninstall_settings'] = isset(self::$Data['uninstall_settings']) ? self::$Data['uninstall_settings'] : true;

		//Flag used to remove entire wp-snapshot directory
		$default['uninstall_files']    = isset(self::$Data['uninstall_files'])  ? self::$Data['uninstall_files']  : true;

		//Flag used to remove all tables
		$default['uninstall_tables']   = isset(self::$Data['uninstall_tables']) ? self::$Data['uninstall_tables'] : true;

		//Flag used to show debug info
		$default['package_debug']   = isset(self::$Data['package_debug']) ? self::$Data['package_debug'] : false;
		
		//Flag used to enable mysqldump
		$default['package_mysqldump']   = isset(self::$Data['package_mysqldump']) ? self::$Data['package_mysqldump'] : false;
		
		//Optional mysqldump search path
		$default['package_mysqldump_path']   = isset(self::$Data['package_mysqldump_path']) ? self::$Data['package_mysqldump_path'] : '';
		
		//Optional mysqldump search path
		$default['package_zip_flush']   = isset(self::$Data['package_zip_flush']) ? self::$Data['package_zip_flush'] : false;
		
		//Flag for .htaccess file
		$default['storage_htaccess_off']   = isset(self::$Data['storage_htaccess_off']) ? self::$Data['storage_htaccess_off'] : false;

		self::$Data = $default;
		return self::Save();

	}
	
	/**
	*  LegacyClean: Cleans up legacy data
	*/
	public static function LegacyClean() {
		global $wpdb;

		//PRE 5.0
		$table = $wpdb->prefix."flipwall";
		$wpdb->query("DROP TABLE IF EXISTS $table");
		delete_option('FLIPWALL_pack_passcount'); 
		delete_option('FLIPWALL_add1_passcount'); 
		delete_option('FLIPWALL_add1_clicked'); 
		delete_option('FLIPWALL_options'); 
		
		//PRE 5.1
		//Next version here if needed
	}
	
	/**
	*  DeleteWPOption: Cleans up legacy data
	*/
	public static function DeleteWPOption($optionName) {
		
		if ( in_array($optionName, $GLOBALS['FLIPWALL_OPTS_DELETE']) ) {
			return delete_option($optionName); 
		}
		return false;
	}

}

//Init Class
SLFW_Settings::init();

?>