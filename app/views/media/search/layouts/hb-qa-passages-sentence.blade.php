<div class="tab-pane" id="qa-passages-sentence_tab">	
	<div class='row'>
		<div class='tabOptions hidden'>
			<div class='btn-group' style="margin-left:5px;">
				<button type="button" class="btn btn-default openAllColumns">Open all columns</button>
				<button type="button" class="btn btn-default openDefaultColumns hidden">Open default columns</button>
				<div class="btn-group vbColumns">
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
					<span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu">
						<li><a href="#" data-vb="hide" data-vbSelector="content_question_id"></i>Question ID</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="content_question_text"></i>Question</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="content_passage_id"></i>Passage ID</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="content_passage_text"></i>Passage</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="created_at"></i>Created At</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="created_by"></i>Created By</a></li>
					</ul>
				</div>
			</div>
		</div>	
	</div>
	<div class='ctable-responsive cResults'>
	    <table class="table table-striped qwe">
	       	<thead data-query-key="match[documentType]" data-query-value="qa-passages-sentence">
		        <tr>
		            <th data-vbIdentifier="checkbox">Select</th>
			    	<th class="sorting" data-vbIdentifier="content_question_id" data-query-key="orderBy[content.question.id]">Question ID</th>
			    	<th class="sorting" data-vbIdentifier="content_question_text" data-query-key="orderBy[content.question.text]" style='min-width:400px; width:auto;'>Question</th>
			    	<th class="sorting" data-vbIdentifier="content_passage_id" data-query-key="orderBy[content.passage.id]">Passage ID</th>
					<th class="sorting" data-vbIdentifier="content_passage_text" data-query-key="orderBy[content.passage.text]">Passage</th>  
				    <th class="sorting" data-vbIdentifier="created_at" data-query-key="orderBy[created_at]" style="min-width:220px; width:auto;">Created At</th>
				    <th class="sorting" data-vbIdentifier="created_by" data-query-key="orderBy[user_id]">Created By</th>
		        </tr>
				<tr class="inputFilters">
					<td data-vbIdentifier="checkbox">
						<input type="checkbox" class="checkAll" />
					</td>
					<td data-vbIdentifier="content_question_id">
						<input class="input-sm form-control" type='text' data-query-key="match[content.question.id]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="content_question_text">
						<input class="input-sm form-control" type='text' data-query-key="match[content.question.text]" data-query-operator="like" placeholder="Enter your search keywords here" />
					</td>
					<td data-vbIdentifier="content_passage_id">
						<input class="input-sm form-control" type='text' data-query-key="match[content.passage.id]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="content_passage_text">
						<input class="input-sm form-control" type='text' data-query-key="match[content.passage.text]" data-query-operator="like" placeholder="Enter your search keywords here" />
					</td>
					<td data-vbIdentifier="created_at">
						<div class="input-daterange">
						    <input type="text" class="input-sm form-control" name="start" data-query-key="match[created_at]" data-query-operator=">=" style="width:49% !important; float:left;" placeholder="Start Date" />
						    <input type="text" class="input-sm form-control" name="end" data-query-key="match[created_at]" data-query-operator="=<" style="width:49% !important; float:right;" placeholder="End Date" />
						</div>
					</td>
					<td data-vbIdentifier="created_by">
						<input class="input-sm form-control" type='text' data-query-key="match[user_id]" data-query-operator="like" />	
					</td>
				</tr>											        
	        </thead>
	        <tbody class='results'>								
				<script class='template' type="text/x-handlebars-template">
			        @{{#each documents}}
			        <tr class="text-center">
			            <td data-vbIdentifier="checkbox"><input type="checkbox" id="@{{ this._id }}" name="rowchk" value="@{{ this._id }}"></td>
				    	<td data-vbIdentifier="content_question_id">@{{ this.content.question.id }}</td>
				    	<td data-vbIdentifier="content_question_text">@{{ this.content.question.text }}</td>
			            <td data-vbIdentifier="content_passage_id">@{{ this.content.passage.id }}</td>
				    	<td data-vbIdentifier="content_passage_text">@{{ this.content.passage.text }}</td>
					    <td data-vbIdentifier="created_at">@{{ this.created_at }}</td>
					    <td data-vbIdentifier="created_by">@{{ this.user_id }}</td>
					</tr>
			        @{{/each}}
				</script>
	        </tbody>
	    </table>
	</div>			
</div>