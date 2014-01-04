@if (Session::has('flashNotice'))
<div class="alert alert-warning">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
   	<strong><i class="fa fa-exclamation-triangle fa-fw"></i>Notice</strong>
    <hr class="message-inner-separator">
    <p>{{ Session::get('flashNotice'); }}</p>
</div>
@endif
@if (Session::has('flashError'))
<div class="alert alert-danger">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
   	<strong><i class="fa fa-exclamation-triangle fa-fw"></i>Error!</strong>
    <hr class="message-inner-separator">
    <p>{{ Session::get('flashError'); }}</p>
</div>
@endif
@if (Session::has('flashSuccess'))
<div class="alert alert-success">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
   	<strong><i class="fa fa-check fa-fw"></i>Succes!</strong>
    <hr class="message-inner-separator">
    <p>{{ Session::get('flashSuccess'); }}</p>
</div>
@endif