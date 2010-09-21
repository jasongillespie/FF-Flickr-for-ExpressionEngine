$(document).ready(function() {
		
	
	// Apply FancyBox to the Choose Photo button
	openFancyBox();
	
	
	// Apply FancyBox to the images
	$("a.singleImage").fancybox({
		'transition': 'fade',
		'overlayOpacity': 0.75							
	});
	
	
	// Trash existing image selection and startover
	// Uses the links REL attribute to determine which image to trash
	$("div.flickrContainer  a.flickrTrash").live('click',function() {
		
		// Get the REL attribute from the trash link
		var field_name = escapejQuery($(this).attr('rel'));
		
		// Get the parent container of the trash link
		var parentClass = $(this).parents('.flickrContainer').attr('id');
		//alert(parentClass);
		
		// Remove the VALUE from the field input
		$("input#" + field_name).attr('value','');
		
		// Hide the image chooser
		$("div#" + field_name + "_image").css("display","none");
				
		// Display the image thumbnail and URL
		$("div#" + field_name + "_chooser").css("display","block");
		
	});
	
	
	// For each new FF Matrix created initiate FancyBox
	$.fn.ffMatrix.onDisplayCell.digitalwaxworks_flickr = function(cell, FFM){
		
		// Get the name of the new row's input
		var inputName = $('div.flickrContainer input.flickrInput', cell).attr('name');
		
		// Set the input's ID equal to the name
		$("input[name='" + inputName + "']", cell).attr('id', inputName);
		
		// Set the input's REL equal to the escaped name
		$("input[name='" + inputName + "']", cell).attr('rel', escapejQuery(inputName));
		
		// Set the cell's Image Chooser ID to the escaped name
		$(".flickrChooser", cell).attr('id', escapejQuery(inputName) + '_chooser');
		
		// Set the cell's Image Block ID to the escaped name
		$(".flickrImage", cell).attr('id', escapejQuery(inputName) + '_image');
		
		// Set the cell's Trash REL to the escaped name
		$("a.flickrTrash", cell).attr('rel', escapejQuery(inputName));
		
		// Set the URL of the browser with the new fields ID
		$(".flickrChooser a", cell).attr('href',FT_URL + 'digitalwaxworks_flickr/includes/browser.php?v=Main&fn=' + inputName + '&api=' + flickrAPI + '&secret=' + flickrSecret + '&token=' + flickrToken + '&nsid=' + flickrNSID);
							
		// Initiate FancyBox for the new element
		openFancyBox();
		
	};

	
	function openFancyBox() {
		// Open the Fancybox iframe
		$("a.ff_flickr_input").fancybox({
			'width'		: 780,
			'height'	: 600,
			'type'		: 'iframe',
			'transition': 'fade',
			'overlayOpacity': 0.75,
			'cyclic'	: false,
			'showNavArrows' : false,
			'padding'	: 0,
			'scrolling'	: 'no'
		});
	}
	
	
	function escapejQuery(str) {
		var str = str;
		str1 = str.replace(/\[/g,'0').replace(/\]/g,'0');
		return str1;
	}
	
	
});