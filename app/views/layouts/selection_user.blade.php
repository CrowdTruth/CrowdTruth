<div class="navbar-form navbar-right">
	<a href='{{ URL::to('selection')}}' class="btn btn-info selectionButton {{  (Request::is('selection') ? 'active' : '') }}"><i class="fa fa-shopping-cart fa-fw"></i>Selection</a>
    

<div class="btn-group">
    <button type="button" class="btn userButton dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user fa-fw"></i>{{ Auth::user()->firstname }}
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu pull-right" role="menu">
        <li><a href="#"><i class="fa fa-folder-open fa-fw"></i>My activity</a></li>
        <li><a href="{{ URL::to('/user/logout') }}"><i class="fa fa-sign-out fa-fw"></i>Log out</a></li>
    </ul>
</div>

    <div class='selectionContent' style='display:none;'>
        @include('selection.inline_menu')
    </div>
</div>

@section('selection_user_javascript')
	<script type="text/javascript">
		$(document).ready(function () {
            $(".selectionButton").popover({
                trigger: "manual",
                html: true,
                'animation' : false,
                'content' : function(){ return $('.selectionContent').html() },
                'placement' : 'bottom',
                 template: '<div class="popover selectionPopover"><div class="arrow"></div><div class="popover-content"></div></div>'
            })
                .on("mouseenter", function () {
                    var _this = this;
                    $(this).popover("show");
                    $(".popover").on("mouseleave", function () {
                        $(_this).popover('hide');
                    });
                }).on("mouseleave", function () {
                    var _this = this;
                    setTimeout(function () {
                        if (!$(".popover:hover").length) {
                            $(_this).popover("hide");
                        }
                    }, 100);
                });              
		});
	</script>
@stop