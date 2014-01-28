<?php

use crowdwatson\MechanicalTurkService;

class PostProcessController extends BaseController {

	public function getIndex() {
	   return Redirect::to('postprocess/listview');
	}

	public function getListview() {
		$ct = PostProcessController::newCTfromTemplate('relation_direction/relation_direction_1');
		$ct->name = 'mock';
		$ct->save();
		$crowdtasks = CrowdTask::all();
	   	return View::make('postprocess.listview')->with('crowdtasks', $crowdtasks);	
	}
	
	public function getTableview() {
	   $crowdtasks = CrowdTask::all();
	   return View::make('postprocess.tableview');
	}

	private function newCTfromTemplate($template){
		try {
			// Currently, the HIT format is used.
			$turk = new MechanicalTurkService(base_path() . '/public/templates/');
			$hit = $turk->hitFromTemplate($template);
			$ct = CrowdTask::getFromHit($hit);
			$ct->template = $template;
			return $ct;
		} catch (AMTException $e){
			Session::flash('flashError', $e->getMessage());
			return new CrowdTask;
		}
	}	
}
