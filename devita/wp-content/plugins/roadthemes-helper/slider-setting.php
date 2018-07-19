<?php 

if( ! function_exists( 'road_get_slider_setting' ) ) {
	function road_get_slider_setting() {
		$status_opt = array(
			'',
			__( 'Yes', 'devita' ) => true,
			__( 'No', 'devita' ) => false,
		);
		
		$effect_opt = array(
			'',
			__( 'Fade', 'devita' ) => 'fade',
			__( 'Slide', 'devita' ) => 'slide',
		);
	 
		return array( 
			array(
				'type' => 'checkbox',
				'heading' => __( 'Enable slider', 'devita' ),
				'param_name' => 'enable_slider',
				'value' => true,
				'save_always' => true, 
				'group' => __( 'Slider Options', 'devita' ),
			),
			array(
				'type' => 'textfield',
				'heading' => __( 'Items Default', 'devita' ),
				'param_name' => 'items',
				'group' => __( 'Slider Options', 'devita' ),
				'value' => 5,
			),
			array(
				'type' => 'textfield',
				'heading' => __( 'Item Desktop', 'devita' ),
				'param_name' => 'item_desktop',
				'group' => __( 'Slider Options', 'devita' ),
				'value' => 4,
			), 
			array(
				'type' => 'textfield',
				'heading' => __( 'Item Small', 'devita' ),
				'param_name' => 'item_small',
				'group' => __( 'Slider Options', 'devita' ),
				'value' => 3,
			),
			array(
				'type' => 'textfield',
				'heading' => __( 'Item Tablet', 'devita' ),
				'param_name' => 'item_tablet',
				'group' => __( 'Slider Options', 'devita' ),
				'value' => 2,
			),
			array(
				'type' => 'textfield',
				'heading' => __( 'Item Mobile', 'devita' ),
				'param_name' => 'item_mobile',
				'group' => __( 'Slider Options', 'devita' ),
				'value' => 1,
			),
			array(
				'type' => 'dropdown',
				'heading' => __( 'Navigation', 'devita' ),
				'param_name' => 'navigation',
				'value' => $status_opt,
				'save_always' => true,
				'group' => __( 'Slider Options', 'devita' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => __( 'Pagination', 'devita' ),
				'param_name' => 'pagination',
				'value' => $status_opt,
				'save_always' => true,
				'group' => __( 'Slider Options', 'devita' )
			),
			array(
				'type' => 'textfield',
				'heading' => __( 'Speed sider', 'devita' ),
				'param_name' => 'speed',
				'value' => '500',
				'save_always' => true,
				'group' => __( 'Slider Options', 'devita' )
			),
			array(
				'type' => 'checkbox',
				'heading' => __( 'Slider Auto', 'devita' ),
				'param_name' => 'auto',
				'value' => false, 
				'group' => __( 'Slider Options', 'devita' )
			),
			array(
				'type' => 'checkbox',
				'heading' => __( 'Slider loop', 'devita' ),
				'param_name' => 'loop',
				'value' => false, 
				'group' => __( 'Slider Options', 'devita' )
			),
			array(
				'type' => 'dropdown',
				'heading' => __( 'Effects', 'devita' ),
				'param_name' => 'effect',
				'value' => $effect_opt,
				'save_always' => true,
				'group' => __( 'Slider Options', 'devita' )
			), 
		);
	}
}