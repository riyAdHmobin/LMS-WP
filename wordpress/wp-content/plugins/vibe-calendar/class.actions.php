<?php
/**
 * Initialise WPLMS Calendar
 *
 * @class       VibeCal_Actions
 * @author      VibeThemes
 * @category    Admin
 * @package     WPLMS-Calendar/classes
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class VibeCal_Actions{

	public static $instance;
	
	public static function init(){
        if ( is_null( self::$instance ) )
            self::$instance = new VibeCal_Actions();
        return self::$instance;
    }

	private function __construct(){
        if(function_exists('bp_activity_add')){
            add_action('vibe_calendar_shared_connected',array($this,'vibe_calendar_shared_connected'),10,4);
        }
       
    }
    
    function vibe_calendar_shared_connected($post_id,$user_id,$shared_type,$shared_values){
        $type = 'vibecal_connected_'.$shared_type;
        switch ($shared_type) {
            case 'course':
                    foreach ($shared_values as $key => $shared_value) {
                        bp_activity_add( 
                            apply_filters('bp_course_record_activity',array( 
                                    'user_id' => $user_id, 
                                    'action' => __('Course connected with Calendar event','vibecal'),
                                    'content' => sprintf(__('Course is connected with Calendar event %s','vibecal'),get_the_title($post_id)),
                                    'primary_link' => get_permalink($post_id), 
                                    'component' => $this->get_component_name($post_id,$user_id),
                                    'type' => $type, 
                                    'item_id' => $shared_value, 
                                    'secondary_item_id' => $post_id,
                                ) 
                            )
                        );
                    }
                break;
            case 'group':
					foreach ($shared_values as $key => $shared_value) {
						bp_activity_add( 
							apply_filters('bp_group_record_activity',array( 
									'user_id' => $user_id, 
									'action' => __('Group connected with Calendar event','vibecal'),
									'content' => sprintf(__('Group is connected with Calendar event %s','vibecal'),get_the_title($post_id)),
									'primary_link' => get_permalink($post_id), 
									'component' => $this->get_component_name($post_id,$user_id),
									'type' => $type, 
									'item_id' => $shared_value, 
									'secondary_item_id' => $post_id,
								) 
							)
						);
					}
                break;
            default:
                break;
        }
        
	}

 
    function get_component_name($post_id,$user_id){
        $component = 'vibecal';
        $shared_type = get_post_meta($post_id,'shared_type',true);
        if(!empty($shared_type)){
            switch ($shared_type) {
                case 'course':
                        $component = 'course';
                    break;
                case 'group':
                        $component = 'groups';
                    break; 
                case 'shared':
                        $component = 'shared';
                    break;
            }
        }
        return $component;
    }
	
}

add_action('bp_include',function(){
    VibeCal_Actions::init();
});

