@extends('layouts.default')

@section('content')
	<div class="container">
		<div class="row">
			<!-- Left column for filters -->
			<div  id="filtercolumn" class="col-md-2 ">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Filters</h3>
					</div>
					<div class="panel-body" style="border-bottom: 1px solid #eee">
						Filter #1
					</div>
					<div class="panel-body" style="border-bottom: 1px solid #eee">
						Filter #2
					</div>
					<seperator/>
					<div class="panel-body" style="border-bottom: 1px solid #eee">
						Filter #3
					</div>
					<seperator/>
					<div class="panel-body">
						Filter #4
					</div>
				</div>
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
						<div class="panel-heading">
							<div style="width: 25%; float:left;">
								<a class="">{{$crowdtask->id}}</a>
							</div>
	              			<div style="float:left;">
	              				Created on {{ date('D d M Y H:i ', strtotime($crowdtask->created_at)) }} by {{$crowdtask->creator}}
		              		</div>
			           		<div class="pull-right" style="width: 33%;">
			           			<div class="progress" style="height: 15px;">	
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
			               			<h4>{{ $crowdtask->name }}</h4><p>{{ $crowdtask->description }}</p>
			               		</div>
			               		<div class="col-md-2" style="text-align: center; margin-top: 20px;">
			               			<strong style="font-size: 20px; "><span class="glyphicon glyphicon-time"></span><br> {{ $crowdtask -> getElapsedTime($crowdtask->created_at) }}</strong></p>
			               		</div>	
			               	</div>
			               	<div class="row">
			               		<!-- This row has the following content: #sentences, judgments/unit and template info; block with worker info; block of costs, block of completion percentage; -->
			               		<div class="col-md-4" style="border-right: 1px solid #eee;">
			               			<strong style="font-size: 18px;">{{ $crowdtask->unitsPerTask }}</strong> Sentences<br>
			               			<strong style="font-size: 18px;">{{ $crowdtask->judgmentsPerUnit }}</strong> Judgments/Unit<br>
			               			Template: <strong style="font-size: 16px;">{{ $crowdtask->template}}</strong> 
			               		</div>
			               		<div class="col-md-2" style="border-right: 1px solid #eee; text-align: center; vertical-align: middle;"> 
			               			on<br><h2>{{implode(", ", $crowdtask->platform)}}</h2>
			               		</div>
			               		<div class="col-md-2" style="border-right: 1px solid #eee; text-align: center; vertical-align: middle;"> 
			               			Flagged workers<h3>{{$crowdtask->flaggedWorkers}}</h3>
				               		<button class="btn btn-sm">Workers</button>
				               	</div>
							    <div class="col-md-2" style="border-right: 1px solid #eee; text-align: center; vertical-align: middle;">
							    	Payment/Unit:<strong> ${{ $crowdtask->reward }}</strong><br>
							       	<strong>Total costs:</strong><h2>$ {{$crowdtask->totalCost()}}</h2>
							    </div>
							    <div class="col-md-2" style="text-align: center; vertical-align: middle;">
							    	Completion:<br>
									<strong style="font-size:25px;">{{round(($crowdtask->completedJudgments() / $crowdtask->totalJudgments()) * 100, 2)}}%</strong><br>
			               			<strong>{{$crowdtask->completedJudgments()}} / {{$crowdtask->totalJudgments()}}</strong><br>Judgments
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
	</script>
@stop