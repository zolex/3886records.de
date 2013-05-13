$.support.transition = false;

//Background Slider Settings
jQuery(function($){
    $.supersized({
	    // Functionality
	    slideshow               :   1,			// Slideshow on/off
	    autoplay				:	1,			// Slideshow starts playing automatically
	    start_slide             :   1,			// Start slide (0 is random)
	    stop_loop				:	0,			// Pauses slideshow on last slide
	    random					: 	0,			// Randomize slide order (Ignores start slide)
	    slide_interval          :   12000,		// Length between transitions
	    transition              :   1, 			// 0-None, 1-Fade, 2-Slide Top, 3-Slide Right, 4-Slide Bottom, 5-Slide Left, 6-Carousel Right, 7-Carousel Left
	    transition_speed		:	1000,		// Speed of transition
	    new_window				:	0,			// Image links open in new window/tab
	    pause_hover             :   0,			// Pause slideshow on hover
	    keyboard_nav            :   0,			// Keyboard navigation on/off
	    performance				:	2,			// 0-Normal, 1-Hybrid speed/quality, 2-Optimizes image quality, 3-Optimizes transition speed // (Only works for Firefox/IE, not Webkit)
	    image_protect			:	0,			// Disables image dragging and right click with Javascript
											       
	    // Size & Position						   
	    min_width		        :   0,			// Min width allowed (in pixels)
	    min_height		        :   0,			// Min height allowed (in pixels)
	    vertical_center         :   1,			// Vertically center background
	    horizontal_center       :   1,			// Horizontally center background
	    fit_always				:	0,			// Image will never exceed browser width or height (Ignores min. dimensions)
	    fit_portrait         	:   1,			// Portrait images will not exceed browser height
	    fit_landscape			:   0,			// Landscape images will not exceed browser width
											       
	    // Components							
	    slide_links				:	'blank',	// Individual links for each slide (Options: false, 'num', 'name', 'blank')
	    thumb_links				:	0,			// Individual thumb links for each slide
	    thumbnail_navigation    :   0,			// Thumbnail navigation
	    slides 					:  	[			// Slideshow Images
										    {image : '/img/slides/slide1.jpg', title : '<h1><span class="raleway">3886</span>records</h1>', thumb : 'img/slides/slide1_thumb.jpg'},
										    {image : '/img/slides/slide2.jpg', title : '<h1>finest electronic music</h1>', thumb : 'img/slides/slide2_thumb.jpg'}],		
								
	    // Theme Options			   
	    progress_bar			:	0,			// Timer for each slide							
	    mouse_scrub				:	0
	
    });   
});


$(document).ready(function () {
    
    $('a.tooltips').tooltip();

    //Tabs
    $('#myTab a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

    // CSS3 animation for top menu panel
    $('.nav a').hover(function(){
    	if(!$(this).parent().hasClass('current')) {
    		$(this).find('span').addClass('animate0 rotateIcon');
    	}
    }, function() {
	    $(this).find('span').removeClass('animate0 rotateIcon');
    });
  
    $('a[data-method=async]').click(function(e) {
	
		$(this).loadPage();
		return false;
	});
	
	/*
	$('li.dropdown').hover(function() {
	
	    $('[data-toggle="dropdown"]', this).dropdown('toggle');
	
	}, function() {
	
	    //$('[data-toggle="dropdown"]', this).dropdown('toggle');
	});
	*/
	
	$( "input[type=date]" ).datepicker({
		inline: true
	});
});

window.onpopstate = function(e) {

	gotoPage(window.location.href, false);
};

var gotoPage = function(target, pushState) {

	$.ajax({
		url: target,
		method: 'GET',
		dataType: 'json',
		success: function(json) {
			if (pushState) {
				window.history.pushState(null, null, target);
			}
			$('#content').html(json.content);
			$('#content a[data-method=async]').click(function(e) {
				$(this).loadPage();
				return false;
			});
			if (typeof json.params.metaTitle != 'undefined') {
			
			    $('head title').text(json.params.metaTitle + ' - 3886records');
			    
			} else {
			    
		        $('head title').text('3886records - independent electronic music label');
		    }
		},
		error: function(response) {
			if (response.responseText) {
				if (pushState) {
					window.history.pushState(null, null, target);
				}
				eval("var json = "+ response.responseText);
				$('#content').html(json.content);
				$('#content a[data-method=async]').click(function(e) {
					$(this).loadPage();
					return false;
				});
				
				if (typeof json.params.metaTitle != 'undefined') {
			
			        $('head title').text(json.params.metaTitle + ' - 3886records');
			        
			    } else {
			    
			        $('head title').text('3886records - independent electronic music label');
			    }
			}
		}
	});
};

(function($){

	$.fn.loadPage = function() {

		return this.each(function () {

			var target = $(this).attr('href');
            var $nav = $(this).parents('nav');
			if (target.replace(/^\//) == window.location.pathname.replace(/^\//, ''))
			    return false;

            gotoPage(target, true);
            
            $('.active', $nav).removeClass('active');
	        $(this).parents('li').addClass('active');
	        $('.dropdown.open [data-toggle="dropdown"]', $nav).dropdown('toggle');
			$('.btn-navbar:visible').click();
		});
	};
	
})(jQuery);
