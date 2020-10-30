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
	
    private $display_instance_number = 0;
    private $html_id = '';
    private $wrapper_attrs = array();

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
	
	/**
     * Renders output this zone
     * @since 2.2.0
     * @return string or void
     */
		
	public function display( $ret = false ){

        $this->display_instance_number++;
        $this->html_id = 'adguru_zone_'.$this->ID.'_'.$this->display_instance_number;
        $this->wrapper_attrs = array();

        $server = adguru()->server;
		$need_wrapper = $this->wrapper_needed();
        

        ob_start(); 

        if( $need_wrapper )
        {
            $wrap_attrs = $this->get_wrapper_attrs();
            //print wrapper div
            echo '<div ';
            foreach( $wrap_attrs as $key => $attr )
            {
                echo ' '.sanitize_key($key).'="'.esc_attr( $attr ).'"';
            }
            echo ' >';
        }

        echo '<span id="'.$this->html_id.'" class="adguru-zone">';

        $links = $server->get_appropiate_ad_links( $this->ID );

        if( is_array( $links  ) )
        {
            $tot_slide = count( $links );
            if( $tot_slide == 0 )
            {
                #nothing to do
            }
            elseif( $tot_slide == 1 )
            {
                #show single ad
                $ad_id = intval( $server->get_ad_by_percentage_probability( $links[0] ) );
                echo $server->show_ad( $ad_id , true);
            }
            else
            {
                #show slider
                $ad_id_list = array();
                foreach( $links as $ad_set )
                {
                    $ad_id = intval( $server->get_ad_by_percentage_probability( $ad_set ) );          
                    if( $ad_id )
                    { 
                        $ad_id_list[] = $ad_id; 
                    }
                }
                
                $slider_html_id = "adguru_slider_".$this->ID."_".$this->display_instance_number;
                $args = array(
                    "slider_html_id"=> $slider_html_id,
                    "width"         => $this->width,
                    "height"        => $this->height,
                    "auto"          => 5000,
                    "vertical"      => false,
                    "pagination"    => false
                );

                $args = apply_filters( 'adguru_zone_slider_options', $args, $this->ID, $this->display_instance_number );

                echo '<ul id="'.$slider_html_id.'" class="adguru_ad_slider" style="width:'.$args['width'].';height:'.$args['height'].'" data-options="'.esc_attr( json_encode( $args ) ).'">';
                    foreach( $ad_id_list as $ad_id )
                    {
                        echo '<li style="width:'.$args['width'].';height:'.$args['height'].'">';
                        echo $server->show_ad( $ad_id, true );
                        echo '</li>';
                    }
                echo '</ul>';
                
                
            }#end if( $tot_slide==0)
        
            
        }#end if(is_array( $links ) )

        echo '</span>';
        if( $need_wrapper )
        {
            echo '</div>';//CLOSEING OF WRAPPER DIV
        }

        echo $this->get_visibility_style();
        
        $output = ob_get_clean();
        
        $output = apply_filters("adguru_zone_output", $output , $this );
        
        if( $ret )
        { 
            return $output; 
        }
        else
        {
            echo $output;
        }
	}

    /**
     * Generate CSS to show/hide zone based on visitbilty conditions
     * @since 2.4.0
     * @return string
     */
    private function get_visibility_style(){
        ob_start();
        if( isset( $this->visibility ) && isset( $this->visibility['show_on_screen_size'] ) && $this->visibility['show_on_screen_size'] == 'custom' )
        {
            $min_width = isset( $this->visibility['screen_min_width'] ) ? intval( $this->visibility['screen_min_width'] ) : 0;
            $max_width = isset( $this->visibility['screen_max_width'] ) ? intval( $this->visibility['screen_max_width'] ) : 0;

            if( $min_width || $max_width )
            {
                /*
                 * Hide the zone if the screen size is not in between min and max width.
                 * Apply opposite rule.
                 */
                $html_id = ( !empty( $this->wrapper_attrs['id'] ) ) ? $this->wrapper_attrs['id'] : $this->html_id;
                $rules = array();
                echo '<style type="text/css">';
                echo '@media ';
                if( $min_width )
                {
                    $rules[] = 'screen and (max-width: ' . ( $min_width - 1 ) . 'px)';
                }
                if( $max_width )
                {
                    $rules[] = 'screen and (min-width: ' . ($max_width + 1) . 'px)';
                }
                echo implode(', ', $rules );
                echo '{';
                echo '#' . $html_id . '{display:none;}';
                echo '}';
                echo '</style>';
            }
        }

        $output = ob_get_clean();
        return $output;
    }

    /**
     * Checks whether extra wrapper element is needed or not
     * @since 2.2.0
     * @return bool
     */
    private function wrapper_needed()
    {
        if ( isset( $this->design ) && is_array( $this->design ) && isset( $this->design['wrapper'] ) && $this->design['wrapper'] == 0 )
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * Get html attributes from zone wrapper DIV
     * @since 2.2.0
     * @return array
     */
    private function get_wrapper_attrs(){

        $wrapper_class = 'adguru-zone-wrap';
        
        $alignment = (isset( $this->design['alignment'] ) ) ? $this->design['alignment'] : 'center';
        switch ($alignment) {
            case 'left':
            {
               $wrapper_class = $wrapper_class. ' align_left'; 
               break; 
            }
            case 'center':
            {
               $wrapper_class = $wrapper_class. ' align_center'; 
               break; 
            }
            case 'right':
            {
               $wrapper_class = $wrapper_class. ' align_right'; 
               break; 
            }
            case 'float_left':
            {
               $wrapper_class = $wrapper_class. ' float_left'; 
               break; 
            }
            case 'float_right':
            {
               $wrapper_class = $wrapper_class. ' float_right'; 
               break; 
            }
        }//end switch

        $attrs = array(
            'id' => 'adguru_zone_wrap_'.$this->ID.'_'.$this->display_instance_number,
            'class' => $wrapper_class,
        );
        $this->wrapper_attrs = apply_filters('adguru_zone_wrapper_attrs', $attrs, $this );

        return $this->wrapper_attrs;
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
            $items = $this->inserter['page_types'];
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