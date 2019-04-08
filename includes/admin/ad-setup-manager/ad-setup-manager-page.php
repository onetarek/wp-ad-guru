<?php 
$page = $_REQUEST['page'];
$all_ad_type_args = adguru()->ad_types->types;

$current_ad_type = isset( $_GET['ad_type'] ) ? $_GET['ad_type'] : 'banner';
if(! isset( $all_ad_type_args[ $current_ad_type ] ) )
{
	return ;
}
else
{
	$current_ad_type_args = $all_ad_type_args[ $current_ad_type ];
}

$use_zone = isset( $current_ad_type_args['use_zone'] ) ? $current_ad_type_args['use_zone'] : false;
$zone_id = isset( $_GET['zone_id'] ) ? intval( $_GET['zone_id'] ) : 0 ; 
if( $use_zone ){ $editor_title = sprintf( __("Setup %s to Zone", "adguru" ) , $current_ad_type_args['plural_name'] );  } else {  $editor_title =  sprintf( __("Setup %s to pages", "adguru" ) , $current_ad_type_args['plural_name'] ); }

?>
<div class="wrap" id="ad_setup_manger_wrap">
	<h2><?php _e( "Setup Ads", "adguru" ); ?></h2>

	<?php do_action( "adguru_ad_setup_manager_top" , $current_ad_type_args ); ?>
	<?php do_action( "adguru_ad_setup_manager_top_{$current_ad_type}" , $current_ad_type_args ); ?>

	<h2 class="nav-tab-wrapper">
		<?php 
		foreach( $all_ad_type_args as $key => $args )
		{ 
			$tab_class = ( $key == $current_ad_type  )? 'nav-tab nav-tab-active' : 'nav-tab';
			$tab_link = admin_url( 'admin.php?page=adguru_setup_ads&ad_type='.$key );
		?>
		<a class='<?php echo $tab_class?>' href="<?php echo $tab_link ?>"><?php echo $args['name'] ?></a>
		<?php }?>
	</h2>

		
	<?php do_action( "adguru_ad_setup_manager_after_tabs" , $current_ad_type_args ); ?>
	<?php do_action( "adguru_ad_setup_manager_after_tabs_{$current_ad_type}" , $current_ad_type_args ); ?>

	<div id="editor_container">
		<div id="editor_title"><?php echo $editor_title ?></div>
		<?php 
		#Print Zone select dropdown if current ad type uses zone
		if( $use_zone ){

			$zones = adguru()->manager->get_zones();
			?>
			<div>
				<form action="" method="get">
					<input type="hidden" name="page" value="<?php echo $page ?>" />
					<input type="hidden" name="ad_type" value="<?php echo $current_ad_type ?>" />
					<strong><?php _e( 'Zone', 'adguru' )?> : </strong> 
					<select id="zone_id_list" name="zone_id" onchange="this.form.submit()">
						<option value="0" <?php echo ( $zone_id == 0 ) ? ' selected="selected" ': ""  ?>><?php echo __( "Select A Zone", "adguru" ) ?></option>
						<?php 
						$valid_zone_id = false;
						foreach($zones as $zone)
						{
							$selected = '';
							$class = '';
							if( $zone->active !=1 ){ $class = ' class="inactive" '; }
							if( $zone_id == $zone->ID ){ $selected = ' selected="selected" '; $valid_zone_id = true; }
							echo '<option value="'.$zone->ID.'"'.$class.$selected.'>'.$zone->name.' - '.$zone->width.'x'.$zone->height.'</option>';
						}
						?>
					</select>
				</form>
			</div>
			<?php 

		}//end if( $use_zone )
		?>
		<div id="condition_sets_box">
			<div class="condition-set">
				<div class="set-header">
					<span class="ec-btn" title="Edit page type"></span>
					<span class="page-type-display-box"><span class="page-type-display-text">Archive &raquo; Tag &raquo;</span><input type="text" class="term-name" size="12" placeholder="Term name/slug" /><span>
					<div class="cs-box">

						<select class="country-select" style="width: 321px;"><option value="--">---Select A Country---</option><option value="US">United States</option><option value="GB">United Kingdom</option><option value="CA">Canada</option><option value="AU">Australia</option><option value="NZ">New Zealand</option><optgroup label="- - - - - - - - - - - - - - - - - - - - - - - -"></optgroup><option value="AF">Afghanistan</option><option value="AX">Aland Islands</option><option value="AL">Albania</option><option value="DZ">Algeria</option><option value="AS">American Samoa</option><option value="AD">Andorra</option><option value="AO">Angola</option><option value="AI">Anguilla</option><option value="A1">Anonymous Proxy</option><option value="AQ">Antarctica</option><option value="AG">Antigua and Barbuda</option><option value="AR">Argentina</option><option value="AM">Armenia</option><option value="AW">Aruba</option><option value="AP">Asia/Pacific Region</option><option value="AT">Austria</option><option value="AZ">Azerbaijan</option><option value="BS">Bahamas</option><option value="BH">Bahrain</option><option value="BD">Bangladesh</option><option value="BB">Barbados</option><option value="BY">Belarus</option><option value="BE">Belgium</option><option value="BZ">Belize</option><option value="BJ">Benin</option><option value="BM">Bermuda</option><option value="BT">Bhutan</option><option value="BO">Bolivia</option><option value="BQ">Bonaire, Saint Eustatius and Saba</option><option value="BA">Bosnia and Herzegovina</option><option value="BW">Botswana</option><option value="BV">Bouvet Island</option><option value="BR">Brazil</option><option value="IO">British Indian Ocean Territory</option><option value="BN">Brunei Darussalam</option><option value="BG">Bulgaria</option><option value="BF">Burkina Faso</option><option value="BI">Burundi</option><option value="KH">Cambodia</option><option value="CM">Cameroon</option><option value="CV">Cape Verde</option><option value="KY">Cayman Islands</option><option value="CF">Central African Republic</option><option value="TD">Chad</option><option value="CL">Chile</option><option value="CN">China</option><option value="CX">Christmas Island</option><option value="CC">Cocos (Keeling) Islands</option><option value="CO">Colombia</option><option value="KM">Comoros</option><option value="CG">Congo</option><option value="CD">Congo, The Democratic Republic of the</option><option value="CK">Cook Islands</option><option value="CR">Costa Rica</option><option value="CI">Cote D'Ivoire</option><option value="HR">Croatia</option><option value="CU">Cuba</option><option value="CW">Curacao</option><option value="CY">Cyprus</option><option value="CZ">Czech Republic</option><option value="DK">Denmark</option><option value="DJ">Djibouti</option><option value="DM">Dominica</option><option value="DO">Dominican Republic</option><option value="EC">Ecuador</option><option value="EG">Egypt</option><option value="SV">El Salvador</option><option value="GQ">Equatorial Guinea</option><option value="ER">Eritrea</option><option value="EE">Estonia</option><option value="ET">Ethiopia</option><option value="EU">Europe</option><option value="FK">Falkland Islands (Malvinas)</option><option value="FO">Faroe Islands</option><option value="FJ">Fiji</option><option value="FI">Finland</option><option value="FR">France</option><option value="GF">French Guiana</option><option value="PF">French Polynesia</option><option value="TF">French Southern Territories</option><option value="GA">Gabon</option><option value="GM">Gambia</option><option value="GE">Georgia</option><option value="DE">Germany</option><option value="GH">Ghana</option><option value="GI">Gibraltar</option><option value="GR">Greece</option><option value="GL">Greenland</option><option value="GD">Grenada</option><option value="GP">Guadeloupe</option><option value="GU">Guam</option><option value="GT">Guatemala</option><option value="GG">Guernsey</option><option value="GN">Guinea</option><option value="GW">Guinea-Bissau</option><option value="GY">Guyana</option><option value="HT">Haiti</option><option value="HM">Heard Island and McDonald Islands</option><option value="VA">Holy See (Vatican City State)</option><option value="HN">Honduras</option><option value="HK">Hong Kong</option><option value="HU">Hungary</option><option value="IS">Iceland</option><option value="IN">India</option><option value="ID">Indonesia</option><option value="IR">Iran, Islamic Republic of</option><option value="IQ">Iraq</option><option value="IE">Ireland</option><option value="IM">Isle of Man</option><option value="IL">Israel</option><option value="IT">Italy</option><option value="JM">Jamaica</option><option value="JP">Japan</option><option value="JE">Jersey</option><option value="JO">Jordan</option><option value="KZ">Kazakhstan</option><option value="KE">Kenya</option><option value="KI">Kiribati</option><option value="KP">Korea, Democratic People's Republic of</option><option value="KR">Korea, Republic of</option><option value="KW">Kuwait</option><option value="KG">Kyrgyzstan</option><option value="LA">Lao People's Democratic Republic</option><option value="LV">Latvia</option><option value="LB">Lebanon</option><option value="LS">Lesotho</option><option value="LR">Liberia</option><option value="LY">Libya</option><option value="LI">Liechtenstein</option><option value="LT">Lithuania</option><option value="LU">Luxembourg</option><option value="MO">Macau</option><option value="MK">Macedonia</option><option value="MG">Madagascar</option><option value="MW">Malawi</option><option value="MY">Malaysia</option><option value="MV">Maldives</option><option value="ML">Mali</option><option value="MT">Malta</option><option value="MH">Marshall Islands</option><option value="MQ">Martinique</option><option value="MR">Mauritania</option><option value="MU">Mauritius</option><option value="YT">Mayotte</option><option value="MX">Mexico</option><option value="FM">Micronesia, Federated States of</option><option value="MD">Moldova, Republic of</option><option value="MC">Monaco</option><option value="MN">Mongolia</option><option value="ME">Montenegro</option><option value="MS">Montserrat</option><option value="MA">Morocco</option><option value="MZ">Mozambique</option><option value="MM">Myanmar</option><option value="NA">Namibia</option><option value="NR">Nauru</option><option value="NP">Nepal</option><option value="NL">Netherlands</option><option value="NC">New Caledonia</option><option value="NI">Nicaragua</option><option value="NE">Niger</option><option value="NG">Nigeria</option><option value="NU">Niue</option><option value="NF">Norfolk Island</option><option value="MP">Northern Mariana Islands</option><option value="NO">Norway</option><option value="OM">Oman</option><option value="O1">Other</option><option value="PK">Pakistan</option><option value="PW">Palau</option><option value="PS">Palestinian Territory</option><option value="PA">Panama</option><option value="PG">Papua New Guinea</option><option value="PY">Paraguay</option><option value="PE">Peru</option><option value="PH">Philippines</option><option value="PN">Pitcairn Islands</option><option value="PL">Poland</option><option value="PT">Portugal</option><option value="PR">Puerto Rico</option><option value="QA">Qatar</option><option value="RE">Reunion</option><option value="RO">Romania</option><option value="RU">Russian Federation</option><option value="RW">Rwanda</option><option value="BL">Saint Barthelemy</option><option value="SH">Saint Helena</option><option value="KN">Saint Kitts and Nevis</option><option value="LC">Saint Lucia</option><option value="MF">Saint Martin</option><option value="PM">Saint Pierre and Miquelon</option><option value="VC">Saint Vincent and the Grenadines</option><option value="WS">Samoa</option><option value="SM">San Marino</option><option value="ST">Sao Tome and Principe</option><option value="A2">Satellite Provider</option><option value="SA">Saudi Arabia</option><option value="SN">Senegal</option><option value="RS">Serbia</option><option value="SC">Seychelles</option><option value="SL">Sierra Leone</option><option value="SG">Singapore</option><option value="SX">Sint Maarten (Dutch part)</option><option value="SK">Slovakia</option><option value="SI">Slovenia</option><option value="SB">Solomon Islands</option><option value="SO">Somalia</option><option value="ZA">South Africa</option><option value="GS">South Georgia and the South Sandwich Islands</option><option value="SS">South Sudan</option><option value="ES">Spain</option><option value="LK">Sri Lanka</option><option value="SD">Sudan</option><option value="SR">Suriname</option><option value="SJ">Svalbard and Jan Mayen</option><option value="SZ">Swaziland</option><option value="SE">Sweden</option><option value="CH">Switzerland</option><option value="SY">Syrian Arab Republic</option><option value="TW">Taiwan</option><option value="TJ">Tajikistan</option><option value="TZ">Tanzania, United Republic of</option><option value="TH">Thailand</option><option value="TL">Timor-Leste</option><option value="TG">Togo</option><option value="TK">Tokelau</option><option value="TO">Tonga</option><option value="TT">Trinidad and Tobago</option><option value="TN">Tunisia</option><option value="TR">Turkey</option><option value="TM">Turkmenistan</option><option value="TC">Turks and Caicos Islands</option><option value="TV">Tuvalu</option><option value="UG">Uganda</option><option value="UA">Ukraine</option><option value="AE">United Arab Emirates</option><option value="UM">United States Minor Outlying Islands</option><option value="UY">Uruguay</option><option value="UZ">Uzbekistan</option><option value="VU">Vanuatu</option><option value="VE">Venezuela</option><option value="VN">Vietnam</option><option value="VG">Virgin Islands, British</option><option value="VI">Virgin Islands, U.S.</option><option value="WF">Wallis and Futuna</option><option value="EH">Western Sahara</option><option value="YE">Yemen</option><option value="ZM">Zambia</option><option value="ZW">Zimbabwe</option></select>
						
					</div>
					<div class="ac-box">
						<span class="ac-btn"></span>
					</div>
					
				</div>
				<div class="set-error-msg-box"><!-- Error message will go here --></div>
				<div class="set-body">
					<div class="page-type-list-box">
						<div class="page-type-list-box-inner">
							<span class="title">Select type of page</span>
							<ul class="page-type-list">
								<li class="usable">Home</li>
								<li>
									<span class="group-name">Single Page</span>
									<ul>
										<li class="usable">Any type post</li>
										<li class="usable">Post</li>
										<li class="usable">Page</li>
									</ul>
								</li>
								<li>
									<span class="group-name">Taxonomy Archive page</span>
									<ul>
										<li class="usable">Any Taxonomy page</li>
										<li>
											<span class="group-name">Category</span>
											<ul>
												<li class="usable">Any Cagegory</li>
												<li class="usable">Uncategorized</li>
												<li class="usable">Tutorial</li>
											</ul>
										</li>
										<li>
											<span class="group-name">Tag</span>
											<ul>
												<li class="usable">Any Tag</li>
												<li class="usable">Specific Tag</li>
											</ul>
										</li>
										
									</ul>
								</li>
								<li class="usable">Author Archive Page</li>
								<li class="usable">Search Result Page</li>
								<li class="usable">404 Page</li>
							</ul>
						</div>
						<div class="open-close-arrow-box"><span class="open-close-arrow"></span></div>
					</div>
					<div class="condition-detail">Banners for a single page where post type is post</div>
					<div class="slides-box">
						<div class="slide">
							<span class="slide-delete-btn" title="Delete this slide"></span>
							<div class="slide-header">
								Slide 1
								<span class="equal-btn" title="Click to fill all rotate fields with equal value"></span>
							</div>
							<div class="ads-box">
								<div class="ad">
									<div class="title">Amazon Ad 1 - 300x250</div>
									<div class="control-box">
										<span class="percentage-box"><span class="percentage-label">Rotate</span><input type="number" class="percentage" value="100" max="100" min="0"> %</span>
										<a class="edit-btn" href="#" target="_blank" title="Edit this ad"></a>
										<span class="remove-btn" title="Remove this ad"></span>
									</div>
								</div><!-- /.ad -->
								<div class="ad">
									<div class="title">Amazon Ad 2 - 300x250</div>
									<div class="control-box">
										<span class="percentage-box"><span class="percentage-label">Rotate</span><input type="number" class="percentage" value="100" max="100" min="0"> %</span>
										<a class="edit-btn" href="#" target="_blank" title="Edit this ad"></a>
										<span class="remove-btn" title="Remove this ad"></span>
									</div>
								</div><!-- /.ad -->
								<div class="ad">
									<div class="title">Amazon Ad 3 - 300x250</div>
									<div class="control-box">
										<span class="percentage-box"><span class="percentage-label">Rotate</span><input type="number" class="percentage" value="100" max="100" min="0"> %</span>
										<a class="edit-btn" href="#" target="_blank" title="Edit this ad"></a>
										<span class="remove-btn" title="Remove this ad"></span>
									</div>
									<div class="more">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</div>
								</div><!-- /.ad -->
							</div><!-- /.ads-box -->
							<div class="add-ad-btn-box"><span class="add-ad-btn">Add new banner</span></div>
						</div><!-- /.slide -->
						
						<div class="slide">
							<span class="slide-delete-btn" title="Delete this slide"></span>
							<div class="slide-header">
								Slide 2
								<span class="equal-btn" title="Click to fill all rotate fields with equal value"></span>
							</div>
							<div class="ads-box">
								<!-- add more ad here -->
							</div><!-- /.ads-box -->
							<div class="add-ad-btn-box"><span class="add-ad-btn">Add new banner</span></div>
						</div><!-- /.slide -->

					</div><!-- /.slides-box -->
					<div class="add-slide-btn-box"><span class="add-slide-btn">Add new slide</span></div>
					<div class="new-slide-btn-box"></div>
				</div><!-- /.set-body -->
				<div class="set-footer">
					<span class="save-btn" />Save</span>
					<span class="save-loading"><img src="<?php echo ADGURU_PLUGIN_URL ?>assets/images/loading32.gif" height="32" /></span>
					<span class="delete-set-loading"><img src="<?php echo ADGURU_PLUGIN_URL ?>assets/images/loading32.gif" height="32" /></span>
					<span class="delete-set-btn" title="Delete this set"></span>	
				</div><!-- /.set-footer -->
			</div>

			<div class="condition-set collapsed">
				<div class="set-header">
					<span class="ec-btn"></span>
					Single &gt; Post
					<div class="cs-box">

						<select class="country-select">
							<option>Select Country</option>
						</select>
						
					</div>
					<div class="ac-box">
						<span class="ac-btn"></span>
					</div>
				</div>
				<div class="set-body">Body</div>
				<div class="set-footer">Footer</div>
			</div>
			

		</div><!-- /#condition_sets_box -->
		<div id="add_condition_set_btn_box"><span id="add_condition_set_btn">Add New Ad Set &amp; Condition</span></div>



	</div><!-- end #editor_container -->

	<?php do_action( "adguru_ad_setup_manager_bottom_{$current_ad_type}" , $current_ad_type_args ); ?>
	<?php do_action( "adguru_ad_setup_manager_bottom" , $current_ad_type_args ); ?>

</div><!-- end .wrap -->

<style type="text/css">
	.hidden{
		display: none;
	}
	#wpcontent{
		background: #ffffff;
	}
	.nav-tab-wrapper .nav-tab-active{
		background: #ffffff;
		border-bottom: 1px solid #ffffff;
	}
	#editor_container{
		width: 100%;
		max-width: 1000px;
		min-height: 800px;
		
		box-sizing: border-box;
	}
	#editor_title{
		width: 100%;
		font-size: 20px;
		color: #000000;
		padding: 10px;
		text-align: center;
		box-sizing: border-box;
	}
	#zone_id_list option.inactive{ color:#cccccc;}

	.condition-set{
		width: 100%
		box-sizing: border-box;
		border: 1px solid #cccccc;
		border-radius: 7px;
		margin-bottom: 10px;
		background: #ffffff;
		position: relative;

	}
	.condition-set .set-header{
		min-height: 41px;
		padding-left: 5px;
		padding-top: 5px;
		font-size: 15px;
		line-height: 30px;
		background: #e5e5e5;
		color: #555555;
		border-top-left-radius: 7px;
		border-top-right-radius: 7px;
		border-bottom: 1px solid #cccccc;
		box-sizing: border-box;
		position: relative;
	}
	.condition-set .set-header .ec-btn{
		width: 30px;
		height: 30px;
		color: #49a0bc;
		display: inline-block;
		border-right: 1px solid #cccccc;
		cursor: pointer;
	}
	.condition-set .set-header .ec-btn::before{
		font-family: "dashicons";
	 	content: "\f540";
		display: inline-block;
		-webkit-font-smoothing: antialiased;
		font-weight: normal;
		vertical-align: top;
		font-size: 24px;
		line-height: 30px;
	}
	.condition-set .set-header .ec-btn:hover{
		color:#49bc52;
	}

	.set-header .page-type-display-box{
		display: inline-block;
	}
	.set-header .page-type-display-text{
		display: inline-block;
		font-size: 15px;
		line-height: 30px;
	}
	.set-header .term-name{
		width: 150px;
		margin: 0px;
		padding: 3px 5px;
		border: 1px solid #ddd;
		font-size: 15px;
		line-height: 18px;
		color: #32373c;
		background-color: #fff;
		box-sizing: border-box;
	}



	.condition-set .set-header .cs-box{
		width: 210px;
		position: absolute;
		top: 0;
		right: 40px;
		height: 40px;
		padding-top: 5px;
		padding-left: 5px;
		box-sizing: border-box;
	}
	.condition-set .set-header .ac-box{
		width: 40px;
		height: 40px;
		position: absolute;
		top: 0;
		right: 0px;
		border-left: 1px solid #cccccc;
		padding: 0px;
		box-sizing: border-box;
	}

	.condition-set .cs-box .country-select{
		width: 200px;
		max-width: 200px;
		height: 30px;
		border: 1px solid #ffffff;
		background: #fff;
		margin: 0;
		padding: 2px;
		overflow: hidden;
	}
	.condition-set .ac-box .ac-btn{
		width: 100%;
		display: inline-block;
		text-align: center;
		cursor: pointer;
	}

	.condition-set .ac-box .ac-btn::before{
		font-family: "dashicons";
	 	content: "\f343";
	 	color: #bbbbbb;
		display: inline-block;
		-webkit-font-smoothing: antialiased;
		font-weight: normal;
		vertical-align: top;
		font-size: 24px;
		line-height: 40px;
			
	}
	.condition-set.collapsed .ac-box .ac-btn::before{
		content: "\f347";
	}
	.condition-set .ac-box .ac-btn:hover::before{ color: #000000; }
	
	.condition-set .set-error-msg-box{
		font-size: 14px;
		line-height: 20px;
		padding: 5px;
		color: #ff0000;
		border-bottom: 1px solid #cccccc;
		text-align: center;
	}
	.condition-set .set-body{
		min-height: 300px;
		padding: 10px;
		position: relative;
	}
	.condition-set.collapsed .set-body{
		display: none;
	}
	.condition-set .set-body .condition-detail{
		font-size: 14px;
		line-height: 14px;
		margin-bottom: 10px;
	}

	.condition-set .slides-box{}
	.condition-set .slide{
		padding: 10px;
		padding-top: 3px;
		border:1px solid #eeeeee;
		margin-bottom: 10px;
		position: relative;
	}
	.condition-set .slide:hover{
		border-color:#cccccc;
	}
	.condition-set .slide .slide-header{
		position: relative;
		font-size: 13px;
		line-height: 21px;
		text-transform: uppercase;
		font-weight: bold;
		margin-bottom: 10px;
	}
	.condition-set .slide .slide-header .equal-btn{
		position: absolute;
		right: 102px;
		top: 0px;
		display: inline-block;
		height: 24px;
		width: 24px;
		text-align: center;
		color: #bbbbbb;
		-webkit-transform: rotate(90deg);
		-moz-transform: rotate(90deg);
		-o-transform: rotate(90deg);
		transform: rotate(90deg);
		cursor: pointer;
		border: 1px solid #ffffff;
	}
	.condition-set .slide .slide-header .equal-btn::before{
		font-family: "dashicons";
	 	content: "\f523";
		display: inline-block;
		-webkit-font-smoothing: antialiased;
		font-weight: normal;
		vertical-align: top;
		font-size: 20px;
		line-height: 24px;
	}
	.condition-set .slide:hover .slide-header .equal-btn{
		color: #000000;
		border: 1px solid aliceblue;
	}

	.condition-set .slide .slide-delete-btn{
		width: 30px;
		height: 30px;
		position: absolute;
		bottom: 0px;
		right: 0px;
		display: inline-block;
		text-align: center;
		vertical-align: top;
		cursor: pointer;
		color: #bbbbbb;
		border: 1px solid #ffffff;
	}
	.condition-set .slide .slide-delete-btn::before{
		font-family: "dashicons";
	 	content: "\f335";
		display: inline-block;
		-webkit-font-smoothing: antialiased;
		font-weight: normal;
		vertical-align: top;
		font-size: 30px;
		line-height: 30px;
	}
	.condition-set .slide:hover .slide-delete-btn{
		color: #ff0000;
		border: 1px solid aliceblue;
	}


	.condition-set .ads-box{
		position: relative;
	}
	.condition-set .ad{
		position: relative;
		font-size: 13px;
		line-height: 13px;
		font-weight: normal;
		padding: 8px;
		border: 1px solid #dddddd;
		border-left-color: #49a0bc;
		margin-bottom: 4px;
	}
	.condition-set .ad:hover{
		background: #f4f4f4;
	}
	.condition-set .title{
		font-size: 13px;
		line-height: 14px;
		font-weight: normal;
	}
	.condition-set .ad .more{
		font-size: 13px;
		line-height: 13px;
		color: #cccccc;
		margin-top: 10px;

	}
	.condition-set .ad .control-box{
		position: absolute;
		width: 190px;
		height: 30px;
		top: 0px;
		right: 0px;
		text-align: right;
	}
	.condition-set .ad .control-box .percentage-box{
		width: 112px;
		height: 30px;
		display: inline-block;
		font-size: 13px;
		line-height: 13px;
		vertical-align: top;
		text-align: left;
	}
	.percentage-label{
		color: #cccccc;
		font-size: 13px;
		line-height: 13px;
		display: inline-block;
		margin-right: 5px;

	}
	.percentage-box .percentage{
		width: 45px;
		font-size: 13px;
		line-height: 13px;
		padding: 3px;
		text-align: center;
		height: 24px;
		margin: 3px;
		margin-left: 0px;

	}
	.condition-set .ad .control-box .edit-btn{
		width: 30px;
		height: 30px;
		display: inline-block;
		text-align: center;
		vertical-align: top;
		cursor: pointer;
		color: #bbbbbb;
	}
	.condition-set .ad .control-box .edit-btn::before{
		font-family: "dashicons";
	 	content: "\f540";
		display: inline-block;
		-webkit-font-smoothing: antialiased;
		font-weight: normal;
		vertical-align: top;
		font-size: 16px;
		line-height: 30px;
	}
	.condition-set .ad:hover .control-box .edit-btn{
		color: #000000;
	}

	.condition-set .ad .control-box .remove-btn{
		width: 30px;
		height: 30px;
		display: inline-block;
		text-align: center;
		vertical-align: top;
		cursor: pointer;
		color: #bbbbbb;
	}
	.condition-set .ad .control-box .remove-btn::before{
		font-family: "dashicons";
	 	content: "\f335";
		display: inline-block;
		-webkit-font-smoothing: antialiased;
		font-weight: normal;
		vertical-align: top;
		font-size: 22px;
		line-height: 30px;
	}
	.condition-set .ad:hover .control-box  .remove-btn{
		color: #ff0000;
	}
	.condition-set .add-ad-btn-box{
		text-align: center;
		margin-top: 10px;
	}
	.condition-set .add-ad-btn{
		font-size: 13px;
		line-height: 20px;
		color: #bbbbbb;
		cursor: pointer;
	}
	.condition-set .add-ad-btn::before{
		font-family: "dashicons";
	 	content: "\f132";
		display: inline-block;
		-webkit-font-smoothing: antialiased;
		font-weight: normal;
		vertical-align: middle;
		font-size: 16px;
		line-height: 20px;
	}
	.condition-set .add-ad-btn:hover{
		color: #000000;
	}


	.condition-set .add-slide-btn-box{
		text-align: center;
		margin-top: 10px;
	}
	.condition-set .add-slide-btn{
		font-size: 16px;
		line-height: 20px;
		color: #bbbbbb;
		cursor: pointer;
	}
	.condition-set .add-slide-btn::before{
		font-family: "dashicons";
	 	content: "\f132";
		display: inline-block;
		-webkit-font-smoothing: antialiased;
		font-weight: normal;
		vertical-align: middle;
		font-size: 18px;
		line-height: 20px;
	}
	.condition-set .add-slide-btn:hover{
		color: #000000;
	}


	.condition-set .set-footer{
		height: 32px;
		padding: 10px;
		border-top: 1px solid #eeeeee;
		position: relative;
	}
	.condition-set.collapsed .set-footer{
		display: none;
	}

	.condition-set .set-footer .save-btn{
		width: 100px;
		border: 1px solid #49a0bc;
		padding:5px;
		text-align: center;
		font-size: 15px;
		font-weight: normal;
		line-height: 16px;
		color: #49a0bc;
		text-transform: uppercase;
		display: inline-block;
		cursor: pointer;
	}
	.condition-set .set-footer .save-btn::before{
		font-family: "dashicons";
	 	content: "\f132";
		display: inline-block;
		-webkit-font-smoothing: antialiased;
		font-weight: normal;
		vertical-align: middle;
		font-size: 18px;
		line-height: 20px;
	}
	.condition-set .set-footer .save-btn:hover{
		background: #eeeeee;
	}
	.condition-set .set-footer .save-loading{
		width: 32px;
		height: 32px;
		display: inline-block;
		vertical-align: middle;
		margin-left: 15px;
	}
	.condition-set .set-footer .save-loading img{
		margin: 0;
		padding: 0;
	}

	.condition-set .set-footer .delete-set-btn{
		width: 32px;
		height: 32px;
		position: absolute;
		top: 10px;
		right: 2px;
		display: inline-block;
		color: #bbbbbb;
		text-align: center;
		cursor: pointer;
	}
	.condition-set .set-footer .delete-set-btn::before{
		font-family: "dashicons";
	 	content: "\f182";
		display: inline-block;
		-webkit-font-smoothing: antialiased;
		font-weight: normal;
		vertical-align: middle;
		font-size: 28px;
		line-height: 32px;
	}
	.condition-set .set-footer .delete-set-btn:hover{
		color: #ff0000;
	}
	.condition-set .set-footer .delete-set-loading{
		width: 32px;
		height: 32px;
		position: absolute;
		top: 10px;
		right: 50px;
		display: inline-block;
		vertical-align: middle;
	}
	.condition-set .set-footer .delete-set-loading img{
		margin: 0;
		padding: 0;
	}
	
	#add_condition_set_btn_box{
		margin-top: 20px;
		text-align: center;
	}
	#add_condition_set_btn{
		height: 30px;
		font-size: 20px;
		line-height: 30px;
		font-weight: 100;
		padding: 10px 20px;
		background: #efefef;
		color: green;
		border: 1px solid green;
		border-radius: 7px;
		text-align: center;
		cursor: pointer;
		display: inline-block;
	}
	#add_condition_set_btn:hover{
		background: #ffffff;
	}



	.condition-set .set-body .page-type-list-box{
		width:250px;
		min-height: 10px;
		overflow: hidden;
		position: absolute;
		background: #f9f9f9;
		top:0px;
		left: -1px;
		border: 1px solid #cccccc;
		border-top: none;
		border-bottom-right-radius: 4px;
		border-bottom-left-radius: 4px;
		z-index: 10;
	}
	.condition-set .set-body .page-type-list-box .page-type-list-box-inner{
		width:100%;
		height: 290px;
		overflow: scroll;
	}
	.page-type-list-box.collapsed .page-type-list-box-inner{
		display: none;
	}
	.page-type-list-box .title{
		text-align: center;
		display: block;
		font-size: 15px;
		font-weight: normal;
		color: #000000;
		border-bottom: 1px dashed #cccccc;
		padding: 5px;

	}
	.page-type-list-box .open-close-arrow-box{
		width: 250px;
		height: 10px;
		position: absolute;
		border-top: 1px solid #cccccc;
		background: #eeeeee;
		bottom: 0px;
		left: 0px;
		text-align: center;
		cursor: pointer;
	}
	.page-type-list-box.collapsed .open-close-arrow-box{
		background: #f9f9f9;
	}
	.page-type-list-box .open-close-arrow{
		display: inline-block;
		width: 22px;
		height: 10px;
		text-align: center;
		color: #888888;
		vertical-align: top;
		margin-top: -4px;

	}
	.page-type-list-box .open-close-arrow::before{
		font-family: "dashicons";
	 	content: "\f142";
		display: inline-block;
		-webkit-font-smoothing: antialiased;
		font-weight: normal;
		vertical-align: bottom;
		font-size: 22px;
		
	}
	.open-close-arrow-box:hover .open-close-arrow{
		color: #000000;
	}

	.page-type-list-box.collapsed .open-close-arrow::before{
		content: "\f140";
	}

	.page-type-list-box .page-type-list{
		padding: 10px;
		margin: 0px;
		font-size: 13px;
	}
	.page-type-list li{
		margin-bottom: 0px;
		font-size: 13px;
		line-height: 15px;
		padding: 5px;
	}
	.page-type-list li.usable{
		color: #444444;
		cursor: pointer;
	}
	.page-type-list li.usable:hover{
		background: #c9e9f3;
	}
	.page-type-list .group-name{
		font-weight: bold;
	}
	.page-type-list ul{
		padding-left: 10px;
	}
</style>