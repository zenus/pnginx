<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 15-11-7
 * Time: 下午7:34
 */

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

