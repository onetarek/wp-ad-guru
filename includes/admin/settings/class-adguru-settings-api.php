<?php
/**
 * Adguru Settings API wrapper class
 * Forked from : https://github.com/tareq1988/wordpress-settings-api-class
 */

// Don't allow direct access
if ( ! defined( 'ABSPATH' ) ) exit;
 
if ( !class_exists( 'ADGURU_Settings_API' ) ):
class ADGURU_Settings_API {

	/**
	 * Key name in wp options table
	 **/
	 protected $settings_name = "";
	
	/**
	 * Keep all settings fields value
	 **/
	 protected $options;
	
	/**
	 * Prefix string to use in many places
	 **/
	 protected $prefix = "";
	
    /** settings tabs array
     *
     * @var array
     */
    protected $tabs = array();

	/**
     * settings sections array
	 * section in each tab. 
     * Note: this 'section' does not mean the word 'section' in WP settings API
     * @var array
	 * @example array( 
	 		'tab1' => array( 
				'text'=>"Tab 1 text',
				'sections'=>array(
					"section1"=>"Section1 Text",
					"section2"=>"Section2 Text"
					)
				)
			'tab2' =>array(......)
		)
     */
    protected $sections = array();

    /**
     * Settings fields array
     *
     * @var array
	 * @example array(
	 		"tab1"=>array(
				"section1"=>array(
					"field1"=>array(..args....);
					"field2"=>array(..args....);
					),
				"section2"=>array(....)
				)
			"tab2"=>array(....)
	 	)
     */
    protected $fields = array();
	
	protected $current_tab = 'general';
	protected $current_section = 'main';

    private $duplicate_field_id = null;

    public function __construct( $args ) {
		
		if( !is_array( $args ) ){ throw new Exception( "Adguru Settings API Error : Constructor - \$args is not an anrray" ); return; }
		if( empty( $args['settings_name'] ) || empty( $args['prefix'] ) ) 
		{
			throw new Exception('Adguru Settings API Error : settings_name and prefix are required. Pass those in constructor as an associative array'); 
			return;
		}
		
		$this->settings_name = $args['settings_name'];
		$this->prefix = $args['prefix'];
		
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
    }

    /**
     * Enqueue scripts and styles
     */
    function admin_enqueue_scripts() {
        wp_enqueue_style( 'wp-color-picker' );

        wp_enqueue_media();
        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_script( 'jquery' );
    }


	/**
	 * Set tabs
	 */
	 public function set_tabs( $tabs ){
		$this->tabs = $tabs;		
	 }
	 
	 /**
	  * Add a single tab
	  */
	  public function add_tab( $slug , $text ){
		if( $slug !="" && $text !="")
        {
            $this->tabs[ $slug ] = $text;
        }
        
	  }
	
	/**
     * Set settings sections
     *
     * @param array   $sections setting sections array where tabs are keys
     */
    public function set_sections( $sections ) {
        $this->sections = $sections;
    }

	/**
     * Set settings sections for a tab
     * @param string $tab
     * @param array   $sections . Setting sections array for a single tab
     */
    public function set_sections_to_tab( $tab , $sections ) {
        if( $tab !="" && is_aray( $sections ) )
        {
            $this->sections[ $tab ] = $sections;
        }
        
    }
	
    /**
     * Add a single section to a specific tab
     * @param string $tab slug of tab
	 * @param string $slug slug of section
	 * @param string $text text of section
     */
    public function add_section( $tab , $slug, $text ) {
        if( $tab !="" && $slug !="" && $text !="")
        {
            $this->sections[$tab][$slug] = $text;
        }
        
    }

    /**
     * Set settings fields
     *
     * @param array   $fields settings fields array
     */
    function set_fields( $fields ) {
        $this->fields = $fields;
    }

    function add_field( $tab, $section, $field ) {
        $defaults = array(
            'id' => '',
			'name'  => '',
            'label' => '',
            'desc'  => '',
            'type'  => 'text'
        );

        $arg = wp_parse_args( $field, $defaults );
		$id = $arg['id'];
        if( $id !="")
        {
            $this->fields[ $tab ][ $section ][$id] = $arg;
        }
    }

    /**
     * check for duplicate field id. 
     * Same field id can be found in different section. We don't allow this.
     **/
    private function check_duplicate_field(){
        $arr = array();
        foreach( $this->fields as $tab => $sections )
        {
            foreach( $sections as $sec )
            {
                foreach( $sec as $id => $field )
                {
                    if( isset( $arr[$id] ) )
                    {
                        $this->duplicate_field_id = $id;
                        return $id;
                    }
                    else
                    {
                        $arr[$id] = 1;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Initialize and registers the settings sections and fileds to WordPress
     *
     * Usually this should be called at `admin_init` hook.
     *
     * This function gets the initiated settings sections and fields. Then
     * registers them to WordPress and ready for use.
     */
    public function admin_init() { 
        $this->check_duplicate_field();
        if( $this->duplicate_field_id ) 
        {
            return;
        }
        //register settings sections
		$prefix = $this->prefix;
		if ( false == get_option( $this->settings_name ) ) {
			add_option( $this->settings_name , array() );
		}
		
		foreach( $this->fields as $tab => $sections )
		{
			foreach ( $sections as $section => $fields ) 
			{
				add_settings_section(
					$prefix.'_' . $tab . '_' . $section,
					__return_null(),
					'__return_false',
					$prefix.'_' . $tab . '_' . $section
				);
				
				foreach ( $fields as $field )
				{
				
					if ( empty( $field['id'] ) || empty( $field['type'] ) ) 
					{
						continue;
					}
	
					$name = isset( $field['name'] ) ? $field['name'] : '';
					
					$callback = array( $this, 'main_callback' );

                    $defaults = array(
                        'section'     => $section,
                        'id'          => '',
                        'type'        => '',
                        'desc'        => '',
                        'name'        => '',
                        'label'       => '',
                        'size'        => '',
                        'options'     => '',
                        'default'     => '',
                        'min'         => '',
                        'max'         => '',
                        'step'        => '',
                        'placeholder' => '',
                        'readonly'    => false,
                        'faux'        => false
                    );
                    $args = wp_parse_args( $field, $defaults );
					add_settings_field(
						$prefix.'[' . $field['id'] . ']',
						$name,
						$callback,
						$prefix.'_' . $tab . '_' . $section,
						$prefix.'_' . $tab . '_' . $section,
						$args
					);
				}//end foreach $fields as $field			
				
			}//end foreach $sections
		
		}//end foreach $this->fields
		
		// Creates our settings in the options table
		#old technique ( still works )
        //register_setting( $this->settings_name, $this->settings_name, array( $this, 'settings_sanitize' ) );
        #new technique
        register_setting( 
            $this->settings_name, 
            $this->settings_name, 
            array( 
                'sanitize_callback' => array( $this, 'settings_sanitize' ) )
            );
        
    }//end func

    /**
     * Get field description for display
     *
     * @param array   $args settings field args
     */
    public function get_field_description( $args ) {
        if ( ! empty( $args['desc'] ) ) {
            $desc = sprintf( '<p class="description">%s</p>', $args['desc'] );
        } else {
            $desc = '';
        }

        return $desc;
    }

	

    /**
     * Sanitizes a string key Settings
     *
     * Keys are used as internal identifiers. Alphanumeric characters, dashes, underscores, stops, colons and slashes are allowed
     * @param  string $key String key
     * @return string Sanitized key
     */
    public function sanitize_key( $key ) {
    	$raw_key = $key;
    	$key = preg_replace( '/[^a-zA-Z0-9_\-\.\:\/]/', '', $key );
    	return $key;
    }

    /**
     * Get field attribute values
     * @param array $args 
     * @return array
     **/
    public function get_field_attrs( $args )
    {
        $attrs = array();
        $attrs['default'] = $default = isset( $args['default'] ) ? $args['default'] : '';
        $attrs['value'] = $this->get_option( $args['id'] , $default );
        $attrs['name'] = $this->prefix.'[' . esc_attr( $args['id'] ) . ']';
        $attrs['id'] = $this->prefix.'_'.$this->sanitize_key( $args['id'] );
        return $attrs;

    }

    /**
     * Main callback function to pass with add_settings_field function
     * When WP main core function calls this function then it decides the actuall callback function for the field.
     * and calls that callback function 
     * We need this wrapper function to pass current object of this class to the callback function declared in outside of this class.
     * So that custom callback functions can use public methods of this object.
     * Custom callback function receives two arguments. 
     *  1. $args ( field options )
     *  2. $this ( current object of this class )
     * @param array $args
     *
     *  Example of custom callback funciton 
     *  function my_field_func( $args , $setting_api )
     *  {
     *      $attrs = $settings_api->get_field_attrs( $args );
     *      //do something to render custom field output.
     *  }
     *
     *
     **/
    public function main_callback( $args )
    {
        $callback = false;
        if( !empty( $args['callback'] ) && is_callable( $args['callback'] ) )
        {
            $callback = $args['callback'] ;
        
        }
        elseif( method_exists( $this, 'callback_'.$args['type'] ) )
        {
            $callback = array( $this, 'callback_'.$args['type'] );
        }
        else
        {
            $callback = array( $this, 'missing_callback' );
        }

        call_user_func_array( $callback , array( $args, $this ) );  
    }


    /**
     * Show error message if a callback function is not found for a specific field type. 
     **/
    public function missing_callback( $args ){
        echo '<div style="color:#ff0000">Callabck function not found for field type <b>'.$args['type'].'</b></div>';
    }

	/**
	 * Header Callback
	 *
	 * Renders the header.
	 * @param array $args Arguments passed by the setting
	 * @return void
	 */
	public function callback_header( $args ) {
		echo $args['desc'];
	}

    /**
     * Text Callback
     *
     * Renders text fields.
     *
     * @param array $args Arguments passed by the setting
     * @return void
     */
    public function callback_text( $args ){

    	$attrs = $this->get_field_attrs( $args );
        $name = $attrs['name'];
    	if( isset( $args['faux'] ) && true === $args['faux'] )
        {
    		$args['readonly'] = true;
    		$value = isset( $args['default'] ) ? $args['default'] : '';
    		$name  = '';
    	} 
    	$readonly = $args['readonly'] === true ? ' readonly="readonly"' : '';
        $placeholder = $args['placeholder'] != "" ? ' placeholder="'.$args['placeholder'].'"': '';
    	$size     = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
        $html     = '<input type="text" class="' . sanitize_html_class( $size ) . '-text" id="'.esc_attr($attrs['id'] ).'" name="'.esc_attr( $name ).'" value="' . esc_attr( stripslashes( $attrs['value'] ) ) . '"' . $readonly.$placeholder . '/>';
    	$html    .= '<label for="'.esc_attr($attrs['id'] ).'"> '  . $this->get_field_description( $args ) . '</label>';

    	echo $html;
    }

    /**
     * Displays a url field for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_url( $args ){
        $this->callback_text( $args );
    }

    /**
     * Displays a number field for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_number( $args ){
        
        $attrs = $this->get_field_attrs( $args );
        $name = $attrs['name'];
        
        $readonly = $args['readonly'] === true ? ' readonly="readonly"' : '';
        $placeholder = $args['placeholder'] != "" ? ' placeholder="'.$args['placeholder'].'"': '';
        $max = $args['max'] != '' ? ' max="'.$args['max'].'"' : '';
        $min = $args['min'] != '' ? ' min="'.$args['min'].'"' : '';
        $step = $args['step'] != '' ? ' step="'.$args['step'].'"' : '';
        $size     = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
        $html     = '<input type="number" class="' . sanitize_html_class( $size ) . '-text" id="'.esc_attr($attrs['id'] ).'" name="'.esc_attr( $name ).'" value="' . esc_attr( stripslashes( $attrs['value'] ) ) . '"' .$readonly.$placeholder.$max.$min.$step. '/>';
        $html    .= '<label for="'.esc_attr($attrs['id'] ).'"> '  . $this->get_field_description( $args ) . '</label>';

        echo $html;
    }

    /**
     * Displays a checkbox for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_checkbox( $args ){
        
        $attrs = $this->get_field_attrs( $args );
        $checked = checked( $attrs['value'], 'on', false );
        $html  = '<fieldset>';
        $html  .= '<label for="'.esc_attr($attrs['id']).'">';
        $html  .= '<input type="hidden" name="'.esc_attr($attrs['name']).'" value="off" />';
        $html  .= '<input type="checkbox" class="checkbox" id="'.esc_attr($attrs['id']).'" name="'.esc_attr($attrs['name']).'" value="on" '.$checked.' />';
        $html  .= $args['label'].'</label>';
        $html  .= $this->get_field_description( $args );
        $html  .= '</fieldset>';

        echo $html;
    }

    /**
     * Displays a multicheckbox a settings field
     *
     * @param array   $args settings field args
     */
    function callback_multicheck( $args ){

        $attrs = $this->get_field_attrs( $args );
        $value = $attrs['value'];

        $html  = '<fieldset>';

        foreach ( $args['options'] as $key => $label )
        {
            $checked = isset( $value[$key] ) ? ' checked="checked" ' : '';
            $name = $attrs['name'].'['.$key.']';
            $html    .= '<label for="'.esc_attr( $attrs['id'].'_'.$key ).'">';
            $html    .= '<input type="checkbox" class="checkbox" id="'.esc_attr( $attrs['id'].'_'.$key ).'" name="'.$name.'" value="'.esc_attr( $key ).'" '.$checked.' />';
            $html    .= $label.'</label><br>';
        }

        $html .= $this->get_field_description( $args );
        $html .= '</fieldset>';

        echo $html;
    }

    /**
     * Displays a multicheckbox a settings field
     *
     * @param array   $args settings field args
     */
    function callback_radio( $args ){

        $attrs = $this->get_field_attrs( $args );
        $html  = '<fieldset>';

        foreach ( $args['options'] as $key => $label )
        {
            $checked = checked( $attrs['value'], $key, false );
            $html .= '<label for="'.esc_attr( $attrs['id'].'_'.$key ).'">';
            $html .= '<input type="radio" class="radio" id="'.esc_attr( $attrs['id'].'_'.$key ).'" name="'.esc_attr( $attrs['name'] ).'" value="'.esc_attr( $key ).'" '.$checked.' />';
            $html .= $label.'</label><br>';
        }

        $html .= $this->get_field_description( $args );
        $html .= '</fieldset>';

        echo $html;
    }

    /**
     * Displays a selectbox for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_select( $args ){
        
        $attrs = $this->get_field_attrs( $args );
        $size  = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';
        
        $html  = '<select class="'.esc_attr( $size ).'" name="'.esc_attr( $attrs['name'] ).'" id="'.esc_attr( $attrs['id'] ).'">';
        
        foreach ( $args['options'] as $key => $label )
        {
            $selected = selected( $attrs['value'], $key, false );
            $html .= '<option value="'.$key.'" '.$selected.'>'.$label.'</option>';
        }

        $html .= '</select>';
        $html .= $this->get_field_description( $args );

        echo $html;
    }

    /**
     * Displays a textarea for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_textarea( $args ){
        $attrs = $this->get_field_attrs( $args );
        $size  = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';
        $placeholder = $args['placeholder'] != "" ? ' placeholder="'.$args['placeholder'].'"': '';
        $html  = '<textarea rows="5" cols="55" class="'.esc_attr( $size ).'-text" id="'.esc_attr( $attrs['id'] ).'" name="'.esc_attr( $attrs['name'] ).'" '.$placeholder.'>'.esc_textarea( $attrs['value'] ).'</textarea>';
        $html  .= $this->get_field_description( $args );

        echo $html;
    }

    /**
     * Displays a HTML for a settings field
     *
     * @param array   $args settings field args
     * @return string
     */
    function callback_html( $args ){
        echo $args['desc'];
    }

    /**
     * Displays a wysiwyg editor for a settings field
     *
     * @param array  $args settings field args
     */
    function callback_editor( $args ){

        $attrs = $this->get_field_attrs( $args );
        $value = $this->get_option( $args['id'], $args['section'], $args['default'] );
        $size  = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : '500px';

        echo '<div style="max-width: ' . $size . ';">';

        $editor_settings = array(
            'teeny'         => true,
            'textarea_name' => $attrs['name'],
            'textarea_rows' => 10
        );

        if ( isset( $args['options'] ) && is_array( $args['options'] ) ) {
            $editor_settings = array_merge( $editor_settings, $args['options'] );
        }

        wp_editor( $attrs['value'], $attrs['id'], $editor_settings );

        echo '</div>';

        echo $this->get_field_description( $args );
    }

    /**
     * Displays a file upload field for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_file( $args ) {

        $attrs = $this->get_field_attrs( $args );

        $size  = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';
        $label = isset( $args['options']['button_label'] ) ? $args['options']['button_label'] : __( 'Choose File' );

        $html  = '<input type="text" class="'.esc_attr($size).'-text wpsa-url" id="'.esc_attr($attrs['id']).'" name="'.esc_attr($attrs['name']).'" value="'.esc_attr($attrs['value']).'"/>';
        $html  .= '<input type="button" class="button wpsa-browse" value="' . $label . '" />';
        $html  .= $this->get_field_description( $args );

        echo $html;
    }

    /**
     * Displays a password field for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_password( $args ) {

        $attrs = $this->get_field_attrs( $args );
        $size  = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';
        $placeholder = $args['placeholder'] != "" ? ' placeholder="'.$args['placeholder'].'"': '';
        $html  = '<input type="password" class="'.$size.'-text" id="'.esc_attr( $attrs['id'] ).'" name="'.esc_attr( $attrs['name'] ).'" value="'.esc_attr( $attrs['value'] ).'" '.$placeholder.' />';
        $html  .= $this->get_field_description( $args );

        echo $html;
    }

    /**
     * Displays a color picker field for a settings field
     *
     * @param array   $args settings field args
     */
    public function callback_color( $args ) {

        $attrs = $this->get_field_attrs( $args );
        $size  = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';

        $html  = '<input type="text" class="'.$size.'-text wp-color-picker-field" id="'.esc_attr( $attrs['id'] ).'" name="'.esc_attr( $attrs['name'] ).'" value="'.esc_attr( $attrs['value'] ).'" data-default-color="'.esc_attr( $attrs['default'] ).'" />';
        $html  .= $this->get_field_description( $args );

        echo $html;
    }


	/**
	 * Settings Sanitization
	 *
	 * Adds a settings error (for the updated message)
	 * At some point this will validate input
	 *
	 * @param array $input The value inputted in the field
	 *
	 * @return string $input Sanitizied value
	 */
	public function settings_sanitize( $input = array() ) {
		if( !is_array( $input ) ) { return $input;}
	
		$prefix = $this->prefix;		
		$old_options = $this->get_options();
		if( !is_array( $old_options ) ) { $old_options = array(); }
		
		if( empty( $_POST['_wp_http_referer'] ) ) 
		{
			return $input;
		}
	
		parse_str( $_POST['_wp_http_referer'], $referrer );
	
		$fields = $this->fields;
		$tab      = isset( $referrer['tab'] ) ? $referrer['tab'] : 'general';
		$section  = isset( $referrer['section'] ) ? $referrer['section'] : 'main';
	
		$input = ( $input ) ? $input : array();
	
		$input = apply_filters( $prefix.'_' . $tab . '_' . $section . '_sanitize', $input );
	
		// Loop through each setting being saved and pass it through a sanitization filter
		foreach ( $input as $key => $value ) {
	
			// Get the setting type (checkbox, select, etc)
			$type = isset( $fields[ $tab ][ $section ][ $key ]['type'] ) ? $fields[ $tab ][ $section ][ $key ]['type'] : false;
			
			if ( $type )
			{
				$sanitize_callback = false;
				$s_callback = isset( $fields[ $tab ][ $section ][ $key ]['sanitize_callback'] ) ? $fields[ $tab ][ $section ][ $key ]['sanitize_callback'] : false;
				if( $s_callback && is_callable( $s_callback ) )
				{
					$sanitize_callback = $s_callback;
				}
				elseif( method_exists( $this, "sanitize_callback_".$type) )
				{
					$sanitize_callback = array( $this, "sanitize_callback_".$type );
				}
				// Field type specific filter
				if( $sanitize_callback )
				{
					$input[ $key ] = call_user_func( $sanitize_callback, $value );
				}
			}
	
		}
		
		// General filter
		$input = apply_filters( $prefix.'_sanitize', $input );
			
		// Loop through the whitelist and unset any that are empty for the tab being saved
		$section_fields = ! empty( $fields[ $tab ][ $section ] ) ? $fields[ $tab ][ $section ] : array();
	
		if ( ! empty( $section_fields ) )
		{
			foreach ( $section_fields as $key => $value )
			{
	
				// settings used to have numeric keys, now they have keys that match the option ID. This ensures both methods work
				if ( is_numeric( $key ) )
				{
					$key = $value['id'];
				}
				if ( empty( $input[ $key ] ) )
				{
					unset( $old_options[ $key ] );
				}
	
			}
		}
	
		// Merge our new settings with the existing
		$output = array_merge( $old_options, $input );
		add_settings_error( $prefix.'_notices', '', 'Settings updated', 'updated' );
	
		return $output;
	}

    /**
     * Get the value of a settings field
     *
     * @param string  $option  settings field name
     * @param string  $default default text if it's not found
     * @return mix
     */
    public function get_option( $option, $default = '' ) {

		$options =( !is_array( $this->options ) ) ? $this->get_options() : $this->options;
		
        if ( isset( $options[$option] ) )
		{
            return $options[$option];
        }

        return $default;
    }

    /**
     * Get all values of a settings fields
     * Set $this->options with new values
     * @return array
     */
    public function get_options() {

        $options = get_option( $this->settings_name );
		if( !is_array( $options ) ) 
		{
			$options = array();	
		}
		$this->options = $options;
        return $options;
    }
	
	/**
	 * Prepare some vars before run display functions
	 **/
	public function prepare_to_display(){
	
		$tabs = empty( $this->tabs ) ? array() : $this->tabs;
		$active_tab    = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $tabs ) ? $_GET['tab'] : 'general';
		$sections = isset( $this->sections[ $active_tab ] ) ? $this->sections[ $active_tab ] : false;
		$key = 'main';
	
		if ( is_array( $sections ) ) {
			$key = key( $sections );
		}

		$active_section = isset( $_GET['section'] ) && ! empty( $sections ) && array_key_exists( $_GET['section'], $sections ) ? $_GET['section'] : $key;
		$this->active_tab = $active_tab;
		$this->active_section = $active_section;
	
	
	}	
	/**
	 * Print tab navigation
	 **/ 
	public function show_tabs(){
	
		$tabs = empty( $this->tabs ) ? array() : $this->tabs;
		$active_tab    = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $tabs ) ? $_GET['tab'] : 'general';
		$sections = isset( $this->sections[ $active_tab ] ) ? $this->sections[ $active_tab ] : false;
		$key = 'main';
	
		if ( is_array( $sections ) ) {
			$key = key( $sections );
		}

		$section = isset( $_GET['section'] ) && ! empty( $sections ) && array_key_exists( $_GET['section'], $sections ) ? $_GET['section'] : $key;
	
	
	}//end func

    /**
     * Show navigations as tab
     *
     * Shows all the settings section labels as tab
     */
    function show_navigation() {
        $html = '<h2 class="nav-tab-wrapper">';

        foreach ( $this->sections as $tab ) {
            $html .= sprintf( '<a href="#%1$s" class="nav-tab" id="%1$s-tab">%2$s</a>', $tab['id'], $tab['title'] );
        }

        $html .= '</h2>';

        echo $html;
    }

	/**
	 * Display 
	 * Renders the settings page contents.
	 */
	function display() {
	
		if( $this->duplicate_field_id )
        {
            echo '<p style="color:red;font-size:20px;"> Duplicate settings field is detected. Field id = '.$this->duplicate_field_id.'</p>';
            return ;
        }
        settings_errors();
        $tabs = empty( $this->tabs ) ? array() : $this->tabs;
		$active_tab    = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $tabs ) ? $_GET['tab'] : 'general';
		$sections = isset( $this->sections[ $active_tab ] ) ? $this->sections[ $active_tab ] : false;
		$key = 'main';
		if ( is_array( $sections ) ) {
			$key = key( $sections );
		}

		$active_section = isset( $_GET['section'] ) && ! empty( $sections ) && array_key_exists( $_GET['section'], $sections ) ? $_GET['section'] : $key;
		$this->active_tab = $active_tab;
		$this->active_section = $active_section;
				
		ob_start();
		?>

		<h1 class="nav-tab-wrapper">
			<?php
			foreach( $this->tabs as $tab_id => $tab_name ) {

				$tab_url = add_query_arg( array(
					'settings-updated' => false,
					'tab'              => $tab_id,
				) );

				// Remove the section from the tabs so we always end up at the main section
				$tab_url = remove_query_arg( 'section', $tab_url );

				$active = $active_tab == $tab_id ? ' nav-tab-active' : '';

				echo '<a href="' . esc_url( $tab_url ) . '" title="' . esc_attr( $tab_name ) . '" class="nav-tab' . $active . '">';
					echo esc_html( $tab_name );
				echo '</a>';
			}
			?>
		</h1>
		<?php

		$number_of_sections = count( $sections );
		$number = 0;
		if ( $number_of_sections > 1 ) {
			echo '<div><ul class="subsubsub">';
			foreach( $sections as $section_id => $section_name ) {
				echo '<li>';
				$number++;
				$tab_url = add_query_arg( array(
					'settings-updated' => false,
					'tab' => $active_tab,
					'section' => $section_id
				) );
				$class = '';
				if ( $active_section == $section_id ) {
					$class = 'current';
				}
				echo '<a class="' . $class . '" href="' . esc_url( $tab_url ) . '">' . $section_name . '</a>';

				if ( $number != $number_of_sections ) {
					echo ' | ';
				}
				echo '</li>';
			}
			echo '</ul></div>';
		}
		?>
		<div id="tab_container">
			<form method="post" action="options.php">
				<table class="form-table">
				<?php
				$prefix =  $this->prefix;
                //Output nonce, action, and option_page fields for a settings page. 
				settings_fields( $this->settings_name );

				
				do_action( $prefix.'_tab_top', $active_tab, $active_section );
				do_action( $prefix.'_tab_top_' . $active_tab . '_' . $active_section );

				do_settings_sections( $prefix.'_' . $active_tab . '_' . $active_section );

				do_action( $prefix.'_tab_bottom_' . $active_tab . '_' . $active_section  );
				do_action( $prefix.'_tab_bottom', $active_tab , $active_section );


				?>
				</table>
				<?php submit_button(); ?>
			</form>
		</div><!-- #tab_container-->
		<?php
		$this->script();
		echo ob_get_clean();
	}
	

    /**
     * JavaScript and CSS codes 
     * Initiate Color Picker
     * Initiate file uploader
     */
    function script() {
        ?>
        <script>
            jQuery(document).ready(function($) {
                //Initiate Color Picker
                $('.wp-color-picker-field').wpColorPicker();

                $('.wpsa-browse').on('click', function (event) {
                    event.preventDefault();

                    var self = $(this);

                    // Create the media frame.
                    var file_frame = wp.media.frames.file_frame = wp.media({
                        title: self.data('uploader_title'),
                        button: {
                            text: self.data('uploader_button_text'),
                        },
                        multiple: false
                    });

                    file_frame.on('select', function () {
                        attachment = file_frame.state().get('selection').first().toJSON();

                        self.prev('.wpsa-url').val(attachment.url);
                    });

                    // Finally, open the modal
                    file_frame.open();
                });
        });
        </script>

        <style type="text/css">
            /** WordPress 3.8 Fix **/
            .form-table th { padding: 20px 10px; }
            #wpbody-content .metabox-holder { padding-top: 5px; }
        </style>
        <?php
    }

}
endif;