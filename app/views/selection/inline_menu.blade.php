
						<div class='menu_selection'>
							@if (($items = Cart::content()) && count(Cart::content()) > 0)
								<table class='table table-striped'>
								<tbody>

								@foreach ($items as $item)
								<?php 
									$repository = new \mongo\Repository;
									$entity = $repository->find($item['id']); 
								?>
									<tr>
										<td>
											<div class='btn-group'>
												<a class='btn btn-default btn-sm col-xs-9' href='{{ URL::to('files/view?' . $entity['_id']) }}'>
													<i class='fa fa-file-text fa-fw'></i>
													<span>{{ $entity['title'] }}</span>
												</a>
												<a class='btn btn-default btn-sm col-xs-3 dropdown-toggle' data-toggle='dropdown' href='#'>
													<span class='fa fa-caret-down fa-fw'></span>
												</a>
												<ul class='dropdown-menu pull-right'>
													<li><a href='{{ URL::to('files/view?' . $entity['_id']) }}'><i class='fa fa-file-text-o fa-fw'></i>View</a></li>
													<li><a class='update_selection' href='{{ URL::to('selection/remove?selectionID=' . $item['rowid']) }}'><i class='fa fa-trash-o fa-fw'></i>Remove from selection</a></li>
												</ul>
												</div>
										</td>
									</tr>
								@endforeach
									<tr>
										<td>
											<a class='update_selection btn btn-danger col-xs-12' href='{{ URL::to('selection/destroy') }}'>Empty Selection</a>
										</td>
									</tr>
									</tbody>
								</table>
							@else
							<div class="panel panel-warning">
								<div class="panel-heading">
									<h4><i class="fa fa-exclamation fa-fw"></i>Notice</h4>
								</div>
								<div class="panel-body">
									No documents have been selected yet
								</div>
							</div>
							@endif
						</div>