$(document).ready(function () {
	//Scrolling
	$(document).ready(function () {
		$('#navbar-scrolling').on('click', 'a', function (event) {
			var navbarHeight = $('nav').height();
			// event.preventDefault();
			var id = $(this).attr('href'),
				top = $(id).offset().top;
			var scrollTopHeigt = top - navbarHeight - 50;
			$('body,html').animate({ scrollTop: scrollTopHeigt }, 1000);
		});
	});

	// $(document).ready(function () {
	// 	$('#pricing').on('click', 'a', function (event) {
	// 		var navbarHeight = $('nav').height();
	// 		event.preventDefault();
	// 		var id = $(this).attr('href'),
	// 			top = $(id).offset().top;
	// 		var scrollTopHeigt = top - navbarHeight;
	// 		$('body,html').animate({ scrollTop: scrollTopHeigt }, 1000);
	// 	});
	// });
	$(".navbar-link").click(function (event) {
		event.preventDefault();
	});

	// $(function () {
	// 	$('[data-toggle="popover"]').popover()
	// })
	$(document).ready(function () {
		$(document).scrollTop();
	});

	$(window).scroll(function () {
		$(".navbar").toggleClass("nav-scroll", $(this).scrollTop() > 0);
	});

});
