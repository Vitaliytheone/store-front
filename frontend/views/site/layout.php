<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Title</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.4.2/css/swiper.min.css">
    <link rel="stylesheet" href="/css/frontend/main.css">
    <link rel="stylesheet" href="/css/frontend/colors.css">
</head>
<body>
<nav class="navbar navbar-white">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">
                <!--<img src="http://belwake.ru/wp-content/themes/flora/images/blog/placeholder.jpg" alt="" title="" class="img-responsive">-->
                FastinstaFollowers
            </a>
        </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="/">Home</a></li>
                <li><a href="/product">Twitter</a></li>
                <li class="dropdown">
                    <a href="/product" class="dropdown-toggle disabled">YouTube <span class="caret"></span></a>
                    <ul class="dropdown-menu multi-level">
                        <li><a href="/product">Followers</a></li>
                        <li><a href="/product">Likes</a></li>
                        <li class="dropdown-submenu">
                            <a href="/product" class="dropdown-toggle disabled">Views</a>
                            <ul class="dropdown-menu">
                                <li><a href="#">100 views</a></li>
                                <li><a href="#">500 views</a></li>
                                <li><a href="#">1000 views</a></li>
                                <li><a href="#">500000 views</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="/product" class="dropdown-toggle disabled">Facebook <span class="caret"></span></a>
                    <ul class="dropdown-menu multi-level">
                        <li><a href="/product">Followers</a></li>
                        <li><a href="/product">Likes</a></li>
                        <li class="dropdown-submenu">
                            <a href="#" class="dropdown-toggle disabled">Views</a>
                            <ul class="dropdown-menu">
                                <li><a href="#">100 views</a></li>
                                <li><a href="#">500 views</a></li>
                                <li><a href="#">1000 views</a></li>
                                <li><a href="#">500000 views</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li><a href="/cart" class="bold">(<?= $site['cart_count'] ?>) Cart</a></li>
            </ul>
        </div>
    </div>
</nav>

<?= $content ?>

<!-- Process block End -->
</body>
<!-- Footer block Start -->
<footer class="bg-white">
    <div class="footer-copy">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    &copy; 2017 site.ru All Rights Reserved.
                </div>
                <div class="col-md-8">
                    <ul class="footer-links">
                        <li><a href="/content">Terms of Service</a></li>
                        <li><a href="/content">Privacy Policy</a></li>
                        <li><a href="/contact">Contact Us</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?= $site['custom_footer'] ?>
</footer>
<!-- Footer block End -->

<!-- Google reCaptcha -->
<script src="https://www.google.com/recaptcha/api.js?hl=en"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.4.2/js/swiper.min.js"></script>
<script src="/js/main.js"></script>

<?php foreach ($site['scripts'] as $script) : ?>
<script type="text/javascript" <?php if (!empty($script['src'])) : ?> src="<?= $script['src'] ?>" <?php endif; ?>>
    <?php if (!empty($script['code'])) : ?>
        <?= $script['code'] ?>
    <?php endif; ?>
</script>
<?php endforeach; ?>

</html>