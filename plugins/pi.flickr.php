<?php

$plugin_info = array(
  'pi_name' => 'Flickr - by ee hive',
  'pi_version' =>'1.0.0',
  'pi_author' =>'ee hive - Brett DeWoody',
  'pi_author_url' => 'http://www.ee-hive.com/flickr',
  'pi_description' => 'Provides tags for integrating Flickr into your website',
  'pi_usage' => Flickr::usage()
  );

class Flickr {
	
	function photostream() {
		
		global $PREFS, $TMPL;
		
		$template = $TMPL->tagdata;
		
		$numPhotos = $TMPL->fetch_param('num');
		$numPhotos = $numPhotos != '' ?  $numPhotos :  10;
		
		// Load the flickr class
		$flickr = $this->_flickr();
		$f = $flickr[0];
		$ff_flickr = $flickr[1];
		
		// Get the desired size, or default to square
		$sz = $this->_size($TMPL->fetch_param('size'));
		
		// Retrieve photostream from Flickr
		$recent = $f->people_getPublicPhotos($ff_flickr->site_settings['option_nsid'], 1, 'description', 10, 1);
		
		// If number of returned photo is less than num
		$numPhotos = min($numPhotos,$recent['photos']['total']);
		
		$flickr_photos = $recent['photos']['photo'];
		
		$r = '';
		
		for ($i = 0; $i < $numPhotos; $i++) {
			
			// Retrieve the data for each photo
			$flickr_data = $flickr_photos[$i];
			
			// Define the template tags
			$flickr_img = 'http://farm' . $flickr_data['farm'] . '.static.flickr.com/' . $flickr_data['server'] . '/' . $flickr_data['id'] . '_' . $flickr_data['secret'] . $sz . '.jpg';
			$flickr_url = $ff_flickr->site_settings['option_photourl'] . $flickr_data['id'];
			$flickr_title = $flickr_data['title'];
			$flickr_description = $flickr_data['description'];
			
			// Replace the template tags
			$template_loop = str_replace('{flickr_img}', $flickr_img, $template);
			$template_loop = str_replace('{flickr_url}', $flickr_url, $template_loop);
			$template_loop = str_replace('{flickr_title}', $flickr_title, $template_loop);
			$template_loop = str_replace('{flickr_description}', $flickr_description, $template_loop);
			
			// Append the string to the 
			$r .= $template_loop;
		}
		
		return $r;
	
  	}
	
	
	
	function favorites() {
		
		global $PREFS, $TMPL;
		
		$template = $TMPL->tagdata;
		
		$numPhotos = $TMPL->fetch_param('num');
		$numPhotos = $numPhotos != '' ?  $numPhotos :  10;
		
		// Load the flickr class
		$flickr = $this->_flickr();
		$f = $flickr[0];
		$ff_flickr = $flickr[1];
		
		// Get the desired size, or default to square
		$sz = $this->_size($TMPL->fetch_param('size'));
		
		// Retrieve favorites from Flickr
		$favorites = $f->favorites_getPublicList($ff_flickr->site_settings['option_nsid'], NULL, NULL, 'description', 10, 1);
		
		// If number of returned photo is less than num
		$numPhotos = min($numPhotos,$favorites['photos']['total']);
		
		
		$flickr_photos = $favorites['photos']['photo'];
		
		$r = '';
		
		for ($i = 0; $i < $numPhotos; $i++) {
			
			// Retrieve the data for each photo
			$flickr_data = $flickr_photos[$i];

			// Define the template tags
			$flickr_img = 'http://farm' . $flickr_data['farm'] . '.static.flickr.com/' . $flickr_data['server'] . '/' . $flickr_data['id'] . '_' . $flickr_data['secret'] . $sz . '.jpg';
			$flickr_url = 'http://www.flickr.com/photos/' . $flickr_data['owner'] . '/' . $flickr_data['id'];
			$flickr_title = $flickr_data['title'];
			$flickr_description = $flickr_data['description'];
			
			// Replace the template tags
			$template_loop = str_replace('{flickr_img}', $flickr_img, $template);
			$template_loop = str_replace('{flickr_url}', $flickr_url, $template_loop);
			$template_loop = str_replace('{flickr_title}', $flickr_title, $template_loop);
			$template_loop = str_replace('{flickr_description}', $flickr_description, $template_loop);
			
			// Append the string to the 
			$r .= $template_loop;
		}
		
		return $r;
	}
	
	
	
	function photosets() {
		
		global $PREFS, $TMPL;
		
		$template = $TMPL->tagdata;
		
		// Load the flickr class
		$flickr = $this->_flickr();
		$f = $flickr[0];
		$ff_flickr = $flickr[1];
		
		// Get the desired size, or default to square
		$sz = $this->_size($TMPL->fetch_param('size'));
		
		// Retrieve sets from Flickr
		$sets = $f->photosets_getList($ff_flickr->site_settings['option_nsid']);
		
		$r = '';
		
		foreach($sets['photoset'] as $photoset) {
			
			// Define the template tags
			$set_img = "http://farm" . $photoset['farm']. ".static.flickr.com/" . $photoset['server'] . "/" . $photoset['primary'] . "_" . $photoset['secret'] . $sz . '.jpg';
			$set_url = $ff_flickr->site_settings['option_photourl'] . '/sets/' . $photoset['id'];
			$set_title = $photoset['title'];
			$set_count = $photoset['photos'];
			$set_description = $photoset['description'];
			$set_id = $photoset['id'];
			
			// Replace the template tags
			$template_loop = str_replace('{set_img}', $set_img, $template);
			$template_loop = str_replace('{set_url}', $set_url, $template_loop);
			$template_loop = str_replace('{set_title}', $set_title, $template_loop);
			$template_loop = str_replace('{set_count}', $set_count, $template_loop);
			$template_loop = str_replace('{set_id}', $set_id, $template_loop);
			$template_loop = str_replace('{set_description}', $set_description, $template_loop);
			
			$r .= $template_loop;
		}
		
		return $r;
	}
	
	
	
	function tagcloud() {
		
		global $PREFS, $TMPL;
		
		$template = $TMPL->tagdata;
		
		// Load the flickr class
		$flickr = $this->_flickr();
		$f = $flickr[0];
		$ff_flickr = $flickr[1];
		
		// Retrieve sets from Flickr
		$tags = $f->tags_getListUserPopular($ff_flickr->site_settings['option_nsid'],1000);
		
		$fontMin = $TMPL->fetch_param('font_min');
		$fontMax = $TMPL->fetch_param('font_max');
		$fontMin = $fontMin != '' ?  $fontMin :  11;
		$fontMax = $fontMax != '' ?  $fontMax :  28;
		$size = $fontMin;
					
		$numTags = count($tags);
		sort($tags);
					
		$increment = intval($numTags/($fontMax-$fontMin));
					
		for ($i=0; $i < $numTags; $i++) {
			$output[$tags[$i]['_content']] = $size ;
			if ($increment == 0 || $i % $increment == 0 )  { 
			$size++;
			}
		}
					
		ksort($output);
		
		$r = '';
		
		foreach ($output as $tg => $sz) {
			// Replace the template tags
			$template_loop = str_replace('{tag_name}', $tg, $template);
			$template_loop = str_replace('{tag_link}', $ff_flickr->site_settings['option_photourl'] . 'tags/' . $tg, $template_loop);
			$template_loop = str_replace('{tag_size}', $sz, $template_loop);
			
			$r .= $template_loop;

		}
		
		return $r;
	}
	
	
	
	function _flickr() {
		$fieldframe  = new Fieldframe();
		$ff_flickr = $fieldframe->_init_ftype('digitalwaxworks_flickr', $req_strict=TRUE);

		require_once(FT_PATH . "/digitalwaxworks_flickr/scripts/flickr/Phpflickr.php");
		
		$f = new phpFlickr($ff_flickr->site_settings['option_api'], $ff_flickr->site_settings['option_secret']);
		$f->setToken($ff_flickr->site_settings['option_auth']);
		
		return array($f, $ff_flickr);
	}
	
	
	
	function _size($size) {
		// Default to square
		$size = $size != '' ?  $size :  'square';
		
		switch ($size) {
			case 'square':
				$sz = "_s";
				break;
			case 'thumb':
				$sz = "_t";
				break;
			case 'small':
				$sz = "_m";
				break;
			case 'medium':
				$sz = "";
				break;
			case 'large':
				$sz = "b";
				break;
			default:
      			$sz = "";
		}
		
		return $sz;
	}
	
	
	// ----------------------------------------
	//  Plugin Usage
	// ----------------------------------------
	
	// This function describes how the plugin is used.
	//  Make sure and use output buffering
	
	function usage() {
	  ob_start(); 
	  ?>
	The Flickr Plugin is provides several
	tags to incorporate Flickr into your
    website.
	
	{exp:flickr:photostream}
	
	Displays your Flickr photostream
	
	  <?php
	  $buffer = ob_get_contents();
		
	  ob_end_clean(); 
	
	  return $buffer;
	}
	// END


}

?>