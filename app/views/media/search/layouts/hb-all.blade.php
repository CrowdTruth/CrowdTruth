<div class="tab-pane active" id="all_tab">
	<div class='row'>
		<div class='tabOptions hidden'>
			<div class="btn-group" style="margin-left:5px;">
				<button type="button" class="btn btn-default specificFilter">
					Specific Filters
				</button>
			</div>
			<div class='btn-group vbColumns' style="margin-left:5px;">
				<button type="button" class="btn btn-default openAllColumns">Open all columns</button>
				<button type="button" class="btn btn-default openDefaultColumns hidden">Open default columns</button>
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu" role="menu" style="max-height:800px; overflow-y:scroll;">
					<li role="presentation" class="dropdown-header">General</li>
					<li><a href="#" data-vb="show" data-vbSelector="sent_id"></i>ID</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="format"></i>Format</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="domain"></i>Domain</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="title" ></i>File name</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="created_at"></i>Created</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="created_by"></i>Created By</a></li>
					<li role="presentation" class="divider"></li>
					<li role="presentation" class="dropdown-header">Content</li>
					<li><a href="#" data-vb="show" data-vbSelector="relation"></i>Seed Relation</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="term_1"></i>Term 1</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="term_1_processed"></i>Term 1 Processed</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="term_2"></i>Term 2</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="term_2_processed"></i>Term 2 Processed</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="sentence"></i>Sentence</a></li>
					<li><a href="#" data-vb="show" data-vbSelector="sentence_processed"></i>Sentence processed</a></li>
					<li role="presentation" class="divider"></li>
					<li role="presentation" class="dropdown-header">Jobs</li>
					<li><a href="#" data-vb="hide" data-vbSelector="number_of_FactSpan_jobs"></i>FactSpan Jobs</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="fact_clarity1"></i>FactSpan Clarity 1</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="fact_clarity2"></i>FactSpan Clarity 2</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="fact_top1"></i>FactSpan Top Answer 1</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="fact_top2"></i>FactSpan Top Answer 2</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="number_of_RelEx_jobs"></i>RelEx Jobs</a></li>
					<li><a href="#" data-vb="show" data-vbSelector="relex_clarity"></i>RelEx Clarity</a></li>
					<li><a href="#" data-vb="show" data-vbSelector="relex_top"></i>RelEx Top Answer</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="number_of_RelDir_jobs"></i>RelDir Jobs</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="reldir_clarity"></i>RelDir Clarity</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="reldir_top"></i>RelDir Top Answer</a></li>
					<li role="presentation" class="divider"></li>
					<li role="presentation" class="dropdown-header">Statistics</li>
					<li><a href="#" data-vb="hide" data-vbSelector="number_of_batches"></i>Batches</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="number_of_jobs"></i>Jobs</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="number_of_cf_judgements"></i>CF judgements</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="number_of_amt_judgements"></i>AMT judgements</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="number_of_children"></i>Children</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="parents"></i>Parents</a></li>
					<li><a href="#" data-vb="show" data-vbSelector="sentence_wordcount"></i>Words</a></li>
				</ul>
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
								  <button type="button" class="btn btn-sm btn-info active relexNone">Not Applied</button>
								</div>
							</td>
						</tr>
						<tr>
							<td>Relation Between Terms</td>
							<td class="text-right">
								<div class="btn-group" id='relationBetweenTerms'>
								  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.relationBetweenTerms]" data-query-value="1"><i class="fa fa-check"></i></button>
								  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.relationBetweenTerms]" data-query-value="0"><i class="fa fa-minus"></i></button>
								  <button type="button" class="btn btn-sm btn-info active relexNone">Not Applied</button>
								</div>
							</td>
						</tr>
						<tr>
							<td>Semicolon Between Terms</td>
							<td class="text-right">
								<div class="btn-group" id='semicolonBetweenTerms'>
								  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.semicolonBetweenTerms]" data-query-value="1"><i class="fa fa-check"></i></button>
								  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.semicolonBetweenTerms]" data-query-value="0"><i class="fa fa-minus"></i></button>
								  <button type="button" class="btn btn-sm btn-info active relexNone">Not Applied</button>
								</div>
							</td>
						</tr>
						<tr>
							<td>Comma-separated Terms</td>
							<td class="text-right">
								<div class="btn-group" id='commaSeparatedTerms'>
								  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.commaSeparatedTerms]" data-query-value="1"><i class="fa fa-check"></i></button>
								  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.commaSeparatedTerms]" data-query-value="0"><i class="fa fa-minus"></i></button>
								  <button type="button" class="btn btn-sm btn-info active relexNone">Not Applied</button>
								</div>
							</td>
						</tr>
						<tr>
							<td>Parenthesis Around Terms</td>
							<td class="text-right">
								<div class="btn-group" id='parenthesisAroundTerms'>
								  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.parenthesisAroundTerms]" data-query-value="1"><i class="fa fa-check"></i></button>
								  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.parenthesisAroundTerms]" data-query-value="0"><i class="fa fa-minus"></i></button>
								  <button type="button" class="btn btn-sm btn-info active relexNone">Not Applied</button>
								</div>
							</td>
						</tr>
						<tr>
							<td>Overlapping Terms</td>
							<td class="text-right">
								<div class="btn-group" id='overlappingTerms'>
								  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.overlappingTerms]" data-query-value="1"><i class="fa fa-check"></i></button>
								  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.overlappingTerms]" data-query-value="0"><i class="fa fa-minus"></i></button>
								  <button type="button" class="btn btn-sm btn-info active relexNone">Not Applied</button>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>	
		
		</div>
	</div>

	<div class='ctable-responsive'>		
	    <table class="table table-striped">
	        <thead data-query-key="" data-query-value="">
		        <tr>
		            <th data-vbIdentifier="checkbox">Select</th>

		            <th class="sorting" data-vbIdentifier="id" data-query-key="orderBy[_id]">ID</th>
					<th class="sorting" data-vbIdentifier="title" data-query-key="orderBy[title]" data-toggle="tooltip" data-placement="top" title="Upload file name">File Name</th>
		            <th class="sorting" data-vbIdentifier="format" data-query-key="orderBy[format]">Format</th>
		            <th class="sorting" data-vbIdentifier="domain" data-query-key="orderBy[domain]">Domain</th>
		            <th class="sorting" data-vbIdentifier="documentType" data-query-key="orderBy[documentType]">Document-Type</th>
		            <th class="sorting" data-vbIdentifier="created_at" data-query-key="orderBy[created_at]">Created</th>
		            <th class="sorting" data-vbIdentifier="created" data-query-key="orderBy[user_id]">Created by</th>
		            <th class="sorting whiteSpaceNormal" data-vbIdentifier="number_of_batches" data-query-key="orderBy[cache.batches.count]" data-toggle="tooltip" data-placement="top" title="Number of batches the sentence was used in">Batches</th>
		            <th class="sorting whiteSpaceNormal" data-vbIdentifier="number_of_jobs" data-query-key="orderBy[cache.jobs.count]" data-toggle="tooltip" data-placement="top" title="Number of jobs the sentence was used in">Jobs</th>     
		            <th class="sorting whiteSpaceNormal" data-vbIdentifier="clarity" data-query-key="orderBy[avg_clarity]" data-toggle="tooltip" data-placement="top" title="Clarity of the results">Clarity</th>     
                    <th class="sorting whiteSpaceNormal" data-vbIdentifier="number_of_children" data-query-key="orderBy[cache.children.count]" data-toggle="tooltip" data-placement="top" title="The number of units generated from this unit">Children</th>
                    <th class="sorting whiteSpaceNormal" data-vbIdentifier="parents"  data-toggle="tooltip" data-placement="top" title="The units from which this media unit was created">Parents</th>


                </tr>

                <tr class="inputFilters">
					<td>
						<input type="checkbox" class="checkAll" />
					</td>
					<td>
						<input class="input-sm form-control" type='text' data-query-key="match[_id]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="title">
						<input class="input-sm form-control" type='text' data-query-key="match[title]" data-query-operator="like" />
					</td>
					<td>
						<input class="input-sm form-control" type='text' data-query-key="match[format]" data-query-operator="like" />
					</td>
					<td>
						<input class="input-sm form-control" type='text' data-query-key="match[domain]" data-query-operator="like" />
					</td>
					<td>
                        <input class="input-sm form-control" type='text' data-query-key="match[documentType]" data-query-operator="like" />
                    </td>
					<td data-vbIdentifier="created_at">
						<div class="input-daterange">
						    <input type="text" class="input-sm form-control" name="start" data-query-key="match[created_at]" data-query-operator=">=" style="width:49% !important; float:left;" placeholder="Start Date" />
						    <input type="text" class="input-sm form-control" name="end" data-query-key="match[created_at]" data-query-operator="=<" style="width:49% !important; float:right;" placeholder="End Date" />
						</div>
					</td>
					<td>
						<input class="input-sm form-control" type='text' data-query-key="match[user_id]" data-query-operator="like" />
					</td>
					<td data-vbIdentifier="number_of_batches">
						<input class="input-sm form-control" type='text' data-query-key="match[cache.batches.count]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
						<input class="input-sm form-control" type='text' data-query-key="match[cache.batches.count]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
					</td>
					<td data-vbIdentifier="number_of_jobs">
						<input class="input-sm form-control" type='text' data-query-key="match[cache.jobs.count]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
						<input class="input-sm form-control" type='text' data-query-key="match[cache.jobs.count]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
					</td>
					<td data-vbIdentifier="clarity">
						<input class="input-sm form-control" type='text' data-query-key="match[avg_clarity]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
						<input class="input-sm form-control" type='text' data-query-key="match[avg_clarity]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
					</td>
                     <td data-vbIdentifier="number_of_children">
                        <input class="input-sm form-control" type='text' data-query-key="match[cache.children.count]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
                        <input class="input-sm form-control" type='text' data-query-key="match[cache.children.count]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
                    </td>

                    <td data-vbIdentifier="parents">
                        <input class="input-sm form-control" type='text' data-query-key="match[parents][]" />
                    </td>

				</tr>											        
	        </thead>
	        <tbody class='results'>											
				<script class='template' type="text/x-handlebars-template">
			        @{{#each documents}}
			        <tr>
			            <td data-vbIdentifier="checkbox"><input type="checkbox" id="@{{ this._id }}" name="rowchk" value="@{{ this._id }}"></td>
			            <td data-vbIdentifier="id">@{{ this._id }}</td>

			            <td data-vbIdentifier="title">@{{ this.title }}</td>
			            <td data-vbIdentifier="format">@{{ this.format }}</td>
			            <td data-vbIdentifier="domain">@{{ this.domain }}</td>
			            <td data-vbIdentifier="documentType">@{{ this.documentType }}</td>
			            <td data-vbIdentifier="created_at">@{{ this.created_at }}</td>	
			            <td data-vbIdentifier="user_id">@{{ highlightSelf this.user_id }}</td>
			            <td data-vbIdentifier="number_of_batches">@{{ this.cache.batches.count }}</td>
			            <td data-vbIdentifier="number_of_jobs">@{{ this.cache.jobs.count }}</td>
			            <td data-vbIdentifier="clarity">@{{ this.avg_clarity }}</td>
                        <td data-vbIdentifier="number_of_children">@{{ this.cache.children.count }}</td>
                        <td data-vbIdentifier="parents">@{{ this.parents }}</td>

			        </tr>
			        @{{/each}}
				</script>
	        </tbody>
	    </table>
    </div>											
</div>
<script>
	$("#col_1").change(function() {
		var searchColumn = this.val();
		var myId = this.id;
		$("#" + myId + "_search").attr("data-query-key", "match[" + searchColumn + "]");
		
	});
</script>
