<?php
// @extends('layouts.default')

// @section('content')
//<!-- START upload_content -->
?>

<!doctype html>

<html lang="en">

<head>
<meta charset="ISO-8859-1">
<title>Crowd-Watson for AMT</title>

<!-- Style sheets -->
<link href="../../app/assets/bootstrap-theme.css" rel="stylesheet">
<link href="css/bootstrap.css" rel="stylesheet">
</head>

<body class="body">
	<div class="container col-xs-10 col-sm-offset-1" style="padding-top: 30px;">
		<div class="panel panel-default">
        	<div class="panel-heading">
            	<h4><i class="fa fa-fw"></i>Jobs overview</h4>
           	</div>
        	<div style="padding-top: 10px;">
			<ul class="nav nav-tabs">
				<li class="active"><a><span class="glyphicon glyphicon-th-list"></span> List-view</a></li>
				<li class="active"><a><span class="glyphicon glyphicon-th"></span> Table-view</a></li>
			</ul>
			</div>
            <div class="panel-body" style="padding-bottom: 0px;">
            	Sort by: <a class="">Job status </a>|<a class=""> Platform </a>|<a class=""> Date </a>|<a class=""> Incurred costs </a>
            </div>
<!--         Here starts the job-panel, thus start with template here *using blade -->
            <div class="panel panel-default" style="margin:15px;">
  				<div style="padding:5px; padding-left:10px;">
    				<div class="row" style="padding: 0px;">
            			<div class="col-md-2" style="border-right: 1px solid transparent;"><a class="">123456</a></div>
              			<div class="col-md-10">Created on 6-1-2013 by Jelle</div>
               		</div>	
  					<div class="row">
	               		<div class="col-md-10"><h4>Test Job for Mock-up</h4><p>Here you could fit the job description. Based on an analysis of the output table this block should give the most important information, please let me know if you miss something or prefer to see it placed differently. Next to this, we can of course keep the table format via the tab above when more specific queries are needed.</p></div>
	               		<div class="col-md-2 pull-right" style="text-align: center; border-left: 1px solid #eee;">Days running:<p style="font-size: 20px;"><strong>2</strong></div>	
	               	</div>
	               	<div class="row">
	               		<div class="col-md-4" style="padding-top:5px; font-size:larger; vertical-align:baseline; border-right: 1px solid #eee; border-top: 1px solid #eee;"><strong style="font-size: 18px;">20</strong> Sentences<br><strong style="font-size: 18px;">10</strong> Judgments/Unit<br><strong style="font-size: 25px;">T1</strong> Template</div>
	               		<div class="col-md-2" style="padding-top:5px; border-right: 1px solid #eee; border-top: 1px solid #eee;"> on<br><h2 style="text-align: center;">AMT</h2></div>
	               		<div class="col-md-2" style="padding-top:5px; border-right: 1px solid #eee; border-top: 1px solid #eee; text-align: center;" > Max Judgm/Worker<h3 style="text-align: center;">10</h3><button class="btn btn-sm">Workers</button></div>
	               		<div class="col-md-2" style="padding-top:5px; border-top: 1px solid #eee; text-align: center;" >Payment/Unit:<strong> $0.02</strong><strong><br>Total costs:</strong><h2>$23.08</h2></div>
	               		<div class="col-md-2" style=" padding-top:5px; text-align: center; border-top: 1px solid #eee; border-left: 1px solid #eee;">Completion:<br><strong style="font-size:25px;">55%</strong><br><strong>110/200</strong><br>Judgments</div>
	               	</div>
  				</div>
  				<div class="panel-footer">
  					<button class="btn btn-primary" action="">Analyse</button>
  					<button class="btn btn-primary" action="">Details</button>
					<div class="btn-group">
    					<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user fa-fw"></i>Actions
        				<span class="caret"></span>
    					</button>
    					<ul class="dropdown-menu pull-right" role="menu">
        					<li><a href="#"><i class="fa fa-folder-open fa-fw"></i>Pause Job</a></li>
        					<li><a href=""><i class="fa fa-sign-out fa-fw"></i>Cancel Job</a></li>
        					<li class="divider"></li>
        					<li><a href=""><i class="fa fa-sign-out fa-fw"></i>Delete Job</a></li>
    					</ul>
					</div>
					
				</div>
			</div>
      	</div>      		
	</div>
</body>