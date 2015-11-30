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


function ngx_event_flags($i = null){
    static $ngx_event_flags = null;
    if(!is_null($i)){
       $ngx_event_flags  = $i;
    }else{
       return $ngx_event_flags;
    }
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

