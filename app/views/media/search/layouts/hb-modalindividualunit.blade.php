<div class='hidden' id='modalIndividualUnit'>
	<script class='template' type="text/x-handlebars-template">
		<div class="modal fade bs-example-modal-lg" id="activeTabModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabelUnit" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabelUnit">Individual Unit @{{#addDocumentTypeLabel this.infoStat.documentType}} @{{/addDocumentTypeLabel}} Page</h4>
					</div>
					<div class="modal-body" >
						@{{#if this.infoStat.content.sentence.formatted }} 
								<div><strong>Sentence: </strong> @{{ this.infoStat.content.sentence.formatted}} </div>
							@{{else}}
								<div><strong>Sentence: </strong> @{{ this.infoStat.content.sentence.text}} </div>
							@{{/if}}
							@{{#if this.infoStat.content.terms.first.formatted}}						
								<div><strong> Term 1: </strong> @{{ this.infoStat.content.terms.first.formatted }} </div>
							@{{else}}
								<div><strong> Term 1: </strong> @{{ this.infoStat.content.terms.first.text }} </div>
							@{{/if}}
							@{{#if this.infoStat.content.terms.second.formatted}}						
								<div><strong> Term 2: </strong> @{{ this.infoStat.content.terms.second.formatted }} </div>
							@{{else}}
								<div><strong> Term 2: </strong> @{{ this.infoStat.content.terms.second.text }} </div>
							@{{/if}}
							<div><strong> Seed Relation: </strong> @{{ this.infoStat.content.relation.noPrefix }} </div>
						<br/>
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
								<div><strong> Domain: </strong> @{{ this.infoStat.domain }} </div>
								<div><strong> Format: </strong> @{{ this.infoStat.format }} </div>
								<div><strong> Type: </strong> @{{#addDocumentTypeLabel this.infoStat.documentType}} @{{/addDocumentTypeLabel}} </div>
								<div><strong> Properties: </strong> 
								<ul>
								@{{#eachProperty this.infoStat.content.properties}}
									<li> @{{key}}: @{{value}} </li>
								@{{/eachProperty}}
								</ul>
								</div>
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
								<div><strong data-toggle="tooltip" data-placement="top" title="#Spam Workerunits: @{{ this.infoStat.cache.workerunits.spam }} </br> # NonSpam Workerunits: @{{ this.infoStat.cache.workerunits.nonSpam }}"> Collected a total of @{{ this.infoStat.cache.workerunits.count }} Annotation(s) </strong> </div>
								<hr/>
								<div><strong> Average @{{#addDocumentTypeLabel this.infoStat.documentType}} @{{/addDocumentTypeLabel}} Clarity (across CrowdTruth jobs): @{{ toFixed this.infoStat.avg_clarity 2}} </strong> </div>
								<div><strong> Marked as low-quality in @{{ this.infoStat.cache.filtered.count}} Job(s): </strong></div>
								<table class="tablesorter table table-striped table-condensed" border="1" bordercolor="#C0C0C0" text-align="center"> 
								<thead> 
								<tr> 
								  <th class="header">Job Id</th>
								  <th class="header">Job Title</th>
								  <th class="header"  data-toggle="tooltip" data-placement="top" title="Sentence Clarity: the value is defined as the maximum sentence annotation score achieved on any annotation for that relex-structured-sentence. High agreement over the annotations is represented by high cosine scores, indicating a clear sentence."> @{{#addDocumentTypeLabel this.infoStat.documentType}} @{{/addDocumentTypeLabel}} Clarity</th>
								</tr>
								</thead>
								<tbody>
								@{{#each this.jobContent}}
								 @{{#inArray metrics.filteredUnits.list ../infoStat._id }}
								<tr>
								 <td> @{{#ifarray platformJobId }} @{{/ifarray}} </td>
								 <td> @{{ jobConf.content.title }} </td>
								 @{{#each metrics.units.withoutSpam }}
								 <td> @{{ toFixed avg.max_relation_Cos 2}} </td>
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