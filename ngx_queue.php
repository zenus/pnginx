<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 15-11-14
 * Time: 下午11:30
 */

class ngx_queue_t  extends SplDoublyLinkedList{

}

function ngx_queue_init($obj){
    $queue = new ngx_queue_t();
    return $obj = $queue;
}
