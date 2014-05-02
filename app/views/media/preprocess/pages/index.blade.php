@extends('layouts.default_new')

@section('content')
@section('pageHeader', 'Pre-process Media')

				<div class="@yield('colWidth', 'col-xs-10 col-md-offset-1')">
					<div class='maincolumn CW_box_style'>
@include('layouts.flashdata')						
						<div class='row'>
							<div class="col-xs-12">
								@include('media.layouts.nav_new')
							</div>
							<div class="col-xs-4 text-center hidden">
								<h2 class='thumbHeader'>
									@yield('preprocessHeader')
								</h2>
							</div>
						</div>			
							@yield('preprocessPage')
					</div>
				</div>
@stop