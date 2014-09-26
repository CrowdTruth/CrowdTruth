@extends('layouts.default_new')
@section('content')
<!-- START /index -->
<style>
.row {
	margin-top: 20px;
}
</style>
<div class="col-xs-8 col-md-offset-2">
	<div class='maincolumn CW_box_style'>
		<div class="page-header text-center" style="margin:10px;">
			<h2><i class="fa fa-angle-left" style="float:left; color:#999; display:inline-block; cursor:pointer" onclick="javascript:window.history.back()"></i>Contact <small>the CrowdTruth team</small></h2>
		</div>
		@include('layouts.flashdata')
		<div class="row">
			<div class="col-xs-10 col-xs-offset-2"  style="padding-bottom:40px; padding-top:10px">
				{{ Form::open(array('url' => 'contact_request')) }}
				<div class="row">
					<label class="control-label col-xs-10" for="name"><i class='fa fa-fw fa-user'></i> Name</label>
					<div class="col-xs-10">
						{{ Form::text('name', null, array('class' => 'form-control col-xs-10', 'placeholder' => 'Your full name')) }}
					</div>
				</div>
				<div class="row">
					<label class="control-label col-xs-10" for="email"><i class='fa fa-fw fa-envelope'></i> E-mail</label>
					<div class="col-xs-10">
						{{ Form::email('email', null, array('class' => 'form-control col-xs-10', 'placeholder' => 'Your e-mail adress')) }}
					</div>
				</div>
				<div class="row">
					<label class="control-label col-xs-10" for="message"><i class='fa fa-fw fa-pencil'></i> Message</label>
					<div class="col-xs-10">
						{{ Form::textarea('message', null, array('class' => 'form-control col-xs-10', 'placeholder' => 'Your message')) }}
					</div>
				</div>
				<div class="row">
					<div class="col-xs-10">
						{{ Form::submit('Send', array('class' => 'btn btn-primary pull-right')) }}
					</div>
				</div>
				{{ Form::close() }}
			</div>
		</div>
	</div>
</div>
@stop