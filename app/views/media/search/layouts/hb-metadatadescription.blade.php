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
						<li><a href="#" data-vb="show" data-vbSelector="metadata_identifier"></i>Identifier</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="metadata_entities"></i>Extracted Entities</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="metadata_topics"></i>Topics</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="video_title"></i>Title</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="metadata_language"></i>Language</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="number_of_batches"></i>Used In # of Batches</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="number_of_jobs"></i>Used In # of Jobs</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="created_at"></i>Created At</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="created_by"></i>Created By</a></li>
					</ul>
				</div>
			</div>
		</div>	
	</div>
	<div class='ctable-responsive cResults'>
	    <table class="table table-striped qwe">
	       	<thead data-query-key="collection=temp&match[documentType]" data-query-value="metadatadescription">
		        <tr>
		            <th data-vbIdentifier="checkbox">Select</th>
			    	<th class="sorting" data-vbIdentifier="metadata_identifier" data-query-key="orderBy[title]">Identifier</th>
			    	<th class="sorting" data-vbIdentifier="metadata_entities_description" data-query-key="orderBy[content.description]">Metadata Entities </th>
			    	<th class="sorting" data-vbIdentifier="metadata_entities" data-query-key="orderBy[content.features.entities.value]">Extracted Entities</th>
			    	<th class="sorting" data-vbIdentifier="metadata_topics" data-query-key="orderBy[content.features.topics.label]">Topics</th>
			    	<th class="sorting" data-vbIdentifier="video_title" data-query-key="orderBy[videoTitle]">Title</th>
		            <th class="sorting" data-vbIdentifier="metadata_language" data-query-key="orderBy[language]">Language</th>
					<th class="sorting whiteSpaceNormal" data-vbIdentifier="number_of_batches" data-query-key="orderBy[cache.batches.count]">Used In # of Batches</th>
				    <th class="sorting whiteSpaceNormal" data-vbIdentifier="number_of_jobs" data-query-key="orderBy[cache.jobs.count]">Used In # of Jobs</th>     
				    <th class="sorting whiteSpaceNoWrap" data-vbIdentifier="created_at" data-query-key="orderBy[created_at]" style="min-width:220px; width:auto;">Created At</th>
				    <th class="sorting" data-vbIdentifier="created_by" data-query-key="orderBy[user_id]">Created By</th>
		        </tr>
				<tr class="inputFilters">
					<td data-vbIdentifier="checkbox">
						<input type="checkbox" class="checkAll" />
					</td>
					<td data-vbIdentifier="metadata_identifier">
						<input class="input-sm form-control" type='text' data-query-key="match[title]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="metadata_entities_description">
						<input class="input-sm form-control" type='text' data-query-key="match[content.description]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="metadata_entities">
						<input class="input-sm form-control" type='text' data-query-key="match[content.features.entities]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="metadata_topics">
						<input class="input-sm form-control" type='text' data-query-key="match[content.features.topics]" data-query-operator="like" />	
					</td>
					<td data-vbIdentifier="video_title">
						<input class="input-sm form-control" type='text' data-query-key="match[videoTitle]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="metadata_language">
						<input class="input-sm form-control" type='text' data-query-key="match[language]" data-query-operator="like" />
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
				    	<td data-vbIdentifier="metadata_identifier"> 
							<a class='testModal' id='@{{ this._id }}' data-modal-query="unit=@{{this._id}}" data-api-target="{{ URL::to('api/analytics/unit?') }}" data-target="#modalIndividualMetadataDescriptionUnit" data-toggle="tooltip" data-placement="top" title="Click to see the individual unit page">
								@{{ this.title }}
							</a>
				    	</td>
				    	<td data-vbIdentifier="metadata_entities_description">
			            	@{{ highlightEntitiesDescription @root.searchQuery this.content }}
			            </td>
			            <td data-vbIdentifier="metadata_entities">
			            	@{{#if  this.content.features.entities }}
				            	@{{#eachProperty this.content.features.entities }}
				            		<b>@{{value.value}}, </b>
				            	@{{/eachProperty}}   
			            	@{{/if}}
			            </td>
				    	<td data-vbIdentifier="metadata_topics">
				    		@{{#if  this.content.features.topics }}
				            	@{{#eachProperty this.content.features.topics }}
				            		<b>@{{value.label}} - score: @{{value.score}} </b> (@{{value.wikiLink}}) <br>
				            	@{{/eachProperty}}   
			            	@{{/if}}
			            </td>
				    	<td data-vbIdentifier="video_title">@{{ this.videoTitle }}</td>
				    	<td data-vbIdentifier="metadata_language"> @{{ this.language }} </td>
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
		   			<div class="modal-body" >
						<div>
							<video width="240" height="160" controls preload="none" data-toggle="tooltip" data-placement="top" title="Click to play">
								<source src="@{{ this.infoStat.videoContent }}" type="video/mp4" >
								<source src="@{{ this.infoStat.videoContent }}" type="video/ogg" >
								Your browser does not support the video tag.
								</source>
							</video>
							<div style="float:right;">
								<strong> Video Title: </strong> @{{ this.infoStat.videoTitle }} <br>
							    <strong> Video Metadata Description: </strong> @{{ this.infoStat.content }} 
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
										<div><strong data-toggle="tooltip" data-placement="top" title="#Spam WorkerUnits: @{{ this.infoStat.cache.workerUnits.spam }} </br> # NonSpam WorkerUnits: @{{ this.infoStat.cache.workerUnits.nonSpam }}"> Collected a total of @{{ this.infoStat.cache.workerUnits.count }} Annotation(s) </strong> </div>
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
						 							@{{#inArray metrics.filteredUnits.list ../infoStat._id }}
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
		                                   @{{#inArray metrics.filteredUnits.list ../infoStat._id}}
							<td> True </td>
						   @{{else}}
							<td> False </td>
						   @{{/inArray}}

						   @{{#each metrics.units.withoutSpam}}
						   <td> @{{ toFixed max_relation_Cos.avg 2}} </td>
						   <td> @{{ toFixed no_annotators.avg 2}} </td>
						   @{{/each}}
						   <td> @{{ toFixed metrics.aggUnits.mean.max_relation_Cos.avg 2}} </td>
						   <td> @{{ toFixed metrics.aggUnits.mean.no_annotators.avg 2}} </td>
						   <td> @{{ toFixed metrics.aggUnits.stddev.max_relation_Cos.avg 2}} </td>
						   <td> @{{ toFixed metrics.aggUnits.stddev.no_annotators.avg 2}} </td>
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
						  @{{#each this.workerUnitContent}}
						   @{{#each workerUnitType}}
						    <tr>
						     <td> @{{ ../_id }} </td>  		  
		                                     <td> @{{ ../valuesWorker.softwareAgent_id}} </td>
						     <td> @{{ ../valuesWorker.cfWorkerTrust}} </td>
						     <td> @{{ job_info.jobConf.content.title}} </td>
						      @{{#each job_info.metrics.workers.withFilter}}
						       @{{#ifvalue ../../_id value=@key}}
						       <td> @{{ toFixed worker_cosine.avg 2}} </td>
						       <td> @{{ toFixed avg_worker_agreement.avg 2}} </td>
						       <td> @{{ toFixed ann_per_unit.avg 2}} </td>
						       @{{/ifvalue}}
						      @{{/each}}
						       <td> 
    							@{{#each workerUnit}}
							 
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