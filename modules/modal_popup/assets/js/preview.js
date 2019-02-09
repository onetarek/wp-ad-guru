/**
 * Modal Popup live preview
 * @author oneTarek
 * @since 2.0.0
 */
;

var ADGURU_MP_PREIVEW = {};

( function( $ ){
	ADGURU_MP_PREIVEW = {
		
	    config : {},
		paused : false,
		preview_css_array : {
			"overlay" : {},
			"container-wrap" : {},
			"container" : {},
			"content" : {},
			"close" : {},
			"close-wrap" : {},
		},
		
		init : function( params ){
			// set config
			var defaults = {
				'theme_editor' : true
			};
			params = (typeof params !== 'object') ? {} : params;
			this.config = $.extend(defaults, params);
			this.make_preview_box_sticky();
			this.add_events();
		},
		
		pause : function(){
			this.paused = true;
		},
		resume : function(){
			this.paused = false;
		},
		add_events : function(){
			var fields = ADGURU_MP_FIELDS.get_fields();
			for( var id in fields )
			{

				$(document).on('wpafb-field:change:'+id, function(event , args){
					var field_id = args['id'];
					var field_value = args['value'];
					var field_name = fields[field_id]['name'];
					var field_group = fields[field_id]['group'];
					if( args['type'] == 'radio_image' )
					{
						field_value = {'value':field_value, 'img_url' : args['img_url']};
					}
					ADGURU_MP_FIELDS.set_value( field_group , field_name , field_value );
					ADGURU_MP_PREIVEW.reload();
					
				});
				
			}

	       
		},//End function add_events
		
		make_preview_box_sticky : function(){
			
			var box = $('#adguru_mp_preview_box');
			var top = box.offset().top;
			var stop = top - 34;

			$(window).scroll(function() {
				if( $(window).width() < 851 )
				{
					box.removeClass('sticky');
					return;
				}
			    var cur_pos = $(window).scrollTop();
			    if( cur_pos > stop )
			    {	if(!box.hasClass('sticky'))
					{
			    		box.addClass('sticky');
			    	}
			    }
			    else
			    {
			    	box.removeClass('sticky');
			    }
			    
			});
			
		},

		show_preview_in_sidebar_view : function(){
			//set width of container wrap
			var preview_area_width = $("#adguru_mp_preview_area").width();
			var width = preview_area_width - 40;
			$("#adguru_modal_popup_container_wrap_example").css('width', width+'px');
			$("#adguru_modal_popup_container_wrap_example").css('margin-left', '20px');

			$("#adguru_modal_popup_example").removeClass('hidden').removeClass('full_view').addClass('sidebar_view');
			$("#adguru_mp_preview_full_view_close_btn_wrap").addClass('hidden');
		},
		
		show_preview_in_full_view : function(){
			//set width of container wrap
			$("#adguru_modal_popup_container_wrap_example").css('width','100%');
			$("#adguru_modal_popup_container_wrap_example").css('margin-left', '0px');
			$("#adguru_modal_popup_example").removeClass('hidden').removeClass('sidebar_view').addClass('full_view');
			$("#adguru_mp_preview_full_view_close_btn_wrap").removeClass('hidden');
		},

		reload : function(){
			if( this.paused ){ return false; }
			this.set_close_button_html();
			this.prepare_css_array_for_preview();
			this.apply_css_to_preview();
			this.show_preview_in_sidebar_view();
		},//end function reload
		
		set_close_button_html : function(){

			var design = ADGURU_MP_FIELDS.get_value_by_group('design');
			var close_btn_content = "X";
			var close_button_type = ( typeof design['close_button_type'] !== 'undefined') ? design['close_button_type'] : 'text';
			if( close_button_type == 'text' )
			{
				var close_text = ( typeof design['close_text'] !== 'undefined' ) ? design['close_text'] : 'X';
				close_btn_content = close_text;
			}
			else
			{
				var close_image_source_type = ( typeof design['close_image_source_type'] !== 'undefined' ) ? design['close_image_source_type'] : 'builtin';
				if( close_image_source_type == 'builtin' )
				{
					if( typeof  design['close_image_name'] !== 'undefined' )
					{
						var close_image_url = design['close_image_name'].img_url;
					}
					
				}
				else
				{
					close_image_url = ( typeof design['close_custom_image_url'] !== 'undefined' ) ? design['close_custom_image_url'] : '#';
				}
				
				close_btn_content = '<img src="'+close_image_url+'" alt = "X" />';
				
			}
			$("#adguru_modal_popup_close_example").html( close_btn_content );
		},

		create_style_str : function( rules ){
			var str = "";
			for( var i in rules )
			{
				str = str+i+':'+rules[i]+';';
			}
			return str;
		},

		apply_css_to_preview : function(){
			var overlay = $("#adguru_modal_popup_overlay_example");
			var container_wrap = $("#adguru_modal_popup_container_wrap_example");
			var container = $("#adguru_modal_popup_conatiner_example");
			var content = $("#adguru_modal_popup_content_example");
			var close = $("#adguru_modal_popup_close_example");
			var close_wrap = $("#adguru_modal_popup_close_wrap_example");

			var overlay_style = this.create_style_str( this.preview_css_array['overlay'] );
			var container_wrap_style = this.create_style_str( this.preview_css_array['container-wrap'] );
			var container_style = this.create_style_str( this.preview_css_array['container'] );
			var content_style = this.create_style_str( this.preview_css_array['content'] );
			var close_style = this.create_style_str( this.preview_css_array['close'] );
			var close_wrap_style = this.create_style_str( this.preview_css_array['close-wrap'] );

			overlay.removeAttr('style').attr('style', overlay_style );
			container_wrap.removeAttr('style').attr('style', container_wrap_style );
			container.removeAttr('style').attr('style', container_style );
			content.removeAttr('style').attr('style', content_style );
			close.removeAttr('style').attr('style', close_style );
			close_wrap.removeAttr('style').attr('style', close_wrap_style );
		},

		prepare_css_array_for_preview : function(){
			//set blank rules object
			var rules = {};
			//set blank group object in rules
			for( var i in this.preview_css_array )
			{
				rules[i] = {};
			}

			var design = ADGURU_MP_FIELDS.get_value_by_group('design');

			var overlay_z_index = 99995;
			var container_z_index = overlay_z_index + 1;
			var close_location = ( typeof  design['close_location'] !== 'undefined' ) ? design['close_location'] : 'top_right';
			var close_location_class = close_location.replace('_', '-' );
			$("#adguru_modal_popup_close_wrap_example").removeClass();
			$("#adguru_modal_popup_close_wrap_example").addClass('mp-close-wrap '+close_location_class);


			//OVERLAY
			var hex_color = ( typeof design['overlay_background_color'] !== 'undefined' ) ? design['overlay_background_color'] : "#444444";
			var opacity = ( typeof  design['overlay_background_opacity'] !== 'undefined' ) ? design['overlay_background_opacity'] : '75';
			rules['overlay']['background-color'] = ADGURU_ADMIN_HELPER.hex_to_rgba( hex_color , opacity/100 );
			rules['overlay']['z-index'] = overlay_z_index;

			//CONTAINER
			rules['container-wrap']['z-index'] = container_z_index;

			var container_border_width = 0;
			if( typeof design['container_border_enable'] !== 'undefined' && design['container_border_enable'] == 1 )
			{	
			    var w = ( typeof  design['container_border_width'] !== 'undefined' ) ? design['container_border_width']+"px" : '5px';
			    var s = ( typeof  design['container_border_style'] !== 'undefined' ) ? design['container_border_style'] : 'solid';
			    var c = ( typeof  design['container_border_color'] !== 'undefined' ) ? design['container_border_color'] : '#cccccc';
			    rules['container']['border'] = w+' '+s+' '+c;
			    rules['container']['border-radius'] = ( typeof  design['container_border_radius'] !== 'undefined' ) ? design['container_border_radius']+"px" : '0';
			    container_border_width = w;
			}
			var container_padding = ( typeof  design['container_padding'] !== 'undefined' ) ? design['container_padding'] : 0;
			rules['container']['padding'] = container_padding+'px';

			if( typeof design['container_background_enable'] !== 'undefined' && design['container_background_enable'] == 1 )
			{
			    var hex_color = ( typeof design['container_background_color'] !== 'undefined' ) ? design['container_background_color'] : "#ffffff";
			    var opacity = ( typeof  design['container_background_opacity'] !== 'undefined' ) ? design['container_background_opacity'] : '100';
			    rules['container']['background-color'] = ADGURU_ADMIN_HELPER.hex_to_rgba( hex_color , opacity/100 );
			}

			if( typeof design['container_box_shadow_enable'] !== 'undefined' && design['container_box_shadow_enable'] == 1 )
			{
			    var h_offset = ( typeof  design['container_box_shadow_h_offset'] !== 'undefined' ) ? design['container_box_shadow_h_offset']+"px" : '3px';
			    var v_offset = ( typeof  design['container_box_shadow_v_offset'] !== 'undefined' ) ? design['container_box_shadow_v_offset']+"px" : '3px';
			    var blur_radius = ( typeof  design['container_box_shadow_blur_radius'] !== 'undefined' ) ? design['container_box_shadow_blur_radius']+"px" : '3px';
			    var spread = ( typeof  design['container_box_shadow_spread'] !== 'undefined' ) ? design['container_box_shadow_spread']+"px" : '3px';

			    var hex_color = ( typeof  design['container_box_shadow_color'] !== 'undefined' ) ? design['container_box_shadow_color'] : '#444444';
			    var opacity = ( typeof  design['container_box_shadow_opacity'] !== 'undefined' ) ? design['container_box_shadow_opacity'] : '25';

			    var color = ADGURU_ADMIN_HELPER.hex_to_rgba( hex_color , opacity/100 );
			    var inset = ( typeof design['container_box_shadow_inset'] !== 'undefined' && design['container_box_shadow_inset'] == 'yes' ) ? ' inset' : '';

			    rules['container']['box-shadow'] = h_offset+' '+v_offset+' '+blur_radius+' '+spread+' '+color+inset;
			    rules['container']['-moz-box-shadow'] = rules['container']['box-shadow'];
			    rules['container']['-webkit-box-shadow'] = rules['container']['box-shadow'];
			}

			//CLOSE BUTTON
			rules['close']['height'] = ( typeof  design['close_height'] !== 'undefined' ) ? design['close_height']+"px" : '20px';
			rules['close']['width'] = ( typeof  design['close_width'] !== 'undefined' ) ? design['close_width']+"px" : '20px';
			rules['close']['padding'] = ( typeof  design['close_padding'] !== 'undefined' ) ? design['close_padding']+"px" : '2px';
			if( typeof design['close_border_enable'] !== 'undefined' && design['close_border_enable'] == 1 )
			{
			    var w = ( typeof  design['close_border_width'] !== 'undefined' ) ? design['close_border_width']+"px" : '3px';
			    var s = ( typeof  design['close_border_style'] !== 'undefined' ) ? design['close_border_style'] : 'solid';
			    var c = ( typeof  design['close_border_color'] !== 'undefined' ) ? design['close_border_color'] : '#cccccc';
			    rules['close']['border'] = w+' '+s+' '+c;
			    rules['close']['border-radius'] = ( typeof  design['close_border_radius'] !== 'undefined' ) ? design['close_border_radius']+"px" : '0';
			}
			rules['close']['text-align'] = 'center';
			var button_type = ( typeof  design['close_button_type'] !== 'undefined' ) ? design['close_button_type'] : 'text';
			if( button_type == 'text')
			{
			    rules['close']['color'] = ( typeof  design['close_color'] !== 'undefined' ) ? design['close_color'] : '#000000';
			    rules['close']['font-size'] = ( typeof  design['close_font_size'] !== 'undefined' ) ? design['close_font_size']+"px" : '16px';
			    rules['close']['line-height'] = ( typeof  design['close_line_height'] !== 'undefined' ) ? design['close_line_height']+"px" : '16px';

			    var font_name = ( typeof  design['close_font_family'] !== 'undefined' ) ? design['close_font_family'] : 'Arial';
			    
			    rules['close']['font-family'] = (font_name == 'use_from_theme' ) ? 'inherit': ADGURU_ADMIN_HELPER.get_font_family_with_fallback( font_name );
			    rules['close']['font-weight'] = ( typeof  design['close_font_weight'] !== 'undefined' ) ? design['close_font_weight'] : 'normal';
			    rules['close']['font-style'] = ( typeof  design['close_font_style'] !== 'undefined' ) ? design['close_font_style'] : 'normal';

			    if( typeof design['close_text_shadow_enable'] !== 'undefined' && design['close_text_shadow_enable'] == 1 )
			    {
			        var h_offset = ( typeof  design['close_text_shadow_h_offset'] !== 'undefined' ) ? design['close_text_shadow_h_offset']+"px" : '1px';
			        var v_offset = ( typeof  design['close_text_shadow_v_offset'] !== 'undefined' ) ? design['close_text_shadow_v_offset']+"px" : '1px';
			        var blur_radius = ( typeof  design['close_text_shadow_blur_radius'] !== 'undefined' ) ? design['close_text_shadow_blur_radius']+"px" : '1px';
			        var color = ( typeof  design['close_text_shadow_color'] !== 'undefined' ) ? design['close_text_shadow_color'] : '#444444';
			        rules['close']['text-shadow'] = h_offset+' '+v_offset+' '+blur_radius+' '+color;
			    }

			    rules['close']['line-height'] = ( typeof  design['close_line_height'] !== 'undefined' ) ? design['close_line_height']+"px" : '16px';

			}
			else
			{
			    //rule for button type image
			}

			if( typeof design['close_background_enable'] !== 'undefined' && design['close_background_enable'] == 1 )
			{
			    var hex_color = ( typeof design['close_background_color'] !== 'undefined' ) ? design['close_background_color'] : "#ffffff";
			    var opacity = ( typeof  design['close_background_opacity'] !== 'undefined' ) ? design['close_background_opacity'] : '100';
			    rules['close']['background-color'] = ADGURU_ADMIN_HELPER.hex_to_rgba( hex_color , opacity/100 );
			}


			if( typeof design['close_box_shadow_enable'] !== 'undefined' && design['close_box_shadow_enable'] == 1 )
			{
			    var h_offset = ( typeof  design['close_box_shadow_h_offset'] !== 'undefined' ) ? design['close_box_shadow_h_offset']+"px" : '3px';
			    var v_offset = ( typeof  design['close_box_shadow_v_offset'] !== 'undefined' ) ? design['close_box_shadow_v_offset']+"px" : '3px';
			    var blur_radius = ( typeof  design['close_box_shadow_blur_radius'] !== 'undefined' ) ? design['close_box_shadow_blur_radius']+"px" : '3px';
			    var spread = ( typeof  design['close_box_shadow_spread'] !== 'undefined' ) ? design['close_box_shadow_spread']+"px" : '3px';

			    var hex_color = ( typeof  design['close_box_shadow_color'] !== 'undefined' ) ? design['close_box_shadow_color'] : '#444444';
			    var opacity = ( typeof  design['close_box_shadow_opacity'] !== 'undefined' ) ? design['close_box_shadow_opacity'] : '25';

			    var color = ADGURU_ADMIN_HELPER.hex_to_rgba( hex_color , opacity/100 );
			    var inset = ( typeof design['close_box_shadow_inset'] !== 'undefined' && design['close_box_shadow_inset'] == 'yes' ) ? ' inset' : '';

			    rules['close']['box-shadow'] = h_offset+' '+v_offset+' '+blur_radius+' '+spread+' '+color+inset;
			    rules['close']['-moz-box-shadow'] = rules['container']['box-shadow'];
			    rules['close']['-webkit-box-shadow'] = rules['container']['box-shadow'];
			}

			
			if( close_location == 'top_left' )
			{
			    var close_top = ( typeof  design['close_top'] !== 'undefined' ) ? design['close_top'] : 0;
			    var close_left = ( typeof  design['close_left'] !== 'undefined' ) ? design['close_left'] : 0;

			    if( close_top < 0 && (container_border_width + close_top )< 0 ){ close_top_negative = container_border_width + close_top; }
			    if( close_left < 0 && (container_border_width + close_left )< 0 ){ close_left_negative = container_border_width + close_left; }

			    rules['close-wrap']['top'] = close_top+'px';
			    rules['close-wrap']['left'] = close_left+'px';
			    
			}
			else if( close_location == 'top_center' )
			{
			    var close_top = ( typeof  design['close_top'] !== 'undefined' ) ? design['close_top'] : 0;  

			    if( close_top < 0 && (container_border_width + close_top )< 0 ){ close_top_negative = container_border_width + close_top; }

			    rules['close-wrap']['top'] = close_top+'px';
			}
			else if( close_location == 'top_right' )
			{
			    var close_top = ( typeof  design['close_top'] !== 'undefined' ) ? design['close_top'] : 0;
			    var close_right = ( typeof  design['close_right'] !== 'undefined' ) ? design['close_right'] : 0;

			    if( close_top < 0 && (container_border_width + close_top )< 0 ){ close_top_negative = container_border_width + close_top; }
			    if( close_right < 0 && (container_border_width + close_right )< 0 ){ close_right_negative = container_border_width + close_right; }

			    rules['close-wrap']['top'] = close_top+'px';
			    rules['close-wrap']['right'] = close_right+'px';
			}
			else if( close_location == 'middle_left' )
			{
			    var close_left = ( typeof  design['close_left'] !== 'undefined' ) ? design['close_left'] : 0;
			    
			    if( close_left < 0 && (container_border_width + close_left )< 0 ){ close_left_negative = container_border_width + close_left; }

			    //close_left = close_left - container_border_width;
			    rules['close-wrap']['left'] = close_left+'px';
			}
			else if( close_location == 'middle_right' )
			{
			    var close_right = ( typeof  design['close_right'] !== 'undefined' ) ? design['close_right'] : 0;
			    
			    if( close_right < 0 && (container_border_width + close_right )< 0 ){ close_right_negative = container_border_width + close_right; }
			    
			    rules['close-wrap']['right'] = close_right+'px';
			}
			else if( close_location == 'bottom_left' )
			{
			    var close_left = ( typeof  design['close_left'] !== 'undefined' ) ? design['close_left'] : 0;
			    var close_bottom = ( typeof  design['close_bottom'] !== 'undefined' ) ? design['close_bottom'] : 0;

			    if( close_left < 0 && (container_border_width + close_left )< 0 ){ close_left_negative = container_border_width + close_left; }
			    if( close_bottom < 0 && (container_border_width + close_bottom )< 0 ){ close_bottom_negative = container_border_width + close_bottom; }

			    rules['close-wrap']['left'] = close_left+'px';
			    rules['close-wrap']['bottom'] = close_bottom+'px';

			}
			else if( close_location == 'bottom_center' )
			{
			    var close_bottom = ( typeof  design['close_bottom'] !== 'undefined' ) ? design['close_bottom'] : 0;

			    if( close_bottom < 0 && (container_border_width + close_bottom )< 0 ){ close_bottom_negative = container_border_width + close_bottom; }

			    rules['close-wrap']['bottom'] = close_bottom+'px';
			}
			else if( close_location == 'bottom_right' )
			{
			    var close_right = ( typeof  design['close_right'] !== 'undefined' ) ? design['close_right'] : 0;
			    var close_bottom = ( typeof  design['close_bottom'] !== 'undefined' ) ? design['close_bottom'] : 0;

			    if( close_right < 0 && (container_border_width + close_right )< 0 ){ close_right_negative = container_border_width + close_right; }
			    if( close_bottom < 0 && (container_border_width + close_bottom )< 0 ){ close_bottom_negative = container_border_width + close_bottom; }
			    
			    rules['close-wrap']['right'] = close_right+'px';
			    rules['close-wrap']['bottom'] = close_bottom+'px';
			}
			this.preview_css_array = rules;
			

		},//End function set_preview_css_array_for_popup

		
		

	};//end ADGURU_MP_PREIVEW
	
} )(jQuery);

function adguru_mp_show_preview_in_sidebar_view()
{
	ADGURU_MP_PREIVEW.show_preview_in_sidebar_view();
}
function adguru_mp_show_preview_in_full_view()
{
	ADGURU_MP_PREIVEW.show_preview_in_full_view();
}
