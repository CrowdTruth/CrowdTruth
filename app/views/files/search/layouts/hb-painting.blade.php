<div class="tab-pane" id="painting_tab">
	<div class='row'>
		<div class='searchOptions col-xs-12'>
			<select name="search_limit" class="selectpicker pull-left">
				<option value="10">10 Records per page</option>
				<option value="25">25 Records per page</option>
				<option value="50">50 Records per page</option>
				<option value="100">100 Records per page</option>
			</select>
			<div class='visibleColumns pull-left'>
			</div>		
		</div>
	</div>
	<div class='row'>
		<div class='col-xs-12 cw_pagination'>
		</div>								
	</div>
    <table class="table table-striped">
        <thead data-query-key="field[documentType]" data-query-value="painting">
	        <tr>
	            <th class="sorting" data-query-key="orderBy[_id]">ID</th>
	            <th class="sorting" data-query-key="orderBy[title]">Title</th>
	            <th class="sorting" data-query-key="orderBy[source]">Source</th>
	        </tr>
			<tr class="inputFilters">
				<td>
					<input type='text' data-query-key="field[_id]" data-query-operator="[like]" />
				</td>
				<td>
					<input type='text' data-query-key="field[title]" data-query-operator="[like]" />
				</td>
				<td>
					<input type='text' data-query-key="field[source]" data-query-operator="[like]" />
				</td>
			</tr>											        
        </thead>
        <tbody class='results'>											
			<script class='template' type="text/x-handlebars-template">
		        @{{#each documents}}
					<tr>
						<td colspan="3">
						<div class="row">
							<div class="col-xs-4">
								<div class="thumbnail">
									<a href="@{{ this.content.URL }}">
								  		<img src="@{{ this.content.URL }}">
								  	</a>
								</div>
							</div>
							<div class="col-xs-8">
								@{{#if this.content.features.Object.matches }}
								    <table class="table table-striped table-condensed">
								    	<thead>
								    		<tr>
									    		<th>Tag</th>
									    		<th>Score</th>
								    		</tr>
								    	</thead>
								    	<tbody class='featureMatches'>
								    		@{{#each this.content.features.Object.matches }}
											<tr style="@{{gradualColors this.score ../../content.features.Object.matches.0.score }}">
												<td>
													@{{ this.tag }}
												</td>
												<td>
													@{{toFixed this.score 2}}													
												</td>
											</tr>
											@{{/each}}	
										</tbody>
									</table>
								@{{/if}}
							</div>
						</div>
					</td>
					</tr>
		        @{{/each}}
			</script>
        </tbody>
    </table>											
</div>