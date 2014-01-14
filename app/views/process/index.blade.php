@extends('layouts.default')

@section('content')
				<!-- START process/index --> 


				<div class="col-xs-10 col-md-offset-1">
					<div class='maincolumn CW_box_style'>

						<div class='tab'>
@include('process.nav')

									
						</div>
					</div>
				</div>
{{ javascript_include_tag() }}
				<!-- STOP process/index --> 				
@stop