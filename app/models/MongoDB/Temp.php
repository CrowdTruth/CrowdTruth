<?php

namespace MongoDB;

require_once 'help_functions.php';

use Moloquent, Input;


class Temp extends Moloquent {

    protected $collection = 'temp';
    protected static $unguarded = true;
    public static $snakeAttributes = false;

    public function __construct()
    {
        $this->filterResults();
        parent::__construct();
    }

    protected $hidden = ['metrics.pivotTables'];

    public function filterResults()
    {
        $input = Input::all();
        if(array_key_exists('wasDerivedFrom', $input))    array_push($this->appends, 'wasDerivedFrom');
        if(array_key_exists('wasGeneratedBy', $input))    array_push($this->with, 'wasGeneratedBy');
        if(array_key_exists('wasAttributedTo', $input))    $this->with = array_merge($this->with, array('wasAttributedToUserAgent', 'wasAttributedToCrowdAgent'));
        if(array_key_exists('wasAttributedToUserAgent', $input))    array_push($this->with, 'wasAttributedToUserAgent');
        if(array_key_exists('wasAttributedToCrowdAgent', $input))    array_push($this->with, 'wasAttributedToCrowdAgent');
        if(array_key_exists('hasConfiguration', $input))    array_push($this->with, 'hasConfiguration');
        if(isset($input['wasDerivedFrom']['without'])) $this->hidden = array_merge($this->hidden, array_flatten(array($input['wasDerivedFrom']['without'])));
        if(isset($input['without'])) $this->hidden = array_merge($this->hidden, array_flatten(array($input['without'])));
    }  


    public static function createStatisticsForMetadatadescriptionCache () {
        set_time_limit(5200);
        \Session::flash('rawArray', 1);
        $db = \DB::getMongoDB();
        $db = $db->temp;

        $result = \MongoDB\Entity::where('documentType', '=', 'metadatadescription')->where('content.description', 'exists', true)->get()->toArray();
        if (count($result) > 0) {
            foreach ($result as &$parent) {
                $children = \MongoDB\Entity::whereIn('parents', [$parent['_id']])->where('content.features.entities', 'exists', true)->get(['content.features'])->toArray();
                $parent['content']['statistics']['majvoting'] = array();
                $parent['content']['statistics']["crowdtruth"] = array();
                $parent['content']['features']['entities'] = array();
                //$parent['content']['features']['initialEntities'] = array();
                $parent['content']['features']['topics'] = array();

                foreach ($children as $child) {  
                    if(isset($child["content"]["features"]["topics"])) {
                        $parent['content']['features']['topics'] = $child["content"]["features"]["topics"];
                    }
                }

                foreach ($children as $child) {
                    if (!empty($child['content']['features']['initialEntities']))
                    foreach($child['content']['features']['initialEntities'] as $childKey => $childValue) {
                        $found = false;
                        foreach ($parent['content']['statistics']['majvoting'] as $parentKey => $parentValue) {
                            if (strtolower($childValue["label"]) == strtolower($parentValue["label"]) && intval($childValue["startOffset"]) == intval($parentValue["startOffset"]) && intval($childValue["endOffset"]) == intval($parentValue["endOffset"])) {
                                $found = true;
                            //    $parent["content"]["statistics"]["entities"][$parentKey]["relevanceScore"]["count"] += 1;
                            //    $parent["content"]["statistics"]["entities"][$parentKey]["relevanceScore"]["value"] = $parent["content"]["statistics"]["entities"][$parentKey]["relevanceScore"]["count"] / 6;
                                array_push($parent["content"]["features"]["entities"][$parentKey]["extractors"], $childValue["provenance"]);
                                array_push($parent["content"]["statistics"]["majvoting"][$parentKey]["noExtractorsPerLabel"]["extractors"], $childValue["provenance"]);
                                $parent["content"]["statistics"]["majvoting"][$parentKey]["noExtractorsPerLabel"]["count"] += 1;
                                $parent["content"]["statistics"]["majvoting"][$parentKey]["noExtractorsPerLabel"]["relevanceScore"]["value"] = $parent["content"]["statistics"]["majvoting"][$parentKey]["noExtractorsPerLabel"]["count"] / 6;
                                $parent["content"]["statistics"]["majvoting"][$parentKey]["confidence"]["extractors"][$childValue["provenance"]] = $childValue["confidence"];
                                $parent["content"]["statistics"]["majvoting"][$parentKey]["confidence"]["value"] = max($parent["content"]["statistics"]["majvoting"][$parentKey]["confidence"]["extractors"]);
                                $noConf = 0;
                                foreach ($parent["content"]["statistics"]["majvoting"][$parentKey]["confidence"]["extractors"] as $confKey => $confVal) {
                                    if ($confVal != null) {
                                        $noConf ++;
                                    }
                                }
                                if ($noConf != 0) {
                                    $parent["content"]["statistics"]["majvoting"][$parentKey]["clarity"]["mean"] = array_sum($parent["content"]["statistics"]["majvoting"][$parentKey]["confidence"]["extractors"]) / $noConf;
                                    $parent["content"]["statistics"]["majvoting"][$parentKey]["clarity"]["stddev"] = stats_stddev($parent["content"]["statistics"]["majvoting"][$parentKey]["confidence"]["extractors"]);
                                    $parent["content"]["statistics"]["majvoting"][$parentKey]["clarity"]["mse"] = pow($parent["content"]["statistics"]["majvoting"][$parentKey]["clarity"]["stddev"], 2) / $noConf;
                                }
                                else {
                                    $parent["content"]["statistics"]["majvoting"][$parentKey]["clarity"]["mean"] = $parent["content"]["statistics"]["majvoting"][$parentKey]["confidence"]["value"];
                                    $parent["content"]["statistics"]["majvoting"][$parentKey]["clarity"]["stddev"] = stats_stddev($parent["content"]["statistics"]["majvoting"][$parentKey]["confidence"]["extractors"]);
                                    $parent["content"]["statistics"]["majvoting"][$parentKey]["clarity"]["mse"] = pow($parent["content"]["statistics"]["majvoting"][$parentKey]["clarity"]["stddev"], 2);
                                }

                                foreach ($childValue["types"] as $keyType => $valueType) {
                                    $foundType = false;
                                    foreach ($parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsPerType"] as $parentTypeKey => $parentTypeValue) {
                                        if (strtolower($parentTypeKey) == strtolower($valueType["typeURI"])) {
                                            if (!in_array($childValue["provenance"], $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsPerType"][$parentTypeKey]["extractors"])) {
                                                $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsPerType"][$parentTypeKey]["count"] += 1;
                                                array_push($parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsPerType"][$parentTypeKey]["extractors"], $childValue["provenance"]);           
                                                $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsPerType"][$parentTypeKey]["relevanceScore"]["value"] = $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsPerType"][$parentTypeKey]["count"] / $parent["content"]["statistics"]["majvoting"][$parentKey]["noExtractorsPerLabel"]["count"];
                                            }
                                            $foundType = true;
                                        }
                                        else {
                                            $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsPerType"][$parentTypeKey]["relevanceScore"]["value"] = $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsPerType"][$parentTypeKey]["count"] / $parent["content"]["statistics"]["majvoting"][$parentKey]["noExtractorsPerLabel"]["count"];
                                        }

                                        if ($foundType == true) {
                                            break;
                                        }
                                    }

                                    if ($foundType == false) {
                                        if ($valueType["typeURI"] != null) {
                                            $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsPerType"][$valueType["typeURI"]] = array();
                                            $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsPerType"][$valueType["typeURI"]]["count"] = 1;
                                            $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsPerType"][$valueType["typeURI"]]["extractors"] = array();
                                            array_push($parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsPerType"][$valueType["typeURI"]]["extractors"], $childValue["provenance"]);
                                            $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsPerType"][$valueType["typeURI"]]["relevanceScore"] = array();
                                            $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsPerType"][$valueType["typeURI"]]["relevanceScore"]["value"] = 1/ $parent["content"]["statistics"]["majvoting"][$parentKey]["noExtractorsPerLabel"]["count"];
                                        }
                                    }

                                    $foundResource = false;
                                    foreach ($parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsPerResource"] as $parentResourceKey => $parentResourceValue) {
                                        if (strtolower($parentResourceKey) == strtolower($valueType["entityURI"])) {
                                            if (!in_array($childValue["provenance"], $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsPerResource"][$parentResourceKey]["extractors"])) {
                                                $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsPerResource"][$parentResourceKey]["count"] += 1;
                                                array_push($parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsPerResource"][$parentResourceKey]["extractors"], $childValue["provenance"]);
                                                $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsPerResource"][$parentResourceKey]["relevanceScore"]["value"] = $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsPerResource"][$parentResourceKey]["count"] / $parent["content"]["statistics"]["majvoting"][$parentKey]["noExtractorsPerLabel"]["count"];
                                            }
                                            $foundResource = true;
                                        }
                                        else {
                                            $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsPerResource"][$parentResourceKey]["relevanceScore"]["value"] = $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsPerResource"][$parentResourceKey]["count"] / $parent["content"]["statistics"]["majvoting"][$parentKey]["noExtractorsPerLabel"]["count"];
                                        }

                                        if ($foundResource == true) {
                                            break;
                                        }
                                    }

                                    if ($foundResource == false) {
                                        if ($valueType["entityURI"] != null) {
                                            $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsPerResource"][$valueType["entityURI"]] = array();
                                            $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsPerResource"][$valueType["entityURI"]]["count"] = 1;
                                            $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsPerResource"][$valueType["entityURI"]]["extractors"] = array();
                                            array_push($parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsPerResource"][$valueType["entityURI"]]["extractors"], $childValue["provenance"]);
                                            $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsPerResource"][$valueType["entityURI"]]["relevanceScore"] = array();
                                            $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsPerResource"][$valueType["entityURI"]]["relevanceScore"]["value"] = 1/$parent["content"]["statistics"]["majvoting"][$parentKey]["noExtractorsPerLabel"]["count"];
                                        
                                        }
                                    }

                                    $foundLabelTypePair = false;
                                    $tempTypeValue = "";
                                    if ($valueType["typeURI"] != null) {
                                        $tempTypeValue = $valueType["typeURI"];
                                    }
                                    foreach ($parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelTypePair"] as $parentLabelTypeKey => $parentLabelTypeValue) {
                                        if (strtolower($parentLabelTypeKey) == strtolower($childValue["label"] . "-" . $tempTypeValue)) {
                                            if (!in_array($childValue["provenance"], $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelTypePair"][$parentLabelTypeKey]["extractors"])) {
                                                $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelTypePair"][$parentLabelTypeKey]["count"] += 1;
                                                array_push($parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelTypePair"][$parentLabelTypeKey]["extractors"], $childValue["provenance"]);
                                                $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelTypePair"][$parentLabelTypeKey]["relevanceScore"]["value"] = $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelTypePair"][$parentLabelTypeKey]["count"] / $parent["content"]["statistics"]["majvoting"][$parentKey]["noExtractorsPerLabel"]["count"];
                                            }
                                            $foundLabelTypePair = true;
                                        }
                                        else {
                                            $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelTypePair"][$parentLabelTypeKey]["relevanceScore"]["value"] = $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelTypePair"][$parentLabelTypeKey]["count"] / $parent["content"]["statistics"]["majvoting"][$parentKey]["noExtractorsPerLabel"]["count"];
                                        }

                                        if ($foundLabelTypePair == true) {
                                            break;
                                        }
                                    }

                                    if ($foundLabelTypePair == false) {
                                        $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelTypePair"][$childValue["label"] . "-" . $tempTypeValue] = array();
                                        $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelTypePair"][$childValue["label"] . "-" . $tempTypeValue]["count"] = 1;
                                        $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelTypePair"][$childValue["label"] . "-" . $tempTypeValue]["extractors"] = array();
                                        array_push($parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelTypePair"][$childValue["label"] . "-" . $tempTypeValue]["extractors"], $childValue["provenance"]);
                                        $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelTypePair"][$childValue["label"] . "-" . $tempTypeValue]["relevanceScore"] = array();
                                        $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelTypePair"][$childValue["label"] . "-" . $tempTypeValue]["relevanceScore"]["value"] = 1 / $parent["content"]["statistics"]["majvoting"][$parentKey]["noExtractorsPerLabel"]["count"];
                                    }

                                    /*
                                    $foundLabelResourcePair = false;
                                    $tempResourceValue = "";
                                    if ($valueType["entityURI"] != null) {
                                        $tempResourceValue = $valueType["entityURI"];
                                    }
                                    foreach ($parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelResourcePair"] as $parentLabelResourceKey => $parentLabelResourceValue) {
                            
                                        if (strtolower($parentLabelResourceKey) == strtolower($childValue["label"] . "-" . $tempResourceValue)) {
                                            if (!in_array($childValue["provenance"], $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelResourcePair"][$parentLabelResourceKey]["extractors"])) {
                                                //echo $parent["_id"];
                                                //dd($parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelResourcePair"]);
                                                $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelResourcePair"][$childValue["label"] . "-" . $tempResourceValue]["count"] += 1;
                                                array_push($parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelResourcePair"][$childValue["label"] . "-" . $tempResourceValue]["extractors"], $childValue["provenance"]);
                                                $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelResourcePair"][$childValue["label"] . "-" . $tempResourceValue]["relevanceScore"]["value"] = $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelResourcePair"][$childValue["label"] . "-" . $tempResourceValue]["count"] / $parent["content"]["statistics"]["majvoting"][$parentKey]["noExtractorsPerLabel"]["count"];
                                            }
                                            $foundLabelResourcePair = true;
                                        }
                                        else {
                                            $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelResourcePair"][$parentLabelResourceKey]["relevanceScore"]["value"] = $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelResourcePair"][$parentLabelResourceKey]["count"] / $parent["content"]["statistics"]["majvoting"][$parentKey]["noExtractorsPerLabel"]["count"];
                                        }

                                        if ($foundLabelResourcePair == true) {
                                            break;
                                        }
                                    }

                                    if ($foundLabelResourcePair == false) {
                                        $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelResourcePair"][$childValue["label"] . "-" . $tempResourceValue] = array();
                                        $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelResourcePair"][$childValue["label"] . "-" . $tempResourceValue]["count"] = 1;
                                        $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelResourcePair"][$childValue["label"] . "-" . $tempResourceValue]["extractors"] = array();
                                        array_push($parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelResourcePair"][$childValue["label"] . "-" . $tempResourceValue]["extractors"], $childValue["provenance"]);
                                        $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelResourcePair"][$childValue["label"] . "-" . $tempResourceValue]["relevanceScore"] = array();
                                        $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelResourcePair"][$childValue["label"] . "-" . $tempResourceValue]["relevanceScore"]["value"] = 1/$parent["content"]["statistics"]["majvoting"][$parentKey]["noExtractorsPerLabel"]["count"];
                                    }
                                    */

                                    $foundTypeResourcePair = false;
                                    $tempTypeValue = "";
                                    $tempResourceValue = "";
                                    if ($valueType["entityURI"] != null) {
                                        $tempResourceValue = $valueType["entityURI"];
                                    }
                                    if ($valueType["typeURI"] != null) {
                                        $tempTypeValue = $valueType["typeURI"];
                                    }
                                    foreach ($parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsTypeResourcePair"] as $parentLabelTypeKey => $parentLabelTypeValue) {

                                        if (strtolower($parentLabelTypeKey) == strtolower($tempTypeValue . "-" . $tempResourceValue)) {
                                            if (!in_array($childValue["provenance"], $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsTypeResourcePair"][$parentLabelTypeKey]["extractors"])) {
                                                $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsTypeResourcePair"][$parentLabelTypeKey]["count"] += 1;
                                                array_push($parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsTypeResourcePair"][$parentLabelTypeKey]["extractors"], $childValue["provenance"]);
                                                $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsTypeResourcePair"][$parentLabelTypeKey]["relevanceScore"]["value"] = $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsTypeResourcePair"][$parentLabelTypeKey]["count"] / $parent["content"]["statistics"]["majvoting"][$parentKey]["noExtractorsPerLabel"]["count"];
                                            }
                                            $foundTypeResourcePair = true;
                                        }
                                        else {
                                            $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsTypeResourcePair"][$parentLabelTypeKey]["relevanceScore"]["value"] = $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsTypeResourcePair"][$parentLabelTypeKey]["count"] / $parent["content"]["statistics"]["majvoting"][$parentKey]["noExtractorsPerLabel"]["count"];
                                        }

                                        if ($foundTypeResourcePair == true) {
                                            break;
                                        }
                                    }

                                    if ($foundTypeResourcePair == false) {
                                        $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsTypeResourcePair"][$tempTypeValue . "-" . $tempResourceValue] = array();
                                        $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsTypeResourcePair"][$tempTypeValue . "-" . $tempResourceValue]["count"] = 1;
                                        $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsTypeResourcePair"][$tempTypeValue . "-" . $tempResourceValue]["extractors"] = array();
                                        array_push($parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsTypeResourcePair"][$tempTypeValue . "-" . $tempResourceValue]["extractors"], $childValue["provenance"]);
                                        $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsTypeResourcePair"][$tempTypeValue . "-" . $tempResourceValue]["relevanceScore"] = array();
                                        $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsTypeResourcePair"][$tempTypeValue . "-" . $tempResourceValue]["relevanceScore"]["value"] = 1/$parent["content"]["statistics"]["majvoting"][$parentKey]["noExtractorsPerLabel"]["count"];
                                    }

                                    $foundLabelTypeResourcePair = false;
                                    foreach ($parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelTypeResourcePair"] as $parentLabelTypeResourceKey => $parentLabelTypeResourceValue) {
                                        if (strtolower($parentLabelTypeResourceKey) == strtolower($childValue["label"] . "-" . $tempTypeValue . "-" . $tempResourceValue)) {
                                            if (!in_array($childValue["provenance"], $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelTypeResourcePair"][$parentLabelTypeResourceKey]["extractors"])) {
                                                $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelTypeResourcePair"][$parentLabelTypeResourceKey]["count"] += 1;
                                                array_push($parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelTypeResourcePair"][$parentLabelTypeResourceKey]["extractors"], $childValue["provenance"]);
                                                $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelTypeResourcePair"][$parentLabelTypeResourceKey]["relevanceScore"]["value"] = $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelTypeResourcePair"][$parentLabelTypeResourceKey]["count"] / $parent["content"]["statistics"]["majvoting"][$parentKey]["noExtractorsPerLabel"]["count"];
                                            }
                                            $foundLabelTypeResourcePair = true;
                                        }
                                        else {
                                            $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelTypeResourcePair"][$parentLabelTypeResourceKey]["relevanceScore"]["value"] = $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelTypeResourcePair"][$parentLabelTypeResourceKey]["count"] / $parent["content"]["statistics"]["majvoting"][$parentKey]["noExtractorsPerLabel"]["count"];
                                        }

                                        if ($foundLabelTypeResourcePair == true) {
                                            break;
                                        }
                                    }

                                    if ($foundLabelTypeResourcePair == false) {
                                        $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelTypeResourcePair"][$childValue["label"] . "-" . $tempTypeValue . "-" . $tempResourceValue] = array();
                                        $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelTypeResourcePair"][$childValue["label"] . "-" . $tempTypeValue . "-" . $tempResourceValue]["count"] = 1;
                                        $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelTypeResourcePair"][$childValue["label"] . "-" . $tempTypeValue . "-" . $tempResourceValue]["extractors"] = array();
                                        array_push($parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelTypeResourcePair"][$childValue["label"] . "-" . $tempTypeValue . "-" . $tempResourceValue]["extractors"], $childValue["provenance"]);
                                        $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelTypeResourcePair"][$childValue["label"] . "-" . $tempTypeValue . "-" . $tempResourceValue]["relevanceScore"] = array();
                                        $parent['content']['statistics']['majvoting'][$parentKey]["noExtractorsLabelTypeResourcePair"][$childValue["label"] . "-" . $tempTypeValue . "-" . $tempResourceValue]["relevanceScore"]["value"] = 1/$parent["content"]["statistics"]["majvoting"][$parentKey]["noExtractorsPerLabel"]["count"];
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
                        //    $newEntity["relevanceScore"] = array();
                        //    $newEntity["relevanceScore"]["count"] = 1;
                        //    $newEntity["relevanceScore"]["value"] = 1/6;
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
                            $newEntity["clarity"]["stddev"] = stats_stddev($newEntity["confidence"]["extractors"]);
                            $newEntity["clarity"]["mse"] = pow($newEntity["clarity"]["stddev"], 2) / 1;
                            $newEntity["noExtractorsPerType"] = array();
                            $newEntity["noExtractorsPerResource"] = array();
                            $newEntity["noExtractorsLabelTypePair"] = array();
                            $newEntity["noExtractorsLabelResourcePair"] = array();
                            $newEntity["noExtractorsTypeResourcePair"] = array();
                            $newEntity["noExtractorsLabelTypeResourcePair"] = array();

                            foreach ($childValue["types"] as $keyType => $valueType) {
                                if ($valueType["typeURI"] != null) {
                                    $newEntity["noExtractorsPerType"][$valueType["typeURI"]] = array();
                                    $newEntity["noExtractorsPerType"][$valueType["typeURI"]]["count"] = 1;
                                    $newEntity["noExtractorsPerType"][$valueType["typeURI"]]["extractors"] = array();
                                    array_push($newEntity["noExtractorsPerType"][$valueType["typeURI"]]["extractors"], $childValue["provenance"]);
                                    $newEntity["noExtractorsPerType"][$valueType["typeURI"]]["relevanceScore"] = array();
                                    $newEntity["noExtractorsPerType"][$valueType["typeURI"]]["relevanceScore"]["value"] = 1;
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
                                if ($valueType["typeURI"] != null) {
                                    $tempTypeValue = $valueType["typeURI"];
                                }
                                if (!array_key_exists($childValue["label"] . "-" . $tempTypeValue, $newEntity["noExtractorsLabelTypePair"]) || 
                                    !array_key_exists(strtolower($childValue["label"] . "-" . $tempTypeValue), $newEntity["noExtractorsLabelTypePair"])) {
                                    $newEntity["noExtractorsLabelTypePair"][$childValue["label"] . "-" . $tempTypeValue] = array();
                                    $newEntity["noExtractorsLabelTypePair"][$childValue["label"] . "-" . $tempTypeValue]["count"] = 1;
                                    $newEntity["noExtractorsLabelTypePair"][$childValue["label"] . "-" . $tempTypeValue]["extractors"] = array();
                                    array_push($newEntity["noExtractorsLabelTypePair"][$childValue["label"] . "-" . $tempTypeValue]["extractors"], $childValue["provenance"]);
                                    $newEntity["noExtractorsLabelTypePair"][$childValue["label"] . "-" . $tempTypeValue]["relevanceScore"] = array();
                                    $newEntity["noExtractorsLabelTypePair"][$childValue["label"] . "-" . $tempTypeValue]["relevanceScore"]["value"] = 1;
                                }

                                $tempResourceValue = "";
                                if ($valueType["entityURI"] != null) {
                                    $tempResourceValue = $valueType["entityURI"];
                                }
                                if (!array_key_exists($childValue["label"] . "-" . $tempResourceValue, $newEntity["noExtractorsLabelResourcePair"]) ||
                                    !array_key_exists(strtolower($childValue["label"] . "-" . $tempResourceValue), $newEntity["noExtractorsLabelResourcePair"])) {
                                    $newEntity["noExtractorsLabelResourcePair"][$childValue["label"] . "-" . $tempResourceValue] = array();
                                    $newEntity["noExtractorsLabelResourcePair"][$childValue["label"] . "-" . $tempResourceValue]["count"] = 1;
                                    $newEntity["noExtractorsLabelResourcePair"][$childValue["label"] . "-" . $tempResourceValue]["extractors"] = array();
                                    array_push($newEntity["noExtractorsLabelResourcePair"][$childValue["label"] . "-" . $tempResourceValue]["extractors"], $childValue["provenance"]);
                                    $newEntity["noExtractorsLabelResourcePair"][$childValue["label"] . "-" . $tempResourceValue]["relevanceScore"] = array();
                                    $newEntity["noExtractorsLabelResourcePair"][$childValue["label"] . "-" . $tempResourceValue]["relevanceScore"]["value"] = 1;
                                }

                                if (!array_key_exists($tempTypeValue . "-" . $tempResourceValue, $newEntity["noExtractorsTypeResourcePair"]) || 
                                    !array_key_exists(strtolower($tempTypeValue . "-" . $tempResourceValue), $newEntity["noExtractorsTypeResourcePair"])) {
                                    $newEntity["noExtractorsTypeResourcePair"][$tempTypeValue . "-" . $tempResourceValue] = array();
                                    $newEntity["noExtractorsTypeResourcePair"][$tempTypeValue . "-" . $tempResourceValue]["count"] = 1;
                                    $newEntity["noExtractorsTypeResourcePair"][$tempTypeValue . "-" . $tempResourceValue]["extractors"] = array();
                                    array_push($newEntity["noExtractorsTypeResourcePair"][$tempTypeValue . "-" . $tempResourceValue]["extractors"], $childValue["provenance"]);
                                    $newEntity["noExtractorsTypeResourcePair"][$tempTypeValue . "-" . $tempResourceValue]["relevanceScore"] = array();
                                    $newEntity["noExtractorsTypeResourcePair"][$tempTypeValue . "-" . $tempResourceValue]["relevanceScore"]["value"] = 1;
                                }

                                if (!array_key_exists($childValue["label"] . "-" . $tempTypeValue . "-" . $tempResourceValue, $newEntity["noExtractorsLabelTypeResourcePair"]) || 
                                    !array_key_exists(strtolower($childValue["label"] . "-" . $tempTypeValue . "-" . $tempResourceValue), $newEntity["noExtractorsLabelTypeResourcePair"])) {
                                    $newEntity["noExtractorsLabelTypeResourcePair"][$childValue["label"] . "-" . $tempTypeValue . "-" . $tempResourceValue] = array();
                                    $newEntity["noExtractorsLabelTypeResourcePair"][$childValue["label"] . "-" . $tempTypeValue . "-" . $tempResourceValue]["count"] = 1;
                                    $newEntity["noExtractorsLabelTypeResourcePair"][$childValue["label"] . "-" . $tempTypeValue . "-" . $tempResourceValue]["extractors"] = array();
                                    array_push($newEntity["noExtractorsLabelTypeResourcePair"][$childValue["label"] . "-" . $tempTypeValue . "-" . $tempResourceValue]["extractors"], $childValue["provenance"]);
                                    $newEntity["noExtractorsLabelTypeResourcePair"][$childValue["label"] . "-" . $tempTypeValue . "-" . $tempResourceValue]["relevanceScore"] = array();
                                    $newEntity["noExtractorsLabelTypeResourcePair"][$childValue["label"] . "-" . $tempTypeValue . "-" . $tempResourceValue]["relevanceScore"]["value"] = 1;
                                }
                            }
                            array_push($parent['content']['statistics']['majvoting'], $newEntity);
                            array_push($parent['content']['features']['entities'], $extractedEntity);
                         //   dd($parent['content']['statistics']['entities']);
                        }
                //    dd($parent['content']['statistics']['entities']);

                    }
                }

                $parent['content']['statistics']["crowdtruth"]["cosineSimilarityAllLabels"] = array();

                $extractors = array("thd", "nerd", "textrazor", "lupedia", "semitags", "dbpediaspotlight");
                if (count($parent['content']['statistics']['majvoting']) != 0) {
                    $pivotTable = array();
                    $cosineSimilarity = array();

                    for ($j = 0; $j < sizeof($extractors); $j ++) {
                        $pivotTable[$extractors[$j]] = array();
                        $cosineSimilarity[$extractors[$j]] = array();
                        for ($i = 0; $i < count($parent["content"]["statistics"]["majvoting"]); $i ++) {
                            $pivotTable[$extractors[$j]][$parent["content"]["statistics"]["majvoting"][$i]["label"] . '-' . $parent["content"]["statistics"]["majvoting"][$i]["startOffset"] . '-' . $parent["content"]["statistics"]["majvoting"][$i]["endOffset"]] = 0;
                        }
                    }

                    foreach ($parent["content"]["statistics"]["majvoting"] as $keyEntity => $keyValue) {
                        foreach ($keyValue["noExtractorsPerLabel"]["extractors"] as $keyExtractor => $valueExtractor) {
                            $pivotTable[$valueExtractor][$keyValue["label"] . '-' . $keyValue["startOffset"] . '-' . $keyValue["endOffset"]] = 1;
                        }
                    }

                    foreach ($extractors as $extractorName) {
                        $cosineSimilarity[$extractorName] = array();
                        $sumArray = sumUpArrays($pivotTable);
                        $diffArray = extractArrays($sumArray, $pivotTable[$extractorName]);
                         //  dd($pivotTable[$key][$extractorName]);
                        $cosineSimilarity[$extractorName] = cosinus($pivotTable[$extractorName], $diffArray);
                    }

                    $parent['content']['statistics']["crowdtruth"]["cosineSimilarityAllLabels"] = $cosineSimilarity;

                    $parent['content']['statistics']['crowdtruth']['entities'] = array();

                    foreach ($parent['content']['statistics']['majvoting'] as $key => $value) {
                        $labelDetails = $value["label"] . '-' . $value["startOffset"] . '-' . $value["endOffset"];
                        $parent['content']['statistics']['crowdtruth']['entities'][$key] = array();
                        $parent['content']['statistics']['crowdtruth']['entities'][$key]["label"] = $value["label"];
                        $parent['content']['statistics']['crowdtruth']['entities'][$key]["startOffset"] = $value["startOffset"];
                        $parent['content']['statistics']['crowdtruth']['entities'][$key]["endOffset"] = $value["endOffset"];

                        $parent['content']['statistics']['crowdtruth']['entities'][$key]["cosineSimilarityPerType"] = array();

                        $pivotTable = array();
                        $cosineSimilarity = array();
                        $extractors = $parent['content']['statistics']['majvoting'][$key]["noExtractorsPerLabel"]["extractors"];

                        for ($j = 0; $j < sizeof($extractors); $j ++) {
                            $pivotTable[$extractors[$j]] = array();
                            $cosineSimilarity[$extractors[$j]] = array();
                        }
                            
                        foreach ($parent["content"]["statistics"]["majvoting"][$key]["noExtractorsPerType"] as $typeKey => $typeValue ) {
                            foreach ($extractors as $extractorName) {
                                $pivotTable[$extractorName][$typeKey] = 0;
                            }
                        }

                        foreach ($parent["content"]["statistics"]["majvoting"][$key]["noExtractorsPerType"] as $typeKey => $typeValue) {
                            foreach ($typeValue["extractors"] as $keyExtractor => $valueExtractor) {
                                $pivotTable[$valueExtractor][$typeKey] = 1;
                            }
                        }

                        foreach ($extractors as $extractorName) {
                            $cosineSimilarity[$extractorName] = array();
                            $sumArray = sumUpArrays($pivotTable);
                            $diffArray = extractArrays($sumArray, $pivotTable[$extractorName]);
                            $cosineSimilarity[$extractorName] = cosinus($pivotTable[$extractorName], $diffArray);
                        }

                        $parent['content']['statistics']["crowdtruth"]["entities"][$key]["cosineSimilarityPerType"] = $cosineSimilarity;


                        $parent['content']['statistics']['crowdtruth']['entities'][$key]["cosineSimilarityPerResource"] = array();

                        $pivotTable = array();
                        $cosineSimilarity = array();
                    
                        for ($j = 0; $j < sizeof($extractors); $j ++) {
                            $pivotTable[$extractors[$j]] = array();
                            $cosineSimilarity[$extractors[$j]] = array();
                        }
                            
                        foreach ($parent["content"]["statistics"]["majvoting"][$key]["noExtractorsPerResource"] as $resourceKey => $resourceValue ) {
                            foreach ($extractors as $extractorName) {
                                $pivotTable[$extractorName][$resourceKey] = 0;
                            }
                        }

                        foreach ($parent["content"]["statistics"]["majvoting"][$key]["noExtractorsPerResource"] as $resourceKey => $resourceValue) {
                            foreach ($resourceValue["extractors"] as $keyExtractor => $valueExtractor) {
                                $pivotTable[$valueExtractor][$resourceKey] = 1;
                            }
                        }

                        foreach ($extractors as $extractorName) {
                            $cosineSimilarity[$extractorName] = array();
                            $sumArray = sumUpArrays($pivotTable);
                            $diffArray = extractArrays($sumArray, $pivotTable[$extractorName]);
                            $cosineSimilarity[$extractorName] = cosinus($pivotTable[$extractorName], $diffArray);
                        }

                        $parent['content']['statistics']["crowdtruth"]["entities"][$key]["cosineSimilarityPerResource"] = $cosineSimilarity;


                        $parent['content']['statistics']['crowdtruth']['entities'][$key]["cosineSimilarityPerLabelTypePair"] = array();

                        $pivotTable = array();
                        $cosineSimilarity = array();
                    
                        for ($j = 0; $j < sizeof($extractors); $j ++) {
                            $pivotTable[$extractors[$j]] = array();
                            $cosineSimilarity[$extractors[$j]] = array();
                        }
                            
                        foreach ($parent["content"]["statistics"]["majvoting"][$key]["noExtractorsLabelTypePair"] as $resourceKey => $resourceValue ) {
                            foreach ($extractors as $extractorName) {
                                $pivotTable[$extractorName][$resourceKey] = 0;
                            }
                        }

                        foreach ($parent["content"]["statistics"]["majvoting"][$key]["noExtractorsLabelTypePair"] as $resourceKey => $resourceValue) {
                            foreach ($resourceValue["extractors"] as $keyExtractor => $valueExtractor) {
                                $pivotTable[$valueExtractor][$resourceKey] = 1;
                            }
                        }

                        foreach ($extractors as $extractorName) {
                            $cosineSimilarity[$extractorName] = array();
                            $sumArray = sumUpArrays($pivotTable);
                            $diffArray = extractArrays($sumArray, $pivotTable[$extractorName]);
                            $cosineSimilarity[$extractorName] = cosinus($pivotTable[$extractorName], $diffArray);
                        }

                        $parent['content']['statistics']["crowdtruth"]["entities"][$key]["cosineSimilarityPerLabelTypePair"] = $cosineSimilarity;


                        $parent['content']['statistics']['crowdtruth']['entities'][$key]["cosineSimilarityPerLabelResourcePair"] = array();

                        $pivotTable = array();
                        $cosineSimilarity = array();
                    
                        for ($j = 0; $j < sizeof($extractors); $j ++) {
                            $pivotTable[$extractors[$j]] = array();
                            $cosineSimilarity[$extractors[$j]] = array();
                        }
                            
                        foreach ($parent["content"]["statistics"]["majvoting"][$key]["noExtractorsLabelResourcePair"] as $resourceKey => $resourceValue ) {
                            foreach ($extractors as $extractorName) {
                                $pivotTable[$extractorName][$resourceKey] = 0;
                            }
                        }

                        foreach ($parent["content"]["statistics"]["majvoting"][$key]["noExtractorsLabelResourcePair"] as $resourceKey => $resourceValue) {
                            foreach ($resourceValue["extractors"] as $keyExtractor => $valueExtractor) {
                                $pivotTable[$valueExtractor][$resourceKey] = 1;
                            }
                        }

                        foreach ($extractors as $extractorName) {
                            $cosineSimilarity[$extractorName] = array();
                            $sumArray = sumUpArrays($pivotTable);
                            $diffArray = extractArrays($sumArray, $pivotTable[$extractorName]);
                            $cosineSimilarity[$extractorName] = cosinus($pivotTable[$extractorName], $diffArray);
                        }

                        $parent['content']['statistics']["crowdtruth"]["entities"][$key]["cosineSimilarityPerLabelResourcePair"] = $cosineSimilarity;



                        $parent['content']['statistics']['crowdtruth']['entities'][$key]["cosineSimilarityPerTypeResourcePair"] = array();

                        $pivotTable = array();
                        $cosineSimilarity = array();
                    
                        for ($j = 0; $j < sizeof($extractors); $j ++) {
                            $pivotTable[$extractors[$j]] = array();
                            $cosineSimilarity[$extractors[$j]] = array();
                        }
                            
                        foreach ($parent["content"]["statistics"]["majvoting"][$key]["noExtractorsTypeResourcePair"] as $resourceKey => $resourceValue ) {
                            foreach ($extractors as $extractorName) {
                                $pivotTable[$extractorName][$resourceKey] = 0;
                            }
                        }

                        foreach ($parent["content"]["statistics"]["majvoting"][$key]["noExtractorsTypeResourcePair"] as $resourceKey => $resourceValue) {
                            foreach ($resourceValue["extractors"] as $keyExtractor => $valueExtractor) {
                                $pivotTable[$valueExtractor][$resourceKey] = 1;
                            }
                        }

                        foreach ($extractors as $extractorName) {
                            $cosineSimilarity[$extractorName] = array();
                            $sumArray = sumUpArrays($pivotTable);
                            $diffArray = extractArrays($sumArray, $pivotTable[$extractorName]);
                            $cosineSimilarity[$extractorName] = cosinus($pivotTable[$extractorName], $diffArray);
                        }

                        $parent['content']['statistics']["crowdtruth"]["entities"][$key]["cosineSimilarityPerTypeResourcePair"] = $cosineSimilarity;


                        $parent['content']['statistics']['crowdtruth']['entities'][$key]["cosineSimilarityPerLabelTypeResourcePair"] = array();

                        $pivotTable = array();
                        $cosineSimilarity = array();
                    
                        for ($j = 0; $j < sizeof($extractors); $j ++) {
                            $pivotTable[$extractors[$j]] = array();
                            $cosineSimilarity[$extractors[$j]] = array();
                        }
                            
                        foreach ($parent["content"]["statistics"]["majvoting"][$key]["noExtractorsLabelTypeResourcePair"] as $resourceKey => $resourceValue ) {
                            foreach ($extractors as $extractorName) {
                                $pivotTable[$extractorName][$resourceKey] = 0;
                            }
                        }

                        foreach ($parent["content"]["statistics"]["majvoting"][$key]["noExtractorsLabelTypeResourcePair"] as $resourceKey => $resourceValue) {
                            foreach ($resourceValue["extractors"] as $keyExtractor => $valueExtractor) {
                                $pivotTable[$valueExtractor][$resourceKey] = 1;
                            }
                        }

                        foreach ($extractors as $extractorName) {
                            $cosineSimilarity[$extractorName] = array();
                            $sumArray = sumUpArrays($pivotTable);
                            $diffArray = extractArrays($sumArray, $pivotTable[$extractorName]);
                            $cosineSimilarity[$extractorName] = cosinus($pivotTable[$extractorName], $diffArray);
                        }

                        $parent['content']['statistics']["crowdtruth"]["entities"][$key]["cosineSimilarityPerLabelTypeResourcePair"] = $cosineSimilarity;



                    }



                }

        }
            

            try {
                \MongoDB\Temp::where('documentType', '=', 'metadatadescription')->forceDelete();

                $db->batchInsert(
                    $result,
                    array('continueOnError' => true)
                );             
            } catch (Exception $e) {
            // ContinueOnError will still throw an exception on duplication, even though it continues, so we just move on.
            }
       //     dd("done");
       
        }
        \Session::forget('rawArray');
        
            //    print_r($parent["content"]); 
    }


    public static function createImageCache()
    {
        \Session::flash('rawArray', 1);
        $db = \DB::getMongoDB();
        $db = $db->temp;

        // \Queue::push('Queues\UpdateUnits', \MongoDB\Entity::whereIn('documentType', ['painting', 'drawing', 'picture'])->lists('_id'));

        $result = \MongoDB\Entity::whereIn('documentType', ['painting', 'drawing', 'picture'])->get()->toArray();

        if(count($result) > 0)
        {
            foreach($result as &$parent)
            {
                $children = \MongoDB\Entity::whereIn('parents', [$parent['_id']])->get(['recognizedFeatures', 'content.features'])->toArray();



                $parent['content']['features'] = [];
                $parent['totalRelevantFeatures'] = 0;

                foreach($children as $child){

                    if(isset($child['recognizedFeatures']))
                    {
                        $parent['totalRelevantFeatures'] = $parent['totalRelevantFeatures'] + count($child['recognizedFeatures']);
                    }

                    
                     if(isset($child['content']['features']))
                    {
                        if(is_array($child['content']['features'])){
                             foreach($child['content']['features'] as $k => $v){
                                if (array_key_exists($k, $parent['content']['features'])){
                                        if(! is_array($parent['content']['features'][$k])) 
                                            $parent['content']['features'][$k] = [$parent['content']['features'][$k]];

                                        if(is_array($v)){
                                            foreach($v as $vit)
                                                array_push($parent['content']['features'][$k], $vit);
                                        }
                                }
                                else 
                                {
                                    $parent['content']['features'][$k] = $v;
                                }
                            }                            
                        }                  
                    }



                    // $featureKey = key($child['content']['features']);

                    // if(!isset($parent['content']['features'][$featureKey]))
                    // {
                    //     $parent['content']['features'][$featureKey] = [];

                    //     if(is_array($child['content']['features'][$featureKey]))
                    //     {
                    //         foreach($child['content']['features'][$featureKey] as $k => $v)
                    //         {
                    //             $parent['content']['features'][$featureKey][$k] = $v;
                    //         }                           
                    //     } 

                    // }
                    // else {
                    //     if(is_array($child['content']['features'][$featureKey]))
                    //     {
                    //         foreach($child['content']['features'][$featureKey] as $k => $v)
                    //         {
                    //             $parent['content']['features'][$featureKey][$k] = $v;
                    //         }
                    //     } 
                    //     else 
                    //     {
                    //         $parent['content']['features'][$featureKey] = $child['content']['features'][$featureKey];
                    //     }
                    // }
                }
            }

            try {
                \MongoDB\Temp::whereIn('documentType', ['painting', 'drawing', 'picture'])->forceDelete();

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

    public static function getDistinctFieldLabelAndCount($field, $tags = null)
    {
        if($tags == null)
        {
            $distinctFieldValues = array_flatten(Entity::distinct($field)->get()->toArray());
        }
        else
        {
            $distinctFieldValues = array_flatten(Entity::whereIn('tags', $tags)->distinct('documentType', 'domain')->get()->toArray());
        }

        $distinctFieldValuesAndCount = array();

        foreach($distinctFieldValues as $distinctFieldValue)
        {
            $distinctFieldValuesAndCount[$distinctFieldValue]['label'] = $distinctFieldValue;

            if(isset(\MongoDB\Entity::getKeyLabelMapping()[strtolower($distinctFieldValue)])) {
                $distinctFieldValuesAndCount[$distinctFieldValue]['label'] = \MongoDB\Entity::getKeyLabelMapping()[$distinctFieldValue];
            }

            $distinctFieldValuesAndCount[$distinctFieldValue]['count'] = Entity::where($field, $distinctFieldValue)->count();
        }

        return $distinctFieldValuesAndCount;
    }
	
    public static function createMainSearchFiltersCache()
    {
        // $mainSearchFilters['media']['formats'] = $this->getDistinctFieldAndCount('format', ['unit']);
        // $mainSearchFilters['media']['domains'] = $this->getDistinctFieldAndCount('domain', ['unit']);
        $mainSearchFilters['media']['documentTypes'] = static::getDistinctFieldLabelAndCount('documentType', ['unit']);
        
		// All units
		$mainSearchFilters['media']['documentTypes']['all'] = ["count" => \MongoDB\Entity::whereIn('tags', ['unit'])->count(),
                                                                "label" => "All units"
                                                                ];
        
        unset($mainSearchFilters['media']['documentTypes']['twrex']);

        $mainSearchFilters['job']['count'] = Entity::where('documentType', 'job')->count();
        $mainSearchFilters['workers']['count'] = \MongoDB\CrowdAgent::all()->count();

        ksort($mainSearchFilters['media']['documentTypes']);

        $entity = new \MongoDB\Temp;
        $entity->_id = "mainSearchFilters";
        $entity->filters = $mainSearchFilters;
        $entity->save();

        return $entity->toArray();
    }

    public static function getMainSearchFiltersCache()
    {
        if(!\MongoDB\Temp::where('_id', 'mainSearchFilters')->first())
        {
            static::createImageCache();
            static::createJobCache();
        //    static::createStatisticsForMetadatadescriptionCache();
        //    static::createMetadatadescriptionCache();
            return static::createMainSearchFiltersCache();
        }

        return \MongoDB\Temp::where('_id', 'mainSearchFilters')->first()->toArray();
    }

    public static function createJobCache()
    {        
        \Session::flash('rawArray', 1);
        $db = \DB::getMongoDB();
        $db = $db->temp;

        $result = \MongoDB\Entity::where('documentType', 'job')->with('hasConfiguration')->get()->toArray();

        if(count($result) > 0)
        {
            try {
                \MongoDB\Temp::where('documentType', 'job')->forceDelete();

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
