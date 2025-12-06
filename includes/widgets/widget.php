<?php
// Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;

add_action( 'widgets_init', 'adguru_widget_init' );


function adguru_widget_init()
{

	register_widget( 'ADGURU_Widget' );
}

class ADGURU_Widget extends WP_Widget{

	function __construct(){
		
		$widget_ops = array( 'classname' => 'adguru', 'description' => __('A widget that displays adguru ad zones ', 'wp-ad-guru') );
		
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'adguru-zone-widget' );
		
		parent::__construct( 'adguru-zone-widget', __('Ad Guru Zone', 'wp-ad-guru'), $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ){

		extract( $args );

		//Our variables from the widget settings.
		$title = apply_filters('widget_title', $instance['title'] );
		$zone_id = $instance['zone_id'];
		
		echo $before_widget; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		// Display the widget title 
		if ( $title )
		{
			echo $before_title . $title . $after_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		//Display the name 
		if ( $zone_id )
		{		
			adguru()->server->show_zone( $zone_id );
		}
		else
		{
			esc_html_e("No zone is selected for this widget. Go to your dashboard widgets page and select a zone for this adGuru widget" , "wp-ad-guru" );
		}
		echo $after_widget; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			
	}

	//Update the widget 
	 
	function update( $new_instance, $old_instance ){

		$instance = $old_instance;

		//Strip tags from title and name to remove HTML 
		$instance['title'] = wp_strip_all_tags( $new_instance['title'] );
		$instance['zone_id'] = intval( $new_instance['zone_id'] );
		
		return $instance;
	}

	
	function form( $instance ){

		global $wpdb;
		//Set up some default widget settings.
		$defaults = array( 'title' => __('Ads', 'wp-ad-guru'), 'zone_id' =>0 );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e('Title:', 'wp-ad-guru'); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'zone_id' ) ); ?>"><?php esc_html_e('Select a zone:', 'wp-ad-guru'); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'zone_id' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'zone_id' ) ); ?>" style="width:100%;" >
				<option value="0" selected="selected"><?php esc_html_e('Select a zone:', 'wp-ad-guru'); ?></option>
				<?php				
				$zones = adguru()->manager->get_active_zones();
				if( !$zones ){ $zones = array(); }
				foreach($zones as $zone)
				{
					echo '<option value="'.$zone->ID.'"'; if( $zone->ID == $instance['zone_id'] )echo ' selected="selected"'; echo '>'.$zone->name.'-'.$zone->width.'x'.$zone->height.'</option>';	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</select>
			
		</p>

	<?php
	}
}

