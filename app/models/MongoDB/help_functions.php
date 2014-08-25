<?php

function cmp($a, $b) {
    if ($a["startOffset"] == $b["startOffset"]) {
        return 0;
    }
    return ($a["startOffset"] < $b["startOffset"]) ? -1 : 1;
}     

function norm($vector) {
    return sqrt(dotProduct($vector, $vector));
}

function dotProduct($a, $b) {
    $dotProduct = 0;
    foreach ($a as $key => $val) {
        if (!empty($a[$key]) && !empty($b[$key])) {
            $dotProduct += $a[$key] * $b[$key];
        }
    }
    return $dotProduct;
}

function cosinus($a, $b) {
    $normA = norm($a);
    $normB = norm($b);

    return (($normA * $normB) != 0)
           ? dotProduct($a, $b) / ($normA * $normB)
           : 0;
}

function sumUpArrays($allArrays) {
    $sum = array();

    foreach ($allArrays as $arrayVal) {
        $j = 0;
        foreach ($arrayVal as $key => $val) { 
            $sum[$key] = 0;
            $j ++;
        }
        break;
    }

    foreach ($allArrays as $extrName => $extrValues) {
        $j = 0;
        foreach ($extrValues as $key => $val) {
            $sum[$key] = $sum[$key] + $val;
            $j ++;
        }
        
    }
//  dd($sum);
    return $sum;
}

function extractArrays($sumArray, $referenceArray) {
    $result = array();

    foreach($sumArray as $key => $value)  {
        $result[$key] = 0;
    }

    $i = 0;
    foreach ($sumArray as $key => $value) {
        $result[$key] = $sumArray[$key] - $referenceArray[$key];
        $i ++;
    }
//  dd($result);
    return $result;
}    

function stats_stddev($a) {
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
