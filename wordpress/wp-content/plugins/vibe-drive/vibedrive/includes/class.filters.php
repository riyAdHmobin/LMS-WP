<?php
/**
 * VibeDrive Filter hooks
 *
 * @author 		VibeThemes
 * @category 	Init
 * @package 	vibedrive/Includes
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VibeDrive_Filters{

	public static $instance;
    
    public static function init(){

        if ( is_null( self::$instance ) )
            self::$instance = new VibeDrive_Filters();
        return self::$instance;
    }

	private function __construct(){

		add_filter('vibedrive_include_scripts',array($this,'inclulde_vibedrive_script'));
		add_filter('vibebp_group_tabs',array($this,'add_group_drive'));
		add_filter('vibebp_component_icon',array($this,'set_icon'),10,2);
	
	}

	function set_icon($icon,$component_name){
		if($component_name == 'mydrive'){
			return '<svg width="100%" height="100%" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;">
    <path d="M24,16L0,16L5,2L19,2L24,16ZM8,10L12,14L16,10L13,10L13,5L11,5L11,10L8,10Z" style="fill-opacity:0.65;fill-rule:nonzero;"/>
    <path d="M19,18.5C19,18.224 19.224,18 19.5,18C19.776,18 20,18.224 20,18.5C20,18.776 19.776,19 19.5,19C19.224,19 19,18.776 19,18.5ZM24,16L24,22L0,22L0,16L0.508,14.579L23.501,14.602L24,16ZM22,17L2,17L2,20L22,20L22,17Z" style="fill-rule:nonzero;"/>
</svg>';
		}
		return $icon;
	}
	function inclulde_vibedrive_script(){

		if(bp_is_my_profile() && bp_current_action() == 'drive'){
			return true;
		}
		if(defined('VIBEBP_VERSION') && bp_is_my_profile()){
			return true;
		}
	}

	function add_group_drive($tabs){
		$tabs['vibedrive_group_drive']=__('Group Drive','vibedrive');
		return $tabs;
	}
}

VibeDrive_Filters::init();