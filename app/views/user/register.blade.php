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
                         {{ Form::text('firstname', Input::old('firstname'), array('class' => 'form-control', 'placeholder' => 'First name', 'required', 'autofocus')) }}
                </div>
                <div class="col-xs-6">
                   {{ Form::text('lastname', Input::old('lastname'), array('class' => 'form-control', 'placeholder' => 'Last name', 'required', 'autofocus')) }}
                </div>
            </div>
            {{ Form::text('username', Input::old('username'), array('class' => 'form-control', 'placeholder' => 'Username', 'required')) }}
            {{ Form::input('email', 'email', Input::old('email'), array('class' => 'form-control', 'placeholder' => 'Email', 'required')) }}
            {{ Form::input('password', 'password', '', array('class' => 'form-control', 'placeholder' => 'Password', 'required')) }}
            {{ Form::input('password', 'confirm_password', '', array('class' => 'form-control', 'placeholder' => 'Confirm password', 'required')) }}
             {{ Form::text('invitation', Input::old('invitation'), array('class' => 'form-control', 'placeholder' => 'Invatation code', 'required')) }}
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