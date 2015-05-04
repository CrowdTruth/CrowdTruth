@extends('layouts.default_new')

@section('head')
@stop

@section('title','Profile Settings')
@section('content')

<div class="col-xs-12 col-md-10 col-md-offset-1">
	<div class='maincolumn CW_box_style'>
		<div class='tab'>
			@include('projects.nav', array('project'=>$project))
			@include('layouts.flashdata')
			<div class='title'>
				<h2>Project: {{ $project }}</h2>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					Administrators
				</div>
				<div class="panel-body" id="userRolePanel">
					@foreach($users['admin'] as $user)
						<span>{{ link_to('user/' . $user, $user, array('class' => 'btn btn-sm btn-warning')) }}</span>
					@endforeach
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					Members
				</div>
				<div class="panel-body" id="userRolePanel">
					@foreach($users['member'] as $user)
						{{ link_to('user/' . $user, $user, array('class' => 'btn btn-sm btn-success')) }}
					@endforeach
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					Guests
				</div>
				<div class="panel-body" id="userRolePanel">
					@foreach($users['guest'] as $user)
						<span>{{ link_to('user/' . $user, $user, array('class' => 'badge')) }}</span>
					@endforeach
				</div>
			</div>
		</div>
	</div>
</div>
@stop

@section('end_javascript')
{{ javascript_include_tag('bootstrap-select.js') }}

<script>
</script>

@stop
