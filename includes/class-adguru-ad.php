<?php
/**
 * Single Ad Class
 * @author oneTarek
 * @since 2.0.0
 **/
 
// Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'ADGURU_Ad' ) ) :

class ADGURU_Ad{

	public $ID;
	public $name;
	public $description;
	public $type;
	public $post_type;
	public $meta = array();
		
	public function __construct( $type ){

		$this->type =  $type;
		$this->post_type = ADGURU_POST_TYPE_PREFIX.$type;
	}

    /**
     * Magic getter to get property directly form #meta.
     * All keys in meta start with underscore(_) , but access witout underscore
     * @param $prop
     *
     * @return mixed
     */
    public function __get( $prop ){

        if ( array_key_exists( "_".$prop, $this->meta ) ) 
		{
            return $this->meta[ "_".$prop ];
        }

        return $this->{$prop};
    }

    /**
     * Magic setter to set property directly to #meta.
     * All keys in meta start with underscore(_) , but access witout underscore
     * @param $prop
     *
     * @return mixed
     */
    public function __set( $prop , $value ){

        if ( property_exists( get_class( $this ) , $prop ) ) 
		{
            $this->{$prop} = $value;
        }
		else
		{
			$this->meta[ "_".$prop ] = $value;
		}
    }
		
    /**
     * Magic isset to check property directly in $meta
     * All keys in meta start with underscore(_) , but access witout underscore
     * @param $prop
     *
     * @return bool
     */
    public function __isset( $prop ){

        return isset( $this->{$prop} ) || isset( $this->meta[ "_".$prop ] );
    }	
	
	public function display( $ret = false ){

		ob_start();	
		do_action("adguru_ad_display_{$this->type}" , $this );
		$output = ob_get_clean();
		
		$output = apply_filters("adguru_{$this->type}_output", $output , $this );
		
		if( $ret )
		{ 
			return $output; 
		}
		else
		{
			echo $output;
		}
	}

	public function print_content( $args = array() )
	{	
		$args = is_array( $args ) ? $args : array();
		$ret = isset( $args['ret'] ) ? $args['ret'] : false;
		$output = "";
		$content_type = ( isset($this->content_type) ) ? $this->content_type : "";
		if( $content_type != "")
		{
			
			
			$wrap_attrs = ( isset( $args['wrapper_attributes'] ) && is_array( $args['wrapper_attributes'] ) ) ? $args['wrapper_attributes'] : array();
			$wrap_class = isset( $wrap_attrs['class'] ) ? $wrap_attrs['class'] : '';
			$wrap_attrs['class'] = 'adguru-content-'.$content_type.' '.$wrap_class;
			
			$output.= '<div ';
			foreach( $wrap_attrs as $key => $value )
			{
				$output.= $key.'="'.esc_attr( $value ).'" ';
			}
			$output.= '>';
			ob_start();
			do_action("adguru_content_print_{$content_type}" , $this );
			$output.= ob_get_clean();
			$output = apply_filters("adguru_content_output_{$content_type}", $output , $this );
			$output.='</div>';
		}	
		
		if( $ret )
		{ 
			return $output; 
		}
		else
		{
			echo $output;
		}
	}

	
}//end class

endif;