@extends('layouts.default_new')

@section('content')
			<!-- START /index --> 			
			<div class="col-xs-12 col-md-8 col-md-offset-2">
				<div class='maincolumn CW_box_style'>		

					<div class="page-header text-center" style="margin:10px;">
						<h2>Hi{{ (Auth::check() ? ' ' . Auth::user()->firstname : '') }} <small> Welcome to the CrowdTruth framework </small> </h2>
					</div>
					@include('layouts.flashdata')
					<div class='row'>
						<div class="lead text-center">
							Try out the CrowdTruth tools:
						</div>
						<div class="col-xs-12 col-md-10 col-md-offset-1">

							<div class="col-xs-4">
								<div class='tools'>
									<a href="/media/upload" class="noastyle">
									<i class="fa fa-upload" style='font-size:400%;'></i>
										<h4>Add Media</h4>
										<div>Load your data or an online source into the platform</div>
									</a>
								</div>
							</div>
							<div class="col-xs-4">
								<div class='tools'>
									<a href="/jobs/batch" class="noastyle">
										<i class="fa fa-edit" style='font-size:400%; margin-left:8%;'></i>
										<h4>Create Job</h4>
										<div>Order a new crowdsourcing job for your media</div>
									</a>
								</div>
							</div>
							<div class="col-xs-4">
								<div class='home-disabled'>
								
										<i class="fa fa-desktop" style='font-size:400%;'></i>
										<h4>Create Template</h4>
										<div>Design your own crowdsourcing task</div>

								</div>
							</div>
							<div class="col-xs-4">
								<div class='tools'>
									<a href="/media" class="noastyle">
										<i class="fa fa-files-o" style='font-size:400%;'></i>
										<h4>Select Media</h4>
										<div>View existing media and their statistics, and create batches</div>
									</a>
								</div>
							</div>
							<div class="col-xs-4">
								<div class='tools'>
									<a href="/jobs" class="noastyle">
										<i class="fa fa-shopping-cart" style='font-size:400%;'></i>
										<h4>Select Jobs</h4>
										<div>View existing crowdsourcing jobs or submit new ones</div>
									</a>
								</div>
							</div>
							<div class="col-xs-4">
								<div class='tools'>
									<a href="/workers" class="noastyle">
										<i class="fa fa-users" style='font-size:400%;'></i>
										<h4>Select Workers</h4>
										<div>View worker analytics and flag spammers</div>
									</a>
								</div>
							</div>
						</div>
					</div>
					<hr />
					<div class='row'>
						<div class="lead text-center">
							Read more about CrowdTruth:
						</div>
						<div class='col-xs-12 col-md-6'>
							<ul class='website'>
								<li>
									<i class="fa fa-fw fa-info pull-left" style='font-size:40px;'></i>				  	
									<a href="https://github.com/CrowdTruth/CrowdTruth/wiki" target="_blank" class="noastyle">
									<div class="media-body">
									  <h4 class="media-heading">Documentation</h4>
									  Information on the usage and inner workings of CrowdTruth
									</div>
								</a>
								  </li>
								<li>
									<i class="fa fa-fw fa-github pull-left" style='font-size:40px;'></i>				  	
									<a href="https://github.com/CrowdTruth/CrowdTruth/" target="_blank" class="noastyle">
									<div class="media-body">
									  <h4 class="media-heading">Source code</h4>
									  Download the latest version from Github
									</div>
								</a>
								  </li>
								<li>
									<i class="fa fa-fw fa-file-code-o pull-left" style='font-size:40px;'></i>				  	
									<a href="https://github.com/CrowdTruth/CrowdTruth/wiki/templates" target="_blank" class="noastyle">
									<div class="media-body">
									  <h4 class="media-heading">Example Templates</h4>
									  Example templates and code snippets to get you started
									</div>
								</a>
								  </li>
							</ul>
						</div>
						<div class='col-xs-12 col-md-6'>
							<ul class='website'>
								<li>
									<i class="fa fa-fw fa-newspaper-o pull-left" style='font-size:40px;'></i>				  	
									<a href="http://crowdtruth.org" target="_blank" class="noastyle">
									<div class="media-body">
									  <h4 class="media-heading">Blog</h4>
									  Read the latest news on our development blog.
									</div>
								</a>
								  </li>
								  <li>
									<i class="fa fa-fw fa-flask pull-left" style='font-size:40px;'></i>				  	
									<a href="http://crowdtruth.org/papers" target="_blank" class="noastyle">
										<div class="media-body">
										  <h4 class="media-heading">Papers</h4>
										  Scientific papers on CrowdTruth (harnessing disagreement)
										</div>
									</a>
								  </li>
								 <li>
									<i class="fa fa-fw fa-slideshare pull-left" style='font-size:40px;'></i>				  	
									<a href="http://crowdtruth.org/presentations" target="_blank" class="noastyle">
										<div class="media-body">
										  <h4 class="media-heading">Presentations</h4>
										  Scientific presentations on CrowdTruth
										</div>
									</a>
								  </li>	
							</ul>
						</div>
						
					</div>
					<hr />
					<div class='row'>	
						<div class="col-xs-4 text-center">
							<a href="http://crowdtruth.org/team" target="_blank" class="noastyle">
								<i class="fa fa-fw fa-puzzle-piece"></i> Team
							</a>
						</div>						
						<div class="col-xs-4 text-center">
							<a href="http://crowdtruth.org/partners" target="_blank" class="noastyle">
								<i class="fa fa-fw fa-university"></i> Partners
							</a>
						</div>
						<div class="col-xs-4 text-center">
							<a href="http://crowdtruth.org/contact" target="_blank" class="noastyle">
								<i class="fa fa-fw fa-envelope-o"></i> Contact
							</a>
						</div>
					</div>
				</div>
				<footer style="padding:10px; margin-bottom:20px; text-align:center;">Latest update: 
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

		echo date("Y-m-d", filemtime(base_path() . DIRECTORY_SEPARATOR . '.git')); ?>
	 </footer>
			</div>

<style>
.noastyle{
	text-decoration:none !important; 
	color:#333 !important;
	display:block;
}
.tools {
	margin: 5px -10px 5px -10px;
	height:150px;
	overflow:hidden;
	text-align:center;
	padding: 10px 4px 10px 4px;
	background-color: #f5f3b4;
	background-image: -webkit-linear-gradient(#fffff5, #f5f3b4);
	background-image: linear-gradient(#fffff5, #f5f3b4);
	background-repeat: repeat-x;
	color: #666;
	border: 1px solid #dfddb5;
	border-radius: 3px;
	box-shadow: 0 1px 0 #fff;
	-webkit-transition: border 0.2s;
    transition: border 0.2s;
}
.tools:hover {
	border: 1px solid #bfbd05;
}
.home-disabled {
	margin: 5px -10px 5px -10px;
	height:150px;
	overflow:hidden;
	text-align:center;
	padding: 10px 4px 10px 4px;
	background-color: #D6D6D6;
	background-image: -webkit-linear-gradient(#fffff5, #D6D6D6);
	background-image: linear-gradient(#fffff5, #D6D6D6);
	background-repeat: repeat-x;
	color: #666;
	border: 1px solid #C6C6C6;
	border-radius: 3px;
	box-shadow: 0 1px 0 #fff;
}
.tools div, .home-disabled div {
	font-size:13px;
}
.tools h4, .home-disabled h4 {
	margin-bottom:5px;
}
.website {
    list-style:none;
    padding-left:0;
}
.website li {
	margin:20px 0 20px 0;
}
</style>

			<!-- STOP /index--> 				
@stop
