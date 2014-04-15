@extends('layouts.default')

@section('head')
<link rel="stylesheet" type="text/css" href="/custom_assets/custom.css"></link>
<script type="text/javascript" src="/custom_assets/crowdwatson.js"></script>
@stop

@section('content')
<div class="messagePage" ng-controller="messageCtrl">
  <!-- Main column with results -->
   <div class="row">
    
      <div class="ng-scope disabled pull-left">
        <ul style="margin-left: 20px;" class="pagination ng-isolate-scope">
          <li><a ng-click="gotoOverview()">Show all workers</a></li>
        </ul>
      </div>
  </div>

  <div class="messageContainer">
    <div class="modal-header">
            <h3>Flag a worker</h3>
    </div>
    <div class="modal-header">
      <div class="row leftindent">
        <div class="col-md-3">Worker:</div>
        <div class="col-md-9">@{{flagselected}}</div> 
      </div>
      <div class="row leftindent" style="margin-top: 8px;">
        <div class="col-md-3">Subject:</div>
        <div class="col-md-9"><input class="messagesubject" type="text" ng-model="message.subject"></div> 
      </div>
      
    </div>
    <div class="modal-body">
      <h4>Message:</h4>
      <div>
        <select class="messageselect" ng-model="message" ng-options="m.title for m in flagtemplates"></select><br>
        <textarea class="messagebox" ng-model="message.content"></textarea>
      </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-danger" ng-click="showPrevious()">Cancel</button>
        <button class="btn btn-primary" ng-click="flagWorker()">Flag</button>
    </div>
  </div>  
</div>
@stop

@section('end_javascript')
  <script>
         
  </script>
@stop