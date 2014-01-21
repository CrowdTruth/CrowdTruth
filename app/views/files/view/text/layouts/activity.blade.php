								<div class='table-responsive'>
									<table class='table table-striped'>
										<tbody>
											<tr>
												<td>URI</td>
												<td>
													<a href='#'>{{ $activity->_id }}</a>
												</td>
											</tr>
											@if($activity->used)
											<tr>
												<td>Used</td>
												<td>{{ link_to(URL::to('files/view?URI=' . $activity->used->_id), $activity->used->_id) }}</td>

											</tr>
											@endif										
											<tr>
												<td>Type</td>
												<td>{{ $activity->type }}</td>
											</tr>											
											<tr>
												<td>Label</td>
												<td>{{ $activity->label }}</td>
											</tr>
											<tr>
												<td>Time</td>
												<td>{{ $activity->created_at }}</td>
											</tr>
											<tr>
												<td>User agent</td>
												<td>{{ link_to('#', $activity->wasAssociatedWith->firstname . ' '. $activity->wasAssociatedWith->lastname) }}</td>
											</tr>
											<tr>
												<td>Software agent</td>
												<td>{{ link_to($activity->software_id,  $activity->software_id) }}</td>
											</tr>
										</tbody>
									</table>
								</div>
