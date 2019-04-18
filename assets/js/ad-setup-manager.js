;
var ADGURU_ASM = {};
( function( $ ){

	ADGURU_ASM = {

		last_set_number : 0,
		
		init : function(){
			this.add_events();
			this.create_blank_condition_set();
			this.create_blank_condition_set();
		},

		add_events : function(){
			$("#add_condition_set_btn").click(function(){
				ADGURU_ASM.create_blank_condition_set();
			});
		},

		get_ad_html : function( ad_data ){

		},

		get_slide_html : function( slide_data ){
			
			var tmpl = ADGURU_ASM_DATA.slide_html_template;
			var html = tmpl.replace('{{SLIDE_NUMBER}}', slide_data['number'] );
			if( slide_data['ad_ids'].length )
			{

			}
			else
			{
				var html = html.replace('{{ADS_HTML}}', '' );
			}
			return html;
		},

		create_condition_set : function( data ){
			this.last_set_number++;
			var html_id = 'condition_set_'+this.last_set_number;
			var tmpl = ADGURU_ASM_DATA.condition_set_html_template;
			var html = tmpl.replace('{{SET_HTML_ID}}', html_id );

			var html = html.replace('{{PAGE_TYPE_DISPLAY_HTML}}', data['page_type_display_html'] );
			var html = html.replace('{{CONDITION_DETAIL}}', data['condition_detail'] );
			var slide_data = {
				'ad_ids' : [],
				'number' : 0
			};
			if( data['ad_zone_link_set'].length )
			{

			}
			else
			{
				slide_data['number'] = 1;
				var slide_html = this.get_slide_html( slide_data );
				var html = html.replace('{{SLIDES_HTML}}', slide_html );
			}

			$("#condition_sets_box").append( html );
		},

		create_blank_condition_set : function(){
			var data = {
				'page_type_display_html' : '<span style="color:red">Select page type</span>',
				'condition_detail' : '',
				'ad_zone_link_set' : [],
			};

			this.create_condition_set(data);
		},

	};//end ADGURU_ASM

	$(document).ready(function(){
		ADGURU_ASM.init();
	});

} )(jQuery);
console.log("Ad setup manager loaded.....");