<?php

//use Jenssegers\Mongodb\Model as Eloquent;

class CrowdTask extends Moloquent {
	protected $fillable = array('title', 'description', 'keywords', 'template', 'reward', 'maxAssignments', 'assignmentDur');
    


	public static $rules = array(
	  'title' => 'required',
	  'desciprtion' => 'required',
	  'reward' => 'required|numeric',
	  'maxAssignments' => 'required|numeric'
	);

	public function getFromHit($hit){

		return new CrowdTask(array(
			'title' 		=> $hit->getTitle(),
			'description' 	=> $hit->getDescription(),
			'keywords'		=> $hit->getKeywords(),
			'reward'		=> $hit->getReward()['Amount'],
			'maxAssignments'=> $hit->getMaxAssignments(),
			'assignmentDur'	=> $hit->getAssignmentDurationInSeconds()
			));
	}

}

?>