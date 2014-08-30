<?php
/*
  Plugin Name: Flipwall
  Plugin URI: http://www.SaschaLabs.net/flipwall/
  Description: Create a backup of your WordPress files and database. Duplicate and move an entire site from one location to another in a few steps. Create a full snapshot of your site at any point in time.
  Version: 0.6.0
  Author: Sascha Slusche
  Author URI: http://www.SaschaLabs.net
  License: GPLv2 or later
 */

/* ================================================================================ 
  Copyright 2011-2014  Sascha Slusche

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

  SOURCE CONTRIBUTORS:
  Gaurav Aggarwal
  Jonathan Foote
 ================================================================================ */

require_once("define.php");

if (is_admin() == true) {
	
	require_once 'classes/logging.php';
	require_once 'classes/utility.php';
	require_once 'classes/ui.php';
	require_once 'classes/settings.php';
	require_once 'classes/server.php';
	require_once 'classes/package.php';
	require_once 'classes/package.archive.zip.php';
    require_once 'views/actions.php';
	
    /* ACTIVATION 
      Only called when plugin is activated */
    function FLIPWALL_activate() {

        global $wpdb;
		
		//Only update database on version update
		if (FLIPWALL_VERSION != get_option("FLIPWALL_version_plugin")) {
			$table_name = $wpdb->prefix . "FLIPWALL_packages";
		
			//PRIMARY KEY must have 2 spaces before for dbDelta to work
		   $sql = "CREATE TABLE `{$table_name}` (
			   `id`			BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT  PRIMARY KEY,
			   `name`		VARCHAR(250)	NOT NULL,
			   `hash`		VARCHAR(50)		NOT NULL,
			   `status`		INT(11)			NOT NULL,
			   `created`	DATETIME		NOT NULL DEFAULT '0000-00-00 00:00:00',
			   `owner`		VARCHAR(60)		NOT NULL,
			   `package`	MEDIUMBLOB		NOT NULL,
			    KEY `hash` (`hash`))";

		   require_once(FLIPWALL_WPROOTPATH . 'wp-admin/includes/upgrade.php');
		   @dbDelta($sql);
			
		}

		//WordPress Options Hooks
        update_option('FLIPWALL_version_plugin',  FLIPWALL_VERSION);

        //Setup All Directories
        SLFW_Util::InitSnapshotDirectory();
    }
	

    /* UPDATE 
      register_activation_hook is not called when a plugin is updated
      so we need to use the following function */
    function FLIPWALL_update() {
        if (FLIPWALL_VERSION != get_option("FLIPWALL_version_plugin")) {
            FLIPWALL_activate();
        }
		load_plugin_textdomain('wpflipwall', FALSE, dirname(plugin_basename(__FILE__)) . '/lang/');
    }

    /* DEACTIVATION / UNINSTALL 
	 * Only called when plugin is deactivated.
	 * For uninstall see uninstall.php */
    function FLIPWALL_deactivate() {
        //No actions needed yet
    }

    /* META LINK ADDONS
      Adds links to the plugins manager page */
    function FLIPWALL_meta_links($links, $file) {
        $plugin = plugin_basename(__FILE__);
        // create link
        if ($file == $plugin) {
            $links[] = '<a href="' . FLIPWALL_HELPLINK . '" title="' . __('FAQ', 'wpflipwall') . '" target="_blank">' . __('FAQ', 'wpflipwall') . '</a>';
            $links[] = '<a href="' . FLIPWALL_GIVELINK . '" title="' . __('Partner', 'wpflipwall') . '" target="_blank">' . __('Partner', 'wpflipwall') . '</a>';
            $links[] = '<a href="' . FLIPWALL_CERTIFIED . '" title="' . __('Approved Hosts', 'wpflipwall') . '"  target="_blank">' . __('Approved Hosts', 'wpflipwall') . '</a>';
            return $links;
        }
        return $links;
    }

    //HOOKS 
    //load_plugin_textdomain('wpflipwall', FALSE, dirname(plugin_basename(__FILE__)) . '/lang/');
    register_activation_hook(__FILE__, 'FLIPWALL_activate');
    register_deactivation_hook(__FILE__, 'FLIPWALL_deactivate');

	//ACTIONS
    add_action('plugins_loaded',						'FLIPWALL_update');
    add_action('admin_init',							'FLIPWALL_init');
    add_action('admin_menu',							'FLIPWALL_menu');
	add_action('wp_ajax_FLIPWALL_task_reset',			'FLIPWALL_task_reset');
    add_action('wp_ajax_FLIPWALL_package_scan',		'FLIPWALL_package_scan');
    add_action('wp_ajax_FLIPWALL_package_build',		'FLIPWALL_package_build');
	add_action('wp_ajax_FLIPWALL_package_delete',		'FLIPWALL_package_delete');
	add_action('wp_ajax_FLIPWALL_package_report',		'FLIPWALL_package_report');
	add_action('wp_ajax_SLFW_UI_SaveViewStateByPost',	array('SLFW_UI', 'SaveViewStateByPost'));
	add_action('admin_notices',							array('SLFW_UI', 'ShowReservedFilesNotice'));
	
	//FILTERS
    add_filter('plugin_action_links',					'FLIPWALL_manage_link', 10, 2);
    add_filter('plugin_row_meta',						'FLIPWALL_meta_links', 10, 2);
	

    /**
     *  FLIPWALL_INIT
     *  Init routines  */
    function FLIPWALL_init() {
        /* CSS */
        wp_register_style('jquery-ui', FLIPWALL_PLUGIN_URL . 'assets/css/jquery-ui.css', null, "1.9.2");
		wp_register_style('font-awesome', FLIPWALL_PLUGIN_URL . 'assets/css/font-awesome.min.css', null, '4.0.3' );
        wp_register_style('FLIPWALL_style', FLIPWALL_PLUGIN_URL . 'assets/css/style.css', null, FLIPWALL_VERSION);
		/* JS */
		wp_register_script('parsley', FLIPWALL_PLUGIN_URL . 'assets/js/parsley-standalone.min.js', array('jquery'), '1.1.18');
		
    }
	
	//PAGE VIEWS
    function FLIPWALL_get_menu()	{
		$current_page = isset($_REQUEST['page']) ? esc_html($_REQUEST['page']) : 'flipwall';
		switch ($current_page) {
			case 'flipwall':			 include('views/packages/controller.php');	break;
			case 'flipwall-settings':	 include('views/settings/controller.php');	break;
			case 'flipwall-tools':	 include('views/tools/controller.php');		break;
			case 'flipwall-support':	 include('views/support.php');				break;
		}	
	}

    /**
     *  FLIPWALL_MENU
     *  Loads the menu item into the WP tools section and queues the actions for only this plugin */
    function FLIPWALL_menu() {
		
		$perms = 'import';
		
        //Main Menu
        $main_menu		= add_menu_page('Flipwall Plugin', 'Flipwall', $perms, 'flipwall', 'FLIPWALL_get_menu', plugins_url('flipwall/assets/img/create.png'));
        $page_packages	= add_submenu_page('flipwall',  __('Packages', 'wpflipwall'), __('Packages', 'wpflipwall'), $perms, 'flipwall',			 'FLIPWALL_get_menu');
        $page_settings	= add_submenu_page('flipwall',  __('Settings', 'wpflipwall'), __('Settings', 'wpflipwall'), $perms, 'flipwall-settings', 'FLIPWALL_get_menu');
        $page_tools		= add_submenu_page('flipwall',  __('Tools',	'wpflipwall'),  __('Tools', 'wpflipwall'),	  $perms, 'flipwall-tools',	 'FLIPWALL_get_menu');
		$page_support	= add_submenu_page('flipwall',  __('Support',  'wpflipwall'), __('Support', 'wpflipwall'),  $perms, 'flipwall-support',  'FLIPWALL_get_menu');

        //Apply Scripts
        add_action('admin_print_scripts-' . $page_packages, 'FLIPWALL_scripts');
		add_action('admin_print_scripts-' . $page_settings, 'FLIPWALL_scripts');
		add_action('admin_print_scripts-' . $page_support,  'FLIPWALL_scripts');
		add_action('admin_print_scripts-' . $page_tools,	'FLIPWALL_scripts');

		//Apply Styles
        add_action('admin_print_styles-'  . $page_packages, 'FLIPWALL_styles');
        add_action('admin_print_styles-'  . $page_settings, 'FLIPWALL_styles');
		add_action('admin_print_styles-'  . $page_support,  'FLIPWALL_styles');
		add_action('admin_print_styles-'  . $page_tools,	'FLIPWALL_styles');
    }

    /**
     *  FLIPWALL_SCRIPTS
     *  Loads the required javascript libs only for this plugin  */
    function FLIPWALL_scripts() {
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-progressbar');
		wp_enqueue_script('parsley');
    }

    /**
     *  FLIPWALL_STYLES
     *  Loads the required css links only for this plugin  */
    function FLIPWALL_styles() {
        wp_enqueue_style('jquery-ui');
        wp_enqueue_style('FLIPWALL_style');
		wp_enqueue_style('font-awesome');
    }

    /**
     *  FLIPWALL_MANAGE_LINK
     *  Adds the manage link in the plugins list */
    function FLIPWALL_manage_link($links, $file) {
        static $this_plugin;
        if (!$this_plugin)
            $this_plugin = plugin_basename(__FILE__);

        if ($file == $this_plugin) {
            $settings_link = '<a href="admin.php?page=flipwall">' . __("Manage", 'wpflipwall') . '</a>';
            array_unshift($links, $settings_link);
        }
        return $links;
    }
}
?>