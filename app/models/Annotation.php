<?php




// Probably not gonna use this yet.





use mongo\text\sentence;
use mongo\text\entity;

class Annotation extends Entity {

	protected $platform_id;
	protected $data;

	public function __construct($jobentity, $platform, $data){

	}

	public function createCrowdAgent($platform, $data){

		$workerid = '';
		if($platform == 'amt') {
			$workerId = $data['WorkerId'];
		} else {
			throw new Exception("Unknown platform $platform");
			// CF is not (yet?) needed here -> webhook.
		}	

		if($id = CrowdAgent::where('platformAgentId', $workerId)->where('platform_id', $platform)->pluck('_id')) 
			return $id;

		else {
			$agent = new CrowdAgent;
			$agent->_id= "/crowdagent/$platform/$workerId";
			$agent->used = 'todo. UnitId?';
			$agent->platform_id= $platform;
			$agent->platformAgentId = $workerId;
			$agent->save();
			
			return $agent->_id;
		}

	}
}

?>