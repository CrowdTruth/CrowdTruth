@extends('layouts.default')

@section('content')

				<div class="col-xs-10 col-md-offset-1">
					<div class='maincolumn CW_box_style'>
@include('preprocess.breadcrumb')						
@include('preprocess.chang.nav')
						<div class='tab'>
@yield('changContent')									
						</div>
					</div>
				</div>		
@stop