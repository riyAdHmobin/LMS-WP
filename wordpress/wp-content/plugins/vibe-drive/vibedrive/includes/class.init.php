<?php
/**
 * VibeDrive Initialise
 *
 * @author 		VibeThemes
 * @category 	Init
 * @package 	vibedrive/Includes
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VibeDrive_Init{

	public static $instance;
    
    public static function init(){

        if ( is_null( self::$instance ) )
            self::$instance = new VibeDrive_Init();
        return self::$instance;
    }

	function __construct(){
		//add_action('files_uploaded_by_user_mydrive',array($this,'files_uploaded_by_user_mydrive'),10);

	}

	function files_uploaded_by_user_mydrive($files){
		$user_id = bp_displayed_user_id();
		$uploaded_files = get_user_meta($user_id,'vibe_mydrive_meta', false);
		//print_r($uploaded_files);

		if(!empty($uploaded_files)){

			foreach($uploaded_files as $k=>$v){
				$icon = wp_mime_type_icon($v['post_mime_type']);
				//print_r($icon);
				//print_r($v['post_mime_type']);
				//echo convertToReadableSize($v['post_size']);
?>
				<table class="uploaded_content" style="width:100%">
					<tr>
						<td>
							<img class="mime_type_icon" style="max-width:20px;max-height:20px;" src="<?php echo $icon;?>" />
							<a href="<?php echo $v['guid'];?>">&nbsp;<?php echo $v['post_title']; ?></a>
						</td>
						<td>
							<?php echo convertToReadableSize($v['post_size']); ?>
						</td>
						<td>
							<?php echo $v['post_access'];?>
						</td>
					</tr>
				</table>
<?php		

			}
			
		}
		else echo '<p class="error">'.__('No Files Present which is Uploaded By this User','vibedrive').'</p>';

	}
}

VibeDrive_Init::init();