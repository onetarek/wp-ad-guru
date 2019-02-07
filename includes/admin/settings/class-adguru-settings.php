<?php
/**
 * Settings API class for Ad Guru
 * @author oneTarek
 * @since 2.0.0
 */

// Don't allow direct access
if ( ! defined( 'ABSPATH' ) ) exit;

require_once dirname( __FILE__ ) . '/class-adguru-settings-api.php';

if( ! class_exists( 'ADGURU_Settings' ) ) :

class ADGURU_Settings{

	public $tabs = array();
	public $sections = array();
	public $fields = array();
	
	/* 
	 * ADGURU_Settings_API object 
	 */
	public $settings_api;
	
	public function __construct(){
		$this->settings_api = new ADGURU_Settings_API( array( 
				'settings_name' => ADGURU_OPTIONS_FIELD_NAME, 
				'prefix' => 'adguru_settings' 
				));
		add_action( 'admin_init', array( $this, 'admin_init') );
	}

	public function admin_init() {
	
		//Add Tabs
		$this->add_tab( 'general' , __( 'General', 'adguru' ) );
		//$this->add_tab( 'emails' , __( 'Emails', 'adguru' ) );
		
		//Add sections to tabs
		$this->add_section( 'general' , 'main',  __( 'General Settings', 'adguru' ) );
		//$this->add_section( 'emails' , 'main',  __( 'General Emails', 'adguru' ) );
		
		#======Add fields here =============
		$this->add_field("general", 'main', array(
			'type'	=>	'checkbox',
			'id'	=>	'enable_geo_location',
			'name'	=> __('Geo Location', 'adguru'),
			'label'	=> __('Enable', 'adguru'),
			'desc'	=> __("Geo location feature to serve ads based on visitor's country.<br>This feature does not work perfectly with caching enabled", 'adguru'),
			'default' => 'off'
		));
		
		//$this->example_settings_fields();
		
		//Now let other plugins or extensions to add Tabs, Sections and Fields
		do_action("adguru_settings_add", $this );
		
		//Now let other plugins or extensions to modify Tabs, Sections and Fields

		#Filter tabs
		$this->tabs = apply_filters( "adguru_settings_tabs", $this->tabs );
		
		#Filter sections
		foreach( $this->tabs as $tab => $text )
		{
			$sections = isset( $this->sections[ $tab ] ) ? $this->sections[ $tab ] : array( 'main' =>__( 'General Settings', 'adguru' ) );
			$sections = apply_filters( "adguru_settings_sections_{$tab}", $sections );
			$this->sections[ $tab ] = $sections;
		}
		$this->sections = apply_filters( "adguru_settings_sections", $this->sections );
		
		#Filter fields
		foreach( $this->sections as $tab => $sections )
		{
			foreach( $sections as $sec => $text)
			{
				$fields = ( ! empty( $this->fields[ $tab ][ $sec ] ) ) ? $this->fields[ $tab ][ $sec ] : array(); 
				$fields = apply_filters( "adguru_settings_fields_{$tab}_{$sec}", $fields );
				if( ! empty( $fields ) ){ $this->fields[ $tab ][ $sec ] = $fields; }
			}
		
		}
		$this->fields = apply_filters( "adguru_settings_fields", $this->fields );
	
		//set the settings
		$this->settings_api->set_tabs( $this->tabs );
		$this->settings_api->set_sections( $this->sections );
		$this->settings_api->set_fields( $this->fields );
	
		//initialize settings
		$this->settings_api->admin_init();
		
	}//end func


	public function add_tab( $slug , $text ){
		if( !isset( $this->tabs[ $slug ] ) )
		{
			$this->tabs[ $slug ] = $text;
		}
		
	}

    public function add_section( $tab , $slug, $text ){
        if( !isset( $this->sections[$tab] ) ){ $this->sections[$tab] = array();}
        if( !isset( $this->sections[$tab][$slug] ) )
        {
        	$this->sections[$tab][$slug] = $text;
        }
        
    }	  

    public function add_field( $tab, $section, $field ){
		$id = $field['id'];
        $this->fields[ $tab ][ $section ][ $id ] = $field;
    }


    /**
     * Example of all possible type of settings fields
     *
     */
    public function example_settings_fields() {
		$this->add_tab( 'example' , __( 'Example of Settings Fields', 'adguru' ) );
		$this->add_section( 'example' , 'main',  __( 'Main Section', 'adguru' ) );
		$this->add_section( 'example' , 'secondary',  __( 'Secondary Section', 'adguru' ) );


		$this->add_field('general' , 'main', array(
			'type' => 'header',
			'id'   => 'header_example_1',
			'name' => '',
			'desc' => '<h3>Just a Heading</h3>',
					
		));

		$this->add_field('example' , 'main', array(
			'type'  => 'text',
			'id'    => 'example_text_small',
			'name'  => __( 'Small Text Input', 'adguru' ),
            'desc'  => __( 'Text input description', 'adguru' ),
            'default' => '',
            'size'  => 'small'
		));

		$this->add_field('example' , 'main', array(
			'type'  => 'text',
			'id'    => 'example_text_medium',
			'name'  => __( 'Medium Text Input', 'adguru' ),
            'desc'  => __( 'Text input description', 'adguru' ),
            'default' => '',
            'placeholder' => 'I am a placeholder',
            'size'  => 'medium'
		));

		$this->add_field('example' , 'main', array(
			'type'  => 'text',
			'id'    => 'example_text_large',
			'name'  => __( 'Large Text Input', 'adguru' ),
            'desc'  => __( 'Text input description', 'adguru' ),
            'default' => '',
            'placeholder' => 'I am a placeholder',
            'size'  => 'large'
		));

		$this->add_field('example' , 'main', array(
			'type' => 'number',
			'id'   => 'example_number_1',
			'name' => 'Number 1',
            'label'=> __( 'Number Input', 'adguru' ),
            'desc' => __( 'Number input description', 'adguru' ),
            'default' => '',
            'placeholder' => 'I am a placeholder'
		));

		$this->add_field('example' , 'main', array(
			'type' => 'number',
			'id'   => 'example_number_2',
			'name' => 'Number 2',
            'label'=> __( 'Number Input', 'adguru' ),
            'desc' => __( 'Number input with max min and step', 'adguru' ),
            'max'  =>50,
            'min'  =>20,
            'step' =>5,
            'default' => '',
            'placeholder' => 'I am a placeholder'
		));

		$this->add_field('example' , 'main', array(
			'type'    => 'password',
			'id'      => 'example_password_1',
            'name'   => __( 'Password', 'adguru' ),
            'desc'    => __( 'Password description', 'adguru' ),
            'default' => '',
            'placeholder' => 'Enter password'
		));

		$this->add_field('example' , 'main', array(
			'type'  => 'textarea',
			'id'    => 'example_textarea_1',
			'name' => __( 'Textarea Input', 'adguru' ),
			'desc'  => __( 'Textarea description', 'adguru' ),
			'placeholder' => 'I am a placeholder'		
		));

		$this->add_field('example' , 'main', array(
			'type'  => 'checkbox',
			'id'    => 'example_checkbox_1',
			'name'  => __( 'Checkbox', 'adguru' ),
            'label' => __( 'Checkbox Label', 'adguru' ),
            'desc'  => __( 'Checkbox descrition', 'adguru' ),		
		));

		$this->add_field('example' , 'main', array(
			'type' => 'radio',
			'id'   => 'example_radio_1',
            'name' => __( 'Radio Button', 'adguru' ),
            'desc' => __( 'A radio button', 'adguru' ),
            'options' => array(
                'yes' => 'Yes',
                'no' => 'No'
            )	
		));

		$this->add_field('example' , 'main', array(
			'type'    => 'multicheck',
			'id'      => 'example_multicheck_1',
            'name'   => __( 'Multile checkbox', 'adguru' ),
            'desc'    => __( 'Multi checkbox description', 'adguru' ),
            'options' => array(
                  'one'   => 'One',
                  'two'   => 'Two',
                  'three' => 'Three',
                  'four'  => 'Four'
            )		
		));

		$this->add_field('example' , 'main', array(
			'type'    => 'select',
			'id'      => 'example_selectbox_1',
            'name'   => __( 'A Dropdown selectbox', 'adguru' ),
            'desc'    => __( 'Dropdown description', 'adguru' ),
            'default' => 'no',
            'options' => array(
                 'yes' => 'Yes',
                 'no'  => 'No'
            )	
		));


		$this->add_field('example' , 'main', array(
			'type'    => 'file',
			'id'      => 'example_file_1',
            'name'   => __( 'File', 'adguru' ),
            'desc'    => __( 'File description', 'adguru' ),
            'default' => '',
            'placeholder' => 'Browse a file',
		));

		$this->add_field('example' , 'main', array(
			'type' 	  => 'editor',
			'id'   	  => 'example_wysiwyg_1',
            'name'   => __( 'WYSIWYG Editor', 'adguru' ),
            'desc' 	  => __( 'wysiwyg editor example', 'adguru' ),
            'default' => ''	
		));

		$this->add_field('example' , 'main', array(
			'type'    => 'color',
			'id'      => 'example_color_1',
            'name'   => __( 'Color', 'adguru' ),
            'desc'    => __( 'Color description', 'adguru' ),
            'default' => ''	
		));

		$this->add_field('example' , 'secondary', array(
			'type'  => 'text',
			'id'    => 'example_text_2',
			'name'  => __( 'Text Input', 'adguru' ),
            'desc'  => __( 'Text input description', 'adguru' ),
            'default' => 'Title 2',
            'placeholder' => 'I am a placeholder'
		));

		$this->add_field('example' , 'secondary', array(
			'type'    => 'color',
			'id'      => 'example_color_2',
            'name'   => __( 'Color', 'adguru' ),
            'desc'    => __( 'Color description', 'adguru' ),
            'default' => '#CFEE00'	
		));



    }//end function example...

	
	public function display(){
       	
   		echo '<div class="wrap">';
   			echo '<h2>AD GURU Settings</h2>';
   			$this->settings_api->display();
   		echo '</div>';
       	
	}//end func
	

}//end class

endif;