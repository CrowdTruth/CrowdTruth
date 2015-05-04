@extends('layouts.default_new')
@section('title','Userlist')
@section('content')

<div class="col-xs-12 col-md-10 col-md-offset-1">
	<div class='maincolumn CW_box_style'>

		<div class='tab'>
			<div class='title'>
				<h2>Projects</h2>
			</div>
			@include('layouts.flashdata')
			<div class='row'>
				<div class="col-xs-12"  style="padding-bottom:40px; padding-top:20px">
					<table class="table table-striped" style='width:100%'>
						<tr>
							<th>Project</th>
							<th class='text-center'>Members</th>
						</tr>
						
						@foreach($projects as $project)
						<tr>
							<td class='text-left'>
							@if($project['canview'])
								{{ link_to(action('ProjectController@getProfile', 
												[ $project['name']  ]), $project['name']) }}
							@else
								{{ $project['name'] }}
							@endif
							</td>
							<td class='text-center'>
							{{ $project['users'] }}
							</td>
						</tr>
						@endforeach
						
						@if($isAdmin)
						<tr class='text-left' >
							<td colspan='2'>
								{{ Form::open([ 'action' => 'ProjectController@createGroup', 'class' => 'form-horizontal jobconf' ] ) }}
								<div class="form-group">
									<div class='col-xs-4'>
										{{ Form::text('addGrp', '', [ 'class' => 'form-control', 'placeholder' => 'Project Name' ] ) }}
									</div>
									<div class='col-xs-2'>
										{{ Form::submit('Create Project', [ 'class' => 'btn btn-primary pull-right' ]); }}
									</div>
								</div>
								{{ Form::close() }}
							</td>
						</tr>
						@endif
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@stop
