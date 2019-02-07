/**
 * @package WP Admin Form Builder
 * @author oneTarek
 * @since 1.0.0
 */
;
var WPAFB = {};
( function( $ ){
	WPAFB = {
		init : function(){
			this.setupColorPIcker();
			this.setupMediaUploader();
			this.setupSlider();
			this.tinyMceEditorSetup();
			this.addEvents();
		},
		setupColorPIcker : function(){
			//Initiate Color Picker
	        $('.wpafb-color-picker-field').wpColorPicker({ //https://make.wordpress.org/core/2012/11/30/new-color-picker-in-wp-3-5/
	        	'change' : function(event, ui){
	        		var args = {};
		        	//args['value'] = $(this).val();
		        	args['value'] = ui.color.toString();
		        	args['type'] = 'color';
		        	args['id'] = $(this).attr('id');
		        	args['formid'] = $(this).attr('formid');
		        	args['obj'] = this;
		        	$(document).trigger('wpafb-field:change', [ args ] );
		        	$(document).trigger('wpafb-field:change:'+args['id'], [ args ] );
		        	//console.log(args);

	        	}
	        });
		},
		setupMediaUploader : function(){
			//File browser
	        $('.wpafb-browse').on('click', function (event) {
	            event.preventDefault();

	            var self = $(this);

	            // Create the media frame.
	            var file_frame = wp.media.frames.file_frame = wp.media({
	                title: self.data('uploader_title'),
	                button: {
	                    text: self.data('uploader_button_text'),
	                },
	                multiple: false
	            });

	            file_frame.on('select', function () {
	                attachment = file_frame.state().get('selection').first().toJSON();

	                url_field = self.prev('.wpafb-url');
	                url_field.val(attachment.url);
	                url_field.trigger('change');
	            });

	            // Finally, open the modal
	            file_frame.open();
	        });
	        //END File browser
		},
		setupSlider : function(){
			//========START SLIDER================
	         $(".wpafb-slider").each(function(){
		    	var INPUT = $(this).find(".wpafb-slider-input").first();
		    	var valueA = INPUT.val();
		        var VALUE = ( !isNaN( valueA ) ) ?  parseFloat(valueA) : 0;
		    	var minA = $(this).attr("min");
		        var MIN = ( !isNaN(minA) )? parseFloat(minA) : 0;
		        var maxA = $(this).attr("max");
		        var MAX = ( !isNaN(maxA) )? parseFloat(maxA) : 100;
		        var stepA = $(this).attr("step");
		        var STEP = ( !isNaN(stepA) )? parseFloat(stepA) : 1; if( STEP == 0 ) { STEP = 1; }
				
		        var disabledA = $(this).attr("disabled");
		        var DISABLED = (typeof disabledA != 'undefined' && disabledA == 'disabled') ? true : false;
		        var STEP = ( !isNaN(stepA) )? parseFloat(stepA) : 1; if( STEP == 0 ) { STEP = 1; }
		        
		        var HANDLE = $(this).find(".custom-handle").first();
		   		var DISPLAY = $(this).find(".display").first();
		        var UNIT_TEXT = "";
		        if( !DISPLAY.length)
		        {
		        	DISPLAY = false;
		        }
		        else
		        {
					UNIT_TEXT = " "+DISPLAY.attr('unit-text');
		        }
		        
		        var SLIDER = $(this).find(".slider").first();
		        
		        $( SLIDER ).slider({
		    	  range: "max",
		          min: MIN,
		          max: MAX,
		          value: VALUE,
		          step: STEP,
		          disabled : DISABLED,
		          create: function() {
		            HANDLE.text( SLIDER.slider( "option", "value" ) );
		            if( DISPLAY )
		            {
		              DISPLAY.val( SLIDER.slider( "option", "value" ) + UNIT_TEXT );
		            }
		          },
		          change: function( event, ui ) {
		            INPUT.val( ui.value );
		            HANDLE.text( ui.value );
		            if( DISPLAY )
		            {
		              DISPLAY.val( ui.value + UNIT_TEXT );
		            }
		            INPUT.trigger('change');
		          },
		          slide: function( event, ui ) {
		            INPUT.val( ui.value );
		            HANDLE.text( ui.value );
		            if( DISPLAY )
		            {
		              DISPLAY.val( ui.value + UNIT_TEXT );
		            }
		            INPUT.trigger('change');
		          }
		        });
		        
		        if( !DISABLED )
		        {
		        
		          var minus_button = $(this).find(".minus-button").first();
		          if( minus_button.length)
		          {
		              minus_button.click(function(){
		                  var sliderCurrentValue = parseFloat( SLIDER.slider( "option", "value" ) );
		                  SLIDER.slider( "value", sliderCurrentValue - STEP );
		              });
		          }

		          var plus_button = $(this).find(".plus-button").first();
		          if( plus_button.length)
		          {
		              plus_button.click(function(){
		                  var sliderCurrentValue = parseFloat( SLIDER.slider( "option", "value" ) );
		                  SLIDER.slider( "value", sliderCurrentValue + STEP );
		              });
		          }
		        }
			    
			});//end $(".wpafb-slider").each(

	        //========END SLIDER==================
		},
		/*
		 * Add event listeners to wp_editor( TinyMCE editor )
		 * Add custom listeners if the editor is wpafb field, Not other TinyMCE editor
		 */
		tinyMceEditorSetup : function(){
			//https://make.wordpress.org/core/2017/05/20/editor-api-changes-in-4-8/
			//https://www.tiny.cloud/docs/advanced/events/#change
			//https://www.tiny.cloud/docs/api/tinymce/tinymce.editor/
			$( document ).on( 'tinymce-editor-setup', function( event, editor ) {
				
				var EDITOR_ID = editor.settings.id;
				if( $("#"+EDITOR_ID).length && $("#"+EDITOR_ID).hasClass('wpafb-field-editor') )
				{
				    editor.on('Change', function (e) {
				    	$("#"+EDITOR_ID).val( editor.getContent() );//add content to the textarea.
				    	$("#"+EDITOR_ID).trigger('change'); //textarea element of the editor
				    });
				    //add more listener here
			    }// end if
			});
		},

		addEvents : function(){

			//Image type field preview
	        $('.wpafb-image-url').on('change', function(){
	        	var url = $(this).val();
	        	var img_holder = $( this).nextAll('.wpafb-preview-image-holder').first();
	        	if( img_holder.length )
	        	{
	        		var img = img_holder.children('.wpafb-preview-image').first();
	        		if( img.length )
	        		{
	        			if( url == "")
	        			{
	        				img.addClass( "hidden" );
	        			}
	        			else
	        			{
	        				img.attr('src', url );
	        				img.removeClass( "hidden" );
	        			}
	        		}
	        	}
	        	var args = {};
	        	args['value'] = $(this).val();
	        	args['type'] = 'image';
	        	args['id'] = $(this).attr('id');
	        	args['formid'] = $(this).attr('formid');
	        	args['obj'] = this;
	        	$(document).trigger('wpafb-field:change', [ args ] );
	        	$(document).trigger('wpafb-field:change:'+args['id'], [ args ] );//example usages : jQuery(document).on('wpafb-field:change:my_field_id', function(event , args){......});
	        	//console.log(args);

	        });
	        //End Image type field preview
			
			$('.wpafb-field-text').change(function(){
	        	
	        	var args = {};
	        	args['value'] = $(this).val();
	        	args['type'] = 'text';
	        	args['id'] = $(this).attr('id');
	        	args['formid'] = $(this).attr('formid');
	        	args['obj'] = this;
	        	$(document).trigger('wpafb-field:change', [ args ] );
	        	$(document).trigger('wpafb-field:change:'+args['id'], [ args ] );
	        	//console.log(args);

	        });

	        $('.wpafb-field-number').change(function(){
	        	
	        	var args = {};
	        	args['value'] = $(this).val();
	        	args['type'] = 'number';
	        	args['id'] = $(this).attr('id');
	        	args['formid'] = $(this).attr('formid');
	        	args['obj'] = this;
	        	$(document).trigger('wpafb-field:change', [ args ] );
	        	$(document).trigger('wpafb-field:change:'+args['id'], [ args ] );
	        	//console.log(args);

	        });

	        $('.wpafb-field-checkbox').change(function(){
	        	var args = {};
	        	args['value'] = $(this).val();
	        	args['type'] = 'checkbox';
	        	args['id'] = $(this).attr('id');
	        	args['formid'] = $(this).attr('formid');
	        	args['obj'] = this;
	        	if( $(this).is(':checked') == false )
	        	{
	        		args['value']  = $(this).attr('offvalue');
	        	}

	        	$(document).trigger('wpafb-field:change', [ args ] );
	        	$(document).trigger('wpafb-field:change:'+args['id'], [ args ] );
	        	//console.log(args);

	        });

	        $('.wpafb-field-multicheck-item').change(function(){
	        	var fieldid = $(this).attr('fieldid');
	        	var field = $(this).closest('.wpafb-field-multicheck');
	        	var items = $(field).find('.wpafb-field-multicheck-item').toArray();
	        	var value = {};
	        	for( var i = 0; i< items.length ; i++ )
	        	{
	        		var item = $( items[i] );
	        		key = item.attr('item-key');
	        		if( item.is(':checked') )
		        	{
		        		value[key] = item.val();
		        	}
		        	else
		        	{
		        		value[key] = item.attr('offvalue');
		        	}
	        	}
	        	var args = {};
	        	args['value'] = value;
	        	args['type'] = 'multicheck';
	        	args['id'] = fieldid;
	        	args['formid'] = $(field).attr('formid');
	        	args['obj'] = this;
	        	$(document).trigger('wpafb-field:change', [ args ] );
	        	$(document).trigger('wpafb-field:change:'+args['id'], [ args ] );
	        	//console.log(args);

	        });

	        $('.wpafb-field-radio-item').change(function(){
	        	var fieldid = $(this).attr('fieldid');
	        	var field = $(this).closest('.wpafb-field-radio');
	        	var items = $(field).find('.wpafb-field-radio-item').toArray();
	        	var value = '';
	        	for( var i = 0; i< items.length ; i++ )
	        	{
	        		var item = $( items[i] );
	        		if( item.is(':checked') )
		        	{
		        		value = item.val();
		        		break;
		        	}
	        	}
	        	var args = {};
	        	args['value'] = value;
	        	args['type'] = 'radio';
	        	args['id'] = fieldid;
	        	args['formid'] = $(field).attr('formid');
	        	args['obj'] = this;
	        	$(document).trigger('wpafb-field:change', [ args ] );
	        	$(document).trigger('wpafb-field:change:'+args['id'], [ args ] );
	        	//console.log(args);

	        });

	        $('.wpafb-field-radio-image-item').change(function(){
	        	var fieldid = $(this).attr('fieldid');
	        	var field = $(this).closest('.wpafb-field-radio-image');
	        	var items = $(field).find('.wpafb-field-radio-image-item').toArray();
	        	var value = '';
	        	var img_url = '';
	        	for( var i = 0; i< items.length ; i++ )
	        	{
	        		var item = $( items[i] );
	        		if( item.is(':checked') )
		        	{
		        		value = item.val();
		        		var box = item.closest('.wpafb-radio-image-item-box');
		        		field.children('.wpafb-radio-image-item-box').removeClass('selected');
		        		box.addClass('selected');
		        		img_url = box.find('img').first().attr('src');
		        		break;
		        	}
	        	}
	        	var args = {};
	        	args['value'] = value;
	        	args['type'] = 'radio_image';
	        	args['id'] = fieldid;
	        	args['formid'] = $(field).attr('formid');
	        	args['img_url'] = img_url;
	        	args['obj'] = this;
	        	$(document).trigger('wpafb-field:change', [ args ] );
	        	$(document).trigger('wpafb-field:change:'+args['id'], [ args ] );
	        	//console.log(args);

	        });

	        $('.wpafb-field-select').change(function(){
	        	
	        	var args = {};
	        	args['value'] = $(this).val();
	        	args['type'] = 'select';
	        	args['id'] = $(this).attr('id');
	        	args['formid'] = $(this).attr('formid');
	        	args['obj'] = this;
	        	$(document).trigger('wpafb-field:change', [ args ] );
	        	$(document).trigger('wpafb-field:change:'+args['id'], [ args ] );
	        	//console.log(args);

	        });

	        $('.wpafb-field-textarea').change(function(){
	        	
	        	var args = {};
	        	args['value'] = $(this).val();
	        	args['type'] = 'textarea';
	        	args['id'] = $(this).attr('id');
	        	args['formid'] = $(this).attr('formid');
	        	args['obj'] = this;
	        	$(document).trigger('wpafb-field:change', [ args ] );
	        	$(document).trigger('wpafb-field:change:'+args['id'], [ args ] );
	        	//console.log(args);

	        });

	        $('.wpafb-field-editor').change(function(){ //NOT DONE YET
	        	
	        	var args = {};
	        	args['value'] = $(this).val();
	        	args['type'] = 'editor';
	        	args['id'] = $(this).attr('id');
	        	args['formid'] = $(this).closest('.wpafb-editor').attr('formid');
	        	args['obj'] = this;
	        	$(document).trigger('wpafb-field:change', [ args ] );
	        	$(document).trigger('wpafb-field:change:'+args['id'], [ args ] );
	        	//console.log(args);

	        });

	        $('.wpafb-field-file').change(function(){
	        	
	        	var args = {};
	        	args['value'] = $(this).val();
	        	args['type'] = 'file';
	        	args['id'] = $(this).attr('id');
	        	args['formid'] = $(this).attr('formid');
	        	args['obj'] = this;
	        	$(document).trigger('wpafb-field:change', [ args ] );
	        	$(document).trigger('wpafb-field:change:'+args['id'], [ args ] );
	        	//console.log(args);

	        });

	        $('.wpafb-field-image').change(function(){
	        	
	        	var args = {};
	        	args['value'] = $(this).val();
	        	args['type'] = 'image';
	        	args['id'] = $(this).attr('id');
	        	args['formid'] = $(this).attr('formid');
	        	args['obj'] = this;
	        	$(document).trigger('wpafb-field:change', [ args ] );
	        	$(document).trigger('wpafb-field:change:'+args['id'], [ args ] );
	        	//console.log(args);

	        });


	        $('.wpafb-field-slider').change(function(){
	        	var args = {};
	        	args['value'] = $(this).val();
	        	args['type'] = 'slider';
	        	args['id'] = $(this).attr('id');
	        	args['formid'] = $(this).attr('formid');
	        	args['obj'] = this;
	        	$(document).trigger('wpafb-field:change', [ args ] );
	        	$(document).trigger('wpafb-field:change:'+args['id'], [ args ] );
	        	//console.log(args);

	        });
	       
		},//End function addEvents

		getFieldValue : function(fieldid , detail ){
			
			detail = (typeof detail === 'undefined') ? false : detail; //If detail is true return value within an object along with other related informations. We need this specially for radio-image type field.

			if( $('#'+fieldid).length == 0 )
			{	
				return false;
			}
			var elm = $('#'+fieldid );
			var fieldtype = elm.attr('fieldtype');
			
			if( typeof fieldtype == 'undefined' || fieldtype == '')
			{	
				if( elm.closest('.wpafb-editor').length )
				{
					fieldtype = 'editor';
				}
				else
				{
					return false;
				}
				
			}

			if( fieldtype == 'text' || 
				fieldtype == 'number' || 
				fieldtype == 'password' || 
				fieldtype == 'email' || 
				fieldtype == 'url' || 
				fieldtype == 'color' || 
				fieldtype == 'slider' || 
				fieldtype == 'select' || 
				fieldtype == 'textarea' || 
				fieldtype == 'file' || 
				fieldtype == 'image' 
			)
			{
				return elm.val();
			}
			else if( fieldtype == 'checkbox' )
			{
				return ( elm.is(':checked') == false ) ? elm.attr('offvalue') : elm.val();
			}
			else if( fieldtype == 'multicheck')
			{
				var items = elm.find('.wpafb-field-multicheck-item').toArray();
	        	var value = {};
	        	for( var i = 0; i< items.length ; i++ )
	        	{
	        		var item = $( items[i] );
	        		key = item.attr('item-key');
	        		if( item.is(':checked') )
		        	{
		        		value[key] = item.val();
		        	}
		        	else
		        	{
		        		value[key] = item.attr('offvalue');
		        	}
	        	}
	        	return value;
			}
			else if( fieldtype == 'radio' )
			{
				var items = elm.find('.wpafb-field-radio-item').toArray();
	        	var value = '';
	        	for( var i = 0; i< items.length ; i++ )
	        	{
	        		var item = $( items[i] );
	        		if( item.is(':checked') )
		        	{
		        		value = item.val();
		        		break;
		        	}
	        	}
	        	return value;
			}
			else if( fieldtype == 'editor' )
			{	
				return elm.val();
			}
			else if( fieldtype == 'radio_image' )
			{
	        	var items = $(elm).find('.wpafb-field-radio-image-item').toArray();
	        	var value = '';
	        	var img_url = '';
	        	var info = {};
	        	for( var i = 0; i< items.length ; i++ )
	        	{
	        		var item = $( items[i] );
	        		if( item.is(':checked') )
		        	{
		        		value = item.val();
		        		var box = item.closest('.wpafb-radio-image-item-box');
		        		img_url = box.find('img').first().attr('src');
		        		break;
		        	}
	        	}
	        	
	        	info['value'] = value;
	        	info['img_url'] = img_url;
	        	if( detail )
	        	{
	        		return info;
	        	}
	        	else
	        	{
	        		return value;
	        	}
	        	
	        	
			}
			
			return false;

		},//end function getFieldValue

		setFieldValue : function(fieldid, value ){
			
			if( $('#'+fieldid).length == 0 )
			{	
				return false;
			}
			var elm = $('#'+fieldid );
			var fieldtype = elm.attr('fieldtype');
			var success = false;
			if( typeof fieldtype == 'undefined' || fieldtype == '')
			{	
				if( elm.closest('.wpafb-editor').length )
				{
					fieldtype = 'editor';
				}
				else
				{
					return false;
				}
				
			}

			if( fieldtype == 'text' || 
				fieldtype == 'number' || 
				fieldtype == 'password' || 
				fieldtype == 'email' || 
				fieldtype == 'url' || 
				fieldtype == 'select' || 
				fieldtype == 'textarea' || 
				fieldtype == 'file' || 
				fieldtype == 'image' 
			)
			{
				elm.val( value );
				elm.trigger( 'change' );
				success = true;
			}
			else if( fieldtype == 'color' )
			{
				elm.val( value );
				elm.trigger( 'change' );
				success = true;
			}
			else if( fieldtype == 'slider' )
			{
				var slider_elm = elm.closest('.wpafb-slider').find('.slider').first();
				slider_elm.slider( "value", value ); //change event is declared before in slider options
				success = true;
				
			}
			else if( fieldtype == 'checkbox' )
			{
				if( value == elm.val() )
				{
					elm.prop('checked', true);
				}
				else
				{
					elm.prop('checked', false);
				}
				elm.trigger( 'change' );
				success = true;
			}
			else if( fieldtype == 'multicheck' )
			{
				if( typeof value !== 'object')
				{
					return;
				}
				var items = elm.find('.wpafb-field-multicheck-item').toArray();
	        	var item = false;
	        	for( var i = 0; i< items.length ; i++ )
	        	{
	        		item = $( items[i] );
	        		key = item.attr('item-key');
	        		if( typeof value[key] !== 'undefined' )
	        		{
	        			if( value[key] == item.val() )
	        			{	
	        				item.prop('checked', true);
	        			}
	        			else
	        			{	
	        				item.prop('checked', false);
	        			}
	        		}
	        		else
	        		{	
	        			item.prop('checked', false);
	        		}
	        	}
	        	if(item){ item.trigger( 'change' ); success = true; }

	        	
			}
			else if( fieldtype == 'radio' )
			{
				var items = elm.find('.wpafb-field-radio-item').toArray();
	        	
	        	for( var i = 0; i< items.length ; i++ )
	        	{
	        		var item = $( items[i] );
	        		if( value == item.val() )
		        	{
		        		item.prop('checked', true);
		        		item.trigger( 'change' );
		        		break;
		        	}
	        	}
	        	success = true;
			}
			else if( fieldtype == 'editor' )
			{	
				/*
				TinyMCE editor is not loaded on page load all the time. If the user open editor in text mode last time, on next visit the editr will 
				show the textarea and TinyMCE editor will not exist. When user open the visual mode then TinyMCE editor will be loaded. 
				SO CHECK IF THE TinyMCE editor instance exists or not.
				*/ 
				var tmced = tinymce.get(fieldid);
	        	if( tmced )
	        	{	
	        		//set content directly to the textarea
	        		elm.val( value );
	        		//Also set content to TinyMCE editor instance
	        		tmced.setContent( value );
	        		elm.trigger( 'change' );
	        	}
	        	else
	        	{
	        		elm.val( value );//set content directly to the textarea
					elm.trigger( 'change' );

	        	}
	        	success = true;
			}
			else if( fieldtype == 'radio_image' )
			{
				var items = elm.find('.wpafb-field-radio-image-item').toArray();
	        	
	        	for( var i = 0; i< items.length ; i++ )
	        	{
	        		var item = $( items[i] );
	        		if( value == item.val() )
		        	{
		        		item.prop('checked', true);
		        		item.trigger( 'change' );
		        		break;
		        	}
	        	}
	        	success = true;
			}
			
			return success;

		},//end function setFieldValue

		hideField : function(fieldid){
			$('#row_'+fieldid).addClass('hidden');
		},
		showField : function(fieldid){
			$('#row_'+fieldid).removeClass('hidden');
		},
		hideFieldGroup : function(groupid){
			$('#'+groupid).addClass('hidden');
		},
		showFieldGroup : function(groupid){
			$('#'+groupid).removeClass('hidden');
		}
		

	};//end WPAFB
	
	$(document).ready(function(){
		WPAFB.init();
    });//$(document) 
} )(jQuery);

