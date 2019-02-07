<?php
	global $adguru_mp_theme_manager;
	$themes = $adguru_mp_theme_manager->get_themes();
	$builtin_theme_names = array();
	$custom_theme_names = array();
	$theme_id_list = array();
	foreach( $themes as $theme )
	{
		$theme_id_list[] = $theme->ID;
		if( isset( $theme->builtin ) && $theme->builtin == 1 )
		{
			$builtin_theme_names[ $theme->ID ] = $theme->name;
		}
		else
		{
			$custom_theme_names[ $theme->ID ] = $theme->name;
		}
	}
	$selected_theme_id = ( in_array( $ad->theme_id, $theme_id_list ) ) ? $ad->theme_id : $adguru_mp_theme_manager->default_theme_id;

?>
<div id="modal_popup_ad_editor_design_box" class="postbox">
	<h2 class='hndle'><span><?php _e('Design', 'adguru')?></span></h2>
	<div class="inside">
		<div>
			<table class="widefat" style="width:100%; margin-bottom:20px;">
				<tr>
					<td width="150"><strong><label><?php _e('Popup design source', 'adguru')?> : </label></strong></td>
					<td>
						<label for="design_source_theme"><input type="radio" id="design_source_theme" name="design_source" value="theme" <?php if($ad->design_source == 'theme'){ echo ' checked="checked"';}?> > <?php _e('Popup Theme', 'adguru')?></label> &nbsp;
						<label for="design_source_custom"><input type="radio" id="design_source_custom" name="design_source" value="custom" <?php if($ad->design_source == 'custom'){ echo ' checked="checked"';}?> > <?php _e('Custom Design', 'adguru')?></label>
					</td>
					<td>
						<div id="mp_editor_loading_box" class="hidden">Loading....</div>
					<td>
				</tr>
				<tr id="popup_theme_row" class="<?php if($ad->design_source == 'custom'){ echo 'hidden';}?>">
					<td><strong><label><?php _e('Popup Theme', 'adguru')?> :</label></strong></td>
					<td>
						<select name="theme_id" id="theme_id">
							<optgroup label="Builtin">
								<?php
									foreach( $builtin_theme_names as $id => $name )
									{
										$selected = ( $selected_theme_id == $id ) ? ' selected="selected"' : '';
										echo '<option value="'.esc_attr($id).'" '.$selected.'>'.esc_attr($name).'</option>';
									}
								?>
							</optgroup>
							<optgroup label="Custom">
								<?php
									foreach( $custom_theme_names as $id => $name )
									{
										$selected = ( $selected_theme_id == $id ) ? ' selected="selected"' : '';
										echo '<option value="'.esc_attr($id).'" '.$selected.'>'.esc_attr($name).'</option>';
									}
								?>
							</optgroup>
						</select>
						<a id="customize_theme_btn" class="hidden" style="font-size:14px; cursor:pointer;" onclick="javascript: return adguru_mp_customize_selected_theme()"><?php _e('Customize this theme', 'adguru');?></a>
					</td>
					
				</tr>
			</table>
		</div>
		<div id="popup_custom_design_form_wrap" class="<?php if($ad->design_source == 'theme'){ echo 'hidden';}?>">
			<h2 style="font-size:30px; text-align:center;border-bottom: 1px solid #efefef;"><?php _e('Custom design', 'adguru')?></h2>
			<?php adguru_show_modal_popup_design_form( $ad ); ?>
		</div>
	</div><!-- ./inside -->
</div><!-- /.postbox -->

<div id="modal_popup_ad_editor_sizing_box" class="postbox">
	<h2 class='hndle'><span><?php _e('Sizing', 'adguru')?></span></h2>
	<div class="inside">
		<?php adguru_show_modal_popup_sizing_form( $ad ); ?>
	</div><!-- ./inside -->
</div><!-- /.postbox -->

<div id="modal_popup_ad_editor_animation_box" class="postbox">
	<h2 class='hndle'><span><?php _e('Animation', 'adguru')?></span></h2>
	<div class="inside">
		<?php adguru_show_modal_popup_animation_form( $ad ); ?>
	</div><!-- ./inside -->
</div><!-- /.postbox -->

<div id="modal_popup_ad_editor_position_box" class="postbox">
	<h2 class='hndle'><span><?php _e('Position', 'adguru')?></span></h2>
	<div class="inside">
		<?php adguru_show_modal_popup_position_form( $ad ); ?>
	</div><!-- ./inside -->
</div><!-- /.postbox -->

<div id="modal_popup_ad_editor_triggering_box" class="postbox">
	<h2 class='hndle'><span><?php _e('Triggering', 'adguru')?></span></h2>
	<div class="inside">
		<?php adguru_show_modal_popup_triggering_form( $ad ); ?>
	</div><!-- ./inside -->
</div><!-- /.postbox -->

<div id="modal_popup_ad_editor_closing_box" class="postbox">
	<h2 class='hndle'><span><?php _e('Closing', 'adguru')?></span></h2>
	<div class="inside">
		<?php adguru_show_modal_popup_closing_form( $ad ); ?>
	</div><!-- ./inside -->
</div><!-- /.postbox -->

<div id="modal_popup_ad_editor_advanced_box" class="postbox">
	<h2 class='hndle'><span><?php _e('Other/Advanced Options', 'adguru')?></span></h2>
	<div class="inside">
		<?php adguru_show_modal_popup_other_form( $ad ); ?>
	</div><!-- ./inside -->
</div><!-- /.postbox -->
<script type="text/javascript">
	jQuery('input[type=radio][name=design_source]').change(function(){
	    if( jQuery(this).val() == 'theme')
	    {
	        jQuery("#popup_theme_row").removeClass('hidden');
	        jQuery("#popup_custom_design_form_wrap").addClass('hidden');
	    }
	    else if( jQuery(this).val() == 'custom')
	    {
	        jQuery("#popup_custom_design_form_wrap").removeClass('hidden');
	        jQuery("#popup_theme_row").addClass('hidden');
	    }
	});
</script>
