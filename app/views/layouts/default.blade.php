<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">
		<link rel="shortcut icon" href="/favicon.ico" >
		<title>CrowdTruth</title>
		<!--<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>-->
		{{ stylesheet_link_tag() }}
@yield('head')
	</head>
	<body>
		@include('layouts.navbar')
		
		<div class="
		@yield('container', 'container') 
		CW_{{Request::segment(2)}}">
			<div class="row">
@yield('content')
			</div>   
		</div>
	</div>

@yield('modal')	
{{ javascript_include_tag() }}

<script>
$('.navbar-nav li').tooltip({
	"html" : true
});
</script>

@yield('end_javascript')
@yield('platformend')
@yield('selection_user_javascript')
	</body>
</html>