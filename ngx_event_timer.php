<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 15-12-3
 * Time: 下午11:06
 */
define('NGX_TIMER_LAZY_DELAY',300);
define('NGX_TIMER_INFINITE',-1);

function ngx_event_add_timer(ngx_event_t $ev, $timer){

//    ngx_msec_t      key;
//    ngx_msec_int_t  diff;
//
    $key = ngx_current_msec() + $timer;

    if ($ev->timer_set) {

        /*
         * Use a previous timer value if difference between it and a new
         * value is less than NGX_TIMER_LAZY_DELAY milliseconds: this allows
         * to minimize the rbtree operations for fast connections.
         */

        $diff = $key - $ev->timer;

        if (ngx_abs($diff) < NGX_TIMER_LAZY_DELAY) {
            ngx_log_debug3(NGX_LOG_DEBUG_EVENT, $ev->log, 0,
                           "event timer: %d, old: %M, new: %M",
                            ngx_event_ident($ev->data), $ev->timer, $key);
            return;
        }
        ngx_del_timer($ev);
    }
//
    $ev->timer = $key;

    ngx_log_debug3(NGX_LOG_DEBUG_EVENT, $ev->log, 0,
                   "event timer add: %d: %M:%M",
                    ngx_event_ident($ev->data), $timer, $ev->timer);

    $base = event_base();
    $event = event_new();
    //todo set time_out default
    event_set($event,$ev->data->fd,EV_TIMEOUT,$ev->handler);
    //ngx_rbtree_insert(&ngx_event_timer_rbtree, &ev->timer);
    event_add($event, $timer);
    event_base_loop($base);

    $ev->timer_set = 1;
}


//todo should complete event method
function ngx_event_find_timer()
{
////ngx_msec_int_t      timer;
////    ngx_rbtree_node_t  *node, *root, *sentinel;
//
//    if (ngx_event_timer_rbtree.root == &ngx_event_timer_sentinel) {
//        return NGX_TIMER_INFINITE;
//    }
//
//    root = ngx_event_timer_rbtree.root;
//    sentinel = ngx_event_timer_rbtree.sentinel;
//
//    node = ngx_rbtree_min(root, sentinel);
//
//    timer = (ngx_msec_int_t) (node->key - ngx_current_msec);
//
//    return (ngx_msec_t) (timer > 0 ? timer : 0);
    return 1;
}

//todo should complete event method
function ngx_event_expire_timers()
{
//ngx_event_t        *ev;
//    ngx_rbtree_node_t  *node, *root, *sentinel;
//
//    sentinel = ngx_event_timer_rbtree.sentinel;

//    for ( ;; ) {
//        root = ngx_event_timer_rbtree.root;
//
//        if (root == sentinel) {
//            return;
//        }
//
//        node = ngx_rbtree_min(root, sentinel);
//
//        /* node->key > ngx_current_time */
//
//        if ((ngx_msec_int_t) (node->key - ngx_current_msec) > 0) {
//            return;
//        }
//
//        ev = (ngx_event_t *) ((char *) node - offsetof(ngx_event_t, timer));
//
//        ngx_log_debug2(NGX_LOG_DEBUG_EVENT, ev->log, 0,
//                       "event timer del: %d: %M",
//                       ngx_event_ident(ev->data), ev->timer.key);
//
//        ngx_rbtree_delete(&ngx_event_timer_rbtree, &ev->timer);
//
//#if (NGX_DEBUG)
//        ev->timer.left = NULL;
//        ev->timer.right = NULL;
//        ev->timer.parent = NULL;
//#endif
//
//        ev->timer_set = 0;
//
//        ev->timedout = 1;
//
//        ev->handler(ev);
//    }
}

//todo should complete event method
function ngx_event_cancel_timers()
{
//ngx_event_t        *ev;
//    ngx_rbtree_node_t  *node, *root, *sentinel;
//
//    sentinel = ngx_event_timer_rbtree.sentinel;
//
//    for ( ;; ) {
//        root = ngx_event_timer_rbtree.root;
//
//        if (root == sentinel) {
//            return;
//        }
//
//        node = ngx_rbtree_min(root, sentinel);
//
//        ev = (ngx_event_t *) ((char *) node - offsetof(ngx_event_t, timer));
//
//        if (!ev->cancelable) {
//            return;
//        }
//
//        ngx_log_debug2(NGX_LOG_DEBUG_EVENT, ev->log, 0,
//                       "event timer cancel: %d: %M",
//                       ngx_event_ident(ev->data), ev->timer.key);
//
//        ngx_rbtree_delete(&ngx_event_timer_rbtree, &ev->timer);
//
//        ev->timer_set = 0;
//
//        ev->handler(ev);
}

function event_base(){
   static $base;
    if(is_null($base)){
        $base = event_base_new();
    }
    return $base;
}