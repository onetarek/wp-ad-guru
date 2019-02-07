/**
 * Modal Popup JS
 * @author oneTarek
 * @since 2.0.0
 */
;
var ADGURU_MP = null;

(function($){
	ADGURU_MP = {
		opened_popups : [],//list of popup ids those are currently opened.
		popups_data : [], //list of popups data objects
		pupups_html_backup : [], //List of html of closed popups , we can append a popup to the <body> for second trigger.
		init : function(){
			this.add_events();
			this.prepare_popups_data();
			this.prepare_to_open_popups();
		},

		add_events : function(){
			if( $('.adguru-modal-popup').length )
			{
				$(window).resize(function(){
			    	ADGURU_MP.resize_opened_popups();
				});
			}

			$(document).on('click', '.adguru-modal-popup-overlay', function(){
				var id = $(this).attr('popup-id');
				var popup = $("#adguru_modal_popup_"+id);
				var data = ADGURU_MP.get_popup_data( id );
				if( data == false ){ return ;}

				var closing = data.closing;
				if( typeof closing.close_on_overlay_click != 'undefined' && closing.close_on_overlay_click == 0 )
				{
					return;
				}
				ADGURU_MP.close_popup( id );
			});
			$(document).on('click', '.adguru-modal-popup-close', function(){
				var id = $(this).attr('popup-id');
				ADGURU_MP.close_popup( id );
			});
			
		},

		prepare_popups_data : function(){
			$('.adguru-modal-popup').each(function(){
				var id = $(this).attr('popup-id');
				var sizing = $(this).data('sizing');
				var animation = $(this).data('animation');
				var closing = $(this).data('closing');
				var triggering = $(this).data('triggering');
				var data = {
					'id' : id, 
					'sizing' : sizing,
					'animation' : animation,
					'closing' : closing,
					'triggering' : triggering
				};
				ADGURU_MP.popups_data[ id ] = data;
			});
		},

		prepare_to_open_popups: function(){
			for( var id in this.popups_data )
			{
				var data = this.popups_data[ id ];
				var triggering = data.triggering;
				//if preview mode then show the popup immediately regardless of triggering options
				if( this.is_preview() )
				{
					this.open_popup( id );
					continue;
				} 
				
				if( typeof triggering.auto_open_enable != 'undefined' && triggering.auto_open_enable == 1 )
				{	
					if( this.should_show( id , data ) )
					{
						var delay = parseInt( triggering.auto_open_delay );
						if( delay == 0 )
						{
							this.open_popup( id );
						}
						else
						{
							setTimeout(function(){ ADGURU_MP.open_popup( id ); }, delay*1000);
						}
					}
				}

			}
		},

		get_popup_data : function( id ){
			if( typeof this.popups_data[ id ] != 'undefined' )
			{
				return this.popups_data[ id ];
			}
			else
			{
				return false;
			}
		},

		open_popup : function( id , no_cookie ){
			no_cookie = (typeof no_cookie === 'undefined') ? false : no_cookie;
			var popup = $("#adguru_modal_popup_"+id);
			if( popup.length == 0 )
			{
				//search in backup
				if( typeof this.pupups_html_backup[id] !== 'undefined' )
				{
					popup = this.pupups_html_backup[id];
					$('body').append( popup );
				}
			}
			if( popup.length == 0 ){ return; }
			var data = this.get_popup_data( id );
			if( data == false ){ return ;}

			this.resize_popup( id );
			
			var container = $("#adguru_modal_popup_conatiner_"+id);
			var animation_data = data.animation;
			
			if( typeof animation_data.opening_animation_type != 'undefined' && animation_data.opening_animation_type != 'none' )
			{
				var cont_animation_class ='adg-animated adg-'+animation_data.opening_animation_type;
			
				if( typeof animation_data.opening_animation_speed != 'undefined' && animation_data.opening_animation_speed != 'normal' )
				{
					cont_animation_class+=' '+animation_data.opening_animation_speed;
				}

				//add animation classes to conatiner and remove animation classes once the animation is completed.
				container.addClass( cont_animation_class ).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
                 		$(this).removeClass(cont_animation_class);
       			});
			}

			popup.removeClass('hidden');
			this.add_to_opened( id );
			
			if( ! this.is_preview() )
			{
				if( no_cookie == false )
				{
					this.add_to_cookie( id, data );
				}
			}
			
		},

		close_popup : function( id ){
			var popup = $("#adguru_modal_popup_"+id);
			if( popup.length == 0 ){ return; }
			var data = this.get_popup_data( id );
			if( data == false ){ return ;}
			//add to backup 
			this.pupups_html_backup[id] = popup;

			var container = $("#adguru_modal_popup_conatiner_"+id);
			var animation_data = data.animation;
			if( typeof animation_data.closing_animation_type != 'undefined' && animation_data.closing_animation_type != 'none' )
			{
				var cont_animation_class ='adg-animated adg-'+animation_data.closing_animation_type;
			
				if( typeof animation_data.closing_animation_speed != 'undefined' && animation_data.closing_animation_speed != 'normal' )
				{
					cont_animation_class+=' '+animation_data.closing_animation_speed;
				}

				//add animation classes to conatiner and remove animation classes once the animation is completed.
				container.addClass( cont_animation_class ).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
                 		$(this).removeClass(cont_animation_class);
                 		popup.addClass('hidden');
                 		popup.remove();
       			});
			}
			else
			{
				popup.addClass('hidden');
				popup.remove();
			}

			this.remove_from_opened(id);
		},

		add_to_opened : function( id ){
			if ( this.opened_popups.indexOf( id ) == -1)
			{
				this.opened_popups.push( id );
			}
		},

		remove_from_opened : function( id ){
			var index = this.opened_popups.indexOf( id );
			if (index > -1)
			{
			  this.opened_popups.splice(index, 1);
			}
		},

		resize_opened_popups : function(){
			for( var i in this.opened_popups )
			{
				this.resize_popup( this.opened_popups[i] );
			}
		},

		resize_popup : function( id ){
			var popup = $("#adguru_modal_popup_"+id);
			if( popup.length == 0 ){ return; }
			var data = this.get_popup_data( id );
			if( data == false ){ return ;}

			var container = $("#adguru_modal_popup_conatiner_"+id);
			var content = $("#adguru_modal_popup_content_wrap_"+id);
			var sizing = data.sizing;
			var border_padding = parseInt( sizing.container_border_width )*2 + parseInt( sizing.container_padding )*2;
			var wh = $(window).height();
			if( sizing.auto_height == 0 )
			{
				if( sizing.custom_height_unit == '%' ) // for custom_height_unit == 'px' we don't need to do anything here. We alreay set the max_height rule using PHP
				{
					var cmh = ( (wh/100) * parseInt(sizing.custom_height) ) -  border_padding
					content.css('height', cmh+'px');
				}
			}
			if( sizing.min_height != 0 )
			{
				if( sizing.min_height_unit == '%' ) // for min_height_unit == 'px' we don't need to do anything here. We alreay set the min_height rule using PHP
				{
					var cmh = ( (wh/100) * parseInt(sizing.min_height) ) -  border_padding
					content.css('min-height', cmh+'px');
				}
			}
			if( sizing.max_height != 0 )
			{
				if( sizing.max_height_unit == '%' ) // for max_height_unit == 'px' we don't need to do anything here. We alreay set the max_height rule using PHP
				{
					var cmh = ( (wh/100) * parseInt(sizing.max_height) ) -  border_padding
					content.css('max-height', cmh+'px');
				}
			}

		},

		add_to_cookie : function( id, data ){
			//Check : Apply limitation for each page individually
			var triggering = data.triggering;
			if( typeof triggering.limitation_show_always != 'undefined' && triggering.limitation_show_always == 1 )
			{ 
				return ;
			}
			var cookie_name =  'adgmp_'+id;
			var cvalue = ADGURU_HELPER.get_cookie( cookie_name );
			if( cvalue != "")
			{
				var cdata = JSON.parse( cvalue );
				if( typeof cdata.show_count != 'undefined')
				{
					cdata.show_count++;
				}
				else
				{
					cdata.show_count = 1;
				}
			}
			else
			{
				cdata = { 'show_count' : 1 };
			}
			cvalue = JSON.stringify( cdata ); 
			var expire = ( typeof triggering.limitation_reset_count_after_days != 'undefined' ) ? parseInt( triggering.limitation_reset_count_after_days ) : 7;
			if( typeof triggering.limitation_apply_for_individual_page != 'undefined' && triggering.limitation_apply_for_individual_page == 1 )
			{
				ADGURU_HELPER.set_cookie( cookie_name, cvalue, expire,  window.location.pathname )
			}
			else
			{
				ADGURU_HELPER.set_cookie( cookie_name, cvalue, expire );
			}
		},

		should_show : function( id , data ){
			var triggering = data.triggering;
			if( typeof triggering.limitation_show_always != 'undefined' && triggering.limitation_show_always == 1 )
			{
				return true;
			}
			var count_limit = ( typeof triggering.limitation_showing_count != 'undefined' ) ? parseInt( triggering.limitation_showing_count ) : 1;
			var show_count = 0;
			var cookie_name =  'adgmp_'+id;
			var cvalue = ADGURU_HELPER.get_cookie( cookie_name );
			if( cvalue != "")
			{
				var cdata = JSON.parse( cvalue );
				if( typeof cdata.show_count != 'undefined')
				{
					show_count = cdata.show_count;
				}
			}
			if( show_count < count_limit )
			{
				return true;
			}

			return false;
		},
		is_preview : function(){
			return ( typeof ADGURU_MODAL_POPUP_PREVIEW_MODE != 'undefined' && ADGURU_MODAL_POPUP_PREVIEW_MODE == true );
		}

	};

	$(document).ready(function(){
		ADGURU_MP.init();
	});
})(jQuery)

/*
function adguru_modal_popup_open( id )
{
	ADGURU_MP.open_popup( id , true );
}
function adguru_modal_popup_close( id )
{
	ADGURU_MP.close_popup( id );
}
*/