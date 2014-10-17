<div class="tab-pane" id="fullvideo_tab">	
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
						<li><a href="#" data-vb="show" data-vbSelector="video_identifier"></i>Identifier</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="video_content"></i>Video</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="video_name"></i>Name</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="video_source"></i>Source</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="video_title"></i>Title</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="video_subject"></i>Subject</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="number_of_video_keyframes"></i># Keyframes</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="number_of_video_segments"></i># Segments</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="video_description"></i>Description</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="video_abstract"></i>Abstract</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="video_date"></i>CreationDate</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="video_duration"></i>Duration</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="video_language"></i>Language</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="video_spatial"></i>Location</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="number_of_batches"></i>Used In # of Batches</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="number_of_jobs"></i>Used In # of Jobs</a></li>
                        <li><a href="#" data-vb="hide" data-vbSelector="number_of_cf_judgements"></i># CF judgements</a></li>
                        <li><a href="#" data-vb="hide" data-vbSelector="number_of_amt_judgements"></i># AMT judgements</a></li>
                        <li><a href="#" data-vb="hide" data-vbSelector="number_of_children"></i># of children</a></li>
                        <li><a href="#" data-vb="hide" data-vbSelector="parents"></i>parents</a></li>
                        <li><a href="#" data-vb="hide" data-vbSelector="created_at"></i>Created At</a></li>
                        <li><a href="#" data-vb="show" data-vbSelector="created_by"></i>Created By</a></li>
					</ul>
				</div>
			</div>
		</div>	
	</div>
	<div class='ctable-responsive cResults'>
	    <table class="table table-striped qwe">
	       	<thead data-query-key="match[documentType]" data-query-value="fullvideo">
		        <tr>
                    <th data-vbIdentifier="checkbox">Select</th>
                    <th class="sorting" data-vbIdentifier="video_identifier" data-query-key="orderBy[content.identifier]">Identifier</th>
		            <th class="sorting" data-vbIdentifier="video_content" data-query-key="orderBy[content.identifier]">Video</th>
		            <th class="sorting" data-vbIdentifier="video_name" data-query-key="orderBy[content.videoName]">Name</th>
		            <th class="sorting" data-vbIdentifier="video_source" data-query-key="orderBy[source]">Source</th>
			        <th class="sorting" data-vbIdentifier="video_title" data-query-key="orderBy[content.metadata.title.nl]">Title</th>
		            <th class="sorting" data-vbIdentifier="video_subject" data-query-key="orderBy[content.metadata.subject.nl]">Subject</th>
                    <th class="sorting" data-vbIdentifier="number_of_video_keyframes" data-query-key="orderBy[keyframes.count]"># Keyframes</th>
                    <th class="sorting" data-vbIdentifier="number_of_video_segments" data-query-key="orderBy[segments.count]"># Segments</th>
                    <th class="sorting" data-vbIdentifier="video_description" data-query-key="orderBy[content.metadata.description]">Description</th>
                    <th class="sorting" data-vbIdentifier="video_abstract" data-query-key="orderBy[content.metadata.abstract]" style='min-width:300px'>Abstract</th>
                    <th class="sorting" data-vbIdentifier="video_date" data-query-key="orderBy[content.metadata.date]">CreationDate</th>
                    <th class="sorting" data-vbIdentifier="video_duration" data-query-key="orderBy[content.metadata.extent]">Duration</th>
                    <th class="sorting" data-vbIdentifier="video_language" data-query-key="orderBy[content.metadata.language]">Language</th>
                    <th class="sorting" data-vbIdentifier="video_spatial" data-query-key="orderBy[content.metadata.spatial.nl]">Location</th>
                    <th class="sorting whiteSpaceNormal" data-vbIdentifier="number_of_batches" data-query-key="orderBy[cache.batches.count]">Used In # of Batches</th>
                    <th class="sorting whiteSpaceNormal" data-vbIdentifier="number_of_jobs" data-query-key="orderBy[cache.jobs.count]">Used In # of Jobs</th>
                    <th class="sorting whiteSpaceNormal" data-vbIdentifier="number_of_cf_judgements" data-query-key="orderBy[cache.softwareAgent.cf]" data-toggle="tooltip" data-placement="top" title="Number of judgements the unit got on CrowdFlower"># CF judgements</th>
                    <th class="sorting whiteSpaceNormal" data-vbIdentifier="number_of_amt_judgements" data-query-key="orderBy[cache.softwareAgent.amt]" data-toggle="tooltip" data-placement="top" title="Number of judgements the unit got on AMT"># AMT judgements</th>
                    <th class="sorting whiteSpaceNormal" data-vbIdentifier="number_of_children" data-query-key="orderBy[cache.children.count]" data-toggle="tooltip" data-placement="top" title="The number of units generated from this unit"># of children</th>
                    <th class="sorting whiteSpaceNormal" data-vbIdentifier="parents"  data-toggle="tooltip" data-placement="top" title="The units from which this media unit was created">parents</th>
                    <th class="sorting whiteSpaceNoWrap" data-vbIdentifier="created_at" data-query-key="orderBy[created_at]" style="min-width:220px; width:auto;">Created At</th>
                    <th class="sorting" data-vbIdentifier="created_by" data-query-key="orderBy[user_id]">Created By</th>
		        </tr>
			<tr class="inputFilters">
				<td data-vbIdentifier="checkbox">
					<input type="checkbox" class="checkAll" />
				</td>
				<td data-vbIdentifier="video_identifier">
					<input class="input-sm form-control" type='text' data-query-key="match[content.identifier]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="video_content">
					<input class="input-sm form-control" type='text' data-query-key="match[content.identifier]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="video_name">
					<input class="input-sm form-control" type='text' data-query-key="match[content.videoName]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="video_source">
					<input class="input-sm form-control" type='text' data-query-key="match[source]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="video_title">
					<input class="input-sm form-control" type='text' data-query-key="match[content.metadata.title.nl]" data-query-operator="like" />	
				</td>
				<td data-vbIdentifier="video_subject">
					<input class="input-sm form-control" type='text' data-query-key="match[content.metadata.subject.nl]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="number_of_video_keyframes">
					<input class="input-sm form-control" type='text' data-query-key="match[keyframes.count]" data-query-operator=">" style="width:49%; float:left;" placeholder="gt" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[keyframes]" data-query-operator="<" style="width:49%; float:right;" placeholder="lt" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="number_of_video_segments">
					<input class="input-sm form-control" type='text' data-query-key="match[segments.count]" data-query-operator=">" style="width:49%; float:left;" placeholder="gt" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[segments.count]" data-query-operator="<" style="width:49%; float:right;" placeholder="lt" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="video_description">
					<input class="input-sm form-control" type='text' data-query-key="match[content.metadata.description.nl]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="video_abstract">
					<input class="input-sm form-control" type='text' data-query-key="match[content.metadata.abstract.nl]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="video_date">
					<div class="input-daterange" id="datepicker" data-date-start-view="2" data-date-format="yyyy" data-date-autoclose="true" data-date-min-view-mode="2">
						<input type="text" class="input-sm form-control" name="start" data-query-key="match[content.metadata.date]" data-query-operator=">=" style="width:49% !important; float:left;" placeholder="Start Date" />
						<input type="text" class="input-sm form-control" name="end" data-query-key="match[content.metadata.date]" data-query-operator="=<" style="width:49% !important; float:right;" placeholder="End Date" />
					</div>
				</td>
				<td data-vbIdentifier="video_duration">
					<input class="input-sm form-control" type='text' data-query-key="match[content.metadata.extent]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="video_language">
					<input class="input-sm form-control" type='text' data-query-key="match[content.metadata.language]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="video_spatial">
					<input class="input-sm form-control" type='text' data-query-key="match[content.metadata.spatial.nl]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="number_of_batches">
					<input class="input-sm form-control" type='text' data-query-key="match[cache.batches.count]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[cache.batches.count]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="number_of_jobs">
					<input class="input-sm form-control" type='text' data-query-key="match[cache.jobs.count]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[cache.jobs.count]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
                <td data-vbIdentifier="number_of_cf_judgements">
                    <input class="input-sm form-control" type='text' data-query-key="match[cache.softwareAgent.cf]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
                    <input class="input-sm form-control" type='text' data-query-key="match[cache.softwareAgent.cf]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
                </td>
                <td data-vbIdentifier="number_of_amt_judgements">
                    <input class="input-sm form-control" type='text' data-query-key="match[cache.softwareAgent.amt]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
                    <input class="input-sm form-control" type='text' data-query-key="match[cache.softwareAgent.amt]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
                </td>
                <td data-vbIdentifier="number_of_children">
                    <input class="input-sm form-control" type='text' data-query-key="match[cache.children.count]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
                    <input class="input-sm form-control" type='text' data-query-key="match[cache.children.count]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
                </td>
                <td data-vbIdentifier="parents">
                    <input class="input-sm form-control" type='text' data-query-key="match[parents][]" />
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
				    <td data-vbIdentifier="video_identifier"> 
					<a class='testModal' id='@{{ this._id }}' data-modal-query="unit=@{{this._id}}" data-api-target="{{ URL::to('api/analytics/unit?') }}" data-target="#modalIndividualFullvideo" data-toggle="tooltip" data-placement="top" title="Click to see the individual unit page">
						@{{ this.content.identifier }}
					</a>
				    </td>
			            <td data-vbIdentifier="video_content">
					<video width="240" height="160" controls preload="none" poster="@{{ this.content.metadata.medium.thumbnail }}"  data-toggle="tooltip" data-placement="top" title="Click to play">
						
	  					<source src="@{{ this.content.storage_url }}" type="video/mp4" >
						<source src="@{{ this.content.storage_url }}" type="video/ogg" >
							Your browser does not support the video tag.
						</source>
					</video>
				    </td>
			            <td data-vbIdentifier="video_name" ><a href="@{{ this.content.metadata.attributionURL }}" target="_blank" data-toggle="tooltip" data-placement="top" title="Watch the video on the original source page"> @{{ this.content.videoName }}</a></td>
			            <td data-vbIdentifier="video_source">@{{ this.source }}</td>
			            <td data-vbIdentifier="video_title">@{{ highlightTermsInVideoTitle ../searchQuery this.content.metadata.title.nl }}</td>
				    <td data-vbIdentifier="video_subject" >@{{ highlightTermsInVideoSubject ../searchQuery this.content.metadata.subject.nl }}</td>
				    <td data-vbIdentifier="number_of_video_keyframes" id="keyframe_@{{ @index }}">
					<a class='testModal' data-modal-query="&only[]=content.storage_url&only[]=content.timestamp&match[documentType]=keyframe&match[parents][]=@{{ this._id }}" data-api-target="{{ URL::to('api/search?noCache') }}" data-target="#modalTemplateKeyframes" data-toggle="tooltip" data-placement="top" title="Click to see the keyframes">
						@{{ this.keyframes.count }}
					</a>
				    </td>
				    <td data-vbIdentifier="number_of_video_segments" id="segment_@{{ @index }}">
				    <a class='testModal' data-modal-query="&only[]=content.storage_url&only[]=content.duration&match[documentType]=videosegment&only[]=content.start_time&only[]=content.end_time&match[parents][]=@{{ this._id }}" data-api-target="{{ URL::to('api/search?noCache') }}" data-target="#modalTemplateSegments" data-toggle="tooltip" data-placement="top" title="Click to see the video segments">
						@{{ this.segments.count }}
					</a>
				    </td>
			            <td data-vbIdentifier="video_description" >@{{ this.content.metadata.description.nl }}</td>
				    <td data-vbIdentifier="video_abstract">@{{ highlightTermsInVideoAbstract ../searchQuery this.content.metadata.abstract.nl }}</td>
				    <td data-vbIdentifier="video_date" >@{{ this.content.metadata.date }}</td>
	      			    <td data-vbIdentifier="video_duration"> @{{ this.content.metadata.extent }}	</td>
				    <td data-vbIdentifier="video_language">@{{ this.content.metadata.language }}</td>
				    <td data-vbIdentifier="video_spatial" >@{{ this.content.metadata.spatial.nl }}</td>
				    <td data-vbIdentifier="number_of_batches">@{{ this.cache.batches.count }}</td>
				    <td data-vbIdentifier="number_of_jobs">@{{ this.cache.jobs.count }}</td>
                    <td data-vbIdentifier="number_of_cf_judgements">@{{ this.cache.softwareAgent.cf }}</td>
                    <td data-vbIdentifier="number_of_amt_judgements">@{{ this.cache.softwareAgent.amt }}</td>
                    <td data-vbIdentifier="number_of_children">@{{ this.cache.children.count }}</td>
                    <td data-vbIdentifier="parents">@{{ this.parents }}</td>
				    <td data-vbIdentifier="created_at">@{{ this.created_at }}</td>
				    <td data-vbIdentifier="created_by">@{{ this.user_id }}</td>
				</tr>
			        @{{/each}}
				</script>
	        </tbody>
	    </table>
	</div>	    
	<div class='hidden modalTemplate'>
		<table>
		<script class='template' type="text/x-handlebars-template">
				<!-- Modal -->
				<div class="modal bs-example-modal-lg fade" id="activeTabModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabelWorker" aria-hidden="true">
				  <div class="modal-dialog modal-lg">
				    <div class="modal-content">
				      <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabelWorker">Individual worker page</h4>
				      </div>
				      <div class="modal-body" >
					@{{#each documents}}
						@{{ this._id }}
					@{{/each}}				
				      </div>
				    </div>
				  </div>
				</div>
		</script>
		</table>
	</div>			


	<div class='hidden' id='modalTemplateSegments'>
		<script class='template' type="text/x-handlebars-template">
			<!-- Modal -->
			<div class="modal fade" id="activeTabModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabelWorker" aria-hidden="true">
				<div class="modal-dialog ">
				    <div class="modal-content">
				      <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabelWorker">Segments of the video</h4>
				      </div>
				      <div class="modal-body" >
					<table>
					@{{#each documents}}
						<tr>
							<td>
								<video width="240" height="160" controls preload="none">
									<source src="@{{ this.content.storage_url }}" type="video/mp4" >
									<source src="@{{ this.content.storage_url }}" type="video/ogg" >
										Your browser does not support the video tag.
									</source>
								</video>
							</td>
							<td align="left">
								<strong>Duration</strong>: @{{ this.content.duration }} <br />
								<strong>Start Time</strong>: @{{ this.content.start_time }} <br />
								<strong>End Time</strong>: @{{ this.content.end_time }} <br />
							</td>
						</tr>
					@{{/each}}
					</table>					
				      </div>
				    </div>
				  </div>
				</div>
		</script>
		
	</div>	
	<div class='hidden' id='modalTemplateKeyframes'>

		<script class='template' type="text/x-handlebars-template">
				<!-- Modal -->
				<div class="modal fade" id="activeTabModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabelWorker" aria-hidden="true">
				  <div class="modal-dialog ">
				    <div class="modal-content">
				      <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabelWorker">Keyframes of the video</h4>
				      </div>
				      <div class="modal-body" >
					<table>
					@{{#each documents}}
						<tr>
							<td><img src="@{{ this.content.storage_url }}" width="240" height="160" rel="tooltip" title="Timestamp: @{{ this.content.timestamp }} &#xA; Source name: @{{ this.content.storage_url }} "/></td>
							<td><strong>Timestamp</strong>: @{{ this.content.timestamp }}</td>
						</tr>
					@{{/each}}
					</table>					
				      </div>
				    </div>
				  </div>
				</div>
		</script>

	</div>	
	
	<div class='hidden' id='modalIndividualFullvideo'>
		<script class='template' type="text/x-handlebars-template">
		<!-- Modal -->
		<div class="modal fade bs-example-modal-lg" id="activeTabModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabelVideoUnit" aria-hidden="true">
		 <div class="modal-dialog modal-lg">
		  <div class="modal-content">
		   <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		     <h4 class="modal-title" id="myModalLabelVideoUnit">Individual @{{#addDocumentTypeLabel this.infoStat.documentType}} @{{/addDocumentTypeLabel}} Page</h4>
		   </div>
		   <div class="modal-body" >
			<div>
			<video width="240" height="160" controls preload="none" poster="@{{ this.infoStat.content.metadata.medium.thumbnail }}"  data-toggle="tooltip" data-placement="top" title="Click to play">
				<source src="@{{ this.infoStat.content.storage_url }}" type="video/mp4" >
				<source src="@{{ this.infoStat.content.storage_url }}" type="video/ogg" >
				Your browser does not support the video tag.
				</source>
			</video>
			<div style="float:right;"><strong> Title: </strong> 
					    <ul>
						  <li> NL: @{{ this.infoStat.content.metadata.title.nl }} </li>
						  <li> EN: @{{ this.infoStat.content.metadata.title.en }} </li>
					    </ul>
					   <strong> Subject: </strong> 
					    <ul>
					     <li> NL: @{{ this.infoStat.content.metadata.subject.nl }} </li>
					     <li> EN: @{{ this.infoStat.content.metadata.subject.en }} </li>
					    </ul>
					    <strong> Date: </strong> @{{ this.infoStat.content.metadata.date }} 
					    <strong> Duration: </strong> @{{ this.infoStat.content.metadata.extent }} 
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
					   <div> <strong> Creator: </strong> @{{ this.infoStat.content.metadata.creator.nl }} </div>   
					   <div><strong> Online URL: </strong> <a href="@{{ this.content.metadata.attributionURL }}" target="_blank" data-toggle="tooltip" data-placement="top" title="Watch the video on the original source page"> @{{ this.infoStat.content.metadata.attributionURL }} </a></div>
					  <div><strong> Abstract: </strong> 
					   <ul>
					    <li> NL: @{{ this.infoStat.content.metadata.abstract.nl }} </li>
					    <li> EN: @{{ this.infoStat.content.metadata.abstract.en }} </li>
					   </ul>
					  </div>
					  <div><strong> Language: </strong> @{{ this.infoStat.content.metadata.language }} </div>
					  <div><strong> Location: </strong> @{{ this.infoStat.content.metadata.spatial }} </div>
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
   						  <th class="header"  data-toggle="tooltip" data-placement="top" title="Sentence Clarity: the value is defined as the maximum sentence workerunit score achieved on any workerunit for that relex-structured-sentence. High agreement over the annotations is represented by high cosine scores, indicating a clear sentence."> @{{#addDocumentTypeLabel this.infoStat.documentType}} @{{/addDocumentTypeLabel}} Clarity</th>
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
						   <td> @{{ toFixed avg.max_relation_Cos.avg 2}} </td>
						   <td> @{{ toFixed avg.no_annotators.avg 2}} </td>
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
						  @{{#each this.workerunitContent}}
						   @{{#each workerunitType}}
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
