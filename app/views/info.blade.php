@extends('layouts.default')

@section('content')
			<!-- START /index --> 			
			<div class="col-xs-12">
				<div class='maincolumn CW_box_style'>
@include('layouts.flashdata')	
					<div class="row">
						<div class="col-xs-3">
						<ul class="nav nav-pills nav-stacked"  data-spy="scroll" data-target=".scrolldiv">
						 <li><a href="#heading">heading</a></li>
						 <li><a href="#heading2">heading 2</a></li>
						</ul>
					</div>
						<div class="scrolldiv col-xs-9">
							<h3 id="heading">Heading</h3>
							<p></p>
						</div>
					</div>
				</div>
			</div>
@stop