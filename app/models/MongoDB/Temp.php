<?php

namespace MongoDB;

use Moloquent;

class Temp extends Moloquent {

	protected $collection = 'temp';
	protected static $unguarded = true;
    public static $snakeAttributes = false;

	public static function createImageCache()
	{
		\MongoDB\Temp::whereIn('documentType', ['painting', 'drawing', 'picture'])->forceDelete();

        \Session::flash('rawArray', 1);
        $db = \DB::getMongoDB();
        $db = $db->temp;

		$result = \MongoDB\Entity::whereIn('documentType', ['painting', 'drawing', 'picture'])->get()->toArray();

		if(count($result) > 0)
		{

			foreach($result as &$parent)
			{
				$children = \MongoDB\Entity::whereIn('parents', [$parent['_id']])->get(['content.features'])->toArray();

				$parent['content']['features'] = [];
				foreach($children as $child){
					$featureKey = key($child['content']['features']);

					if(!isset($parent['content']['features'][$featureKey]))
							$parent['content']['features'][$featureKey] = [];

					array_push($parent['content']['features'][$featureKey], $child['content']['features'][$featureKey]);
				}
			}

	        try {
	            $db->batchInsert(
	                $result,
	                array('continueOnError' => true)
	            );             
	        } catch (Exception $e) {
	        // ContinueOnError will still throw an exception on duplication, even though it continues, so we just move on.
	        }
			
		}

        \Session::forget('rawArray');
    }		
}