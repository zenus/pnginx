<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 15-12-16
 * Time: 下午11:18
 * @param ngx_queue_t $accept_event
 * @return ngx_queue_t
 */
function  ngx_posted_accept_events($accept_event = null){
    static $ngx_posted_accept_events = null;
    if(!is_null($accept_event)){
        $ngx_posted_accept_events = $accept_event;
    }else{
        return $ngx_posted_accept_events;
    }

}
function ngx_posted_events ($posted_events = null){
    static $ngx_posted_events = null;
    if(!is_null($posted_events)){
       $ngx_posted_events = $posted_events;
    }else{
       return $ngx_posted_events;
    }
}

function ngx_delete_posted_event(ngx_event_t $ev){

    $ev->posted = 0;
    //ngx_queue_remove(&(ev)->queue);                                           \
    $ev->queue->shift();
    ngx_log_debug1(NGX_LOG_DEBUG_CORE, $ev->log, 0, "delete posted event %p", $ev);
}


function ngx_event_process_posted(ngx_cycle_t $cycle, ngx_queue_t $posted)
{
//ngx_queue_t  *q;
//    ngx_event_t  *ev;

    while ($posted->valid()) {

//        q = ngx_queue_head(posted);
//        ev = ngx_queue_data(q, ngx_event_t, queue);
        $ev = $posted->current();

        ngx_log_debug1(NGX_LOG_DEBUG_EVENT, $cycle->log, 0,
                      "posted event %p", $ev);

        ngx_delete_posted_event($ev);
        $ev->handler($ev);
        $posted->next();
    }

}