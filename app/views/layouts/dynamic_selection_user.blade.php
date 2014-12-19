<div class="navbar-form navbar-right">
    @yield('dynamicButton')
    @if(Auth::check())
    <div class="btn-group">
		<a href='{{ URL::to('media/upload') }}' class="{{ (Request::is('media/upload') ? 'active' : '') }} btn btn-default"><i class="fa fa-upload fa-fw"></i> Upload Media</a>
    </div>
    <div class="btn-group">
        <button type="button" class="btn userButton dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user fa-fw"></i> {{ Auth::user()->firstname }}
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" role="menu">
            <li role="presentation"><a href="{{ URL::to('/user/' . Auth::user()->_id) }}"><i class="fa fa-user fa-fw"></i> Profile</a></li>
			<li role="presentation"><a href="{{ URL::to('/user/' . Auth::user()->_id . '/activity') }}"><i class="fa fa-bar-chart fa-fw"></i> Activity</a></li>
            <li role="presentation"><a href="{{ URL::to('/user/' . Auth::user()->_id . '/settings') }}"><i class="fa fa-gears fa-fw"></i> Settings</a></li>
			<li role="presentation" class="divider"></li>
			<li role="presentation" class="dropdown-header">Administration</li>
			<li role="presentation"><a href="{{ URL::to('/users') }}"><i class="fa fa-list-ol fa-fw"></i> Users</a></li>
			<li role="presentation" class="disabled"><a href="{{ URL::to('/groups') }}"><i class="fa fa-list-ol fa-fw"></i> Groups</a></li>
			<li role="presentation" class="divider"></li>
            <li role="presentation"><a href="{{ URL::to('logout') }}"><i class="fa fa-sign-out fa-fw"></i> Log out</a></li>
        </ul> 
    </div>
    @else
       {{ link_to('login', "Log in", array("class" => "btn btn-primary")) }}
    @endif
</div>