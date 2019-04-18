<?php
/**
 * Ad Guru Helper class to make some pieces of HTML. All methods of this class are static. 
 * Do not make instance of this class. 
 * Use all helper functions directly using class name like ADGURU_HTML_Elements::method_name()
 * @author oneTarek
 * @since 2.0.0
 **/

// Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'ADGURU_HTML_Elements' ) ) :

class ADGURU_HTML_Elements{

	public static function print_msg( $msg, $class = "adguru_help_msg" ){ 

		echo '<div class="'.$class.'">'.$msg.'</div>';
	}

	public static function get_country_list_select_input( $select = "", $attr = array() ){
		
		if( $select == "" ){ $select = "none"; }
		$country_list = ADGURU_Helper::get_country_list();
		$html = '<select '; 
		foreach( $attr as $key => $val ){ $html.= $key.'="'.$val.'" '; }
		$html.= ">";
		$i = 0;
		foreach( $country_list as $slug => $name )
		{	
			$i++;
			if( $i == 7 ){ $html.= '<optgroup label="- - - - - - - - - - - - - - - - - - - - - - - -"></optgroup>'; }
			if( $slug == $select ){ $sel = ' selected="selected"'; }else{ $sel = ''; }
			$html.= '<option value="'.$slug.'" '.$sel.'>'.$name.'</option>';
		}
		$html.= "</select>";
		echo $html;
	}	


}//end class

endif;