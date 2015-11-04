<?php
/**
 * Created by PhpStorm.
 * User: zenus@github.com
 * Date: 2015/11/4
 * Time: 15:54
 */
function ngx_strcmp($s1, $s2){
   return strcmp($s1,$s2);
}
function ngx_strhas($s,$i){
   return isset($s[$i])&&!empty($s[$i]);
}
