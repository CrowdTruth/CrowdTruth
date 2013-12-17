<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">
		<title>Crowd-Watson</title>
		<?= stylesheet_link_tag() ?>
	</head>
	<body>
@include('layouts.navbar')

		<div class="container CW_{{Request::segment(1)}}">
			<div class="row">
@yield('content')
			</div>   
		</div>
	</div>
	<?= javascript_include_tag() ?>
	</body>
</html>