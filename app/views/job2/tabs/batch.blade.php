@extends('layouts.default_new')

@section('container')

@section('head')
{{ stylesheet_link_tag('bootstrap-select.css') }}
{{ stylesheet_link_tag('bootstrap-dropdown-checkbox.css') }}
{{ stylesheet_link_tag('bootstrap.datepicker3.css') }}
{{ stylesheet_link_tag('custom.css') }}

<style>
.container {
	-webkit-transform:translatez(0);-webkit-backface-visibility:hidden;-webkit-perspective:1000;
}
</style>
@stop

@section('content')
<!-- START search_content -->
<div class="col-xs-10 col-md-offset-1">
	<div class='maincolumn CW_box_style'>

		<div class='tab'>
			@include('job2.nav')
			@include('layouts.flashdata')
			<div>
				<div class="panel panel-default">		
					<div class="panel-heading">
						<h4>Select Batch</h4>
					</div>
					<div class="panel-body">					
							<div>
								<div class='searchStats pull-left'>
								</div>
								<div class='cw_pagination pull-right'>
								</div>
							</div>
								{{ Form::open(array('class' => 'form-horizontal jobconf', 'action' => array('JobsController2@postFormPart', 'submit'), 'method' => 'POST'))}}	
								<div class="tab-content documentTypesTabs">
									<div class="tab-pane active ctable-responsive" id="all_tab">
										<table class="table table-striped">
											<thead data-query-key="match[documentType]" data-query-value="batch">
												<tr>
													<th></th>
													<th class="sorting" data-query-key="orderBy[title]">Title</th>
													<th class="sorting" data-query-key="orderBy[content]">Content</th>
													<th>Size</th>
													<th class="sorting" data-query-key="orderBy[format]">Format</th>
													<th class="sorting" data-query-key="orderBy[domain]">Domain</th>
													<th class="sorting whiteSpaceNoWrap" data-query-key="orderBy[user_id]">Created By</th>
												</tr>
												<tr class="inputFilters">
													<td style='min-width:10px;'></td>
													<td data-vbIdentifier="title">
														<input class="input-sm form-control" type='text' data-query-key="match[title]" data-query-operator="like"  placeholder="Enter your search keywords here" />
													</td>
													<td>
														<input class="input-sm form-control" type='text' data-query-key="match[content]" data-query-operator="like" />
													</td>
													<td style='min-width:40px;'>
													</td>
													<td>
														<?php 
														$format = Session::get('format');
														if (empty($format)){

															echo '<input class="input-sm form-control" type="text" data-query-key="match[format]" data-query-operator="like" />';
														}
														else
														{

															echo '<input class="input-sm form-control" type="text" data-query-key="match[format]" data-query-value="'.$format.'" />';
														}
														?>
													</td>
													<td>
														<input class="input-sm form-control" type='text' data-query-key="match[domain]" data-query-operator="like" />
													</td>
													<td>
														<input class="input-sm form-control" type='text' data-query-key="match[user_id]" data-query-operator="like" />
													</td>
												</tr>
											</thead>
											<tbody class='results'>
												<script class='template' type="text/x-handlebars-template">
													@{{#each documents}}
													<tr class='selectrow'>
														<td><input type='radio' name='batch' value='@{{ this._id}}' /></td>
														<td class='text-left double-title' data-vbIdentifier="title"><strong>@{{ this.title }}</strong><div class='list-content-small'>@{{ this._id }}</div></td>
														<td class='text-left' data-vbIdentifier="content">@{{ this.content }}</td>
														<td data-vbIdentifier="size">@{{ this.parents.length }}</td>
														<td data-vbIdentifier="format">@{{ this.format }}</td>
														<td data-vbIdentifier="domain">@{{ this.domain }}</td>
														<td data-vbIdentifier="user_id">@{{ highlightSelf this.user_id }} </td>
													</tr>
													@{{/each}}
												</script>
											</tbody>
										</table>
									</div>
									<script class='searchStatsTemplate' type="text/x-handlebars-template">
										Showing @{{ count.from }} to @{{ count.to }} of @{{ count.total}} entries
									</script>
								</div>
								{{ Form::submit('Next', array('class' => 'btn btn-lg btn-primary pull-right')); }}
								{{ Form::close()}}	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- STOP search_content -->
@stop