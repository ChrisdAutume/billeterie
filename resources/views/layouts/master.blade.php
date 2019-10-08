<!DOCTYPE html>
<html>
	<head>
	    <meta charset="UTF-8">
	    <title>@yield('title') - Billetterie</title>

	    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">


	    @yield('sublayout-css')
	    @yield('css')
	    <!--[if lt IE 9]>
	        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	    <![endif]-->

	    <link href="{{ @asset('/css/style.css') }}" rel="stylesheet" type="text/css" />

	</head>
	<body>
		@yield('bodycontent')

	    <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
	    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	    <script src="{{ @asset('js/admin.min.js') }}" type="text/javascript"></script>
	    @yield('js')
	    @yield('sublayout-js')
		@if(config('billeterie.piwik'))
		<!-- Piwik -->
		<script type="text/javascript">
			var _paq = _paq || [];
			_paq.push(['trackPageView']);
			_paq.push(['enableLinkTracking']);
			(function() {
				var u="//piwik.uttnetgroup.fr/";
				_paq.push(['setTrackerUrl', u+'piwik.php']);
				_paq.push(['setSiteId', '{{ config('billeterie.piwik') }}']);
						@yield('subpiwik')
				var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
				g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
			})();
		</script>
		<noscript><p><img src="//piwik.uttnetgroup.fr/piwik.php?idsite={{ config('billeterie.piwik') }}" style="border:0;" alt="" /></p></noscript>
		<!-- End Piwik Code -->
		@endif
		@if(config('billeterie.analytics'))
		<script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', '{{ config('billeterie.analytics') }}');
		</script>
		@endif
	</body>
</html>
