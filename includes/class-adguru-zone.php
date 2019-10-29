<?php
/**
 * Single Zone Class
 * @author oneTarek
 * @since 2.0.0
 **/
 
// Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'ADGURU_Zone' ) ) :

class ADGURU_Zone{

	public $ID;
	public $name;
	public $description;
	public $meta = array();
		
	public function __construct(){
		
	}

    /**
     * Magic getter to get property directly from #meta.
     * All keys in meta start with underscore(_) , but access witout underscore
     * @param $prop
     *
     * @return mixed
     */
    public function __get( $prop ){

        if( array_key_exists( "_".$prop, $this->meta ) ) 
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

        if( property_exists( get_class( $this ) , $prop ) ) 
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
	
	
		
	public function display(){
        
		echo "<pre>"; print_r($this); echo "</pre>";
	}

    
    /**
     * Checks whether automatic insertion is enabled or not
     * @return bool
     */
    public function auto_insert_enabled(){

        if( isset( $this->inserter ) && is_array( $this->inserter ) && $this->inserter['enabled'] == 1 )
        {
            return true;
        }
        return false;
    }
    
}//end class

endif;