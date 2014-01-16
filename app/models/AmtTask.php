<?php

//use Jenssegers\Mongodb\Model as Eloquent;

class AmtTask extends CrowdTask {
	protected $fillable = array('title', 'description', 'keywords', 'template', 'autoApprovalDelayInSeconds', 'qualificationRequirement', 'requesterAnnotation' ,'assignmentReviewPolicy');
  	
  	public static $rules = array(
  		);

  	public static function getFromHit($hit){

		return new AmtTask(array(
			'title' 		=> $hit->getTitle(),
			'description' 	=> $hit->getDescription(),
			'keywords'		=> $hit->getKeywords(),
			'reward'		=> $hit->getReward()['Amount'],
			'maxAssignments'=> $hit->getMaxAssignments(),
			'assignmentDur'	=> $hit->getAssignmentDurationInSeconds(),
			'autoApprovalDelayInSeconds' => $hit->getAutoApprovalDelayInSeconds(),
			'qualificationRequirement'=> $hit->getQualificationRequirement(),
			'assignmentReviewPolicy' => $hit->getAssignmentReviewPolicy()
			));
	}
?>