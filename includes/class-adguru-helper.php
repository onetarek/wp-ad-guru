<?php
/**
 * Ad Guru Helper class. All methods of this class are static. 
 * Do not make instance of this class. 
 * Use all helper functions directly using class name like ADGURU_Helper::method_name()
 * @author oneTarek
 * @since 2.0.0
 **/

use GeoIp2\Database\Reader;

// Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'ADGURU_Helper' ) ) :

class ADGURU_Helper{

	public static function current_page_url(){
		if( isset( $_SERVER['REQUEST_URI'] ) )
		{
			return home_url( $_SERVER['REQUEST_URI'] );
		}
		else
		{
			return home_url();
		}
		
	}
	
	public static function is_valid_url( $url ){

		//return ( ! preg_match('/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $url)) ? FALSE : TRUE;
		return ( ! preg_match('/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+|localhost):?(\d+)?\/?/i', $url)) ? FALSE : TRUE;
	}	

	public static function get_keywords_from_string( $s ){
		
		//$s=strip_tags($s);
		$s = strtolower( trim( $s ) );
		if( $s == "" ){ return false; }
		
		#remove stop words http://en.wikipedia.org/wiki/Stop_words   http://www.webconfs.com/stop-words.php
		$stop_words = array( 
			"able", "about", "above", "abroad", "according", "accordingly", "across", "actually", "adj", "after", "afterwards", "again", "against", "ago", "ahead", "ain't", "all", "allow", "allows", "almost", "alone", "along", "alongside", "already", "also", "although", "always", "am", "amid", "amidst", "among", "amongst", "an", "and", "another", "any", "anybody", "anyhow", "anyone", "anything", "anyway", "anyways", "anywhere", "apart", "appear", "appreciate", "appropriate", "are", "aren't", "around", "as", "a's", "aside", "ask", "asking", "associated", "at", "available", "away", "awfully", "back", "backward", "backwards", "be", "became", "because", "become", "becomes", "becoming", "been", "before", "beforehand", "begin", "behind", "being", "believe", "below", "beside", "besides", "best", "better", "between", "beyond", "both", "brief", "but", "by", "came", "can", "cannot", "cant", "can't", "caption", "cause", "causes", "certain", "certainly", "changes", "clearly", "c'mon", "co", "co.", "com", "come", "comes", "concerning", "consequently", "consider", "considering", "contain", "containing", "contains", "corresponding", "could", "couldn't", "course", "c's", "currently", "dare", "daren't", "definitely", "described", "despite", "did", "didn't", "different", "directly", "do", "does", "doesn't", "doing", "done", "don't", "down", "downwards", "during", "each", "edu", "eg", "eight", "eighty", "either", "else", "elsewhere", "end", "ending", "enough", "entirely", "especially", "et", "etc", "even", "ever", "evermore", "every", "everybody", "everyone", "everything", "everywhere", "ex", "exactly", "example", "except", "fairly", "far", "farther", "few", "fewer", "fifth", "first", "five", "followed", "following", "follows", "for", "forever", "former", "formerly", "forth", "forward", "found", "four", "from", "further", "furthermore", "get", "gets", "getting", "given", "gives", "go", "goes", "going", "gone", "got", "gotten", "greetings", "had", "hadn't", "half", "happens", "hardly", "has", "hasn't", "have", "haven't", "having", "he", "he'd", "he'll", "hello", "help", "hence", "her", "here", "hereafter", "hereby", "herein", "here's", "hereupon", "hers", "herself", "he's", "hi", "him", "himself", "his", "hither", "hopefully", "how", "howbeit", "however", "hundred", "i'd", "ie", "if", "ignored", "i'll", "i'm", "immediate", "in", "inasmuch", "inc", "inc.", "indeed", "indicate", "indicated", "indicates", "inner", "inside", "insofar", "instead", "into", "inward", "is", "isn't", "it", "it'd", "it'll", "its", "it's", "itself", "i've", "just", "k", "keep", "keeps", "kept", "know", "known", "knows", "last", "lately", "later", "latter", "latterly", "least", "less", "lest", "let", "let's", "like", "liked", "likely", "likewise", "little", "look", "looking", "looks", "low", "lower", "ltd", "made", "mainly", "make", "makes", "many", "may", "maybe", "mayn't", "me", "mean", "meantime", "meanwhile", "merely", "might", "mightn't", "mine", "minus", "miss", "more", "moreover", "most", "mostly", "mr", "mrs", "much", "must", "mustn't", "my", "myself", "name", "namely", "nd", "near", "nearly", "necessary", "need", "needn't", "needs", "neither", "never", "neverf", "neverless", "nevertheless", "new", "next", "nine", "ninety", "no", "nobody", "non", "none", "nonetheless", "noone", "no-one", "nor", "normally", "not", "nothing", "notwithstanding", "novel", "now", "nowhere", "obviously", "of", "off", "often", "oh", "ok", "okay", "old", "on", "once", "one", "ones", "one's", "only", "onto", "opposite", "or", "other", "others", "otherwise", "ought", "oughtn't", "our", "ours", "ourselves", "out", "outside", "over", "overall", "own", "particular", "particularly", "past", "per", "perhaps", "placed", "please", "plus", "possible", "presumably", "probably", "provided", "provides", "que", "quite", "qv", "rather", "rd", "re", "really", "reasonably", "recent", "recently", "regarding", "regardless", "regards", "relatively", "respectively", "right", "round", "said", "same", "saw", "say", "saying", "says", "second", "secondly", "see", "seeing", "seem", "seemed", "seeming", "seems", "seen", "self", "selves", "sensible", "sent", "serious", "seriously", "seven", "several", "shall", "shan't", "she", "she'd", "she'll", "she's", "should", "shouldn't", "since", "six", "so", "some", "somebody", "someday", "somehow", "someone", "something", "sometime", "sometimes", "somewhat", "somewhere", "soon", "sorry", "specified", "specify", "specifying", "still", "sub", "such", "sup", "sure", "take", "taken", "taking", "tell", "tends", "th", "than", "thank", "thanks", "thanx", "that", "that'll", "thats", "that's", "that've", "the", "their", "theirs", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "there'd", "therefore", "therein", "there'll", "there're", "theres", "there's", "thereupon", "there've", "these", "they", "they'd", "they'll", "they're", "they've", "thing", "things", "think", "third", "thirty", "this", "thorough", "thoroughly", "those", "though", "three", "through", "throughout", "thru", "thus", "till", "to", "together", "too", "took", "toward", "towards", "tried", "tries", "truly", "try", "trying", "t's", "twice", "two", "un", "under", "underneath", "undoing", "unfortunately", "unless", "unlike", "unlikely", "until", "unto", "up", "upon", "upwards", "us", "use", "used", "useful", "uses", "using", "usually", "v", "value", "various", "versus", "very", "via", "viz", "vs", "want", "wants", "was", "wasn't", "way", "we", "we'd", "welcome", "well", "we'll", "went", "were", "we're", "weren't", "we've", "what", "whatever", "what'll", "what's", "what've", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "where's", "whereupon", "wherever", "whether", "which", "whichever", "while", "whilst", "whither", "who", "who'd", "whoever", "whole", "who'll", "whom", "whomever", "who's", "whose", "why", "will", "willing", "wish", "with", "within", "without", "wonder", "won't", "would", "wouldn't", "yes", "yet", "you", "you'd", "you'll", "your", "you're", "yours", "yourself", "yourselves", "you've", "zero"
		);
		
		$key_list = explode( " " , $s );
		$keys_temp = array_diff( $key_list, $stop_words );
		$solid_keys = array();
		foreach( $keys_temp as $key )
		{
			$key = trim( $key );
			if( $key )
			{ 
				$solid_keys[] = $key;
			}
		}
		if( count( $solid_keys ) )
			return $solid_keys;
		else 
			return false;
	}#end func
	


	public static function get_country_list(){

		static $all_country_list;
		if( is_array( $all_country_list ) )
		{ 
			return $all_country_list; 
		}
		
		$country_list = array(
			"--" => "Select A Country",
			"AF" => "Afghanistan",
			"AX" => "Aland Islands",
			"AL" => "Albania",
			"DZ" => "Algeria",
			"AS" => "American Samoa",
			"AD" => "Andorra",
			"AO" => "Angola",
			"AI" => "Anguilla",
			"A1" => "Anonymous Proxy",
			"AQ" => "Antarctica",
			"AG" => "Antigua and Barbuda",
			"AR" => "Argentina",
			"AM" => "Armenia",
			"AW" => "Aruba",
			"AP" => "Asia/Pacific Region",
			"AU" => "Australia",
			"AT" => "Austria",
			"AZ" => "Azerbaijan",
			"BS" => "Bahamas",
			"BH" => "Bahrain",
			"BD" => "Bangladesh",
			"BB" => "Barbados",
			"BY" => "Belarus",
			"BE" => "Belgium",
			"BZ" => "Belize",
			"BJ" => "Benin",
			"BM" => "Bermuda",
			"BT" => "Bhutan",
			"BO" => "Bolivia",
			"BQ" => "Bonaire, Saint Eustatius and Saba",
			"BA" => "Bosnia and Herzegovina",
			"BW" => "Botswana",
			"BV" => "Bouvet Island",
			"BR" => "Brazil",
			"IO" => "British Indian Ocean Territory",
			"BN" => "Brunei Darussalam",
			"BG" => "Bulgaria",
			"BF" => "Burkina Faso",
			"BI" => "Burundi",
			"KH" => "Cambodia",
			"CM" => "Cameroon",
			"CA" => "Canada",
			"CV" => "Cape Verde",
			"KY" => "Cayman Islands",
			"CF" => "Central African Republic",
			"TD" => "Chad",
			"CL" => "Chile",
			"CN" => "China",
			"CX" => "Christmas Island",
			"CC" => "Cocos (Keeling) Islands",
			"CO" => "Colombia",
			"KM" => "Comoros",
			"CG" => "Congo",
			"CD" => "Congo, The Democratic Republic of the",
			"CK" => "Cook Islands",
			"CR" => "Costa Rica",
			"CI" => "Cote D'Ivoire",
			"HR" => "Croatia",
			"CU" => "Cuba",
			"CW" => "Curacao",
			"CY" => "Cyprus",
			"CZ" => "Czech Republic",
			"DK" => "Denmark",
			"DJ" => "Djibouti",
			"DM" => "Dominica",
			"DO" => "Dominican Republic",
			"EC" => "Ecuador",
			"EG" => "Egypt",
			"SV" => "El Salvador",
			"GQ" => "Equatorial Guinea",
			"ER" => "Eritrea",
			"EE" => "Estonia",
			"ET" => "Ethiopia",
			"EU" => "Europe",
			"FK" => "Falkland Islands (Malvinas)",
			"FO" => "Faroe Islands",
			"FJ" => "Fiji",
			"FI" => "Finland",
			"FR" => "France",
			"GF" => "French Guiana",
			"PF" => "French Polynesia",
			"TF" => "French Southern Territories",
			"GA" => "Gabon",
			"GM" => "Gambia",
			"GE" => "Georgia",
			"DE" => "Germany",
			"GH" => "Ghana",
			"GI" => "Gibraltar",
			"GR" => "Greece",
			"GL" => "Greenland",
			"GD" => "Grenada",
			"GP" => "Guadeloupe",
			"GU" => "Guam",
			"GT" => "Guatemala",
			"GG" => "Guernsey",
			"GN" => "Guinea",
			"GW" => "Guinea-Bissau",
			"GY" => "Guyana",
			"HT" => "Haiti",
			"HM" => "Heard Island and McDonald Islands",
			"VA" => "Holy See (Vatican City State)",
			"HN" => "Honduras",
			"HK" => "Hong Kong",
			"HU" => "Hungary",
			"IS" => "Iceland",
			"IN" => "India",
			"ID" => "Indonesia",
			"IR" => "Iran, Islamic Republic of",
			"IQ" => "Iraq",
			"IE" => "Ireland",
			"IM" => "Isle of Man",
			"IL" => "Israel",
			"IT" => "Italy",
			"JM" => "Jamaica",
			"JP" => "Japan",
			"JE" => "Jersey",
			"JO" => "Jordan",
			"KZ" => "Kazakhstan",
			"KE" => "Kenya",
			"KI" => "Kiribati",
			"KP" => "Korea, Democratic People's Republic of",
			"KR" => "Korea, Republic of",
			"KW" => "Kuwait",
			"KG" => "Kyrgyzstan",
			"LA" => "Lao People's Democratic Republic",
			"LV" => "Latvia",
			"LB" => "Lebanon",
			"LS" => "Lesotho",
			"LR" => "Liberia",
			"LY" => "Libya",
			"LI" => "Liechtenstein",
			"LT" => "Lithuania",
			"LU" => "Luxembourg",
			"MO" => "Macau",
			"MK" => "Macedonia",
			"MG" => "Madagascar",
			"MW" => "Malawi",
			"MY" => "Malaysia",
			"MV" => "Maldives",
			"ML" => "Mali",
			"MT" => "Malta",
			"MH" => "Marshall Islands",
			"MQ" => "Martinique",
			"MR" => "Mauritania",
			"MU" => "Mauritius",
			"YT" => "Mayotte",
			"MX" => "Mexico",
			"FM" => "Micronesia, Federated States of",
			"MD" => "Moldova, Republic of",
			"MC" => "Monaco",
			"MN" => "Mongolia",
			"ME" => "Montenegro",
			"MS" => "Montserrat",
			"MA" => "Morocco",
			"MZ" => "Mozambique",
			"MM" => "Myanmar",
			"NA" => "Namibia",
			"NR" => "Nauru",
			"NP" => "Nepal",
			"NL" => "Netherlands",
			"NC" => "New Caledonia",
			"NZ" => "New Zealand",
			"NI" => "Nicaragua",
			"NE" => "Niger",
			"NG" => "Nigeria",
			"NU" => "Niue",
			"NF" => "Norfolk Island",
			"MP" => "Northern Mariana Islands",
			"NO" => "Norway",
			"OM" => "Oman",
			"O1" => "Other",
			"PK" => "Pakistan",
			"PW" => "Palau",
			"PS" => "Palestinian Territory",
			"PA" => "Panama",
			"PG" => "Papua New Guinea",
			"PY" => "Paraguay",
			"PE" => "Peru",
			"PH" => "Philippines",
			"PN" => "Pitcairn Islands",
			"PL" => "Poland",
			"PT" => "Portugal",
			"PR" => "Puerto Rico",
			"QA" => "Qatar",
			"RE" => "Reunion",
			"RO" => "Romania",
			"RU" => "Russian Federation",
			"RW" => "Rwanda",
			"BL" => "Saint Barthelemy",
			"SH" => "Saint Helena",
			"KN" => "Saint Kitts and Nevis",
			"LC" => "Saint Lucia",
			"MF" => "Saint Martin",
			"PM" => "Saint Pierre and Miquelon",
			"VC" => "Saint Vincent and the Grenadines",
			"WS" => "Samoa",
			"SM" => "San Marino",
			"ST" => "Sao Tome and Principe",
			"A2" => "Satellite Provider",
			"SA" => "Saudi Arabia",
			"SN" => "Senegal",
			"RS" => "Serbia",
			"SC" => "Seychelles",
			"SL" => "Sierra Leone",
			"SG" => "Singapore",
			"SX" => "Sint Maarten (Dutch part)",
			"SK" => "Slovakia",
			"SI" => "Slovenia",
			"SB" => "Solomon Islands",
			"SO" => "Somalia",
			"ZA" => "South Africa",
			"GS" => "South Georgia and the South Sandwich Islands",
			"SS" => "South Sudan",
			"ES" => "Spain",
			"LK" => "Sri Lanka",
			"SD" => "Sudan",
			"SR" => "Suriname",
			"SJ" => "Svalbard and Jan Mayen",
			"SZ" => "Swaziland",
			"SE" => "Sweden",
			"CH" => "Switzerland",
			"SY" => "Syrian Arab Republic",
			"TW" => "Taiwan",
			"TJ" => "Tajikistan",
			"TZ" => "Tanzania, United Republic of",
			"TH" => "Thailand",
			"TL" => "Timor-Leste",
			"TG" => "Togo",
			"TK" => "Tokelau",
			"TO" => "Tonga",
			"TT" => "Trinidad and Tobago",
			"TN" => "Tunisia",
			"TR" => "Turkey",
			"TM" => "Turkmenistan",
			"TC" => "Turks and Caicos Islands",
			"TV" => "Tuvalu",
			"UG" => "Uganda",
			"UA" => "Ukraine",
			"AE" => "United Arab Emirates",
			"GB" => "United Kingdom",
			"US" => "United States",
			"UM" => "United States Minor Outlying Islands",
			"UY" => "Uruguay",
			"UZ" => "Uzbekistan",
			"VU" => "Vanuatu",
			"VE" => "Venezuela",
			"VN" => "Vietnam",
			"VG" => "Virgin Islands, British",
			"VI" => "Virgin Islands, U.S.",
			"WF" => "Wallis and Futuna",
			"EH" => "Western Sahara",
			"YE" => "Yemen",
			"ZM" => "Zambia",
			"ZW" => "Zimbabwe"

		);
		
		/*
		COUNTRY LIST SHORTING ORDER
		--------------first show mejor 5 countries------------------- 
		United States | US
		United Kingdom | GB
		Canada | CA
		Australia | AU
		New Zealand | NZ
		--------THEN------------
		A
		B
		C...
		*/
		//asort( $country_list );
		
		$country_list_new = array();
		$country_list_new['--']=$country_list['--'];
		$country_list_new['US']=$country_list['US'];
		$country_list_new['GB']=$country_list['GB'];
		$country_list_new['CA']=$country_list['CA'];
		$country_list_new['AU']=$country_list['AU'];
		$country_list_new['NZ']=$country_list['NZ'];

		foreach( $country_list as $key => $val )
		{
			$country_list_new[ $key ] = $val;
		}
	
		$all_country_list = $country_list_new;
		return $country_list_new;
	}//END FUNC

	public static function get_visitor_country_code()
	{	
		static $visitor_country_code;
		
		if( isset( $visitor_country_code ) )
		{
			return $visitor_country_code;
		}
		elseif( isset( $_SESSION['adg_visitor_country_code'] ) )
		{
			$visitor_country_code = $_SESSION['adg_visitor_country_code'];
			return $visitor_country_code;
		}
		else
		{
			$visitor_country_code = "";
			require_once ADGURU_PLUGIN_DIR.'/libs/geoip/GeoIP2-php/vendor/autoload.php';
			//Creates the Reader object.
			//use GeoIp2\Database\Reader;
			$reader = new Reader(ADGURU_PLUGIN_DIR.'/libs/geoip/database/GeoLite2-Country.mmdb');
			try
			{
				$record = $reader->country( $_SERVER['REMOTE_ADDR'] );
				$visitor_country_code =  $record->country->isoCode;
			}
			catch( Exception $e )
			{
				//echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
			$_SESSION['adg_visitor_country_code'] = $visitor_country_code;
			
		}
		return $visitor_country_code;
	}//END FUNC		
	
	/**
	 * Convert hexadecimal color to rgb or grba color syntax 
	 * @param string hex color code
	 * @param float opacity value of rgba color
	 * @return string 
	 **/
	public static function hexToRgba($color, $opacity = false)
	{
		$default = 'rgb(0,0,0)';

		//Return default if no color provided
		if(empty($color)) 
		{
			return $default;
		}

		//Sanitize $color if "#" is provided
		if($color[0] == '#')
		{
			$color = substr($color, 1);
		}

		//Check if color has 6 or 3 characters and get values
		if(strlen($color) == 6) 
		{
			$hex = array($color[0].$color[1], $color[2].$color[3], $color[4].$color[5]);
		}
		elseif(strlen($color) == 3)
		{
			$hex = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
		}
		else
		{
			return $default;
		}

		//Convert hexadecimal to rgb
		$rgb = array_map('hexdec', $hex);

		//if opacity is set create rgba else rgb
		if ($opacity !== false)
		{
			if(abs($opacity) > 1)
			{
				$opacity = 1.0;
			}
			$output = 'rgba('.implode(',', $rgb).','.$opacity.')';
		}
		else
		{
			$output = 'rgb('.implode(',', $rgb).')';
		}

		//Return rgb(a) color string
		return $output;
	}

	/**
	 * Repalce string from the beginning of another sting
	 * @param string search
	 * @param string replace
	 * @param string str
	 * @return string subject
	 */ 
	public static function str_replace_beginning($search, $replace, $subject){
		if( strpos($subject, $search) === 0 )
		{
			return $replace.substr($subject, strlen($search) );
		}
		else
		{
			return $subject;
		}
	}

	/**
	 * List of commonly Used Font name list. https://www.w3schools.com/cssref/css_websafe_fonts.asp
	 * @return array
	 **/
	public static function get_common_font_list(){
		$fonts = array(
			'Arial' => 'Arial',
			'Arial Black' => 'Arial Black',
			'Comic Sans MS' => 'Comic Sans MS',
			'Courier New' => 'Courier New',
			'Georgia' => 'Georgia',
			'Impact' => 'Impact',
			'Lucida Sans Unicode' => 'Lucida Sans Unicode',
			'Lucida Console' => 'Lucida Console',
			'Palatino Linotype' => 'Palatino Linotype',
			'Tahoma' => 'Tahoma',
			'Times New Roman' => 'Times New Roman',
			'Trebuchet MS' => 'Trebuchet MS',
			'Verdana' => 'Verdana',
		);
		return $fonts;
	}

	/**
	 * Get font family value to used in CSS, 
	 * The font-family property should hold several font names as a "fallback" system, to ensure maximum compatibility between browsers/operating systems. If the browser does not support the first font, it tries the next font.
	 * https://www.w3schools.com/cssref/css_websafe_fonts.asp
	 * @param string font name
	 * @return string font family
	 */

	public static function get_font_family_with_fallback( $font ){
		if( $font == ""){ return "";}
		$font_families = array(
			'Arial' => 'Arial, Helvetica, sans-serif',
			'Arial Black' => '"Arial Black", Gadget, sans-serif',
			'Comic Sans MS' => '"Comic Sans MS", cursive, sans-serif',
			'Courier New' => '"Courier New", Courier, monospace',
			'Georgia' => 'Georgia, serif',
			'Impact' => 'Impact, Charcoal, sans-serif',
			'Lucida Sans Unicode' => '"Lucida Sans Unicode", "Lucida Grande", sans-serif',
			'Lucida Console' => '"Lucida Console", Monaco, monospace',
			'Palatino Linotype' => '"Palatino Linotype", "Book Antiqua", Palatino, serif',
			'Tahoma' => 'Tahoma, Geneva, sans-serif',
			'Times New Roman' => '"Times New Roman", Times, serif',
			'Trebuchet MS' => '"Trebuchet MS", Helvetica, sans-serif',
			'Verdana' => 'Verdana, Geneva, sans-serif',
		);
		return isset( $font_families[$font] ) ? $font_families[$font] : $font;
	}

	/**
	 * Get close icon list 
	 * @return array
	 **/
	public static function get_close_icon_list( $file_type = 'any' ){
		
		static $icons;
		if( !is_array( $icons ) )
		{

			$png_icons = array(
				'core_close_default_png' => array('label'=>'', 'desc'=>'', 'filename'=>'close-default.png', 'filetype'=>'png', 'url'=>ADGURU_PLUGIN_URL .'assets/images/close-icons/close-default.png'),
				'core_close_circle_cross_white_red_shadow_png' => array('label'=>'', 'desc'=>'', 'filename'=>'close-circle-cross-white-red-shadow.png', 'filetype'=>'png', 'url'=>ADGURU_PLUGIN_URL .'assets/images/close-icons/close-circle-cross-white-red-shadow.png'),
				'core_close_circle_cross_white_black_png' => array('label'=>'', 'desc'=>'', 'filename'=>'close-circle-cross-white-black.png', 'filetype'=>'png', 'url'=>ADGURU_PLUGIN_URL .'assets/images/close-icons/close-circle-cross-white-black.png'),
				'core_close_circle_cross_white_red_png' => array('label'=>'', 'desc'=>'', 'filename'=>'close-circle-cross-white-red.png', 'filetype'=>'png', 'url'=>ADGURU_PLUGIN_URL .'assets/images/close-icons/close-circle-cross-white-red.png'),
				'core_close_circle_cross_white_transparent_png' => array('label'=>'', 'desc'=>'', 'filename'=>'close-circle-cross-white-transparent.png', 'filetype'=>'png', 'url'=>ADGURU_PLUGIN_URL .'assets/images/close-icons/close-circle-cross-white-transparent.png'),
				'core_close_circle_cross_red_transparent_png' => array('label'=>'', 'desc'=>'', 'filename'=>'close-circle-cross-red-transparent.png', 'filetype'=>'png', 'url'=>ADGURU_PLUGIN_URL .'assets/images/close-icons/close-circle-cross-red-transparent.png'),
				'core_close_cross_thin_black_png' => array('label'=>'', 'desc'=>'', 'filename'=>'close-cross-thin-black.png', 'filetype'=>'png', 'url'=>ADGURU_PLUGIN_URL .'assets/images/close-icons/close-cross-thin-black.png'),
				'core_close_cross_thin_white_png' => array('label'=>'', 'desc'=>'', 'filename'=>'close-cross-thin-white.png', 'filetype'=>'png', 'url'=>ADGURU_PLUGIN_URL .'assets/images/close-icons/close-cross-thin-white.png'),
				'core_close_cross_thin_grey_png' => array('label'=>'', 'desc'=>'', 'filename'=>'close-cross-thin-grey.png', 'filetype'=>'png', 'url'=>ADGURU_PLUGIN_URL .'assets/images/close-icons/close-cross-thin-grey.png'),
				'core_close_cross_thin_red_png' => array('label'=>'', 'desc'=>'', 'filename'=>'close-cross-thin-red.png', 'filetype'=>'png', 'url'=>ADGURU_PLUGIN_URL .'assets/images/close-icons/close-cross-thin-red.png'),
				'core_close_cross_double_white_red_png' => array('label'=>'', 'desc'=>'', 'filename'=>'close-cross-double-white-red.png', 'filetype'=>'png', 'url'=>ADGURU_PLUGIN_URL .'assets/images/close-icons/close-cross-double-white-red.png'),
				'core_close_cross_fancy_1_red_png' => array('label'=>'', 'desc'=>'', 'filename'=>'close-cross-fancy-1-red.png', 'filetype'=>'png', 'url'=>ADGURU_PLUGIN_URL .'assets/images/close-icons/close-cross-fancy-1-red.png'),
				'core_close_cross_fancy_2_red_png' => array('label'=>'', 'desc'=>'', 'filename'=>'close-cross-fancy-2-red.png', 'filetype'=>'png', 'url'=>ADGURU_PLUGIN_URL .'assets/images/close-icons/close-cross-fancy-2-red.png')
			);
			
			$svg_icons = array();

			$png_icons = apply_filters('adguru_close_png_icon_list', $png_icons );
			$svg_icons = apply_filters('adguru_close_svg_icon_list', $svg_icons );
			$icons = array( 'png'=>$png_icons, 'svg'=>$svg_icons );

		}//end if !is_array( $icons )

		if( $file_type == 'png')
		{ 
			return $icons['png']; 
		}
		elseif( $file_type == 'svg' )
		{
			return $icons['svg']; 
		}
		else
		{
			return $icons;
		}
		
	}

	/**
	 * Get close icon 
	 * @return array or false
	 **/
	public static function get_close_icon( $name ){

		$icons = self::get_close_icon_list();
		if( isset( $icons['png'][$name] ) )
		{
			return $icons['png'][$name];
		}
		elseif( isset( $icons['svg'][$name] ) )
		{
			return $icons['png'][$name];
		}
		else
		{
			return false;
		}
	}

	/**
	 * Checks if a string can be used as a variable name
	 * @param string
	 * @return bool
	 */
	public static function is_valid_variable_name( $name ){
		
		return ( 1 === preg_match('/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/', $name) ) ? true : false;
	}

	/**
	 * Retrieve all registred post types those have real user facing usages.
	 * @since 2.1.0 was in ADGURU_Ad_Setup_Manager class
	 * @since 2.2.0 Moved to this class
	 */

	public static function get_post_type_list(){
		#retrieve all registred post types.
		$post_types = get_post_types( '', 'names' ); 		

		#remove post types those are used for internal usage by WordPress.
		$rempost = array( 'attachment', 'revision', 'nav_menu_item' );
		$post_types = array_diff( $post_types, $rempost );	

		#remove post types those are being used by ADGURU itself.
		$post_types = array_diff( $post_types, adguru()->post_types->types );	

		#remove post types those has no UI, means those are beings used for internal usages only.
		foreach( $post_types as $key => $val )
		{
			$ptobj = get_post_type_object( $key );
			if( !$ptobj->show_ui ) 
			{ 
				unset( $post_types[ $key ] );
			}
			else
			{
				#capitalize first char of name
				$post_types[ $key ]	= ucfirst( $val );
			}
		}

		return $post_types;

	}

	/**
	 * Retrieve registred taxonomies those have real user facing usages.
	 * @since 2.1.0 was in ADGURU_Ad_Setup_Manager class
	 * @since 2.2.0 Moved to this class
	 */
	public static function get_taxonomy_list(){
		static $taxonomy_list;
		if( isset( $taxonomy_list ) )
		{	
			return $taxonomy_list;
		}

		$taxonomies = get_taxonomies(array(), 'objects');
		
		$remTax = array( "nav_menu", "link_category", "post_format", "single", "Single" ); #we remove "single" because it a reserve word for this plugin. This word "Single" we are using to store as a taxonomy for when  post types are stored as terms.	
		
		foreach( $taxonomies as $key => $taxobj )
		{
			if( in_array($key, $remTax ) )#remove taxonomies those are being used only for internal usages. Those object/post_types does not have show UI.
			{
				unset( $taxonomies[ $key ] );
				continue;
			}
			
			if( !isset( $taxobj->object_type ) || !is_array( $taxobj->object_type ) )
			{ 
				unset( $taxonomies[ $key ] );
				continue;  
			}

			foreach( $taxobj->object_type  as $object_type )
			{
				$ptobj = get_post_type_object( $object_type );
				if( !$ptobj->show_ui )
				{ 
					unset( $taxonomies[ $key ] ); 
					break;
				}
			}
		}
		$taxonomy_list = $taxonomies;
		return $taxonomies;
	}

}//end class

endif;