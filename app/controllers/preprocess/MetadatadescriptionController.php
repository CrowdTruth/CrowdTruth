<?php

namespace preprocess;


use \preprocess\MetadatadescriptionStructurer as MetadatadescriptionStructurer;
use BaseController, Cart, View, App, Input, Redirect, Session;
use \Repository as Repository;
use \Entity as Entity;
use \Entities\Unit as Unit;

class MetadatadescriptionController extends BaseController {

	protected $repository;
	protected $metadataAnnotationStructurer;

	public function __construct(Repository $repository, MetadatadescriptionStructurer $metadataAnnotationStructurer)
	{
		$this->repository = $repository;
		$this->metadataAnnotationStructurer = $metadataAnnotationStructurer;
	}

	public function getIndex()
	{
		
		return Redirect::to('media/preprocess/metadatadescription/actions');
	}

	public function getInfo()
	{
		return View::make('media.preprocess.metadatadescription.pages.info');
	}

	public function getActions()
	{

		$entities = Unit::where('documentType', '=', 'tv-news-broadcasts')->get();
	
		if(count($entities) > 0)
		{
			return View::make('media.preprocess.metadatadescription.pages.actions', compact('entities'));
		}

		return Redirect::to('media/upload')->with('flashNotice', 'You have not uploaded any "tv-news-broadcasts" documents yet');


		$items = Cart::content();

		if(count($items) > 0)
		{
			$entities = array();

			foreach($items as $item)
			{
				if($entity = $this->repository->find($item['id']))
				{
					if($entity->documentType != "tv-news-broadcasts")
					{
						continue;
					}
						
					$entity['rowid'] = $item['rowid'];
					array_push($entities, $entity);
				}
					
			}
			
			return View::make('media.preprocess.metadatadescription.pages.actions', compact('entities'));

		}

		return Redirect::to('media/browse')->with('flashNotice', 'You have not added any "tv-news-broadcasts" items to your selection yet');

	}

	public function getPreview()
	{
		if($URI = Input::get('URI'))
		{
			if($entity = $this->repository->find($URI)) {
				if($entity->documentType != "tv-news-broadcasts")
				{
					continue;
				}

				$document = $this->metadataAnnotationStructurer->process($entity);
				
			}
		} 
		else 
		{
			return Redirect::back()->with('flashError', 'No valid URI given: ' . $URI);
		}	
	}

function stats_stddev_func($a) {
    $size = 0; 
    $nonNullArray = array();

    foreach ($a as $value) {
        if ($value != null) {
            $size ++;
            array_push($nonNullArray, $value);
        }
    }
    
    if ($size === 0) {
        return null;
    }
    
    $mean = array_sum($nonNullArray) / $size;
    $carry = 0.0;
    foreach ($nonNullArray as $val) {
        $d = ((double) $val) - $mean;
        $carry += $d * $d;
    };
    
    return sqrt($carry / $size);
}    
/*
function createStatisticsForMetadatadescriptionCache ($id) {
    set_time_limit(5200);
    \Session::flash('rawArray', 1);
    $db = \DB::getMongoDB();
    $db = $db->entities;

    $result = Entity::where('_id', $id)->get()->toArray();
    //    dd($result);
    foreach($result as &$parent) {
        $children = Entity::whereIn('parents', [$id])->where('content.features.cleanedUpEntities', 'exists', true)->where("documentType", '!=', "annotatedmetadatadescription")->get(['content.features'])->toArray();
       // dd($children);
        $eventChildren = Entity::whereIn('parents', [$id])->where('content.automatedEvents', 'exists', true)->get(['content.automatedEvents'])->toArray();
                $parent['content']['features'] = array();
                $parent['content']['features']['cleanedUpEntities'] = array();
                $parent['content']['features']['automatedEvents'] = array();
                $parent['content']['features']['topics'] = array();
                $parent['content']['features']['people'] = array();
                $parent['content']['features']['time'] = array();
                $parent['content']['features']['location'] = array();
                $parent['content']['features']['other'] = array();
                $parent['annotations'] = array();
                $parent['annotations']['statistics'] = array();
                $parent['annotations']['features'] = array();
                $parent['annotations']['statistics']['majvoting'] = array();
                $parent['annotations']['statistics']["crowdtruthmetrics"] = array();
                $parent['annotations']['features']['cleanedUpEntities'] = array();
                $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"] = array();
                $parent['annotations']['statistics']["crowdtruthmetrics"]["cleanedUpEntities"] = array();
                $parent['annotations']['features']['topics'] = array();

                foreach ($children as $child) {  
                    if(isset($child["content"]["features"]["topics"])) {
                        $parent['annotations']['features']['topics'] = $child["content"]["features"]["topics"];
                        $parent['content']['features']['topics'] = $child["content"]["features"]["topics"];
                    }
                }

                foreach ($eventChildren as $child) {  
                    if(isset($child["content"]["automatedEvents"])) {
                        $parent['annotations']['automatedEvents'] = $child["content"]["automatedEvents"];
                        $parent['content']['features']['automatedEvents'] = $child["content"]["automatedEvents"];
                    }
                }

                foreach ($children as $child) {
                    if (!empty($child['content']['features']['cleanedUpEntities']))
                    foreach($child['content']['features']['cleanedUpEntities'] as $childKey => $childValue) {
                        $found = false;
                        foreach ($parent['annotations']['statistics']['majvoting']["cleanedUpEntities"] as $parentKey => $parentValue) {
                            if (strtolower($childValue["label"]) == strtolower($parentValue["label"]) && intval($childValue["startOffset"]) == intval($parentValue["startOffset"]) && intval($childValue["endOffset"]) == intval($parentValue["endOffset"])) {
                                $found = true;
                                array_push($parent['annotations']["features"]["cleanedUpEntities"][$parentKey]["extractors"], $childValue["provenance"]);
								array_push($parent['content']["features"]["cleanedUpEntities"][$parentKey]["extractors"], $childValue["provenance"]);
                                array_push($parent["annotations"]["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["noExtractorsPerLabel"]["extractors"], $childValue["provenance"]);
                                $parent['annotations']["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["noExtractorsPerLabel"]["count"] += 1;
                                $parent['annotations']["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["noExtractorsPerLabel"]["relevanceScore"]["value"] = $parent["annotations"]["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["noExtractorsPerLabel"]["count"] / 6;
                                $parent['annotations']["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["confidence"]["extractors"][$childValue["provenance"]] = $childValue["confidence"];
                                $parent['annotations']["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["confidence"]["value"] = max($parent["annotations"]["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["confidence"]["extractors"]);
                                $noConf = 0;
                                foreach ($parent['annotations']["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["confidence"]["extractors"] as $confKey => $confVal) {
                                    if ($confVal != null) {
                                        $noConf ++;
                                    }
                                }
                                if ($noConf != 0) {
                                    $parent['annotations']["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["clarity"]["mean"] = array_sum($parent['annotations']["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["confidence"]["extractors"]) / $noConf;
                                    $parent['annotations']["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["clarity"]["stddev"] = $this->stats_stddev_func($parent['annotations']["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["confidence"]["extractors"]);
                                    $parent['annotations']["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["clarity"]["mse"] = pow($parent['annotations']["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["clarity"]["stddev"], 2) / $noConf;
                                }
                                else {
                                    $parent['annotations']["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["clarity"]["mean"] = $parent['annotations']["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["confidence"]["value"];
                                    $parent['annotations']["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["clarity"]["stddev"] = $this->stats_stddev_func($parent['annotations']["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["confidence"]["extractors"]);
                                    $parent['annotations']["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["clarity"]["mse"] = pow($parent['annotations']["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["clarity"]["stddev"], 2);
                                }

                                foreach ($childValue["types"] as $keyType => $valueType) {
                                    $foundType = false;
                                    foreach ($parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsPerType"] as $parentTypeKey => $parentTypeValue) {
                                        if ($parentTypeKey == strtolower($valueType["typeURI"])) {
                                            if (!in_array($childValue["provenance"], $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsPerType"][$parentTypeKey]["extractors"])) {
                                                $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsPerType"][$parentTypeKey]["count"] += 1;
                                                array_push($parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsPerType"][$parentTypeKey]["extractors"], $childValue["provenance"]);           
                                                $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsPerType"][$parentTypeKey]["relevanceScore"]["value"] = $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsPerType"][$parentTypeKey]["count"] / $parent['annotations']["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["noExtractorsPerLabel"]["count"];
                                            }
                                            $foundType = true;
                                        }
                                        else {
                                            $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsPerType"][$parentTypeKey]["relevanceScore"]["value"] = $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsPerType"][$parentTypeKey]["count"] / $parent['annotations']["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["noExtractorsPerLabel"]["count"];
                                        }

                                        if ($foundType == true) {
                                            break;
                                        }
                                    }

                                    if ($foundType == false) {
                                        if ($valueType["typeURI"] != null) {
                                            $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsPerType"][strtolower($valueType["typeURI"])] = array();
                                            $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsPerType"][strtolower($valueType["typeURI"])]["count"] = 1;
                                            $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsPerType"][strtolower($valueType["typeURI"])]["extractors"] = array();
                                            array_push($parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsPerType"][strtolower($valueType["typeURI"])]["extractors"], $childValue["provenance"]);
                                            $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsPerType"][strtolower($valueType["typeURI"])]["relevanceScore"] = array();
                                            $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsPerType"][strtolower($valueType["typeURI"])]["relevanceScore"]["value"] = 1/ $parent["annotations"]["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["noExtractorsPerLabel"]["count"];
                                        }
                                    }

                                    $foundResource = false;
                                    foreach ($parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsPerResource"] as $parentResourceKey => $parentResourceValue) {
                                        if (strtolower($parentResourceKey) == strtolower($valueType["entityURI"])) {
                                            if (!in_array($childValue["provenance"], $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsPerResource"][$parentResourceKey]["extractors"])) {
                                                $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsPerResource"][$parentResourceKey]["count"] += 1;
                                                array_push($parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsPerResource"][$parentResourceKey]["extractors"], $childValue["provenance"]);
                                                $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsPerResource"][$parentResourceKey]["relevanceScore"]["value"] = $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsPerResource"][$parentResourceKey]["count"] / $parent["annotations"]["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["noExtractorsPerLabel"]["count"];
                                            }
                                            $foundResource = true;
                                        }
                                        else {
                                            $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsPerResource"][$parentResourceKey]["relevanceScore"]["value"] = $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsPerResource"][$parentResourceKey]["count"] / $parent["annotations"]["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["noExtractorsPerLabel"]["count"];
                                        }

                                        if ($foundResource == true) {
                                            break;
                                        }
                                    }

                                    if ($foundResource == false) {
                                        if ($valueType["entityURI"] != null) {
                                            $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsPerResource"][$valueType["entityURI"]] = array();
                                            $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsPerResource"][$valueType["entityURI"]]["count"] = 1;
                                            $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsPerResource"][$valueType["entityURI"]]["extractors"] = array();
                                            array_push($parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsPerResource"][$valueType["entityURI"]]["extractors"], $childValue["provenance"]);
                                            $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsPerResource"][$valueType["entityURI"]]["relevanceScore"] = array();
                                            $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsPerResource"][$valueType["entityURI"]]["relevanceScore"]["value"] = 1/$parent["annotations"]["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["noExtractorsPerLabel"]["count"];
                                        
                                        }
                                    }

                                    $foundLabelTypePair = false;
                                    $tempTypeValue = "";
                                    if ($valueType["typeURI"] != null) {
                                        $tempTypeValue = strtolower($valueType["typeURI"]);
                                    }
                                    foreach ($parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelTypePair"] as $parentLabelTypeKey => $parentLabelTypeValue) {
                                        if (strtolower($parentLabelTypeKey) == strtolower($childValue["label"] . "-" . $tempTypeValue)) {
                                            if (!in_array($childValue["provenance"], $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelTypePair"][$parentLabelTypeKey]["extractors"])) {
                                                $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelTypePair"][$parentLabelTypeKey]["count"] += 1;
                                                array_push($parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelTypePair"][$parentLabelTypeKey]["extractors"], $childValue["provenance"]);
                                                $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelTypePair"][$parentLabelTypeKey]["relevanceScore"]["value"] = $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelTypePair"][$parentLabelTypeKey]["count"] / $parent["annotations"]["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["noExtractorsPerLabel"]["count"];
                                            }
                                            $foundLabelTypePair = true;
                                        }
                                        else {
                                            $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelTypePair"][$parentLabelTypeKey]["relevanceScore"]["value"] = $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelTypePair"][$parentLabelTypeKey]["count"] / $parent["annotations"]["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["noExtractorsPerLabel"]["count"];
                                        }

                                        if ($foundLabelTypePair == true) {
                                            break;
                                        }
                                    }

                                    if ($foundLabelTypePair == false) {
                                        $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelTypePair"][$childValue["label"] . "-" . $tempTypeValue] = array();
                                        $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelTypePair"][$childValue["label"] . "-" . $tempTypeValue]["count"] = 1;
                                        $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelTypePair"][$childValue["label"] . "-" . $tempTypeValue]["extractors"] = array();
                                        array_push($parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelTypePair"][$childValue["label"] . "-" . $tempTypeValue]["extractors"], $childValue["provenance"]);
                                        $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelTypePair"][$childValue["label"] . "-" . $tempTypeValue]["relevanceScore"] = array();
                                        $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelTypePair"][$childValue["label"] . "-" . $tempTypeValue]["relevanceScore"]["value"] = 1 / $parent["annotations"]["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["noExtractorsPerLabel"]["count"];
                                    }

                                    
                                    $foundLabelResourcePair = false;
                                    $tempResourceValue = "";
                                    if ($valueType["entityURI"] != null) {
                                        $tempResourceValue = $valueType["entityURI"];
                                    }
                                    foreach ($parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelResourcePair"] as $parentLabelResourceKey => $parentLabelResourceValue) {
                            
                                        if (strtolower($parentLabelResourceKey) == strtolower($childValue["label"] . "-" . $tempResourceValue)) {
                                            if (!in_array($childValue["provenance"], $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelResourcePair"][$parentLabelResourceKey]["extractors"])) {
                                                $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelResourcePair"][$parentLabelResourceKey]["count"] += 1;
                                                array_push($parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelResourcePair"][$parentLabelResourceKey]["extractors"], $childValue["provenance"]);
                                                $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelResourcePair"][$parentLabelResourceKey]["relevanceScore"]["value"] = $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelResourcePair"][$parentLabelResourceKey]["count"] / $parent["annotations"]["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["noExtractorsPerLabel"]["count"];
                                            }
                                            $foundLabelResourcePair = true;
                                        }
                                        else {
                                            $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelResourcePair"][$parentLabelResourceKey]["relevanceScore"]["value"] = $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelResourcePair"][$parentLabelResourceKey]["count"] / $parent["annotations"]["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["noExtractorsPerLabel"]["count"];
                                        }

                                        if ($foundLabelResourcePair == true) {
                                            break;
                                        }
                                    }

                                    if ($foundLabelResourcePair == false) {
                                        $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelResourcePair"][$parentLabelResourceKey] = array();
                                        $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelResourcePair"][$parentLabelResourceKey]["count"] = 1;
                                        $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelResourcePair"][$parentLabelResourceKey]["extractors"] = array();
                                        array_push($parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelResourcePair"][$parentLabelResourceKey]["extractors"], $childValue["provenance"]);
                                        $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelResourcePair"][$parentLabelResourceKey]["relevanceScore"] = array();
                                        $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelResourcePair"][$parentLabelResourceKey]["relevanceScore"]["value"] = 1/$parent["annotations"]["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["noExtractorsPerLabel"]["count"];
                                    }
                                    

                                    $foundTypeResourcePair = false;
                                    $tempTypeValue = "";
                                    $tempResourceValue = "";
                                    if ($valueType["entityURI"] != null) {
                                        $tempResourceValue = $valueType["entityURI"];
                                    }
                                    if ($valueType["typeURI"] != null) {
                                        $tempTypeValue = $valueType["typeURI"];
                                    }
                                    foreach ($parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsTypeResourcePair"] as $parentLabelTypeKey => $parentLabelTypeValue) {

                                        if (strtolower($parentLabelTypeKey) == strtolower($tempTypeValue . "-" . $tempResourceValue)) {
                                            if (!in_array($childValue["provenance"], $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsTypeResourcePair"][$parentLabelTypeKey]["extractors"])) {
                                                $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsTypeResourcePair"][$parentLabelTypeKey]["count"] += 1;
                                                array_push($parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsTypeResourcePair"][$parentLabelTypeKey]["extractors"], $childValue["provenance"]);
                                                $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsTypeResourcePair"][$parentLabelTypeKey]["relevanceScore"]["value"] = $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsTypeResourcePair"][$parentLabelTypeKey]["count"] / $parent["annotations"]["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["noExtractorsPerLabel"]["count"];
                                            }
                                            $foundTypeResourcePair = true;
                                        }
                                        else {
                                            $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsTypeResourcePair"][$parentLabelTypeKey]["relevanceScore"]["value"] = $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsTypeResourcePair"][$parentLabelTypeKey]["count"] / $parent["annotations"]["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["noExtractorsPerLabel"]["count"];
                                        }

                                        if ($foundTypeResourcePair == true) {
                                            break;
                                        }
                                    }

                                    if ($foundTypeResourcePair == false) {
                                        $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsTypeResourcePair"][$parentLabelTypeKey] = array();
                                        $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsTypeResourcePair"][$parentLabelTypeKey]["count"] = 1;
                                        $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsTypeResourcePair"][$parentLabelTypeKey]["extractors"] = array();
                                        array_push($parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsTypeResourcePair"][$parentLabelTypeKey]["extractors"], $childValue["provenance"]);
                                        $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsTypeResourcePair"][$parentLabelTypeKey]["relevanceScore"] = array();
                                        $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsTypeResourcePair"][$parentLabelTypeKey]["relevanceScore"]["value"] = 1/$parent["annotations"]["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["noExtractorsPerLabel"]["count"];
                                    }

                                    $foundLabelTypeResourcePair = false;
                                    foreach ($parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelTypeResourcePair"] as $parentLabelTypeResourceKey => $parentLabelTypeResourceValue) {
                                        if (strtolower($parentLabelTypeResourceKey) == strtolower($childValue["label"] . "-" . $tempTypeValue . "-" . $tempResourceValue)) {
                                            if (!in_array($childValue["provenance"], $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelTypeResourcePair"][$parentLabelTypeResourceKey]["extractors"])) {
                                                $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelTypeResourcePair"][$parentLabelTypeResourceKey]["count"] += 1;
                                                array_push($parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelTypeResourcePair"][$parentLabelTypeResourceKey]["extractors"], $childValue["provenance"]);
                                                $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelTypeResourcePair"][$parentLabelTypeResourceKey]["relevanceScore"]["value"] = $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelTypeResourcePair"][$parentLabelTypeResourceKey]["count"] / $parent["annotations"]["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["noExtractorsPerLabel"]["count"];
                                            }
                                            $foundLabelTypeResourcePair = true;
                                        }
                                        else {
                                            $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelTypeResourcePair"][$parentLabelTypeResourceKey]["relevanceScore"]["value"] = $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelTypeResourcePair"][$parentLabelTypeResourceKey]["count"] / $parent["annotations"]["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["noExtractorsPerLabel"]["count"];
                                        }

                                        if ($foundLabelTypeResourcePair == true) {
                                            break;
                                        }
                                    }

                                    if ($foundLabelTypeResourcePair == false) {
                                        $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelTypeResourcePair"][$parentLabelTypeResourceKey] = array();
                                        $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelTypeResourcePair"][$parentLabelTypeResourceKey]["count"] = 1;
                                        $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelTypeResourcePair"][$parentLabelTypeResourceKey]["extractors"] = array();
                                        array_push($parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelTypeResourcePair"][$parentLabelTypeResourceKey]["extractors"], $childValue["provenance"]);
                                        $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelTypeResourcePair"][$parentLabelTypeResourceKey]["relevanceScore"] = array();
                                        $parent['annotations']['statistics']['majvoting']["cleanedUpEntities"][$parentKey]["noExtractorsLabelTypeResourcePair"][$parentLabelTypeResourceKey]["relevanceScore"]["value"] = 1/$parent["annotations"]["statistics"]["majvoting"]["cleanedUpEntities"][$parentKey]["noExtractorsPerLabel"]["count"];
                                    }

                                }
                            }
                            if ($found == true) {
                                break;
                            }
                        //    break;
                        }

                        if ($found == false) {
                            $extractedEntity = array();
                            $extractedEntity["label"] = $childValue["label"];
                            $extractedEntity["startOffset"] = intval($childValue["startOffset"]);
                            $extractedEntity["endOffset"] = intval($childValue["endOffset"]);
                            $extractedEntity["extractors"] = array();
                            array_push($extractedEntity["extractors"], $childValue["provenance"]);
                            $newEntity = array();
                            $newEntity["label"] = $childValue["label"];
                            $newEntity["startOffset"] = $childValue["startOffset"];
                            $newEntity["endOffset"] = $childValue["endOffset"];
                            $newEntity["noExtractorsPerLabel"] = array();
                            $newEntity["noExtractorsPerLabel"]["extractors"] = array();
                            array_push($newEntity["noExtractorsPerLabel"]["extractors"], $childValue["provenance"]);
                            $newEntity["noExtractorsPerLabel"]["count"] = 1;
                            $newEntity["noExtractorsPerLabel"]["relevanceScore"] = array();
                            $newEntity["noExtractorsPerLabel"]["relevanceScore"]["value"] = 1/6;
                            $newEntity["confidence"] = array();
                            $newEntity["confidence"]["extractors"] = array();
                            $newEntity["confidence"]["extractors"][$childValue["provenance"]] = $childValue["confidence"];
                            $newEntity["confidence"]["value"] = $childValue["confidence"];
                            $newEntity["clarity"] = array();
                            $newEntity["clarity"]["mean"] = $childValue["confidence"];
                            $newEntity["clarity"]["stddev"] = $this->stats_stddev_func($newEntity["confidence"]["extractors"]);
                            $newEntity["clarity"]["mse"] = pow($newEntity["clarity"]["stddev"], 2) / 1;
                            $newEntity["noExtractorsPerType"] = array();
                            $newEntity["noExtractorsPerResource"] = array();
                            $newEntity["noExtractorsLabelTypePair"] = array();
                            $newEntity["noExtractorsLabelResourcePair"] = array();
                            $newEntity["noExtractorsTypeResourcePair"] = array();
                            $newEntity["noExtractorsLabelTypeResourcePair"] = array();

                            foreach ($childValue["types"] as $keyType => $valueType) {
                                if ($valueType["typeURI"] != null || $valueType["typeURI"] != "") {
                                    $newEntity["noExtractorsPerType"][strtolower($valueType["typeURI"])] = array();
                                    $newEntity["noExtractorsPerType"][strtolower($valueType["typeURI"])]["count"] = 1;
                                    $newEntity["noExtractorsPerType"][strtolower($valueType["typeURI"])]["extractors"] = array();
                                    array_push($newEntity["noExtractorsPerType"][strtolower($valueType["typeURI"])]["extractors"], $childValue["provenance"]);
                                    $newEntity["noExtractorsPerType"][strtolower($valueType["typeURI"])]["relevanceScore"] = array();
                                    $newEntity["noExtractorsPerType"][strtolower($valueType["typeURI"])]["relevanceScore"]["value"] = 1;
                                }
                                if ($valueType["entityURI"] != null) {
                                    $newEntity["noExtractorsPerResource"][$valueType["entityURI"]] = array();
                                    $newEntity["noExtractorsPerResource"][$valueType["entityURI"]]["count"] = 1;
                                    $newEntity["noExtractorsPerResource"][$valueType["entityURI"]]["extractors"] = array();
                                    array_push($newEntity["noExtractorsPerResource"][$valueType["entityURI"]]["extractors"], $childValue["provenance"]);
                                    $newEntity["noExtractorsPerResource"][$valueType["entityURI"]]["relevanceScore"] = array();
                                    $newEntity["noExtractorsPerResource"][$valueType["entityURI"]]["relevanceScore"]["value"] = 1;
                                }
            
                                $tempTypeValue = "";
                                if ($valueType["typeURI"] != null || $valueType["typeURI"] != "") {
                                    $tempTypeValue = strtolower($valueType["typeURI"]);
                                }
                                if (!array_key_exists($childValue["label"] . "-" . strtolower($tempTypeValue), $newEntity["noExtractorsLabelTypePair"])) {
                                    $newEntity["noExtractorsLabelTypePair"][$childValue["label"] . "-" . strtolower($tempTypeValue)] = array();
                                    $newEntity["noExtractorsLabelTypePair"][$childValue["label"] . "-" . strtolower($tempTypeValue)]["count"] = 1;
                                    $newEntity["noExtractorsLabelTypePair"][$childValue["label"] . "-" . strtolower($tempTypeValue)]["extractors"] = array();
                                    array_push($newEntity["noExtractorsLabelTypePair"][$childValue["label"] . "-" . strtolower($tempTypeValue)]["extractors"], $childValue["provenance"]);
                                    $newEntity["noExtractorsLabelTypePair"][$childValue["label"] . "-" . strtolower($tempTypeValue)]["relevanceScore"] = array();
                                    $newEntity["noExtractorsLabelTypePair"][$childValue["label"] . "-" . strtolower($tempTypeValue)]["relevanceScore"]["value"] = 1;
                                }

                                $tempResourceValue = "";
                                if ($valueType["entityURI"] != null || $valueType["typeURI"] != "") {
                                    $tempResourceValue = $valueType["entityURI"];
                                }
                                if (!array_key_exists($childValue["label"] . "-" . $tempResourceValue, $newEntity["noExtractorsLabelResourcePair"])) {
                                    $newEntity["noExtractorsLabelResourcePair"][$childValue["label"] . "-" . $tempResourceValue] = array();
                                    $newEntity["noExtractorsLabelResourcePair"][$childValue["label"] . "-" . $tempResourceValue]["count"] = 1;
                                    $newEntity["noExtractorsLabelResourcePair"][$childValue["label"] . "-" . $tempResourceValue]["extractors"] = array();
                                    array_push($newEntity["noExtractorsLabelResourcePair"][$childValue["label"] . "-" . $tempResourceValue]["extractors"], $childValue["provenance"]);
                                    $newEntity["noExtractorsLabelResourcePair"][$childValue["label"] . "-" . $tempResourceValue]["relevanceScore"] = array();
                                    $newEntity["noExtractorsLabelResourcePair"][$childValue["label"] . "-" . $tempResourceValue]["relevanceScore"]["value"] = 1;
                                }

                                if (!array_key_exists($tempTypeValue . "-" . $tempResourceValue, $newEntity["noExtractorsTypeResourcePair"])) {
                                    $newEntity["noExtractorsTypeResourcePair"][$tempTypeValue . "-" . $tempResourceValue] = array();
                                    $newEntity["noExtractorsTypeResourcePair"][$tempTypeValue . "-" . $tempResourceValue]["count"] = 1;
                                    $newEntity["noExtractorsTypeResourcePair"][$tempTypeValue . "-" . $tempResourceValue]["extractors"] = array();
                                    array_push($newEntity["noExtractorsTypeResourcePair"][$tempTypeValue . "-" . $tempResourceValue]["extractors"], $childValue["provenance"]);
                                    $newEntity["noExtractorsTypeResourcePair"][$tempTypeValue . "-" . $tempResourceValue]["relevanceScore"] = array();
                                    $newEntity["noExtractorsTypeResourcePair"][$tempTypeValue . "-" . $tempResourceValue]["relevanceScore"]["value"] = 1;
                                }

                                if (!array_key_exists($childValue["label"] . "-" . $tempTypeValue . "-" . $tempResourceValue, $newEntity["noExtractorsLabelTypeResourcePair"])) {
                                    $newEntity["noExtractorsLabelTypeResourcePair"][$childValue["label"] . "-" . $tempTypeValue . "-" . $tempResourceValue] = array();
                                    $newEntity["noExtractorsLabelTypeResourcePair"][$childValue["label"] . "-" . $tempTypeValue . "-" . $tempResourceValue]["count"] = 1;
                                    $newEntity["noExtractorsLabelTypeResourcePair"][$childValue["label"] . "-" . $tempTypeValue . "-" . $tempResourceValue]["extractors"] = array();
                                    array_push($newEntity["noExtractorsLabelTypeResourcePair"][$childValue["label"] . "-" . $tempTypeValue . "-" . $tempResourceValue]["extractors"], $childValue["provenance"]);
                                    $newEntity["noExtractorsLabelTypeResourcePair"][$childValue["label"] . "-" . $tempTypeValue . "-" . $tempResourceValue]["relevanceScore"] = array();
                                    $newEntity["noExtractorsLabelTypeResourcePair"][$childValue["label"] . "-" . $tempTypeValue . "-" . $tempResourceValue]["relevanceScore"]["value"] = 1;
                                }
                            }
                            array_push($parent['annotations']['statistics']['majvoting']['cleanedUpEntities'], $newEntity);
                            array_push($parent['annotations']['features']['cleanedUpEntities'], $extractedEntity);
                            array_push($parent['content']['features']['cleanedUpEntities'], $extractedEntity);

                //            dd($parent['annotations']['features']['cleanedUpEntities']);
                        }
                    }
                }


                foreach ($parent["annotations"]["statistics"]["majvoting"]["cleanedUpEntities"] as $ent) {
          			$foundP = false;
          			$foundL = false;
          			$foundT = false;
          			$foundO = false;
          			$types = array_keys($ent["noExtractorsPerType"]);

		          	if (count($types) == 0) {
		          		$newOther = array();
		          		$newOther["label"] = $ent["label"];
		          		$newOther["startOffset"] = $ent["startOffset"];
		          		$newOther["endOffset"] = $ent["endOffset"];
		          		array_push($parent['content']['features']['other'], $newOther);
		            	continue;
		          	}

		          	foreach ($types as $type) {
		            	if (stripos($type, "person") || stripos($type, "agent") || stripos($type, "organization")) {
		              		if ($foundP == true) {
		                		continue;
		              		}
			              	$foundP = true;
			            	$newPeople = array();
			          		$newPeople["label"] = $ent["label"];
			          		$newPeople["startOffset"] = $ent["startOffset"];
			          		$newPeople["endOffset"] = $ent["endOffset"];
			          		array_push($parent['content']['features']['people'], $newPeople);
			            }
			            else if (stripos($type, "place") || stripos($type, "settlement") || stripos($type, "country") || stripos($type, "city") || stripos($type, "location") || stripos($type, "land")) {
			            	if ($foundL == true) {
			                	continue;
			              	}
			              	$foundL = true;
			            	$newLocation = array();
			          		$newLocation["label"] = $ent["label"];
			          		$newLocation["startOffset"] = $ent["startOffset"];
			          		$newLocation["endOffset"] = $ent["endOffset"];
			          		array_push($parent['content']['features']['location'], $newLocation);
			            }
			            else if (stripos($type, "time") || stripos($type, "period") || stripos($type, "year") || stripos($type, "date")) {
			              	if ($foundT == true) {
			                	continue;
			              	}
			              	$foundT = true;
			            	$newTime = array();
			          		$newTime["label"] = $ent["label"];
			          		$newTime["startOffset"] = $ent["startOffset"];
			          		$newTime["endOffset"] = $ent["endOffset"];
			          		array_push($parent['content']['features']['time'], $newTime);
			            }
			            else {
			              	if ($foundO == true) {
			                	continue;
			              	}
			              	$foundO = true;
			              	$newOther = array();
			          		$newOther["label"] = $ent["label"];
			          		$newOther["startOffset"] = $ent["startOffset"];
			          		$newOther["endOffset"] = $ent["endOffset"];
			          		array_push($parent['content']['features']['other'], $newOther);
			            }
		        	}
		    	}

		    	$parent['content']['otherCount'] = count($parent['content']['features']['other']);
		    	$parent['content']['peopleCount'] = count($parent['content']['features']['people']);
		    	$parent['content']['timeCount'] = count($parent['content']['features']['time']);
		    	$parent['content']['locationCount'] = count($parent['content']['features']['location']);
		    	$parent['content']['automatedEventsCount'] = count($parent['content']['features']['automatedEvents']);
                $words = explode(" ", $parent["content"]["description"]);
                $parent["wordCount"] = count($words);
                $parent["totalNoOfFeatures"] = count($parent["content"]["features"]["cleanedUpEntities"]) + count($parent["content"]["features"]["automatedEvents"]);
    

		    
            //    $content = $result["content"];
            //    $content["features"] = $parent['annotations']['features'];
            //    $result["content"] = $content;
              //  $result["annotations"] = $parent['annotations'];

            //    dd($result);
                try {
                Entity::where('_id', '=', $id)->forceDelete();
            //    Entity::insert($result);
            //    $result->save();
                $db->batchInsert(
                    $result,
                    array('continueOnError' => true)
                );             
            } catch (Exception $e) {
            // ContinueOnError will still throw an exception on duplication, even though it continues, so we just move on.
            }
          }
        \Session::forget('rawArray');
      //       dd("done");
    }
*/
	public function getProcess()
	{
		if($URI = Input::get('URI'))
		{
			if($entity = $this->repository->find($URI)) {
				if($entity->documentType != "tv-news-broadcasts")
				{
					continue;
				}
			
				$metadataProcessing = $this->metadataAnnotationStructurer->process($entity);
				$status_processing = $this->metadataAnnotationStructurer->store($entity, $metadataProcessing);
            /*    if (isset($status_processing["processAutomatedEventExtraction"])) {
                	if (!isset($status_processing["processAutomatedEventExtraction"]['error'])) {
						Entity::where('_id', '=', $entity->_id)->update( array('preprocessed.automatedEvents' => true));
					}
					echo "<pre>";
				}
				if (isset($status_processing["thdapi"])) {	
					if (!isset($status_processing["thdapi"]['error'])) {
						Entity::where('_id', '=', $entity->_id)->update( array('preprocessed.automatedEntities' => true));
					}
					echo "<pre>";
				}
				if (isset($status_processing["textrazorapi"])) {	
					if (!isset($status_processing["textrazorapi"]['error'])) {
						Entity::where('_id', '=', $entity->_id)->update( array('preprocessed.automatedEntities' => true));
					}
					echo "<pre>";
				}
				if (isset($status_processing["semitagsapi"])) {	
					if (!isset($status_processing["semitagsapi"]['error'])) {
						Entity::where('_id', '=', $entity->_id)->update( array('preprocessed.automatedEntities' => true));
					}
					echo "<pre>";
				}
				if (isset($status_processing["nerdapi"])) {	
					if (!isset($status_processing["nerdapi"]['error'])) {
						Entity::where('_id', '=', $entity->_id)->update( array('preprocessed.automatedEntities' => true));
					}
					echo "<pre>";
				}
				if (isset($status_processing["lupediaapi"])) {	
					if (!isset($status_processing["lupediaapi"]['error'])) {
						Entity::where('_id', '=', $entity->_id)->update( array('preprocessed.automatedEntities' => true));
					}
					echo "<pre>";
				}
				if (isset($status_processing["dbpediaspotlightapi"])) {	
					if (!isset($status_processing["dbpediaspotlightapi"]['error'])) {
						Entity::where('_id', '=', $entity->_id)->update( array('preprocessed.automatedEntities' => true));
					}
					echo "<pre>";
				}

				if(isset($status_processing["processAutomatedEventExtraction"]['success'])) {
					$this->createStatisticsForMetadatadescriptionCache($entity->_id);
					return Redirect::back()->with('flashSuccess', 'Your video description has been pre-processed in named entities and putative events');
				}
				else {
					return Redirect::back()->with('flashError', 'An error occurred while the video description was being pre-processed in named entities and putative events');
				}
				*/
			}
		} 
		else 
		{
			return Redirect::back()->with('flashError', 'No valid URI given: ' . $URI);
		}	
	}

}

