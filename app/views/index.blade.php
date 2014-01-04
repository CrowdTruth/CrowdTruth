@extends('layouts.default')

@section('content')

			<!-- START /index --> 			
			<div class="col-xs-8 col-md-offset-2">
				<div class='maincolumn CW_box_style'>
@include('layouts.flashdata')						

					<div class="page-header text-center" style="margin:10px;">
						<h2>Hi {{ Auth::user()->firstname }}! <small> Welcome to the Crowd-Watson framework </small> </h2>
					</div>


					<div style='padding:10px 30px 20px 10px; text-align:center;'>
					Post any issues you may find in either Google Docs or Github (Links below).
					<br />
					<br />
					<div class="btn-group">
						<a href='http://bit.ly/cw-documentation' class='btn btn-default'>View Documentation</a>
						<a href='http://bit.ly/cw-issues' class='btn btn-default'>Post issue(s) in gDoc</a>
						<a href='https://github.com/khamkham/crowd-watson/issues' class='btn btn-default'>Github issues</a>
					</div>

					</div>

				</div>
			</div>
			<!-- STOP /index--> 				
@stop