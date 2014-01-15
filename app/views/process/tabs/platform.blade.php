@extends('layouts.default')

@section('content')
	

				<div class="col-xs-10 col-md-offset-1">
					<div class='maincolumn CW_box_style'>

						<div class='tab'>
@include('process.nav')
@include('layouts.flashdata')
									
						</div>
						<div>
							{{ Form::open(array( ))}}
							{{ Form::checkbox('cf', 'Crowdflower'); }}
							{{ Form::checkbox('amt', 'AMT'); }}
							{{ Form::close()}}
						</div>

					</div>
				</div>
	
@stop