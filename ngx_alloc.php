<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 16-1-26
 * Time: 上午7:37
 */

function  ngx_pagesize($i = null){

    static $ngx_pagesize ;
    if(!is_null($i)){
       $ngx_pagesize = $i ;
    }else{
       return $ngx_pagesize;
    }
}