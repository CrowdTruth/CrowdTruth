@extends('layouts.default')
@section('head')
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.13/angular.min.js"></script>
<!-- <script type="text/javascript" src="http://code.angularjs.org/1.2.9/angular.js"></script>
 --><script type="text/javascript" src="/custom_assets/angular-form-builder/angular-form-builder.js"></script>
<script type="text/javascript" src="/custom_assets/angular-form-builder/angular-form-builder-components.js"></script>
<script type="text/javascript" src="/custom_assets/angular-form-builder/angular-validator.min.js"></script>
<!-- basic rules (not required) -->
<script type="text/javascript" src="/custom_assets/angular-form-builder/angular-validator-rules.js"></script>
<link type="text/css" rel="stylesheet" href="/custom_assets/angular-form-builder/angular-form-builder.css"/>
<script type="text/javascript" src="/custom_assets/ui-bootstrap-tpls-0.10.0.min.js"></script>
<script type="text/javascript" src="/custom_assets/angular-form-builder/demo.js"></script>
<script type="text/javascript" src="/custom_assets/src-min-noconflict/ace.js"></script>
<script type="text/javascript" src="/custom_assets/ui-ace.min.js"></script>
@stop

@section('content')
<div ng-app="templatebuilder">
<style>.ace_editor { min-height: 650px; } .tab-content{min-height: 650px;}</style>
		<div class="row maincolumn CW_box_style" ng-controller="templatebuilderCtrl" style="padding:20px">
		<div class="row">


					<div class="page-header text-center" style="margin:10px;">
						<h2>Template Builder</h2>
					</div>

					<div class="col-md-6">
						<!-- Show builder when tab = components  -->
						<div class="panel panel-default" ng-show="tab=='components'">
							<div class="panel-heading">
								<h3 class="panel-title">Builder</h3>
							</div>
							<div fb-builder="default" style="min-height:100px; padding:10px"></div>
							<div class="panel-footer">
								<div class="checkbox">
									<label><input type="checkbox" ng-model="isShowScope" />
										Show scope
									</label>
								</div>
								<pre ng-if="isShowScope">@{{form}}</pre>
							</div>
						</div>
					

						<!-- Show result when tab = js or css  -->
						<div class="panel panel-default" ng-show="tab!='components'">
							<div class="panel-heading">
								<h3 class="panel-title">Result</h3>
							</div>
							<div style="min-height:100px; padding:20px">
								<div class="csssandbox">
									<style scoped ng-bind-html="css"></style>
									<form class="form-horizontal">
										<div ng-model="input" fb-form="default" fb-default="defaultValue"></div>
										<div class="form-group">
											<div class="col-md-8 col-md-offset-4">
												<input type="submit" ng-click="submit()" class="btn btn-default"/>
											</div>
										</div>
									</form>
								</div>
							</div>
						<div class="panel-footer">
							<div class="checkbox">
								<label><input type="checkbox" ng-model="isShowScope" />
									Show scope
								</label>
							</div>
							<pre ng-if="isShowScope">@{{input}}</pre>
						</div>
					</div>
					</div>
					<div class="col-md-6">
						<div class="panel panel-default">
							<tabset justified="true">
								<tab heading="Components" select="changeTab('components')" active>
									<div fb-components></div>
								</tab>
								<tab heading="CSS" select="changeTab('css')">
									<div ui-ace="{
									useWrapMode : true, mode: 'css', onLoad:cssLoaded, onChange:cssChanged}" ng-model="uncleancss"></div>
								</tab><!-- theme:'twilight', -->

								<tab heading="JS" select="changeTab('js')">
									<div ui-ace="{
									useWrapMode : true, mode: 'javascript', onLoad:jsLoaded}"></div></tab>
								</tabset>
							</div>
						</div>




</div>
<div class="row">
									---
									<iframe ng-attr-srcdoc="@{{ html }}"></iframe>
									---
</div>
@stop

@section('end_javascript')
<script type="text/javascript" src="/custom_assets/jquery.scoped.js"></script>
<script>

/*$(document).ready(function(){
	$('.csssandbox').mouseup(function(){$.scoped();});
});*/
</script>

@stop
