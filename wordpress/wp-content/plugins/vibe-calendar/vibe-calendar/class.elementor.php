<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

 class Vibe_Cal_Elementor extends \Elementor\Widget_Base{

    public function get_name() {
		return 'vibebp calendar';
	}

	public function get_title() {
		return __( 'Vibe Calendar', 'vibecal' );
	}

	public function get_icon() {
		return 'vicon vicon-calendar';
	}

	public function get_categories() {
		return [ 'vibebp' ];
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Controls', 'vibecal' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		
		$this->add_control(
			'show_events',
			[
				'label' => __('Show Events', 'vibecal'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'list',
				'options' => array(
	                'list'=>_x('List','elementor widget','vibecal'),
	                'grid' => _x('Grid','elementor widget','vibecal'),
	                'monthly_calendar' => _x('Monthly Calendar','elementor widget','vibecal'),
	               	'weekly_calendar' => _x('Weekly Calendar','elementor widget','vibecal'),
                ),
			]
		);


		$this->add_control(
			'number_of_events',
			[
				'label' =>__('Total Number of Events in View', 'vibecal'),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range'=>[
					'min' => 1,
					'max' => 99,
					'step' => 1,
				],
				'default' => [
					'size'=>1,
				]
			]
		);

		$this->end_controls_section();
	}

	protected function render() {

		$settings = $this->get_settings_for_display();
		

 		if($settings['show_events'] == 'monthly_calendar' || $settings['show_events'] == 'weekly_calendar'){
 			wp_enqueue_style('fullcalendar',plugins_url('assets/css/fullcalendar.min.css',
 				__FILE__),array(),VIBECAL_VERSION);
 			wp_enqueue_script('vibecalendar',plugins_url('assets/css/cal.css',__FILE__),array(),VIBECAL_VERSION);
 			wp_enqueue_script('fullcalendar',plugins_url('assets/js/fullcalendar.min.js',__FILE__),array(),VIBECAL_VERSION,true);
 		}
		wp_enqueue_script('vibe-cal-widget',plugins_url('assets/js/cal_widget.js',__FILE__),array('wp-element','wp-data'),VIBECAL_VERSION,true);

		$blog_id = '';
        if(function_exists('get_current_blog_id')){
            $blog_id = get_current_blog_id();
        }

		wp_localize_script('vibe-cal-widget','vibecalwidget',apply_filters('vibe_cal_widget_script_args',array(
			'api_url'=>get_rest_url($blog_id,VIBECAL_API_NAMESPACE),
			'client_id'=>function_exists('vibebp_get_setting')?vibebp_get_setting('client_id'):'',
			'view'=>($settings['show_events'] == 'monthly_calendar'?'dayGridMonth':'timeGridWeek'),
			'settings'=>$settings,
			'months'=>array(
				1=>__('JAN','vibecal'),
				2=>__('FEB','vibecal'),
				3=>__('MAR','vibecal'),
				4=>__('APR','vibecal'),
				5=>__('MAY','vibecal'),
				6=>__('JUN','vibecal'),
				7=>__('JUL','vibecal'),
				8=>__('AUG','vibecal'),
				9=>__('SEP','vibecal'),
				10=>__('OCT','vibecal'),
				11=>__('NOV','vibecal'),
				12=>__('DEC','vibecal'),
			),
			'translations'=>array(
				'message_text'=>__('Type message','vibecal'),
				'message_subject'=>__('Message subject','vibecal'),
				'cancel'=>__('Cancel','vibecal'),
				'starts'=>__('Starts','vibecal'),
				'ends'=>__('Ends','vibecal'),
				'see_details'=>__('See details','vibecal'),
			)
		)));

		?>
		<div id="vibe_cal_widget">
			<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
		</div>
		<style>
			.vibe_cal_events.list .vibe_cal_event{align-items:center;}span.cal_date{display:flex;flex-direction:row;padding:0 1rem 1rem;align-items:flex-start;line-height:1}span.cal_date>span{flex:1;margin:.5rem;display:flex;flex-direction:column;align-items:center;}span.cal_date>span+span>span:first-child{font-size:2rem}span.cal_date>span>span:first-child{font-size:4rem;font-weight:600}.vibe_cal_event{display:flex}.vibe_cal_events.grid{display:grid;grid-template-columns:1fr 1fr}.vibe_cal_event .event_details{display:flex;flex-direction:column;padding:0 1rem}.vibe_cal_event .event_details a{font-size:1.4rem}.event_details > span{margin:0.75rem 0;}.event_details > span >span { padding: 5px 10px; border-radius: 5px; background: rgba(0,0,0,0.05); margin: 5px; }span.cal_time { padding: 5px; border: 1px solid rgba(0,0,0,0.05); border-radius: 5px; } span.cal_time > span { padding: 5px; }.fc-toolbar .fc-toolbar-chunk:last-child { display: none; } .fc-toolbar { flex-direction: row-reverse; } button.fc-today-button.btn.btn-primary:before { font-family: 'vicon'; content: "\e6b6"; } button.fc-prev-button.btn.btn-primary>span:before { font-family: 'vicon'; content: "\e64a"; } button.fc-next-button.btn.btn-primary>span:before { font-family: vicon; content: "\e649"; } .fc-toolbar-chunk { display: flex; align-items: center; font-size: 1rem; } .fc-toolbar-chunk  button { background: none; padding:0;}div#vibe_cal_widget { position: relative; } .event_wrapper { position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; z-index: 999; } .event_wrapper >.event { background: #fff; z-index: 99; padding: 1rem; border: 1px solid rgba(0,0,0,0.1); box-shadow: 0 5px 50px rgba(0,0,0,0.2); } .event_description { padding: 0 0 1rem 0; } .event_start_end { display: flex; flex-direction: column; } .event_start_end > span { display: flex; justify-content: space-between; }.event_actions{margin-top:1rem;display:flex;justify-content: space-between;align-items:center;}.event_wrapper > span { position: absolute; left: 0; top: 0; width: 100%; height: 100%; z-index: -1; }.show_event .vibe_fullcalendar_wrapper { filter: blur(5px); opacity: 0.8; }.event_start_end .vicon { margin-right: 5px; }
		</style>
		<?php
	}

}