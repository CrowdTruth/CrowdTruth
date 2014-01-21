<div class="btn-group">
	  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
	    Actions <span class="caret"></span>
	  </button>
	  <ul class="dropdown-menu pull-right" role="menu">
	    <li><a href='{{ URL::to('selection/add?' . $entity['_id']) }}' class='update_selection'><i class="fa fa-plus-circle fa-fw"></i>Add to selection</a></li>
	    <li><a href='{{ URL::to('files/delete?' . $entity['_id']) }}'><i class='fa fa-trash-o fa-fw'></i>Delete document</a></li>
	  </ul>
</div>