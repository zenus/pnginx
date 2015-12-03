<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 15-12-3
 * Time: 下午11:06
 */

function ngx_event_timer(ngx_event_t $t, $time){

//    ngx_msec_t      key;
//    ngx_msec_int_t  diff;
//
//    key = ngx_current_msec + timer;
//
//    if (ev->timer_set) {
//
//        /*
//         * Use a previous timer value if difference between it and a new
//         * value is less than NGX_TIMER_LAZY_DELAY milliseconds: this allows
//         * to minimize the rbtree operations for fast connections.
//         */
//
//        diff = (ngx_msec_int_t) (key - ev->timer.key);
//
//        if (ngx_abs(diff) < NGX_TIMER_LAZY_DELAY) {
//            ngx_log_debug3(NGX_LOG_DEBUG_EVENT, ev->log, 0,
//                           "event timer: %d, old: %M, new: %M",
//                            ngx_event_ident(ev->data), ev->timer.key, key);
//            return;
//        }
//
//        ngx_del_timer(ev);
//    }
//
//    ev->timer.key = key;
//
//    ngx_log_debug3(NGX_LOG_DEBUG_EVENT, ev->log, 0,
//                   "event timer add: %d: %M:%M",
//                    ngx_event_ident(ev->data), timer, ev->timer.key);
//
//    ngx_rbtree_insert(&ngx_event_timer_rbtree, &ev->timer);
//
//    ev->timer_set = 1;
}