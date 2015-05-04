	<div class='hidden' id='modalVideoKeyframes'>

		<script class='template' type="text/x-handlebars-template">
				<!-- Modal -->
				<div class="modal fade" id="activeTabModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabelWorker" aria-hidden="true">
				  <div class="modal-dialog ">
				    <div class="modal-content">
				      <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabelWorker">Keyframes of the video</h4>
				      </div>
				      <div class="modal-body" >
					<table>
					@{{#each documents}}
						<tr>
							<td><img src="@{{ this.content.storage_url }}" width="240" height="160" rel="tooltip" title="Timestamp: @{{ this.content.timestamp }} &#xA; Source name: @{{ this.content.storage_url }} "/></td>
							<td><strong>Timestamp</strong>: @{{ this.content.timestamp }}</td>
						</tr>
					@{{/each}}
					</table>					
				      </div>
				    </div>
				  </div>
				</div>
		</script>

	</div>	