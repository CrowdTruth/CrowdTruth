<div class="tab-pane" id="twrex-structured-sentence_tab">
	<div class='row'>
		<div class='tabOptions hidden'>
			<div class="btn-group" style="margin-left:5px;">
				<button type="button" class="btn btn-default specificFilter">
					Specific Filters
				</button>
			</div>
			<div class='btn-group' style="margin-left:5px;">
				<button type="button" class="btn btn-default openAllColumns">Open all columns</button>
				<button type="button" class="btn btn-default openDefaultColumns hidden">Open default columns</button>
				<div class="btn-group vbColumns">
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
					<span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu">
						<li><a href="#" data-vb="show" data-vbSelector="checkbox"></i>Select</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="format"></i>Format</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="domain"></i>Domain</a></li>
						<!-- <li><a href="#" data-vb="show" data-vbSelector="documentType"></i>Document-Type</a></li> -->
						<li><a href="#" data-vb="show" data-vbSelector="relation"></i>Relation</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="sent_id"></i>ID</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="term_1"></i>Term 1</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="term_1_processed"></i>Term 1 Processed</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="term_2"></i>Term 2</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="term_2_processed"></i>Term 2 Processed</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="sentence"></i>Sentence</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="sentence_processed"></i>Sentence processed</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="number_of_batches"></i># Batches</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="number_of_jobs"></i># Jobs</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="sentence_wordcount"></i># Words</a></li>					
						<li><a href="#" data-vb="hide" data-vbSelector="created_at"></i>Created</a></li>
					</ul>
				</div>
			</div>
			<div class='specificFilterContent hidden'>
				<table class='table table-striped table-condensed specificFilterOptions'>
					<tbody>
						<tr>
							<td>Relation In Sentence</td>
							<td class="text-right">
								<div class="btn-group" id='relationInSentence'>
								  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.relationInSentence]" data-query-value="1"><i class="fa fa-check"></i></button>
								  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.relationInSentence]" data-query-value="0"><i class="fa fa-minus"></i></button>
								  <button type="button" class="btn btn-sm btn-info active">Not Applied</button>
								</div>
							</td>
						</tr>
						<tr>
							<td>Relation Outside Terms</td>
							<td class="text-right">
								<div class="btn-group" id='relationOutsideTerms'>
								  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.relationOutsideTerms]" data-query-value="1"><i class="fa fa-check"></i></button>
								  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.relationOutsideTerms]" data-query-value="0"><i class="fa fa-minus"></i></button>
								  <button type="button" class="btn btn-sm btn-info active twrexNone">Not Applied</button>
								</div>
							</td>
						</tr>
						<tr>
							<td>Relation Between Terms</td>
							<td class="text-right">
								<div class="btn-group" id='relationBetweenTerms'>
								  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.relationBetweenTerms]" data-query-value="1"><i class="fa fa-check"></i></button>
								  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.relationBetweenTerms]" data-query-value="0"><i class="fa fa-minus"></i></button>
								  <button type="button" class="btn btn-sm btn-info active twrexNone">Not Applied</button>
								</div>
							</td>
						</tr>
						<tr>
							<td>Semicolon Between Terms</td>
							<td class="text-right">
								<div class="btn-group" id='semicolonBetweenTerms'>
								  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.semicolonBetweenTerms]" data-query-value="1"><i class="fa fa-check"></i></button>
								  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.semicolonBetweenTerms]" data-query-value="0"><i class="fa fa-minus"></i></button>
								  <button type="button" class="btn btn-sm btn-info active twrexNone">Not Applied</button>
								</div>
							</td>
						</tr>
						<tr>
							<td>Comma-separated Terms</td>
							<td class="text-right">
								<div class="btn-group" id='commaSeparatedTerms'>
								  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.commaSeparatedTerms]" data-query-value="1"><i class="fa fa-check"></i></button>
								  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.commaSeparatedTerms]" data-query-value="0"><i class="fa fa-minus"></i></button>
								  <button type="button" class="btn btn-sm btn-info active twrexNone">Not Applied</button>
								</div>
							</td>
						</tr>
						<tr>
							<td>Parenthesis Around Terms</td>
							<td class="text-right">
								<div class="btn-group" id='parenthesisAroundTerms'>
								  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.parenthesisAroundTerms]" data-query-value="1"><i class="fa fa-check"></i></button>
								  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.parenthesisAroundTerms]" data-query-value="0"><i class="fa fa-minus"></i></button>
								  <button type="button" class="btn btn-sm btn-info active twrexNone">Not Applied</button>
								</div>
							</td>
						</tr>
						<tr>
							<td>Overlapping Terms</td>
							<td class="text-right">
								<div class="btn-group" id='overlappingTerms'>
								  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.overlappingTerms]" data-query-value="1"><i class="fa fa-check"></i></button>
								  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.overlappingTerms]" data-query-value="0"><i class="fa fa-minus"></i></button>
								  <button type="button" class="btn btn-sm btn-info active twrexNone">Not Applied</button>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>			
		</div>
	</div>
	<div class='ctable-responsive cResults'>
	    <table class="table table-striped qwe">
	        <thead data-query-key="match[documentType]" data-query-value="twrex-structured-sentence">
		        <tr>
		            <th data-vbIdentifier="checkbox" data-toggle="tooltip" data-placement="top" title="Check to select this row">Select</th>
		            <th class="sorting" data-vbIdentifier="format" data-query-key="orderBy[format]" data-toggle="tooltip" data-placement="top" title="Format of the sentence">Format</th>
		            <th class="sorting" data-vbIdentifier="domain" data-query-key="orderBy[domain]" data-toggle="tooltip" data-placement="top" title="Domain to which this sentence belongs">Domain</th>
		            <!-- <th class="sorting" data-vbIdentifier="documentType" data-query-key="orderBy[documentType]">Document-Type</th> -->
		            <th class="sorting" data-vbIdentifier="relation" data-query-key="orderBy[content.relation.noPrefix]" data-toggle="tooltip" data-placement="top" title="Seed Relation used to identify sentence in corpus">Relation</th>
			    <th class="sorting" data-vbIdentifier="sent_id" data-query-key="orderBy[_id]" data-toggle="tooltip" data-placement="top" title="CrowdTruth unit ID">ID</th>
		            <th class="sorting" data-vbIdentifier="term_1" data-query-key="orderBy[content.terms.first.text]" data-toggle="tooltip" data-placement="top" title="Subject of the seed relation used to identify the sentence">Term 1</th>
		            <th class="sorting" data-vbIdentifier="term_1_processed" data-query-key="orderBy[content.terms.first.formatted]" data-toggle="tooltip" data-placement="top" title="Term 1 with processing as it appears in Processed Sentences">Term 1 Processed</th>
		            <th class="sorting" data-vbIdentifier="term_2" data-query-key="orderBy[content.terms.second.text]" data-toggle="tooltip" data-placement="top" title="Object of the seed relation used to identify the sentence">Term 2</th>
		            <th class="sorting" data-vbIdentifier="term_2_processed" data-query-key="orderBy[content.terms.second.formatted]" data-toggle="tooltip" data-placement="top" title="Term 2 with processing as it appears in Processed Sentences">Term 2 Processed</th>
		            <th class="sorting" data-vbIdentifier="sentence" data-query-key="orderBy[content.sentence.text]" data-toggle="tooltip" data-placement="top" title="Original sentence from the corpus">Sentence</th>
		            <th class="sorting" data-vbIdentifier="sentence_processed" data-query-key="orderBy[content.sentence.formatted]" data-toggle="tooltip" data-placement="top" title="Original sentence with extra processing including highlighting of terms">Sentence Processed</th>
		            <th class="sorting whiteSpaceNormal" data-vbIdentifier="sentence_wordcount" data-query-key="orderBy[content.properties.sentenceWordCount]" data-toggle="tooltip" data-placement="top" title="Number of words in the sentence"># Words</th>
		            <th class="sorting whiteSpaceNormal" data-vbIdentifier="number_of_batches" data-query-key="orderBy[cache.batches.count]" data-toggle="tooltip" data-placement="top" title="Number of batches the sentence was used in"># Batches</th>
		            <th class="sorting whiteSpaceNormal" data-vbIdentifier="number_of_jobs" data-query-key="orderBy[cache.jobs.count]" data-toggle="tooltip" data-placement="top" title="Number of jobs the sentence was used in"># Jobs</th>     
		            <th class="sorting whiteSpaceNoWrap" data-vbIdentifier="created_at" data-query-key="orderBy[created_at]" style="min-width:220px; width:auto;" data-toggle="tooltip" data-placement="top" title="When the sentence was loaded into the framework">Created</th>
		        </tr>
				<tr class="inputFilters">
					<td data-vbIdentifier="checkbox">
						<input type="checkbox" class="checkAll" />
					</td>
					<td data-vbIdentifier="format">
						<input class="input-sm form-control" type='text' data-query-key="match[format]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="domain">
						<input class="input-sm form-control" type='text' data-query-key="match[domain]" data-query-operator="like" />
					</td>
<!-- 					<td data-vbIdentifier="documentType">
						<input class="input-sm form-control" type='text' data-query-key="match[documentType]" data-query-operator="like" />
					</td> -->
					<td data-vbIdentifier="relation">
						<input class="input-sm form-control" type='text' data-query-key="match[content.relation.noPrefix]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="sent_id">
						<input class="input-sm form-control" type='text' data-query-key="match[_id]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="term_1">
						<input class="input-sm form-control" type='text' data-query-key="match[content.terms.first.text]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="term_1_processed">
						<input class="input-sm form-control" type='text' data-query-key="match[content.terms.first.formatted]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="term_2">
						<input class="input-sm form-control" type='text' data-query-key="match[content.terms.second.text]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="term_2_processed">
						<input class="input-sm form-control" type='text' data-query-key="match[content.terms.second.formatted]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="sentence">
						<input class="input-sm form-control" type='text' data-query-key="match[content.sentence.text]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="sentence_processed">
						<input class="input-sm form-control" type='text' data-query-key="match[content.sentence.formatted]" data-query-operator="like" placeholder="Enter your search keywords here" />
					</td>
					<td data-vbIdentifier="sentence_wordcount">
						<input class="input-sm form-control" type='text' data-query-key="match[content.properties.sentenceWordCount]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
						<input class="input-sm form-control" type='text' data-query-key="match[content.properties.sentenceWordCount]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
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
				</tr>											        
	        </thead>
	        <tbody class='results'>											
				<script class='template' type="text/x-handlebars-template">
			        @{{#each documents}}
			        <tr class="text-center">
			            <td data-vbIdentifier="checkbox"><input type="checkbox" id="@{{ this._id }}" name="rowchk" value="@{{ this._id }}"></td>
			            <td data-vbIdentifier="format">@{{ this.format }}</td>
			            <td data-vbIdentifier="domain">@{{ this.domain }}</td>
			            <td data-vbIdentifier="relation">@{{ this.content.relation.noPrefix }}</td>
				    <td data-vbIdentifier="sent_id">
					<a class='testModal' id='@{{ this._id }}' data-modal-query="unit=@{{this._id}}" data-api-target="{{ URL::to('api/analytics/unit?') }}" data-target="#modalIndividualUnit" data-toggle="tooltip" data-placement="top" title="Click to see the individual unit page">
						@{{ this._id }}
					</a>
				    </td>
			            <td data-vbIdentifier="term_1">@{{ this.content.terms.first.text }}</td>
			            <td data-vbIdentifier="term_1_processed">@{{ this.content.terms.first.formatted }}</td>
			            <td data-vbIdentifier="term_2">@{{ this.content.terms.second.text }}</td>
			            <td data-vbIdentifier="term_2_processed">@{{ this.content.terms.second.formatted }}</td>
			            <td data-vbIdentifier="sentence" class="text-left">@{{ this.content.sentence.text }}</td>
			            <td data-vbIdentifier="sentence_processed" class="text-left">@{{ highlightTerms ../searchQuery this.content }}</td>
			            <td data-vbIdentifier="sentence_wordcount">@{{ this.content.properties.sentenceWordCount }}</td>
			            <td data-vbIdentifier="number_of_batches">@{{ this.cache.batches.count }}</td>
			            <td data-vbIdentifier="number_of_jobs">@{{ this.cache.jobs.count }}</td>
			            <td data-vbIdentifier="created_at">@{{ this.created_at }}</td>
			        </tr>
			        @{{/each}}
				</script>
	        </tbody>
    	</table>
    </div>

	<div class='hidden' id='modalIndividualUnit'>

		<script class='template' type="text/x-handlebars-template">
				<!-- Modal -->
				<div class="modal fade bs-example-modal-lg" id="activeTabModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabelUnit" aria-hidden="true">
				  <div class="modal-dialog modal-lg">
				    <div class="modal-content">
				      <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabelUnit">Individual Unit Page</h4>
				      </div>
				      <div class="modal-body" >
					<div class="panel-group" id="accordion">
					  <div class="panel panel-default">
					    <div class="panel-heading">
					      <h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
						  Unit Information 
						</a>
					      </h4>
					    </div>
					    <div id="collapseOne" class="panel-collapse collapse in">
					      <div class="panel-body">					
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
						<div><strong> Domain: </strong> @{{ this.infoStat.domain }} </div>
						<div><strong> Format: </strong> @{{ this.infoStat.format }} </div>
						<div><strong> Type: </strong> @{{ this.infoStat.documentType }} </div>
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
					      <h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
						  Unit Stats
						</a>
					      </h4>
					     </div>
					     <div id="collapseTwo" class="panel-collapse collapse">
					      <div class="panel-body">
						<div><strong> @{{ this.infoStat.cache.batches.count }} Batches </strong>  </div>
						<div><strong> @{{ this.infoStat.cache.jobs.distinct }} Distinct Job Type(s) </strong></div>
						<div><strong data-toggle="tooltip" data-placement="top" title="@{{#eachProperty this.infoStat.cache.jobs.types }}@{{ key }}: @{{ value }} <br /> @{{/eachProperty}}"> @{{ this.infoStat.cache.jobs.count }} Job(s) </strong> </div>
						<div><strong data-toggle="tooltip" data-placement="top" title="#Spam Workers: @{{ this.infoStat.cache.workers.spamCount }} <br /> #NonSpam Workers: @{{ this.infoStat.cache.workers.nonSpamCount }}"> @{{ this.infoStat.cache.workers.count }} Worker(s) in Total </strong> </div>
						<div><strong data-toggle="tooltip" data-placement="top" title="#Spam Annotations: @{{ this.infoStat.cache.annotations.spamCount }} </br> # NonSpam Annotations: @{{ this.infoStat.cache.annotations.nonSpamCount }}"> @{{ this.infoStat.cache.annotations.count }} Annotation(s) in Total </strong> </div>
						<hr/>
						<div><strong data-toggle="tooltip" data-placement="top" title="Jobs: @{{ this.infoStat.cache.filteredOutUnit.list }}"> @{{ this.infoStat.cache.filteredOutUnit.count}} Time(s) Filtered Out Unit</strong></div>
						<div><strong> Average Unit Clarity (across CrowdTruth jobs): @{{ this.infoStat.cache.avg_clarity }} </strong> </div>
					      </div>
					    </div>
       					   </div>
					   
					   <div class="panel panel-default">
					     <div class="panel-heading">
					      <h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
						  Used in Jobs
						</a>
					      </h4>
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
						  <th class="header" rowspan="3">Annotation Vector</th>
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
						   <td data-toggle="tooltip" data-placement="top" title="Job Title: @{{ jobConf.content.title }}"> @{{ platformJobId }} </td>  					  
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
					      <h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion" href="#collapseFour">
						  Workers on Unit
						</a>
					      </h4>
					    </div>
					    <div id="collapseFour" class="panel-collapse collapse">
					      <div class="panel-body">
						<table id="myIndividualWorkerTable" class="tablesorter table table-striped table-condensed"> 
						<thead> 
						<tr> 
						    <th class="header">Worker Id</th>
						    <th class="header">Platform</th>
						    <th class="header">Job Title</th>
						    <th class="header">Avg. Worker Cosine</th>
						    <th class="header">Avg. Worker Agreement</th>
						    <th class="header">Avg. # Annotation/Unit</th>
						    <th class="header">Worker Annotation Vector</th>
						</tr> 
						</thead>
						<tbody>
						  @{{#each this.annotationContent}} 
						   @{{#each annotationType}}
						    <tr>
						     <td> @{{ ../_id }} </td>  		  
		                                     <td> @{{ ../valuesWorker.softwareAgent_id}} </td>
						     <td> @{{ job_info.jobConf.content.title}} </td>
						      @{{#each job_info.metrics.workers.withFilter}}
						       @{{#ifvalue ../../_id value=@key}}
						       <td> @{{ toFixed worker_cosine.avg 2}} </td>
						       <td> @{{ toFixed avg_worker_agreement.avg 2}} </td>
						       <td> @{{ toFixed ann_per_unit.avg 2}} </td>
						       @{{/ifvalue}}
						      @{{/each}}
						       <td> 
    							@{{#each annotation}}
							 
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
				      </div>
				    </div>
				  </div>
				</div>
		</script>

	</div>					
</div>