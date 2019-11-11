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
     * @since 2.2.0
     * @return bool
     */
    public function auto_insert_enabled(){

        if( isset( $this->inserter ) && is_array( $this->inserter ) && isset( $this->inserter['enabled'] ) && $this->inserter['enabled'] == 1 )
        {
            return true;
        }
        return false;
    }

    /**
     * Gets place for automatic insertion
     * @since 2.2.0
     * @return string
     */
    public function get_auto_insert_place(){

        if( isset( $this->inserter ) && is_array( $this->inserter ) && isset( $this->inserter['place'] ) )
        {
            return $this->inserter['place'];
        }
        return '';
    }

    /**
     * Gets page types for automatic insertion
     * @since 2.2.0
     * @return array
     */
    public function get_auto_insert_page_types(){

        if( isset( $this->inserter ) && is_array( $this->inserter ) && isset( $this->inserter['page_types'] ) && is_array( $this->inserter['page_types'] ) )
        {
            $list = array();
            $items = $this->inserter['page_types'];//write_log($items);
            foreach( $items as $key=> $val )
            {
                if( intval( $val ) == 1 )
                {
                    $list[] = $key;
                } 
            }
            return $list;
        }
        return array();
    }

    /**
     * Gets post numbers in loop after which this zone should be inserted
     * @since 2.2.0
     * @return array
     */
    public function get_after_post_numbers(){
        $list = array();
        if( isset( $this->inserter ) && is_array( $this->inserter ) && isset( $this->inserter['after_post_numbers'] ) )
        {
            $str = trim( $this->inserter['after_post_numbers'] );
            if( $str != "")
            {
               $parts = explode(',', $str );
               foreach( $parts as $part )
               {
                    $num = intval( $part );
                    if( $num )
                    {
                        $list[] = $num;
                    }
               }
            }  
        }
        return $list;
    }

    /**
     * Gets post numbers in loop after which this zone should be inserted
     * @since 2.2.0
     * @return array
     */
    public function get_after_comment_numbers(){
        $list = array();
        if( isset( $this->inserter ) && is_array( $this->inserter ) && isset( $this->inserter['after_comment_numbers'] ) )
        {
            $str = trim( $this->inserter['after_comment_numbers'] );
            if( $str != "")
            {
               $parts = explode(',', $str );
               foreach( $parts as $part )
               {
                    $num = intval( $part );
                    if( $num )
                    {
                        $list[] = $num;
                    }
               }
            }  
        }
        return $list;
    }

    /**
     * Checks whether automatic insertion is possible on current page
     * @since 2.2.0
     * @return bool
     */
    public function is_auto_insert_possible( $current_page_info ){
        if( !$this->auto_insert_enabled() )
        {
            return false;
        }
        
        $page_types = $this->get_auto_insert_page_types();
        //write_log($page_types, $current_page_info);
        if( empty($page_types) )
        {
            return false;
        }

        $page_type = $current_page_info['page_type'];

        if( in_array( $page_type, array( 'author', 'category', 'tag', 'custom_taxonomy', 'date' ) ) && in_array('archive_any', $page_types ) )
        {
            return true;
        }

        switch($page_type)
        {
            case 'home' : 
            {
               if( in_array('home', $page_types ) )
                {
                    return true;
                } 
                break;
            }
            case 'search' : 
            {
               if( in_array('search', $page_types ) )
                {
                    return true;
                } 
                break;
            }
            case '404_not_found' : 
            {
               if( in_array('404_not_found', $page_types ) )
                {
                    return true;
                } 
                break;
            }
            case 'singular':
            {
                if( in_array('single_any', $page_types ) )
                {
                    return true;
                }
                elseif( in_array('single_'.$current_page_info['post_type'], $page_types ) )
                {
                    return true;
                }
                break;
            }
            case 'author' : 
            {
               if( in_array('archive_author', $page_types ) )
                {
                    return true;
                } 
                break;
            }
            case 'category':
            {
                if( in_array('archive_category', $page_types ) )
                {
                    return true;
                }
                break;
            }
            case 'tag':
            {
                if( in_array('archive_post_tag', $page_types ) )
                {
                    return true;
                }
                break;
            }
            case 'custom_taxonomy':
            {
                if( in_array('archive_'.$current_page_info['taxonomy'], $page_types ) )
                {
                    return true;
                }
                break;
            }
            case 'date':
            {
                if( in_array('archive_date', $page_types ) )
                {
                    return true;
                }
                break;
            }

        }//end switch

        return false;

    }

    /**
     * Checks whether automatic insertion is possible before current post in loop
     * @since 2.2.0
     * @return bool
     */
    public function is_auto_insert_possible_before_post( $current_post_number ){
        
        //check the value of insertsion after posts field
        $numbers = $this->get_after_post_numbers();
        if( in_array( $current_post_number-1, $numbers ) )
        {
            return true;
        }

        return false; 

    }

    /**
     * Checks whether automatic insertion is possible before current comment in loop
     * @since 2.2.0
     * @return bool
     */
    public function is_auto_insert_possible_before_comment( $current_comment_number ){
        
        //check the value of insertsion after comments field
        $numbers = $this->get_after_comment_numbers();
        if( in_array( $current_comment_number-1, $numbers ) )
        {
            return true;
        }

        return false; 

    }

}//end class

endif;