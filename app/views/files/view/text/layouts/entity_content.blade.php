								<?php $entitySeparatedByNewline = explode("\n", $entityContent); ?>
								<div class='table-responsive'>
									<table class='table table-striped tableContent'>
										<thead>
											<th>Line Number</th>
											<th>Content</th>
										</thead>
										<tbody class='content'>
											@foreach ($entitySeparatedByNewline as $linenumber => $lineval)
											<tr>
												<td><strong> {{ $linenumber }} </strong></td>
												<td>
													 {{ $lineval }}
												</td>
											</tr>
											@endforeach
										</tbody>
									</table>							
								</div>