<?php
/*
Plugin Name: VibeDrive
Plugin URI: https://vibethemes.com/vibedrive
Description: Create a Drive for BuddyPress, Members, Groups and others
Version: 1.3
Requires at least: WP 3.8, BuddyPress 1.9 
Tested up to: 5.8.2
License: GPLv2 or later (LICENSE.txt)
Author: VibeThemes
Author URI: http://www.VibeThemes.com
Text Domain: vibedrive
Domain Path: /languages/

*/
if ( !defined( 'ABSPATH' ) ) exit; 

if(!defined('VIBEDRIVE_VERSION')){
    define('VIBEDRIVE_VERSION','1.3');
}

if(!defined('VIBEDRIVE_SETTINGS_OPTION')){
    define('VIBEDRIVE_SETTINGS_OPTION','vibedrive');
}

include_once 'includes/class.actions.php';
include_once 'includes/class.admin.php';
include_once 'includes/class.api.php';
include_once 'includes/class.filters.php';
include_once 'includes/functions.php';
include_once 'includes/class.init.php';
include_once 'includes/autoupdate.php';




add_action('plugins_loaded','vibedrive_translations');
function vibedrive_translations(){
    $locale = apply_filters("plugin_locale", get_locale(), 'vibedrive');
    $lang_dir = dirname( __FILE__ ) . '/languages/';
    $mofile        = sprintf( '%1$s-%2$s.mo', 'vibedrive', $locale );
    $mofile_local  = $lang_dir . $mofile;
    $mofile_global = WP_LANG_DIR . '/plugins/' . $mofile;

    if ( file_exists( $mofile_global ) ) {
        load_textdomain( 'vibedrive', $mofile_global );
    } else {
        load_textdomain( 'vibedrive', $mofile_local );
    }  
}




add_action( 'init', 'vibebp_vibedrive_plugin_update' );
function vibebp_vibedrive_plugin_update() {
    /* Load Plugin Updater */
    require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'includes/autoupdate.php' );

    /* Updater Config */
    $config = array(
        'base'      => plugin_basename( __FILE__ ), //required
        'dashboard' => true,
        'repo_uri'  => 'https://wplms.io/',  //required
        'repo_slug' => 'vibe-drive',  //required
    );

    /* Load Updater Class */
    new VibeBp_Vibedrive_Auto_Update( $config );
}
