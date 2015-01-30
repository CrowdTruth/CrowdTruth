@extends('layouts.default_new')
@section('title','Userlist')
@section('content')

<div class="col-xs-12 col-md-10 col-md-offset-1">
	<div class='maincolumn CW_box_style'>

		<div class='tab'>
			<div class='title'>
				<h2>Group list</h2>
			</div>
			@include('layouts.flashdata')
			<div class='row'>
				<div class="col-xs-12"  style="padding-bottom:40px; padding-top:20px">
					<table class="table table-striped" style='width:100%'>
						<tr>
							<th>Group</th>
						</tr>
						
						@foreach($groupInfo as $grInfo)
						<tr class='text-left' >
							<td>
							@if($grInfo['canview'])
								{{ link_to('group/'.$grInfo['name'], $grInfo['name']) }}
							@else
								{{ $grInfo['name'] }}
							@endif
							</td>
						</tr>
						@endforeach
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@stop
