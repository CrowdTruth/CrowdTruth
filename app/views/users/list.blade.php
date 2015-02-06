@extends('layouts.default_new')
@section('title','Userlist')
@section('content')

<div class="col-xs-12 col-md-10 col-md-offset-1">
	<div class='maincolumn CW_box_style'>

		<div class='tab'>
			<div class='title'>
				<h2>Userlist</h2>
			</div>
			@include('layouts.flashdata')
			<div class='row'>
				<div class="col-xs-12"  style="padding-bottom:40px; padding-top:20px">
					<table class="table table-striped" style='width:100%'>
						<tr>
							<th>Name</th>
							<th>Email</th>
							<th>Projects</th>
						</tr>
						
						<?php use \MongoDB\Security\Roles as Roles; ?>
						
						@foreach ($userlist as $user)
						<tr class='text-left' >
							<td>{{ link_to('user/' . $user['_id'], $user['firstname'] . ' ' . $user['lastname']) }} <small>{{ $user['_id'] }}</small></td>
							<td>{{ HTML::obfuscate($user['email']) }}</td>
							<td>
								<ul class="list-group">
								@foreach($usergroups[$user['_id']]['groups'] as $grInfo)
									<li class="list-group-item">
									@if($grInfo['canview'])
										{{ link_to(action('ProjectController@getGroupDetails', 
												[ $grInfo['name']  ]), $grInfo['name']) }}
									@else
										{{ $grInfo['name'] }}
									@endif
									
									@if($grInfo['assignrole'])
										<div class="btn-group">
											<button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
												{{ Roles::getRoleLabel($grInfo['role']) }}<span class="caret"></span>
											</button>
											<ul class="dropdown-menu" role="menu">
											@foreach(Roles::$GROUP_ROLE_NAMES as $role)
													<li>{{ link_to(action('ProjectController@groupActions', 
														[ $grInfo['name'], // Group name
														'action' => 'assignRole',
														'usedId' => $user['_id'],
														'role'   => $role ]),
													 Roles::getRoleLabel($role)) }}</li>
											@endforeach
											</ul>
											{{ link_to(action('ProjectController@groupActions', 
														[ $grInfo['name'], // Group name
														'action' => 'removeGroup',
														'usedId' => $user['_id'] ]),
													 '', [ 'class' => 'fa fa-close', 'style' => 'color:red' ]) }}
										</div>
									@else
										<span class="badge">{{ Roles::getRoleLabel($grInfo['role']) }}</span>
									@endif
									</li>
								@endforeach
								@if(count($usergroups[$user['_id']]['tojoin'])>0)
									<li class="list-group-item">
										<div class="btn-group">
											<button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
												Add user to project<span class="caret"></span>
											</button>
											<ul class="dropdown-menu" role="menu">
											@foreach($usergroups[$user['_id']]['tojoin'] as $grInfo)
												<li>{{ link_to(action('ProjectController@groupActions', 
														[ $grInfo, // Group name
														'action' => 'addGroup',
														'usedId' => $user['_id'] ]),
													$grInfo) }}
												</li>
											@endforeach
											</ul>
										</div>
									</li>
								@endif
								</ul>
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
