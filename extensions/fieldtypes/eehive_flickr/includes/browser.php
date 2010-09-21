<?php
	require_once("../scripts/flickr/Phpflickr.php");
				
	$f = new phpFlickr($_GET['api'], $_GET['secret']);
	$f->setToken($_GET['auth']);
	
	$active = $_GET['v'];
	
	// URL string
	$URLstring = '&fn=' . $_GET['fn'] . '&photourl=' . $_GET['photourl'] . '&api=' . $_GET['api'] . '&secret=' . $_GET['secret'] . '&token=' . $_GET['token'] . '&nsid=' . $_GET['nsid'];
				
	$flickrURL = getAddress();
			
	// Get the page number		
	$page = isset($_GET['p']) ?  $_GET['p'] :  1;
	$perpage = 40;				
				
	//echo $f->getErrorMsg();
	//print_r($recent);

	function getAddress() {
		$protocol = $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
		return $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}
	
	function escapejQuery($str) {
		$str = str_replace("[","0",$str);
		$str = str_replace("]","0",$str);
		return $str;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Flickr Browser</title>
<link rel="stylesheet" type="text/css" href="../styles/browser.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script type="text/javascript" src="../scripts/jquery.jscrollpane.js"></script>
<script>
	$(document).ready(function() {						   
		
		$("a.select").click(function() {
			// Get the base URL for the selected image
			var picURL = $(this).attr('rel');
			var picData = $(this).attr('href');
			
			// Set the image URL for the hidden input
			$("input[rel='<?=escapejQuery($_GET['fn'])?>']", top.document).attr('value',picData);
				
			// Add the image URL to the hidden field
			$("#<?=escapejQuery($_GET['fn'])?>_image > a.singleImage > span", top.document).html(picURL);
			$("#<?=escapejQuery($_GET['fn'])?>_image > a.singleImage > img", top.document).attr('src',picURL + '_s.jpg');
			$("#<?=escapejQuery($_GET['fn'])?>_image > a.singleImage", top.document).attr('href',picURL + '.jpg');
			
			// Hide the image chooser
			$("div#<?=escapejQuery($_GET['fn'])?>_chooser", top.document).css("display","none");
				
			// Display the image thumbnail and URL
			$("div#<?=escapejQuery($_GET['fn'])?>_image", top.document).css("display","block");
						
			// Bind Fancybox to the new image chooser																													 
			parent.top.$("a.singleImage").fancybox();
					
			// Close the Fancybox
			parent.top.$.fancybox.close();
				
		});
		
		$(function() {
			$('.scrollpane').jScrollPane({scrollbarWidth:11});
		});

	});
</script>
</head>

<body>
    <div id="flickrBrowser">
        <div id="flickrNav">
            <a <?php if($active == "Main") {echo 'class="active"';}?> href="browser.php?v=Main<?=$URLstring?>">Photostream</a>
            <a <?php if($active == "Sets" || $active == "Set") {echo 'class="active"';}?> href="browser.php?v=Sets<?=$URLstring?>">Sets</a> 
            <a <?php if($active == "Tags" || $active == "Tag") {echo 'class="active"';}?> href="browser.php?v=Tags<?=$URLstring?>">Tags</a>
        </div>
		<?php 
			if ($active == "Main") {
				include_once('photostream.php');
			} elseif ($active == "Sets") { 
				include_once('sets.php');
			} elseif ($active == "Set") { 
				include_once('setphotos.php');
			} elseif ($active == "Tags") { 
				include_once('tags.php'); 
			} elseif ($active == "Tag") { 
				include_once('tagphotos.php');
			} 
		?>
        <div style="clear:both;"></div>
    </div>
</body>
</html>