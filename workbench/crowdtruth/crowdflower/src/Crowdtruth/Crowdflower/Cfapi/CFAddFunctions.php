<?php

/**
* Convert an object to an array for easier usage.
* @param object $obj
* @return array result 
*/
function objectToArray($obj) {
        if (is_object($obj)) {
                $obj = get_object_vars($obj);
        }
        if (is_array($obj)) {
                return array_map(__FUNCTION__, $obj);
        }
        else {
                return $obj;
        }
}

/**
* Add string prefix to array keys.
* @param Array $data
* @param string $prefix
* @return Array $prefixedData
*/
function prefixData($data, $prefix) {
      $prefixedData = array();
      foreach ($data as $key => $value) {
          $newkey = "$prefix" . '[' . $key . ']';
          $prefixedData[$newkey] = $value;
      }
      return $prefixedData;
}



?>
