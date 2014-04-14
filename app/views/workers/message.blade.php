@extends('layouts.default')

@section('head')
<script type="text/javascript" src="/custom_assets/crowdwatson.js"></script>  
<script type="text/javascript" src="/custom_assets/angular-moment.js"></script>
<script type="text/javascript" src="/custom_assets/moment.js"></script>
<link rel="stylesheet" type="text/css" href="/custom_assets/custom.css"></link>
<script type="text/javascript" src="/custom_assets/angular-ui.js"></script>
@stop


@section('content')

  <!-- Main column with results -->
   <div ng-controller="messageCtrl">
     <div class="row">
    
      <div class="ng-scope disabled pull-left">
        <ul style="margin-left: 20px;" class="pagination ng-isolate-scope">
          <li><a ng-click="gotoOverview()">Show all workers</a></li>
        </ul>
      </div>
  </div>

  <div class="messageContainer">

      <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h3>Send a message</h3>
      </div>
      <div>
        <div class="row leftindent">
          <div class="col-md-3">To:</div>
          <div class="col-md-9"><b>@{{selection}}</b></div> 
        </div>
        <div class="row leftindent">
          <div class="col-md-3">Subject:</div>
          <div class="col-md-9"><input class="messagesubject" type="text" ng-model="message.subject"></div> 
        </div>
        
      </div>
      <div class="modal-body">
        <h4>Message:</h4>
        <div>
          <select class="messageselect" ng-model="message" ng-options="m.title for m in messagetemplates"></select><br>
          <textarea class="messagebox" ng-model="message.content"></textarea>
        </div>
      </div>
      <div class="modal-footer">
          <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
          <button class="btn btn-primary" ng-click="modal.close()">Send</button>
      </div>
    </div>
  </div>  


@stop
