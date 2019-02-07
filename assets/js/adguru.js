/**
Ad Guru JS for front-end usage
@author: oneTarek
@since 2.0.0
*/
;

//IE8 doesnt support the trim function. But you can define it like this: http://stackoverflow.com/questions/11219731/trim-function-doesnt-work-in-ie8
if(typeof String.prototype.trim !== 'function') {
  String.prototype.trim = function() {
	return this.replace(/^\s+|\s+$/g, ''); 
  }
}	
//ADGURU HELPER OBJECT FOR COMMON FUNCTIONS
var ADGURU_HELPER = {

	set_cookie : function ( cname, cvalue, exdays, path ){
		path = path || '/';
		var d = new Date();
		d.setTime( d.getTime()+( exdays*24*60*60*1000 ) );
		var expires = "expires="+d.toGMTString();
		document.cookie = cname + "=" + cvalue + "; " + expires + "; path="+path;
	},

	get_cookie : function ( cname ){
		var name = cname + "=";
		var ca = document.cookie.split(';');
		for(var i=0; i<ca.length; i++)
		  {
		  var c = ca[i].trim();
		  if (c.indexOf(name)==0) return c.substring(name.length,c.length);
		  }
		return "";
	}

};//end ADGURU_HELPER

//MAIN ADGURU OBJECT
var ADGURU = null;
(function($){
	ADGURU = {
		init : function(){
			$('.adguru_ad_slider').each(function(){
				var options =  $(this).data('options');
				$(this).simplecarousel({
					width : options.width,
					height : options.height,
					auto: options.auto,
					vertical: options.vertical,
					pagination: options.pagination
				});	
			});
		}
	}//end ADGURU
	
	$(document).ready(function(){
		ADGURU.init();
	});
})(jQuery)
