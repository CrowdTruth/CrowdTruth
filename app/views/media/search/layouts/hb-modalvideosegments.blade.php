	<div class='hidden' id='modalVideoSegments'>
		<script class='template' type="text/x-handlebars-template">
			<!-- Modal -->
			<div class="modal fade" id="activeTabModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabelWorker" aria-hidden="true">
				<div class="modal-dialog ">
				    <div class="modal-content">
				      <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabelWorker">Segments of the video</h4>
				      </div>
				      <div class="modal-body" >
					<table>
					@{{#each documents}}
						<tr>
							<td>
								<video width="240" height="160" controls preload="none">
									<source src="@{{ this.content.storage_url }}" type="video/mp4" >
									<source src="@{{ this.content.storage_url }}" type="video/ogg" >
										Your browser does not support the video tag.
									</source>
								</video>
							</td>
							<td align="left">
								<strong>Duration</strong>: @{{ this.content.duration }} <br />
								<strong>Start Time</strong>: @{{ this.content.start_time }} <br />
								<strong>End Time</strong>: @{{ this.content.end_time }} <br />
							</td>
						</tr>
					@{{/each}}
					</table>					
				      </div>
				    </div>
				  </div>
				</div>
		</script>
		
	</div>											