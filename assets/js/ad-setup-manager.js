;
var ADGURU_ASM = {};
( function( $ ){

	ADGURU_ASM = {
		init : function(){
			this.add_events();
			this.create_new_condition_set();
		},

		add_events : function(){

		},

		create_new_condition_set : function(){
			$("#condition_sets_box").append( ADGURU_ASM_DATA.condition_set_html_template );
		}

	};//end ADGURU_ASM

	$(document).ready(function(){
		ADGURU_ASM.init();
	});

} )(jQuery);
console.log("Ad setup manager loaded.....");