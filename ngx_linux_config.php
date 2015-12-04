<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 15-12-4
 * Time: 下午11:03
 * @param null $env
 * @return array|null
 */
function environ($env = null){
   static $environ = array();
    if(!is_null($env)){
        if(is_array($env)){
           $environ = $env ;
        }
        if(is_string($env)){
           $environ[] = $env;
        }
    }else{
       return $environ;
    }
}