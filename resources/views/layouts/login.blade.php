<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">

    <title>Login | {{ config('app.name') }}</title>

	<link href="/img/icon/apple-touch-icon-144x144.png" rel="apple-touch-icon" type="image/png" sizes="144x144">
	<link href="/img/icon/apple-touch-icon-114x114.png" rel="apple-touch-icon" type="image/png" sizes="114x114">
	<link href="/img/icon/apple-touch-icon-72x72.png" rel="apple-touch-icon" type="image/png" sizes="72x72">
	<link href="/img/icon/apple-touch-icon-57x57.png" rel="apple-touch-icon" type="image/png">
	<link href="/img/icon/apple-touch-icon-png" rel="icon" type="image/png">
	<link href="/img/icon/favicon.ico" rel="shortcut icon">

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->

    <link rel="stylesheet" href="/css/lib/font-awesome/font-awesome.min.css">
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/pages/login.css">
</head>
<body>

    @yield('content')

    <!-- JavaScripts -->
	<script src="/js/plugins.js"></script>
	<script src="/js/app.js"></script>

    <script type="text/javascript" src="/js/lib/match-height/jquery.matchHeight.min.js"></script>
    <script>
        $(function() {
            $('.page-center').matchHeight({
                target: $('html')
            });

            $(window).resize(function(){
                setTimeout(function(){
                    $('.page-center').matchHeight({ remove: true });
                    $('.page-center').matchHeight({
                        target: $('html')
                    });
                },100);
            });
        });
    </script>

</body>
</html>
