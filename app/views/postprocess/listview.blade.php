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
						               			<strong style="font-size: 18px;"></strong> Sentences<br><strong style="font-size: 18px;">{{ $crowdtask->maxAssignments }}</strong> 
						               			Judgments/Unit<br>Template: <strong style="font-size: 16px;">{{ $crowdtask->template}}</strong> </div>
						               		<div class="col-md-2" style="padding-top:5px; border-right: 1px solid #eee; border-top: 1px solid #eee;"> on<br><h2 style="text-align: center;">AMT</h2></div>
						               		<div class="col-md-2" style="padding-top:5px; border-right: 1px solid #eee; border-top: 1px solid #eee; text-align: center;" > 
						               			Max Judgm/Worker<h3 style="text-align: center;">10</h3><button class="btn btn-sm">Workers</button></div>
						               		<div class="col-md-2" style="padding-top:5px; border-top: 1px solid #eee; text-align: center;" >Payment/Unit:<strong> ${{ $crowdtask->reward }}</strong>
						               			<strong><br>Total costs:</strong><h2><div id="totalCost"></div></h2></div>
						               		<div class="col-md-2" style=" padding-top:5px; text-align: center; border-top: 1px solid #eee; border-left: 1px solid #eee;">Completion:<br>
						               			<strong style="font-size:25px;">55%</strong><br><strong>110/200</strong><br>Judgments</div>
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
		//todo change to cost per job
		$(document).ready(calculate());
	    function calculate(){
	    var reward = {{$crowdtask->reward}};
	    var maxAssignments = {{$crowdtask->maxAssignments}};
	    //var sentences = $
		var cost = reward*maxAssignments;
		var result = " $ " + cost.toFixed(2);
		document.getElementById('totalCost').innerHTML=result;
		} 
	</script>

@stop