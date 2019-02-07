<?php
/**
 * AdGuru Admin Notice Class
 * Manage Admin Notices
 * @package     WP AD GURU
 * @since       2.0.0
 * @author oneTarek
 */

// Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'ADGURU_Admin_Notice' ) ) :

class ADGURU_Admin_Notice{
	
	public $notices = array();

	public function __construct(){

		add_action('admin_notices', array( $this, 'render_notices' ) );
	}

	/*
	 * Add new notice to the list
	 * @param sting $msg
	 * @param array $args
	 $args = array(
		"type"=>"success", 			|possible values ( error, warning, success and info) 
		"class"=>"", 				| any additional CSS class names
		"dismissible"=>true/false 	| To apply a closing icon. 
	 )
	 */
	public function add( $msg , $args ){

		$this->notices[] = array( "msg"=> $msg, "args"=>$args );
	}

	public function render_notices(){

		foreach( $this->notices as $notice )
		{
			$msg  = $notice['msg'];
			$args = $notice["args"];
			$this->render( $msg , $args );
		}
	}

	public function render( $msg , $args ){
		
		$class 	= "notice";
		$type 	= isset( $args['type'] ) ? $args['type'] : "error";
		$class.=" notice-".$type;
		if( isset( $args['dismissible'] ) && $args['dismissible'] )
		{
			$class.=" is-dismissible";
		}
		if( isset( $args['class'] ) && $args['class'] != "" )
		{
			$class.=" ".$args['class'];
		}
		echo '<div class="'.$class.'"><p>'.$msg.'</p></div>';
		
	}


}//end class

endif;