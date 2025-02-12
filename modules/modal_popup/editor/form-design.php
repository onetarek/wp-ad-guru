<?php

$design_form_args = array(
	'id' => 'mp_design_form',
	'header_callback' => 'adguru_modal_popup_form_design_header_callback',
	'footer_callback' => 'adguru_modal_popup_form_design_footer_callback',
	'fields' => array(
		
		//----------START CONTAINER --------------------------------------------------------
		"df_header_container" => array(
			'type' 	=> 'header',
			'id' 	=> 'df_header_container',
			'value'	=> __("CONTAINER", 'wp-ad-guru' ),
			'align' => 'left',
			'tag'	=>'h3',
			'single_column' => true
		),
		"design_container_border_enable" => array(
			'type' 	=> 'checkbox',
			'id' 	=> 'design_container_border_enable',
			'label' => __('Show border', 'wp-ad-guru' ),
			'default'	=> "0",
			'help'	=> __('Check this if you want to show a border around the popup container' , 'wp-ad-guru' )
		),
		"design_container_border_group" => array(
			'type' 	=> 'group',
			'group_type' => 'row',
			'id' 	=> 'design_container_border_group',
			'fieldset' => array('legend'=>__( 'Border', 'wp-ad-guru' ) ),
			'fields'=> array(
				"design_container_border_width" => array(
					'type' 	=> 'slider',
					'id' 	=> 'design_container_border_width',
					'label' => __('Width', 'wp-ad-guru' ),
					'default'	=> 5,
					'min' 	=> 0,
					'max' 	=> 100,
					'step' 	=> 1,
					'display_text' => 'px',
					'help'	=> __('Thickness of cotainer border in pixel', 'wp-ad-guru' ),
				),
				"design_container_border_style" => array(
					'type' 	=> 'select',
					'id' 	=> 'design_container_border_style',
					'label' => 'Style',
					'default'	=> "solid",
					'help'	=> __('Style of cotainer border', 'wp-ad-guru' ),
					'options' => array(
						'solid'		=> 'solid',
						'dotted'	=> 'dotted',
						'dashed'	=> 'dashed',
						'double'	=> 'double',
						'groove'	=> 'groove',
						'ridge'		=> 'ridge',
						'inset'		=> 'inset',
						'outset'	=> 'outset',
						'initial'	=> 'initial',
						'inherit'	=> 'inherit',
						'hidden'	=> 'hidden',
						'none'		=> 'none'
					)
				),
				"design_container_border_color" => array(
					'type' 	=> 'color',
					'id' 	=> 'design_container_border_color',
					'label' => __('Color', 'wp-ad-guru' ),
					'default'	=> "#dddddd",
					'help'	=> __('Color of cotainer border', 'wp-ad-guru' ),
				),
				"design_container_border_radius" => array(
					'type' 	=> 'slider',
					'id' 	=> 'design_container_border_radius',
					'label' => __('Radius', 'wp-ad-guru' ),
					'default'	=> 0,
					'min' 	=> 0,
					'max' 	=> 500,
					'step' 	=> 1,
					'display_text' => 'px',
					'help'	=> __('Radius of cotainer border in pixel', 'wp-ad-guru' ),
				),
				
			)// end of group fields
		),//end of design_container_border_group
		"design_container_padding" => array(
			'type' 	=> 'slider',
			'id' 	=> 'design_container_padding',
			'label' => __('Padding', 'wp-ad-guru' ),
			'default'	=> 0,
			'min' 	=> 0,
			'max' 	=> 100,
			'step' 	=> 1,
			'display_text' => 'px',
			'help'	=> __('Space between the cotainer border and content in pixel', 'wp-ad-guru' ),
		),
		"design_container_background_enable" => array(
			'type' 	=> 'checkbox',
			'id' 	=> 'design_container_background_enable',
			'label' => __('Use Background', 'wp-ad-guru' ),
			'default'	=> "1",
			'help'	=> __('Check this if you want to use background of the popup container' , 'wp-ad-guru' )
		),
		"design_container_background_group" => array(
			'type' 	=> 'group',
			'group_type' => 'row',
			'id' 	=> 'design_container_background_group',
			'fieldset' => array('legend'=>__( 'Background', 'wp-ad-guru' ) ),
			'fields'=> array(
				"design_container_background_color" => array(
					'type' 	=> 'color',
					'id' 	=> 'design_container_background_color',
					'label' => __('Color', 'wp-ad-guru' ),
					'default'	=> "#ffffff",
					'help'	=> __('Color of cotainer background', 'wp-ad-guru' ),
				),
				"design_container_background_opacity" => array(
					'type' 	=> 'slider',
					'id' 	=> 'design_container_background_opacity',
					'label' => __('Opacity', 'wp-ad-guru' ),
					'default'	=> 100,
					'min' 	=> 0,
					'max' 	=> 100,
					'step' 	=> 1,
					'display_text' => '%',
					'help'	=> __('Opacity of cotainer background in %', 'wp-ad-guru' ),
				),
			)
		),//end of design_container_background_group
		"design_container_box_shadow_enable" => array(
			'type' 	=> 'checkbox',
			'id' 	=> 'design_container_box_shadow_enable',
			'label' => __('Show Shadow', 'wp-ad-guru' ),
			'default'	=> "1",
			'help'	=> __('Check this if you want to show box shadow of the popup container' , 'wp-ad-guru' )
		),
		"design_container_box_shadow_group" => array(
			'type' 	=> 'group',
			'group_type' => 'row',
			'id' 	=> 'design_container_box_shadow_group',
			'fieldset' => array('legend'=>__( 'Box shadow', 'wp-ad-guru' ) ),
			'fields'=> array(
				"design_container_box_shadow_h_offset" => array(
					'type' 	=> 'slider',
					'id' 	=> 'design_container_box_shadow_h_offset',
					'label' => __('Horizontal position', 'wp-ad-guru' ),
					'default'	=> 1,
					'min' 	=> 0,
					'max' 	=> 100,
					'step' 	=> 1,
					'display_text' => 'px',
					'help'	=> __('Horizontal position of cotainer box shadow in pixel', 'wp-ad-guru' ),
				),
				"design_container_box_shadow_v_offset" => array(
					'type' 	=> 'slider',
					'id' 	=> 'design_container_box_shadow_v_offset',
					'label' => __('Vertical position', 'wp-ad-guru' ),
					'default'	=> 1,
					'min' 	=> 0,
					'max' 	=> 100,
					'step' 	=> 1,
					'display_text' => 'px',
					'help'	=> __('Vertical position of cotainer box shadow in pixel', 'wp-ad-guru' ),
				),
				"design_container_box_shadow_blur_radius" => array(
					'type' 	=> 'slider',
					'id' 	=> 'design_container_box_shadow_blur_radius',
					'label' => __('Blur radius', 'wp-ad-guru' ),
					'default'	=> 3,
					'min' 	=> 0,
					'max' 	=> 100,
					'step' 	=> 1,
					'display_text' => 'px',
					'help'	=> __('Blur radius of cotainer box shadow in pixel', 'wp-ad-guru' ),
				),
				"design_container_box_shadow_spread" => array(
					'type' 	=> 'slider',
					'id' 	=> 'design_container_box_shadow_spread',
					'label' => __('Spread', 'wp-ad-guru' ),
					'default'	=> 0,
					'min' 	=> 0,
					'max' 	=> 100,
					'step' 	=> 1,
					'display_text' => 'px',
					'help'	=> __('Spread of cotainer box shadow in pixel', 'wp-ad-guru' ),
				),
				"design_container_box_shadow_color" => array(
					'type' 	=> 'color',
					'id' 	=> 'design_container_box_shadow_color',
					'label' => __('Color', 'wp-ad-guru' ),
					'default'	=> "#000000",
					'help'	=> __('Color of cotainer box shadow', 'wp-ad-guru' ),
				),
				"design_container_box_shadow_opacity" => array(
					'type' 	=> 'slider',
					'id' 	=> 'design_container_box_shadow_opacity',
					'name' 	=> 'design[container][box-shadow-opacity]',
					'label' => __('Opacity', 'wp-ad-guru' ),
					'default'	=> 25,
					'min' 	=> 0,
					'max' 	=> 100,
					'step' 	=> 1,
					'display_text' => '%',
					'help'	=> __('Opacity of cotainer box shadow in %', 'wp-ad-guru' ),
				),
				"design_container_box_shadow_inset" => array(
					'type' 	=> 'select',
					'id' 	=> 'design_container_box_shadow_inset',
					'label' => __('Inset', 'wp-ad-guru' ),
					'default'	=> 'no',
					'options' => array('no'=>'No', 'yes'=>'Yes'),
					'help'	=> __('Set the box shadow to inset (inner shadow)', 'wp-ad-guru' ),
				),
			)
		),//end of design_container_box_shadow_group
		"design_container_custom_css_class" => array(
			'type' 	=> 'text',
			'id' 	=> 'design_container_custom_css_class',
			'label' => __('Custom CSS class', 'wp-ad-guru' ),
			'default'	=> '',
			'size'  => 'medium',
			'help'	=> __('Add custom CSS class name to the container element. For multiple class names use space between two classes', 'wp-ad-guru' ),
		),
		//----------END CONTAINER --------------------------------------------------------
		//----------START CLOSE BUTTON ----------------------------------------------------
		
		"df_header_close" => array(
			'type' 	=> 'header',
			'id' 	=> 'df_header_close',
			'value'	=> __("CLOSE BUTTON", 'wp-ad-guru' ),
			'align' => 'left',
			'tag'	=>'h3',
			'single_column' => true
		),
		"design_close_height" => array(
			'type' 	=> 'slider',
			'id' 	=> 'design_close_height',
			'label' => __('Height', 'wp-ad-guru' ),
			'default'	=> 30,
			'min' 	=> 0,
			'max' 	=> 100,
			'step' 	=> 1,
			'display_text' => 'px',
			'help'	=> __('Height of the close button in pixel. Note : this value includes padding and border width value', 'wp-ad-guru' ),
		),
		"design_close_width" => array(
			'type' 	=> 'slider',
			'id' 	=> 'design_close_width',
			'label' => __('Width', 'wp-ad-guru' ),
			'default'	=> 30,
			'min' 	=> 0,
			'max' 	=> 100,
			'step' 	=> 1,
			'display_text' => 'px',
			'help'	=> __('Width of the close button in pixel. Note : this value includes padding and border width value', 'wp-ad-guru' ),
		),
		"design_close_padding" => array(
			'type' 	=> 'slider',
			'id' 	=> 'design_close_padding',
			'label' => __('Padding', 'wp-ad-guru' ),
			'default'	=> 0,
			'min' 	=> 0,
			'max' 	=> 30,
			'step' 	=> 1,
			'display_text' => 'px',
			'help'	=> __('Space between the close button border and text or image in pixel', 'wp-ad-guru' ),
		),
		"design_close_border_enable" => array(
			'type' 	=> 'checkbox',
			'id' 	=> 'design_close_border_enable',
			'label' => __('Show border', 'wp-ad-guru' ),
			'default'	=> "0",
			'help'	=> __('Check this if you want to show a border around the close button' , 'wp-ad-guru' )
		),
		"design_close_border_group" => array(
			'type' 	=> 'group',
			'group_type' => 'row',
			'id' 	=> 'design_close_border_group',
			'fieldset' => array('legend'=>__( 'Border', 'wp-ad-guru' ) ),
			'fields'=> array(
				"design_close_border_width" => array(
					'type' 	=> 'slider',
					'id' 	=> 'design_close_border_width',
					'label' => __('Width', 'wp-ad-guru' ),
					'default'	=> 5,
					'min' 	=> 0,
					'max' 	=> 30,
					'step' 	=> 1,
					'display_text' => 'px',
					'help'	=> __('Thickness of border in pixel', 'wp-ad-guru' ),
				),
				"design_close_border_style" => array(
					'type' 	=> 'select',
					'id' 	=> 'design_close_border_style',
					'label' => 'Style',
					'default'	=> "solid",
					'help'	=> __('Style of border', 'wp-ad-guru' ),
					'options' => array(
						'solid'		=> 'solid',
						'dotted'	=> 'dotted',
						'dashed'	=> 'dashed',
						'double'	=> 'double',
						'groove'	=> 'groove',
						'ridge'		=> 'ridge',
						'inset'		=> 'inset',
						'outset'	=> 'outset',
						'initial'	=> 'initial',
						'inherit'	=> 'inherit',
						'hidden'	=> 'hidden',
						'none'		=> 'none'
					)
				),
				"design_close_border_color" => array(
					'type' 	=> 'color',
					'id' 	=> 'design_close_border_color',
					'label' => __('Color', 'wp-ad-guru' ),
					'default'	=> "#dddddd",
					'help'	=> __('Color of border', 'wp-ad-guru' ),
				),
				"design_close_border_radius" => array(
					'type' 	=> 'slider',
					'id' 	=> 'design_close_border_radius',
					'label' => __('Radius', 'wp-ad-guru' ),
					'default'	=> 0,
					'min' 	=> 0,
					'max' 	=> 50,
					'step' 	=> 1,
					'display_text' => 'px',
					'help'	=> __('Radius of border in pixel', 'wp-ad-guru' ),
				),
				
			)// end of group fields
		),//end of design_close_border_group
		"design_close_button_type" => array(
			'type' 	=> 'radio',
			'id' 	=> 'design_close_button_type',
			'label' => __('Button type', 'wp-ad-guru' ),
			'default'	=> 'image',
			'options' => array('text'=>__("Text", 'wp-ad-guru' ), 'image'=>__("Image", 'wp-ad-guru' )),
			'help'	=> __('Select the type of button. You can use either text or image for the close button', 'wp-ad-guru' ),
		),
		"design_close_button_type_text_group" => array(
			'type' 	=> 'group',
			'group_type' => 'row',
			'id' 	=> 'design_close_button_type_text_group',
			'fields'=> array(
				"design_close_text" => array(
					'type' 	=> 'text',
					'id' 	=> 'design_close_text',
					'label' => __('Button text', 'wp-ad-guru' ),
					'default'	=> 'X',
					'size'  => 'small',
					'help'	=> __('Text to show in close button', 'wp-ad-guru' ),
				),
				"design_close_font_group" => array(
					'type' 	=> 'group',
					'group_type' => 'row',
					'id' 	=> 'design_close_font_group',
					'fieldset' => array('legend'=>__( 'Font', 'wp-ad-guru' ) ),
					'fields'=> array(
						"design_close_color" => array(
							'type' 	=> 'color',
							'id' 	=> 'design_close_color',
							'label' => __('Color', 'wp-ad-guru' ),
							'default'	=> "#ffffff",
						),
						"design_close_font_size" => array(
							'type' 	=> 'slider',
							'id' 	=> 'design_close_font_size',
							'label' => __('Size', 'wp-ad-guru' ),
							'default'	=> 18,
							'min' 	=> 0,
							'max' 	=> 100,
							'step' 	=> 1,
							'display_text' => 'px'
						),
						"design_close_line_height" => array(
							'type' 	=> 'slider',
							'id' 	=> 'design_close_line_height',
							'label' => __('Line height', 'wp-ad-guru' ),
							'default'	=> 18,
							'min' 	=> 0,
							'max' 	=> 100,
							'step' 	=> 1,
							'display_text' => 'px'
						),
						"design_close_font_family" => array(
							'type' 	=> 'select',
							'id' 	=> 'design_close_font_family',
							'label' => __('Family', 'wp-ad-guru' ),
							'default'	=> 'Arial',
							'options' => array_merge( array('use_from_theme'=>'Use From Theme'), ADGURU_Helper::get_common_font_list() ),
							'help'	=> __("If you don't want to use any font from this list then select 'Use From Theme', font-family will be inherited from the active WordPress theme" , 'wp-ad-guru' )
						),
						"design_close_font_weight" => array(
							'type' 	=> 'select',
							'id' 	=> 'design_close_font_weight',
							'label' => __('Weight', 'wp-ad-guru' ),
							'default'	=> 'normal',
							'options' => array(
								'normal' => "normal",
								'bold' => "bold",
								'bolder' => "bolder",
								'lighter' => "lighter",
								'100' => "100",
								'200' => "200",
								'300' => "300",
								'400' => "400",
								'500' => "500",
								'600' => "600",
								'700' => "700",
								'800' => "800",
								'900' => "900",
								)
						),
						"design_close_font_style" => array(
							'type' 	=> 'select',
							'id' 	=> 'design_close_font_style',
							'label' => __('Style', 'wp-ad-guru' ),
							'default'	=> 'normal',
							'options' => array(
								'normal' => "normal",
								'italic' => "italic",
								'oblique' => "oblique"
								)
						),
					),
				),//end of design_close_font_group
				"design_close_text_shadow_enable" => array(
					'type' 	=> 'checkbox',
					'id' 	=> 'design_close_text_shadow_enable',
					'label' => __('Use text shadow', 'wp-ad-guru' ),
					'default'	=> "1",
					'help'	=> __('Check this if you want to show text shadow for close button' , 'wp-ad-guru' )
				),
				"design_close_text_shadow_group" => array(
					'type' 	=> 'group',
					'group_type' => 'row',
					'id' 	=> 'design_close_text_shadow_group',
					'fieldset' => array('legend'=>__( 'Text shadow', 'wp-ad-guru' ) ),
					'fields'=> array(
						"design_close_text_shadow_h_offset" => array(
							'type' 	=> 'slider',
							'id' 	=> 'design_close_text_shadow_h_offset',
							'label' => __('Horizontal position', 'wp-ad-guru' ),
							'default'	=> 1,
							'min' 	=> 0,
							'max' 	=> 100,
							'step' 	=> 1,
							'display_text' => 'px'
						),
						"design_close_text_shadow_v_offset" => array(
							'type' 	=> 'slider',
							'id' 	=> 'design_close_text_shadow_v_offset',
							'label' => __('Vertical position', 'wp-ad-guru' ),
							'default'	=> 1,
							'min' 	=> 0,
							'max' 	=> 100,
							'step' 	=> 1,
							'display_text' => 'px'
						),
						"design_close_text_shadow_blur_radius" => array(
							'type' 	=> 'slider',
							'id' 	=> 'design_close_text_shadow_blur_radius',
							'label' => __('Blur radius', 'wp-ad-guru' ),
							'default'	=> 1,
							'min' 	=> 0,
							'max' 	=> 100,
							'step' 	=> 1,
							'display_text' => 'px'
						),
						"design_close_text_shadow_color" => array(
							'type' 	=> 'color',
							'id' 	=> 'design_close_text_shadow_color',
							'label' => __('Color', 'wp-ad-guru' ),
							'default'	=> "#444444",
						),
					)
				),//end design_close_text_shadow_group				
			),
		),//end of design_close_button_type_text_group

		"design_close_button_type_image_group" => array(
			'type' 	=> 'group',
			'group_type' => 'row',
			'id' 	=> 'design_close_button_type_image_group',
			'fields'=> array(
				"design_close_image_source_type" => array(
					'type' 	=> 'radio',
					'id' 	=> 'design_close_image_source_type',
					'label' => __('Button image source', 'wp-ad-guru' ),
					'default'	=> 'builtin',
					'options' => array('builtin'=>'Select from list', 'custom'=>'Custom'),
					'disabled' => array('custom'), //will enable this when advanced designer extension is ready
				),
				"design_close_image_list_heading" => array(
					'type' 	=> 'html',
					'id' 	=> 'design_close_image_list_heading',
					'label' => __('Built-in close button image list', 'wp-ad-guru' ),
					'single_column' => true,
				),
				
				"design_close_image_name" => array(
					'type' 	=> 'radio_image',
					'id' 	=> 'design_close_image_name',
					//'label' => __('Button image list', 'wp-ad-guru' ),
					'default'	=> 'core_close_default_png',
					'options' => ADGURU_Helper::get_close_icon_list('png'),
					'single_column' => true,
				),

				"design_close_custom_image_url" => array(
					'type' 	=> 'image',
					'id' 	=> 'design_close_custom_image_url',
					'label' => __('Custom Image', 'wp-ad-guru' )
				),

			),
		),//end of design_close_button_type_image_group
		"design_close_background_enable" => array(
			'type' 	=> 'checkbox',
			'id' 	=> 'design_close_background_enable',
			'label' => __('Use Background', 'wp-ad-guru' ),
			'default'	=> "0",
			'help'	=> __('Check this if you want to use background of the popup close button' , 'wp-ad-guru' )
		),
		"design_close_background_group" => array(
			'type' 	=> 'group',
			'group_type' => 'row',
			'id' 	=> 'design_close_background_group',
			'fieldset' => array('legend'=>__( 'Background', 'wp-ad-guru' ) ),
			'fields'=> array(
				"design_close_background_color" => array(
					'type' 	=> 'color',
					'id' 	=> 'design_close_background_color',
					'label' => __('Color', 'wp-ad-guru' ),
					'default'	=> "#ffffff",
				),
				"design_close_background_opacity" => array(
					'type' 	=> 'slider',
					'id' 	=> 'design_close_background_opacity',
					'label' => __('Opacity', 'wp-ad-guru' ),
					'default'	=> 100,
					'min' 	=> 0,
					'max' 	=> 100,
					'step' 	=> 1,
					'display_text' => '%',
				),
			)
		),//end of design_close_background_group
		
		"design_close_box_shadow_enable" => array(
			'type' 	=> 'checkbox',
			'id' 	=> 'design_close_box_shadow_enable',
			'label' => __('Show Shadow', 'wp-ad-guru' ),
			'default'	=> "0",
			'help'	=> __('Check this if you want to show box shadow of the close button' , 'wp-ad-guru' )
		),
		"design_close_box_shadow_group" => array(
			'type' 	=> 'group',
			'group_type' => 'row',
			'id' 	=> 'design_close_box_shadow_group',
			'fieldset' => array('legend'=>__( 'Drop shadow', 'wp-ad-guru' ) ),
			'fields'=> array(
				"design_close_box_shadow_h_offset" => array(
					'type' 	=> 'slider',
					'id' 	=> 'design_close_box_shadow_h_offset',
					'label' => __('Horizontal position', 'wp-ad-guru' ),
					'default'	=> 1,
					'min' 	=> 0,
					'max' 	=> 100,
					'step' 	=> 1,
					'display_text' => 'px'
				),
				"design_close_box_shadow_v_offset" => array(
					'type' 	=> 'slider',
					'id' 	=> 'design_close_box_shadow_v_offset',
					'label' => __('Vertical position', 'wp-ad-guru' ),
					'default'	=> 1,
					'min' 	=> 0,
					'max' 	=> 100,
					'step' 	=> 1,
					'display_text' => 'px'
				),
				"design_close_box_shadow_blur_radius" => array(
					'type' 	=> 'slider',
					'id' 	=> 'design_close_box_shadow_blur_radius',
					'label' => __('Blur radius', 'wp-ad-guru' ),
					'default'	=> 3,
					'min' 	=> 0,
					'max' 	=> 100,
					'step' 	=> 1,
					'display_text' => 'px'
				),
				"design_close_box_shadow_spread" => array(
					'type' 	=> 'slider',
					'id' 	=> 'design_close_box_shadow_spread',
					'label' => __('Spread', 'wp-ad-guru' ),
					'default'	=> 0,
					'min' 	=> 0,
					'max' 	=> 100,
					'step' 	=> 1,
					'display_text' => 'px'
				),
				"design_close_box_shadow_color" => array(
					'type' 	=> 'color',
					'id' 	=> 'design_close_box_shadow_color',
					'label' => __('Color', 'wp-ad-guru' ),
					'default'	=> "#000000"
				),
				"design_close_box_shadow_opacity" => array(
					'type' 	=> 'slider',
					'id' 	=> 'design_close_box_shadow_opacity',
					'label' => __('Opacity', 'wp-ad-guru' ),
					'default'	=> 25,
					'min' 	=> 0,
					'max' 	=> 100,
					'step' 	=> 1,
					'display_text' => '%'
				),
				"design_close_box_shadow_inset" => array(
					'type' 	=> 'select',
					'id' 	=> 'design_close_box_shadow_inset',
					'label' => __('Inset', 'wp-ad-guru' ),
					'default'	=> 'no',
					'options' => array('no'=>'No', 'yes'=>'Yes'),
					'help'	=> __('Set the box shadow to inset (inner shadow)', 'wp-ad-guru' ),
				),
			)
		),//end of design_close_box_shadow_group
		"design_close_location_group" => array(
			'type' => 'group',
			'group_type' => 'row',
			'id' => 'design_close_location_group',
			'fieldset' => array('legend' => __('Location of close button', 'wp-ad-guru') ),
			'fields' => array(
				"design_close_location" => array(
					'type' 	=> 'select',
					'id' 	=> 'design_close_location',
					'label' => __('Location', 'wp-ad-guru' ),
					'default'	=> 'top_left',
					'options' => array(
						'top_left' 		=> 'Top Left',
						'top_center' 	=> 'Top Center',
						'top_right' 	=> 'Top Right',
						'middle_left' 	=> 'Middle Left',
						'middle_right' 	=> 'Middle Right',
						'top_right' 	=> 'Top Right',
						'bottom_left' 	=> 'Bottom Left',
						'bottom_center' => 'Bottom Center',
						'bottom_right' 	=> 'Bottom Right'
					),
					'help' => __('Location of close button', 'wp-ad-guru' ),
				),
				"design_close_top" => array(
					'type' 	=> 'slider',
					'id' 	=> 'design_close_top',
					'label' => __('Top', 'wp-ad-guru' ),
					'default'	=> 0,
					'min' 	=> -100,
					'max' 	=> 100,
					'step' 	=> 1,
					'display_text' => 'px',
					'hidden' => true,
				),
				"design_close_left" => array(
					'type' 	=> 'slider',
					'id' 	=> 'design_close_left',
					'label' => __('Left', 'wp-ad-guru' ),
					'default'	=> 0,
					'min' 	=> -100,
					'max' 	=> 100,
					'step' 	=> 1,
					'display_text' => 'px',
					'hidden' => true,
				),
				"design_close_right" => array(
					'type' 	=> 'slider',
					'id' 	=> 'design_close_right',
					'label' => __('Right', 'wp-ad-guru' ),
					'default'	=> 0,
					'min' 	=> -100,
					'max' 	=> 100,
					'step' 	=> 1,
					'display_text' => 'px',
					'hidden' => true,
				),
				"design_close_bottom" => array(
					'type' 	=> 'slider',
					'id' 	=> 'design_close_bottom',
					'label' => __('Bottom', 'wp-ad-guru' ),
					'default'	=> 0,
					'min' 	=> -100,
					'max' 	=> 100,
					'step' 	=> 1,
					'display_text' => 'px',
					'hidden' => true,
				),

			)
		),//end of design_close_location_group
		"design_close_custom_css_class" => array(
			'type' 	=> 'text',
			'id' 	=> 'design_close_custom_css_class',
			'label' => __('Custom CSS class', 'wp-ad-guru' ),
			'default'	=> '',
			'size'  => 'medium',
			'help'	=> __('Add custom CSS class name to the close button element. For multiple class names use space between two classes', 'wp-ad-guru' ),
		),
		//----------END CLOSE BUTTON ------------------------------------------------------
		//----------START OVERLAY  --------------------------------------------------------
		
		"df_header_overlay" => array(
			'type' 	=> 'header',
			'id' 	=> 'df_header_overlay',
			'value'	=> __("OVERLAY", 'wp-ad-guru' ),
			'align' => 'left',
			'tag'	=>'h3',
			'single_column' => true
		),
		"design_overlay_background_color" => array(
			'type' 	=> 'color',
			'id' 	=> 'design_overlay_background_color',
			'label' => __('Color', 'wp-ad-guru' ),
			'default'	=> "#000000",
			'help'	=> __('Background color of overlay', 'wp-ad-guru' ),
		),
		"design_overlay_background_opacity" => array(
			'type' 	=> 'slider',
			'id' 	=> 'design_overlay_background_opacity',
			'label' => __('Opacity', 'wp-ad-guru' ),
			'default'	=> 75,
			'min' 	=> 0,
			'max' 	=> 100,
			'step' 	=> 1,
			'display_text' => '%',
			'help'	=> __('Opacity of cotainer background in %', 'wp-ad-guru' ),
		),
		//----------START OVERLAY  ----------------------------------------------------------

	)//end of fields array 
); // end array $design_form_args

function adguru_modal_popup_form_design_header_callback( $form_obj )
{
	do_action('adguru_editor_form_modal_popup_design_top', $form_obj );
}

function adguru_modal_popup_form_design_footer_callback( $form_obj )
{
	?>
	<script type="text/javascript">
		jQuery(document).on('wpafb-field:change:design_container_border_enable', function(event , args){
		
			var value = args['value'];
			if( value == 1 )
			{
				WPAFB.showFieldGroup('design_container_border_group');
			}
			else
			{
				WPAFB.hideFieldGroup('design_container_border_group');
			}
		});

		jQuery(document).on('wpafb-field:change:design_container_background_enable', function(event , args){
		
			var value = args['value'];
			if( value == 1 )
			{
				WPAFB.showFieldGroup('design_container_background_group');
			}
			else
			{
				WPAFB.hideFieldGroup('design_container_background_group');
			}
		});

		jQuery(document).on('wpafb-field:change:design_container_box_shadow_enable', function(event , args){
		
			var value = args['value'];
			if( value == 1 )
			{
				WPAFB.showFieldGroup('design_container_box_shadow_group');
			}
			else
			{
				WPAFB.hideFieldGroup('design_container_box_shadow_group');
			}
		});

		jQuery(document).on('wpafb-field:change:design_close_button_type', function(event , args){
		
			var value = args['value'];
			if( value == 'text' )
			{
				WPAFB.showFieldGroup('design_close_button_type_text_group');
				WPAFB.hideFieldGroup('design_close_button_type_image_group');
			}
			else
			{
				WPAFB.hideFieldGroup('design_close_button_type_text_group');
				WPAFB.showFieldGroup('design_close_button_type_image_group');
			}
		});

		jQuery(document).on('wpafb-field:change:design_close_image_source_type', function(event , args){
		
			var value = args['value'];
			if( value == 'builtin' )
			{
				WPAFB.showField('design_close_image_list_heading');
				WPAFB.showField('design_close_image_name');
				WPAFB.hideField('design_close_custom_image_url');
			}
			else
			{
				WPAFB.hideField('design_close_image_list_heading');
				WPAFB.hideField('design_close_image_name');
				WPAFB.showField('design_close_custom_image_url');
			}
		});

		jQuery(document).on('wpafb-field:change:design_close_background_enable', function(event , args){
		
			var value = args['value'];
			if( value == 1 )
			{
				WPAFB.showFieldGroup('design_close_background_group');
			}
			else
			{
				WPAFB.hideFieldGroup('design_close_background_group');
			}
		});

		jQuery(document).on('wpafb-field:change:design_close_text_shadow_enable', function(event , args){
		
			var value = args['value'];
			if( value == 1 )
			{
				WPAFB.showFieldGroup('design_close_text_shadow_group');
			}
			else
			{
				WPAFB.hideFieldGroup('design_close_text_shadow_group');
			}
		});

		jQuery(document).on('wpafb-field:change:design_close_border_enable', function(event , args){
		
			var value = args['value'];
			if( value == 1 )
			{
				WPAFB.showFieldGroup('design_close_border_group');
			}
			else
			{
				WPAFB.hideFieldGroup('design_close_border_group');
			}
		});

		jQuery(document).on('wpafb-field:change:design_close_box_shadow_enable', function(event , args){
		
			var value = args['value'];
			if( value == 1 )
			{
				WPAFB.showFieldGroup('design_close_box_shadow_group');
			}
			else
			{
				WPAFB.hideFieldGroup('design_close_box_shadow_group');
			}
		});

		jQuery(document).on('wpafb-field:change:design_close_location', function(event , args){
		
			var value = args['value'];
			WPAFB.hideField('design_close_top');
			WPAFB.hideField('design_close_left');
			WPAFB.hideField('design_close_right');
			WPAFB.hideField('design_close_bottom');

			if( value == 'top_left')
			{
				WPAFB.showField('design_close_top');
				WPAFB.showField('design_close_left');
			}
			else if( value == 'top_center')
			{
				WPAFB.showField('design_close_top'); 
			}
			else if( value == 'top_right')
			{
				WPAFB.showField('design_close_top');
				WPAFB.showField('design_close_right');
			}
			else if( value == 'middle_left')
			{
				WPAFB.showField('design_close_left');
			}
			else if( value == 'middle_right')
			{
				WPAFB.showField('design_close_right');
			}
			else if( value == 'bottom_left')
			{
				WPAFB.showField('design_close_left');
				WPAFB.showField('design_close_bottom'); 
			}
			else if( value == 'bottom_center')
			{
				WPAFB.showField('design_close_bottom'); 
			}
			else if( value == 'bottom_right')
			{
				WPAFB.showField('design_close_bottom');
				WPAFB.showField('design_close_right');
			}

		});



	</script>
	<?php 
	do_action('adguru_editor_form_modal_popup_design_bottom', $form_obj );
}

//Apply filters so that extensions can add/modify fields settings
$design_form_args = apply_filters('adguru_editor_form_modal_popup_design_args', $design_form_args );
//reassign header and footer callback to prevent modification the value of  header and  footer callback key.
$design_form_args['header_callback'] = 'adguru_modal_popup_form_design_header_callback';
$design_form_args['footer_callback'] = 'adguru_modal_popup_form_design_footer_callback';
//Create the form object
$design_form = adguru()->form_builder->create_form($design_form_args);


function adguru_show_modal_popup_design_form( $ad )
{

	$design_form = adguru()->form_builder->get_form('mp_design_form');
	if( $design_form )
	{ 
		$design_data = array();
		if(! isset($ad->design) || !is_array($ad->design) )
		{
			$ad->design = array();
		}
		else
		{
			foreach( $ad->design as $key => $value )
			{
				$id = 'design_'.$key;
				$design_data[$id] = $value;
			}
			
		}
		$design_form->set_data( $design_data );
		//Before render modify the fields settings, specially update fields hidden status based on the value.
		
		if( $design_form->get_value('design_container_border_enable') == '0')
		{
			$design_form->set_hidden_field('design_container_border_group');
		}

		if( $design_form->get_value('design_container_background_enable') == '0')
		{
			$design_form->set_hidden_field('design_container_background_group');
		}

		if( $design_form->get_value('design_container_box_shadow_enable') == '0')
		{
			$design_form->set_hidden_field('design_container_box_shadow_group');
		}

		if( $design_form->get_value('design_close_button_type') == 'text')
		{
			$design_form->set_hidden_field('design_close_button_type_image_group');
		}
		else
		{
			$design_form->set_hidden_field('design_close_button_type_text_group');
		}

		
		if( $design_form->get_value('design_close_image_source_type') == 'builtin')
		{
			$design_form->set_hidden_field('design_close_custom_image_url');
		}
		else
		{
			$design_form->set_hidden_field('design_close_image_list_heading');
			$design_form->set_hidden_field('design_close_image_name');
		}

		if( $design_form->get_value('design_close_background_enable') == '0')
		{
			$design_form->set_hidden_field('design_close_background_group');
		}

		if( $design_form->get_value('design_close_text_shadow_enable') == '0')
		{
			$design_form->set_hidden_field('design_close_text_shadow_group');
		}

		if( $design_form->get_value('design_close_border_enable') == '0')
		{
			$design_form->set_hidden_field('design_close_border_group');
		}

		if( $design_form->get_value('design_close_box_shadow_enable') == '0')
		{
			$design_form->set_hidden_field('design_close_box_shadow_group');
		}

		$design_close_location= $design_form->get_value('design_close_location');
		if( $design_close_location == 'top_left')
		{
			$design_form->set_hidden_field('design_close_top', false ); // false to show the field
			$design_form->set_hidden_field('design_close_left', false );
		}
		elseif( $design_close_location == 'top_center')
		{
			$design_form->set_hidden_field('design_close_top', false ); 
		}
		elseif( $design_close_location == 'top_right')
		{
			$design_form->set_hidden_field('design_close_top', false ); 
			$design_form->set_hidden_field('design_close_right', false );
		}
		elseif( $design_close_location == 'middle_left')
		{
			$design_form->set_hidden_field('design_close_left', false ); 
		}
		elseif( $design_close_location == 'middle_right')
		{
			$design_form->set_hidden_field('design_close_right', false ); 
		}
		elseif( $design_close_location == 'bottom_left')
		{
			$design_form->set_hidden_field('design_close_left', false );
			$design_form->set_hidden_field('design_close_bottom', false ); 
		}
		elseif( $design_close_location == 'bottom_center')
		{
			$design_form->set_hidden_field('design_close_bottom', false ); 
		}
		elseif( $design_close_location == 'bottom_right')
		{
			$design_form->set_hidden_field('design_close_bottom', false ); 
			$design_form->set_hidden_field('design_close_right', false );
		}

		do_action('adguru_editor_form_modal_popup_design_before_render', $design_form );
		//render the form
		$design_form->render();
		
	}//end if $design_form
}



