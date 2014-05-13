<div class='hidden' id='modalMessage'>

		<script class='template' type="text/x-handlebars-template">
				<!-- Modal -->
				<div class="modal fade" id="activeTabModal">
				  <div class="modal-dialog">
				    <div class="modal-content">
				      <form id="messageform" class="ajaxform" name="input" action="/api/actions/message" method="post">
				      <div class="modal-header">
				        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				        <h4 class="modal-title">Send message</h4>
				      </div>
				      <div class="modal-body">
				      	
						<input class="form-control" type="text" rel="static-val" name="messageto" id="messageto" placeholder="To (comma separated)" required /><br>
						<input class="form-control" type="text" name="messagesubject" id="messagesubject" placeholder="Subject" required /><br>
						<textarea class="form-control" name="messagecontent" placeholder="Message" rows="6" required></textarea>
						
						
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