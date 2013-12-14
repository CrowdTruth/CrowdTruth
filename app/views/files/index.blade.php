@extends('layouts.default')

@section('content')
				<div class="col-xs-4">
					<div class='sidebar CW_box_style'>
						sidebar
					</div>
				</div>
				<div class="col-xs-8">
					<div class='maincolumn CW_box_style'>
@include('files.nav')
					</div>
				</div>
@stop