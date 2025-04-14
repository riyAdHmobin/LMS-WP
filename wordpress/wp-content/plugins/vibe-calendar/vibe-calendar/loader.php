<?php
/**
 * Plugin Name: Vibe Calendar
 * Plugin URI: https://vibebp.com
 * Description: Calendar for VibeBP Framework
 * Author: VibeThemes
 * Author URI: https://vibethemes.com
 * Version: 1.4
 * Text Domain: vibecal
 * Domain Path: /languages
 * Tested up to: 5.5.2
 *
 * @package VibeCal
 */

if ( ! defined( 'ABSPATH' ) ) exit;


if( !defined('VIBECAL_SLUG')){
	define( 'VIBECAL_SLUG', 'calendar' ); 
}
if( !defined('VIBECAL_API_NAMESPACE')){
	define( 'VIBECAL_API_NAMESPACE', 'vibecal/v1' ); 
}
if( !defined('VIBECAL_API_TYPE')){
	define( 'VIBECAL_API_TYPE', 'event' ); 
}

if(!defined('VIBECAL_VERSION')){
    define('VIBECAL_VERSION','1.4');
}

defined('VIBECAL_EVENT_POST_TYPE') or define('VIBECAL_EVENT_POST_TYPE', 'vibecal_event');
defined('VIBECAL_EVENT_TAXONOMY') or define('VIBECAL_EVENT_TAXONOMY', 'vibecal_event_category');
defined('VIBECAL_CALENDAR_TAXONOMY') or define('VIBECAL_EVENT_CALENDAR', 'vibecal_event_calendar');

include_once 'functions.php';
include_once 'class.api.php';
include_once 'class.init.php';
include_once 'class.actions.php';

add_action('plugins_loaded','vibe_cal_translations');
function vibe_cal_translations(){
    $locale = apply_filters("plugin_locale", get_locale(), 'vibecal');
    $lang_dir = dirname( __FILE__ ) . '/languages/';
    $mofile        = sprintf( '%1$s-%2$s.mo', 'vibecal', $locale );
    $mofile_local  = $lang_dir . $mofile;
    $mofile_global = WP_LANG_DIR . '/plugins/' . $mofile;

    if ( file_exists( $mofile_global ) ) {
        load_textdomain( 'vibecal', $mofile_global );
    } else {
        load_textdomain( 'vibecal', $mofile_local );
    }  
}


