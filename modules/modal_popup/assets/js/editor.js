/**
 * ADGURU Modal Popup Editor
 * @author oneTarek
 * @since 2.0.0
 */
;
var ADGURU_MP_EDITOR = {};

( function( $ ){
	ADGURU_MP_EDITOR = {

		themes_data : [],
		design_source : 'theme',
		theme_id : 0,

		init : function(){

			this.design_source = ( $("#design_source_theme").is(':checked') ) ? 'theme' : 'custom';
			if( this.design_source == 'theme' )
			{
				this.theme_id = parseInt( $("#theme_id").val() );
			}
			this.ad_events();
			ADGURU_MP_PREIVEW.init();
			this.refresh();
		},

		ad_events : function(){

			$('input[type=radio][name=design_source]').on('change', function() {
			     ADGURU_MP_EDITOR.design_source = $(this).val();
			     ADGURU_MP_EDITOR.refresh();

			});

			$('#theme_id').on('change', function() { 
			     ADGURU_MP_EDITOR.theme_id = parseInt( $(this).val() ); 
			     ADGURU_MP_EDITOR.refresh();

			});

		},

		refresh : function(){
			if( this.design_source == 'custom')
			{
				//
				ADGURU_MP_FIELDS.read_values('design');
				ADGURU_MP_PREIVEW.reload();
			}
			else
			{
				if( this.theme_id == 0 )
				{
					return;
				}
				
				if( typeof this.themes_data[ this.theme_id ] !== 'undefined')
				{
					//set theme design data to ADGURU_MP_FIELDS
					ADGURU_MP_FIELDS.set_value_by_group('design',this.themes_data[ this.theme_id ]['meta']['design']);
					ADGURU_MP_PREIVEW.reload();

				}
				else
				{
					//load theme data
					$("#mp_editor_loading_box").removeClass('hidden');
					$("#customize_theme_btn").addClass('hidden');
					var qData={
						"action"	:	"adguru_mp_get_theme_data", 
						"theme_id"	: 	this.theme_id,
						"for_editor" : 1
						};
					
					$.ajax({
					   url: adGuruAdminVars.ajaxUrl,
					   type: "GET",
					   global: false,
					   cache: false,
					   async: true,
					   dataType: 'json',
					   data:qData,
						success: function(response){				
							
								$("#mp_editor_loading_box").addClass('hidden');
								$("#customize_theme_btn").removeClass('hidden');
								if(response.status == 'success')
								{
									var theme_data = $.parseJSON( response.theme_data );
									ADGURU_MP_EDITOR.themes_data[ ADGURU_MP_EDITOR.theme_id ] = theme_data;
									ADGURU_MP_FIELDS.set_value_by_group('design',theme_data['meta']['design']);
									ADGURU_MP_PREIVEW.reload();
												
								}
								else
								{
									alert(response.message);
								}
							},
						error: function(xhr,errorThrown){
							alert('Something went wrong');
						}
						   
					  });//end $.ajax

				}
			}
		},
		customize_selected_theme : function(){
			ADGURU_MP_PREIVEW.pause();
			var design = JSON.parse(JSON.stringify( this.themes_data[this.theme_id]['meta']['design'] ) ) ;
			var close_image_source_type = ( typeof design['close_image_source_type'] !== 'undefined' ) ? design['close_image_source_type'] : 'builtin';
			for( id in ADGURU_MP_FIELDS.fields )
			{
				var field = ADGURU_MP_FIELDS.fields[ id ];
				if( field['group'] != 'design'){ continue; }
				if( field['name'] == 'close_image_name' && close_image_source_type == 'builtin')
				{
					var value = design['close_image_name']['value'];
				}
				else
				{
					var value = design[ field['name'] ];
				}
				WPAFB.setFieldValue( id , value );
			}

			ADGURU_MP_PREIVEW.resume();
			ADGURU_MP_FIELDS.set_value_by_group('design',design );
			$("#design_source_custom").trigger('click');
			ADGURU_MP_PREIVEW.reload();

		}


	};//end ADGURU_MP_EDITOR
	
} )(jQuery);

function adguru_mp_customize_selected_theme()
{
	ADGURU_MP_EDITOR.customize_selected_theme();
	return false;
}