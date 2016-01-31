<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 15-11-14
 * Time: 下午11:30
 */

class ngx_queue_t  extends SplDoublyLinkedList{

}

//function ngx_queue_init($obj){
//    $queue = new ngx_queue_t();
//    return $obj = $queue;
//}

/* the stable insertion sort */

function ngx_queue_sort(ngx_queue_t $queue,
    callable $cmp)
{
//    ngx_queue_t  *q, *prev, *next;

//    q = ngx_queue_head(queue);
//
//    if (q == ngx_queue_last(queue)) {
//        return;
//    }
    $count = $queue->count();
    if($count <= 1){
       return;
    }

    for ( $queue->rewind(); $queue->valid(); $queue->next()) {

        $q = $queue->current();
        $k = $queue->key();
        {
            //prev = ngx_queue_prev(q);
//            $queue->prev();
            $prev = $k < 1 ? null : $queue->offsetGet(($k-1));
//            $prev = $queue->current();
//            $queue->next();
        }


        //ngx_queue_remove($q);
        $queue->offsetUnset($k);
        $pk = $k -1;

        do {
            if ($cmp($prev, $q) <= 0) {
                break;
            }

            $prev = ($pk < 1) ? null : $queue->offsetGet(($pk-1));

        } while ($pk);

        $queue->offsetSet($k-1,$prev);
    }
}

function ngx_queue_split(ngx_queue_t &$h, $q, ngx_queue_t &$n){
    $kk = 0;
    for ($h->rewind(); $h->valid(); $h->next()) {
        $v = $h->current();
        $k = $h->key();
        if($v == $q){
           $kk = $k;
        }
        if($kk > 0){
            $h->offsetUnset($k);
            $n->push($v);
        }
    }
}

function ngx_queue_add(ngx_queue_t &$h, ngx_queue_t $n){
    for($n->rewind(); $n->valid(); $n->current()){
        $h->push($n->current());
    }
}



