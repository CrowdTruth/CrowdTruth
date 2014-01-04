<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">
		<title>Crowd-Watson</title>
		{{ stylesheet_link_tag() }}
		{{ stylesheet_link_tag('custom/user.css') }}

	</head>
	<body>

<div class="table">
    <div class="child">
        <div class="register well">
        	@include('layouts.flashdata')
            {{ Form::open(array('url' => 'user/register', 'class' => 'form', 'role' => 'form')) }}
            <div class="row">
                <div class="col-xs-6">
                    <input class="form-control" name="firstname" placeholder="First Name" type="text"
                        required autofocus />
                </div>
                <div class="col-xs-6">
                    <input class="form-control" name="lastname" placeholder="Last Name" type="text" required />
                </div>
            </div>
            <input class="form-control" name="username" placeholder="Username" type="text" required />
            <input class="form-control" name="email" placeholder="Email" type="email" required />
            <input class="form-control" name="password" placeholder="Password" type="password" required />
            <input class="form-control" name="confirm_password" placeholder="Confirm Password" type="password" required />
            <input class="form-control" name="invitation" placeholder="Invitation code" type="text" required />
            <button class="btn btn-lg btn-primary btn-block" type="submit">Sign up</button>
            <div class='hr-or'></div>
            <a href='{{ URL::to('user/login') }}' class='btn btn-lg btn-success btn-block'>Log in</a>            
            </form>
        </div>

        </div>
    </div>
</div>
    {{ javascript_include_tag() }}
	</body>
</html>