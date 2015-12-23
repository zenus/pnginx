<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 15-11-7
 * Time: 下午7:34
 */

/*
 * The event filter requires to read/write the whole data:
 * select, poll, /dev/poll, kqueue, epoll.
 */
define('NGX_USE_LEVEL_EVENT',0x00000001);

/*
 * The event filter is deleted after a notification without an additional
 * syscall: kqueue, epoll.
 */
define('NGX_USE_ONESHOT_EVENT',0x00000002);

/*
 * The event filter notifies only the changes and an initial level:
 * kqueue, epoll.
 */
define('NGX_USE_CLEAR_EVENT',0x00000004);

/*
 * The event filter has kqueue features: the eof flag, errno,
 * available data, etc.
 */
define('NGX_USE_KQUEUE_EVENT',0x00000008);

/*
 * The event filter supports low water mark: kqueue's NOTE_LOWAT.
 * kqueue in FreeBSD 4.1-4.2 has no NOTE_LOWAT so we need a separate flag.
 */
define('NGX_USE_LOWAT_EVENT',0x00000010);

/*
 * The event filter requires to do i/o operation until EAGAIN: epoll.
 */
define('NGX_USE_GREEDY_EVENT',0x00000020);

/*
 * The event filter is epoll.
 */
define('NGX_USE_EPOLL_EVENT',0x00000040);

/*
 * Obsolete.
 */
define('NGX_USE_RTSIG_EVENT',0x00000080);

/*
 * Obsolete.
 */
define('NGX_USE_AIO_EVENT',0x00000100);

/*
 * Need to add socket or handle only once: i/o completion port.
 */
define('NGX_USE_IOCP_EVENT',0x00000200);

/*
 * The event filter has no opaque data and requires file descriptors table:
 * poll, /dev/poll.
 */
define('NGX_USE_FD_EVENT',0x00000400);

/*
 * The event module handles periodic or absolute timer event by itself:
 * kqueue in FreeBSD 4.4, NetBSD 2.0, and MacOSX 10.4, Solaris 10's event ports.
 */
define('NGX_USE_TIMER_EVENT',0x00000800);

/*
 * All event filters on file descriptor are deleted after a notification:
 * Solaris 10's event ports.
 */
define('NGX_USE_EVENTPORT_EVENT',0x00001000);

/*
 * The event filter support vnode notifications: kqueue.
 */
define('NGX_USE_VNODE_EVENT',0x00002000);

define('NGX_READ_EVENT', EV_READ);
define('NGX_WRITE_EVENT',EV_WRITE);
define('NGX_CLOSE_EVENT',1);

define('NGX_UPDATE_TIME',1);
define('NGX_POST_EVENTS',2);
define('NGX_DISABLE_EVENT',2);
define('NGX_INVALID_INDEX',0xd0d0d0d0);

function ngx_event_flags($i = null){
    static $ngx_event_flags = null;
    if(!is_null($i)){
       $ngx_event_flags  = $i;
    }else{
       return $ngx_event_flags;
    }
}

function ngx_event_ident(ngx_connection_t $p) {
 return $p->fd;
}

class ngx_event_t {
/**   void  **/    private  $data;

/**   unsigned  **/    private  $write;

/**   unsigned  **/    private  $accept;

    /* used to detect the stale events in kqueue and epoll */
/**   unsigned  **/    private  $instance;

    /*
     * the event was passed or would be passed to a kernel;
     * in aio mode - operation was posted.
     */
/**   unsigned  **/    private  $active;

/**   unsigned  **/    private  $disabled;

    /* the ready event; in aio mode 0 means that no operation can be posted */
/**   unsigned  **/    private  $ready;

/**   unsigned  **/    private  $oneshot;

    /* aio operation is complete */
/**   unsigned  **/    private  $complete;

/**   unsigned  **/    private  $eof;
/**   unsigned  **/    private  $error;

/**   unsigned  **/    private  $timedout;
/**   unsigned  **/    private  $timer_set;

/**   unsigned  **/    private  $delayed;

/**   unsigned  **/    private  $deferred_accept;

    /* the pending eof reported by kqueue, epoll or in aio chain operation */
/**   unsigned  **/    private  $pending_eof;

/**   unsigned  **/    private  $posted;

/**   unsigned  **/    private  $closed;

    /* to test on worker exit */
/**   unsigned  **/    private  $channel;
/**   unsigned  **/    private  $resolver;

/**   unsigned  **/    private  $cancelable;


/**   unsigned  **/    private  $available;

/**   ngx_event_handler_pt  **/    private  $handler;


#if (NGX_HAVE_IOCP)
//    ngx_event_ovlp_t ovlp;
#endif

/**   ngx_uint_t  **/    private  $index;

/**   ngx_log_t  **/    private  $log;

    //todo php special use it as libevent  event element
    private $event;
/**   ngx_rbtree_node_t  **/    private  $timer;

    /* the posted queue */
/**   ngx_queue_t  **/    private  $queue;

#if 0

    /* the threads support */

    /*
     * the event thread context, we store it here
     * if $(CC) does not understand __thread declaration
     * and pthread_getspecific() is too costly
     */

/**   void  **/    private  $thr_ctx;
    public function __set($property,$value){
       $this->$property = $value;
    }
    public function __get($property){
       return $this->$property;
    }

#if (NGX_EVENT_T_PADDING)

    /* event should not cross cache line in SMP */

//   private  $padding[NGX_EVENT_T_PADDING];
#endif
#endif

}

function ngx_io(ngx_os_io_t $ngx_os_io_t = null)
{
    static $ngx_io = null;
    if ($ngx_io !== null) {
        $ngx_io = $ngx_os_io_t;
    } else {
        return $ngx_io;
    }
}

function ngx_add_timer(ngx_event_t $e, $time){
    ngx_event_add_timer($e, $time);
}

function ngx_del_timer(ngx_event_t $ev){
    ngx_event_del_timer($ev);

}
function ngx_event_del_timer(ngx_event_t $ev){

    ngx_log_debug2(NGX_LOG_DEBUG_EVENT, $ev->log, 0,
                   "event timer del: %d: %M",
    ngx_event_ident($ev->data), $ev->timer);

    event_del($ev->event);
    event_free($ev->event);


    $ev->timer_set = 0;
}

function ngx_accept_mutex_ptr($ptr = null){
    static $ngx_accept_mutex_ptr = null;
    if(!is_null($ptr)){
       $ngx_accept_mutex_ptr = $ptr;
    }else{
        return $ngx_accept_mutex_ptr;
    }
}

//ngx_atomic_t         *ngx_accept_mutex_ptr;
//ngx_shmtx_t           ngx_accept_mutex;
//ngx_uint_t            ngx_use_accept_mutex;
function ngx_use_accept_mutex($i = null){
    static $ngx_use_accept_mutex = null;
    if(!is_null($i)){
       $ngx_use_accept_mutex = $i;
    }else{
        return $ngx_use_accept_mutex;
    }

}
//ngx_uint_t            ngx_accept_events;
function ngx_accept_events($i = null){
   static $ngx_accept_events = null;
    if(!is_null($i)){
       $ngx_accept_events = $i;
    }else{
       return $ngx_accept_events;
    }
}
//ngx_uint_t            ngx_accept_mutex_held;
function ngx_accept_mutex_held($i = null){
    static $ngx_accept_mutex_held = null;
    if(!is_null($i)){
       $ngx_accept_mutex_held = $i;
    }else{
       return $ngx_accept_mutex_held;
    }
}
//ngx_msec_t            ngx_accept_mutex_delay;
function ngx_accept_mutex_delay($i = null){
    static $ngx_accept_mutex_delay = null;
    if(!is_null($i)){
        $ngx_accept_mutex_delay = $i;
    }else{
       return $ngx_accept_mutex_delay;
    }

}

function ngx_timer_resolution($i = null){
    static $ngx_timer_resolution = null;
    if(!is_null($i)){
        $ngx_timer_resolution = $i;
    }else{
       return $ngx_timer_resolution;
    }
}

function ngx_accept_disabled($i = null){
    static $ngx_accept_disabled = null;
    if(!is_null($i)){
       $ngx_accept_disabled =  $i;
    }else{
       return $ngx_accept_disabled;
    }
}

function  ngx_accept_mutex($shmtx = null){
    static $ngx_accept_mutex = null;
    if(!is_null($shmtx)){
        $ngx_accept_mutex = $shmtx;
    }else{
       return $ngx_accept_mutex;
    }
}


function ngx_process_events_and_timers(ngx_cycle_t $cycle)
{
//ngx_uint_t  flags;
//    ngx_msec_t  timer, delta;

    if (ngx_timer_resolution()) {
        $timer = NGX_TIMER_INFINITE;
        $flags = 0;

    } else {
    //todo should complete event method
        $timer = ngx_event_find_timer();
        $flags = NGX_UPDATE_TIME;
    }

    if (ngx_use_accept_mutex()) {
        if ($ngx_accept_disabled = ngx_accept_disabled() > 0) {
            $ngx_accept_disabled--;
            ngx_accept_disabled($ngx_accept_disabled);

        } else {
            //todo should complete lock method
            if (ngx_trylock_accept_mutex($cycle) == NGX_ERROR) {
                return;
            }

            if (ngx_accept_mutex_held()) {
                $flags |= NGX_POST_EVENTS;

            } else {
                if ($timer == NGX_TIMER_INFINITE
                    || ($timer > ($ngx_accept_mutex_delay = ngx_accept_mutex_delay())))
                {
                    $timer = ngx_accept_mutex_delay();
                }
            }
        }
    }

    $delta = ngx_current_msec();

    //todo should complete event method
    ngx_process_events($cycle, $timer, $flags);

    $delta = ngx_current_msec() - $delta;

    ngx_log_debug1(NGX_LOG_DEBUG_EVENT, $cycle->log, 0,
                   "timer delta: %M", $delta);

    ngx_event_process_posted($cycle, ngx_posted_accept_events());

    if (ngx_accept_mutex_held()) {
        //todo should complete lock method
        ngx_shmtx_unlock(ngx_accept_mutex());
    }

    if ($delta) {
        ngx_event_expire_timers();
    }

    ngx_event_process_posted($cycle, ngx_posted_events());
}

//todo need to complete event method
function ngx_add_event(ngx_event_t $ev,  $event,  $flags){
  return NGX_OK;
}
//todo need to complete event method
function ngx_add_conn(ngx_connection_t $con){
    return NGX_OK;
}

//todo need to complete event method
function ngx_del_conn(ngx_connection_t $con, $flags){
    return NGX_OK;
}
//todo need to complete event method
function ngx_process_events(ngx_cycle_t $cycle,  $timer,  $flags){

}
//todo need to complete event method
function ngx_del_event(ngx_event_t $ev,  $event,  $flags)
{
//    int                  op;
//    uint32_t             prev;
//    ngx_event_t         *e;
//    ngx_connection_t    *c;
//    struct epoll_event   ee;

    /*
     * when the file descriptor is closed, the epoll automatically deletes
     * it from its queue, so we do not need to delete explicitly the event
     * before the closing the file descriptor
     */

    if ($flags & NGX_CLOSE_EVENT) {
        $ev->active = 0;
        return NGX_OK;
    }

    $c = $ev->data;

    if ($event == NGX_READ_EVENT) {
        $e = $c->write;
        $prev = NGX_WRITE_EVENT;

    } else {
        $e = $c->read;
        $prev = NGX_READ_EVENT;
    }

//    if ($e->active) {
//    op = EPOLL_CTL_MOD;
//    ee.events = prev | (uint32_t) flags;
//        ee.data.ptr = (void *) ((uintptr_t) c | ev->instance);
//
//    } else {
//    op = EPOLL_CTL_DEL;
//    ee.events = 0;
//    ee.data.ptr = NULL;
//}
//
//    ngx_log_debug3(NGX_LOG_DEBUG_EVENT, ev->log, 0,
//                   "epoll del event: fd:%d op:%d ev:%08XD",
//                   c->fd, op, ee.events);
//
//    if (epoll_ctl(ep, op, c->fd, &ee) == -1) {
//    ngx_log_error(NGX_LOG_ALERT, ev->log, ngx_errno,
//                      "epoll_ctl(%d, %d) failed", op, c->fd);
//        return NGX_ERROR;
//    }
//
//    ev->active = 0;

    return NGX_OK;
}
//ngx_int_t             ngx_accept_disabled;



