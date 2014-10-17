<div class="tab-pane" id="metadatadescription_tab">	
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
						<li><a href="#" data-vb="show" data-vbSelector="description_identifier"></i>Identifier</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="video_description">Video Description</th>
						<li><a href="#" data-vb="show" data-vbSelector="description_topics"></i>Topics</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="video_title"></i>Title</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="description_language"></i>Language</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="avg_clarity"></i>Avg Clarity</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="words_count"></i>Word Count</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="no_of_machine_features">Total # Machine Features</th>
						<li><a href="#" data-vb="show" data-vbSelector="no_of_machine_events"># Machine Events</th>
						<li><a href="#" data-vb="show" data-vbSelector="machine_events">Machine Events</th>
						<li><a href="#" data-vb="show" data-vbSelector="no_of_people"></i># of People Features</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="people"></i>People Features</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="no_of_location"></i># of Location Features</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="location"></i>Location Features</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="no_of_time"></i># of Time Features</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="time"></i>Time Features</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="number_of_batches"></i>Used In # of Batches</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="number_of_jobs"></i>Used In # of Jobs</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="created_at"></i>Created At</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="created_by"></i>Created By</a></li>
					</ul>
				</div>
			</div>
		</div>	
	</div>
	<div class='ctable-responsive cResults'>
	    <table class="table table-striped qwe">
	       	<thead data-query-key="match[documentType]" data-query-value="metadatadescription">
		        <tr>
		            <th data-vbIdentifier="checkbox">Select</th>
			    	<th class="sorting" data-vbIdentifier="description_identifier" data-query-key="orderBy[title]">Identifier</th>
			    	<th class="sorting" data-vbIdentifier="video_description" data-query-key="orderBy[content.description]">Video Description </th>
			    	<th class="sorting" data-vbIdentifier="description_topics" data-query-key="orderBy[content.features.topics]">Topics</th>
			    	<th class="sorting" data-vbIdentifier="video_title" data-query-key="orderBy[videoTitle]">Title</th>
		            <th class="sorting" data-vbIdentifier="description_language" data-query-key="orderBy[language]">Language</th>			 
			    	<th class="sorting" data-vbIdentifier="avg_clarity" data-query-key="orderBy[avg_clarity]">Avg Clarity</th>
			    	<th class="sorting whiteSpaceNormal" data-vbIdentifier="words_count" data-query-key="orderBy[wordCount]">Word Count</th>
			    	<th class="sorting whiteSpaceNormal" data-vbIdentifier="no_of_machine_features" data-query-key="orderBy[totalNoOfFeatures]">Total # Machine Features </th>
			    	<th class="sorting whiteSpaceNormal" data-vbIdentifier="no_of_machine_events" data-query-key="orderBy[content.automatedEventsCount]"># Machine Events </th>
			    	<th class="sorting" data-vbIdentifier="machine_events" data-query-key="orderBy[content.features.automatedEvents.label]">Machine Events</th>
			    	<th class="sorting whiteSpaceNormal" data-vbIdentifier="no_of_people" data-query-key="orderBy[content.peopleCount]"># of People Features</th>
		            <th class="sorting" data-vbIdentifier="people" data-query-key="orderBy[content.features.people.label]">People Features</th>			 
			    	<th class="sorting whiteSpaceNormal" data-vbIdentifier="no_of_location" data-query-key="orderBy[content.locationCount]"># of Location Features</th>
		            <th class="sorting" data-vbIdentifier="location" data-query-key="orderBy[content.features.location.label]">Location Features</th>	
		            <th class="sorting whiteSpaceNormal" data-vbIdentifier="no_of_time" data-query-key="orderBy[content.timeCount]"># of Time Features</th>
		            <th class="sorting" data-vbIdentifier="time" data-query-key="orderBy[content.features.time.label]">Time Features</th>	
					<th class="sorting whiteSpaceNormal" data-vbIdentifier="number_of_batches" data-query-key="orderBy[cache.batches.count]">Used In # of Batches</th>
				    <th class="sorting whiteSpaceNormal" data-vbIdentifier="number_of_jobs" data-query-key="orderBy[cache.jobs.count]">Used In # of Jobs</th>     
				    <th class="sorting whiteSpaceNoWrap" data-vbIdentifier="created_at" data-query-key="orderBy[created_at]" style="min-width:220px; width:auto;">Created At</th>
				    <th class="sorting" data-vbIdentifier="created_by" data-query-key="orderBy[user_id]">Created By</th>
		        </tr>
				<tr class="inputFilters">
					<td data-vbIdentifier="checkbox">
						<input type="checkbox" class="checkAll" />
					</td>
					<td data-vbIdentifier="description_identifier">
						<input class="input-sm form-control" type='text' data-query-key="match[title]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="video_description">
						<input class="input-sm form-control" type='text' data-query-key="match[content.description]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="description_topics">
						<input class="input-sm form-control" type='text' data-query-key="match[content.features.topics]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="video_title">
						<input class="input-sm form-control" type='text' data-query-key="match[videoTitle]" data-query-operator="like" />	
					</td>
					<td data-vbIdentifier="description_language">
						<input class="input-sm form-control" type='text' data-query-key="match[language]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="avg_clarity">
						<input class="input-sm form-control" type='text' data-query-key="match[avg_clarity]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="words_count">
						<input class="input-sm form-control" type='text' data-query-key="match[wordCount]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
						<input class="input-sm form-control" type='text' data-query-key="match[wordCount]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
					</td>
					<td data-vbIdentifier="no_of_machine_features">
						<input class="input-sm form-control" type='text' data-query-key="match[totalNoOfFeatures]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
						<input class="input-sm form-control" type='text' data-query-key="match[totalNoOfFeatures]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
					</td>
					<td data-vbIdentifier="no_of_machine_events">
						<input class="input-sm form-control" type='text' data-query-key="match[content.automatedEventsCount]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
						<input class="input-sm form-control" type='text' data-query-key="match[content.automatedEventsCount]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
					</td>
					<td data-vbIdentifier="machine_events">
						<input class="input-sm form-control" type='text' data-query-key="match[content.features.automatedEvents.label]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="no_of_people">
						<input class="input-sm form-control" type='text' data-query-key="match[content.peopleCount]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
						<input class="input-sm form-control" type='text' data-query-key="match[content.peopleCount]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
					</td>
					<td data-vbIdentifier="people">
						<input class="input-sm form-control" type='text' data-query-key="match[content.features.people.label]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="no_of_location">
						<input class="input-sm form-control" type='text' data-query-key="match[content.features.location.label]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
						<input class="input-sm form-control" type='text' data-query-key="match[content.features.location.label]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
					</td>
					<td data-vbIdentifier="location">
						<input class="input-sm form-control" type='text' data-query-key="match[content.locationCount]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="no_of_time">
						<input class="input-sm form-control" type='text' data-query-key="match[content.timeCount]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
						<input class="input-sm form-control" type='text' data-query-key="match[content.timeCount]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
					</td>
					<td data-vbIdentifier="time">
						<input class="input-sm form-control" type='text' data-query-key="match[content.features.time.label]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="number_of_batches">
						<input class="input-sm form-control" type='text' data-query-key="match[cache.batches.count]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
						<input class="input-sm form-control" type='text' data-query-key="match[cache.batches.count]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
					</td>
					<td data-vbIdentifier="number_of_jobs">
						<input class="input-sm form-control" type='text' data-query-key="match[cache.jobs.count]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
						<input class="input-sm form-control" type='text' data-query-key="match[cache.jobs.count]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
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
				    	<td data-vbIdentifier="description_identifier"> 
							<a class='testModal' id='@{{ this._id }}' data-modal-query="unit=@{{this._id}}" data-api-target="{{ URL::to('api/analytics/unit?') }}" data-target="#modalIndividualMetadataDescriptionUnit" data-toggle="tooltip" data-placement="top" title="Click to see the individual unit page">
								@{{ this.title }}
							</a>
				    	</td>
				    	<td data-vbIdentifier="video_description">
				    		@{{ this.content.description}}	
			            </td>
				    	<td data-vbIdentifier="description_topics">
				    		@{{#if  this.content.features.topics }}
				            	@{{#eachProperty this.content.features.topics }}
				            		<b>@{{value.label}} - score: @{{value.score}} </b> (@{{value.wikiLink}}) <br>
				            	@{{/eachProperty}}   
			            	@{{/if}}
			            </td>
			            <td data-vbIdentifier="video_title">@{{ this.videoTitle }}</td>
						<td data-vbIdentifier="description_language"> @{{ this.language }} </td>
						<td data-vbIdentifier="avg_clarity"> @{{ this.avg_clarity }} </td>
						@{{#if  this.wordCount }}
							<td data-vbIdentifier="words_count"> @{{ this.wordCount }} </td>
						@{{else}}
							<td data-vbIdentifier="words_count"> 0 </td>
						@{{/if}}	
						@{{#if  this.totalNoOfFeatures }}	
							<td data-vbIdentifier="no_of_machine_features"> @{{ this.totalNoOfFeatures }} </td>
						@{{else}}	
							<td data-vbIdentifier="no_of_machine_features"> 0 </td>
						@{{/if}}
						@{{#if  this.content.automatedEventsCount }}
							<td data-vbIdentifier="no_of_machine_events"> @{{ this.content.automatedEventsCount }} </td>
						@{{else}}
							<td data-vbIdentifier="no_of_machine_events"> 0 </td>
						@{{/if}}
						@{{#if  this.content.features.automatedEvents }}
							<td data-vbIdentifier="machine_events"> @{{ highlightautomatedEvents @root.searchQuery this.content }} </td>
				    	@{{ else }}
			           		<td data-vbIdentifier="machine_events"> @{{ this.content.description }} </td>
			            @{{/if}}
			            @{{#if  this.content.peopleCount }}	
							<td data-vbIdentifier="no_of_people"> @{{ this.content.peopleCount }} </td>
						@{{else}}
							<td data-vbIdentifier="no_of_people"> 0 </td>
						@{{/if}}
						@{{#if  this.content.features.people }}
							<td data-vbIdentifier="people"> @{{ highlightPeople @root.searchQuery this.content }} </td>
			           	@{{ else }}
			           		<td data-vbIdentifier="people"> @{{ this.content.description }} </td>	
			            @{{/if}}
			            @{{#if  this.content.locationCount }}	
							<td data-vbIdentifier="no_of_location"> @{{ this.content.locationCount }} </td>
						@{{else}}
							<td data-vbIdentifier="no_of_location"> 0 </td>
						@{{/if}}
						@{{#if  this.content.features.location }} 
							<td data-vbIdentifier="location"> @{{ highlightLocation @root.searchQuery this.content }} </td>
			           	@{{ else }}
			           		<td data-vbIdentifier="location"> @{{ this.content.description }} </td>	
			            @{{/if}}
			            @{{#if  this.content.timeCount }}	
							<td data-vbIdentifier="no_of_time"> @{{ this.content.timeCount }} </td>
						@{{else}}
							<td data-vbIdentifier="no_of_time"> 0 </td>
						@{{/if}}
						@{{#if  this.content.features.time }}
							<td data-vbIdentifier="time"> @{{ highlightTime @root.searchQuery this.content }} </td>
			           	@{{ else }}
			           		<td data-vbIdentifier="time"> @{{ this.content.description }} </td>	
			            @{{/if}}	
				    	<td data-vbIdentifier="number_of_batches">@{{ this.cache.batches.count }}</td>
					    <td data-vbIdentifier="number_of_jobs">@{{ this.cache.jobs.count }}</td>
					    <td data-vbIdentifier="created_at">@{{ this.created_at }}</td>
					    <td data-vbIdentifier="created_by">@{{ this.user_id }}</td>
				</tr>
			        @{{/each}}
				</script>
	        </tbody>
	    </table>
	</div>

<div class='hidden' id='modalIndividualMetadataDescriptionUnit'>
	<script class='template' type="text/x-handlebars-template">
		<!-- Modal -->
		<div class="modal fade bs-example-modal-lg" id="activeTabModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabelMetadataDescriptionUnit" aria-hidden="true">
			<div class="modal-dialog modal-lg">
		  		<div class="modal-content">
		   			<div class="modal-header">
		    			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		     			<h4 class="modal-title" id="myModalLabelMetadataDescriptionUnit">Individual @{{#addDocumentTypeLabel this.infoStat.documentType}} @{{/addDocumentTypeLabel}} Page</h4>
		   			</div>
		   			<div class="modal-body report-modal-body report-pre">
						<div>
							<video width="240" height="160" controls preload="none" data-toggle="tooltip" data-placement="top" title="Click to play">
								<source src="@{{ this.infoStat.videoContent }}" type="video/mp4" >
								<source src="@{{ this.infoStat.videoContent }}" type="video/ogg" >
								Your browser does not support the video tag.
								</source>
							</video>
							<div style="float:right;">
								<strong> Video Title: </strong> @{{ this.infoStat.videoTitle }} <br>
							    <strong> Video Metadata Description: </strong> @{{ this.infoStat.content.description }} 
							</div>
						</div>
		         		<div class="panel-group" id="accordion">
			  				<div class="panel panel-default">
			    				<div class="panel-heading">
			      					<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
										<h4 class="panel-title">
				 							@{{#addDocumentTypeLabel this.infoStat.documentType}} @{{/addDocumentTypeLabel}} Information 
										</h4>
			      					</a>
			    				</div>
			    				<div id="collapseOne" class="panel-collapse collapse in">
			      					<div class="panel-body">
										<div><strong> Type: </strong> @{{ this.infoStat.documentType }} </div>
									   	<div><strong> Format: </strong> @{{ this.infoStat.format }} </div>
									   	<div><strong> Domain: </strong> @{{ this.infoStat.domain }} </div>
									   	<div><strong> Source: </strong> @{{ this.infoStat.source }} </div>
									   	<div><strong> Language: </strong> @{{ this.infoStat.language }} </div>
			      					</div>
			    				</div>
			  				</div>	
			  				<div class="panel panel-default">
			    				<div class="panel-heading">
			      					<a data-toggle="collapse" data-parent="#accordion" href="#collapseFive">
										<h4 class="panel-title">
				 							@{{#addDocumentTypeLabel this.infoStat.documentType}} @{{/addDocumentTypeLabel}} Majority Voting Results 
										</h4>
			      					</a>
			    				</div>
			    				<div id="collapseFive" class="panel-collapse collapse">
			      					<div class="panel-body report-pre report-modal-body">
										<table class="tablesorter table table-striped table-condensed" border="1" bordercolor="#C0C0C0" text-align="center"> 
											<thead> 
												<tr> 
												  	<th class="header" rowspan="2">Label</th>
												  	<th class="header" rowspan="2">Start Offset</th>
						   						  	<th class="header" rowspan="2">End Offset</th>
						   						  	<th class="header" rowspan="2">Confidence</th>
						   						  	<th class="header" colspan="3">Clarity</th>
						   						   	<th class="header" colspan="2">Extractors/Label</th>
						   						  	<th class="header" rowspan="2">Extractors/Type</th>
						   						  	<th class="header" rowspan="2">Extractors/Resource</th>
						   						  	<th class="header" rowspan="2">Extractors/LabelTypePair</th>
						   						  	<th class="header" rowspan="2">Extractors/LabelResourcePair</th>
						   						  	<th class="header" rowspan="2">Extractors/TypeResourcePair</th>
						   						  	<th class="header" rowspan="2">Extractors/LabelTypeResource</th>
												</tr>
												<tr>
													<th class="header">Mean</th>
						   						  	<th class="header">Stddev</th>
												  	<th class="header">MSE</th>
						   						  	<th class="header">Extractors</th>
						   						  	<th class="header">Relevance</th>
						   						</tr>
											</thead>
											<tbody>
												@{{#each this.infoStat.content.statistics.majvoting}}
						 						<tr>
													<td> @{{ label }} </td>
													<td> @{{ startOffset }} </td>
													<td> @{{ endOffset }} </td>
													<td> @{{ toFixed confidence.value 3}} </td>
													<td> @{{ toFixed clarity.mean 3}} </td>
													<td> @{{ toFixed clarity.stddev 3}} </td>
													<td> @{{ toFixed clarity.mse 3}} </td>
													<td> @{{ noExtractorsPerLabel.count }} </td>
													<td> @{{ toFixed noExtractorsPerLabel.relevanceScore.value 3}} </td>
													<td> 
														<table border="1" bordercolor="#C0C0C0">
														<tr>
															<th class="header">Type</th>
						   						  			<th class="header">Extractors</th>
						   						  			<th class="header">Relevance</th>
						   						  		</tr>
														@{{#each noExtractorsPerType}}
															<tr>
																<td> @{{ @key }} </td>
																<td> @{{ count }} </td>
																<td> @{{ toFixed relevanceScore.value 3}} </td>
							    							</tr>
						 								@{{/each}}
														</table>
													</td>
													<td> 
														<table border="1" bordercolor="#C0C0C0">
														<tr>
															<th class="header">Resource</th>
						   						  			<th class="header">Extractors</th>
						   						  			<th class="header">Relevance</th>
						   						  		</tr>
														@{{#each noExtractorsPerResource}}
															<tr>
																<td> @{{ @key }} </td>
																<td> @{{ count }} </td>
																<td> @{{ toFixed relevanceScore.value 3}} </td>
							    							</tr>
						 								@{{/each}}
														</table>
													</td>
													<td> 
														<table border="1" bordercolor="#C0C0C0">
														<tr>
															<th class="header">LabelTypePair</th>
						   						  			<th class="header">Extractors</th>
						   						  			<th class="header">Relevance</th>
						   						  		</tr>
														@{{#each noExtractorsLabelTypePair}}
															<tr>
																<td> @{{ @key }} </td>
																<td> @{{ count }} </td>
																<td> @{{ toFixed relevanceScore.value 3}} </td>
							    							</tr>
						 								@{{/each}}
														</table>
													</td>
													<td> 
														<table border="1" bordercolor="#C0C0C0">
														<tr>
															<th class="header">LabelResourcePair</th>
						   						  			<th class="header">Extractors</th>
						   						  			<th class="header">Relevance</th>
						   						  		</tr>
														@{{#each noExtractorsLabelResourcePair}}
															<tr>
																<td> @{{ @key }} </td>
																<td> @{{ count }} </td>
																<td> @{{ toFixed relevanceScore.value 3}} </td>
							    							</tr>
						 								@{{/each}}
														</table>
													</td>
													<td> 
														<table border="1" bordercolor="#C0C0C0">
														<tr>
															<th class="header">TypeRespurcePair</th>
						   						  			<th class="header">Extractors</th>
						   						  			<th class="header">Relevance</th>
						   						  		</tr>
														@{{#each noExtractorsTypeResourcePair}}
															<tr>
																<td> @{{ @key }} </td>
																<td> @{{ count }} </td>
																<td> @{{ toFixed relevanceScore.value 3}} </td>
							    							</tr>
						 								@{{/each}}
														</table>
													</td>
													<td> 
														<table border="1" bordercolor="#C0C0C0">
														<tr>
															<th class="header">LabelTypeResourcePair</th>
						   						  			<th class="header">Extractors</th>
						   						  			<th class="header">Relevance</th>
						   						  		</tr>
														@{{#each noExtractorsLabelTypeResourcePair}}
															<tr>
																<td> @{{ @key }} </td>
																<td> @{{ count }} </td>
																<td> @{{ toFixed relevanceScore.value 3}} </td>
							    							</tr>
						 								@{{/each}}
														</table>
													</td>
												</tr>
												@{{/each}}
											</tbody>
										</table>
			      					</div>
			    				</div>
			  				</div>
			  				<div class="panel panel-default">
			    				<div class="panel-heading">
			      					<a data-toggle="collapse" data-parent="#accordion" href="#collapseSix">
										<h4 class="panel-title">
				 							@{{#addDocumentTypeLabel this.infoStat.documentType}} @{{/addDocumentTypeLabel}} CrowdTruth Cosine Similarity All Labels Result 
										</h4>
			      					</a>
			    				</div>
			    				<div id="collapseSix" class="panel-collapse collapse">
			      					<div class="panel-body report-pre report-modal-body">
										<table class="tablesorter table table-striped table-condensed" border="1" bordercolor="#C0C0C0" text-align="center"> 
											<thead> 
												<tr> 
												  	<th class="header">Extractor</th>
												  	<th class="header">Cosine Similarity</th>
						   						</tr>
											</thead>
											<tbody>
												@{{#each this.infoStat.content.statistics.crowdtruth.cosineSimilarityAllLabels}}
						 						<tr>
													<td> @{{ @key }} </td>
													<td> @{{ this }} </td>
												</tr>
												@{{/each}}
											</tbody>
										</table>
			      					</div>
			    				</div>
			  				</div>
			  				<div class="panel panel-default">
			    				<div class="panel-heading">
			      					<a data-toggle="collapse" data-parent="#accordion" href="#collapseSeven">
										<h4 class="panel-title">
				 							@{{#addDocumentTypeLabel this.infoStat.documentType}} @{{/addDocumentTypeLabel}} CrowdTruth Cosine Similarity Per Label Results 
										</h4>
			      					</a>
			    				</div>
			    				<div id="collapseSeven" class="panel-collapse collapse">
			      					<div class="panel-body report-pre report-modal-body">
										<table class="tablesorter table table-striped table-condensed" border="1" bordercolor="#C0C0C0" text-align="center"> 
											<thead> 
												<tr> 
												  	<th class="header">Label</th>
												  	<th class="header">Start Offset</th>
						   						  	<th class="header">End Offset</th>
						   						  	<th class="header">CosineSimilarityPerType</th>
						   						  	<th class="header">CosineSimilarityPerResource</th>
						   						   	<th class="header">CosineSimilarityPerLabelTypePair</th>
						   						  	<th class="header">CosineSimilarityPerLabelResourcePair</th>
						   						  	<th class="header">CosineSimilarityPerTypeResourcePair</th>
						   						  	<th class="header">CosineSimilarityPerLabelTypeResourcePair</th>
												</tr>
											</thead>
											<tbody>
												@{{#each this.infoStat.content.statistics.crowdtruth.entities}}
						 						<tr>
													<td> @{{ label }} </td>
													<td> @{{ startOffset }} </td>
													<td> @{{ endOffset }} </td>
													<td> 
														<table border="1" bordercolor="#C0C0C0">
														<tr>
															<th class="header">Extractor</th>
						   						  			<th class="header">CosSim</th>
						   						  		</tr>
														@{{#each cosineSimilarityPerType}}
															<tr>
																<td> @{{ @key }} </td>
																<td> @{{ this }} </td>
							    							</tr>
						 								@{{/each}}
														</table>
													</td>
													<td> 
														<table border="1" bordercolor="#C0C0C0">
														<tr>
															<th class="header">Extractor</th>
						   						  			<th class="header">CosSim</th>
						   						  		</tr>
														@{{#each cosineSimilarityPerResource}}
															<tr>
																<td> @{{ @key }} </td>
																<td> @{{ this }} </td>
							    							</tr>
						 								@{{/each}}
														</table>
													</td>
													<td> 
														<table border="1" bordercolor="#C0C0C0">
														<tr>
															<th class="header">Extractor</th>
						   						  			<th class="header">CosSim</th>
						   						  		</tr>
														@{{#each cosineSimilarityPerLabelTypePair}}
															<tr>
																<td> @{{ @key }} </td>
																<td> @{{ this }} </td>
							    							</tr>
						 								@{{/each}}
														</table>
													</td>
													<td> 
														<table border="1" bordercolor="#C0C0C0">
														<tr>
															<th class="header">Extractor</th>
						   						  			<th class="header">CosSim</th>
						   						  		</tr>
														@{{#each cosineSimilarityPerLabelResourcePair}}
															<tr>
																<td> @{{ @key }} </td>
																<td> @{{ this }} </td>
							    							</tr>
						 								@{{/each}}
														</table>
													</td>
													<td> 
														<table border="1" bordercolor="#C0C0C0">
														<tr>
															<th class="header">Extractor</th>
						   						  			<th class="header">CosSim</th>
						   						  		</tr>
														@{{#each cosineSimilarityPerTypeResourcePair}}
															<tr>
																<td> @{{ @key }} </td>
																<td> @{{ this }} </td>
							    							</tr>
						 								@{{/each}}
														</table>
													</td>
													<td> 
														<table border="1" bordercolor="#C0C0C0">
														<tr>
															<th class="header">Extractor</th>
						   						  			<th class="header">CosSim</th>
						   						  		</tr>
														@{{#each cosineSimilarityPerLabelTypeResourcePair}}
															<tr>
																<td> @{{ @key }} </td>
																<td> @{{ this }} </td>
							    							</tr>
						 								@{{/each}}
														</table>
													</td>
												</tr>
												@{{/each}}
											</tbody>
										</table>
			      					</div>
			    				</div>
			  				</div>
			  				<div class="panel panel-default">
			    				<div class="panel-heading">
			      					<a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
					  					<h4 class="panel-title">
					   						@{{#addDocumentTypeLabel this.infoStat.documentType}} @{{/addDocumentTypeLabel}} Stats
					  					</h4>
					 				</a>
			    				</div>
			    				<div id="collapseTwo" class="panel-collapse collapse">
			      					<div class="panel-body">
										<div><strong> Used in @{{ this.infoStat.cache.batches.count }} Batch(es) </strong>  </div>
										<div><strong data-toggle="tooltip" data-placement="top" title="@{{#eachProperty this.infoStat.cache.jobs.types }}@{{ key }} <br /> @{{/eachProperty}}"> Used in @{{ this.infoStat.cache.jobs.distinct }} Distinct Job Type(s) </strong></div>
										<div><strong> Annotated in a total of @{{ this.infoStat.cache.jobs.count }} Job(s) </strong> </div>
										<div><strong data-toggle="tooltip" data-placement="top" title="#Spam Workers: @{{ this.infoStat.cache.workers.spam }} <br /> #NonSpam Workers: @{{ this.infoStat.cache.workers.nonSpam }} <br /> #PotentialSpam Workers: @{{ this.infoStat.cache.workers.potentialSpam }}"> Annotated by a total of @{{ this.infoStat.cache.workers.count }} Worker(s) </strong> </div>
										<div><strong data-toggle="tooltip" data-placement="top" title="#Spam WorkerUnits: @{{ this.infoStat.cache.workerunits.spam }} </br> # NonSpam WorkerUnits: @{{ this.infoStat.cache.workerunits.nonSpam }}"> Collected a total of @{{ this.infoStat.cache.workerunits.count }} Annotation(s) </strong> </div>
										<hr/>
										<div><strong> Average @{{#addDocumentTypeLabel this.infoStat.documentType}} @{{/addDocumentTypeLabel}} Clarity (across CrowdTruth jobs): @{{ toFixed this.infoStat.avg_clarity 2}} </strong> </div>
					  					<div><strong> Marked as low-quality in @{{ this.infoStat.cache.filtered.count}} Job(s): </strong></div>
					  					<table class="tablesorter table table-striped table-condensed" border="1" bordercolor="#C0C0C0" text-align="center"> 
											<thead> 
												<tr> 
												  	<th class="header">Job Id</th>
												  	<th class="header">Job Title</th>
						   						  	<th class="header"  data-toggle="tooltip" data-placement="top" title="Sentence Clarity: the value is defined as the maximum sentence workerUnit score achieved on any workerUnit for that relex-structured-sentence. High agreement over the annotations is represented by high cosine scores, indicating a clear sentence."> @{{#addDocumentTypeLabel this.infoStat.documentType}} @{{/addDocumentTypeLabel}} Clarity</th>
												</tr>
											</thead>
											<tbody>
												@{{#each this.jobContent}}
						 							@{{#inArray metrics.filteredunits.list ../infoStat._id }}
													<tr>
													 <td> @{{#ifarray platformJobId }} @{{/ifarray}} </td>
													 <td> @{{ jobConf.content.title }} </td>
													 @{{#each metrics.units.withoutSpam }}
													 <td> @{{ toFixed max_relation_Cos.avg 2}} </td>
													 @{{/each}}
													</tr>
						 							@{{/inArray}}
												@{{/each}}
											</tbody>
										</table>
					      			</div>
					    		</div>
       					   	</div>
       					   	<div class="panel panel-default">
					    		<div class="panel-heading">
					      			<a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
					       				<h4 class="panel-title">
						  					@{{#addDocumentTypeLabel this.infoStat.documentType}} @{{/addDocumentTypeLabel}} in Jobs
					      				</h4>
					     			</a>
					    		</div>
					    		<div id="collapseThree" class="panel-collapse collapse">
					      			<div class="panel-body">
										<table class="tablesorter table table-striped table-condensed" border="1" bordercolor="#C0C0C0" text-align="center"> 
						<thead> 
						<tr> 
						  <th class="header" rowspan="3">Job Id</th>
						  <th class="header" rowspan="3">Filtered Out</th>
   						  <th class="header" colspan="2">Unit Metrics (across unit)</th>
						  <th class="header" colspan="4">Units Metrics (across all units in job)</th>
						  <th class="header" rowspan="3" data-toggle="tooltip" data-placement="top" title="Annotation Vector: The vector sum of the worker-sentence vectors for each sentence">Annotation Vector</th>
						</tr>
						<tr>
						  <th class="header" rowspan="2">Clarity</td>
						  <th class="header" rowspan="2">#Workers</td>
						  <th class="header" colspan="2">Mean</td>
						  <th class="header" colspan="2">Stddev</td>
						</tr>
						<tr>
						  <th class="header">Clarity</td>
						  <th class="header">#Workers</td>
						  <th class="header">Clarity</td>
						  <th class="header">#Workers</td>
						</tr>
						</thead>
						<tbody>
						  @{{#each this.jobContent}} 
						 
						  <tr>
						   <td data-toggle="tooltip" data-placement="top" title="Job Title: @{{ jobConf.content.title }}"> @{{#ifarray platformJobId }} @{{/ifarray}} </td>  					  
		                                   @{{#inArray metrics.filteredunits.list ../infoStat._id}}
							<td> True </td>
						   @{{else}}
							<td> False </td>
						   @{{/inArray}}

						   @{{#each metrics.units.withoutSpam}}
						   <td> @{{ toFixed avg.max_relation_Cos 2}} </td>
						   <td> @{{ toFixed avg.no_annotators 2}} </td>
						   @{{/each}}
						   <td> @{{ toFixed metrics.aggUnits.mean.max_relation_Cos 2}} </td>
						   <td> @{{ toFixed metrics.aggUnits.mean.no_annotators 2}} </td>
						   <td> @{{ toFixed metrics.aggUnits.stddev.max_relation_Cos 2}} </td>
						   <td> @{{ toFixed metrics.aggUnits.stddev.no_annotators 2}} </td>
						   <td> 
    							@{{#each results.withoutSpam}}
							 @{{#each this}} 
							   <table border="1" bordercolor="#C0C0C0">
							    <tr> 
							     @{{#eachProperty this}}
  								<td> @{{#abrWords key}} @{{/abrWords}} </td>
							     @{{/eachProperty }}
							    </tr>
							    <tr> 
							     @{{#eachProperty this}}
  								<td>@{{value}} </td>
							     @{{/eachProperty }}
							    </tr>
							   </table>
							  @{{/each}}
    							 @{{/each}}
						   </td>
						  @{{/each}}
						  	
						 </tbody>
						</table>
						
					      </div>
					    </div>
					  </div>
					  <div class="panel panel-default">
					    <div class="panel-heading">
					     <a data-toggle="collapse" data-parent="#accordion" href="#collapseFour">
					      <h4 class="panel-title">
						  Workers on @{{#addDocumentTypeLabel this.infoStat.documentType}} @{{/addDocumentTypeLabel}}
					      </h4>
					     </a>
					    </div>
					    <div id="collapseFour" class="panel-collapse collapse">
					      <div class="panel-body">
						<table id="myIndividualWorkerTable" class="tablesorter table table-striped table-condensed" border="1" bordercolor="#C0C0C0"> 
						<thead> 
						<tr> 
						    <th class="header">Worker Id</th>
						    <th class="header">Platform</th>
						    <th class="header">Platform Score</th>
						    <th class="header">Job Title</th>
						    <th class="header" data-toggle="tooltip" data-placement="top" title="Average Worker Cosine: is the vector cosine similarity between the annotations of a worker and the aggregated annotations of the other workers in a sentence, reflecting how close the relation(s) chosen by the worker are to the opinion of the majority for that sentence.">Avg. Worker Cosine</th>
						    <th class="header" data-toggle="tooltip" data-placement="top" title="Average Worker Agreement: worker metric based on the average worker-worker agreement between a worker and the rest of workers, weighted by the number of sentences in common.">Avg. Worker Agreement</th>
						    <th class="header" data-toggle="tooltip" data-placement="top" title="Avg. # Annotations / Unit: indicates the average number of different relations per sentence used by a worker for annotating a set of sentences.">Avg. # Annotation/Unit</th>
						    <th class="header" data-toggle="tooltip" data-placement="top" title="Worker Annotation Vector: the result of a single worker annotating a single unit. For each relation that the worker annotated in the unit, there is a 1 in the corresponding component, otherwise a 0.">Worker Annotation Vector</th>
						</tr> 
						</thead>
						<tbody>
						  @{{#each this.workerunitContent}}
						   @{{#each workerunitType}}
						    <tr>
						     <td> @{{ ../_id }} </td>  		  
		                                     <td> @{{ ../valuesWorker.softwareAgent_id}} </td>
						     <td> @{{ ../valuesWorker.cfWorkerTrust}} </td>
						     <td> @{{ job_info.jobConf.content.title}} </td>
						      @{{#each job_info.metrics.workers.withFilter}}
						       @{{#ifvalue ../../_id value=@key}}
						       <td> @{{ toFixed worker_cosine 2}} </td>
						       <td> @{{ toFixed avg_worker_agreement 2}} </td>
						       <td> @{{ toFixed ann_per_unit 2}} </td>
						       @{{/ifvalue}}
						      @{{/each}}
						       <td> 
    							@{{#each workerunit}}
							 
							   <table border="1" bordercolor="#C0C0C0">
							    <tr> 
							     @{{#eachProperty this}}
  								<td> @{{#abrWords key}} @{{/abrWords}} </td>
							     @{{/eachProperty }}
							    </tr>
							    <tr> 
							     @{{#eachProperty this}}
  								<td>@{{value}} </td>
							     @{{/eachProperty }}
							    </tr>
							   </table>
							
    							 @{{/each}}
						      </td>
						     </tr> 
						   @{{/each}}
						  @{{/each}}
						  	
						 </tbody>
						</table>
					      </div>
					    </div>
					  </div>
					  

			</div>
		   </div>
  	          </div>
		 </div>
		</div>
		</script>

	</div>	    											
</div>