;
var ADGURU_ASM = {};
( function( $ ){

	ADGURU_ASM = {

		last_set_number : 0,
		
		init : function(){
			this.add_events();
			this.create_new_condition_set();
		},

		add_events : function(){

		},

		get_ad_html : function( ad_data ){

		},

		get_slide_html : function( slide_data ){

		},

		create_new_condition_set : function(){
			this.last_set_number++;
			var html_id = 'condition_set_'+this.last_set_number;
			var tmpl = ADGURU_ASM_DATA.condition_set_html_template;
			var html = tmpl.replace('{{SET_HTML_ID}}', html_id );
			$("#condition_sets_box").append( html );
		}

	};//end ADGURU_ASM

	$(document).ready(function(){
		ADGURU_ASM.init();
	});

} )(jQuery);
console.log("Ad setup manager loaded.....");