<?php
/**
 * Class for a single form
 * @package WP Admin Form Builder
 * @author oneTarek
 * @since 1.0.0
 */

// Don't allow direct access
if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( "WPAFB_Form" ) ):

class WPAFB_Form{

	/**
	 * Unique string as an ID of the form.
	 * @var string
	 */
	public $id;
	/**
	 * String as CSS class name form wrapper DIV.
	 * @var string
	 */
	public $class;

	/**
	 * True/False decision to define whether this form will be shown in single or two colummn.
	 * @var bool
	 */
	public $single_column;

	/**
	 * All fields settings including groups. And nested fields in groups.
	 * This array will store only minimal data for fields. Detail data will be stored in $field_list 
	 * @var array
	 */

	private $fields;

	/**
	 * Only store all fields without nesteding in groups. Fields in groups will also be stored in this array but not as multidimensional array. 
	 * This array will be generated from the $fiels array.
	 */

	private $field_list = array();
	/**
	 * Header html of the form
	 * @var string
	 */
	public $header;

	/**
	 * Footer html of the form
	 * @var string
	 */
	public $footer;

	/**
	 * Header callback of the form
	 * @var string/array
	 */
	public $header_callback;

	/**
	 * Footer callback of the form
	 * @var string/array
	 */
	public $footer_callback;

	/**
	 * Validation callback of the form input
	 * @var string/array
	 */
	public $validation_callback;
	
	/**
	 * Method of form submisison
	 * @var string 
	 **/
	public $request_method;

	/**
	 * Store the submitted data after the form submission.
	 * This array is an one dimensional array of fieldid=>value pairs. 
	 * Some values are validated and some may not. If there is not error message in $this->input_errors then these values can be stored in db.
	 * @var array / null
	 */
	private $submitted_data = null;

	/**
	 * Store the input error messages 
	 * @var string 
	 **/
	public $input_errors = array();

	/**
	 * CSS class name for fields those have input error
	 */
	public $input_error_class_name = 'wbafb-input-error';

	public function __construct( $args ){

		$defaults = array(
            'id' => '',
            'class' => '',
            'single_column' => false,
            'fields'  => array(),
            'header' => '',
            'footer' => '',
            'header_callback' => '',
            'footer_callback' => '',
            'validation_callback' => '',
            'request_method'  => 'post'
        );
        $args = wp_parse_args( $args, $defaults );
        
        if( trim( $args['id'] ) == "" )
        {
        	throw new Exception('WP Admin Form Builder Error : Form id is required for WPAFB_Form class. Add a unique string for "id" key to $args array. Pass $args array through the constructor of WPAFB_Form class'); 
        	return;
        }
        
        $this->id = trim( $args['id'] );
        $this->class = trim( $args['class'] );
        $this->single_column = is_bool( $args['single_column'] ) ? $args['single_column'] : false;
        $this->header = $args['header'];
        $this->footer = $args['footer'];
        $this->header_callback = $args['header_callback'];
        $this->footer_callback = $args['footer_callback'];
        $this->validation_callback = $args['validation_callback'];
        $this->request_method = strtolower( trim( $args['request_method'] ) );
        if( $this->request_method != "get" || $this->request_method != 'post')
        {
        	$this->request_method = 'post';
        }
        $fields = is_array( $args['fields'] )? $args['fields'] : array();
        $this->setup_fields($fields);
	}

	public function render(){

		echo '<div class="wpafb-wrapper '.esc_attr($this->class).'" id="'.esc_attr( $this->id ).'">';
			echo '<div class="wpafb-header" id="'.esc_attr( $this->id."_header" ).'">';
				$this->form_header();
			echo '</div>'; 
			echo '<table class="form-table wpafb-form-table wpafb-form-table-main">';
				$this->_render( $this->fields );
			echo '</table>';
			echo '<div class="wpafb-footer" id="'.esc_attr( $this->id."_footer" ).'">';
				$this->form_footer();
			echo '</div>';
		echo '</div>';
		
	}

	/**
	 * Render groups and fields recursively
	 */
	private function _render( $short_data , $call_option = array("group"=>false ) ){
		
		$group = isset( $call_option['group'] ) ? $call_option['group'] : false;
		$group_h_in_v = isset( $call_option['group_h_in_v'] ) ? $call_option['group_h_in_v'] : false;
		if( $group )
		{
			$data = $this->field_list[ $short_data['id'] ];
			if( $this->single_column ){ $data['single_column'] = true; }//force to render this field in signle column
			
			
			$fields = isset( $short_data['fields'] ) && is_array( $short_data['fields'] ) ? $short_data['fields'] : array();
			$group_type = $data['group_type'];
			
			if( $group_type == "row")
			{
				$fieldset 	= isset($data['fieldset']) && is_array($data['fieldset'])?  $data['fieldset'] : false;

				echo '<td colspan="2">';
					if( $fieldset )
			        {
			        	$id = $data['id'];
			        	$fieldset_id = $id."_fieldset";
			        	$help_icon = $this->get_help_icon( $data );
			        	echo '<fieldset id="'.esc_attr( $fieldset_id ).'">';
			        	$legend = isset($fieldset['legend']) ? $fieldset['legend'] : "";
			        	echo '<legend>'.$legend.$help_icon.'</legend>';
			        }
		        

					echo '<table class="form-table wpafb-form-table">';
						$this->_render( $fields );
					echo '</table>';
					if( $fieldset )
					{
						echo '</fieldset>';
					}
					echo $this->get_field_description( $data );
				echo '</td>';
			}
			elseif( $group_type == "vertical" )
			{
				$single_column =  isset( $data['single_column'] ) ? $data['single_column'] : false;
				$label =  isset( $data['label'] ) ? $data['label'] : "";
				$colspan = ( $single_column === true ) ? 2 : 1;
				$rcol_class = ( $single_column === true ) ? 'wpafb-single-col' : 'wpafb-right-col';
				if( $single_column === false )
				{
					echo '<td class="wpafb-left-col"><strong><label>'.$label.'</label></strong></td>';
				}
				
				echo '<td class="'.$rcol_class.'" colspan="'.$colspan.'">';
				if( $single_column === true && $label !='' )
				{
					echo '<p><strong><label>'.$label.'</label></strong></p>';
				}

				$fieldset 	= isset($data['fieldset']) && is_array($data['fieldset'])?  $data['fieldset'] : false;
					if( $fieldset )
			        {
			        	$id = $data['id'];
			        	$fieldset_id = $id."_fieldset";
			        	$help_icon = $this->get_help_icon( $data );
			        	echo '<fieldset id="'.esc_attr( $fieldset_id ).'">';
			        	$legend = isset($fieldset['legend']) ? $fieldset['legend'] : "";
			        	echo '<legend>'.$legend.$help_icon.'</legend>';
			        }
					foreach( $fields as $key => $short_args )
					{
						$short_args['id'] = $key;
						$args = $this->field_list[ $key];
						$type = isset( $args['type'] ) ? trim( $args['type'] ) : "";
						if( $type == ""){ continue; }
						
						$args['used_in_group_type'] = $group_type;
						
						if( $type == "group")
						{
							$gtype = isset( $args['group_type'] ) && in_array( trim($args['group_type']) , array("row", "horizontal", "vertical" ) ) ? trim( $args['group_type'] ) : "row";
							if( $gtype == "horizontal")
							{
								$gid = $args['id'];
								$hidden =  isset( $args['hidden'] ) ? $args['hidden'] : false;
								$row_class = ( $hidden === true ) ? "hidden" : "";
								echo '<div id="'.esc_attr($gid).'" class="'.esc_attr($row_class).'">';
									$this->_render( $short_args , array( "group" => true, "group_h_in_v" => true  ) );//group render with horizontal group in vertical group logic 
								echo "</div>";
							}
							else
							{
								continue;
							}
							
						}
						else
						{
							$fl =  isset( $args['label'] ) ? $args['label'] : "";
							$fid = $args['id'];
							$row_id = ($fid !="") ? "row_".$fid : "";
							$fl_class = ( $this->is_disabled_field( $args ) ) ? "disabled" : "";
							$hidden =  isset( $args['hidden'] ) ? $args['hidden'] : false;
							$row_class = ( $hidden === true ) ? "hidden" : "";
							echo '<div id="'.esc_attr($row_id).'" class="'.esc_attr($row_class).'">';
								echo '<label class="'.$fl_class.'" for='.esc_attr($fid).'>'.$fl. '</label><br>';
								$this->call_callback( $args );
							echo "</div>";
						}
							
						
					}
					
					if( $fieldset )
					{
						echo '</fieldset>';
					}
					echo $this->get_field_description( $data );
				echo '</td>';
			}
			elseif($group_type == "horizontal")
			{
				// if this horizontal group is called in a vertical group then do not create columns with label.
				if( $group_h_in_v == false )
				{
				
					$single_column =  isset( $data['single_column'] ) ? $data['single_column'] : false;
					$label =  isset( $data['label'] ) ? $data['label'] : "";
					$help_icon = $this->get_help_icon( $data );
					$colspan = ( $single_column === true ) ? 2 : 1;
					$rcol_class = ( $single_column === true ) ? 'wpafb-single-col' : 'wpafb-right-col';
					if( $single_column === false )
					{
						echo '<td class="wpafb-left-col"><strong><label>'.$label.'</label></strong></td>';
					}
					
					echo '<td class="'.$rcol_class.'" colspan="'.$colspan.'">';
					if( $single_column === true && $label !='' )
					{
						echo '<p><strong><label>'.$label.'</label></strong></p>';
					}

					$fieldset 	= isset($data['fieldset']) && is_array($data['fieldset'])?  $data['fieldset'] : false;
					if( $fieldset )
			        {
			        	$id = $data['id'];
			        	$fieldset_id = ( $id != "" )? $id."_fieldset" : "";
			        	
			        	echo '<fieldset id="'.esc_attr( $fieldset_id ).'">';
			        	$legend = isset($fieldset['legend']) ? $fieldset['legend'] : "";
			        	echo '<legend>'.$legend.$help_icon.'</legend>';
			        }
				}

				$i=0;
				foreach( $fields as $key => $short_args )
				{
					$short_args['id'] = $key;
					$args = $this->field_list[ $key];
					$type = isset( $args['type'] ) ? trim( $args['type'] ) : "";
					if( $type == ""){ continue; }
					if( $type == "group") { continue; }//do not allow group in a adjacent-horizontal group
					$args['used_in_group_type'] = $group_type;
					$fid = $args['id']; 
					$fl =  isset( $args['label'] ) ? $args['label'] : "";
					$fl_class = ( $this->is_disabled_field( $args ) ) ? "disabled" : "";
					if($fl != "")
					{
						if( $i==0 ) { echo '<label class="'.$fl_class.'" for='.esc_attr($fid).'>'.$fl. ' '; } else { echo ' <label class="'.$fl_class.'" for='.esc_attr($fid).'>'.$fl. ' '; }//For first item use only after space.
					}
					$this->call_callback( $args );
					if($fl != "")
					{
						echo '</label> ';
					}

					$i++;	
				}
				if( ! $fieldset )
				{
					echo $help_icon;
				}

				if( $group_h_in_v == false )
				{
					if( $fieldset )
					{
						echo '</fieldset>';
					}
					echo $this->get_field_description( $data );
				echo '</td>';
				}
				else
				{
					echo $this->get_field_description( $data );
				}
			}//end of if( $group_type == ....)

			
		}
		else // else of if( $group )
		{
			$fields = $short_data;
		
			foreach( $fields as $key => $short_args )
			{
				$short_args['id'] = $key;
				$args = $this->field_list[ $key ];

				if( $this->single_column ){ $args['single_column'] = true; }//force to render this field in signle column
				$type = isset( $args['type'] ) ? trim( $args['type'] ) : "";
				if( $type == ""){ continue; }
				$type = trim( $args['type'] );
				$id = $args['id'];
				$row_id = isset( $args['row_id'] ) ? $this->sanitize_attr( $args['row_id'] ) : "row_".$id;
				$row_class = "row wpafb-row";
				if( isset( $args['row_class'] ) )
				{
					$row_class = $row_class." ".trim( $args['row_class'] );
				}
				$hidden =  isset( $args['hidden'] ) ? $args['hidden'] : false;
				if( $hidden === true )
				{
					$row_class = $row_class." hidden";
				}
				$single_column =  isset( $args['single_column'] ) ? $args['single_column'] : false;
				$label = isset( $args['label'] ) ? $args['label'] : "";
				$colspan = ( $single_column === true ) ? 2 : 1;
				$rcol_class = ( $single_column === true ) ? 'wpafb-single-col' : 'wpafb-right-col';

				if( $type == "group" )
				{
					
					$group_type = isset( $args['group_type'] ) && in_array( trim($args['group_type']) , array("row", "horizontal", "vertical" ) ) ? trim( $args['group_type'] ) : "row";
					$args['used_in_group_type'] = $group_type;
					$row_class = $row_class." wpafb-row-group wpafb-row-group-".$group_type;
					$row_id = $id; // ID will be used as row_id for groups
					echo '<tr class="'.esc_attr($row_class).'" id="'.esc_attr($row_id).'">';
						$this->_render( $short_args , array( "group" => true ) );//group render
					echo '</tr>';
				}
				else
				{
					$row_class = $row_class." wpafb-row-not-group";
					$fl_class = ( $this->is_disabled_field( $args ) ) ? "disabled" : "";
					if( $type == "html" || $type == "header" )
					{
						$row_class = $row_class." wpafb-row-not-group wpafb-row-".$type;
					}
					
					
					echo '<tr class="'.esc_attr($row_class).'" id="'.esc_attr($row_id).'">';

					if( $single_column === false )
					{
						echo '<td class="wpafb-left-col"><strong><label class="'.$fl_class.'" for='.esc_attr($id).'>'.$label.'</label></strong></td>';
					}
					
						echo '<td class="'.$rcol_class.'" colspan="'.$colspan.'">';
							if( $single_column === true && $label !='' )
							{
								echo '<p><strong><label class="'.$fl_class.'" for='.esc_attr($id).'>'.$label.'</label></strong></p>';
							}
								$this->call_callback( $args );
						echo '</td>';
					echo '</tr>';

				}// end if( $type == "group" )
				
			}//end foreach( $fields as $key=>$args )
		}// end of if( $group )
		
	}//end function _render()

	/**
     * Get field value
     * Search value in $this->data array by field id. if value found then return that. 
     * If value not found then check the default value from field settings.
     * if default found then return that else return blank. 
     * @param array $args settings field args
     * @return mixed or null
     */
    public function get_field_value( $args ){
        
        if( isset( $args['value']  ) )
        {
        	return $args['value'];
        }
        elseif( isset( $args['default'] ) )
        {
        	return $args['default'];
        }
        return '';

    }

	/**
     * Get field description for display
     *
     * @param array   $args settings field args
     */
    public function get_field_description( $args ){

        if ( ! empty( $args['desc'] ) )
        {
            $desc = sprintf( '<p class="description">%s</p>', $args['desc'] );
        } 
        else 
        {
            $desc = '';
        }
        return $desc;
    }


    /**
     * Sanitizes a string key
     *
     * Keys are used as internal identifiers. Alphanumeric characters, dashes, underscores, stops, colons and slashes are allowed
     * @param  string $key String key
     * @return string Sanitized key
     */
    public function sanitize_key( $key ){

    	$raw_key = $key;
    	$key = preg_replace( '/[^a-zA-Z0-9_\-\.\:\/]/', '', $key );
    	return $key;
    }

    /**
     * Sanitizes a attribute name string and value string when needed.
     *
     * Alphanumeric characters, dashes and underscores are allowed
     * @param  string $key String key
     * @return string Sanitized string
     * This function is simillar to sanitize_html_class() function.
     */
    public function sanitize_attr( $key ){

    	//Strip out any % encoded octets
		$sanitized = preg_replace( '|%[a-fA-F0-9][a-fA-F0-9]|', '', $key );
		//Limit to A-Z,a-z,0-9,_,-
		$sanitized = preg_replace( '/[^A-Za-z0-9_-]/', '', $sanitized );
    	return $sanitized;
    }


    /**
     * Create HTML attributes string to apply to the field tag
     * Example string : onclick="myfunc()" custom_attr="my value".... 
     * Attr value will be wraped in double quote.
     * Skip any attribute name that contains invalid characters. 
     * id and class attributes are not allowed. Those are added by default. 
     * @param array $args . Field args Array of attribute names and values where name is the array key and value is the item value. 
     * @param array $exclude. Array of attribute names those should not be added to the return string.  
     */
    public function get_field_attribute_string( $args , $exclude = array() ){

    	if( ! is_array( $args ) ){ return "";}
    	if( ! isset($args['attrs']) || !is_array( $args['attrs'] ) ){ return "";}
    	$attrs = $args['attrs'];

    	if( ! is_array( $exclude ) ){ $exclude =  array() ; }
    	$exclude =  array_merge($exclude, array("name", "id", "class", "value", "readonly", "disabled", "placeholder", "required", "fieldtype" ) );
    	$str = " ";
    	foreach( $attrs as $name => $value )
    	{
    		if( in_array($name, $exclude ) ){ continue; }
    		//Skip any attribute name that contains invalid characters.
    		$name_sanitized = $this->sanitize_attr( $name );
    		if( $name != $name_sanitized ){ continue ;}
    		$str.= ' '.$name.'="'.esc_attr($value).'"';
    	}
    	return $str;
    }

    /**
     * Add style attribute to args[attrs] array 
     * @param array $args , field options
     * @param array $args
     */
    public function add_style_attr_to_args( $args , $style ){

    	if( isset( $args['attrs'] ) && is_array( $args['attrs'] ) )
		{
			if( isset( $args['attrs']['style'] ) )
			{
				$args['attrs']['style'] = $style.$args['attrs']['style'];
			}
			else
			{
				$args['attrs']['style'] = $style;
			}
			
		}
		else
		{
			$args['attrs'] = array( "style" => $style );
		}
		return $args;
    }

    public function get_used_in_group_type( $args ){

    	return isset( $args['used_in_group_type'] ) ? $args['used_in_group_type'] : "";
    }

    /**
     * Create HTML for help icon with tooltip text
     * @param array $help options of help icon
	 * @return string $html
	 */
    public function create_help_icon_html( $help ){

    	$icon_class = 'wpafb-mask-icon wpafb-mask-icon-'.$help['icon'];
    	$icon_style = 'background-color:'.$help['color'].';';
    	return '<span class="wpafb-tooltip-toggle wpafb-help-icon" tooltip-text="'.esc_attr( $help['text'] ).'" tabindex="0"><span class="'.esc_attr( $icon_class ).'" style="'.esc_attr( $icon_style ).'"></span></span>';
    }
    /**
     * Return HTML for help icon with tooltip text. If 'help' options is not set then return blank string.
     * @param string $args
	 * @return string $html
	 */
    public function get_help_icon( $args ){

    	$help = isset($args['help'] ) ? $args['help'] : array();
		if( empty($help) ){ return ""; }
		if( !is_array($help))
		{
			$help = array('text'=> $help );
		}

		$defaults = array(
            'icon' => 'help',
            'text' => '',
            'color' => '#0080ff'
        );
        $help = wp_parse_args( $help, $defaults );
        if( $help['text'] == '' ){ return ""; }
        if( !in_array( $help['icon'], array('help', 'info' ) ) )
        {
        	$help['icon'] = 'help';
        }
        return $this->create_help_icon_html( $help );
    }

    /**
     * Return HTML for small text blog 'unit_text'. If 'unit_text' options is not set then return blank string.
     * @param string $args
	 * @return string $html
	 */
    public function get_unit_text( $args ){

    	if( isset($args['unit_text'] ) && trim( $args['unit_text'] ) != "")
    	{
    		return '<span class="wpafb-unit-text" >'.trim( $args['unit_text'] ).'</span>';
    	}
    	else
    	{
    		return "";
    	}
    }

    /**
     * Return HTML for after. If 'after' options is not set then return blank string.
     * @param string $args
	 * @return string $html
	 */
    public function get_after_html( $args ){

    	if( isset($args['after'] ) && trim( $args['after'] ) != "")
    	{
    		return $args['after'] ;
    	}
    	else
    	{
    		return "";
    	}
    }
    
    /**
	 * Check wheater a field is disabled or not
	 * @param array $args of field
	 * @return bool
	 */
    public function is_disabled_field( $args ){

    	$disabled_opt 	= isset($args['disabled']) ? $args['disabled'] : false;  
    	if( $disabled_opt === true ){ return true;} //can be an array of item keys for radio and multicheck. We will not disable that if all options are not disabled by provideing 'true'.
    	return false;
    }

    /**
     * Check wheather a field has input error and return error message or false
     */
    public function field_has_input_error( $args ){

    	return isset( $this->input_errors[ $args['id'] ] ) ? $this->input_errors[ $args['id'] ] : false;
    }
    /**
     * Render form header
     */
    public function form_header(){

        echo $this->header;

        if( !empty( $this->header_callback ) && is_callable( $this->header_callback ) )
        {
            call_user_func_array( $this->header_callback , array( $this ) );
        }
        return; 
    }

    /**
     * Render form footer
     */
    public function form_footer(){

        echo $this->footer;
        if( !empty( $this->footer_callback ) && is_callable( $this->footer_callback ) )
        {
            call_user_func_array( $this->footer_callback , array( $this ) );
        }
        return; 
    }

	/**
     * Main callback function that calls appropriate callback function for the field.
     * It decides the actuall callback function for the field.
     * and calls that callback function 
     * We need this wrapper function to pass current object of this class to the callback function declared in outside of this class.
     * So that custom callback functions can use public methods of this object.
     * Custom callback function receives two arguments. 
     *  1. $args ( field options )
     *  2. $this ( current object of this class )
     * @param array $args
     *
     *  Example of custom callback funciton 
     *  function my_field_func( $args , $form )
     *  {
     * 
     *      //do something to render custom field output.
     *  }
     *
     *
     **/
    public function call_callback( $args ){

        $callback = false;
        $arguments = array( $args );
        if( !empty( $args['callback'] ) && is_callable( $args['callback'] ) )
        {
            $callback = $args['callback'] ;
            $arguments[] = $this;
        
        }
        elseif( method_exists( $this, 'callback_'.$args['type'] ) )
        {
            $callback = array( $this, 'callback_'.$args['type'] );
        }
        else
        {
            $callback = array( $this, 'missing_callback' );
        }

        call_user_func_array( $callback , $arguments );  
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
	 * @param array $args field settings args
	 */
	public function callback_header( $args ){

		$fieldtype = ' fieldtype="header"';
		$value =  isset( $args['value'] ) ? $args['value'] : "";
		$align = isset( $args['align'] ) ? $args['align'] : "";
		
		if( $align != "")
		{
			$style = "text-align:".$align.";";
			$args = $this->add_style_attr_to_args( $args , $style );
		}
		$attrs_string = $this->get_field_attribute_string( $args );
		$tag =  isset( $args['tag'] ) ? strtolower( $args['tag'] ) : "";

		if( !in_array($tag, array('h1', 'h2', 'h3', 'h4', 'div') ) )
		{
			$tag = 'h2';
		}
		$id = isset( $args['id'] ) ? $this->sanitize_attr( $args['id'] ) : "";
		$class = 'wpafb-heading-tag';
		$class = isset( $args['class'] ) ? $class.' '.$this->sanitize_attr( $args['class'] ) : $class;

		echo '<'.$tag.' id="'.esc_attr($id).'" class="'.esc_attr($class).'" '.$fieldtype.$attrs_string.'>'.$value.'</'.$tag.'>';
	}

    /**
     * Text Callback
     *
     * Renders text field.
     *
     * @param array $args field settings args
     * @param string $input_type , default is 'text'. Use this to render as input type password email and url
     * @return void
     */
    public function callback_text( $args, $input_type = 'text' ){

    	$ugp 		= $this->get_used_in_group_type( $args );
    	$id 		= isset($args['id']) ? $this->sanitize_attr( $args['id'] ) : ""; 
    	if( $id == "" ){ return "";}
    	$name = $id;
    	$readonly 	= isset($args['readonly']) && $args['readonly'] === true ? ' readonly="readonly"' : '';
    	$disabled 	= isset($args['disabled']) && $args['disabled'] === true ? ' disabled="disabled"' : '';
        $placeholder= isset( $args['placeholder'] ) ? ' placeholder="'.esc_attr( $args['placeholder'] ).'"': '';
        $required 	= isset($args['required']) && $args['required'] === true ? ' required="required"' : '';
    	$size     	= ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $this->sanitize_attr( $args['size'] ) : 'regular';
    	$class 		= "wpafb-field wpafb-field-text wpafb-text";
    	$class 		= ( $size != "") ? $class." ".$size."-text" : $class;
    	if( $this->field_has_input_error( $args ) )
    	{
    		$class = $class.' '.$this->input_error_class_name;
    	}
		$class 		= isset($attrs['class']) ? $class." ".$this->sanitize_attr( $attrs['class'] ) : $class;
		$value 		= $this->get_field_value( $args );
		$atributes 	=  $this->get_field_attribute_string( $args );
		$fieldtype = ' fieldtype="'.$input_type.'"';
		$formid = ' formid="'.esc_attr($this->id).'"';

        $html = '<input type="'.$input_type.'" class="' . esc_attr( $class ).'" id="'.esc_attr($id ).'" name="'.esc_attr( $name ).'" value="' . esc_attr( $value  ) . '"' . $formid.$fieldtype.$readonly.$placeholder.$disabled.$required.$atributes . '/>';
    	$html .= $this->get_unit_text( $args );
        $html .= $this->get_help_icon( $args );
        $html .= $this->get_after_html( $args);

    	if( $ugp != "horizontal" )
    	{
    		$html    .= $this->get_field_description( $args );
    	}
    	echo $html;
    }

	/**
     * Renders a password type field
     * same as text type field
     *
     * @param array $args field settings args
     */
    public function callback_password( $args ) {

        $this->callback_text( $args , 'password' );
    }

    /**
     * Renders an email type field
     * same as text type field
     *
     * @param array $args field settings args
     */
    public function callback_email( $args ) {

        $this->callback_text( $args , 'email' );
    }


    /**
     * Renders a url type field
     *
     * @param array $args field settings args
     */
    public function callback_url( $args ){

        $this->callback_text( $args );
    }

    /**
     * Renders number type field.
     *
     * @param array $args field settings args
     * @return void
     */
    public function callback_number( $args ){
        
        $ugp 		= $this->get_used_in_group_type( $args );
    	$id 		= isset($args['id']) ? $this->sanitize_attr( $args['id'] ) : ""; 
    	if( $id == "" ){ return "";}
    	$name = $id;
    	$readonly 	= isset($args['readonly']) && $args['readonly'] === true ? ' readonly="readonly"' : '';
    	$disabled 	= isset($args['disabled']) && $args['disabled'] === true ? ' disabled="disabled"' : '';
        $placeholder= isset( $args['placeholder'] ) ? ' placeholder="'.esc_attr( $args['placeholder'] ).'"': '';
        $required 	= isset($args['required']) && $args['required'] === true ? ' required="required"' : '';
    	$size     	= ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $this->sanitize_attr( $args['size'] ) : 'regular';
    	$class 		= "wpafb-field wpafb-field-number wpafb-number wpafb-text";
    	$class 		= ( $size != "") ? $class." ".$size."-text" : $class;
    	if( $this->field_has_input_error( $args ) )
    	{
    		$class = $class.' '.$this->input_error_class_name;
    	}
		$class 		= isset($attrs['class']) ? $class." ".$this->sanitize_attr( $attrs['class'] ) : $class;
		$value 		= $this->get_field_value( $args );
		$atributes 	=  $this->get_field_attribute_string( $args, array("max", "min", "step" ) );
		$fieldtype = ' fieldtype="number"';
		$formid = ' formid="'.esc_attr($this->id).'"';
        $max 		= isset( $args['max'] )  ? ' max="'.esc_attr( $this->sanitize_attr( $args['max'] ) ).'"' : '';
        $min 		= isset( $args['min'] )  ? ' min="'.esc_attr( $this->sanitize_attr( $args['min'] ) ).'"' : '';
        $step		= isset( $args['step'] )  ? ' step="'.esc_attr( $this->sanitize_attr( $args['step'] ) ).'"' : '';
        

        $html     = '<input type="number" class="' . esc_attr( $class ).'" id="'.esc_attr($id ).'" name="'.esc_attr( $name ).'" value="' . esc_attr( $value  ) . '"'  .$formid.$fieldtype.$readonly.$placeholder.$max.$min.$step.$disabled.$required.$atributes. '/>';
        $html .= $this->get_unit_text( $args );
        $html .= $this->get_help_icon( $args );
        $html .= $this->get_after_html( $args);
        if( $ugp != "horizontal" )
    	{
    		$html    .= $this->get_field_description( $args );
    	}

        echo $html;
    }

    /**
     * Renders a checkbox type field
     *
     * @param array $args field settings args
     */
    public function callback_checkbox( $args ){
        
        $ugp 		= $this->get_used_in_group_type( $args );
    	$id 		= isset($args['id']) ? $this->sanitize_attr( $args['id'] ) : ""; 
    	if( $id == "" ){ return "";}
    	$name = $id;
    	$readonly 	= isset($args['readonly']) && $args['readonly'] === true ? ' onclick="return false;"' : '';
    	$disabled 	= isset($args['disabled']) && $args['disabled'] === true ? ' disabled="disabled"' : '';
 
    	$class 		= "wpafb-field wpafb-field-checkbox wpafb-checkbox";
		$class 		= isset($attrs['class']) ? $class." ".$this->sanitize_attr( $attrs['class'] ) : $class;
		$value 		= $this->get_field_value( $args );
		$on_off_values 	= ( isset( $args['on_off_values'] ) && is_array( $args['on_off_values'] ) && count( $args['on_off_values'] ) == 2 ) ? $args['on_off_values'] : array( "1", "0" );
		$on_value = $on_off_values[0];
		$off_value= $on_off_values[1];

        $checked = checked( $value, $on_value, false );
        $atributes 	=  $this->get_field_attribute_string( $args, array("checked" ) );
 		$fieldtype = ' fieldtype="checkbox"';
 		$formid = ' formid="'.esc_attr($this->id).'"';

        $html  = '<input type="hidden" name="'.esc_attr($name).'" value="'.esc_attr($off_value).'" />';
        $html  .= '<input type="checkbox" class="'.esc_attr($class).'" id="'.esc_attr($id).'" name="'.esc_attr($name).'" value="'.esc_attr($on_value).'" offvalue="'.esc_attr($off_value).'" '.$formid.$fieldtype.$checked.$readonly.$disabled.$atributes. ' />';
      	$html .= $this->get_unit_text( $args );
        $html .= $this->get_help_icon( $args );
        $html .= $this->get_after_html( $args);
        if( $ugp != "horizontal" )
    	{
    		$html    .= '<label for="'.esc_attr($id ).'"> '  . $this->get_field_description( $args ) . '</label>';
    	}
       
        echo $html;
    }

    /**
     * Renders a multicheckbox type field
     *
     * @param array $args field settings args
     */
    public function callback_multicheck( $args ){

        $ugp 		= $this->get_used_in_group_type( $args );
    	$id 		= isset($args['id']) ? $this->sanitize_attr( $args['id'] ) : ""; 
    	if( $id == "" ){ return "";}
    	$name = $id;

    	$class 		= "wpafb-field wpafb-field-multicheck wpafb-multicheck wpafb-multicheck-".$id;
		$class 		= isset($attrs['class']) ? $class." ".$this->sanitize_attr( $attrs['class'] ) : $class;
		$item_class = "wpafb-field-multicheck-item wpafb-multicheck-item";
		$item_class = isset($attrs['item_class']) ? $item_class." ".$this->sanitize_attr( $attrs['item_class'] ) : $item_class;
		$value 		= $this->get_field_value( $args );
		if( !is_array($value) )
		{
			$value = array();
		}
		
		$on_off_values 	= ( isset( $args['on_off_values'] ) && is_array( $args['on_off_values'] ) && count( $args['on_off_values'] ) == 2 ) ? $args['on_off_values'] : array( "1", "0" );
		$on_value = $on_off_values[0];
		$off_value= $on_off_values[1];

        $atributes 	=  $this->get_field_attribute_string( $args, array("checked" ) );
 		$fieldtype = ' fieldtype="multicheck"';
 		$formid = ' formid="'.esc_attr($this->id).'"';

 		$items_direction = isset( $args['items_direction'] ) ? trim($args['items_direction']) : "horizontal"; // another value is 'vertical'

        $options 		= isset( $args['options'] ) && is_array($args['options']) ? $args['options'] : array();
       
        $disabled_opt 	= isset($args['disabled']) ? $args['disabled'] : false; //can be an array of item keys
    	if( !is_array($disabled_opt) && $disabled_opt !== true && $disabled_opt !== false ){ $disabled_opt = false;}
 		if( empty($disabled_opt)) { $disabled_opt = false; }
 		if( $disabled_opt === true )
 		{
 			$disabled_opt = array_keys( $options ); //add all items in disabled list
 		}

        $help_icon = $this->get_help_icon( $args );
        $fieldset 	= isset($args['fieldset']) && is_array($args['fieldset'])?  $args['fieldset'] : false;
        $html = "";
        $html .='<span id="'.esc_attr($id).'" class="'.esc_attr($class).'" '.$formid.$fieldtype.'>';
        if( $fieldset )
        {
        	$fieldset_id = ( $id != "" )? $id."_fieldset" : "";
        	$html  .= '<fieldset id="'.esc_attr( $fieldset_id ).'">';
        	$legend 		= isset($fieldset['legend']) ? $fieldset['legend'] : "";
        	$html  .= '<legend>'.$legend.$help_icon.'</legend>';
        }
        

        foreach ( $options as $key => $label )
        {
            $val = isset( $value[$key] ) ? $value[$key] : $off_value;
            $checked = checked( $val, $on_value, false );

            $item_name = $name.'['.$key.']';
            $item_id = $id.'_'.$key;
            $disabled = ( $disabled_opt && in_array($key, $disabled_opt ) ) ? ' disabled="disabled"' : '';
            $label_class = ( $disabled !='') ? 'label-multicheck disabled' : 'label-multicheck';

            $html  .= '<label for="'.esc_attr( $item_id ).'" class="'.esc_attr( $label_class).'">';
            $html  .= '<input type="hidden" name="'.esc_attr($item_name).'" value="'.esc_attr($off_value).'" />';
        	$html  .= '<input type="checkbox" class="'.esc_attr($item_class).'" id="'.esc_attr($item_id).'" fieldid="'.esc_attr($id).'" item-key="'.esc_attr($key).'" name="'.esc_attr($item_name).'" value="'.esc_attr($on_value).'" offvalue="'.esc_attr($off_value).'" '.$checked.$disabled.$atributes. ' />';
            $html  .= $label.'</label>';
            if( $items_direction == 'horizontal' ){ $html.= " "; }else{ $html.= "<br>"; }
        }
		
        if( $fieldset )
        {
        	$html .= '</fieldset>';
    	}
    	$html .='</span>';
        $html .= $this->get_unit_text( $args );
        if( !$fieldset )
    	{
    		$html.=$help_icon;
    	}
        $html .= $this->get_after_html( $args);
        $html .= $this->get_field_description( $args );

        echo $html;
    }

    /**
     * Renders a radio type settings field
     *
     * @param array  $args field settings args
     */
    public function callback_radio( $args ){

        $ugp 		= $this->get_used_in_group_type( $args );
    	$id 		= isset($args['id']) ? $this->sanitize_attr( $args['id'] ) : ""; 
    	if( $id == "" ){ return "";}
    	$name = $id;
		$class 		= "wpafb-field wpafb-field-radio wpafb-radio wpafb-radio-".$id;
		$class 		= isset($attrs['class']) ? $class." ".$this->sanitize_attr( $attrs['class'] ) : $class;
		$item_class = "wpafb-field-radio-item wpafb-radio-item radio";
		$item_class = isset($attrs['item_class']) ? $item_class." ".$this->sanitize_attr( $attrs['item_class'] ) : $item_class;

		$value 		= $this->get_field_value( $args );

		$atributes 	=  $this->get_field_attribute_string( $args, array("checked" ) );
		$fieldtype = ' fieldtype="radio"';
		$formid = ' formid="'.esc_attr($this->id).'"';
        $items_direction = isset( $args['items_direction'] ) ? trim($args['items_direction']) : "horizontal"; // another value is 'vertical'

        $options 	= isset( $args['options'] ) && is_array($args['options']) ? $args['options'] : array();

        $disabled_opt 	= isset($args['disabled']) ? $args['disabled'] : false; //can be an array of item keys
    	if( !is_array($disabled_opt) && $disabled_opt !== true && $disabled_opt !== false ){ $disabled_opt = false;}
 		if( empty($disabled_opt)) { $disabled_opt = false; }
 		if( $disabled_opt === true )
 		{
 			$disabled_opt = array_keys( $options ); //add all items in disabled list
 		}


        $help_icon = $this->get_help_icon( $args );
        $fieldset 	= isset($args['fieldset']) && is_array($args['fieldset'])?  $args['fieldset'] : false;

        $html = "";
        $html .='<span id="'.esc_attr($id).'" class="'.esc_attr($class).'" '.$formid.$fieldtype.'>';
        if( $fieldset )
        {
        	$fieldset_id = ( $id != "" )? $id."_fieldset" : "";
        	$html  .= '<fieldset id="'.esc_attr( $fieldset_id ).'">';
        	$legend 		= isset($fieldset['legend']) ? $fieldset['legend'] : "";
        	$html  .= '<legend>'.$legend.$help_icon.'</legend>';
        }

        foreach ( $options as $key => $label )
        {
            $checked = checked( $value, $key, false );
            $disabled = ( $disabled_opt && in_array($key, $disabled_opt ) ) ? ' disabled="disabled"' : '';
            $label_class = ( $disabled !='') ? 'label-radio disabled' : 'label-radio';
            
            $html .= '<label for="'.esc_attr( $id.'_'.$key ).'" class="'.esc_attr( $label_class).'">';
            $html .= '<input type="radio" class="'.esc_attr($item_class).'" id="'.esc_attr( $id.'_'.$key ).'" fieldid="'.esc_attr($id).'" name="'.esc_attr( $name ).'" value="'.esc_attr( $key ).'" '.$checked.$disabled.$atributes.' />';
            $html .= $label.'</label>';
            if( $items_direction == 'horizontal' ){ $html.= " "; }else{ $html.= "<br>"; }
        }
        if( $fieldset )
        {
        	$html .= '</fieldset>';
    	}
    	$html .='</span>';
    	$html .= $this->get_unit_text( $args );
    	if( !$fieldset )
    	{
    		$html.=$help_icon;
    	}
        $html .= $this->get_after_html( $args);
        $html .= $this->get_field_description( $args );

        echo $html;
    }

    /**
     * Renders a selectbox type field
     *
     * @param array $args field settings args
     */
    public function callback_select( $args ){

        $ugp 		= $this->get_used_in_group_type( $args );
    	$id 		= isset($args['id']) ? $this->sanitize_attr( $args['id'] ) : ""; 
    	if( $id == "" ){ return "";}
    	$name = $id;
    	$required 	= isset($args['required']) && $args['required'] === true ? ' required="required"' : '';
    	$class 		= "wpafb-field wpafb-field-select wpafb-select";
		$class 		= isset($attrs['class']) ? $class." ".$this->sanitize_attr( $attrs['class'] ) : $class;
		$value 		= $this->get_field_value( $args );
		
        $atributes 	=  $this->get_field_attribute_string( $args );
 		$fieldtype = ' fieldtype="select"';
 		$formid = ' formid="'.esc_attr($this->id).'"';

        $options 		= isset( $args['options'] ) && is_array($args['options']) ? $args['options'] : array();
       
        $disabled_opt 	= isset($args['disabled']) ? $args['disabled'] : false; //can be an array of item keys
    	if( !is_array($disabled_opt) && $disabled_opt !== true && $disabled_opt !== false ){ $disabled_opt = false;}
 		if( empty($disabled_opt)) { $disabled_opt = false; }
 		$disabled = ( $disabled_opt === true ) ? ' disabled="disabled"' : '';
 		if( $disabled_opt === true )
 		{
 			$disabled_opt = array_keys( $options ); //add all items in disabled list
 		}

       

        $class  .= isset( $args['size'] ) && !is_null( $args['size'] ) ? ' '.$args['size'] : ' regular';
        
        $html  = '<select class="'.esc_attr( $class ).'" name="'.esc_attr( $name ).'" id="'.esc_attr( $id ).'"'.$formid.$fieldtype.$disabled.$required.$atributes.'>';
        


        foreach ( $args['options'] as $key => $val )
        {
            //if $val is an array then consider that as an optiongroup, and loop through the options key again to get more options. 
            //optiongroup array must contaion two keys ( label and options), options key contains array of key and name
            if( is_array($val ) )
            {
            	$optgroup_label = isset( $val['label'] ) ? $val['label'] : '';
            	$opts = ( isset( $val['options'] ) && is_array($val['options'] ) )  ? $val['options'] : array();
            	$html .= '<optgroup label="'.esc_attr($optgroup_label).'">';
            	foreach( $opts as $k => $v )
            	{
            		$label =  $v;
		            $selected = selected( $value, $k, false );
		            $disabled_item = ( is_array($disabled_opt) && in_array($k, $disabled_opt ) ) ? ' disabled="disabled"' : '';
		            $html .= '<option value="'.$k.'" '.$selected.$disabled_item.'>'.$label.'</option>';
            	}
            	$html .= '</optgroup>';
            }
            else
            {
            	$label =  $val;
	            $selected = selected( $value, $key, false );
	            $disabled_item = ( is_array($disabled_opt) && in_array($key, $disabled_opt ) ) ? ' disabled="disabled"' : '';
	            $html .= '<option value="'.$key.'" '.$selected.$disabled_item.'>'.$label.'</option>';
            }
        }

        $html .= '</select>';
        $html .= $this->get_unit_text( $args );
        $html .= $this->get_help_icon( $args );
        $html .= $this->get_after_html( $args);
        if( $ugp != "horizontal" )
    	{
        	$html .= $this->get_field_description( $args );
    	}

        echo $html;
    }

    /**
     * Renders a textarea type field
     *
     * @param array $args field settings args
     */
    public function callback_textarea( $args ){

    	$ugp 		= $this->get_used_in_group_type( $args );
    	$id 		= isset($args['id']) ? $this->sanitize_attr( $args['id'] ) : ""; 
    	if( $id == "" ){ return "";}
    	$name = $id;
    	$readonly 	= isset($args['readonly']) && $args['readonly'] === true ? ' readonly="readonly"' : '';
    	$disabled 	= isset($args['disabled']) && $args['disabled'] === true ? ' disabled="disabled"' : '';
        $placeholder= isset( $args['placeholder'] ) ? ' placeholder="'.esc_attr( $args['placeholder'] ).'"': '';
        $required 	= isset($args['required']) && $args['required'] === true ? ' required="required"' : '';
    	$size     	= ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $this->sanitize_attr( $args['size'] ) : 'medium';
    	if( $size == 'tiny' ){ $size = 'small'; }
    	if( $size == 'regular' ){ $size = 'medium'; }
    	$row     	= ( isset( $args['row'] ) ) ? $this->sanitize_attr( $args['row'] ) : '5';

    	$class 		= "wpafb-field wpafb-field-textarea wpafb-textarea";
    	$class 		= ( $size != "") ? $class." wpafb-textarea-".$size : $class;
    	if( $this->field_has_input_error( $args ) )
    	{
    		$class = $class.' '.$this->input_error_class_name;
    	}
		$class 		= isset($attrs['class']) ? $class." ".$this->sanitize_attr( $attrs['class'] ) : $class;
		$value 		= $this->get_field_value( $args );
		$atributes 	=  $this->get_field_attribute_string( $args , array('row') );
       	$fieldtype = ' fieldtype="textarea"';
       	$formid = ' formid="'.esc_attr($this->id).'"';

        $html  = '<textarea rows="5" cols="55" class="'.esc_attr( $class ).'" id="'.esc_attr( $id ).'" name="'.esc_attr( $name ).'" ' . $formid.$fieldtype.$readonly.$placeholder.$disabled.$required.$atributes . '>'.esc_textarea( $value ).'</textarea>';
        $html .= $this->get_help_icon( $args );
        $html .= $this->get_after_html( $args);
        $html  .= $this->get_field_description( $args );

        echo $html;
    }

     /**
     * Print custom HTML in a row
     * If label option is set then it will create two column and print custom HTML on right column.
     * @param array $args field settings args
     */
    public function callback_html( $args ){

        echo isset( $args['value'] ) ? $args['value'] : "";
    }

    /**
     * Renders a wysiwyg editor
     *
     * @param array  $args field settings args
     */
    public function callback_editor( $args ){

    	$ugp 		= $this->get_used_in_group_type( $args );
        //HTML id attribute value for the textarea and TinyMCE. May only contain lowercase letters and underscores. Hyphens will cause the editor to display improperly. 
    	$id 		= isset($args['id']) ? str_replace("-", "_", $this->sanitize_attr( $args['id'] ) ) : ""; 
    	if( $id == "" ){ return "";}
    	$name = $id;
    	$size     	= ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $this->sanitize_attr( $args['size'] ) : 'large';
    	if( $size == 'tiny' ){ $size = 'small'; }
    	if( $size == 'regular' ){ $size = 'medium'; }

    	$class 		= "wpafb-editor";
    	$class 		= ( $size != "") ? $class." wpafb-editor-".$size : $class;
    	if( $this->field_has_input_error( $args ) )
    	{
    		$class = $class.' '.$this->input_error_class_name;
    	}
		$class 		= isset($attrs['class']) ? $class." ".$this->sanitize_attr( $attrs['class'] ) : $class;

		$editor_class = "wpafb-field wpafb-field-editor";

		$formid = ' formid="'.esc_attr($this->id).'"';

		$value 		= $this->get_field_value( $args );
		

        echo '<div id="' . esc_attr( $id."_wrapper" ) . '" class="' . esc_attr( $class ) . '"'.$formid.'>';

        $editor_settings = array(
            'teeny'         => false,
            'textarea_name' => $name,
            'textarea_rows' => 10
        );
        

        if ( isset( $args['settings'] ) && is_array( $args['settings'] ) )
        {
            $editor_settings = wp_parse_args( $args['settings'], $editor_settings );
        }
        $editor_settings['editor_class'] = isset($editor_settings['editor_class'] ) ? $editor_settings['editor_class']." ".$editor_class : $editor_class;

        if( $size == 'large' && !isset( $editor_settings['editor_height'] ) )
        {
        	$editor_settings['editor_height'] = '300px';
        }

        wp_editor( $value, $id, $editor_settings );

        echo '</div>';
        echo '<div style="margin-top:10px;">';
        	echo $this->get_help_icon( $args );
        	echo $this->get_after_html( $args);
        echo '</div>';
        echo $this->get_field_description( $args );
    }

    /**
     * Render a file upload field 
     *
     * @param array  $args field settings args
     */
    public function callback_file( $args , $image = false ){

        $ugp 		= $this->get_used_in_group_type( $args );
    	$id 		= isset($args['id']) ? $this->sanitize_attr( $args['id'] ) : ""; 
    	if( $id == "" ){ return "";}
    	$name = $id;
    	$readonly 	= isset($args['readonly']) && $args['readonly'] === true ? ' readonly="readonly"' : '';
    	$disabled 	= isset($args['disabled']) && $args['disabled'] === true ? ' disabled="disabled"' : '';
        $placeholder= isset( $args['placeholder'] ) ? ' placeholder="'.esc_attr( $args['placeholder'] ).'"': '';
        $required 	= isset($args['required']) && $args['required'] === true ? ' required="required"' : '';
    	$size     	= ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $this->sanitize_attr( $args['size'] ) : 'regular';
    	$class 		= "wpafb-field wpafb-text wpafb-url";
    	$class 		= ( $image ) ? $class." wpafb-field-image wpafb-image-url" : $class." wpafb-field-file";
    	$class 		= ( $size != "") ? $class." ".$size."-text" : $class;
    	if( $this->field_has_input_error( $args ) )
    	{
    		$class = $class.' '.$this->input_error_class_name;
    	}
		$class 		= isset($attrs['class']) ? $class." ".$this->sanitize_attr( $attrs['class'] ) : $class;
		$value 		= $this->get_field_value( $args );
		$atributes 	=  $this->get_field_attribute_string( $args ); 
		$fieldtype = ( $image == true ) ? ' fieldtype="image"' : ' fieldtype="file"';
		$formid = ' formid="'.esc_attr($this->id).'"';

        $default_button_text = ( $image ) ? 'Upload/Select image' : 'Choose File';
        $button_text = isset( $args['button_text'] ) ? $args['button_text'] : $default_button_text;
        $button_class = 'button wpafb-browse';
        $button_class .= isset( $args['button_class'] ) ? ' '.$this->sanitize_attr( $args['button_class'] ) : '';

        $html  = '<input type="text" class="'.esc_attr($class).'" id="'.esc_attr($id).'" name="'.esc_attr($name).'" value="'.esc_attr($value).'"' . $formid.$fieldtype.$readonly.$placeholder.$disabled.$required.$atributes . '/>';
        $html  .= '<input type="button" class="'.esc_attr($button_class).'" id="'.esc_attr($id.'_button').'" value="' . esc_attr( $button_text ) . '" />';
        
        $html .= $this->get_help_icon( $args );
        $html .= $this->get_after_html( $args);
        if( $image )
        {
        	
        	$image_class = ( $value != "") ? 'wpafb-preview-image' : 'wpafb-preview-image hidden';
        	$html .='<div class="wpafb-preview-image-holder">';
        		$html .='<img class="'.$image_class.'" src="'.esc_attr( $value ).'" >';
        	$html.= '</div>';
        }
        $html  .= $this->get_field_description( $args );

        echo $html;
    }

	/**
     * Render a image upload field 
     *
     * @param array $args field settings args
     */
    public function callback_image( $args ){

    	$this->callback_file( $args , true ); // ture for image type
    }

    /**
     * Renders a color picker type field
     *
     * @param array $args field settings args
     */
    public function callback_color( $args ){

    	$ugp 		= $this->get_used_in_group_type( $args );
    	$id 		= isset($args['id']) ? $this->sanitize_attr( $args['id'] ) : ""; 
    	if( $id == "" ){ return "";}
    	$name = $id;
    	$readonly 	= isset($args['readonly']) && $args['readonly'] === true ? ' readonly="readonly"' : '';
    	$disabled 	= isset($args['disabled']) && $args['disabled'] === true ? ' disabled="disabled"' : '';
        $placeholder= isset( $args['placeholder'] ) ? ' placeholder="'.esc_attr( $args['placeholder'] ).'"': '';
        $required 	= isset($args['required']) && $args['required'] === true ? ' required="required"' : '';
    	$size     	= ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $this->sanitize_attr( $args['size'] ) : 'regular';
    	$class 		= "wpafb-field wpafb-field-color wpafb-color-picker-field";
    	$class 		= ( $size != "") ? $class." ".$size."-text" : $class;
		$class 		= isset($attrs['class']) ? $class." ".$this->sanitize_attr( $attrs['class'] ) : $class;
		$value 		= $this->get_field_value( $args );
		$default = "#cccccc";
		$atributes 	=  $this->get_field_attribute_string( $args );
		$fieldtype = ' fieldtype="color"';
		$formid = ' formid="'.esc_attr($this->id).'"';

        $html = '<table class="wpafb-color-picker-table">';
        	$html .= '<tr>';
        		$html .= '<td>';
        			$html .= '<input type="text" class="'.esc_attr( $class ).'" id="'.esc_attr( $id ).'" name="'.esc_attr( $name ).'" value="'.esc_attr( $value ).'" data-default-color="'.esc_attr( $default ).'"' . $formid.$fieldtype.$readonly.$placeholder.$disabled.$required.$atributes . ' />';
        		$html .= '</td>';
        		$html .= '<td>';
        			$html .= $this->get_help_icon( $args );
        			$html .= $this->get_after_html( $args);
        		$html .= '</td>';
        	$html .= '</tr>';
        	$html .= '<tr>';
        		$html .= '<td colspan="2">';
        			$html .= $this->get_field_description( $args );
       		 	$html .= '</td>';
       		$html .= '</tr>';
       	$html .= '</table>';

        echo $html;
    }

	/**
     * Renders jQuery UI based custom slider
     *
     * @param array $args field settings args
     */
    public function callback_slider( $args ){

    	$ugp 		= $this->get_used_in_group_type( $args );
    	$id 		= isset($args['id']) ? $this->sanitize_attr( $args['id'] ) : ""; 
    	if( $id == "" ){ return "";}
    	$name = $id;
    	$disabled 	= isset($args['disabled']) && $args['disabled'] === true ? ' disabled="disabled"' : '';
    	$size     	= ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $this->sanitize_attr( $args['size'] ) : 'medium';
    	$class 		= "wpafb-slider";
    	$class 		= ( $size != "") ? $class." slider-".$size : $class;
		$class 		= isset($attrs['class']) ? $class." ".$this->sanitize_attr( $attrs['class'] ) : $class;
		$value 		= $this->get_field_value( $args );

		$min 		= isset( $args['min'] ) ? $args['min'] : 0;
		$max 		= isset( $args['max'] ) ? $args['max'] : 100;
		$step 		= isset( $args['step'] ) ? $args['step'] : 1;
		$display 	= isset( $args['display'] ) ? $args['display'] : true;
		$display_text = isset( $args['display_text'] ) ? $args['display_text'] : '';
		$buttons 	= isset( $args['buttons'] ) ? $args['buttons'] : true;

		$atributes 	=  $this->get_field_attribute_string( $args , array('main', 'max', 'step' ) );
		$fieldtype = ' fieldtype="slider"';
		$formid = ' formid="'.esc_attr($this->id).'"';

       	$html = '<span class="'.esc_attr( $class ).'" id="'.esc_attr( $id."_slider" ).'" min="'.esc_attr( $min ).'" max="'.esc_attr( $max ).'" step="'.esc_attr( $step ).'" '.$disabled.'>';
		  $html .= '<input type="hidden" id="'.esc_attr( $id ).'" class="wpafb-field wpafb-field-slider wpafb-slider-input" value="'.esc_attr( $value ).'" name="'.esc_attr( $name ).'" ' . $formid.$fieldtype.$disabled.$atributes . '/>';
		  $html .= '<table width="100%" border="0">';
		    $html .= '<tr>';
		      if( $buttons === true )
		      {
		      $html .= '<td class="pm-button-box">';
		          $html .= '<div class="pm-button minus-button ui-corner-all ui-state-default">-</div>';
		      $html .= '</td>';
		      }
		      $html .= '<td class="slider-box">';
		          $html .= '<div class="slider">';
		              $html .= '<div id="custom-handle" class="ui-slider-handle custom-handle"></div>';
		          $html .= '</div>';
		      $html .= '</td>';
		      if( $buttons === true )
		      {
		      $html .= '<td class="pm-button-box">';
		        $html .= '<div class="pm-button plus-button ui-corner-all ui-state-default">+</div>';
		      $html .= '</td>';
		      }
		      else
		      {
		      $html .= '<td class="blank-box">';
		        //Just a blank <td> to keep blank space after the slider
		      $html .= '</td>';
		      }
		      if( $display === true )
		      {
		      $html .= '<td class="display-box">';
		          $html .= '<input class="display " type="text" unit-text="'.esc_attr( $display_text ).'" readonly />';
		      $html .= '</td>';
		  	  }
		    $html .= '</tr>';
		    $html .= '</table>';
		$html .= '</span>';
		$html .= $this->get_unit_text( $args );
        $html .= $this->get_help_icon( $args );
        $html .= $this->get_after_html( $args);
        if( $ugp != "horizontal" )
    	{
        	$html .= $this->get_field_description( $args );
    	}


        echo $html;
    }//end function

    /**
     * Renders a radio with image type settings field
     *
     * @param array  $args field settings args
     */
    public function callback_radio_image( $args ){

        $ugp 		= $this->get_used_in_group_type( $args );

    	$id 		= isset($args['id']) ? $this->sanitize_attr( $args['id'] ) : ""; 
    	if( $id == "" ){ return "";}
    	$name = $id;
		$class 		= "wpafb-field wpafb-field-radio-image wpafb-radio-image wpafb-radio-image-".$id;
		$class 		= isset($attrs['class']) ? $class." ".$this->sanitize_attr( $attrs['class'] ) : $class;
		$item_class = "wpafb-field-radio-image-item wpafb-radio-image-item radio-image";
		$item_class = isset($attrs['item_class']) ? $item_class." ".$this->sanitize_attr( $attrs['item_class'] ) : $item_class;
		

		$value 		= $this->get_field_value( $args );

		$atributes 	=  $this->get_field_attribute_string( $args, array("checked" ) );
		$fieldtype = ' fieldtype="radio_image"';
		$formid = ' formid="'.esc_attr($this->id).'"';
        $items_direction = isset( $args['items_direction'] ) ? trim($args['items_direction']) : "horizontal"; // another value is 'vertical'

        $options 	= isset( $args['options'] ) && is_array($args['options']) ? $args['options'] : array();

        $disabled_opt 	= isset($args['disabled']) ? $args['disabled'] : false; //can be an array of item keys
    	if( !is_array($disabled_opt) && $disabled_opt !== true && $disabled_opt !== false ){ $disabled_opt = false;}
 		if( empty($disabled_opt)) { $disabled_opt = false; }
 		if( $disabled_opt === true )
 		{
 			$disabled_opt = array_keys( $options ); //add all items in disabled list
 		}


        $help_icon = $this->get_help_icon( $args );
        $fieldset 	= isset($args['fieldset']) && is_array($args['fieldset'])?  $args['fieldset'] : false;

        $html = "";
        $html .='<span id="'.esc_attr($id).'" class="'.esc_attr($class).'" '.$formid.$fieldtype.'>';
        if( $fieldset )
        {
        	$fieldset_id = ( $id != "" )? $id."_fieldset" : "";
        	$html  .= '<fieldset id="'.esc_attr( $fieldset_id ).'">';
        	$legend 		= isset($fieldset['legend']) ? $fieldset['legend'] : "";
        	$html  .= '<legend>'.$legend.$help_icon.'</legend>';
        }

        foreach ( $options as $key => $option )
        {
            $label = isset( $option['label'] ) ? $option['label'] : '';
            $url = isset( $option['url'] ) ? $option['url'] : '';
            
            $checked = checked( $value, $key, false );
            $disabled = ( $disabled_opt && in_array($key, $disabled_opt ) ) ? ' disabled="disabled"' : '';
            
            $item_box_class = 'wpafb-radio-image-item-box';
            if( $disabled ){ $item_box_class.=' disabled';}
            if( trim( $checked ) !='' ){ $item_box_class.=' selected';}
            
            $html .= '<label for="'.esc_attr( $id.'_'.$key ).'" class="'.esc_attr( $item_box_class).'">';
	            $html .= '<input type="radio" class="'.esc_attr($item_class).'" id="'.esc_attr( $id.'_'.$key ).'" fieldid="'.esc_attr($id).'" name="'.esc_attr( $name ).'" value="'.esc_attr( $key ).'" '.$checked.$disabled.$atributes.' />';
	            $html .= '<div class="image-box">';
	            	$html .= '<img src="'.esc_attr($url).'">';
	            $html .= '</div>';
	            
	            if( $label != "") { $html .= '<div class="label-text">'.$label.'</div>'; }
            $html .= '</label>';
            if( $items_direction == 'horizontal' ){ $html.= " "; }else{ $html.= "<br>"; }
        }
        $html .= '<div style="clear:left;"></div>';
        if( $fieldset )
        {
        	$html .= '</fieldset>';
    	}
    	$html .='</span>';
    	$html .= $this->get_unit_text( $args );
    	if( !$fieldset )
    	{
    		$html.=$help_icon;
    	}
        $html .= $this->get_after_html( $args);
        $html .= $this->get_field_description( $args );

        echo $html;
    }

    private function setup_fields( $fields ){

    	$this->fields = $this->add_fields( $fields );
    }

    //recursive function
    private function add_fields( $fields ){

    	$new_fields = array();
    	foreach( $fields as $key => $args )
    	{	
    		
    		$id = isset( $args['id'] ) ? trim( $args['id'] ) : "";
    		$type = isset( $args['type'] ) ? trim( $args['type'] ) : "";

    		if( $id == '' || $type == '' ){ continue; }
    		if( $id != $key ){ continue; }//array key and the id value must be same.
    		if( $type == 'group' )
    		{ 
    			if( !isset($args['group_type']) || !in_array($args['group_type'], array('row', 'horizontal', 'vertical')) ){ continue;}
    		}

    		if( $type == 'group' || $type == 'header' || $type == 'html' )
    		{
    			$args['real_field'] = false;
    		}
    		else
    		{
    			$args['real_field'] = isset( $args['real_field'] ) ? ( bool ) $args['real_field'] : true;
    		}
    		if( $args['real_field'] )
    		{
    			//set value from default value.
    			$args['value'] = isset( $args['default'] ) ? $args['default'] : "";
    		}
    		$args_short = array('type'=>$type ); //make the $this->fields nested array shorter in size to reduce memory usages.
    		
    		$this->field_list[$id] = $args;

    		if( $type == 'group' )
    		{
    			
    			if(isset( $args['fields'] ) && is_array( $args['fields'] ) )
    			{
    				$args_short['fields'] = $this->add_fields( $args['fields'] );
    			}
    			unset( $this->field_list[$id]['fields'] ); //remove nested fields detail from the group field in $this->field_list
    			$new_fields[$id] = $args_short;
    		}
    		else
    		{
    			$new_fields[$id] = $args_short;
    		}
    		


    	}
    	return $new_fields;

    }//end function
        
    public function get_field_list(){
    	return $this->field_list;
    }

    public function set_data( $data ){

    	if( !is_array( $data ) ) { return ; }
    	foreach( $this->field_list as $id => $field )
    	{
    		if( isset( $data[$id] ) )
    		{
    			$this->field_list[$id]['value'] =  $data[$id];
    		}
    		
    	}
    }

	public function get_submitted_data(){

    	return $this->$submitted_data;
    }

    public function prepare_submitted_data(){

    	foreach( $this->field_list as $id => $args )
    	{	if( $args['real_field'] )
    		{
    			$this->prepare_submitted_data_for_field( $args );
    		}
    		
    	}
    	return $this->submitted_data;
    }
    /**
     * get value from request and set to submitted_data
     * @param array . Field args
     */
    private function prepare_submitted_data_for_field( $args ){

    	$request_data = ( $this->request_method == 'post' ) ? $_POST : $_GET;
    	$id = $args['id'];
    	if( !isset($request_data[ $id ]) ){ return ; }
    	$value = $request_data[ $id ];
    	$data = $this->check_validation_and_do_correction( $value , $args );
    	if( isset( $data['error'] ) && $data['error'] )
    	{
    		$this->input_errors[ $id ] = $data['error'];
    	}
    	$this->submitted_data[$id] = $data['value'];
    }

    private function check_validation_and_do_correction( $value , $args ){

    	$data = array( "has_error" => false );
    	$id = $args['id'];
    	$field_type = $args['type'];
    	if( $field_type == 'text' || 
    		$field_type == 'email' || 
    		$field_type == 'password' || 
    		$field_type == 'url' || 
    		$field_type == 'select' || 
    		$field_type == 'radio' || 
    		$field_type == 'checkbox' || 
    		$field_type == 'textarea' || 
    		$field_type == 'editor' || 
    		$field_type == 'file' || 
    		$field_type == 'image' || 
    		$field_type == 'color' 

    	  )
    	{
    		$value = stripslashes ( trim($value) );
    	}
    	elseif( $field_type == 'slider' || $field_type == 'number' )
    	{
    		$value = intval ( trim($value) );
    	}
    	elseif( $field_type == 'multicheck' )
    	{
    		if( !is_array( $value ) )
    		{
    			$data['error'] = 'Wrong datatype was submitted for the multicheck';
    			$data['value'] = array(); //data has been corrected.
    			return $data;
    		}
    		else
    		{
	    		$new_value = array();
	    		foreach( $value as $key=>$val )
	    		{
	    			$new_value[$key] = stripslashes ( trim($val) );
	    		}
	    		$value = $new_value;
    		}

    	
    	}
    	$data['value'] = $value;

    	if( !empty( $this->validation_callback ) && is_callable( $this->validation_callback ) )
        {
            $data = call_user_func_array( $this->validation_callback , array( $data, $args, $this ) );
        }
        if( !empty( $args['validation_callback'] ) && is_callable( $args['validation_callback'] ) )
        {
            $data = call_user_func_array( $args['validation_callback'] , array( $data, $args, $this ) );
        }
        return $data;
    }

    public function get_value( $fieldid ){

    	return ( isset( $this->field_list[ $fieldid ] ) ) ? $this->field_list[ $fieldid ]['value'] : '';
    }

    public function set_hidden_field( $fieldid , $hidden = true ){

    	if( isset( $this->field_list[ $fieldid ] ) )
    	{ 
    		$this->field_list[ $fieldid ]['hidden'] = $hidden; 
    	}
    }
    
    //This function is not in use. Just a backup of thinking.
    private function get_value_from_request_when_html_name_array( $html_name , $id ){

		$request_data = ( $this->request_method == 'post' ) ? $_POST : $_GET;
		
		if( $html_name == "" ){ return null; }
		
		$html_name = str_replace("][", "|", $html_name );
		$html_name = str_replace("[", "|", $html_name );
		$html_name = str_replace("]", "", $html_name );
		$keys = explode("|", $html_name );
		$count = count($keys);
		if( !$count || $keys[0] == ""){ return null; }
		$value = null;
		$keys2 = $keys; //backup of keys for later usages
		$first = array_shift($keys);
		
		if( isset( $request_data[$first] ) )
		{
			$value = $request_data[$first];
		}
		else
		{
			return null;
		}
		foreach( $keys as $key )
		{	if( $key == ""){ return null;} // 'hello[]', 'hello[][test][]', 'hello[][test][]' types of name are not allowed
			if( isset( $value[$key] ) )
			{
				$value = $value[$key];
			}
			else
			{
				return null;
			}
		}

		return $value;
	}//end function 




}//end class
endif;

