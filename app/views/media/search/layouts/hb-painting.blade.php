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
        <tbody class='results lukasz'>											
			<script class='template' type="text/x-handlebars-template">
		        @{{#each documents}}
					<tr>
						<td colspan="3">
						<div class="row">
							<div class="col-xs-4">
								<div class="thumbnail">
									<a href="@{{ this.content.URL }}" class="hb_popover" title="@{{ this.title }}">
										<div class='hidden'>
											@{{ this.domain }} <br /> @{{ this.documentType }}
										</div>
								  		<img src="@{{ this.content.URL }}">
								  	</a>
								</div>
							</div>
							<div class="col-xs-8">
								@{{#if this.content.features.Object.matches }}
									<h5 style='font-weight:bold'> Objects </h5>
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

								@{{#if this.content.features.Scene }}
									<h5 style='font-weight:bold'> Scene </h5>
								    <table class="table table-striped table-condensed">
								    	<thead>
								    		<tr>
									    		<th>Label</th>
									    		<th>Score</th>
								    		</tr>
								    	</thead>
								    	<tbody class='featureMatches'>
								    		@{{#each this.content.features.Scene }}
											<tr style="@{{gradualColors this.score ../../content.features.Scene.0.score }}">
												<td>
													@{{ this.label }}
												</td>
												<td>
													@{{toFixed this.score 2}}													
												</td>
											</tr>
											@{{/each}}	
										</tbody>
									</table>
								@{{/if}}

								@{{#if this.content.features.FacesNumber }}
									<h5 style='font-weight:bold'> Faces </h5>
								    <table class="table table-striped table-condensed">
								    	<thead>
								    		<tr>
								    			@{{#each this.content.features.FacesNumber }}
								    				<th> @{{ @key }} </th>
								    			@{{/each}}
								    		</tr>
								    	</thead>								    
								    	<tbody class=''>
											<tr>
								    		@{{#each this.content.features.FacesNumber }}
												<td>
													@{{ this }}											
												</td>
											@{{/each}}
											</tr>
										</tbody>
									</table>
								@{{/if}}

								@{{#if this.content.features.Classifier }}
									<h5 style='font-weight:bold'> Classifier - Score </h5>
								    <table class="table table-striped table-condensed">
								    	<tbody class=''>
								    		@{{#each this.content.features.Classifier }}
											<tr>
												<td>
													@{{ @key }}
												</td>
												<td>
													@{{divide this 100}}											
												</td>
											</tr>
											@{{/each}}	
										</tbody>
									</table>
								@{{/if}}

								@{{#if this.content.features.ColorsHistogram }}
								    <table class="table table-striped table-condensed">						    
								    	<tbody class=''>
											<tr>
								    		@{{#each this.content.features.ColorsHistogram }}
												<td style='background:@{{ first this }}; width:@{{ last this }}%;'>
													&nbsp;
												</td>
											@{{/each}}									
											</tr>
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