@extends('layouts.default_new')
@section('title','Profile')
@section('content')
			<!-- START /index --> 			
<div class="col-xs-12 col-md-10 col-md-offset-1">
	<div class='maincolumn CW_box_style'>

		<div class='tab'>
			@include('user.nav', array('user'=>$user))
			@include('layouts.flashdata')
		        <div class="media">
            <a class="pull-left" href="#">
                <img class="media-object dp img-circle" src="{{ $user['avatar'] or 'http://crowdtruth.org/wp-content/uploads/2014/09/male.jpg' }}" style="width: 100px;height:100px;">
            </a>
            <div class="media-body" style='margin-top:20px;margin-left:20px;'>
                <h4 class="media-heading">{{ $user['firstname'] }} {{ $user['lastname'] }} <small> Administrator</small></h4>
                <h5>Researcher at <a href="http://gridle.in">VU University, Amsterdam</a></h5>
                <hr style="margin:8px auto">

                <div class="label label-default">Linked In</div>
                <div class="label label-info">Twitter</div>
            </div>
			<div style='height:200px;'></div>
        </div>

        <div class="row">
			<div class="col-xs-10 col-xs-offset-1"  style="padding-bottom:40px; padding-top:20px">
				<?php 
				// Authenticate the user
				try {
					// $user = Sentry::findUserByLogin('carlosm');
					// Login credentials
					$credentials = array(
							// 'email'    => 'c.martinez@esciencecenter.nl',
							'login'    => 'carlosm',
							'password' => 'neocarlos',
					);
					// $user = Sentry::authenticate($credentials, false);
					// echo $user->_id;
				}
				catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
				{
					echo 'Login field is required.';
				}
				catch (Cartalyst\Sentry\Users\PasswordRequiredException $e)
				{
					echo 'Password field is required.';
				}
				catch (Cartalyst\Sentry\Users\WrongPasswordException $e)
				{
					echo 'Wrong password, try again.';
				}
				catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
				{
					echo 'User was not found.';
				}
				catch (Cartalyst\Sentry\Users\UserNotActivatedException $e)
				{
					echo 'User is not activated.';
				}
				// The following is only required if the throttling is enabled
				catch (Cartalyst\Sentry\Throttling\UserSuspendedException $e)
				{
					echo 'User is suspended.';
				}
				catch (Cartalyst\Sentry\Throttling\UserBannedException $e)
				{
					echo 'User is banned.';
				}
				?>
				<br>
				<?php 
				/*if ( ! Sentry::check())
				{
					echo 'user not logged in...';
				}
				else
				{
					echo 'User is logged in...';
				}*/
				?>
				<?php
				// Create the group
				/*
				try {
					$group = Sentry::createGroup([
							'name'        => 'Moderator',
							'permissions' => [
									'admin' => 1,
									'users' => 1,
							],
					]);
					echo print_r($group, true);
				} catch (Cartalyst\Sentry\Groups\NameRequiredException $e) {
					echo 'Name field is required';
				} catch (Cartalyst\Sentry\Groups\GroupExistsException $e) {
					echo 'Group already exists';
				}*/
				
				// Find group
				try {
					$group = Sentry::findGroupByName('Moderator');
					// echo print_r($group, true);
					
					// Get the group permissions
					$groupPermissions = $group->getPermissions();
					// echo print_r($groupPermissions, true);
				} catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e) {
					echo 'Group was not found.';
				}

				try {
					$user = Sentry::findUserByLogin('carlosm');
					// echo $user->_id;

					// Find the group using the group id
					$group = Sentry::findGroupByName('Moderator');
					//echo print_r($group, true);
				
					// Assign the group to the user
					//$user->addGroup($group);
					
					//echo print_r($user, true);
					
					$groups = $user->getGroups();
					// echo print_r($groups, true);
					
					// Get the user permissions
					// $permissions = $user->getPermissions();
					$permissions = $user->getMergedPermissions();
					// echo print_r($permissions, true);
					
					// if($user->hasAccess('admin')) {
					if ($user->hasAnyAccess([ 'admin', 'foo' ])) {
						echo 'User has permission<br>';
					} else {
						echo 'User does not have permission<br>';
					}
				}
				catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
				{
					echo 'Login field is required.';
				}
				catch (Cartalyst\Sentry\Users\PasswordRequiredException $e)
				{
					echo 'Password field is required.';
				}
				catch (Cartalyst\Sentry\Users\UserExistsException $e)
				{
					echo 'User with this login already exists.';
				}
				catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
				{
					echo 'Group was not found.';
				}
				?>
			</div>
		</div>
	</div>
</div>
@stop