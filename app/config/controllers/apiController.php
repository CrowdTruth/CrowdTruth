<?php

class apiController extends BaseController {

	protected $repository;

	public function __construct(\mongo\Repository $repository){
		$this->repository = $repository;
	}

	public function getIndex(){
		if(!$type = Input::get('type'))
			$type = "text";

		if(!$provtype = Input::get('provtype'))
			$provtype = "entity";
			
		$Collection = $this->repository->returnCollectionObjectFor($type, $provtype);
		$query = $Collection;

		if($domain = Input::get('domain'))
			$query = $Collection::where('domain', $domain);

		foreach($query->get() as $item){
			$item['content'] = null;
			print_r($item->getAttributes());
		}


	}

}