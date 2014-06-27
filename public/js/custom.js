function cookiesEnabled() {
 
  if (navigator.cookieEnabled) return true;
  document.cookie = "cookietest=1";
  var ret = document.cookie.indexOf("cookietest=") != -1;
  document.cookie = "cookietest=1; expires=Thu, 01-Jan-1970 00:00:01 GMT";
  return ret;
}

$.support.transition = false;
  
$(document).ready(function () {

	$.supersized({
	    slideshow               :   0,
	    vertical_center         :   1,
	    horizontal_center       :   1,
		slides 					:  	[{image : '/img/slides/slide1.jpg', title : '<h1>finest electronic music</h1>', thumb : 'img/slides/slide1_thumb.jpg'}]
    });
    
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
	
	$( "input.date-input" ).datepicker({
		inline: true,
		dateFormat: 'dd.mm.yy'
	});
});

window.isFirstCall = true;
window.onpopstate = function(e) {

	if (window.isFirstCall) return;
	window.isFirstCall = false;
	gotoPage(window.location.href, false);
};

var gotoPage = function(target, pushState) {

	if (target.match(/\?/))
		cacheKiller = '&async';
	else
		cacheKiller = '?async';

	$.ajax({
		url: target + cacheKiller,
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
			
			$("body, html").animate({
				scrollTop : 0
			}, 500);
		},
		error: function(response) {
			if (response.responseText) {
				alert('Something went wrong: '+ response.responseText);
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
