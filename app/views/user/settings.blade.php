@extends('layouts.default_new')
@section('title','Activity')
@section('content')
			<!-- START /index --> 			
			<div class="col-xs-12 col-md-8 col-md-offset-2">
				<div class='maincolumn CW_box_style'>
					@include('layouts.flashdata')	
					<div class="page-header text-center" style="margin:10px;">
						<h2><i class="fa fa-gears"></i> Profile</h2>
					</div>
					<div class="row">
						<div class="col-xs-12">
							@include('user.nav')
						</div>
						<div class="col-xs-10 col-xs-offset-1"  style="padding-bottom:40px; padding-top:20px">
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