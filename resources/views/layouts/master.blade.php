<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<meta name="csrf-token" content="{!! csrf_token() !!}">

	<title>@yield('page-title') | {{ config('app.name') }}</title>

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

    <!-- Page CSS -->
    @yield('custom-css')
</head>
<body class="with-side-menu">

	@include('layouts.partials.header')

	@can('manage-directory'))
		@include('layouts.partials.menu')
	@else
		@include('layouts.partials.customer_menu')
	@endcan

	@yield('content')

    <!-- JavaScripts -->
	<script src="/js/plugins.js"></script>
	<script src="/js/app.js"></script>

	<!-- App scripts -->
	@yield('custom-js')
	@yield('custom-js-code')
	@yield('partials-js')
</body>
</html>
