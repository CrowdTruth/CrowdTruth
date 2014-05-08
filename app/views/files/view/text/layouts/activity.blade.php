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
												<td>Time</td>
												<td>{{ $activity->created_at }}</td>
											</tr>
											<tr>
												<td>User agent</td>
												<td>{{ link_to('#', $activity->wasAssociatedWithUserAgent->firstname . ' '. $activity->wasAssociatedWithUserAgent->lastname) }}</td>
											</tr>
											<tr>
												<td>Software agent</td>
												<td>{{ link_to_route($activity->wasAssociatedWithSoftwareAgent->_id, $activity->wasAssociatedWithSoftwareAgent->_id) }}</td>
											</tr>
											<tr>
												<td>Software agent label</td>
												<td>{{ $activity->wasAssociatedWithSoftwareAgent->label }}</td>
											</tr>											
										</tbody>
									</table>
								</div>
