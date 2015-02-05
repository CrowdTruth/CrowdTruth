<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">
		<title>CrowdTruth</title>
		{{ stylesheet_link_tag() }}
		{{ stylesheet_link_tag('custom/user.css') }}

	</head>
	<body>

<div class="table">
    <div class="child">
        <div class="login well">
        	@include('layouts.flashdata')
        	{{ Form::open(array('url' => 'user/login', 'class' => 'form', 'role' => 'form')) }}
            <input class="form-control" name="username_or_email" placeholder="Username or Email" type="text" required autofocus />
            <input class="form-control" name="password" placeholder="Password" type="password" required />
            <button class="btn btn-lg btn-primary btn-block" type="submit">Log in</button>
            <div class='hr-or'></div>
            <a href='{{ URL::to('register') }}' class='btn btn-lg btn-success btn-block'>Register</a>
            </form>
        </div>

        </div>
    </div>
</div>
	{{ javascript_include_tag() }}
	</body>
</html>