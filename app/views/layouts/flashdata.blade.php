@if (Session::has('flashNotice'))
<div class="alert alert-warning">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
   	<strong><i class="fa fa-exclamation-triangle fa-fw"></i>Notice</strong>
    <hr class="message-inner-separator">
	@if (is_array(Session::get('flashNotice')))
		@foreach (Session::get('flashNotice') as $status)
			<p>{{ $status }} </p>
		@endforeach
	@else
		<p>{{ Session::get('flashNotice'); }}</p>
	@endif
</div>
@endif
@if (Session::has('flashError'))
<div class="alert alert-danger">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
   	<strong><i class="fa fa-exclamation-triangle fa-fw"></i>Error!</strong>
    <hr class="message-inner-separator">
	@if (is_array(Session::get('flashError')))
		@foreach (Session::get('flashError') as $status)
			<p>{{ $status }} </p>
		@endforeach
	@else
		<p>{{ Session::get('flashError'); }}</p>
	@endif
</div>
@endif
@if (Session::has('flashSuccess'))
<div class="alert alert-success">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
   	<strong><i class="fa fa-check fa-fw"></i>Success!</strong>
    <hr class="message-inner-separator">
	@if (is_array(Session::get('flashSuccess')))
		@foreach (Session::get('flashSuccess') as $status)
			<p>{{ $status }} </p>
		@endforeach
	@else
		<p>{{ Session::get('flashSuccess'); }}</p>
	@endif
</div>
@endif