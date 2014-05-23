@extends('layouts.default')

@section('content')
				<div class="@yield('colWidth', 'col-xs-10 col-md-offset-1')">
					<div class='maincolumn CW_box_style'>
@include('layouts.flashdata')						
						<div class='row'>
							<div class="col-xs-8">
								@include('preprocess.nav')
							</div>
							<div class="col-xs-4 text-center">
								<h2 class='thumbHeader'> Relex &nbsp;<small> {{ Request::segment(3); }}</small></h2>
							</div>
						</div>					
						@include('preprocess.relex.layouts.breadcrumb')
						<div class='tab'>
							@yield('relexContent')
						</div>
					</div>
				</div>		
@stop