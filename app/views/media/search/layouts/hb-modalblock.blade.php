	<div class='hidden' id='modalBlock'>

		<script class='template' type="text/x-handlebars-template">
				<!-- Modal -->
				<div class="modal fade" id="activeTabModal">
				  <div class="modal-dialog">
				    <div class="modal-content">
				      <form id="messageform" class="ajaxform" name="input" action="/api/actions/block" method="post">
				      <div class="modal-header">
				        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				        <h4 class="modal-title">Block worker</h4>
				      </div>
				      <div class="modal-body">
				      	<p>
				      	Please write the reason for blocking worker <b><span rel="static-html"></span></b> below.
				      	</p>
				      	<input type="hidden" rel="static-val" name="workerid">
					<textarea class="form-control" name="blockmessage" placeholder="Message" rows="6" required></textarea>
						
						
				      </div>
				      <div class="modal-footer">
				        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				        <input type="submit" class="btn btn-primary" />
				      </div>
				      </form>
				    </div><!-- /.modal-content -->
				  </div><!-- /.modal-dialog -->
				</div><!-- /.modal -->
		</script>
	</div>		