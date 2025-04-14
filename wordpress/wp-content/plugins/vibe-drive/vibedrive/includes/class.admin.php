<?php
/**
 * VibeDrive Admin section
 *
 * @author 		VibeThemes
 * @category 	Init
 * @package 	vibedrive/Includes
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



class VibeDrive_Admin{

	public static $instance;
    
    public static function init(){

        if ( is_null( self::$instance ) )
            self::$instance = new VibeDrive_Admin();
        return self::$instance;
    }

	private function __construct(){
		$this->option = 'vibedrive_settings';
		add_action('admin_menu',array($this,'create_settings_page'));
	}

	function create_settings_page(){

		add_options_page( _x('VibeDrive','setting page title','vibedrive'), _x('VibeDrive','setting page title','vibedrive'),'manage_options', 'vibedrive', array($this,'settings'));
	}

	function settings(){


		$tabs = apply_filters('vibedrive_settings_tabs',array(
			''=>_x('General','settings tab','vibedrive'),
			'members'=>_x('Members','settings tab','vibedrive'),
			'groups'=>_x('Groups','settings tab','vibedrive'),
		));
		?>
		<div class="wrap">

			<h1><?php _ex('VibeDrive','settings page title','vibedrive'); ?> </h1>

			<h2 class="nav-tab-wrapper">
				<?php
					foreach($tabs as $slug => $tab){
						echo '<a href="'.admin_url( 'options-general.php?page=vibedrive&tab='.$slug).'" class="nav-tab '.(($_GET['tab'] == $slug)?'nav-tab-active':'').'">'.$tab.'</a>';
					}
				?>	
			</h2>
			<?php
			if(empty($_GET['tab'])){
				$slug = 'general';
			}else{
				$slug = sanitize_title($_GET['tab']);
			}

			$this->save($slug);

			$function_name = 'vibedrive_'.$slug;
			if(function_exists($function_name)){
				$function_name();
			}else{
				$this->$slug();
			}
			?>
		</div>
		<?php
	}

	function save($slug){

		if ( !empty($_POST) && check_admin_referer('vibe_drive_settings','_wpnonce') ){

			$vibedrive_settings = array();
			$vibedrive_settings = get_option($this->option);

			//echo "<pre>"; print_r($_POST);
			//print_r($vibedrive_settings);


			unset($_POST['_wpnonce']);
			unset($_POST['_wp_http_referer']);
			unset($_POST['save']);
			switch($slug){
				case 'members':
					$vibedrive_settings['members'] = $_POST;
				break;
				case 'groups':
					$vibedrive_settings['groups'] = $_POST;
				break;
				default:
					$vibedrive_settings['general'] = $_POST;
				break;
			}
			
			update_option($this->option,$vibedrive_settings);

			echo '<div class="updated"><p>'.__('Settings Saved','vibedrive').'</p></div>';
		}


	}




	function vibedrive_settings_generate_form($tab,$settings=array()){

		if(empty($settings))
			return;
	
		echo '<form method="post">';
		wp_nonce_field('vibe_drive_settings','_wpnonce');
		echo '<table class="form-table">
				<tbody>';

		$vibedrive_settings = array();
		$vibedrive_settings = get_option($this->option);


		foreach($settings as $setting ){
			echo '<tr valign="top" '.(empty($setting['class'])?'':'class="'.$setting['class'].'"').'>';
			switch($setting['type']){
				case 'select':
					echo '<th scope="row" class="titledesc"><label>'.$setting['label'].'</label></th>';
					echo '<td class="forminp"><select name="'.$setting['name'].'">';
					foreach($setting['options'] as $key=>$option){
						echo '<option value="'.$key.'" '.(isset($vibedrive_settings[$tab][$setting['name']])?selected($key,$vibedrive_settings[$tab][$setting['name']]):'').'>'.$option.'</option>';
					}
					echo '</select>';
					echo '<span>'.$setting['desc'].'</span></td>';
				break;
				case 'number':
					echo '<th scope="row" class="titledesc"><label>'.$setting['label'].'</label></th>';
					echo '<td class="forminp"><input type="number" name="'.$setting['name'].'" value="'.(!empty($vibedrive_settings[$tab][$setting['name']])?$vibedrive_settings[$tab][$setting['name']]:(!empty($setting['default'])?$setting['default']:'')).'" />';
					echo '<span>'.$setting['desc'].'</span></td>';
				break;
				case 'multiselect':
					echo '<th scope="row" class="titledesc"><label>'.$setting['label'].'</label></th>';

					echo '<td class="forminp">';
					$std = isset( $setting['std'] ) ? $setting['std'] : '';
					echo '<select name="file_types[]" id="file_types[]" class="chzn-select v_option' . $content_class . '" multiple=multiple style="min-width:300px;" data-placeholder="Choose options...">'
					. ( ( '' == $std ) ? '<option value="nothing_selected">  ' . esc_html__('Select', 'vibedrive') . '  </option>' : '' );

					foreach ( $setting['options'] as $key=>$setting_value ){ 
				 		//print_r($setting['options']);
				 		echo '<option value="' . esc_attr( $key ) . '"' . (in_array( $key, $vibedrive_settings['general']['file_types'] ) ? "selected" : "" ). '>' . esc_html( $setting_value['value'] ) . '</option>';

					}

					echo '</select></td>';
				break;

				default:
					echo '<th scope="row" class="titledesc"><label>'.$setting['label'].'</label></th>';
					echo '<td class="forminp"><input type="text" name="'.$setting['name'].'" value="'.(isset($vibedrive_settings[$tab][$setting['name']])?$vibedrive_settings[$tab][$setting['name']]:(isset($setting['default'])?$setting['default']:'')).'" />';
					echo '<span>'.$setting['desc'].'</span></td>';
				break;
			}
			
			echo '</tr>';
		}
		echo '</tbody>
		</table>';
		if(!empty($settings))
			echo '<input type="submit" name="save" value="'.__('Save Settings','vibedrive').'" class="button button-primary" /></form>';
	}	


	function general(){

		echo '<h3>'.__('General Settings','vibe-customtypes').'</h3>';

		$general_settings_array = apply_filters('vibedrive_general_settings_tab',array(
			'general'=> __('General Settings','vibedrive'),

			));
		echo '<ul class="subsubsub">';

		foreach($general_settings_array as $k=>$value){
			if(empty($_GET['sub']) && empty($current)){
				$current = $k;
			}else if(!empty($_GET['sub']) && empty($current)){
				$current = $_GET['sub'];
			}
			echo '<li><a href="?page=vibedrive-settings&tab=general&sub='.$k.'" '.(($k == $current)?'class="current"':'').'>'.$value.'</a>  &#124; </li>';
		}

		echo '</ul><div class="clear"><hr/>';
		if(!isset($_GET['sub'])){$_GET['sub']='';}
		switch($_GET['sub']){
			default:
				$settings= apply_filters('vibedrive_general_settings',array(
				
				array( // Text Input
						'label'	=> __('Attachment Type','vibedrive'), // <label>
						'desc'	=> __('Select valid attachment types ','vibedrive'), // description
						'name' => 'vibedrive_attachment_type',
						'id'	=> 'attachment_type', // field id and name
						'type'	=> 'multiselect', // type of field
						'options' => array(
							array('value'=> 'JPG','label' =>'JPG'),
							array('value'=> 'GIF','label' =>'GIF'),
							array('value'=> 'PNG','label' =>'PNG'),
							array('value'=> 'PDF','label' =>'PDF'),
							array('value'=>'PSD','label'=>'PSD'),
							array('value'=> 'DOC','label' =>'DOC'),
							array('value'=> 'DOCX','label' => 'DOCX'),
							array('value'=> 'PPT','label' =>'PPT'),
							array('value'=> 'PPTX','label' => 'PPTX'),
							array('value'=> 'PPS','label' =>'PPS'),
							array('value'=> 'PPSX','label' => 'PPSX'),
							array('value'=> 'ODT','label' =>'ODT'),
							array('value'=> 'XLS','label' =>'XLS'),
							array('value'=> 'XLSX','label' => 'XLSX'),
							array('value'=> 'MP3','label' =>'MP3'),
							array('value'=> 'M4A','label' =>'M4A'),
							array('value'=> 'OGG','label' =>'OGG'),
							array('value'=> 'WAV','label' =>'WAV'),
							array('value'=> 'WMA','label' =>'WMA'),
							array('value'=> 'MP4','label' =>'MP4'),
							array('value'=> 'M4V','label' =>'M4V'),
							array('value'=> 'MOV','label' =>'MOV'),
							array('value'=> 'WMV','label' =>'WMV'),
							array('value'=> 'AVI','label' =>'AVI'),
							array('value'=> 'MPG','label' =>'MPG'),
							array('value'=> 'OGV','label' =>'OGV'),
							array('value'=> '3GP','label' =>'3GP'),
							array('value'=> '3G2','label' =>'3G2'),
							array('value'=> 'FLV','label' =>'FLV'),
							array('value'=> 'WEBM','label' =>'WEBM'),
							array('value'=> 'APK','label' =>'APK '),
							array('value'=> 'RAR','label' =>'RAR'),
							array('value'=> 'ZIP','label' =>'ZIP'),
				        ),
				        'std'   => 'single'
				        
					),
				array(
						'label' => __('Total VibeDrive Allocation Size','vibedrive'),
						'name' => 'vibedrive_allocation_size',
						'type' => 'number',
						'desc' => __('Set the VibeDrive Allocation Size here(in mb)','vibedrive')
					),

				));

				$this->vibedrive_settings_generate_form('general',$settings);
				break;
		}
	}

	function members(){
		echo '<h3>'.__('Settings for Per Member Space Allocated','vibedrive').'</h3>';

		$members_settings_array = apply_filters('vibedrive_members_settings_tab',array(
			'members'=> __('Members Settings','vibedrive'),
			));

		echo '<ul class="subsubsub">';

		foreach($members_settings_array as $k=>$value){
			if(empty($_GET['sub']) && empty($current)){
				$current = $k;
			}else if(!empty($_GET['sub']) && empty($current)){
				$current = $_GET['sub'];
			}
			echo '<li><a href="?page=vibedrive-settings&tab=members&sub='.$k.'" '.(($k == $current)?'class="current"':'').'>'.$value.'</a>  &#124; </li>';
		}

		echo '</ul><div class="clear"><hr/>';


		if(!isset($_GET['sub'])){$_GET['sub']='';}
		switch($_GET['sub']){
			default:
				$settings= apply_filters('vibedrive_members_settings',array(
				array(
						'label' => __('Total VibeDrive Allocation Size For Each Member','vibedrive'),
						'name' => 'vibedrive_allocation_size_member',
						'type' => 'number',
						'desc' => __('Set the VibeDrive Allocation Size here for each member','vibedrive')
					),
				));

				$this->vibedrive_settings_generate_form('members',$settings);
				break;
		}
	}



	function groups(){
		echo '<h3>'.__('Settings for Per Group Space Allocated','vibedrive').'</h3>';

		$groups_settings_array = apply_filters('vibedrive_groups_settings_tab',array(
			'groups'=> __('Groups Settings','vibedrive'),
			));

		echo '<ul class="subsubsub">';

		foreach($groups_settings_array as $k=>$value){
			if(empty($_GET['sub']) && empty($current)){
				$current = $k;
			}else if(!empty($_GET['sub']) && empty($current)){
				$current = $_GET['sub'];
			}
			echo '<li><a href="?page=vibedrive-settings&tab=groups&sub='.$k.'" '.(($k == $current)?'class="current"':'').'>'.$value.'</a>  &#124; </li>';
		}

		echo '</ul><div class="clear"><hr/>';


		if(!isset($_GET['sub'])){$_GET['sub']='';}
		switch($_GET['sub']){
			default:
				$settings= apply_filters('vibedrive_groups_settings',array(
				array(
						'label' => __('Total VibeDrive Allocation Size For Each Group','vibedrive'),
						'name' => 'vibedrive_allocation_size_group',
						'type' => 'number',
						'desc' => __('Set the VibeDrive Allocation Size here for each group','vibedrive')
					),
				));

				$this->vibedrive_settings_generate_form('groups',$settings);
				break;
		}
	}

}

VibeDrive_Admin::init();