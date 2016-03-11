<?php

namespace Api\shankey;

use \BaseController as BaseController;
use \Input as Input;
use \URL as URL;
use \Response as Response;

use \MongoDB\Repository as Repository;
use \MongoDB\Entity as Entity;
use \MongoDB\Activity as Activity;
use \MongoDB\SoftwareAgent as SoftwareAgent;
use \MongoDB\CrowdAgent as CrowdAgent;
use \MongoDate as MongoDate;

class apiController extends BaseController {

  

  protected $repository;

  public function __construct(Repository $repository){
    $this->repository = $repository;
  }

  public function getIndex(){
    ini_set('max_execution_time', 300);
    $c = 'Entity';
    $user = Input::get('user');
    $nodes = array();
    $relations = array();

    $collection = $this->repository->returnCollectionObjectFor($c);

    $initialData = $this -> getinitialData($collection, $user);



    array_push($nodes, $initialData);
    $next = $this->getNextNodeLevel($nodes,$collection);
    while(count($next['level']['data'])>0){

        
      array_push($nodes, $next['level']);
      $relations = array_merge($relations, $next['relations']);  
      $next = $this->getNextNodeLevel($nodes,$collection);

    }
    
    return Response::json([
      'nodes' => $nodes,
      'relations' => $relations
      ]);


  }





  protected function getinitialData($collection, $user) {

    if($user){
      $userInitCollection = $collection->where('user_id', $user);
    }else{
      $userInitCollection = $collection;
    }

    $initRaw = $userInitCollection->where(function($query){
      $query->where('activity_id','like', '%fileuploader%')
        ->orWhere('activity_id','like', '%imagegetter%')
        ->orWhere('activity_id', 'like', '%openimagesgetter%');
      })->get(array('_id', 'created_at'));

    // $initRaw = $userInitCollection->where(function($query){
    //  $query->where('_id', 'video_1');
    //  })->get(array('_id', 'created_at'));
    $initData = $this -> groupbyDate($initRaw);
    return array(
      'id' => 0,
      'data' => $initData);


  }

  protected function getNextNodeLevel($nodes, $collection){
    
    $rawData = array();
    $lastNodeLevel = end($nodes);
    foreach ($lastNodeLevel['data'] as $index => $node) {
      $nodeChildren = array(
        'parentnode' => $lastNodeLevel['id'].'_'.$index ,
        'entities' => array());
      foreach($node['entities'] as $entity_id){

        $children = $this->getChildren($entity_id, $collection);
        
        $nodeChildren['entities'] = array_merge($nodeChildren['entities'], $children);
        
      }
      array_push($rawData, $nodeChildren);
    }



    $nextRawLevel = array(
      'id' => $lastNodeLevel['id'] + 1,
      'rawData' => $rawData);

    $nextLevelandRel = $this-> processRawData($nextRawLevel, $nodes);

    return $nextLevelandRel;

  //  return $nextRawLevel;


  }

  protected function groupbyDate($rawData) {

    $result = array();

    foreach ($rawData as $value) {

      $found = FALSE;

      foreach ($result as &$target){
        if($this->isGroupableByTime(strtotime($value['created_at']),$target['timestamp'])){
          $found = TRUE;
          array_push($target['entities'],$value['_id']);
          break;
        }

        
      }

      if ($found == FALSE){

        array_push($result, array(
          'id'=> count($result),
          'timestamp'=>strtotime($value['created_at']),
          'entities'=>array($value['_id'])));

      }
    }

    return $result;


  }


  protected function getChildren($id, $collection){

    return $collection->where(function($query) use($id){
      $query->where('parents', $id)
        ->orWhere('batch_id', $id)
        ->orWhere('job_id', $id)
        ->whereRaw(array('_id' => array('$not' => new \MongoRegex("/workerunit/"))));

    })->get(array('_id', 'created_at'))->toArray();



  }

  protected function isGroupableByTime($d1, $d2){



    if (abs($d1 - $d2) < 6000){
      return TRUE;
    }else{
      return FALSE;
    }
  }


  protected function checkNodesForEntity($nodes, $resultLevel, $id){
    foreach ($nodes as $level){
      foreach($level['data'] as $node){
        if (in_array($id, $node['entities'] )){
          return $level['id'].'_'.$node['id'];
        }
      }
    }

    foreach($resultLevel['data'] as $resnode){
      if(in_array($id, $resnode['entities'])){
        return $resultLevel['id'].'_'.$resnode['id'];
      }
    }

    return null;

  }

  protected function processRawData($rawLevel, $nodes){

    $resultLevel = array(
      'id' => $rawLevel['id'],
      'data' => array() );

    $resultRelations = array();

    foreach ($rawLevel['rawData'] as $entitygroup){
    
      foreach ($entitygroup['entities'] as $entity) {


        $targetgroup = $this->checkNodesForEntity($nodes, $resultLevel, $entity['_id']);
        $found = FALSE;
        if ($targetgroup){
          $relID = $entitygroup['parentnode'].'-'.$targetgroup;
          $found = TRUE;
          if (array_key_exists($relID , $resultRelations) ){

            $resultRelations[$relID] ++;

          }else{
            $resultRelations[$relID] = 1;
          }

          
        }else{


        

          foreach ($resultLevel['data'] as &$resultgroup) {


            
            if ($this->isGroupableByTime(strtotime($entity['created_at']), $resultgroup['timestamp'])){

              array_push($resultgroup['entities'], $entity['_id']);

              $relID = $entitygroup['parentnode'].'-'.$rawLevel['id'].'_'.$resultgroup['id'];

              if (array_key_exists($relID, $resultRelations) ){

                $resultRelations[$relID] ++;

              }else{
                $resultRelations[$relID] = 1;
              }

              $found = TRUE;
              break;

            }

          }

          if ($found == FALSE){

            $id = count($resultLevel['data']);

            array_push( $resultLevel['data']  , array( 'id' => $id,
                                  'timestamp' => strtotime($entity['created_at']),
                                  // 'timestamp' => strtotime('5/5/1999'),
                                  'entities' => array($entity['_id'])));


            $relID = $entitygroup['parentnode'].'-'.$rawLevel['id'].'_'.$id;
            if (array_key_exists($relID , $resultRelations) ){

              $resultRelations[$relID] ++;

            }else{
                $resultRelations[$relID] = 1;
            }


          }

        }


        
      }

    }

    foreach ($resultLevel['data'] as &$entities) {
      $entities['entities'] = array_unique($entities['entities']);
    }
    return array(
      'level' => $resultLevel,
      'relations' => $resultRelations);


  }

}