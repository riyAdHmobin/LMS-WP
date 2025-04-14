<?php
/**
 * API\
 *
 * @class       Vibe_Projects_API
 * @author      VibeThemes
 * @category    Admin
 * @package     vibecal
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if(!defined('VIBECAL_EVENT_INVITE')){
    define('VIBECAL_EVENT_INVITE','vibecal_event_invite');
}

class Vibe_Cal_API{


	public static $instance;
	public static function init(){

        if ( is_null( self::$instance ) )
            self::$instance = new Vibe_Cal_API();
        return self::$instance;
    }

	private function __construct(){

        add_action('rest_api_init',array($this,'register_api_endpoints'));
        $this->namespace= !empty(VIBECAL_API_NAMESPACE)?VIBECAL_API_NAMESPACE:'vibecal/v1';
        $this->type = !empty(VIBECAL_API_TYPE)?VIBECAL_API_TYPE:'event';
	}


	function register_api_endpoints(){
        
        register_rest_route( $this->namespace, '/'. $this->type .'/add_event', array(
            'methods'                   =>   'POST',
            'callback'                  =>  array( $this, 'add_event' ),
            'permission_callback' => array( $this, 'get_user_create_permissions_check' ),
        ) );

        register_rest_route( $this->namespace, '/'. $this->type .'/drag', array(
            'methods'                   =>   'POST',
            'callback'                  =>  array( $this, 'drag_event' ),
            'permission_callback' => array( $this, 'get_user_create_permissions_check' ),
        ) );

        register_rest_route( $this->namespace, '/'. $this->type .'/getPublicEvents', array(
            'methods'                   =>   'POST',
            'callback'                  =>  array( $this, 'get_public_events' ),
            'permission_callback' => array( $this, 'get_public_permissions_check' ),
        ) );

        register_rest_route( $this->namespace, '/'. $this->type .'/getEvents', array(
            'methods'                   =>   'POST',
            'callback'                  =>  array( $this, 'get_events' ),
            'permission_callback' => array( $this, 'get_user_permissions_check' ),
        ) );

        register_rest_route( $this->namespace, '/'. $this->type .'/getEvent', array(
            'methods'                   =>   'POST',
            'callback'                  =>  array( $this, 'get_event' ),
            'permission_callback' => array( $this, 'get_user_permissions_check' ),
        ) );

        register_rest_route( $this->namespace, '/'. $this->type .'/event_users', array(
            'methods'                   =>   'POST',
            'callback'                  =>  array( $this, 'get_event_users' ),
            'permission_callback' => array( $this, 'get_user_permissions_check' ),
        ) );

        register_rest_route( $this->namespace, '/'. $this->type .'/delete', array(
            'methods'                   =>   'POST',
            'callback'                  =>  array( $this, 'delete_event' ),
            'permission_callback' => array( $this, 'get_user_create_permissions_check' ),
        ) );

        register_rest_route( $this->namespace, '/'. $this->type .'/hide', array(
            'methods'                   =>   'POST',
            'callback'                  =>  array( $this, 'hide_event' ),
            'permission_callback' => array( $this, 'get_user_create_permissions_check' ),
        ) );

        register_rest_route( $this->namespace, '/'. $this->type .'/remove_user', array(
            'methods'                   =>   'POST',
            'callback'                  =>  array( $this, 'remove_event_user' ),
            'permission_callback' => array( $this, 'get_user_create_permissions_check' ),
        ) );

        register_rest_route( $this->namespace, '/'. $this->type .'/event_invites', array(
            'methods'                   =>   'POST',
            'callback'                  =>  array( $this, 'get_invited_users' ),
            'permission_callback' => array( $this, 'get_user_permissions_check' ),
        ) );

        // issue
        register_rest_route( $this->namespace, '/'. $this->type .'/event_taxonomy', array(
            'methods'                   =>   'POST',
            'callback'                  =>  array( $this, 'get_event_taxonomy' ),
            'permission_callback' => array( $this, 'get_user_permissions_check' ),
        ) );


        register_rest_route( $this->namespace, '/'. $this->type .'/send_invite', array(
            'methods'                   =>   'POST',
            'callback'                  =>  array( $this, 'send_invite' ),
            'permission_callback' => array( $this, 'get_user_create_permissions_check' ),
        ) );
        
        register_rest_route( $this->namespace, '/'. $this->type .'/remove_invite', array(
            'methods'                   =>   'POST',
            'callback'                  =>  array( $this, 'remove_invite' ),
            'permission_callback' => array( $this, 'get_user_create_permissions_check' ),
        ) );

        register_rest_route( $this->namespace, '/'. $this->type .'/invite/my_invites', array(
            'methods'                   =>   'POST',
            'callback'                  =>  array( $this, 'my_invites' ),
            'permission_callback' => array( $this, 'get_user_permissions_check' ),
        ) );

        register_rest_route( $this->namespace, '/'. $this->type .'/invite/action_invite', array(
            'methods'                   =>   'POST',
            'callback'                  =>  array( $this, 'action_invite' ),
            'permission_callback' => array( $this, 'get_user_permissions_check' ),
        ) );

        register_rest_route( $this->namespace, '/'. $this->type .'/label/all_labels', array(
            'methods'                   =>   'POST',
            'callback'                  =>  array( $this, 'get_all_labels' ),
            'permission_callback' => array( $this, 'get_user_permissions_check' ),
        ) );

        register_rest_route( $this->namespace, '/'. $this->type .'/event/add_label', array(
            'methods'                   =>   'POST',
            'callback'                  =>  array( $this, 'add_event_label' ),
            'permission_callback' => array( $this, 'get_user_create_permissions_check' ),
        ) );

        register_rest_route( $this->namespace, '/'. $this->type .'/event/remove_label', array(
            'methods'                   =>   'POST',
            'callback'                  =>  array( $this, 'remove_event_label' ),
            'permission_callback' => array( $this, 'get_user_create_permissions_check' ),
        ) );

        register_rest_route( $this->namespace, '/'. $this->type .'/label/new_label', array(
            'methods'                   =>   'POST',
            'callback'                  =>  array( $this, 'add_new_label' ),
            'permission_callback' => array( $this, 'get_user_create_permissions_check' ),
        ) );

        register_rest_route( $this->namespace, '/'. $this->type .'/label/remove', array(
            'methods'                   =>   'POST',
            'callback'                  =>  array( $this, 'remove_label' ),
            'permission_callback' => array( $this, 'get_user_permissions_check' ),
        ) );

        register_rest_route( $this->namespace, '/taxonomy', array(
            'methods'                   =>   'POST',
            'callback'                  =>  array( $this, 'get_taxonomy' ),
            'permission_callback' => array( $this, 'get_user_permissions_check' ),
        ) );

        register_rest_route( $this->namespace, '/selectcpt/(?P<cpt>\w+)', array(
            array(
                'methods'             =>  'POST',
                'callback'            =>  array( $this, 'selectcpt' ),
                'permission_callback'       => array( $this, 'get_user_permissions_check' ),
                'args'                      =>  array(
                'cpt'                        =>  array(
                    'validate_callback'     =>  function( $param, $request, $key ) {
                                                return !empty( $param );
                                            }
                    ),
                ),
            ),
        ));

        register_rest_route( $this->namespace, '/component/get_group/', array(
            array(
                'methods'             =>  'POST',
                'callback'            =>  array( $this, 'get_group_component' ),
                'permission_callback'       => array( $this, 'get_user_permissions_check' ),
            ),
        ));
        register_rest_route( $this->namespace, '/product/(?P<id>\d+)', array(
            array(
                'methods'             =>  'POST',
                'callback'            =>  array( $this, 'get_product' ),
                'permission_callback'       => array( $this, 'get_user_permissions_check' ),
                'args'                      =>  array(
                'id'                        =>  array(
                    'validate_callback'     =>  function( $param, $request, $key ) {
                                                return is_numeric( $param );
                                            }
                    ),
                ),
            ),
        ));

        register_rest_route( $this->namespace, '/'. $this->type .'/getEvents/eventon', array(
            'methods'                   =>   'POST',
            'callback'                  =>  array( $this, 'get_events_eventon' ),
            'permission_callback' => array( $this, 'get_user_permissions_check' ),
        ) );

        register_rest_route( $this->namespace, '/'. $this->type .'/getEvents/vibezoom', array(
            'methods'                   =>   'POST',
            'callback'                  =>  array( $this, 'get_events_vibezoom' ),
            'permission_callback' => array( $this, 'get_user_permissions_check' ),
        ) );
            
        //google syncing start
        register_rest_route( $this->namespace, '/google-cal-details', array(
            'methods'                   =>   'POST',
            'callback'                  =>  array( $this, 'get_google_cal_details' ),
            'permission_callback' => array( $this, 'get_user_permissions_check' ),
        ) );

        register_rest_route( $this->namespace, '/'. 'user'.'/synced-events', array(
            array(
                'methods'             =>  'POST',
                'callback'            =>  array( $this, 'get_synced_events' ),
                'permission_callback' => array( $this, 'get_user_permissions_check' ),
            ),
        ));

        register_rest_route( $this->namespace, '/user/set-synced-event', array(
            array(
                'methods'             =>  'POST',
                'callback'            =>  array( $this, 'set_synced_event' ),
                'permission_callback' => array( $this, 'get_user_permissions_check' ),
            ),
        ));
        //google syncing end
    }

    function get_public_permissions_check($request){
        
        $body = json_decode($request->get_body(),true);
        $client_id = $request->get_param('client_id');
        if($client_id == vibebp_get_setting('client_id')){
            return true;
        }
        return false;
    }

    function get_user_permissions_check($request){
        
        $body = json_decode($request->get_body(),true);
        
        if (empty($body['token'])){
            $client_id = $request->get_param('client_id');
            if($client_id == vibebp_get_setting('client_id')){
                return true;
            }
        }else{
            $token = $body['token'];
        }

        if(!empty($body['token'])){
            
            $this->user = apply_filters('vibebp_api_get_user_from_token','',$body['token']);
            if(!empty($this->user)){
                return true;
            }
        }

        return false;
    }

    function user_upload_permissions_check($request){

        $body = json_decode(stripslashes($_POST['body']),true);

        if (empty($body['token'])){
            $client_id = $request->get_param('client_id');
            if($client_id == vibebp_get_setting('client_id')){
                return true;
            }
        }else{
            $token = $body['token'];
        }
        if(!empty($body['token'])){
            global $wpdb;

            $this->user = apply_filters('vibebp_api_get_user_from_token','',$body['token']);
            if(!empty($this->user)){
                return true;
            }
        }

        return false;
    }

    function get_user_create_permissions_check($request){
        $body = json_decode($request->get_body(),true);
        if(!empty($body['token'])){
            global $wpdb;
            $this->user = apply_filters('vibebp_api_get_user_from_token','',$body['token']);
            if(!empty($this->user)){
                if(user_can($this->user->id,'edit_posts')){ //<---- additional security for creating , deleting and other things impacting database
                    return true;    
                }
            }
        }
        return false;
    }

    function can_edit_event($post_id,$user){
        $user_id = $user->id;
        if($user_id == get_post_field('post_author',$post_id)){
            return true;
        }
        return false;
    }

    function get_user_by_id($id){
        if(empty($this->users[$id])){
            $this->users[$id] = array(
                'user_id'=>$id
            );
        }
        return $this->users[$id];
    }

    function add_event($request){
        
        $raw_post = json_decode($request->get_body(),true);
        $body = $raw_post['event'];
        $cpt= VIBECAL_EVENT_POST_TYPE;
        
        $return = array();
        $return = array('status'=>false,'message'=>__('Not saved.','vibecal'));
        
      
        if(empty($body['id'] )){
            $args = apply_filters('vibecal_create_event',array(
                'post_type' => $cpt,
                'post_title' => $body['post_title'],
                'post_content' => $body['post_content'],
                'post_status'=>'publish',
                'post_author'=>$this->user->id
            ),$body);
            $id = wp_insert_post($args);
            if(!empty($id)){
                add_post_meta($id,'event_user',$this->user->id);
            }
        }else{ 
            $id =$body['id'];
            if(empty($this->can_edit_event($id,$this->user))){
                return new WP_REST_Response( array('status'=>false,'message'=>__('Can not make changes.','vibecal')), 200 );
            }
            if(!empty($body['post_title']) || !empty($body['post_content'])){
                $args = apply_filters('wplms_front_end_create_curriculum',array(
                    'ID'=>$body['id'],
                    'post_type' => $cpt,
                    'post_title' => $body['post_title'],
                    'post_content' => $body['post_content'],
                    'post_status'=>'publish',
                    'post_author'=>$this->user->id
                ));
                $id = wp_update_post($args);
            }
        }
        
        if(!empty($id)){

            if(empty($this->can_edit_event($id,$this->user))){
                return new WP_REST_Response( array('status'=>false,'message'=>__('Can not make changes.','vibecal')), 200 );
            }

            if(!empty($body['raw'])){
                update_post_meta($id,'raw',$body['raw']);
            }
            $start = $end = time();
            if(!empty($body['meta']) && count($body['meta'])){
                foreach ($body['meta'] as  $meta) {
                    if($meta['meta_key'] == 'start'){
                        $start = $meta['meta_value'];
                    }
                    if($meta['meta_key'] == 'end'){
                        $end = $meta['meta_value'];
                    }
                    if($meta['meta_key'] == 'color'){
                        $meta['meta_key'] = 'event_color';
                    }
                    if(isset($meta['meta_value'])){
                        if($meta['meta_key'] == 'event_labels'){
                            if(count($meta['meta_value'])){
                                delete_post_meta($id,$meta['meta_key']);
                                foreach ($meta['meta_value'] as  $value) { add_post_meta($id,$meta['meta_key'],$value); }
                            }else{
                                delete_post_meta($id,$meta['meta_key']);
                            }
                        }else{
                            update_post_meta($id,$meta['meta_key'],$meta['meta_value']);
                        }
                    }else{
                        delete_post_meta($id,$meta['meta_key']);
                    }
                }
            }
            if(!empty($body['taxonomy']) && count($body['taxonomy'])){
                $_cat_ids = array();
                foreach ($body['taxonomy'] as  $taxonomy) {
                    if(!empty($taxonomy['value'])){
                        foreach($taxonomy['value'] as $k=>$cat_id){
                            if(!is_numeric($cat_id) && strpos($cat_id, 'new_') === 0){
                                $new_cat = explode('new_',$cat_id);
                                $cid = wp_insert_term($new_cat[1],$taxonomy['taxonomy']);
                                if(is_array($cid)){
                                    $taxonomy['value'][$k] = $cid['term_id'];
                                }else{
                                    unset($taxonomy['value'][$k]);
                                }
                            }
                        }
                        wp_set_object_terms( $id, $taxonomy['value'], $taxonomy['taxonomy'] );
                    }
                }
            }

            if(function_exists('vibebp_fireabase_update_stale_requests')){
                vibebp_fireabase_update_stale_requests('global',VIBECAL_API_NAMESPACE.'/event/getEvents');
                vibebp_fireabase_update_stale_requests('global',VIBECAL_API_NAMESPACE.'/event/getEvent?args=%7B%22id%22%3A'.$id);
            }

            $return = apply_filters('vibe_cal_add_event',array(
                'status'=>true,
                'event'=>array(
                    'id'=>$id,
                    'author'=>$this->user->id,
                    'post_title'=>$body['post_title'],
                    'post_content'=>$body['post_content'],
                    'meta' => $this->get_event_meta($id)
                ),
                'message'=>__('Successfully saved !','vibecal')),$request);
        }

        return new WP_REST_Response( $return, 200 );
    }

    function drag_event($request){
        $body = json_decode($request->get_body(),true);
        $event = $body['event'];
        if(!empty($event) && !empty($event['id']) && !empty($event['start']) && !empty($event['end'])){
            $event_id = $event['id'];
            if(is_numeric($event['start']) && is_numeric($event['end']) && $event['start']<$event['end']){
                
                if(empty($this->can_edit_event($event['id'],$this->user))){
                    return new WP_REST_Response( array('status'=>false,'message'=>__('Can not make changes.','vibecal')), 200 );
                }

                update_post_meta($event['id'],'start',$event['start']);
                update_post_meta($event['id'],'end',$event['end']);
                
                if(function_exists('vibebp_fireabase_update_stale_requests')){
                    vibebp_fireabase_update_stale_requests('global',VIBECAL_API_NAMESPACE.'/event/getEvents');
                    vibebp_fireabase_update_stale_requests('global',VIBECAL_API_NAMESPACE.'/event/getEvent?args=%7B%22id%22%3A'.$event_id); 
                }

                $data = array(
                    'status'=>true,
                    'message'=> _x('Successfully updated!','Successfully updated!','vibecal')
                );
            }else{
                $data = array(
                    'status' => 0,
                    'message' => _x('Date is not valid','Date is not valid','vibecal')
                );
            }
        }else{
            $data = array(
                'status' => 0,
                'message' => _x('Data missing','Data missing','vibecal')
            );
        }
        $data = apply_filters('vibe_drag_events',$data,$request);
        return new WP_REST_Response($data, 200);
    }

    function get_event($request){

        $body = json_decode($request->get_body(),true);

        $event = (Array)get_post($body['id']);
        
        if(is_array($body['fields'])){
            $fields = $body['fields'];
            foreach($fields as $i=>$field){
                switch($field['from']){
                    case 'post':
                        $fields[$i]['value']=$event[$field['id']];
                        if($field['id'] == 'post_content'){
                            $fields[$i]['raw']=get_post_meta($body['id'],'raw',true);
                        }
                    break;
                    case 'meta':
                        $fields[$i]['value']=get_post_meta($body['id'],$field['id'],true);
                        if(!empty($field['id'])){
                            if($field['id'] == 'event_labels'){
                                $fields[$i]['value'] = get_post_meta($body['id'],$field['id'],false);
                            }

                            $event[$field['id']]=$fields[$i]['value'];
                        }
                    break;
                    case 'taxonomy':
                        $fields[$i]['value'] = wp_get_object_terms($id,$field['taxonomy'],array('fields'=>'ids'));
                    break;
                }
            }

        }
        return new WP_REST_Response(array('status'=>true,'fields'=>$fields), 200);
    }


    function get_public_events($request){
        $body = json_decode($request->get_body(),true);
        $filter = $body['filter'];
        if(!empty($filter['start']) && !empty($filter['end'])){
            

            //Public events
            $hide_events = get_user_meta($this->user->id,'hide_events',true);
            if(!empty($hide_events)){
                $post_ids = array_merge($post_ids,$hide_events);    
            }
            
            $args = array(
                'post_type'=>VIBECAL_EVENT_POST_TYPE,
                's'=>!empty($body['s'])?$body['s']:'',
                'post__not_in'=>empty($post_ids)?'':$post_ids,
                'meta_query'=>array(
                    'meta_query'=>array(
                    'relation'=>'AND', 
                        array(
                            'key'=>'event_type',
                            'value'=>'public',
                            'compare'=>'='
                        ),
                        array(
                            'key'=>'start',
                            'value'=>$filter['end'],
                            'compare'=>'<='
                        ),
                        array(
                            'key'=>'end',
                            'value'=>$filter['start'],
                            'compare'=>'>='
                        ),
                    )
                )
            );
            if(!empty($filter['label'])){
                $args['meta_query'][] = array(
                    'key'=>'event_labels',
                    'value'=>$filter['label'],
                    'compare'=>'='
                );
            }

            $public_query = new WP_Query(apply_filters('vibe_calendar_public_events_args',$args,$this->user,$body));
            if($public_query->have_posts()){
                while($public_query->have_posts()){
                    $public_query->the_post();
                    global $post;
                    $results[]=array(
                        'id'=>$post->ID,
                        'type'=>'public',
                        'post_title'=>$post->post_title,
                        'description'=>get_the_excerpt(),
                        'author'=>$post->post_author,
                        'link'=> get_permalink($post->ID),
                        'meta' => $this->get_event_meta($post->ID)
                    );
                }
                $data = array(
                    'status' => 1,
                    'data' => $results,
                    'total'=>empty($data['total'])?$public_query->found_posts:$data['total']+$public_query->found_posts,
                    'message' => _x('Events found','Events found','vibecal'),
                );                
            }

            if(empty($results)){
                $data = array(
                    'status' => 0,
                    'message' => _x('No events found!','No events found!','vibecal')
                );
            }
        }else{
            $data = array(
                'status' => 0,
                'message' => _x('Data missing!','Data missing!','vibecal'),
            );
        }

        
        $data = apply_filters('vibe_calendar_public_events',$data,$request);
        return new WP_REST_Response($data, 200);
    }

    function get_events($request){
        $body = json_decode($request->get_body(),true);
        $filter = $body['filter'];
        if(!empty($filter['start']) && !empty($filter['end'])){
            // Query build
            $args = array(
                'post_type'=>VIBECAL_EVENT_POST_TYPE,
                's'=>!empty($body['s'])?$body['s']:'',
                'meta_query'=>array(
                    'meta_query'=>array(
                    'relation'=>'AND', 
                        array(
                            'key'=>'start',
                            'value'=>$filter['end'],
                            'compare'=>'<='
                        ),
                        array(
                            'key'=>'end',
                            'value'=>$filter['start'],
                            'compare'=>'>='
                        ),
                    )
                )
            );
            if(!empty($filter['label'])){
                $args['meta_query'][] = array(
                    'key'=>'event_labels',
                    'value'=>$filter['label'],
                    'compare'=>'='
                );
            }

            if(!empty($filter['module_type'])){
                $args['meta_query'][] = array(
                    'key'=>$filter['module_type'],
                    'value'=>$filter['module_id'],
                    'compare'=>'='
                );
            }else{
                $args['meta_query'][] = array(
                    'key'=>'event_user',
                    'value'=>$this->user->id,
                    'compare'=>'='
                );
            }
            $query = new WP_Query(apply_filters('vibe_calendar_myevents_args',$args,$this->user,$body));
            $results = [];
            $post_ids=[];
            if($query->have_posts()){
                while($query->have_posts()){
                    $query->the_post();
                    global $post;
                    $post_ids[]=$post->ID;
                    $results[]=array(
                        'id'=>$post->ID,
                        'type'=>'public',
                        'post_title'=>$post->post_title,
                        'post_content'=>$post->post_content,
                        'author'=>$post->post_author,
                        'meta' => $this->get_event_meta($post->ID)
                    );
                }
                $data = array(
                    'status' => 1,
                    'data' => $results,
                    'total'=>$query->found_posts,
                    'message' => _x('Events found','Events found','vibecal'),
                );                
            }

            //Public events
            if(empty($filter['module_type'])){
                $hide_events = get_user_meta($this->user->id,'hide_events',true);
                if(!empty($hide_events)){
                    $post_ids = array_merge($post_ids,$hide_events);    
                }
                
                $args = array(
                    'post_type'=>VIBECAL_EVENT_POST_TYPE,
                    's'=>!empty($body['s'])?$body['s']:'',
                    'post__not_in'=>empty($post_ids)?'':$post_ids,
                    'meta_query'=>array(
                        'meta_query'=>array(
                        'relation'=>'AND', 
                            array(
                                'key'=>'event_type',
                                'value'=>'public',
                                'compare'=>'='
                            ),
                            array(
                                'key'=>'start',
                                'value'=>$filter['end'],
                                'compare'=>'<='
                            ),
                            array(
                                'key'=>'end',
                                'value'=>$filter['start'],
                                'compare'=>'>='
                            ),
                        )
                    )
                );
                if(!empty($filter['label'])){
                    $args['meta_query'][] = array(
                        'key'=>'event_labels',
                        'value'=>$filter['label'],
                        'compare'=>'='
                    );
                }

                $public_query = new WP_Query(apply_filters('vibe_calendar_public_events_args',$args,$this->user,$body));
                if($public_query->have_posts()){
                    while($public_query->have_posts()){
                        $public_query->the_post();
                        global $post;
                        
                        if(!in_Array($post->ID,$post_ids)){
                            $results[]=array(
                                'id'=>$post->ID,
                                'type'=>'public',
                                'post_title'=>$post->post_title,
                                'post_content'=>$post->post_content,
                                'author'=>$post->post_author,
                                'meta' => $this->get_event_meta($post->ID)
                            );
                        }
                    }
                    $data = array(
                        'status' => 1,
                        'data' => $results,
                        'total'=>empty($data['total'])?$public_query->found_posts:$data['total']+$public_query->found_posts,
                        'message' => _x('Events found','Events found','vibecal'),
                    );                
                }
            }

            if(empty($results)){
                $data = array(
                    'status' => 0,
                    'message' => _x('No events found!','No events found!','vibecal')
                );
            }
        }else{
            $data = array(
                'status' => 0,
                'message' => _x('Data missing!','Data missing!','vibecal'),
            );
        }

        
        
        $data = apply_filters('vibe_calendar_my_events',$data,$request);
        return new WP_REST_Response($data, 200);
    }

    function get_event_meta($id){
        return apply_filters('vibe_events_meta',array(
            array('meta_key'=>'start','meta_value'=>(int)get_post_meta($id,'start',true)),
            array('meta_key'=>'end','meta_value'=>(int)get_post_meta($id,'end',true)),
            array('meta_key'=>'color','meta_value'=>get_post_meta($id,'event_color',true)),
            array('meta_key'=>'event_type','meta_value'=>get_post_meta($id,'event_type',true)),
            array('meta_key'=>'recurring','meta_value'=>get_post_meta($id,'recurring',true)),
            array('meta_key'=>'daysOfWeek','meta_value'=>get_post_meta($id,'daysOfWeek',true)),
            array('meta_key'=>'googleevent','meta_value'=>get_post_meta($id,'googleevent',true)),
        ));
    }

    function get_event_users($request){
        $post = json_decode($request->get_body());
        $post_id = $post->post_id;
        if(!empty($post_id)){
            $participants = get_post_meta($post_id,'event_user',false);
            $results = array();
            if(!empty($participants) && is_array($participants)){
                foreach ($participants as $key => $id) {
                $results[] = $this->get_user_by_id($id);
                }
                $data = array(
                    'status' => 1,
                    'message' => _x('Participants found','Participants found','vibecal'),
                    'data' => $results
                );
            }else{
                $data = array(
                    'status' => 0,
                    'message' => _x('No Participants found','No Participants found','vibecal'),
                );
            }
        }else{
            $data = array(
                'status' => 0,
                'message' => _x('Data missing','Data missing','vibecal')
            );
        }
        $data = apply_filters('vibe_invite_get_event_users',$data,$request);
        return new WP_REST_Response($data, 200);
    }

    function remove_event_user($request){
        $post = json_decode($request->get_body(),true);
        $participants = get_post_meta($post['post_id'],'event_user',false);
         if(!empty($participants)){
            if(in_array($post['user_id'],array_values($participants))){
                delete_post_meta($post['post_id'],'event_user',$post['user_id']);


                return new WP_REST_Response(array('status'=>1), 200);
            }
        }
        return new WP_REST_Response(array('status'=>0), 200);
    }

    function get_event_taxonomy($request){
        $post = json_decode($request->get_body());
        $post_id = $post->post_id;
        if(!empty($post_id)){
            $terms = get_the_terms($post_id,VIBECAL_EVENT_TAXONOMY);
            $results = array();
            if(!empty($terms) && is_array($terms)){
                $data = array(
                    'status' => 1,
                    'message' => _x('Taxonomy found','Taxonomy found','vibecal'),
                    'data' => $terms
                );
            }else{
                $data = array(
                    'status' => 0,
                    'message' => _x('No Taxonomy found','No Taxonomy found','vibecal'),
                );
            }
        }else{
            $data = array(
                'status' => 0,
                'message' => _x('Data missing','Data missing','vibecal')
            );
        }
        $data = apply_filters('vibe_invite_get_event_taxonomy',$data,$request);
        return new WP_REST_Response($data, 200);
    }

    

    function get_invited_users($request){
        $post = json_decode($request->get_body(),true);
        $args = array(
            'inviter_id' => $this->user->id,
            'accepted' => 'pending',
            'class' => VIBECAL_EVENT_INVITE,
            'item_id' => $post['post_id']
        );
        if(class_exists('BP_Invitation')){
            $invitations = BP_Invitation::get($args);
            if(!empty($invitations) && is_array($invitations)){
                
                $data = array(
                    'status' => 1,
                    'message' => _x('Invitations Found','Invitations Found','vibecal'),
                    'data' => $invitations  
                );
                return new WP_REST_Response($data, 200);
            }
        }
        return new WP_REST_Response(array('status'=>0), 200);
    }

    function remove_invite($request){
        $post = json_decode($request->get_body(),true);
        $args = array(
            'user_id' => $post['user_id'],
            'inviter_id' => $this->user->id,
            'accepted' => 'pending',
            'class' => VIBECAL_EVENT_INVITE,
            'item_id' => $post['post_id']
        );
        if(class_exists('BP_Invitation')){
            $invitations = BP_Invitation::delete($args);

            if(function_exists('vibebp_fireabase_update_stale_requests')){
                vibebp_fireabase_update_stale_requests((int)$post['user_id'],VIBECAL_API_NAMESPACE.'/event/invite/my_invites'); 
                vibebp_fireabase_update_stale_requests('global',VIBECAL_API_NAMESPACE.'/event_invites?args=%7B%22post_id%22%3A'.$post_id); 
            }
            return new WP_REST_Response(array('status'=>1), 200);
        }
        return new WP_REST_Response(array('status'=>0), 200);
    }

    function send_invite($request){
        $post = json_decode($request->get_body(),true);
        $user_ids = $post['user_ids'];
        $post_id =  $post['event_id'];
        if(!empty($user_ids) && is_array($user_ids) && !empty($post_id)){
            if(empty($this->can_edit_event($post_id,$this->user))){
                return new WP_REST_Response( array('status'=>false,'message'=>__('Can not make changes.','vibecal')), 200 );
            }
            $results = array();
            $args = array(
                'inviter_id' => $this->user->id,
                'item_id' => $post_id,
                'class' => VIBECAL_EVENT_INVITE
            );
            if(class_exists('vibe_invite')){
                $BP_Invitation = new vibe_invite();
                foreach ($user_ids as $user) {
                    $args['user_id'] = $user['id'];
                    $invitation = BP_Invitation::get($args);
                    if(!empty($invitation)){
                        $is_invited = false;
                    }else{
                        $is_invited = $BP_Invitation->add_invitation($args);
                    }
                    $results[] = array(
                        'user_id' => $user['id'],
                        'is_invited' => $is_invited
                    );
                    if($is_invited){
                        if(function_exists('vibebp_fireabase_update_stale_requests')){
                            vibebp_fireabase_update_stale_requests((int)$user['id'],VIBECAL_API_NAMESPACE.'/event/invite/my_invites'); 
                        }
                    }
                }
                if(function_exists('vibebp_fireabase_update_stale_requests')){
                    vibebp_fireabase_update_stale_requests('global',VIBECAL_API_NAMESPACE.'/event_invites?args=%7B%22post_id%22%3A'.$post_id); 
                }
                $data = array(
                    'status' => 1,
                    'message' => _x('Invitation send','Invitation send','vibecal'),
                    'data' => $results
                );
            }else{
                $data = array(
                    'status' => 0,
                    'message' => _x('Invitation not active','Invitation not active','vibecal')
                ); 
            }
        }else{
            $data = array(
                'status' => 0,
                'message' => _x('Data missing','Data missing','vibecal')
            );
        }
        $data = apply_filters('vibe_send_invite',$data,$request);
        return new WP_REST_Response($data, 200);
    }

    function my_invites($request){
        $post = json_decode($request->get_body(),true);
        $filter = $post['filter'];

        $t = 1;
        if(!empty($filter) && !empty($filter['start']) && !empty($filter['end'])){
            $ID_array = array();
            $args1 = array(
                'post_type'=>VIBECAL_EVENT_POST_TYPE,
                's'=>!empty($filter['s'])?$filter['s']:'',
                'fields' => 'ids',
                'meta_query'=>array(
                    'meta_query'=>array(
                    'relation'=>'AND',
                        array(
                            'key'=>'start',
                            'value'=>$filter['end'],
                            'compare'=>'<='
                        ),
                        array(
                            'key'=>'end',
                            'value'=>$filter['start'],
                            'compare'=>'>='
                        ),
                    )
                )
            );
            $query = new WP_Query( $args1 );
            $ids = $query->posts;
            if(is_array($ids) && count($ids)){ $ID_array = $ids; }
            else{ $t = 0;} //means ids not found
            
            if(!empty($t)){
                $args = array(
                    'user_id' => $this->user->id,
                    'accepted' => isset($filter['type'])?$filter['type']:'pending',
                    'class' => VIBECAL_EVENT_INVITE,
                    'item_id' => $ID_array
                );
                if(class_exists('BP_Invitation')){
                    $invitations = BP_Invitation::get($args);
                    if(!empty($invitations) && is_array($invitations)){
                        foreach ($invitations as $key => $value) {
                            if(!empty($value->item_id)){
                                $invitations[$key]->event = $this->get_event_by_id($value->item_id);
                            }
                            if(!empty($value->inviter_id)){
                                $invitations[$key]->inviter =  $this->get_user_by_id($value->inviter_id);
                            }
                        }
                        $data = array(
                            'status' => 1,
                            'message' => _x('Invitations Found','Invitations Found','vibecal'),
                            'data' => $invitations  
                        );
                    }else{
                        $data = array(
                            'status' => 0,
                            'message' => _x('Invitations not found','Invitations not found','vibecal')
                        ); 
                    }
                }else{
                    $data = array(
                        'status' => 0,
                        'message' => _x('Invitation not active','Invitation not active','vibecal')
                    );  
                }
            }else{
                $data = array(
                    'status' => 0,
                    'message' => _x('Invitations not found for filter','Invitations not found for filter','vibecal')
                ); 
            }
        }else{
            $data = array(
                'status' => 0,
                'message' => _x('Data missing','Data missing','vibecal')
            );
        }
        $data = apply_filters('vibe_event_my_invites',$data,$request);
        return new WP_REST_Response($data, 200);
    }

    function get_event_by_id($post_id){
        $post = get_post( $post_id );
        return array(
            'id'=>$post_id,
            'post_title'=> $post->post_title,
            'post_content'=>$post->post_content,
            'author'=>$post->post_author,
            'meta' => $this->get_event_meta($post_id)
        );
    }
    function get_event_post($post_id){
        $post = get_post($post_id);
        $post->start = (int)get_post_meta($post_id,'start',true);
        $post->end = (int)get_post_meta($post_id,'end',true);
        return $post;
    }

    function action_invite($request){
        $post = json_decode($request->get_body(),true);
        $invitation_id = $post['invitation_id'];
        $action = $post['action'];
        if(!empty($invitation_id)){
            if(class_exists('BP_Invitation')){
                $args = array(
                    'user_id' => $this->user->id,
                    'id' => $invitation_id
                );
                switch ($action) {
                    case 'accept':
                        $invitation = BP_Invitation::get($args);
                        $is_accepted = BP_Invitation::update(array('accepted'=>'accepted'),$args);
                        if($is_accepted){
                            if(!empty($invitation[0])){
                                $post_id = $invitation[0]->item_id;
                                delete_post_meta($post_id,'event_user',$this->user->id);
                                add_post_meta($post_id,'event_user',$this->user->id);
                                $data = array(
                                    'status' => 1,
                                    'message' => _x('Invitation accepted','Invitation accepted','vibecal'),
                                    'data' => $this->get_event_by_id($post_id)
                                ); 
                            }else{
                                $data = array(
                                    'status' => 0,
                                    'message' => _x('Error occured','Error occured','vibecal')
                                );
                            }
                        }else{
                            $data = array(
                                'status' => 0,
                                'message' => _x('Invitation not accepted','Invitation not accepted','vibecal')
                            ); 
                        }
                        break;
                    case 'reject':
                        $is_updated = BP_Invitation::update(array('accepted'=>'pending'),$args);
                        if($is_updated){
                            $data = array(
                                'status' => 1,
                                'message' => _x('Invitation rejected','Invitation rejected','vibecal')
                            ); 
                        }else{
                            $data = array(
                                'status' => 0,
                                'message' => _x('Invitation not rejected','Invitation not rejected','vibecal')
                            ); 
                        }
                        break;    
                    case 'delete':
                        $invitation = BP_Invitation::get($args);
                        $is_deleted = BP_Invitation::delete($args);
                        if($is_deleted){
                            if(!empty($invitation[0])){
                                $post_id = $invitation[0]->item_id;
                                delete_post_meta($post_id,'event_user',$this->user->id);
                            }
                            $data = array(
                                'status' => 1,
                                'message' => _x('Invitation deleted','Invitation deleted','vibecal')
                            ); 
                        }else{
                            $data = array(
                                'status' => 0,
                                'message' => _x('Invitation not deleted','Invitation not deleted','vibecal')
                            ); 
                        }
                        break;
                    default:
                    $data = array(
                        'status' => 0,
                        'message' => _x('Not Valid Action','Not Valid Action','vibecal')
                    ); 
                }
            }else{
                $data = array(
                    'status' => 0,
                    'message' => _x('Invitation not active','Invitation not active','vibecal')
                ); 
            }
        }else{
            $data = array(
                'status' => 0,
                'message' => _x('Data missing','Data missing','vibecal')
            );
        }
        if(!empty($data['status'])){
            if(function_exists('vibebp_fireabase_update_stale_requests')){
                vibebp_fireabase_update_stale_requests((int)$this->user->id,VIBECAL_API_NAMESPACE.'/event/invite/my_invites'); 
            }
        }
        $data = apply_filters('vibe_action_invite',$data,$request);
        return new WP_REST_Response($data, 200);
    }

    function add_new_label($request){
        $post = json_decode($request->get_body(),true);
        $label = $post['label'];
        $user_id = $this->user->id;
        if(!empty($label['color']) && !empty($label['label'])){
            $color = $label['color']; $title = $label['label']; 
            $key = sanitize_title($title); // make unique key
            $new_label = array('color' => $color,'label' => $title);
            $all_labels = get_user_meta($user_id,'event_labels',true);
            if(!empty($all_labels) && is_array($all_labels)){
                if(array_key_exists($key,$all_labels)){
                    $data = array(
                        'status' => 0,
                        'message' => _x('Label already exist','Label already exist','vibecal')
                        
                    );
                }else{
                    $all_labels[$key] = $new_label;
                    update_user_meta($user_id,'event_labels',$all_labels);
                    $data = array(
                        'status' => 1,
                        'message' => _x('Added new label','Added new label','vibecal'),
                        'data' => array( $key => $new_label )
                    );
                }
            }else{
                $temp = array( $key => $new_label );
                update_user_meta($user_id,'event_labels',$temp);
                $data = array(
                    'status' => 1,
                    'message' => _x('Added new label','Added new label','vibecal'),
                    'data' => $temp
                );
            }
        }else{
            $data = array(
                'status' => 0,
                'message' => _x('Data missing','Data missing','vibecal')
            );
        }

        if(!empty($data['status'])){
            if(function_exists('vibebp_fireabase_update_stale_requests')){
                vibebp_fireabase_update_stale_requests((int)$this->user->id,VIBECAL_API_NAMESPACE.'/event/label/all_labels'); 
            }
        }

        $data = apply_filters('vibe_add_new_label',$data,$request);
        return new WP_REST_Response($data, 200);
    }

    function get_all_labels($request){
        $body = json_decode($request->get_body(),true);
        $all_labels = get_user_meta($this->user->id,'event_labels',true);
        if(!empty($all_labels) && is_array($all_labels)){
            if(!empty($body['count'])){
                foreach ($all_labels as $label_id => $value) {
                   $all_labels[$label_id]['count'] = $this->get_label_id_post_count($label_id);
                }
            }
            $data = array(
                'status' => 1,
                'data' => $all_labels
            );
        }else{
            $data = array(
                'status' => 0
            );
        }
        $data = apply_filters('vibe_get_all_labels',$data,$request);
        return new WP_REST_Response($data, 200);
    }

    function get_label_id_post_count($label_id){
        $args = array(
            'post_type'  => VIBECAL_EVENT_POST_TYPE,
            'meta_query'=>array(
                array(
                    'key'=>'event_labels',
                    'value'=>$label_id,
                    'compare'=>'='
                )
            )
        );
        $query = new WP_Query( $args );
        return $query->found_posts;
    }

    function remove_label($request){
        $post = json_decode($request->get_body(),true);
        $key = $post['key'];
        $user_id = $this->user->id;
        if(!empty($key)){
            $all_labels = get_user_meta($user_id,'event_labels',true);
            if(!empty($all_labels) && is_array($all_labels)){
                if(!array_key_exists($key,$all_labels)){
                    $data = array(
                        'status' => 9,
                        'message' => _x('Label Not Found','Label Not Found','vibecal')
                    );
                }else{
                    unset($all_labels[$key]);
                    update_user_meta($user_id,'event_labels',$all_labels);
                    $data = array(
                        'status' => 1,
                        'message' => _x('Label Removed','Labels Removed','vibecal')
                    );
                }
            }else{
                $data = array(
                    'status' => 0,
                    'message' => _x('Labels Not Found','Labels Not Found','vibecal')
                );
            }
        }else{
            $data = array(
                'status' => 0,
                'message' => _x('Data missing','Data missing','vibecal')
            );
        }

        if(!empty($data['status'])){
            if(function_exists('vibebp_fireabase_update_stale_requests')){
                vibebp_fireabase_update_stale_requests((int)$this->user->id,VIBECAL_API_NAMESPACE.'/event/label/all_labels'); 
            }
        }
        $data = apply_filters('vibe_get_all_labels',$data,$request);
        return new WP_REST_Response($data, 200);
    }
    
    function get_taxonomy($request){
        $post = json_decode(file_get_contents('php://input'));
        $body = $request->get_body();
        $body = json_decode($body);
        $return = array();
        $taxonomy=$body->taxonomy;
        $posts = array();

        if(!empty($body ) && !empty($taxonomy) && !empty($this->user->id) && is_numeric($this->user->id)){
            $terms = get_terms(  array('taxonomy'=>$taxonomy,'hide_empty' => false,'orderby'=>'name','order'=>'ASC','fields' => 'id=>name') );
            if(!empty($terms) && is_array($terms)){
                foreach ($terms as $key=>$term ){
                    $posts[] = array('id'=>$key,'text'=>$term);
                }
            }
            wp_reset_postdata();
        }else{
            $return = array('status'=>false,'message'=>_x('Sorry Something went wrong or invalid post type','','vibecal'));
        }

        if(empty($posts)){
            return new WP_REST_Response( array('status'=>false,'message'=>_x('Sorry no results found!Try another search keyword!','API request','vibecal')), 200 );
        }
        return new WP_REST_Response( array('status'=>true,'posts'=>$posts), 200 );
    }

    function selectcpt($request){

        $cpt= $request->get_param('cpt');
        $body = json_decode($request->get_body());
        $return = array();
        if($cpt=='assignment'){
            $cpt = 'wplms-assignment';
        }
        $results = array();
        if(!empty($body ) && !empty($cpt) && !empty($this->user->id) && is_numeric($this->user->id)){
            
            if($cpt == 'groups'){
                if(function_exists('groups_get_group')){
                    $vgroups =  groups_get_groups(array(
                    'per_page'=>999,
                    'search_terms'=>$body->search
                    ));
                    $return = array();
                    foreach($vgroups['groups'] as $vgroup){
                        $results[] = array('id'=>$vgroup->id,'text'=>$vgroup->name,'link'=>bp_get_group_permalink( $vgroup ));
                    }
                }
            }else{
                $args = array(
                    'post_type'=>$cpt,
                    'posts_per_page'=>99,
                    's'=>$body->search,
                );
                

                $args = apply_filters('wplms_frontend_cpt_query',$args);
                $query = new WP_Query($args);
                
                if($query->have_posts()){
                    while($query->have_posts()){
                        $query->the_post();
                        global $post;
                        $preturn = array('id'=>$post->ID,'text'=>$post->post_title,'link'=>get_permalink($post->ID));
                        if($cpt == 'unit'){
                            $type = get_post_meta($post->ID,'vibe_type',true);
                            if(empty($type) || $type == 'unit'){$type = 'general';}
                            if($type == 'text-document'){$type = 'general';}
                            if($type == 'play'){$type = 'video';}
                            if($type == 'music-file-1'){$type = 'audio';}
                            if($type == 'podcast'){$type = 'audio';}

                            $preturn['type']=$type;
                        }
                        if($cpt == 'quiz'){
                            $isdynamic = get_post_meta($post->ID,'vibe_quiz_dynamic',true);
                            if($isdynamic){
                                 $preturn['type']='dynamic';
                            }else{
                                $preturn['type']='simple';
                            }
                        }
                        if($cpt == 'wplms-assignment'){
                            $preturn['type']=get_post_meta($post->ID,'vibe_assignment_submission_type',true);
                            if(empty($preturn['type'])){
                                $preturn['type']='textarea';
                            }
                        }
                        if($cpt == 'question'){
                            
                            $type = get_post_meta($post->ID,'vibe_question_type',true);
                            if(empty($type)){$type = 'multiple';}
                            $preturn['type']=$type;
                        }

                        if($cpt == 'product'){
                            $product = wc_get_product($post->ID);
                            $preturn['text'] .= ' - '.$product->get_price_html();

                            $preturn['fields'] = apply_filters('wplms_product_fields',array(
                                'ID'=>$post->ID,
                                'post_title'=>$post->post_title,
                                'meta'=>array(
                                    array('meta_key'=>'_price','meta_value'=>get_post_meta($post->ID,'_price',true)),
                                    array('meta_key'=>'vibe_subscription','meta_value'=>get_post_meta($post->ID,'vibe_subscription',true)),
                                    array('meta_key'=>'vibe_duration','meta_value'=>array('value'=>get_post_meta($post->ID,'vibe_duration',true),'parameter'=>get_post_meta($post->ID,'vibe_duration_parameter',true))
                                    )
                                )
                            ));
                        }
                        $results[] = $preturn;
                    }
                }
                wp_reset_postdata();
            }
        }else{
            $return = array('status'=>false,'message'=>_x('Sorry Something went wrong or invalid post type','','vibecal'));
        }

        if(empty($results)){
            return new WP_REST_Response( array('status'=>false,'message'=>_x('Sorry not results found!Try another search keyword!','no results in search api request','vibecal')), 200 );
        }
        return new WP_REST_Response( array('status'=>true,'posts'=>$results), 200 );
    }

    function get_group_component($request){
        $body = json_decode($request->get_body(),true);
        if(!function_exists('groups_get_group')){
            return new WP_REST_Response( array('status'=>false,'message'=>__('Groups not active','vibebp')), 200 );
        }
        $group = groups_get_group(array('group_id'=>$body['id'],'populate_extras'=>true));

        if(!empty($group)){
            return new WP_REST_Response( array('status'=>true,'group'=>apply_filters('wplms_get_group',array(
                'id'=>$group->id,
                'image'=>bp_core_fetch_avatar(array(
                            'item_id' => $group->id,
                            'object'  => 'group',
                            'type'     => 'full',
                            'html'    => false
                        )),
                'title'=>$group->name,
                'description'=>$group->description,
                'status'=>$group->status,
                'type'=>bp_groups_get_group_type( $group->id ),
                'member_count'=>groups_get_groupmeta($group->id,'total_member_count',true),
                'creator_id'=>$groups->creator_id,
                'last_activity'=>groups_get_groupmeta($group->id,'last_activity',true),
            ))), 200 );
        }
        return new WP_REST_Response( array('status'=>false,'message'=>__('Could not load group','vibebp')), 200 );
    }

    function get_forum_component($request){
        $body = json_decode($request->get_body(),true);
        if(!post_type_exists('forum')){
            return new WP_REST_Response( array('status'=>false,'message'=>__('Forums not active','vibebp')), 200 );
        }
        
        $args= array('post__in'=>array($body['id']));
        if ( bbp_has_forums($args) ) {
            while ( bbp_forums() ) { 
                bbp_the_forum();
                global $post;
                
                return new WP_REST_Response( array('status'=>true,'forum'=>apply_filters('wplms_get_forum',array(
                    'id'=>$post->ID,
                    'title'=>$post->post_title,
                    'description'=>$post->post_content,
                    'private'=>bbp_is_forum_private( $post->ID ),
                    'topic_count'=>bbp_get_forum_topic_count( $post->ID, false, true ),
                    'forums_count'=>bbp_get_forum_subforum_count( $post->ID, true ),
                    'last_active'=>bbp_get_forum_last_active_time($post->ID)
                ))));
            }
        }
        return new WP_REST_Response( array('status'=>false,'message'=>__('Could not load group','vibebp')), 200 );
    }

    function get_events_eventon($request){
        $body = json_decode($request->get_body(),true);
        $filter = $body['filter'];
        $results = array();
        if(class_exists( 'EventON' )){
            if(isset($filter) && $filter['start'] && $filter['end']){
                // Query build
                $args = array(
                    'post_type'=>'ajde_events',
                    's'=>!empty($body['s'])?$body['s']:'',
                    'meta_query'=>array(
                        'meta_query'=>array(
                        'relation'=>'AND', 
                            array(
                                'key'=>'date_range_start',
                                'value'=>$filter['end'],
                                'compare'=>'<='
                            ),
                            array(
                                'key'=>'date_range_end',
                                'value'=>$filter['start'],
                                'compare'=>'>='
                            ),
                        )
                    )
                );

                $query = new WP_Query(apply_filters('vibe_calendar_myevents_eventon_args',$args,$this->user,$body));
                $results = [];
                if($query->have_posts()){
                    while($query->have_posts()){
                        $query->the_post();
                        global $post;
                        $results[]=array(
                            'id'=>$post->ID,
                            'post_title'=>$post->post_title,
                            'post_content'=>$post->post_content,
                            'author'=>$post->post_author,
                            'meta' => $this->get_eventon_meta($post->ID),
                            'extra' => $this->get_eventon_extra($post->ID)
                        );
                    }
                    $data = array(
                        'status' => 1,
                        'data' => $results,
                        'total'=>$query->found_posts,
                        'message' => _x('Events found','Events found','vibecal'),
                    );                
                }else{
                    $data = array(
                        'status' => 0,
                        'message' => _x('No events found!','No events found!','vibecal')
                    );
                }
            }else{
                $data = array(
                    'status' => 0,
                    'message' => _x('Data missing!','Data missing!','vibecal')
                );
            }
        }else{
            $data = array(
                'status' => 0,
                'message' => _x('EventOn Plugin not active!','EventOn Plugin not active!','vibecal')
            );
        }
        return new WP_REST_Response(apply_filters('vibe_get_events_eventon',$data,$request), 200);
    }

    function get_eventon_meta($id){
        $color = get_post_meta($id,'evcal_event_color',true);
        if(!empty($color)){ $color = '#'.$color;}
        return array(
            array('meta_key'=>'start','meta_value'=>(int)get_post_meta($id,'date_range_start',true)*1000),
            array('meta_key'=>'end','meta_value'=>(int)get_post_meta($id,'date_range_end',true)*1000),
            array('meta_key'=>'color','meta_value'=>$color)
        );
    }

    function get_eventon_extra($id){
        $r = array();
        $r['canceled'] = get_post_meta($id,'_cancel',true) == 'yes';
        $r['completed'] = get_post_meta($id,'_completed',true) == 'yes';
        $r['featured'] = get_post_meta($id,'_featured',true) == 'yes';
        return $r;
    }

    function add_event_label($request){
        $body = json_decode($request->get_body(),true);
        $label_id = $body['label_id'];
        $post_id = $body['post_id'];
        if(!empty($label_id) && !empty($post_id)){

            if(empty($this->can_edit_event($post_id,$this->user))){
                return new WP_REST_Response( array('status'=>false,'message'=>__('Can not make changes.','vibecal')), 200 );
            }

            $all_labels = get_user_meta($this->user->id,'event_labels',true);
            if(!empty($all_labels) && count($all_labels)){
                if(array_key_exists($label_id,$all_labels)){
                    delete_post_meta($post_id,'event_labels',$label_id);
                    add_post_meta($post_id,'event_labels',$label_id);
                    $data = array(
                        'status' => 1,
                        'message' => _x('Label assigned','Label assigned','vibecal')
                    );
                }else{
                    $data = array(
                        'status' => 0,
                        'message' => _x('Label not exist','Label not exist','vibecal')
                    );
                }
            }else{
                $data = array(
                    'status' => 0,
                    'message' => _x('Please create some labels!','Please create some labels!','vibecal')
                ); 
            }
        }else{
            $data = array(
                'status' => 0,
                'message' => _x('Data missing!','Data missing!','vibecal')
            );
        }
        return new WP_REST_Response(apply_filters('vibe_add_event_label',$data,$request), 200);
    }
    function remove_event_label($request){
        $body = json_decode($request->get_body(),true);
        $label_id = $body['label_id'];
        $post_id = $body['post_id'];
        if(!empty($label_id)){

            if(empty($this->can_edit_event($post_id,$this->user))){
                return new WP_REST_Response( array('status'=>false,'message'=>__('Can not make changes.','vibecal')), 200 );
            }

            $all_labels = get_user_meta($this->user->id,'event_labels',true);
            delete_post_meta($post_id,'event_labels',$label_id);
            $data = array(
                'status' => 1,
                'message' => _x('Label removed','Label removed','vibecal')
            );
        }else{
            $data = array(
                'status' => 0,
                'message' => _x('Data missing!','Data missing!','vibecal')
            );
        }

        return new WP_REST_Response(apply_filters('vibe_add_event_label',$data,$request), 200);
    }


    function delete_event($request){
        $body = json_decode($request->get_body(),true);

        $return = array(
            'status'=>0,
            'message'=>__('Insufficient permissions to delete event','vibecal')
        );
        if(user_can($this->user->id,'manage_options') || get_post_field('post_author',$body['post_id']) == $this->user->id){
            if(wp_delete_post($body['post_id'])){
                do_action('vibecal_event_deleted',$body['post_id'],$this->user->id);
                $return = array(
                    'status'=>1,
                    'message'=>__('Event deleted.','vibecal')
                );
            }
        }
        if(function_exists('vibebp_fireabase_update_stale_requests')){
            vibebp_fireabase_update_stale_requests('global',VIBECAL_API_NAMESPACE.'/event/getEvents');
        }
        return new WP_REST_Response($return, 200);   
    }


    function hide_event($request){
        $body = json_decode($request->get_body(),true);

        $hide_events = get_user_meta($this->user->id,'hide_events',true);
        if(empty($hide_events)){$hide_events=array();}
        $hide_events[]=$body['post_id'];
        update_user_meta($this->user->id,'hide_events',$hide_events);
        $return = array(
            'status'=>1,
            'message'=>__('Event hidden from calendar.','vibecal')
        );
        if(function_exists('vibebp_fireabase_update_stale_requests')){
            vibebp_fireabase_update_stale_requests('global',VIBECAL_API_NAMESPACE.'/event/getEvents');
        }
        return new WP_REST_Response($return, 200); 
    }

    function get_google_cal_details($request){
        $body = json_decode($request->get_body(),true);
        $return = array(
            'status' => 0
        );
        $arr = [];
        $option = get_option('vibe_cal_settings');
        if(!empty($option)){
            if(!empty( $option['google_calendar_googleevent_create'])){
                $arr = array(
                    'google_calendar_googleevent_create' => $option['google_calendar_googleevent_create'],
                    'google_calendar_api_key' => $option['google_calendar_api_key'],
                    'google_calendar_client_id' => $option['google_calendar_client_id']
                );
                $return = array(
                    'status' => 1,
                    'data' => array(
                        'option' => $arr
                    )
                );
            }
        }
        return new WP_REST_Response($return, 200); 
    }

    function get_sync_details(){
        $arr = [];
        $option = get_option('vibe_cal_settings');
        if(!empty($option)){
            if(!empty( $option['google_calendar_googleevent_create'])){
                $arr = array(
                    'google_calendar_googleevent_create' => $option['google_calendar_googleevent_create'],
                    'google_calendar_api_key' => $option['google_calendar_api_key'],
                    'google_calendar_client_id' => $option['google_calendar_client_id']
                );
            }
        }
        return $arr;
    }

    function get_synced_events($request){
        // $google_synced_events = get_user_meta($this->user->id,'google_synced_events',true);
        $google_synced_events =[];
        return new WP_REST_Response(array('status'=>1,'data'=>$google_synced_events), 200);
    }

    function set_synced_event($request){
        $body = json_decode($request->get_body(),true);
        $google_synced_events = get_user_meta($this->user->id,'google_synced_events',true);
        
        $found = false;
        $is_synced = false;
        if(!empty($google_synced_events) && is_array($google_synced_events)){
            if(in_array($body['id'],$google_synced_events)){  $found = true; }
        }else{
            $google_synced_events =[];
        }
        if(!$found){
            $is_synced = true;
            $google_synced_events[]=$body['id'];  
        }
        update_user_meta($this->user->id,'google_synced_events',$google_synced_events);
        return new WP_REST_Response(array('status'=>1,'is_synced'=>$is_synced), 200);
    }

}

Vibe_Cal_API::init();

class vibe_invite extends BP_Invitation_Manager{
    public function __construct( $args = array() ) {
		$this->class_name = VIBECAL_EVENT_INVITE;
	}

    function run_send_action($invitation){
        return true;
    }
    function run_acceptance_action($type,$r){
        return true;
    }
}