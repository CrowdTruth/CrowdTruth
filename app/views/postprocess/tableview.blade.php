@extends('layouts.default')

@section('content')

			<div class="col-xs-10 col-md-offset-1">
				<div class='maincolumn CW_box_style'>

					<div class="tab">
						@include('postprocess.nav')
						<strong>Table view for crowdtasks: </strong>
						@foreach($crowdtasks as $crowdtask)
	              			<table>
	              				<tr>
	              					<td>{{$crowdtask->title}}</td><td> {{$crowdtask->template}}</td>
	              				</tr>
	              			</table>


						@endforeach
						
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



					</div> 
				</div>
			</div>
@stop