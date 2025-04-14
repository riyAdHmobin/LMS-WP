<?php
/**
 * PRofile
 *
 * @class       Vibe_Projects_Profile
 * @author      VibeThemes
 * @category    Admin
 * @package     vibecal
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Vibe_Cal_Init{


	public static $instance;
	public static function init(){

        if ( is_null( self::$instance ) )
            self::$instance = new Vibe_Cal_Init();
        return self::$instance;
    }

	private function __construct(){

		add_action('init',array($this,'register_types'));
		add_action( 'bp_setup_nav', array($this,'add_projects_tab'), 100 );
		add_action('wp_enqueue_scripts',array($this,'enqueue_script'));
		add_filter('vibebp_component_icon',array($this,'set_icon'),10,2);
		
		add_action( 'init', array($this,'register_custom_post_type'));
		add_action( 'init', array($this,'create_nonhierarchical_taxonomy'));

		add_filter('vibecal_script_args',array($this,'script_args'));
		add_filter('vibebp_group_tabs',array($this,'vibebp_group_tabs'),10,3);
		add_filter('vibebp_general_settings',array($this,'vibbp_settings'));
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'init_widgets' ] );

		add_filter('wplms_get_course_tabs',array($this,'course_events'));

	
	}

	public function init_widgets() {		
				
		require_once( __DIR__ . '/class.elementor.php' );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Vibe_Cal_Elementor());
			
	}
	function register_types(){

	}

	function course_events($tabs){
		if(function_exists('vibebp_get_setting') && vibebp_get_setting('course_events')){
			$tabs['course_events'] = __('Events','vibecal');
		}
		return $tabs;
	}
	
	function vibbp_settings($settings){

		$settings[]=array(
						'label'=>__('Calendar Settings','vibebp' ),
						'type'=> 'heading',
					);
		$settings[]=array(
				'label' => __('Who can create events','vibecal'),
				'name' => 'create_events',
				'type' => 'select',
				'options'=>array(
					'edit_posts'=>__('Instructor','vibecal'),
					'manage_options'=>__('Administrator','vibecal'),
					'read'=>__('Students','vibecal'),
				)
			);
		if(function_Exists('bp_is_active') && bp_is_active('groups')){
			$settings[]=array(
				'label' => __('Group events','vibecal'),
				'name' => 'group_events',
				'type' => 'checkbox',
				'desc' => __('Enable Group specific events','vibecal'),
				'default'=>''
			);
		}
		$settings[]=array(
			'label' => __('Course events','vibecal'),
			'name' => 'course_events',
			'type' => 'checkbox',
			'desc' => __('Enable Course specific events','vibecal'),
			'default'=>''
		);
		return $settings;
	}
	function script_args($args){

		if(function_exists('vibebp_get_setting')){
			if(vibebp_get_setting('google_maps_key')){
				$args['settings']['fields'][]=array(
	                'label'=> __('Location','vibecal' ),
	                'type'=> 'location',
	                'style'=>'',
	                'value_type'=>'single',
	                'id' => 'location',
	                'from'=>'meta',
	                'extras' => '',
	            );
			}
			if(vibebp_get_setting('group_events')){
				$args['settings']['fields'][]=array(
	                'label'=> __('Connect to Group','vibecal' ),
	                'type'=> 'selectcpt',
	                'cpt'=>'groups',
	                'style'=>'',
	                'value_type'=>'single',
	                'id' => 'group',
	                'from'=>'meta',
	                'extras' => '',
	            );
			}
			if(vibebp_get_setting('course_events')){
				$args['settings']['fields'][]=array(
	                'label'=> __('Connect to Course','vibecal' ),
	                'type'=> 'selectcpt',
	                'cpt'=>'course',
	                'style'=>'',
	                'value_type'=>'single',
	                'id' => 'course',
	                'from'=>'meta',
	                'extras' => '',
	            );
			}
		}

		return $args;
	}

	function set_icon($icon,$component_name){
		if($component_name == 'calendar'){
			return '<svg width="24" height="24" viewBox="0 0 24 24" version="1.1" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;">
			    <path fill="#fff" d="M20,20L16,20L16,16L20,16L20,20ZM14,10L10,10L10,14L14,14L14,10ZM20,10L16,10L16,14L20,14L20,10ZM8,16L4,16L4,20L8,20L8,16ZM14,16L10,16L10,20L14,20L14,16ZM8,10L4,10L4,14L8,14L8,10Z" style="fill-opacity:0.67;fill-rule:nonzero;"/>
			    <path fill="#fff" d="M24,2L24,24L0,24L0,2L3,2L3,3C3,4.103 3.897,5 5,5C6.103,5 7,4.103 7,3L7,2L17,2L17,3C17,4.103 17.897,5 19,5C20.103,5 21,4.103 21,3L21,2L24,2ZM22,8L2,8L2,22L22,22L22,8ZM20,1C20,0.448 19.553,0 19,0C18.447,0 18,0.448 18,1L18,3C18,3.552 18.447,4 19,4C19.553,4 20,3.552 20,3L20,1ZM6,3C6,3.552 5.553,4 5,4C4.447,4 4,3.552 4,3L4,1C4,0.448 4.447,0 5,0C5.553,0 6,0.448 6,1L6,3Z" style="fill-rule:nonzero;"/>
			</svg>
';
		}
		return $icon;
	}

	function add_projects_tab(){
		global $bp;
		$slug='calendar';
		//Add VibeDrive tab in profile menu
	    bp_core_new_nav_item( array( 
	        'name' => __('Calendar','vibecal'),
	        'slug' => $slug, 
	        'item_css_id' => 'all_articles',
	        'screen_function' => array($this,'show_screen'),
	        'default_subnav_slug' => 'home', 
	        'show_for_displayed_user'=>false,
	        'position' => 58,
	        'user_has_access' => (bp_is_my_profile() || current_user_can('manage_options'))
	    ) );

	    bp_core_new_subnav_item( array(
			'name' 		  => __('My Events','vibecal'),
			'slug' 		  => 'my_events',
			'parent_slug' => $slug,
        	'parent_url' => $bp->displayed_user->domain.$slug.'/',
			'screen_function' => array($this,'show_screen'),
			'user_has_access' => (bp_is_my_profile() || current_user_can('manage_options'))
		) );


		bp_core_new_subnav_item( array(
			'name' 		  => __('My Invites','vibecal'),
			'slug' 		  => 'my_invites',
			'parent_slug' => $slug,
        	'parent_url' => $bp->displayed_user->domain.$slug.'/',
			'screen_function' => array($this,'show_screen'),
			'user_has_access' => (bp_is_my_profile() || current_user_can('manage_options'))
		) );
	
	}

	function show_screen(){

	}

	function enqueue_script(){

		$blog_id = '';
        if(function_exists('get_current_blog_id')){
            $blog_id = get_current_blog_id();
        }

        $cap = 'edit_posts';
        if(function_exists('vibebp_get_setting') && vibebp_get_setting('create_event')){
        	$cap = vibebp_get_setting('create_event');
        }

        $locale = get_locale();
        $locale_key = explode('_',$locale);
        $locale_key= $locale_key[0];
        if(!function_exists('format_code_lang')){
        	require_once( ABSPATH . 'wp-admin/includes/ms.php' );
        }
		$kb=apply_filters('vibecal_script_args',array(
			'api'=>array(
                'url'=>get_rest_url($blog_id,VIBECAL_API_NAMESPACE),
            ),
			'title'=>__('Calendar','vibecal'),
			'shared_sorters'=>array(
				'joined'=>_x('Joined','vibecal'),
                'mine'=>_x('Manage','vibecal'),
            ),
			'shared_tabs'=>array(
				'shared'=>_x('Shared','vibecal'),
				'group'=>_x('Group','vibecal'),
                'course'=>_x('Course','vibecal'),
			),
			'shared_types'=>array(
				'group'=>_x('Group','vibecal'),
                'course'=>_x('Course','vibecal'),
            ),
			'settings'=>array(
            	'new_event_cap'=>[$cap],
				'new_event_category_cap'=>[$cap],
				'new_calendar_cap'=>['edit_posts'], //can edit_posts
            	'weekend'=>5,
				'timeslot'=>15,
				'views'=>array(
					'month'=>__('Month','vibecal'),
					'week'=>__('Week','vibecal'),
					'day'=>__('Day','vibecal')
				),
				'site_langauge'=>array(
					'key'=> $locale_key,
					'label'=>format_code_lang($locale_key)
				),
				'languageCode' => apply_filters('vibefullcalendar_locale_languageCode',''),//forcefully set like 'en' for English
				'fields'=>array(
					array(
                        'label'=> __('Event Title','vibecal' ),
                        'type'=> 'title',
                        'id' => 'post_title',
                        'from'=>'post',
                        'value_type'=>'single',
                        'style'=>'full',
                        'default'=> __('Event Title','vibecal' ),
						),
					array(
						'label'=> __('Event Description','vibecal' ),
						'type'=> 'editor',
						'style'=>'',
						'value_type'=>'single',
						'id' => 'post_content',
						'from'=>'post',
						'extras' => '',
						'default'=> __('Enter a  description about the event.','vibecal' ),
					),
					array(
                        'label'=> __('Event Labels:','vibecal' ),
                        'type'=> 'label',
                        'style'=>'',
                        'value_type'=>'single',
                        'id' => 'event_labels',
                        'from'=>'meta',
                        'extras' => '',
                        'default'=> __('Event Labels.','vibecal' ),
					),
                    array(
                        'label'=> __('Event Color','vibecal' ),
                        'type'=> 'color',
                        'style'=>'',
                        'value_type'=>'single',
                        'id' => 'event_color',
                        'from'=>'meta',
                        'extras' => '',
                        'default'=> __('Event color.','vibecal' ),
					),
                    array(
                        'label'=> __('Start','vibecal' ),
                        'type'=> 'datetime',
                        'style'=>'',
                        'value_type'=>'single',
                        'id' => 'start',
                        'from'=>'meta',
                        'extras' => '',
                    ),
                    array(
                        'label'=> __('End','vibecal' ),
                        'type'=> 'datetime',
                        'style'=>'',
                        'value_type'=>'single',
                        'id' => 'end',
                        'from'=>'meta',
                        'extras' => '',
					),
					array(
                        'label'=> __('Event Type','vibecal' ),
                        'type'=> 'select',
                        'style'=>'',
                        'options'=>array(
                        	array('value'=>'private','label'=>__('Invite Only','vibecal')),
                        	array('value'=>'public','label'=>__('Anyone can join','vibecal'))
                        ),
                        'id' => 'event_type',
                        'from'=>'meta',
                        'extras' => '',
					),
					array(
                        'label'=> __('Recurring Event','vibecal' ),
                        'type'=> 'recurring',
	                    'options'  => array(
	                    	'0'=>__('enable','vibecal' ),
	                    	'1'=>__('disable','vibecal' )),
	                    'style'=>'',
	                    'id' => 'recurring',
	                    'from'=> 'meta',
	                    'default'=>'H',
	                    'is_child'=>true,
                        'from'=>'meta',
                        'extras' => '',
					),
					// array(
					// 	'label'=> __('Google Event','vibecal' ),
					// 	'type'=> 'googleevent',
					// 	'style'=>'',
					// 	'value_type'=>'single',
					// 	'id' => 'googleevent',
					// 	'from'=>'meta',
					// 	'extras' => '',
					// 	'translation' =>  __('Add event','vibecal' ),
					// ),
				)
            ),
            'sorters'=>array(
                'recent'=>_x('Recent','login','vibecal'),
                'alphabetical'=>_x('Alphabetical','login','vibecal'),
            ),
            'translations'=>array(
            	'add_event'=>__('Add Event', 'vibecal'),
            	'edit_event'=>__('Edit Event', 'vibecal'),
				'add_new'=>__('Add New', 'vibecal'),
				'enter_title'=>__('Enter Title', 'vibecal'),
				'start'=>__('Start', 'vibecal'),
				'end'=>__('Ends', 'vibecal'),
				'enter_description'=>__('Enter Description', 'vibecal'),
				'create_event'=>__('Create Event', 'vibecal'),
				'save'=>__('Save', 'vibecal'),
				'cancel'=>__('Cancel', 'vibecal'),
				'more'=>__('More', 'vibecal'),
				'search_here'=>__('Search users...', 'vibecal'),
				'search_users'=>__('Search Users', 'vibecal'),
				'load_more'=>__('Load more events', 'vibecal'),
				'participants'=>__('Participants', 'vibecal'),
				'starts'=>__('Starts', 'vibecal'),
				'ends'=>__('Ends', 'vibecal'),
				'pending_invites'=>__('Pending', 'vibecal'),
				'accepted_invites'=>__('Accepted', 'vibecal'),
				'rejected_invites'=>__('Rejected', 'vibecal'),
				'accept'=>__('Accept', 'vibecal'),
				'reject'=>__('Reject', 'vibecal'),
				'delete'=>__('Delete', 'vibecal'),
				'month'=>__('Month','vibecal'),
				'week'=>__('Week','vibecal'),
				'day'=>__('Day','vibecal'),
				'today' => __('Today','vibecal'),
				'list' => __('List','vibecal'),
				'unlimited' => __('Unlimited','vibecal'),
				'missing_data' => __('Missing data','vibecal'),
				'type_here' => __('Type here','vibecal'),
				'exit_from_event' =>  __('Exit from event','vibecal'),
				'inviter' =>  __('Inviter','vibecal'),
				'send_invite' =>  __('Send Invite','vibecal'),
				'no_invites' =>  __('No Invites Found','vibecal'),
				'start_url'=>  __('Start Url','vibecal'),
				'join_url'=>  __('Join Url','vibecal'),
				'timezone'=>  __('TimeZone','vibecal'),
				'invited_by'=> __('Invited By','vibecal'),
				'send_invites'=> __('Send Invites','vibecal'),
				'invited'=> __('Invited','vibecal'),
				'shared_with'=> __('Shared with','vibecal'),
				'select_sharing_type'=> __('Select sharing type','vibecal'),
				'search_text'=> __('Search...','vibecal'),
				'no_events'=> __('No events found!','vibecal'),
				'view_event'=> __('View','vibecal'),
				'hide_event'=> __('Hide event','vibecal'),
				'every_day'=> __('Everyday','vibecal'),
				'weekly'=> __('Week days','vibecal'),
				'sun'=> __('SUN','vibecal'),
				'mon'=> __('MON','vibecal'),
				'tue'=> __('TUE','vibecal'),
				'wed'=> __('WED','vibecal'),
				'thu'=> __('THU','vibecal'),
				'fri'=> __('FRI','vibecal'),
				'sat'=> __('SAT','vibecal'),
				'meeting'=> __('Meeting','vibecal'),
				'join'=> __('Join','vibecal'),
                'change_to_device_language'=>__('Device','vibecal'),
				'set_langauge'=>__('Set Language','vibecal'),
				'added'=>__('Added','vibecal'),
				'not_added'=>__('Not Added','vibecal'),
				'sync_with_google_cal'=>__('Sync Showing event with Google Calendar','vibecal'),
				'event_synced'=>__('Event synced','vibecal'),
				'meeting'=>__('Google Meet','vibecal'),
				'event_creation_not_enable'=>__('Event Creation not yet enabled','vibecal'),
				'create_event_with_google_meet'=>__('Create Event with Google Meet','vibecal'),
            )
		));
		$enqueue_script = false;

		if(function_exists('bp_is_user') && bp_is_user()){$enqueue_script = true;}
		if(apply_filters('vibebp_enqueue_profile_script',$enqueue_script)){
			//wp_deregister_script('jquery');
    		wp_enqueue_script('fullcalendar',plugins_url('assets/js/fullcalendar.min.js',__FILE__),array(),VIBECAL_VERSION,true);
			wp_enqueue_script('fullcalendar_all_locale',plugins_url('assets/js/locales-all.min.js',__FILE__),array('fullcalendar'),VIBECAL_VERSION,true);
			wp_enqueue_script('vibecal',plugins_url('assets/js/cal.js',__FILE__),array('wp-element','wp-data','fullcalendar'),VIBECAL_VERSION,true);
            wp_localize_script('vibecal','vibecal',$kb);
            wp_enqueue_style('fullcalendar',plugins_url('assets/css/fullcalendar.min.css',__FILE__),array(),VIBECAL_VERSION);
			wp_enqueue_style('vibecal',plugins_url('assets/css/cal.css',__FILE__),array(),VIBECAL_VERSION);
		}
	}

	function register_custom_post_type(){
		register_post_type( VIBECAL_EVENT_POST_TYPE ,apply_filters('vibecal_event_register_post_type',
				array(
					'labels' => array(
						'name' => __('Events','vibecal'),
						'menu_name' => __('Vibe-Events','vibecal'),
						'singular_name' => __('Events','vibecal'),
						'add_new_item' => __('Add New Event','vibecal'),
						'all_items' => __('Events','vibecal')
					),
					'public' => true,
					'publicly_queryable' => true,
					'show_ui' => true,
					'capability_type' => 'post',
					'show_in_admin_bar' => true,
					'show_in_nav_menus' => true,
					'taxonomies' => array( 'events-cat'),
					'supports' => array( 'title','editor','thumbnail','author','comments','excerpt','revisions','custom-fields', 'page-attributes'),
					'hierarchical' => true,
					'rewrite' => array('slug' => 'events'),
					'show_in_menu' => defined('VIBEBP_VERSION')?'vibebp':'projects'
				)
			)
		);
	}

	function create_nonhierarchical_taxonomy() {
 
		register_taxonomy(VIBECAL_EVENT_TAXONOMY,VIBECAL_EVENT_POST_TYPE,array(
			'hierarchical' => true,
			'labels' => array(
					'name' => _x( 'Events Category', 'taxonomy general name' ),
					'singular_name' => _x( 'Category', 'taxonomy singular name' ),
					'search_items' =>  __( 'Search Events Category' ),
					'popular_items' => __( 'Popular Category' ),
					'all_items' => __( 'All Events Category' ),
				),
			'show_ui' => true,
			'show_admin_column' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => VIBECAL_EVENT_POST_TYPE ),
			'show_in_menu' => defined('VIBEBP_VERSION')?'vibebp':'projects'
		));

		register_taxonomy(VIBECAL_EVENT_CALENDAR,VIBECAL_EVENT_POST_TYPE,array(
			'hierarchical' => true,
			'labels' => array(
					'name' => _x( 'Events Calendar', 'taxonomy general name' ),
					'singular_name' => _x( 'Calendar', 'taxonomy singular name' ),
					'search_items' =>  __( 'Search Calendar' ),
					'all_items' => __( 'All Calendars' ),
				),
			'show_ui' => true,
			'show_admin_column' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => VIBECAL_EVENT_POST_TYPE ),
			'show_in_menu' => defined('VIBEBP_VERSION')?'vibebp':'projects'
		));

		
	}

	function vibebp_group_tabs($tabs,$group_id,$user_id){
		if(function_exists('vibebp_get_setting') && vibebp_get_setting('group_events')){
			$tabs['group_events'] = _x('Events','Event tab','vibecal');	
		}
		return $tabs;
	}
}
Vibe_Cal_Init::init();



 

 
