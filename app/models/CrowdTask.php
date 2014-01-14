<?php

//use Jenssegers\Mongodb\Model as Eloquent;

class CrowdTask extends Moloquent {
	protected $fillable = array('title', 'description', 'keywords');
    


	public static $rules = array(
	  'title' => 'required',
	);

	public static function getFromHit($hit){

		return new CrowdTask(array(
			'title' 		=> $hit->getTitle(),
			'description' 	=> $hit->getDescription(),
			'keywords'		=> $hit->getKeywords()

			));
	}

}

?>