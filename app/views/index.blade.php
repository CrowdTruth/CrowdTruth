@extends('layouts.default')

@section('content')
			<!-- START /index --> 			
			<div class="col-xs-8 col-md-offset-2">
				<div class='maincolumn CW_box_style'>
@include('layouts.flashdata')						

					<div class="page-header text-center" style="margin:10px;">
						<h2>Hi {{ Auth::user()->firstname }}! <small> Welcome to the CrowdTruth framework </small> </h2>
					</div>

					<ul class="media-list">
					  <li class="media" style='padding:10px;'>
						<i class="fa fa-files-o pull-left" style='font-size:40px;'></i>
					    <div class="media-body">
					      <h4 class="media-heading">Select Media</h4>
					      View existing files and their statistics; or to create batches of existing files with specific characteristics; or upload new files
					    </div>
					  </li>
					  <li class="media" style='padding:10px;'>
						<i class="fa fa-shopping-cart pull-left" style='font-size:40px;'></i>
					    <div class="media-body">
					      <h4 class="media-heading">Select Jobs</h4>
					      View existing crowdsourcing jobs or submit new ones
					    </div>
					  </li>
					  <li class="media" style='padding:10px;'>
						<i class="fa fa-users pull-left" style='font-size:40px;'></i>				  	
					    <div class="media-body">
					      <h4 class="media-heading">Select Workers</h4>
					      View worker analytics
					    </div>
					  </li>
					</ul>

				</div>
			</div>
			<!-- STOP /index--> 				
@stop