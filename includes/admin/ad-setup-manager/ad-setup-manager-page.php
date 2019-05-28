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
$post_id = isset( $_GET['post_id'] ) ? intval( $_GET['post_id'] ) : 0 ; 

$this->current_ad_type = $current_ad_type;
$this->current_zone_id = $zone_id;
$this->current_post_id = $post_id;

$this->current_ad_type_args = $current_ad_type_args;
$zone_selection_needed = false;
if( $use_zone )
{ 
	if( ! $this->get_current_zone() )
	{
		$zone_selection_needed = true;
	}
	$editor_title = sprintf( __("Setup %s to Zone", "adguru" ) , $current_ad_type_args['plural_name'] );  
} 
else 
{  
	$editor_title =  sprintf( __("Setup %s to pages", "adguru" ) , $current_ad_type_args['plural_name'] ); 
}

if( ! $zone_selection_needed )
{
	$this->prepare();
	$this->print_script();
}

?>
<link rel="stylesheet" type="text/css" href="<?php echo ADGURU_PLUGIN_URL ?>assets/css/ad-setup-manager.css" />
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
			<div id="zone-select-area">
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

		if( ! $zone_selection_needed ) : 
		
		?>
		<div id="condition_sets_box">
			<div class="condition-set">
				<div class="set-header">
					<span class="ec-btn" title="Edit page type"></span>
					<span class="page-type-display-box"><span class="page-type-display-text">Archive &raquo; Tag &raquo;</span><input type="text" class="term-name checking" placeholder="Term name/slug" /></span>
					<div class="cs-box">

						<select class="country-select" style="width: 321px;"><option value="--">Any Country</option><option value="US">United States</option><option value="GB">United Kingdom</option><option value="CA">Canada</option><option value="AU">Australia</option><option value="NZ">New Zealand</option><optgroup label="- - - - - - - - - - - - - - - - - - - - - - - -"></optgroup><option value="AF">Afghanistan</option><option value="AX">Aland Islands</option><option value="AL">Albania</option><option value="DZ">Algeria</option><option value="AS">American Samoa</option><option value="AD">Andorra</option><option value="AO">Angola</option><option value="AI">Anguilla</option><option value="A1">Anonymous Proxy</option><option value="AQ">Antarctica</option><option value="AG">Antigua and Barbuda</option><option value="AR">Argentina</option><option value="AM">Armenia</option><option value="AW">Aruba</option><option value="AP">Asia/Pacific Region</option><option value="AT">Austria</option><option value="AZ">Azerbaijan</option><option value="BS">Bahamas</option><option value="BH">Bahrain</option><option value="BD">Bangladesh</option><option value="BB">Barbados</option><option value="BY">Belarus</option><option value="BE">Belgium</option><option value="BZ">Belize</option><option value="BJ">Benin</option><option value="BM">Bermuda</option><option value="BT">Bhutan</option><option value="BO">Bolivia</option><option value="BQ">Bonaire, Saint Eustatius and Saba</option><option value="BA">Bosnia and Herzegovina</option><option value="BW">Botswana</option><option value="BV">Bouvet Island</option><option value="BR">Brazil</option><option value="IO">British Indian Ocean Territory</option><option value="BN">Brunei Darussalam</option><option value="BG">Bulgaria</option><option value="BF">Burkina Faso</option><option value="BI">Burundi</option><option value="KH">Cambodia</option><option value="CM">Cameroon</option><option value="CV">Cape Verde</option><option value="KY">Cayman Islands</option><option value="CF">Central African Republic</option><option value="TD">Chad</option><option value="CL">Chile</option><option value="CN">China</option><option value="CX">Christmas Island</option><option value="CC">Cocos (Keeling) Islands</option><option value="CO">Colombia</option><option value="KM">Comoros</option><option value="CG">Congo</option><option value="CD">Congo, The Democratic Republic of the</option><option value="CK">Cook Islands</option><option value="CR">Costa Rica</option><option value="CI">Cote D'Ivoire</option><option value="HR">Croatia</option><option value="CU">Cuba</option><option value="CW">Curacao</option><option value="CY">Cyprus</option><option value="CZ">Czech Republic</option><option value="DK">Denmark</option><option value="DJ">Djibouti</option><option value="DM">Dominica</option><option value="DO">Dominican Republic</option><option value="EC">Ecuador</option><option value="EG">Egypt</option><option value="SV">El Salvador</option><option value="GQ">Equatorial Guinea</option><option value="ER">Eritrea</option><option value="EE">Estonia</option><option value="ET">Ethiopia</option><option value="EU">Europe</option><option value="FK">Falkland Islands (Malvinas)</option><option value="FO">Faroe Islands</option><option value="FJ">Fiji</option><option value="FI">Finland</option><option value="FR">France</option><option value="GF">French Guiana</option><option value="PF">French Polynesia</option><option value="TF">French Southern Territories</option><option value="GA">Gabon</option><option value="GM">Gambia</option><option value="GE">Georgia</option><option value="DE">Germany</option><option value="GH">Ghana</option><option value="GI">Gibraltar</option><option value="GR">Greece</option><option value="GL">Greenland</option><option value="GD">Grenada</option><option value="GP">Guadeloupe</option><option value="GU">Guam</option><option value="GT">Guatemala</option><option value="GG">Guernsey</option><option value="GN">Guinea</option><option value="GW">Guinea-Bissau</option><option value="GY">Guyana</option><option value="HT">Haiti</option><option value="HM">Heard Island and McDonald Islands</option><option value="VA">Holy See (Vatican City State)</option><option value="HN">Honduras</option><option value="HK">Hong Kong</option><option value="HU">Hungary</option><option value="IS">Iceland</option><option value="IN">India</option><option value="ID">Indonesia</option><option value="IR">Iran, Islamic Republic of</option><option value="IQ">Iraq</option><option value="IE">Ireland</option><option value="IM">Isle of Man</option><option value="IL">Israel</option><option value="IT">Italy</option><option value="JM">Jamaica</option><option value="JP">Japan</option><option value="JE">Jersey</option><option value="JO">Jordan</option><option value="KZ">Kazakhstan</option><option value="KE">Kenya</option><option value="KI">Kiribati</option><option value="KP">Korea, Democratic People's Republic of</option><option value="KR">Korea, Republic of</option><option value="KW">Kuwait</option><option value="KG">Kyrgyzstan</option><option value="LA">Lao People's Democratic Republic</option><option value="LV">Latvia</option><option value="LB">Lebanon</option><option value="LS">Lesotho</option><option value="LR">Liberia</option><option value="LY">Libya</option><option value="LI">Liechtenstein</option><option value="LT">Lithuania</option><option value="LU">Luxembourg</option><option value="MO">Macau</option><option value="MK">Macedonia</option><option value="MG">Madagascar</option><option value="MW">Malawi</option><option value="MY">Malaysia</option><option value="MV">Maldives</option><option value="ML">Mali</option><option value="MT">Malta</option><option value="MH">Marshall Islands</option><option value="MQ">Martinique</option><option value="MR">Mauritania</option><option value="MU">Mauritius</option><option value="YT">Mayotte</option><option value="MX">Mexico</option><option value="FM">Micronesia, Federated States of</option><option value="MD">Moldova, Republic of</option><option value="MC">Monaco</option><option value="MN">Mongolia</option><option value="ME">Montenegro</option><option value="MS">Montserrat</option><option value="MA">Morocco</option><option value="MZ">Mozambique</option><option value="MM">Myanmar</option><option value="NA">Namibia</option><option value="NR">Nauru</option><option value="NP">Nepal</option><option value="NL">Netherlands</option><option value="NC">New Caledonia</option><option value="NI">Nicaragua</option><option value="NE">Niger</option><option value="NG">Nigeria</option><option value="NU">Niue</option><option value="NF">Norfolk Island</option><option value="MP">Northern Mariana Islands</option><option value="NO">Norway</option><option value="OM">Oman</option><option value="O1">Other</option><option value="PK">Pakistan</option><option value="PW">Palau</option><option value="PS">Palestinian Territory</option><option value="PA">Panama</option><option value="PG">Papua New Guinea</option><option value="PY">Paraguay</option><option value="PE">Peru</option><option value="PH">Philippines</option><option value="PN">Pitcairn Islands</option><option value="PL">Poland</option><option value="PT">Portugal</option><option value="PR">Puerto Rico</option><option value="QA">Qatar</option><option value="RE">Reunion</option><option value="RO">Romania</option><option value="RU">Russian Federation</option><option value="RW">Rwanda</option><option value="BL">Saint Barthelemy</option><option value="SH">Saint Helena</option><option value="KN">Saint Kitts and Nevis</option><option value="LC">Saint Lucia</option><option value="MF">Saint Martin</option><option value="PM">Saint Pierre and Miquelon</option><option value="VC">Saint Vincent and the Grenadines</option><option value="WS">Samoa</option><option value="SM">San Marino</option><option value="ST">Sao Tome and Principe</option><option value="A2">Satellite Provider</option><option value="SA">Saudi Arabia</option><option value="SN">Senegal</option><option value="RS">Serbia</option><option value="SC">Seychelles</option><option value="SL">Sierra Leone</option><option value="SG">Singapore</option><option value="SX">Sint Maarten (Dutch part)</option><option value="SK">Slovakia</option><option value="SI">Slovenia</option><option value="SB">Solomon Islands</option><option value="SO">Somalia</option><option value="ZA">South Africa</option><option value="GS">South Georgia and the South Sandwich Islands</option><option value="SS">South Sudan</option><option value="ES">Spain</option><option value="LK">Sri Lanka</option><option value="SD">Sudan</option><option value="SR">Suriname</option><option value="SJ">Svalbard and Jan Mayen</option><option value="SZ">Swaziland</option><option value="SE">Sweden</option><option value="CH">Switzerland</option><option value="SY">Syrian Arab Republic</option><option value="TW">Taiwan</option><option value="TJ">Tajikistan</option><option value="TZ">Tanzania, United Republic of</option><option value="TH">Thailand</option><option value="TL">Timor-Leste</option><option value="TG">Togo</option><option value="TK">Tokelau</option><option value="TO">Tonga</option><option value="TT">Trinidad and Tobago</option><option value="TN">Tunisia</option><option value="TR">Turkey</option><option value="TM">Turkmenistan</option><option value="TC">Turks and Caicos Islands</option><option value="TV">Tuvalu</option><option value="UG">Uganda</option><option value="UA">Ukraine</option><option value="AE">United Arab Emirates</option><option value="UM">United States Minor Outlying Islands</option><option value="UY">Uruguay</option><option value="UZ">Uzbekistan</option><option value="VU">Vanuatu</option><option value="VE">Venezuela</option><option value="VN">Vietnam</option><option value="VG">Virgin Islands, British</option><option value="VI">Virgin Islands, U.S.</option><option value="WF">Wallis and Futuna</option><option value="EH">Western Sahara</option><option value="YE">Yemen</option><option value="ZM">Zambia</option><option value="ZW">Zimbabwe</option></select>
						
					</div>
					<div class="ac-box">
						<span class="ac-btn"></span>
					</div>
					
				</div>
				
				
				<div class="set-body">
					<div class="page-type-list-wrapper">
						<div class="page-type-list-box">
							<div class="page-type-list-box-inner">
								<span class="title">Select type of page</span>
								<?php echo $this->get_page_type_list_html(); ?>
							</div>
							<div class="open-close-arrow-box"><span class="open-close-arrow"></span></div>
						</div><!-- /.page-type-list-box -->
					</div><!-- /.page-type-list-wrapper -->
					
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
										<span class="ad-remove-btn" title="Remove this ad"></span>
									</div>
								</div><!-- /.ad -->
								<div class="ad">
									<div class="title">Amazon Ad 2 - 300x250</div>
									<div class="control-box">
										<span class="percentage-box"><span class="percentage-label">Rotate</span><input type="number" class="percentage" value="100" max="100" min="0"> %</span>
										<a class="edit-btn" href="#" target="_blank" title="Edit this ad"></a>
										<span class="ad-remove-btn" title="Remove this ad"></span>
									</div>
								</div><!-- /.ad -->
								<div class="ad">
									<div class="title">Amazon Ad 3 - 300x250</div>
									<div class="control-box">
										<span class="percentage-box"><span class="percentage-label">Rotate</span><input type="number" class="percentage" value="100" max="100" min="0"> %</span>
										<a class="edit-btn" href="#" target="_blank" title="Edit this ad"></a>
										<span class="ad-remove-btn" title="Remove this ad"></span>
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
				</div><!-- /.set-body -->
				<div class="set-footer">
					<div class="set-error-msg-box">Error message will go here<!-- Error message will go here --></div>
					<span class="save-btn">Save</span>
					<span class="save-loading"><img src="<?php echo ADGURU_PLUGIN_URL ?>assets/images/loading32.gif" height="32" /></span>
					
					<span class="delete-set-loading"><img src="<?php echo ADGURU_PLUGIN_URL ?>assets/images/loading32.gif" height="32" /></span>
					<span class="delete-set-btn" title="Delete this set"></span>	
				</div><!-- /.set-footer -->
			</div><!-- /.condition-set -->			

		</div><!-- /#condition_sets_box -->
		<div id="add_condition_set_btn_box"><span id="add_condition_set_btn">Add New Ad Set &amp; Condition</span></div>

		<?php $this->render_ad_list_modal(); ?>

	<?php else: //if( ! $zone_selection_needed ) :  ?>
	<div>
		<div style="text-align: center;font-size: 40px; margin-top: 40px;text-transform: uppercase;"><?php _e('Select zone', 'adgur') ?></div>
	</div>
	<?php endif; //if( ! $zone_selection_needed ) :  ?>

	</div><!-- end #editor_container -->

	<?php do_action( "adguru_ad_setup_manager_bottom_{$current_ad_type}" , $current_ad_type_args ); ?>
	<?php do_action( "adguru_ad_setup_manager_bottom" , $current_ad_type_args ); ?>

</div><!-- end .wrap -->
