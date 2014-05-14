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
						<li><a href="#" data-vb="show" data-vbSelector="relation"></i>Seed Relation</a></li>
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
		            <th class="sorting" data-vbIdentifier="relation" data-query-key="orderBy[content.relation.noPrefix]" data-toggle="tooltip" data-placement="top" title="Seed Relation used to identify sentence in corpus">Seed Relation</th>
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
						RelEx-structured sentence @{{ explodeLastSlash this._id }}
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

@include('media.search.layouts.hb-modalindividualunit')
@include('media.search.layouts.hb-modalannotations')
				
</div>
