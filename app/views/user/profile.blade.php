@extends('layouts.default_new')
@section('title','Activity')
@section('content')
			<!-- START /index --> 			
<div class="col-xs-12 col-md-10 col-md-offset-1">
	<div class='maincolumn CW_box_style'>

		<div class='tab'>
			@include('user.nav')
			@include('layouts.flashdata')
			<div class='form-horizontal'>
				<div class='row'>
					{{ Form::label('name', 'Username', [ 'class' => 'col-xs-4 control-label' ]) }}
					<div class='col-xs-8'>
						<p class="form-control-static">{{ Auth::User()->_id }}</p>
					</div>
				</div>
				<div class='row'>
					{{ Form::label('name', 'Name', [ 'class' => 'col-xs-4 control-label' ]) }}
					<div class='col-xs-8'>
						<p class="form-control-static">{{ Auth::User()->firstname }} {{ Auth::User()->lastname }}</p>
					</div>
				</div>
				<div class='row'>
					{{ Form::label('name', 'Email', [ 'class' => 'col-xs-4 control-label' ]) }}
					<div class='col-xs-8'>
						<p class="form-control-static">{{ Auth::User()->email }}</p>
					</div>
				</div>
				<div class='row'>
					{{ Form::label('name', 'Groups', [ 'class' => 'col-xs-4 control-label' ]) }}
					<div class='col-xs-8'>-</div>
				</div>
			</div>
		</div>
	</div>
</div>
<style type="text/css">
.paperlist li {
	padding-bottom: 10px;
}
</style>
@stop