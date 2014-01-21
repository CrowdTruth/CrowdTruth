@extends('files.view.text.pages.entity')

@section('dropdownActions')
<div class="btn-group">
  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
    Actions <span class="caret"></span>
  </button>
  <ul class="dropdown-menu pull-right" role="menu">
    <li><a href='#' class='check_filtered_sentences'><i class="fa fa-check-square fa-fw"></i>Check filtered sentences</a></li>

    <li><a href='#' id='create_new_changDocument'><i class='fa fa-files-o fa-fw'></i>Create new document with filtered sentences</a></li>

<!--     <li><a href='{{ URL::to('files/delete?URI=' . $entity['_id']) }}'><i class='fa fa-trash-o fa-fw'></i>Delete document</a></li>
 -->  </ul>
</div>
@stop

@section('entityContent')

@include('files.view.text.layouts.chang_content', array('entity' => $entity, 'lines' => $entity->content))

@stop