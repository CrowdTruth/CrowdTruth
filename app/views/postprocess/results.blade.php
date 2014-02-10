
<div id="results" class="col-md-10">
				<?php
					$id=0; 
				?>
				@foreach($jobConfigurations as $jobConfiguration)
				<?php $id++ ?>
					<div class="panel panel-default">
						<!-- Top row is panel heading with creation date and creator -->
						<div class="panel-heading clearfix">
							<div style="width: 25%; float:left;">
								<a class="">{{$jobConfiguration->id}}</a>
							</div>
	              			<div style="float:left;">
	              				Created on {{ date('D d M Y H:i ', strtotime($jobConfiguration->created_at)) }} by {{$jobConfiguration->creator}}
		              		</div>
			           		<div class="pull-right" style="width: 33%;">
			           			<div class="progress" style="margin-bottom: 0px;">	
			           				<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="{{$jobConfiguration->progressBar()}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$jobConfiguration->progressBar()}}%;">
		   								<span class="sr-only">{{$jobConfiguration->progressBar()}}% Complete</span>
		  							</div>
		              			</div>
		              		</div>
	               		</div>
	               		<!-- First content row with title and description of the ct and the elapsed time -->
	               		<div class="panel-body" style="padding-top: 0px; padding-bottom: 0px;">
		               		<div class="row" style="border-bottom: 1px solid #eee;">
			               		<div class="col-md-10" style="border-right: 1px solid #eee;">
			               			<h4>{{ $jobConfiguration->name }}</h4>
			               			<p>{{ $jobConfiguration->description }}</p>
			               			<strong style="font-size: 18px;"><i class="fa fa-file"></i> {{ $jobConfiguration->template}}</strong> 
			               		</div>
			               		<div class="col-md-2" style="text-align: center; padding-top: 15px;">
			               			<strong style="font-size: 24px; "><i class="fa fa-clock-o fa-2x"></i><br> {{ $jobConfiguration -> getElapsedTime($jobConfiguration->created_at) }}</strong></p>
			               		</div>	
			               	</div>
			               	<div class="row" style="height: 90px;">
			               		<!-- This row has the following content: #sentences, judgments/unit and template info; block with worker info; block of costs, block of completion percentage; -->
			               		<div class="col-md-2" style="border-right: 1px solid #eee; height: 100%; text-align: center; display: table-cell; vertical-align: middle; padding-top: 10px;">
			               			<strong style="font-size: 24px;"><i class="fa fa-bars"></i> {{ $jobConfiguration->unitsPerTask }}</strong><br>
			               			<strong style="font-size: 24px;"><i class="fa fa-gavel"></i> {{ $jobConfiguration->judgmentsPerUnit }}</strong><br>
			               		</div>
			               		<div class="col-md-4" style="border-right: 1px solid #eee; height: 100%; text-align: center; display: table-cell; padding-top: 10px; vertical-align: middle;"> 
			               			<h2><i class="fa fa-users"></i> {{implode(", ", $jobConfiguration->platform)}}</h2>
			               		</div>
			               		<div class="col-md-2" style="border-right: 1px solid #eee; text-align: center; display: table-cell; padding-top: 5px; font-size: 32px; vertical-align: middle;"> 
			                   		<i class="fa fa-flag"></i>  {{$jobConfiguration->flaggedWorkers}} %</strong><br>
			                   		<button class="btn btn-sm">Workers</button>
				               	</div>
							    <div class="col-md-2" style="border-right: 1px solid #eee; height: 100%; text-align: center; display: table-cell; padding-top: 10px; vertical-align: middle;">
							    	<i class="fa fa-dollar"></i><strong> /</strong> <i class="fa fa-gavel"></i> <strong> ${{ $jobConfiguration->reward }}</strong>
							       	<h2><i class="fa fa-dollar"></i> {{number_format((float)$jobConfiguration->totalCost(), 2, '.', '')}}</h2>
							    </div>
							    <div class="col-md-2" style="text-align: center; height: 100%; display: table-cell; vertical-align: middle; padding-top: 10px;">
							    	<strong> <i class="fa fa-gavel"></i> {{$jobConfiguration->completedJudgments()}} / {{$jobConfiguration->totalJudgments()}}</strong>
							    	<h2><i class="fa fa-check-circle"></i> {{round( ( $jobConfiguration->completedJudgments() / $jobConfiguration->totalJudgments() ) * 100, 1)}}%</h2>
			               		</div>
							</div>
							 <!-- Here starts the hidden details field, see js at bottom of page -->
							<div id="details-<?php echo $id ?>" class="row" style="display: none;">
					            <table class="table table-striped">
					           	@foreach($jobConfiguration->getDetails() as $key=>$val)
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
				{{$jobConfigurations->links();}}
			<!-- Close results column -->
			</div>