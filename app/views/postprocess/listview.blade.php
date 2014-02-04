@extends('layouts.default')

@section('content')
	<div class="container">
		<div class="row">
			
			<div  id="filtercolumn" class="col-md-2 ">

			<!-- Left column for sorting -->
			
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Sort by:</h3>
					</div>
					<div class="panel-body" style="border-bottom: 1px solid #eee">
						<i class="fa fa-check-circle"></i> {{Form::label('user', 'Completion')}}<br>
					</div>
					<div class="panel-body" style="border-bottom: 1px solid #eee">
						<i class="fa fa-dollar"></i> {{Form::label('user', 'Cost incurred')}}<br>
					</div>
					<div class="panel-body" style="border-bottom: 1px solid #eee">
						<i class="fa fa-clock-o"></i> {{Form::label('user', 'Running time')}}<br>
					</div>
					<div class="panel-body">
						<i class="fa fa-gavel"></i> {{Form::label('user', 'Job size')}}<br>
					</div>
				</div>
			<!-- Left column for filters -->
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Apply filter:</h3>
					</div>
					<div class="panel-body" style="border-bottom: 1px solid #eee">
						<i class="fa fa-user"></i> {{Form::label('user', 'Created by:')}}<br>
						{{Form::input('','')}}
					</div>
					<div class="panel-body" style="border-bottom: 1px solid #eee">
						<i class="fa fa-users"></i> {{Form::label('user', 'Platform:')}}<br>
						{{Form::checkbox('')}} CrowdFlower<br>
						{{Form::checkbox('')}} Amazon MTurk
					</div>
					<seperator/>
					<div class="panel-body" style="border-bottom: 1px solid #eee">
						<i class="fa fa-file"></i> {{Form::label('user', 'Template:')}}<br>
						{{Form::checkbox('')}} Relation Direction<br>
						{{Form::checkbox('')}} Relation Extraction<br>
						{{Form::checkbox('')}} Factor Span
					</div>
					<div class="panel-body">
						More??
					</div>
				</div>
			

			<!-- END OF LEFT COLUMN HERE -->
			</div>

			<!-- Main column with results -->
			<div id="results" class="col-md-10">
				<?php
					$id=0; 
				?>
				@foreach($crowdtasks as $crowdtask)
				<?php $id++ ?>
					<div class="panel panel-default">
						<!-- Top row is panel heading with creation date and creator -->
						<div class="panel-heading clearfix">
							<div style="width: 25%; float:left;">
								<a class="">{{$crowdtask->id}}</a>
							</div>
	              			<div style="float:left;">
	              				Created on {{ date('D d M Y H:i ', strtotime($crowdtask->created_at)) }} by {{$crowdtask->creator}}
		              		</div>
			           		<div class="pull-right" style="width: 33%;">
			           			<div class="progress" style="margin-bottom: 0px;">	
			           				<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="{{$crowdtask->progressBar()}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$crowdtask->progressBar()}}%;">
		   								<span class="sr-only">{{$crowdtask->progressBar()}}% Complete</span>
		  							</div>
		              			</div>
		              		</div>
	               		</div>
	               		<!-- First content row with title and description of the ct and the elapsed time -->
	               		<div class="panel-body" style="padding-top: 0px; padding-bottom: 0px;">
		               		<div class="row" style="border-bottom: 1px solid #eee;">
			               		<div class="col-md-10" style="border-right: 1px solid #eee;">
			               			<h4>{{ $crowdtask->name }}</h4>
			               			<p>{{ $crowdtask->description }}</p>
			               			<strong style="font-size: 18px;"><i class="fa fa-file"></i> {{ $crowdtask->template}}</strong> 
			               		</div>
			               		<div class="col-md-2" style="text-align: center; padding-top: 15px;">
			               			<strong style="font-size: 24px; "><i class="fa fa-clock-o fa-2x"></i><br> {{ $crowdtask -> getElapsedTime($crowdtask->created_at) }}</strong></p>
			               		</div>	
			               	</div>
			               	<div class="row" style="height: 90px;">
			               		<!-- This row has the following content: #sentences, judgments/unit and template info; block with worker info; block of costs, block of completion percentage; -->
			               		<div class="col-md-2" style="border-right: 1px solid #eee; height: 100%; text-align: center; display: table-cell; vertical-align: middle; padding-top: 10px;">
			               			<strong style="font-size: 24px;"><i class="fa fa-bars"></i> {{ $crowdtask->unitsPerTask }}</strong><br>
			               			<strong style="font-size: 24px;"><i class="fa fa-gavel"></i> {{ $crowdtask->judgmentsPerUnit }}</strong><br>
			               		</div>
			               		<div class="col-md-4" style="border-right: 1px solid #eee; height: 100%; text-align: center; display: table-cell; padding-top: 10px; vertical-align: middle;"> 
			               			<h2><i class="fa fa-users"></i> {{implode(", ", $crowdtask->platform)}}</h2>
			               		</div>
			               		<div class="col-md-2" style="border-right: 1px solid #eee; text-align: center; display: table-cell; padding-top: 5px; font-size: 32px; vertical-align: middle;"> 
			                   		<i class="fa fa-flag"></i>  {{$crowdtask->flaggedWorkers}}</strong><br>
			                   		<button class="btn btn-sm">Workers</button>
				               	</div>
							    <div class="col-md-2" style="border-right: 1px solid #eee; height: 100%; text-align: center; display: table-cell; padding-top: 10px; vertical-align: middle;">
							    	<i class="fa fa-dollar"></i><strong> /</strong> <i class="fa fa-gavel"></i> <strong> ${{ $crowdtask->reward }}</strong>
							       	<h2><i class="fa fa-dollar"></i> {{number_format((float)$crowdtask->totalCost(), 2, '.', '')}}</h2>
							    </div>
							    <div class="col-md-2" style="text-align: center; height: 100%; display: table-cell; vertical-align: middle; padding-top: 10px;">
							    	<strong> <i class="fa fa-gavel"></i> {{$crowdtask->completedJudgments()}} / {{$crowdtask->totalJudgments()}}</strong>
							    	<h2><i class="fa fa-check-circle"></i> {{round(($crowdtask->completedJudgments() / $crowdtask->totalJudgments()) * 100, 2)}}%</h2>
			               		</div>
							</div>
							 <!-- Here starts the hidden details field, see js at bottom of page -->
							<div id="details-<?php echo $id ?>" class="row" style="display: none;">
					            <table class="table table-striped">
					           	@foreach($crowdtask->getDetails() as $key=>$val)
					           		@if (is_array($val))
									<tr><th>{{ $key }}</th><td><pre><?php var_dump($val) ?></pre></td></tr>
									@else
									<tr><th>{{ $key }}</th><td>{{ $val }}</td></tr>
									@endif
					 	    	@endforeach
					 	    	</table>
	          				</div>
	          			</div>
						<!-- Here starts the panel footer -->
	               		<div class="panel-footer">
	               			<div class="row">
	               				<div style="padding: 3px; padding-left: 5px; float: left;" >
	               					<button class="btn btn-primary" action="">Analyse</button>
	               				</div>
					  			<div data-toggle="buttons" style="float:left; padding: 3px;">
						  			<label class="btn btn-primary">
										<input type="checkbox" name="Details" id="detail-<?php echo $id ?>" onChange="showDetails(<?php echo $id ?>)">Details
									</label>
								</div>
								<div class="btn-group" style="float: left; padding: 3px;">
									<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user fa-fw"></i>Actions
						    			<span class="caret"></span>
					   				</button>
					 				<ul class="dropdown-menu" role="menu">
					       				<li><a href="#"><i class="fa fa-folder-open fa-fw"></i>Pause Job</a></li>
					       				<li><a href=""><i class="fa fa-sign-out fa-fw"></i>Cancel Job</a></li>
					       				<li class="divider"></li>
					       				<li><a href=""><i class="fa fa-sign-out fa-fw"></i>Duplicate Job</a></li>
					       				<li><a href=""><i class="fa fa-sign-out fa-fw"></i>Delete Job</a></li>
					   				</ul>
								</div>
							</div>
						</div>								
					<!--End of panel  -->
					</div>	
				@endforeach
			<!-- Close results column -->
			</div>
		<!-- Close row -->
		</div>
	<!-- Close container -->
	</div>
@section('end_javascript')
	


	<script src="http://codeorigin.jquery.com/jquery-1.10.2.min.js"></script>
	<script>
		function showDetails(id){           
			$('#details-'+id).toggle(this.checked);
			}

		 $(function() {
			var cache = {};
			$( "#birds" ).autocomplete({
				minLength: 2,
				source: function( request, response ) {
							var term = request.term;
							if ( term in cache ) {
								response( cache[ term ] );
								return;
								}
							$.getJSON( "search.php", request, function( data, status, xhr ) {
								cache[ term ] = data;
								response( data );
								});
							}		
			});
		});

	</script>
@stop