<?php
/**
 * VibeDrive API section
 *
 * @author 		VibeThemes
 * @category 	Init
 * @package 	vibedrive/Includes
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define('VIBE_DRIVE_API_NAMESPACE','vibedrive/v1');

class VibeDrive_API{

	public static $instance;
    
    public static function init(){

        if ( is_null( self::$instance ) )
            self::$instance = new VibeDrive_API();
        return self::$instance;
    }

	private function __construct(){

		add_action('rest_api_init',array($this,'register_api_routes'));
	}

    

	function register_api_routes(){

        $drive_upload_file_scope= vibe_drive_upload_scopes();
        $drive_components = apply_filters('vibedrive_components',array('personal','group','shared'));

		 register_rest_route( VIBE_DRIVE_API_NAMESPACE, '/drive/user/(?P<type>('.implode('|',array_keys($drive_upload_file_scope)).'))/upload_file', array(
            array(
                'methods'             =>  'POST',
                'callback'            =>  array( $this, 'uploaded_file' ),
                'permission_callback'       => array( $this, 'get_file_permissions_check' ),
                'args'                      =>  array(
                        'id'                        =>  array(
                            'validate_callback'     =>  function( $param, $request, $key ) {
                                                        return is_numeric( $param );
                                                    }
                        ),
                    ),
            ),
        ));


        register_rest_route( VIBE_DRIVE_API_NAMESPACE, '/drive/(?P<type>('.implode('|',$drive_components).'))/get/vibedrive', array(
            array(
                'methods'             =>  'POST',
                'callback'            =>  array( $this, 'get_files' ),
                'permission_callback'       => array( $this, 'get_file_permissions_check' ),
                'args'                      =>  array(
                        'id'                        =>  array(
                            'validate_callback'     =>  function( $param, $request, $key ) {
                                                        return is_numeric( $param );
                                                    }
                        ),
                    ),
            ),
        ));


        register_rest_route( VIBE_DRIVE_API_NAMESPACE, '/drive/(?P<action>(save|delete|edit))/(?P<file_id>\d+)/', array(
            array(
                'methods'             =>  'POST',
                'callback'            =>  array( $this, 'file_action' ),
                'permission_callback'       => array( $this, 'get_file_permissions_check' ),
                'args'                      =>  array(
                        'file_id'                        =>  array(
                            'validate_callback'     =>  function( $param, $request, $key ) {
                                                        return is_numeric( $param );
                                                    }
                        ),
                    ),
            ),
        ));


        register_rest_route( VIBE_DRIVE_API_NAMESPACE, '/drive/search/(?P<scope>('.implode('|',array_keys($drive_upload_file_scope)).'))/', array(
            array(
                'methods'             =>  'POST',
                'callback'            =>  array( $this, 'search_incope' ),
                'permission_callback'       => array( $this, 'get_file_permissions_check' ),
                'args'                      =>  array(
                        'search'                        =>  array(
                            'validate_callback'     =>  function( $param, $request, $key ) {
                                                        return is_numeric( $param );
                                                    }
                        ),
                    ),
            ),
        ));

        register_rest_route( VIBE_DRIVE_API_NAMESPACE, '/drive/file/(?P<file_id>\d+)/(?P<scope>('.implode('|',array_keys($drive_upload_file_scope)).'))/', array(
            array(
                'methods'             =>  'POST',
                'callback'            =>  array( $this, 'get_scope_ids' ),
                'permission_callback'       => array( $this, 'get_file_permissions_check' ),
            ),
        ));

    }

    //update metas only, upload done by tus
    function uploaded_file($request){
        $body = json_decode($request->get_body(),true);

        $type= $request->get_param('type');
        $module_id = $body['module_id'];
        $user_id = $this->user->id;
        $attachment_id = $body['attachment_id'];
        $return = array(
            'status' => 0
        );
        $file_size = $body['file_size'];
        if($attachment_id && $this->can_edit($attachment_id,$user_id) && !empty($file_size)){
            if(!empty($module_id) && $type == "group"){
                if(!groups_is_user_member( $user_id, $module_id )){
                    return new WP_REST_Response(array(
                        'status'=>0,
                        'message'=>_x('Unable to upload in group','api','vibedrive')
                    ),200);
                }    
            }    
            $attachment_url= wp_get_attachment_url($attachment_id);
            if(!empty($attachment_id)){
                add_post_meta( $attachment_id, 'vibedrive', $type );
                add_post_meta( $attachment_id, 'vibedrive_size', convertToReadableSize($file_size) );
                
                // share in any module
                if(!empty($module_id)){
                    delete_post_meta($attachment_id,'vibedrive_ID');
                    add_post_meta($attachment_id,'vibedrive_ID',$module_id);
                }
            }
            $attachment_post = get_post($attachment_id);
            if(function_exists('vibebp_fireabase_update_stale_requests')){
                vibebp_fireabase_update_stale_requests('global','/get/vibedrive');
            }
            
            $return = array(
                'status'=>1,
                'message'=>__('File uploaded','vibedrive'),
                'file'=>array(
                    'author'=> $user_id,
                    'date'=> $attachment_post->post_date,
                    'id'=> $attachment_id,
                    'mime_type'=> get_post_mime_type($attachment_id),
                    'size'  => get_post_meta($attachment_id,'vibedrive_size',true),
                    'name'=> $attachment_post->post_title,
                    'type'=> $type,
                    'url'=>$attachment_url,
                    'shared_values'=>get_post_meta($attachment_id,'vibedrive_ID',false)
                )
            );
        }
        
        return new WP_REST_Response($return,200);
    }


    function uploaded_file_permissions_check($request){

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

    function can_edit($file_id,$user_id){
        return get_post_field('post_author',$file_id) == $user_id;
    }


    function get_files($request){
        $body = json_decode($request->get_body(),true);

        $type= $request->get_param('type'); //Personal , group
        $module_id = $body['module_id'];
        $scope = $body['scope'];

        global $wpdb, $bp;
        $mygroups = array();
        $args =  apply_filters('vibedrive_api_get_files',array(
            'posts_per_page'=> 20,
            'post_type'     => 'attachment',
            'post_status'   => 'any',
            's'             => $body['search'],
            'paged'          => $body['paged'],
            'orderby'       => $body['orderby'],
            'order'         => $body['order'],
            'meta_query'    =>Array(
                'relation' => 'AND',
                array(
                    'key'=>'vibedrive',
                    'compare'=>'EXISTS'
                ),
                array(
                    'key'=>'vibedrive_size',
                    'compare'=>'EXISTS'
                ),
            )
        ),$request);

       $files = array();
       switch($type){
            case 'personal':
                    $args['author']=$this->user->id;
                break;
            case 'shared':
                    $args['meta_query']['relation']= 'AND';
                    $args['meta_query'][]=array(
                        'key'=>'vibedrive',
                        'value'=>'shared',
                        'compare'=>'='
                    );
                    $args['meta_query'][]= array(
                        'key'=>'vibedrive_ID',
                        'value'=>$this->user->id,
                        'compare'=>'='
                    );
                break;
            case 'group':
                    if(empty($module_id)){
                        $args['meta_query']['relation']= 'AND';
                        $args['meta_query'][]=array(
                            'key'=>'vibedrive',
                            'value'=>'group',
                            'compare'=>'='
                        );
                        $mygroups = $wpdb->get_results($wpdb->prepare("
                            SELECT group_id as id
                            FROM {$bp->groups->table_name_members} 
                            WHERE user_id = %d",
                            $this->user->id),ARRAY_A);
                        $nmygroups = array();
                        if(!empty($mygroups) && is_array($mygroups)){
                            foreach ($mygroups as $value) {$nmygroups[] = $value['id'];}
                        }else{  $nmygroups[] = 19999999;} //force empty 
                        $args['meta_query'][]= array(
                            'key'=>'vibedrive_ID',
                            'value'=> $nmygroups,
                            'compare'=>'IN'
                        );
                    }else{
                        if(!empty($module_id)){
                            if(function_exists('groups_is_user_member') && groups_is_user_member( $this->user->id, $module_id )){
                                $args['meta_query'][]=array(
                                    'key'=>'vibedrive',
                                    'value'=>'group',
                                    'compare'=>'='
                                );
                                $args['meta_query'][]= array(
                                    'key'=>'vibedrive_ID',
                                    'value'=> $module_id,
                                    'compare'=>'='
                                );
                            }else{
                                return new WP_REST_Response(array(
                                    'status'=>0,
                                    'message'=>_x('Unable to access group files','api','vibedrive')
                                ),200);
                            }
                        }else{
                            return new WP_REST_Response(array(
                                'status'=>0,
                                'message'=>_x('Group id missing!','api','vibedrive')
                            ),200);
                        }
                    }
                break;
        }

        if($args['orderby'] == 'size'){
            $args['orderby']='meta_value_num';
            $args['meta_key'] = 'vibedrive_size';
        }

        $args = apply_filters('vibedrive_get_files_api',$args,$body);

       
        $query = new WP_Query($args);

        if(!empty($query->have_posts())){
            while($query->have_posts()){
                $query->the_post();
                global $post;
                $files[] = array(
                    'id'=>$post->ID,
                    'name'=>$post->post_title,
                    'date'=>strtotime($post->post_date),
                    'author'=>$post->post_author,
                    'type'  => get_post_meta($post->ID,'vibedrive',true),
                    'url' => $post->guid,
                    'size'  => convertToReadableSize(get_post_meta($post->ID,'vibedrive_size',true)),
                    'mime_type' => $post->post_mime_type,
                    'shared_values'=>get_post_meta($post->ID,'vibedrive_ID',false)
                );
            }
        }
        wp_reset_postdata();
        return new WP_REST_Response(array('status'=>1,'total'=>$query->found_posts,'files'=>$files),200);
    }

    function get_file_permissions_check($request){

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
            global $wpdb;

            $this->user = apply_filters('vibebp_api_get_user_from_token','',$body['token']);
            if(!empty($this->user)){
                return true;
            }
        }
        return false;
    }


    function file_action($request){
        $body = json_decode($request->get_body(),true);
        $file_id = $request->get_param('file_id');
        $action = $request->get_param('action');
        
        $user_id = $this->user->id;

        $return = array('status'=>0);
        
        if(!vibedrive_usercan($this->user->id,$file_id,$action) || !is_numeric($file_id)){
            $return = array('status'=>0,'message'=>_x('Access denied','api','vibedrive'));
        }else{
            switch($action){
                case 'delete':
                    $post = wp_delete_attachment( $file_id);
                    if($post){
                        $return = array('status'=>1,'message'=>sprintf(_x('File %s deleted.','api','vibedrive'),$post->post_title));
                    }
                break;
                case 'save':
                    update_post_meta($file_id,'vibedrive',$body['file']['type']);
                    delete_post_meta($file_id,'vibedrive_ID');
                    foreach($body['file']['shared_values'] as $shared_value){
                        add_post_meta($file_id,'vibedrive_ID',$shared_value);
                    }
                    $return = array('status'=>1,'message'=>_x('File shared','api','vibedrive'));
                break;
                case 'edit':
                    if(!empty($body['name'])){
                        $args = array(
                            'ID'=>$file_id,
                            'post_title' => $body['name']
                        );
                        $id = wp_update_post($args);
                        if(is_numeric($id)){
                            $return = array('status'=>1,'message'=>_x('File name updated','api','vibedrive'));
                        }else{
                            $return = array('status'=>0,'message'=>_x('Update failed.','api','vibedrive'));
                        } 
                    }
                break;
            }
        }

        if(!empty($return['status'])){
            if(function_exists('vibebp_fireabase_update_stale_requests')){
                vibebp_fireabase_update_stale_requests('global','/get/vibedrive');
            }
        }
        return new WP_REST_Response($return,200);    
    }


    function search_incope($request){
        $args = json_decode($request->get_body(),true);
        $scope= $request->get_param('scope');
        $search = $args['search'];
        $return = array( 'status' => 1,'values'=>[] );
        if (!empty($scope) && !empty($search)) {
            switch ($scope) {
                case 'personal':
                    global $wpdb;
                    $results = $wpdb->get_results("SELECT ID,display_name FROM {$wpdb->users} WHERE `user_nicename` LIKE '%{$search}%' OR 
                        `user_email` LIKE '%{$search}%' OR `user_login` LIKE '%{$search}%' OR `display_name` LIKE '%{$search}%'", ARRAY_A);
                    if (!empty($results)) {
                        $return['status']=1;
                        foreach ($results as $result) {
                            $return['values'][]=array('id'=>$result['ID'],'label'=>$result['display_name']);
                        }
                    }
                break;
                case 'shared':
                    global $wpdb;
                    $results = $wpdb->get_results("SELECT ID,display_name FROM {$wpdb->users} WHERE `user_nicename` LIKE '%{$search}%' OR 
                        `user_email` LIKE '%{$search}%' OR `user_login` LIKE '%{$search}%' OR `display_name` LIKE '%{$search}%'", ARRAY_A);
                    if (!empty($results)) {
                        $return['status']=1;
                        foreach ($results as $result) {
                            $return['values'][]=array('id'=>$result['ID'],'label'=>$result['display_name']);
                        }
                    }
                break;
                case 'group':
                    if (function_exists('bp_is_active') && bp_is_active('groups')) {
                        global $wpdb, $bp;
                        $results = $wpdb->get_results("SELECT id,name FROM {$bp->groups->table_name} WHERE `name` LIKE '%{$search}%' OR 
                            `slug` LIKE '%{$search}%'", ARRAY_A);
                        if (!empty($results)) {
                            $return['status']=1;
                            foreach ($results as $result) {
                                $return['values'][]=array('id'=>$result['id'],'label'=>$result['name']);
                            }
                        }
                    }
                break;
            }
        }
         return new WP_REST_Response($return,200);
    }

    function search_file_permissions_check($request){
       $security = $request->get_param('security');
        if($security != vibe_drive_get_security()){
            return false;
        }
        return true;
    }


    function get_scope_ids($request){
        global $wpdb,$bp;
        $body = json_decode($request->get_body(),true);
        $ids = $body['ids'];
        $scope = $request->get_param('scope');
        switch($scope){
            case 'personal':
            case 'shared':
                $results = $wpdb->get_results( "SELECT id,display_name as label FROM {$wpdb->users} WHERE `id` IN  (".implode(',',$ids).")", ARRAY_A );
            break;
            case 'group':
                $results = $wpdb->get_results( "SELECT id,name as label FROM {$bp->groups->table_name} WHERE `id` IN  (".implode(',',$ids).")", ARRAY_A );
            break;
        }
        return new WP_REST_Response($results,200);
    }

}

VibeDrive_API::init();