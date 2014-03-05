function cookiesEnabled() {
 
  if (navigator.cookieEnabled) return true;
  document.cookie = "cookietest=1";
  var ret = document.cookie.indexOf("cookietest=") != -1;
  document.cookie = "cookietest=1; expires=Thu, 01-Jan-1970 00:00:01 GMT";
  return ret;
}

window.fbAsyncInit = function() {

	FB.init({
		appId      : '583037658388035',
		status     : false,
		cookie     : true,
		xfbml      : true
	});

	FB.Event.subscribe('auth.authResponseChange', function(response) {

		if (response.status === 'connected') {

		  FB.api('/me', function(response) {
			  window.fbAuthResponse = response;
		  });
		  
		} else if (response.status === 'not_authorized') {

			alert('Bitte logge dich bei Facebook ein und autorisiere die 3886records app!');
			
		} else {

			// alert('Bitte logge dich bei Facebook ein!');
		}
	});
	  
	if (!cookiesEnabled()) {

		$('#info').append('<p style="margin-top: 10px; font-weight: bold; color: red;">Bitte aktiviere Cookies damit die Teilnahme möglich ist!<br/><span style="font-weight: normal; font-size: 12px;">(Mindestens für diese Seite und für Facebook, am besten aktivierst Du sie vorrübergehen komplett. Danach kannst du unsere Cookies auch wieder löschen!)</span></p>');

	} else {

		FB.getLoginStatus(function(r) {
		
			if (r.authResponse == null || r.status == "unknown") {
			
				$('#info').append('<p style="margin-top: 10px; font-weight: bold; line-height: 19px; color: red;">Es scheint so dass Du Cookies nicht aktiviert hast. Bitte aktiviere Cookies.<br/><span style="font-weight: normal; font-size: 12px;">(Cookies sind für die Teilnahme am Gewinnspiel erforderlich. Mindestens für diese Seite und für Facebook, besser aktivierst Du sie vorrübergehen komplett. Danach kannst du unsere Cookies auch wieder löschen!)</span></p>');
			}
		});
	}
};

// Load the FB SDK asynchronously
(function(d){
var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
if (d.getElementById(id)) {return;}
js = d.createElement('script'); js.id = id; js.async = true;
js.src = "//connect.facebook.net/de_DE/all.js";
ref.parentNode.insertBefore(js, ref);
}(document));


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
	
	$( "input[type=date]" ).datepicker({
		inline: true
	});
});

window.onpopstate = function(e) {

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
