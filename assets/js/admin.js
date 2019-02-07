/**
Ad Guru JS for admin usage
@author: oneTarek
@since 2.0.0
*/


var ADGURU_ADMIN_HELPER;//global object

(function($){
 
 
ADGURU_ADMIN_HELPER = {
	
	add_loading_overlay : function( element )
	{
		var height 	= $( element ).height();
		var width 	= $( element ).width();
		var zindex 	= $( element ).zIndex();
		$( element ).before( '<div class="overlay"><img src="'+adGuruAdminVars.assetsUrl+'/images/loading32.gif" alt="loading.."><div></div></div>' );
		var prevEl 	= $( element ).prev( ".overlay" );
	    $( prevEl )
		  .height( height )
		  .width( width )
		  .css({
			 'position': 'absolute',
			 'z-index': zindex+1
		  });
	    $( prevEl ).children( "div" ).first()
		  .height( height )
		  .width( width )
		  .css({
			 'opacity' : 0.4,
			 'background-color': 'black'
		  });
		  
		  var margin_left 	= Math.floor( width/2 -16 );
		  var margin_top 	= Math.floor( height/2 -16 );
		 $( prevEl ).children( "img" ).first().css({
			 'position': 'absolute',								   	
			 'margin-top': margin_top,
			 'margin-left': margin_left,			
		 });
	},//end add_loading_overlay
	
	remove_loading_overlay : function( element ){
		$( element ).prev( ".overlay" ).remove();		
	},//end remove_loading_overlay

	hex_to_rgba :  function ( hex, opacity ){
	    if( typeof hex === 'undefined' )
	    {
	        return '';
	    }

	    opacity = ( typeof opacity === 'undefined' ) ? 1.0 : opacity;

	    hex = hex.replace('#', '');
	    var r = parseInt( hex.substring( 0, 2 ), 16 );
	    var g = parseInt( hex.substring( 2, 4 ), 16 );
	    var b = parseInt( hex.substring( 4, 6 ), 16 );
	    //var rgba = 'rgba(' + r + ',' + g + ',' + b + ',' + opacity / 100 + ')'; 
	    var rgba = 'rgba(' + r + ',' + g + ',' + b + ',' + opacity + ')'; 

	    return rgba;
	},

	get_font_family_with_fallback : function ( font ){
	    if( font == ""){ return "";}
	    var font_families = {
	        'Arial' : 'Arial, Helvetica, sans-serif',
	        'Arial Black' : '"Arial Black", Gadget, sans-serif',
	        'Comic Sans MS' : '"Comic Sans MS", cursive, sans-serif',
	        'Courier New' : '"Courier New", Courier, monospace',
	        'Georgia' : 'Georgia, serif',
	        'Impact' : 'Impact, Charcoal, sans-serif',
	        'Lucida Sans Unicode' : '"Lucida Sans Unicode", "Lucida Grande", sans-serif',
	        'Lucida Console' : '"Lucida Console", Monaco, monospace',
	        'Palatino Linotype' : '"Palatino Linotype", "Book Antiqua", Palatino, serif',
	        'Tahoma' : 'Tahoma, Geneva, sans-serif',
	        'Times New Roman' : '"Times New Roman", Times, serif',
	        'Trebuchet MS' : '"Trebuchet MS", Helvetica, sans-serif',
	        'Verdana' : 'Verdana, Geneva, sans-serif',
	    };
	    return ( typeof font_families[ font ] !== 'undefined' ) ? font_families[ font ] : font;
	},

	/**
	 * Run action for a toggler
	 * @param element jQuery element object
	 * @since 2.0.0
	 */
	run_toggler : function ( element ){ 
		var to_toggle = $( element ).attr( 'to_toggle' );
		var value = $( element ).val();
		$( "."+to_toggle ).addClass( "hidden" );
		$( "."+to_toggle+"_"+value ).removeClass( "hidden" );
	}
	
  }//end  ADGURU_ADMIN_HELPER
})(jQuery);



jQuery(document).ready(function($){

	$("#size_list").on('change',function(){
		var val = $(this).val();
		if( val == "custom" )
		{
			$("#width").removeAttr("readonly");
			$("#height").removeAttr("readonly");
		}
		else
		{
			var wh = val.split("x");
			$("#width").val(wh[0]);
			$("#height").val(wh[1]);
			$("#width").attr("readonly","readonly");
			$("#height").attr("readonly","readonly");
		}
	});

	/**
	 * Toggler elements actions
	 * @since 2.0.0
	 **/
	
	$(".adguru_toggler_dropdown").on('change', function(){
				
		ADGURU_ADMIN_HELPER.run_toggler( $( this ) );		
	
	});
	
	//fire on first time
	$(".adguru_toggler_dropdown").each(function(){
		ADGURU_ADMIN_HELPER.run_toggler( $( this ) );
	});
	
	
	//end Toggler elements

	$('.adguru-color-picker-field').wpColorPicker();

});//end jQuery(document).ready