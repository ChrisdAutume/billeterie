<!DOCTYPE html>
<!--[if IE 8 ]><html class="no-js oldie ie8" lang="fr"> <![endif]-->
<!--[if IE 9 ]><html class="no-js oldie ie9" lang="fr"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html class="no-js" lang="fr"> <!--<![endif]-->
<head>

    <!--- basic page needs
    ================================================== -->
    <meta charset="utf-8">
    <title>Soirée de Remise de Diplome de l'UTT</title>
    <meta name="description" content="Billeterie de la Soirée de Remise des Diplômes de l'UTT ! Nous y célèbrerons la toute fraiche promotion 2017, une bonne occasion pour se réunir autour d'une coupe de champagne pour la plus grande soirée du semestre, le Samedi 18 Novembre 2017 à 21h !">
    <meta name="author" content="BDE UTT">

    <!-- mobile specific metas
    ================================================== -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <!-- CSS
  ================================================== -->
    <link rel="stylesheet" href="{{ asset('landing/css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/css/vendor.css') }}">

    <!-- script
    ================================================== -->
    <script src="{{ asset('landing/js/modernizr.js') }}"></script>

    <!-- favicons
     ================================================== -->
    <link rel="icon" type="image/png" href="favicon.ico">

</head>

<body id="top">

<!-- header
================================================== -->
<header>

    <div class="row">
        <div class="logo">
            <a href="#">Soirée R2D</a>
        </div>

        <div class="social-links">
            <ul>
                <li><a href="https://www.facebook.com/bde.utt/"><i class="fa fa-facebook"></i></a></li>
                <li><a href="https://twitter.com/bdeutt"><i class="fa fa-twitter"></i></a></li>
                <li><a href="https://www.instagram.com/bdeutt/"><i class="fa fa-instagram"></i></a></li>
            </ul>
        </div>
    </div>

</header> <!-- /header -->

<!-- home
================================================== -->
<section id="home" class="home-static">

    <div class="shadow-overlay"></div>

    <div class="content-wrap-table">

        <div class="main-content-tablecell">

            <div class="row">
                <div class="col-twelve">

                    <div id="counter">
                        <div class="half">
                            <span>334 <sup>days</sup></span>
                            <span>23 <sup>hours</sup></span>
                        </div>
                        <div class="half">
                            <span>50 <sup>mins</sup></span>
                            <span>33 <sup>secs</sup></span>
                        </div>
                    </div>

                    <div class="bottom-text">
                        <h1>Soirée de Remise de Diplome</h1>
                        <p>
                            Samedi 18 novembre 2017
                        </p>
                    </div>

                </div> <!-- /twelve -->

                <div class="scroll-icon">

                    <p class="scroll-text">Plus d'infos</p>
                    <a href="#info" class="smoothscroll">
                        <div class="mouse"></div>
                    </a>
                    <div class="end-top"></div>

                </div> <!-- /scroll-icon -->
            </div> <!-- /row -->

        </div> <!-- /main-content -->

    </div> <!-- /content-wrap -->

</section> <!-- /home -->





<!-- footer
================================================== -->
<footer>

    <div class="social-wrap">
        <div class="row">

            <ul class="footer-social-list">
                <li><a href="https://www.facebook.com/bde.utt/">
                        <i class="fa fa-facebook"></i><span>Facebook</span>
                    </a></li>
                <li><a href="https://twitter.com/bdeutt">
                        <i class="fa fa-twitter"></i><span>Twitter</span>
                    </a></li>
                <li><a href="https://www.instagram.com/bdeutt/">
                        <i class="fa fa-instagram"></i><span>Instagram</span>
                    </a></li>
            </ul>

        </div> <!-- /row -->
    </div> <!-- /social-wrap -->

    <div class="footer-bottom">

        <div class="footer-logo">
            <img src="{{ asset('landing/images/bde-utt.jpeg') }}" alt="">
        </div>

        <div class="copyright">
            <span>BDE UTT - 12 rue marie curie - 10000 Troyes</span>
            <span> <a href="mailto:bde-r2d@utt.fr">Contact</a></span>
        </div>

    </div> <!-- /footer-bottom -->


</footer>

<div id="preloader">
    <div id="loader"></div>
</div>

<!-- Java Script
================================================== -->
<script src="{{ asset('landing/js/jquery-2.1.3.min.js') }}"></script>
<script src="{{ asset('landing/js/plugins.js') }}"></script>
<script src="{{ asset('landing/js/main.js') }}"></script>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-108823723-1"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-108823723-1');
</script>
<script>
    $(function(){
        var ts = new Date({{config('billeterie.landing_until')->getTimestamp() * 1000}});

        if((new Date()) > ts)
        {
            location.reload();
        }

        $('div#counter').countdown({timestamp: ts})
            .on('update.countdown', function(event) {

                $(this).html(event.strftime('<div class=\"half\">' +
                    '<span>%D <sup>days</sup></span>' +
                    '<span>%H <sup>hours</sup></span>' +
                    '</div>' +
                    '<div class=\"half\">' +
                    '<span>%M <sup>mins</sup></span>' +
                    '<span>%S <sup>secs</sup></span>' +
                    '</div>'));
            });

    });
</script>


</body>

</html>