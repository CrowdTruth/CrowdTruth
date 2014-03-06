@extends('layouts.default')

@section('content')
			<div class="col-xs-10 col-md-offset-1">
				<div class='maincolumn CW_box_style'>
					<div class="tab">
						@include('postprocess.nav')
						<div class="container-fluid">
							<div  class="row-fluid" style="">
								<div  id="filtercolumn" class="span2" style="length: 200px;">
									<div>
										Filter #1
									</div>
									<div>
										Filter #2
									</div>
								</div>
								<div id="results" class="span10">
									<?php
									$id=0; 
									?>
									@foreach($crowdtasks as $crowdtask)
									<?php $id++ ?>
									<div class="panel panel-default" style="margin:15px;">
										<div class="row" style="padding: 0px;">
					            			<div class="col-md-2" style="border-right: 1px solid transparent;"><a class="">{{$crowdtask->id}}</a></div>
					              			<div class="col-md-10">Created on {{ date('D d M Y H:i ', strtotime($crowdtask->created_at)) }} by {{$crowdtask->creator}}</div>
					               		</div>
					               		<div class="row">
						               		<div class="col-md-10"><h4>{{ $crowdtask->name }}</h4><p>{{ $crowdtask->description }}</p></div>
						               		<div class="col-md-2 pull-right" style="text-align: center; border-left: 1px solid #eee;">Time elapsed:
						               			<p style="font-size: 20px;"><strong>{{ $crowdtask -> getElapsedTime($crowdtask->created_at) }}</strong></div>	
						               	</div>
						               	<div class="row">
						               		<div class="col-md-4" style="padding-top:5px; font-size:larger; vertical-align:baseline; border-right: 1px solid #eee; border-top: 1px solid #eee;">
						               			<strong style="font-size: 18px;">{{ $crowdtask->unitsPerTask }}</strong> Sentences<br>
						               			<strong style="font-size: 18px;">{{ $crowdtask->annotationsPerUnit }}</strong> Judgments/Unit<br>
						               			Template: <strong style="font-size: 16px;">{{ $crowdtask->template}}</strong> 
						               		</div>
						               		<div class="col-md-1" style="padding-top:5px; border-right: 1px solid #eee; border-top: 1px solid #eee;"> on<br><h2 style="text-align: center;">{{implode(",", $crowdtask->platform)}}</h2></div>
						               		<div class="col-md-2" style="padding-top:5px; border-right: 1px solid #eee; border-top: 1px solid #eee; text-align: center;" > 
						               			Flagged workers<h3 style="text-align: center;">{{$crowdtask->flaggedWorkers}}</h3>
						               			<button class="btn btn-sm">Workers</button></div>
						               		<div class="col-md-2" style="padding-top:5px; border-top: 1px solid #eee; text-align: center;">Payment/Unit:<strong> ${{ $crowdtask->reward }}</strong>
						               			<strong><br>Total costs:</strong><h2>$ {{$crowdtask->totalCost()}}</h2>
						               		</div>
						               		<div class="col-md-3" style=" padding-top:5px; text-align: center; border-top: 1px solid #eee; border-left: 1px solid #eee;">Completion:<br>
						               			<strong style="font-size:25px;">{{($crowdtask->completedJudgments() / $crowdtask->totalJudgments()) * 100}}%</strong><br>
						               			<strong>{{$crowdtask->completedJudgments()}} / {{$crowdtask->totalJudgments()}}</strong><br>Judgments
						               		</div>
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
               							<div class="panel-footer">
               								<div class="row">
               									<div style="padding: 3px; padding-left: 5px; float: left;" >
               										<button class="btn btn-primary" action="">Analyse</button>
               									</div>
									  			<div data-toggle="buttons" style="float:left; padding: 3px;">
										  			<label class="btn btn-primary">
											  			<!-- <input type="checkbox" id="detail" name="detail"> -->
														<input type="checkbox" name="Details" id="detail-<?php echo $id ?>" onChange="showDetails(<?php echo $id ?>)">Details</input>
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
									</div>								
									@endforeach
								</div>
							</div>
						</div>
					</div> 
				</div>
			</div>
@section('end_javascript')
	<script src="http://codeorigin.jquery.com/jquery-1.10.2.min.js"></script>
	<script>
		function showDetails(id){           
			$('#details-'+id).toggle(this.checked);
			}
	</script>

@stop