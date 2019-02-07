<?php
/**
 * ADGURU User Class
 * A class to manage user and permisison
 *
 * @package WP AD GURU
 * @author oneTarek
 * @since 2.0.0
 */

// Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'ADGURU_User' ) ) :

class ADGURU_User{

	/**
	 * Constructor
	 *
	 * @param none
	 * @return void
	 * @since 2.0.0
	 **/
	public function __construct(){
		
			
	}//end func
	
	/**
	 * Check the permission of current user to do a certain task 
	 * @param string $action
	 **/ 
	public function is_permitted_to( $action ){
		
		//Check the $action name and return decision 
		//Have to work here in future.
		//now just check if user can manage_options  
		return current_user_can('manage_options');
	
	}	

}//end class

endif;