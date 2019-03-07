$(document).ready(function() {
	//Scrolling
	$(document).ready(function() {
		$('#navbar-scrolling').on('click', 'a', function(event) {
			var navbarHeight = $('nav').height();
			event.preventDefault();
			var id = $(this).attr('href'),
				top = $(id).offset().top;
			var scrollTopHeigt = top - navbarHeight;
			$('body,html').animate({ scrollTop: scrollTopHeigt }, 1000);
		});
	});

	$(document).ready(function () {
		$('#pricing').on('click', 'a', function (event) {
			var navbarHeight = $('nav').height();
			event.preventDefault();
			var id = $(this).attr('href'),
				top = $(id).offset().top;
			var scrollTopHeigt = top - navbarHeight;
			$('body,html').animate({ scrollTop: scrollTopHeigt }, 1000);
		});
	});


	$(document).ready(function() {
		$('#footer-scrolling').on('click', 'a', function(event) {
			var footerHeight = $('nav').height();
			event.preventDefault();
			var id = $(this).attr('href'),
				top = $(id).offset().top;
			var scrollTopHeigt = top - footerHeight;
			$('body,html').animate({ scrollTop: scrollTopHeigt }, 1000);
		});
	});


	$(window).scroll(function () {
		$(".navbar").toggleClass("nav-scroll", $(this).scrollTop() > 0);
	});

});
