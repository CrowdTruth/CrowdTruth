@extends('layouts.default')

@section('content')
			<!-- START /index --> 			
			<div class="col-xs-8 col-md-offset-2">
				<div class='maincolumn CW_box_style'>
@include('layouts.flashdata')						

					<div class="page-header text-center" style="margin:10px;">
						<h2>Hi{{ (Auth::check() ? ' ' . Auth::user()->firstname : '') }}! <small> Welcome to the CrowdTruth framework </small> </h2>
					</div>

					<ul class="media-list">
					  <li class="media" style='padding:10px;'>
						<i class="fa fa-fw fa-files-o pull-left" style='font-size:40px;'></i>
					  <a href="/media" class="noastyle"><div class="media-body">
					      <h4 class="media-heading">Select Media</h4>
					      View existing files and their statistics; or to create batches of existing files with specific characteristics; or upload new files
					    </div></a>
					  </li>
					  <li class="media" style='padding:10px;'>
						<i class="fa fa-fw fa-shopping-cart pull-left" style='font-size:40px;'></i>
					     <a href="/jobs" class="noastyle"><div class="media-body">
					      <h4 class="media-heading">Select Jobs</h4>
					      View existing crowdsourcing jobs or submit new ones
					    </div></a>
					  </li>
					  <li class="media" style='padding:10px;'>
						<i class="fa fa-fw fa-users pull-left" style='font-size:40px;'></i>				  	
					    <a href="/workers" class="noastyle">
					    <div class="media-body">
					      <h4 class="media-heading">Select Workers</h4>
					      View worker analytics
					    </div>
					</a>
					  </li>
					  <hr>
					 <li class="media" style='padding:10px;'>
						<i class="fa fa-fw fa-info pull-left" style='font-size:40px;'></i>				  	
					    <a href="/info" class="noastyle">
					    <div class="media-body">
					      <h4 class="media-heading">Documentation</h4>
					      Information on the usage and inner workings of the CrowdTruth framework
					    </div>
					</a>
					  </li>
					  <li class="media" style='padding:10px;'>
						<i class="fa fa-fw fa-flask pull-left" style='font-size:40px;'></i>				  	
					    <a href="/papers" class="noastyle">
					    <div class="media-body">
					      <h4 class="media-heading">Papers</h4>
					      Scientific papers on CrowdTruth (harnessing disagreement)
					    </div>
					</a>
					  </li>
					<li class="media" style='padding:10px;'>
						<i class="fa fa-fw fa-puzzle-piece pull-left" style='font-size:40px;'></i>				  	
					    <a href="/team" class="noastyle">
					    <div class="media-body">
					      <h4 class="media-heading">The CrowdTruth team</h4>
					      Description of team members and their roles and affiliation
					    </div>
					</a>
					  </li>					  
					</ul>	
				</div>

<footer style="padding:10px"><span class="pull-right">Latest update: 
 <?php 
/* 	$all = array();
 
 	foreach(scandir(app_path()) as $d){
 		$path = app_path()  . DIRECTORY_SEPARATOR . $d;
 		if((is_dir($path)) and ($d!='.') and ($d!='..') and ($d!='storage')){
 			$all[]=filemtime($path);
 		}
 	}
 	foreach ($all as $one) {
 		echo date("Y-m-d", max($all)) . '<br>';
 	}*/

 	echo date("Y-m-d", filemtime(base_path() . DIRECTORY_SEPARATOR . '.git')); ?></span>
 </footer>
			</div>

<style>
.noastyle{
	text-decoration:none !important; 
	color:#333 !important;
}
</style>

			<!-- STOP /index--> 				
@stop
