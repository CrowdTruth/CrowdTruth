@extends('layouts.default_new')
@section('title','Profile')
@section('content')
			<!-- START /index --> 			
<div class="col-xs-12 col-md-10 col-md-offset-1">
	<div class='maincolumn CW_box_style'>
		<div class='tab'>
			@include('users.nav', array('user'=>$user))
			@include('layouts.flashdata')
			<div class="media">
	            <a class="pull-left" href="#">
	                <img class="media-object dp img-circle" src="{{ $user['avatar'] or 'http://crowdtruth.org/wp-content/uploads/2014/09/male.jpg' }}" style="width: 100px;height:100px;">
	            </a>
	            <div class="media-body" style='margin-top:20px;margin-left:20px; height:120px;'>
	                <h4 class="media-heading">{{ $user['firstname'] }} {{ $user['lastname'] }} <small> Administrator</small></h4>
	                <h5>Researcher at <a href="http://gridle.in">VU University, Amsterdam</a></h5>
	                <hr style="margin:8px auto">

	                <div class="label label-default">Linked In</div>
	                <div class="label label-info">Twitter</div>
	            </div>
				
				<div class="panel panel-default">
					<div class="panel-heading">
						Projects
					</div>
					<div class="panel-body" id="userRolePanel">
						@foreach($projects as $project)
							{{ link_to(action('ProjectController@getProfile', [ $project['name']  ]), $project['name'], array('class' => 'badge')) }}
						@endforeach
					</div>
				</div>

				<div style='height:200px;'>
				</div>
	        </div>
		</div>
	</div>
</div>
@stop
