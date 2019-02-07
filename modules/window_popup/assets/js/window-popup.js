/**
 * Window Popup JS
 * @author oneTarek
 * @since 2.0.0
*/
;
var ADGURU_WINP = null;

(function($){
	ADGURU_WINP = {
		opened_popups : [],//list of popup ids those are currently opened.
		opened_window_references : {}, //list of references of window objects of opend windows. 
		popups_data : [], //list of popups data objects
		init : function(){
			this.add_events();
			this.prepare_popups_data();
			this.prepare_to_open_popups();
		},

		add_events : function(){
			
		},

		prepare_popups_data : function(){
			$('.adguru-window-popup').each(function(){
				var id = $(this).attr('popup-id');
				var url = $(this).attr('popup-url');
				var sizing = $(this).data('sizing');
				var popup_options = $(this).data('popup-options');
				var triggering = $(this).data('triggering');

				var data = {
					'id' : id, 
					'url' : url,
					'sizing' : sizing,
					'popup_options' : popup_options,
					'triggering' : triggering
				};
				ADGURU_WINP.popups_data[ id ] = data;
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
					$('body').one('click', function(){
						ADGURU_WINP.open_popup( id );
					});
					continue;
				} 

				var SW = this.should_show( id , data ) ; //SW means should show
				/*
				if( typeof triggering.auto_open_enable != 'undefined' && triggering.auto_open_enable == 1 )
				{	
					if( this.should_show( id , data ) )
					{
						var delay = parseInt( triggering.auto_open_delay );
						if( delay == 0 )
						{console.info("OPening popup without delay");
							this.open_popup( id );
						}
						else
						{console.info("OPening popup with delay "+delay);

							setTimeout(function(){ ADGURU_WINP.open_popup( id ); }, delay);
						}
					}
				}*/
				//open on body click 
				if( typeof triggering.open_on_body_click_enable != 'undefined' && triggering.open_on_body_click_enable == 1 )
				{	
					if( SW )
					{
						$('body').one('click', function(){
							ADGURU_WINP.open_popup( id );
						});
						/*
						$('#myButton').on('click', function(){
							console.info("OPening popup with butotn CLICK ");
							ADGURU_WINP.open_popup( id , false );
						});
						*/
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

		open_popup : function( id , add_count ){
			if( typeof add_count === 'undefined'){add_count = true;}
			//check if popup is opened already
			if ( this.opened_popups.indexOf( id ) != -1)
			{
				var windowRef = this.opened_window_references[id];
				if( windowRef.closed == false )
				{   //Window is opened and setting foucs to that
					windowRef.focus();
					return false;
				}
			}

			var data = this.get_popup_data( id );
			if( data == false ){ return ;}
			
			var sizing = data.sizing;
			var popup_options = data.popup_options;
			
			var url = data.url;
			var name = "adguru_window_popup_"+id;
			var features = {};
			var featuresStr = "";
			var width = 500;
			var height = 500;
			var left = 100;
			var top = 100;
			var position = "center";
			if( typeof sizing.mode != 'undefined' && sizing.mode == 'custom' )
			{
				width = parseInt( sizing.custom_width );
				height = parseInt( sizing.custom_height );
			}
			else
			{
				//
			}
			features['width'] = width;
			features['height'] = height;

			if( position == "center" ){ left = (screen.width) ? (screen.width-width)/2 : 100; top = (screen.height) ? (screen.height-height)/2 : 100; }
			features['left'] = left;
			features['top'] = top;
			
			if( typeof popup_options.window_options != 'undefined' )
			{
				var wo = popup_options.window_options;
				for(var k in wo) { features[k] = wo[k]; }
			}

			for( var k in features )
			{
				featuresStr+= k+'='+features[k]+','; 
			}
			this.opened_window_references[id] = window.open(url, name, featuresStr);

			//safari is not opening popup , possible solution here https://stackoverflow.com/questions/20696041/window-openurl-blank-not-working-on-imac-safari
			
			this.add_to_opened( id );
			if( !this.is_preview() && add_count )
			{
				this.add_to_cookie( id, data );
			}
			
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


		add_to_cookie : function( id, data ){
			//Check : Apply limitation for each page individually
			var triggering = data.triggering;
			if( typeof triggering.limitation_show_always != 'undefined' && triggering.limitation_show_always == 1 )
			{ 
				return ;
			}
			var cookie_name =  'adgwinp_'+id;
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
			var cookie_name =  'adgwinp_'+id;
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
			return ( typeof ADGURU_WINDOW_POPUP_PREVIEW_MODE != 'undefined' && ADGURU_WINDOW_POPUP_PREVIEW_MODE == true );
		}

	};

	$(document).ready(function(){
		ADGURU_WINP.init();
	});
})(jQuery)