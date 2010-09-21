<?php

if ( ! defined('EXT')) exit('Invalid file request');


/**
 * ee hive - Flickr Class
 *
 * @package   ee hive - Flickr
 * @author    ee hive - Brett Dewoody <brett@ee-hive.com>
 * @copyright Copyright (c) 2010 ee hive
 * @license   http://creativecommons.org/licenses/by-sa/3.0/ Attribution-Share Alike 3.0 Unported
 */
class Eehive_flickr extends Fieldframe_Fieldtype {
	
	

	/**
	 * Fieldtype Info
	 * @var array
	 */
	var $info = array(
		'name'     => 'Flickr - by ee hive',
		'version'  => '1.0.2',
		'docs_url' => 'http://www.ee-hive.com/flickr',
		'no_lang'  => TRUE,
		'versions_xml_url' => 'http://www.ee-hive.com/flickr/versions.xml'
	);
	
	var $requires = array(
        'ff'        => '1.3.3',
        'cp_jquery' => '1.1'
    );

	
	function display_site_settings() {
		// Initialize a new instance of SettingsDisplay
		$SD = new Fieldframe_SettingsDisplay();
		
		$settings = $this->site_settings;
		
		// Define the initial textarea value
		$value_api = 	isset($settings['option_api']) ?  $settings['option_api'] :  '';
		$value_secret = isset($settings['option_secret']) ?  $settings['option_secret'] :  '';
		
		// If the user is receiving the FROB then retrieve the AUTH token
		if (isset($_GET['frob'])) {
			
			$frob = $_GET['frob'];
			
			
			require_once(FT_PATH . "/eehive_flickr/scripts/flickr/Phpflickr.php");
			
			$f = new Phpflickr($value_api, $value_secret);
			
			$auth_array = $f->auth_getToken($frob);
			
			$person_array = $f->people_getInfo($auth_array['user']['nsid']);
			
			// If there was an error save it 
			if ($f->getErrorMsg() != '') {
				$error = $f->getErrorMsg();
			}
			
			$settings['option_auth'] = $auth_array['token'];
			$settings['option_nsid'] = $auth_array['user']['nsid'];
			$settings['option_photourl'] = $person_array['photosurl'];
			$settings['option_profileurl'] = $person_array['profileurl'];
			
		}
		
		$value_auth = 	isset($settings['option_auth']) ?  $settings['option_auth'] :  '';
		$value_nsid = 	isset($settings['option_nsid']) ?  $settings['option_nsid'] :  '';
		$value_photourl = 	isset($settings['option_photourl']) ?  $settings['option_photourl'] :  '';
		$value_profileurl = 	isset($settings['option_profileurl']) ?  $settings['option_profileurl'] :  '';
		
		// Open the settings block
		$r = $SD->block('Flickr API Settings');
	 
		// Add the Default Option Template setting
		$r .= $SD->row(array(
						 $SD->label('Flickr API Key'),
						 $SD->text('option_api', $value_api)
					   ));
		
		// Add the Default Option Template setting
		$r .= $SD->row(array(
						 $SD->label('Flickr API Secret'),
						 $SD->text('option_secret', $value_secret)
					   ));
		
		// If there was an error display it
		if (isset($error)) {
			$r .= $SD->row(array('','<div style="color:#cb7376; font-weight:bold; line-height:24px; height:24px;"><img align="left" style="padding-right:10px;" src="' . FT_URL . 'eehive_flickr/images/warning-icon.png" alt=""/>There was an error connecting with Flickr.  Please click the \'Apply\' button again.</div>'));
		}
		if($value_nsid != '' && $value_auth != '') {
			$r .= $SD->row(array('','<div style="color:#9ccb73; font-weight:bold; line-height:24px; height:24px;"><img align="left" style="padding-right:10px;" src="' . FT_URL . 'eehive_flickr/images/accept-icon.png" alt=""/>EE Flickr has successfully installed</div>'));
		}
		
		
		if ($value_api != '' && $value_secret != '' && $value_auth != '') {
		$r .= $SD->row(array(
						 $SD->label('Flickr User ID'),
						 $SD->text('option_nsid', $value_nsid)
						));
			
		$r .= $SD->row(array(
						 $SD->label('Flickr Token'),
						 $SD->text('option_auth', $value_auth)
					   ));
		
		$r .= $SD->row(array(
						 $SD->label('Flickr Photo URL'),
						 $SD->text('option_photourl', $value_photourl)
						));
			
		$r .= $SD->row(array(
						 $SD->label('Flickr Profile URL'),
						 $SD->text('option_profileurl', $value_profileurl)
					   ));
		}
		
		
		// Generate the Callback URL for Flickr
		if ($value_auth == '') {
			$callbackURL =  'http://' . $_SERVER['HTTP_HOST']. $_SERVER['PHP_SELF'] . '?C=admin&M=utilities&P=fieldtypes_manager';
			
			$r .= $SD->info_row('Your callback URL is: <span style="font-weight:bold; color:#999;">' . $callbackURL . '</span>');
		}
		
		
		// If the user has saved their API Key and Secret
		if ($value_api != '' && $value_secret != '' && $value_auth == '') {
			
			$sig_str = md5($value_secret . 'api_key' . $value_api . 'permsread');
			
			$r .= $SD->row(array('<a style="font-weight:bold; color:#86c950;" href="http://flickr.com/services/auth/?api_key=' . $value_api . '&perms=read&api_sig=' . $sig_str . '">Click to activate Flickr</a>'));
		
		}
	 
		// Close the settings block
		$r .= $SD->block_c();
	 
		// Return the settings block
		return $r;
	}
	
	
	function save_site_settings($site_settings) {
		
		return $site_settings;
	}
	
	
	function display_field_settings($field_settings) {
		
		$cell2 = '';

		return array('cell2' => $cell2);
		
	}
	

	function display_field($field_name, $field_data, $field_settings) {
		
		// Initialize vars
		$displayChooser = 'display:block; ';
		$displayImage = 'display:block; ';
		$pic = '';
		$picRaw = isset($field_data) ?  $field_data :  '';
		$picSquare = '';
		$picMedium = '';
		
		// Flickr Vars
		$flickrField = $field_name;
		$flickrAPI = $this->site_settings['option_api'];
		$flickrSecret = $this->site_settings['option_secret'];
		$flickrToken = $this->site_settings['option_auth'];
		$flickrNSID = $this->site_settings['option_nsid'];
		$flickrPhotoURL = $this->site_settings['option_photourl'];
		$flickrURL = FT_URL;
		
		
		// If a pic has already been selected read it in and unserialize it
		if ($picRaw != '') {
			$picArray = unserialize(urldecode($picRaw));
			$pic = $picArray[0];
			$picSquare = $pic . '_s.jpg';
			$picMedium = $pic . '.jpg';
			
			// Set the Image Chooser to display:none
			$displayChooser = 'display:none; ';
		} else {
			
			// Set the Image to display:none;
			$displayImage = 'display:none; ';
		}
		
		// Save JS vars
		$this->insert_js('
			var flickrAPI = "' . $this->site_settings['option_api'] . '";
			var flickrSecret = "' . $this->site_settings['option_secret'] . '";
			var flickrToken = "' . $this->site_settings['option_auth'] . '";
			var flickrNSID = "' . $this->site_settings['option_nsid'] . '";
			var flickrURL = "' . FT_URL . '";
		');
	
		// Include necessary JS and CSS
		$this->include_js('scripts/fancybox/jquery.fancybox-1.3.1.pack.js');
		$this->include_js('scripts/jquery.ff-flickr.js');
		$this->include_css('scripts/fancybox/jquery.fancybox-1.3.1.css');
		$this->include_css('styles/flickr.css');

		
		$r = '';
		$r .= '<div class="flickrContainer" id="flickrContainer_' . $this->escapejQuery($flickrField) . '">';
		$r .= '<input type="hidden" class="flickrInput" id="' . $flickrField . '" rel="' . $this->escapejQuery($flickrField) . '" name="' . $flickrField . '" value="' . $picRaw . '" />';
		$r .= '<div class="flickrChooser" id="' . $this->escapejQuery($flickrField) . '_chooser" style="' . $displayChooser . '">
			<a  class="ff_flickr_input" href="' . $flickrURL . 'eehive_flickr/includes/browser.php?v=Main&fn=' . $flickrField . '&photourl=' . $flickrPhotoURL . '&api=' . $flickrAPI . '&secret=' . $flickrSecret . '&token=' . $flickrToken . '&nsid=' . $flickrNSID . '" onClick="return false;"><button >Choose Photo</button></a> No photo chosen
		</div>';
		$r .= '<div class="flickrImage" id="' . $this->escapejQuery($flickrField) . '_image" style="' . $displayImage . '">
			<a class="singleImage" href="' . $picMedium . '">
				<img src="' . $picSquare . '" align="left" />
				<span>' . $pic . '</span>
			</a> 
			<a class="flickrTrash" href="#" rel="' . $this->escapejQuery($flickrField) . '" onClick="return false;"><img src="' . $flickrURL . 'eehive_flickr/images/trash-icon.gif" /></a>
		</div>';
		$r .= '</div>';

		
		return $r;

	}
	
	
	// Cell Settings
	function display_cell_settings($cell_settings) {
		
		return $this->display_field_settings($cell_settings);
		
	}	
	
	// Cell Data
	function display_cell($cell_name, $cell_data, $cell_settings) {
      
	   return $this->display_field($cell_name, $cell_data, $cell_settings);
	
	}
	
	
	// TAG: Display the photo, in the appropriate size
	function display_tag($params, $tagdata, $field_data, $field_settings) {
		
		// Unserialize the photo data
		$picArray = unserialize(urldecode($field_data));
		$pic = $picArray[0];
		
		$r = '';
		
		$r .= $pic;
		
		if (isset($params['size'])) {
			$size = $params['size'];
			if($size == 'square') {
				$r .= "_s.jpg";
			}
			if  ($size == "thumb") {
				$r .= "_t.jpg";
			}
			if  ($size == "small") {
				$r .= "_m.jpg";
			}
			if  ($size == "medium") {
				$r .= ".jpg";
			}
			if  ($size == "large") {
				$r .= "_b.jpg";
			}
		} else {
			// Else display the medium size
			$r .= ".jpg";
		}
		
		return $r;
		
	}
	
	
	// TAG: Display the photo's title
	function title($params, $tagdata, $field_data, $field_settings) {
		// Unserialize the photo data
		$picArray = unserialize(urldecode($field_data));
		$title = $picArray[3];
		
		$r = '';
		
		$r .= $title;
		
		return $r;
	}
	
	
	// TAG: Display the photo's description
	function description($params, $tagdata, $field_data, $field_settings) {
		// Unserialize the photo data
		$picArray = unserialize(urldecode($field_data));
		$description = $picArray[4];
		
		$r = '';
		
		$r .= $description;
		
		return $r;
	}
	
	
	// TAG: Display the photo's page URL
	function pagelink($params, $tagdata, $field_data, $field_settings) {
		
		// Pull in the site settings
		$settings = $this->site_settings;
		
		// Unserialize the photo data
		$picArray = unserialize(urldecode($field_data));
		$id = $picArray[1];
		
		$r = '';
		
		$r .= 'http://www.flickr.com/photos/' . $settings['option_nsid'] . '/' . $id;
		
		return $r;
	}
	
	
	
	// Function to escape necessary jQuery characters
	// Bug in jQuery prevents us from merely escaping special characters. So instead
	// we'll replace them with zeros.
	function escapejQuery($str) {
		$str = str_replace("[","0",$str);
		$str = str_replace("]","0",$str);
		return $str;
	}

}