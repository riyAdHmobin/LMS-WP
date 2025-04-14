<?php
/**
 * VibeDrive Action Hooks
 *
 * @author 		VibeThemes
 * @category 	Init
 * @package 	vibedrive/Includes
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if( !defined('VIBE_PLUGIN_URL')){
    define('VIBE_PLUGIN_URL',plugins_url());
}
if( !defined('VIBETHEMES_URL')){
	define( 'VIBETHEMES_URL', 'https://vibethemes.com' ); 
}
if( !defined('BP_VIBE_DRIVE_SLUG')){
	define( 'BP_VIBE_DRIVE_SLUG', 'mydrive' ); 
}
if( !defined('BP_VIBE_DRIVE_SHARED_SLUG')){
	define( 'BP_VIBE_DRIVE_SHARED_SLUG', 'shared' ); 
}


class VibeDrive_Actions{

	public static $instance;
    
    public static function init(){

        if ( is_null( self::$instance ) )
            self::$instance = new VibeDrive_Actions();
        return self::$instance;
    }

	private function __construct(){

		add_action( 'bp_setup_nav', array($this,'add_vibedrive_tab'), 100 );
		add_action( 'bp_init', array($this,'setup_group_nav') );

		add_action('wp_enqueue_scripts',array($this,'vibedrive_scripts'));
	}


	function add_vibedrive_tab(){

		global $bp;
		//Add VibeDrive tab in profile menu
	    bp_core_new_nav_item( array( 
	        'name' => __('VibeDrive','vibedrive'),
	        'slug' => BP_VIBE_DRIVE_SLUG, 
	        'item_css_id' => 'vibedrive',
	        'show_for_displayed_user'=>false,
	        'screen_function' => array($this,'show_mydrive'),
	        'default_subnav_slug' => 'personal', 
	        'position' => 20
	    ) );

	    //Add mydrive submenu in gift tab
	    bp_core_new_subnav_item( array(
				'name' 		  => __( 'My Drive', 'vibedrive' ),
				'slug' 		  => 'personal',
				'parent_slug' => BP_VIBE_DRIVE_SLUG,
	        	'parent_url' => $bp->displayed_user->domain.BP_VIBE_DRIVE_SLUG.'/',
				'screen_function' => array($this,'show_mydrive'),
				'user_has_access' => (bp_is_my_profile() || current_user_can('manage_options'))
			) );

	    bp_core_new_subnav_item( array(
				'name' 		  => __( 'Shared with me', 'vibedrive' ),
				'slug' 		  => 'shared',
				'parent_slug' => BP_VIBE_DRIVE_SLUG,
	        	'parent_url' => $bp->displayed_user->domain.BP_VIBE_DRIVE_SLUG.'/',
				'screen_function' => array($this,'show_mydrive'),
				'user_has_access' => (bp_is_my_profile() || current_user_can('manage_options'))
			) );

	    bp_core_new_subnav_item( array(
				'name' 		  => __( 'Shared in Groups', 'vibedrive' ),
				'slug' 		  => 'group',
				'parent_slug' => BP_VIBE_DRIVE_SLUG,
	        	'parent_url' => $bp->displayed_user->domain.BP_VIBE_DRIVE_SLUG.'/',
				'screen_function' => array($this,'show_mydrive'),
				'user_has_access' => (bp_is_my_profile() || current_user_can('manage_options'))
			) );


	}


	function show_mydrive(){

		//Show mydrive content
		add_action( 'bp_template_title', array( $this, 'mydrive_tab_content_title' ) );
		add_action( 'bp_template_content', array($this,'mydrive_tab_content'));
    	bp_core_load_template( 'members/single/plugins');
	}

	function mydrive_tab_content_title(){
		 echo '<h3>'.__("My Drive :","vibedrive").'</h3>';
	}

	function get_item_id(){
		global $bp;
		//print_r($bp);
		//print_r('#####a');
		//echo bp_get_current_group_id();
		switch(bp_current_component()){
			case 'groups':
				//return $bp->groups->group_id;
			return bp_get_current_group_id();
			break;
			default:
				return bp_displayed_user_id();
			break;
		}
	}

	function get_item_name(){
		switch(bp_current_component()){
			case 'groups':
				//return bp_current_item()->id;
			return bp_get_current_group_id();
			break;
			default:
				return bp_core_get_user_displayname(bp_displayed_user_id());
			break;
		}
	}

	function get_item_type(){
		switch(bp_current_component()){
			case 'groups':
				return bp_current_component();
			break;
			default:
				return 'personal';
			break;
		}
	}

	function get_item_image(){

	}

	function vibedrive_scripts(){

		if(bp_current_component() == 'dashboard' || apply_filters('vibebp_enqueue_profile_script',false)){

			$vibe_drive_settings = apply_filters('vibedrive_mydrive_tab_settings',array(
				'apiUrl'=> site_url().'/wp-json/'.VIBE_DRIVE_API_NAMESPACE,
				'settings'=> array(
					'userQuota'=>40,
					'scopes'=>vibe_drive_upload_scopes(),
					'sharing'=>vibe_drive_sharing_scopes(),
					'allowed_files_mime_types' => vibedrive_getFilesMimeTypes(),
					'maximum_upload_size' => 100, //MB =
				),
				'sorters'=>array(
					'recent'=>__('Recently uploaded','vibedrive'),
					'alphabetical'=>__('Alphabetical','vibedrive'),
					'size'=>__('Size','vibedrive'),
				),
				'security'=>vibe_drive_get_security(),
				'translations'=>array(
					'title'=>__("Drive","vibedrive"),
					'file_upload'=>__('Upload File','vibedrive'),
					'uploading'=>__('...uploading','vibedrive'),
					'add_new_file'=>__('Add New File','vibedrive'),
					'file_name'=>__('FileName','vibedrive'),
					'file_size'=>__('Size','vibedrive'),
					'file_access'=>__('Accessibility','vibedrive'),
					'file_action'=>__('Action','vibedrive'),
					'file_edit'=>__('Edit','vibedrive'),
					'file_share'=>__('Share','vibedrive'),
					'file_delete'=>__('Delete','vibedrive'),
					'no_files'=>__('No Files Found','vibedrive'),
					'fileAdded'=>__('File Uploaded.','vibedrive'),
					'select_scope'=>__('Select Scope','vibedrive'),
					'save'=>__('Save','vibedrive'),
					'edit'=>__('Edit','vibedrive'),
					'load_more'=>__('Load more','vibedrive'),
					'placeholder' =>__('Search...','vibedrive'),
					'file_name' =>__('File name','vibedrive'),
					'sort_accordingly' =>__('Sort According to your File Type','vibedrive'),
					'showing' =>__('Showing....','vibedrive'),
					'file_uploaded_successfully' => __('File uploaded successfull','vibedrive'),
					'file_type_not_supported_to_upload' => __('File type not supported to upload!','vibedrive'),
					'maximum_upload_size_is' => __('Maximum upload size is','vibedrive'),			
				)
			));

			/***********************Live**********************/
			wp_enqueue_script('vibedrive',plugins_url('../assets/js/vibedrive.js',__FILE__),array('wp-element'),VIBEDRIVE_VERSION,true);
			wp_localize_script('vibedrive','vibedrive',$vibe_drive_settings);
			wp_enqueue_style('vibedrive',plugins_url('../assets/css/vibedrive.css',__FILE__),array(),VIBEDRIVE_VERSION);	
		}
		
	}


	function mydrive_tab_content(){

		?> 
        <div id="vibedrive" data-type="<?php echo bp_current_component(); ?>" user="<?php echo 
			json_encode(array(
				'id'=> $this->get_item_id(),
				'type'=>$this->get_item_type(), 
				'name'=> $this->get_item_name(),
				'image'=> $this->get_item_image(),
			)); ?>"></div>
        <script>
        	var event = new CustomEvent(props.component.component+'_loaded');
			document.dispatchEvent(event);
		</script>
        <?php

	}



	function shared_tab_content_title(){
		 echo '<h3>'.__("Shared Files :","vibedrive").'</h3>';
	}


	function shared_tab_content(){
		?> 
        <div id="VibeDriveShared"></div>
        <?php
	}


	function setup_group_nav(){
		global $bp; 
		$user_access = false;
		$group_link = '';
		if( bp_is_active('groups') && !empty($bp->groups->current_group) ){
			$group_link = $bp->root_domain . '/' . bp_get_groups_root_slug() . '/' . $bp->groups->current_group->slug . '/';
			$user_access = $bp->groups->current_group->user_has_access;
			bp_core_new_subnav_item( array( 
			'name' => __( 'VibeDrive', 'vibedrive'),
			'slug' => 'vibedrive', 
			'parent_url' => $group_link, 
			'parent_slug' => $bp->groups->current_group->slug,
			'screen_function' => array($this,'bp_group_custom'), 
			'position' => 50, 
			'item_css_id' => 'vibedrive_groups' ,
			'user_has_access' => (bp_is_my_profile() || current_user_can('manage_options'))

			));
		}
	}


	function bp_group_custom() {

		add_action('wp_enqueue_scripts',array($this,'vibedrive_scripts'));


		add_action('bp_template_title', array( $this, 'my_new_group_show_screen_title'));
		add_action('bp_template_content', array( $this, 'my_new_group_show_screen_content'));
		$templates = array('groups/single/plugins.php', 'plugin-template.php');

		if (strstr(locate_template($templates), 'groups/single/plugins.php')) {
			bp_core_load_template(apply_filters('bp_core_template_plugin', 'groups/single/plugins'));
		} else {
			bp_core_load_template(apply_filters('bp_core_template_plugin', 'plugin-template'));
		}

	}


	function my_new_group_show_screen_title() {
		//echo 'Vibedrive Tab Title';
	}

	function my_new_group_show_screen_content() {
		?>

		 <div id="vibedrive" data-type="group"></div>
		 <?php
	}



}

VibeDrive_Actions::init();