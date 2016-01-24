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

define('NGX_EVENT_MODULE',0x544E5645);
define('NGX_EVENT_CONF',0x02000000);

define('NGX_READ_EVENT', EV_READ);
define('NGX_WRITE_EVENT',EV_WRITE);
define('NGX_CLOSE_EVENT',1);


define('DEFAULT_CONNECTIONS', 512);

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

//ngx_atomic_t         *ngx_accept_mutex_ptr;
//ngx_shmtx_t           ngx_accept_mutex;
function  ngx_use_accept_mutex($i = null){
    static $ngx_use_accept_mutex = null;
    if(!is_null($i)){
       $ngx_use_accept_mutex = $i ;
    }else{
       return $ngx_use_accept_mutex;
    }
}
function  ngx_accept_events($i = null){
    static $ngx_accept_events = null;
    if(!is_null($i)){
        $ngx_accept_events = $i ;
    }else{
        return $ngx_accept_events;
    }
}
function  ngx_accept_mutex_held($i = null){
    static $ngx_accept_mutex_held = null;
    if(!is_null($i)){
        $ngx_accept_mutex_held = $i ;
    }else{
        return $ngx_accept_mutex_held;
    }
}
function ngx_accept_mutex_delay($i = null){
    static $ngx_accept_mutex_delay = null;
    if(!is_null($i)){
        $ngx_accept_mutex_delay = $i ;
    }else{
        return $ngx_accept_mutex_delay;
    }
}
function ngx_accept_disabled($i = null){
    static $ngx_accept_disabled = null;
    if(!is_null($i)){
        $ngx_accept_disabled = $i ;
    }else{
        return $ngx_accept_disabled;
    }
}

function ngx_event_timer_alarm($i = null){

    static $ngx_event_timer_alarm = null;
    if(!is_null($i)){
       $ngx_event_timer_alarm = $i;
    }else{
       return $ngx_event_timer_alarm;
    }
}



class ngx_event_module_t {
    public  $name;

//void                 *(*create_conf)(ngx_cycle_t *cycle);
    public $create_conf;
//char                 *(*init_conf)(ngx_cycle_t *cycle, void *conf);
    public $init_conf;
    //   ngx_int_t  (*add)(ngx_event_t *ev, ngx_int_t event, ngx_uint_t flags);
    public $add;
    //ngx_int_t  (*del)(ngx_event_t *ev, ngx_int_t event, ngx_uint_t flags);
    public $del;

    //ngx_int_t  (*enable)(ngx_event_t *ev, ngx_int_t event, ngx_uint_t flags);
    public $enable;
    //ngx_int_t  (*disable)(ngx_event_t *ev, ngx_int_t event, ngx_uint_t flags);
    public $disable;

    //ngx_int_t  (*add_conn)(ngx_connection_t *c);
    public $add_conn;
    //ngx_int_t  (*del_conn)(ngx_connection_t *c, ngx_uint_t flags);
    public $del_conn;

    //ngx_int_t  (*notify)(ngx_event_handler_pt handler);
    public $notify;

//    ngx_int_t  (*process_events)(ngx_cycle_t *cycle, ngx_msec_t timer,
    //                 ngx_uint_t flags);
    public $process_events;

    //ngx_int_t  (*init)(ngx_cycle_t *cycle, ngx_msec_t timer);
    public $init;
    //void       (*done)(ngx_cycle_t *cycle);
    public $done;
}


class ngx_event_t {
/**   void  **/ /****ngx_connection_t***/   public  $data;

/**   unsigned  **/    public  $write;

/**   unsigned  **/    public  $accept;

    /* used to detect the stale events in kqueue and epoll */
/**   unsigned  **/    public  $instance;

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
//ngx_uint_t            ngx_accept_events;
//ngx_uint_t            ngx_accept_mutex_held;
//ngx_msec_t            ngx_accept_mutex_delay;


function ngx_timer_resolution($i = null){
    static $ngx_timer_resolution = null;
    if(!is_null($i)){
        $ngx_timer_resolution = $i;
    }else{
       return $ngx_timer_resolution;
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

function ngx_events_module(){
    static $ngx_events_module;
    if(is_null($ngx_events_module)){
        $obj = new ngx_module_t();
        $ngx_events_module = $obj;
        $ngx_events_module->version = 1;
        $ngx_events_module->ctx = ngx_events_module_ctx();
        $ngx_events_module->commands = ngx_events_commands();
        $ngx_events_module->type = NGX_CORE_MODULE;
    }
    return $ngx_events_module;
}
function ngx_events_module_ctx(){

    static $ngx_events_module_ctx;
    if(is_null($ngx_events_module_ctx)){
        $obj= new ngx_core_module_t();
        $ngx_events_module_ctx = $obj;
        $ngx_events_module_ctx->name = 'events';
        $ngx_events_module_ctx->create_conf = null;
        $ngx_events_module_ctx->init_conf = 'ngx_event_init_conf';
    }
    return $ngx_events_module_ctx;
}

function ngx_events_commands(){

    $ngx_events_commands = array(
        array(
            'name'=>"events",
            'type'=>NGX_MAIN_CONF|NGX_CONF_BLOCK|NGX_CONF_NOARGS,
            'set'=>'ngx_events_block',
            'conf'=>0,
            'offset'=>0,
            'post'=>NULL
        ),
        array(
            'name'=>'',
            'type'=>0,
            'set'=>NULL,
            'conf'=>0,
            'offset'=>0,
            'post'=>NULL
        ),
    );

    return $ngx_events_commands;

}

function ngx_event_core_module(){
    static $ngx_event_core_module;
    if(is_null($ngx_event_core_module)){
        $obj = new ngx_module_t();
        $ngx_event_core_module = $obj;
        $ngx_event_core_module->version = 1;
        $ngx_event_core_module->ctx = ngx_event_core_module_ctx();
        $ngx_event_core_module->commands = ngx_event_core_commands();
        $ngx_event_core_module->type = NGX_EVENT_MODULE;
        $ngx_event_core_module->init_module = 'ngx_event_module_init';
        $ngx_event_core_module->init_process = 'ngx_event_process_init';
    }
    return $ngx_event_core_module;
}
function ngx_event_core_module_ctx(){

    static $ngx_events_module_ctx;
    if(is_null($ngx_events_module_ctx)){
        $obj= new ngx_event_module_t();
        $ngx_events_module_ctx = $obj;
        $ngx_events_module_ctx->name = 'event_core';
        $ngx_events_module_ctx->create_conf = 'ngx_event_core_create_conf';
        $ngx_events_module_ctx->init_conf = 'ngx_event_core_init_conf';

    }
    return $ngx_events_module_ctx;
}

function ngx_event_core_commands(){

    $ngx_event_core_commands = array(
        array(
            'name'=>"work_connections",
            'type'=>NGX_EVENT_CONF|NGX_CONF_TAKE1,
            'set'=>'ngx_event_connections',
            'conf'=>0,
            'offset'=>0,
            'post'=>NULL
        ),
        array(
            'name'=>"use",
            'type'=>NGX_EVENT_CONF|NGX_CONF_TAKE1,
            'set'=>'ngx_event_use',
            'conf'=>0,
            'offset'=>0,
            'post'=>NULL
        ),
        array(
            'name'=>"multi_accept",
            'type'=>NGX_EVENT_CONF|NGX_CONF_FLAG,
            'set'=>'ngx_conf_set_flag_slot',
            'conf'=>0,
            'offset'=>0,
            'post'=>NULL
        ),
        array(
            'name'=>"multi_mutex_delay",
            'type'=>NGX_EVENT_CONF|NGX_CONF_TAKE1,
            'set'=>'ngx_conf_set_msec_slot',
            'conf'=>0,
            'offset'=>0,
            'post'=>NULL
        ),
        array(
            'name'=>'',
            'type'=>0,
            'set'=>NULL,
            'conf'=>0,
            'offset'=>0,
            'post'=>NULL
        ),
    );

    return $ngx_event_core_commands;

}

function ngx_event_max_module($i = null){
   static $ngx_event_max_module  = null;
    if(!is_null($i)){
       $ngx_event_max_module = $i;
    }else{
       return $ngx_event_max_module;
    }
}

function ngx_events_block(ngx_conf_t $cf, /*ngx_command_t*/ $cmd, $conf)
{
//char                 *rv;
//    void               ***ctx;
//    ngx_uint_t            i;
//    ngx_conf_t            pcf;
//    ngx_event_module_t   *m;

    if ($conf) {
        return "is duplicate";
        }

    /* count the number of the event modules and set up their indices */

    ngx_event_max_module(0);
    for ($i = 0; ngx_modules($i); $i++) {
        if (ngx_modules($i)->type != NGX_EVENT_MODULE) {
            continue;
        }

        $max = ngx_event_max_module();
        ngx_modules($i)->ctx_index = $max++;
        ngx_event_max_module($max);

    }

//    ctx = ngx_pcalloc(cf->pool, sizeof(void *));
//    if (ctx == NULL) {
//        return NGX_CONF_ERROR;
//    }
//
//    *ctx = ngx_pcalloc(cf->pool, ngx_event_max_module * sizeof(void *));
//    if (*ctx == NULL) {
//    return NGX_CONF_ERROR;
//}
    $ctx = array();
    $conf = $ctx;

    for ($i = 0; ngx_modules($i); $i++) {
        if (ngx_modules($i)->type != NGX_EVENT_MODULE) {
            continue;
        }

        $m = ngx_modules($i)->ctx;

        if ($m->create_conf) {
            $ctx[ngx_modules($i)->ctx_index] = $m->create_conf($cf->cycle);
            if ($ctx[ngx_modules($i)->ctx_index] == NULL) {
                return NGX_CONF_ERROR;
            }
        }
    }

    $pcf = $cf;
    $cf->ctx = $ctx;
    $cf->module_type = NGX_EVENT_MODULE;
    $cf->cmd_type = NGX_EVENT_CONF;

    $rv = ngx_conf_parse($cf, NULL);

    $cf = $pcf;

    if ($rv != NGX_CONF_OK) {
        return $rv;
    }

    for ($i = 0; ngx_modules($i); $i++) {
        if (ngx_modules($i)->type != NGX_EVENT_MODULE) {
            continue;
        }

        $m = ngx_modules($i)->ctx;

        if ($m->init_conf) {
            $rv = $m->init_conf($cf->cycle, $ctx[ngx_modules($i)->ctx_index]);
                if ($rv != NGX_CONF_OK) {
                    return $rv;
                }
            }
    }

    return NGX_CONF_OK;
}

function ngx_event_init_conf(ngx_cycle_t $cycle, $conf)
{
    if (ngx_get_conf($cycle->conf_ctx, ngx_events_module()) == NULL) {
            ngx_log_error(NGX_LOG_EMERG, $cycle->log, 0,
                      "no \"events\" section in configuration");
            return NGX_CONF_ERROR;
    }

    return NGX_CONF_OK;
}

class ngx_event_conf_t {
    private    $connections;
    private    $use;

    private    $multi_accept;
    private    $accept_mutex;

    private    $accept_mutex_delay;

    private    $name;

    public function __set($name,$value){
       $this->$name = $value;
    }
    public function __get($name){
       return $this->$name;
    }
}

function ngx_event_core_create_conf(ngx_cycle_t $cycle)
{

//ngx_event_conf_t  *ecf;
    $ecf = new ngx_event_conf_t();


    $ecf->connections = NGX_CONF_UNSET_UINT;
    $ecf->use = NGX_CONF_UNSET_UINT;
    $ecf->multi_accept = NGX_CONF_UNSET;
    $ecf->accept_mutex = NGX_CONF_UNSET;
    $ecf->accept_mutex_delay = NGX_CONF_UNSET_MSEC;
    $ecf->name =  NGX_CONF_UNSET;

    return $ecf;
}

function ngx_event_core_init_conf(ngx_cycle_t $cycle, $conf)
{
//ngx_event_conf_t  *ecf = conf;
    $ecf = $conf;

//    int                  fd;
//    ngx_int_t            i;
//    ngx_module_t        *module;
//    ngx_event_module_t  *event_module;

    $module = NULL;


//    fd = epoll_create(100);
//
//    if (fd != -1) {
//        (void) close(fd);
//        module = &ngx_epoll_module;
//
//    } else if (ngx_errno != NGX_ENOSYS) {
//        module = &ngx_epoll_module;
//    }

    //todo should find the best event method
    //$module = ngx_epoll_module();



    if ($module == NULL) {
        for ($i = 0; ngx_modules($i); $i++) {

            if (ngx_modules($i)->type != NGX_EVENT_MODULE) {
                continue;
            }

            $event_module = ngx_modules($i)->ctx;

            if (ngx_strcmp($event_module->name, 'event_core') == 0)
            {
                continue;
            }

            $module = ngx_modules($i);
            break;
        }
    }

    if ($module == NULL) {
        ngx_log_error(NGX_LOG_EMERG, $cycle->log, 0, "no events module found");
        return NGX_CONF_ERROR;
    }

    ngx_conf_init_uint_value($ecf->connections, DEFAULT_CONNECTIONS);
    $cycle->connection_n = $ecf->connections;

    ngx_conf_init_uint_value($ecf->use, $module->ctx_index);

    $event_module = $module->ctx;
    ngx_conf_init_ptr_value($ecf->name, $event_module->name);

    ngx_conf_init_value($ecf->multi_accept, 0);
    ngx_conf_init_value($ecf->accept_mutex, 1);
    ngx_conf_init_msec_value($ecf->accept_mutex_delay, 500);

    return NGX_CONF_OK;
}


function ngx_event_connections(ngx_conf_t $cf, /*ngx_command_t*/ $cmd, $conf)
{
    $ecf = $conf;

   // ngx_str_t  *value;

    if ($ecf->connections != NGX_CONF_UNSET_UINT) {
    return "is duplicate";
    }

    $value = $cf->args;
    $ecf->connections = ngx_atoi($value[1]);
    if ($ecf->connections ==  NGX_ERROR) {
        ngx_conf_log_error(NGX_LOG_EMERG, $cf, 0,
        "invalid number \"%V\"", $value[1]);

        return NGX_CONF_ERROR;
    }

    $cf->cycle->connection_n = $ecf->connections;

    return NGX_CONF_OK;
}

function ngx_event_get_conf($conf_ctx, $module)
{

    return  ngx_get_conf($conf_ctx, ngx_events_module())[$module->ctx_index];
}

function ngx_event_use(ngx_conf_t $cf, $conf)
//function ngx_event_use(ngx_conf_t $cf, /*ngx_command_t*/ $cmd, $conf)
{
    $ecf = $conf;

//    ngx_int_t             m;
//    ngx_str_t            *value;
//    ngx_event_conf_t     *old_ecf;
//    ngx_event_module_t   *module;

    if ($ecf->use != NGX_CONF_UNSET_UINT) {
        return "is duplicate";
    }

    $value = $cf->args;

    if ($cf->cycle->old_cycle->conf_ctx) {
        $old_ecf = ngx_event_get_conf($cf->cycle->old_cycle->conf_ctx, ngx_event_core_module());
    } else {
        $old_ecf = NULL;
    }


    for ($m = 0; ngx_modules($m); $m++) {
        if (ngx_modules($m)->type != NGX_EVENT_MODULE) {
            continue;
        }

        $module = ngx_modules($m)->ctx;
        //if (module->name->len == value[1].len) {
            if (ngx_strcmp($module->name, $value[1]) == 0) {
                $ecf->use = ngx_modules($m)->ctx_index;
                $ecf->name = $module->name;

                if (ngx_process() == NGX_PROCESS_SINGLE
                    && $old_ecf
                    && $old_ecf->use != $ecf->use)
                {
                    ngx_conf_log_error(NGX_LOG_EMERG, $cf, 0,
                        "when the server runs without a master process ".
                               "the \"%V\" event type must be the same as ".
                               "in previous configuration - \"%s\" ".
                               "and it cannot be changed on the fly, ".
                               "to change it you need to stop server ".
                               "and start it again",
                               array($value[1], $old_ecf->name));

                    return NGX_CONF_ERROR;
                }

                return NGX_CONF_OK;
            }
        //}
    }

    ngx_conf_log_error(NGX_LOG_EMERG, $cf, 0,
        "invalid event type \"%V\"", $value[1]);

    return NGX_CONF_ERROR;
}


function ngx_event_module_init(ngx_cycle_t $cycle)
{
//void              ***cf;
//    u_char              *shared;
//    size_t               size, cl;
//    ngx_shm_t            shm;
//    ngx_time_t          *tp;
//    ngx_core_conf_t     *ccf;
//    ngx_event_conf_t    *ecf;

    $cf = ngx_get_conf($cycle->conf_ctx, ngx_events_module());
    $ecf = $cf[ngx_event_core_module()->ctx_index];

    if (!ngx_test_config() && ngx_process() <= NGX_PROCESS_MASTER) {
        ngx_log_error(NGX_LOG_NOTICE, $cycle->log, 0,
                      "using the \"%s\" event method", $ecf->name);
    }

    $ccf = ngx_get_conf($cycle->conf_ctx, ngx_core_module());

    ngx_timer_resolution($ccf->timer_resolution);

//        ngx_int_t      limit;
//    struct rlimit  rlmt;

    if ($rlmt = posix_getrlimit() == false) {
        ngx_log_error(NGX_LOG_ALERT, $cycle->log, 0,
                      "getrlimit(RLIMIT_NOFILE) failed, ignored");

    } else {
        if ($ecf->connections >  $rlmt['soft openfiles']
        && ($ccf->rlimit_nofile == NGX_CONF_UNSET
        || $ecf->connections > $ccf->rlimit_nofile))
        {
            $limit = ($ccf->rlimit_nofile == NGX_CONF_UNSET) ?
                          $rlmt['rlim_cur'] : $ccf->rlimit_nofile;

            ngx_log_error(NGX_LOG_WARN, $cycle->log, 0,
                          "%ui worker_connections exceed ".
                          "open file resource limit: %i",
                          array($ecf->connections, $limit));
        }
    }


    if ($ccf->master == 0) {
           return NGX_OK;
        }

//    if (ngx_accept_mutex_ptr) {
//        return NGX_OK;
//    }


    /* cl should be equal to or greater than cache line size */

//    cl = 128;
//
//    size = cl            /* ngx_accept_mutex */
//        + cl          /* ngx_connection_counter */
//        + cl;         /* ngx_temp_number */

#if (NGX_STAT_STUB)

//    size += cl           /* ngx_stat_accepted */
//        + cl          /* ngx_stat_handled */
//        + cl          /* ngx_stat_requests */
//        + cl          /* ngx_stat_active */
//        + cl          /* ngx_stat_reading */
//        + cl          /* ngx_stat_writing */
//        + cl;         /* ngx_stat_waiting */

#endif

//    shm.size = size;
//    shm.name.len = sizeof("nginx_shared_zone") - 1;
//    shm.name.data = (u_char *) "nginx_shared_zone";
//    shm.log = cycle->log;
//
//    if (ngx_shm_alloc(&shm) != NGX_OK) {
//        return NGX_ERROR;
//    }
//
//    shared = shm.addr;
//
//    ngx_accept_mutex_ptr = (ngx_atomic_t *) shared;
//    ngx_accept_mutex.spin = (ngx_uint_t) -1;
//
//    if (ngx_shmtx_create(&ngx_accept_mutex, (ngx_shmtx_sh_t *) shared,
//                         cycle->lock_file.data)
//        != NGX_OK)
//    {
//        return NGX_ERROR;
//    }
//
//    ngx_connection_counter = (ngx_atomic_t *) (shared + 1 * cl);
//
//    (void) ngx_atomic_cmp_set(ngx_connection_counter, 0, 1);

//    ngx_log_debug2(NGX_LOG_DEBUG_EVENT, cycle->log, 0,
//                   "counter: %p, %d",
//                   ngx_connection_counter, *ngx_connection_counter);

//    ngx_temp_number = (ngx_atomic_t *) (shared + 2 * cl);

    $tp = ngx_timeofday();

    ngx_random_number(($tp->msec << 16) + ngx_pid());

#if (NGX_STAT_STUB)

//    ngx_stat_accepted = (ngx_atomic_t *) (shared + 3 * cl);
//    ngx_stat_handled = (ngx_atomic_t *) (shared + 4 * cl);
//    ngx_stat_requests = (ngx_atomic_t *) (shared + 5 * cl);
//    ngx_stat_active = (ngx_atomic_t *) (shared + 6 * cl);
//    ngx_stat_reading = (ngx_atomic_t *) (shared + 7 * cl);
//    ngx_stat_writing = (ngx_atomic_t *) (shared + 8 * cl);
//    ngx_stat_waiting = (ngx_atomic_t *) (shared + 9 * cl);

#endif

    return NGX_OK;
}



function ngx_event_process_init(ngx_cycle_t $cycle)
{
//ngx_uint_t           m, i;
//    ngx_event_t         *rev, *wev;
//    ngx_listening_t     *ls;
//    ngx_connection_t    *c, *next, *old;
//    ngx_core_conf_t     *ccf;
//    ngx_event_conf_t    *ecf;
//    ngx_event_module_t  *module;

    $ccf = ngx_get_conf($cycle->conf_ctx, ngx_core_module());
    $ecf = ngx_event_get_conf($cycle->conf_ctx, ngx_event_core_module());

    if ($ccf->master && $ccf->worker_processes > 1 && $ecf->accept_mutex) {
        ngx_use_accept_mutex(1);
        ngx_accept_mutex_held(0);
        ngx_accept_mutex_delay($ecf->accept_mutex_delay);

    } else {
        ngx_use_accept_mutex(0);
}



    ngx_queue_init(ngx_posted_accept_events());
    ngx_queue_init(ngx_posted_events());

    if (ngx_event_timer_init($cycle->log) == NGX_ERROR) {
        return NGX_ERROR;
    }

    for ($m = 0; ngx_modules($m); $m++) {
        if (ngx_modules($m)->type != NGX_EVENT_MODULE) {
            continue;
        }

        if (ngx_modules($m)->ctx_index != $ecf->use) {
            continue;
        }

        $module = ngx_modules($m)->ctx;

        if ($module->init($cycle, ngx_timer_resolution()) != NGX_OK) {
        /* fatal */
        exit(2);
        }

        break;
    }


    if (ngx_timer_resolution() && !(ngx_event_flags() & NGX_USE_TIMER_EVENT)) {
//        struct sigaction  sa;
//        struct itimerval  itv;
//
//        ngx_memzero(&sa, sizeof(struct sigaction));
        //sa.sa_handler = ngx_timer_signal_handler;
        //sigemptyset(&sa.sa_mask);

        if (pcntl_signal(SIGALRM, 'ngx_timer_signal_handler', NULL) == false) {
            ngx_log_error(NGX_LOG_ALERT, $cycle->log, pcntl_get_last_error(),
                          "sigaction(SIGALRM) failed");
            return NGX_ERROR;
        }
#####################################################################################
        //todo should have my own function of set timer
//        itv.it_interval.tv_sec = ngx_timer_resolution / 1000;
//        itv.it_interval.tv_usec = (ngx_timer_resolution % 1000) * 1000;
//        itv.it_value.tv_sec = ngx_timer_resolution / 1000;
//        itv.it_value.tv_usec = (ngx_timer_resolution % 1000 ) * 1000;
//
//        if (setitimer(ITIMER_REAL, &itv, NULL) == -1) {
//            ngx_log_error(NGX_LOG_ALERT, cycle->log, ngx_errno,
//                          "setitimer() failed");
//        }
    }
#########################################################################################

    if (ngx_event_flags() & NGX_USE_FD_EVENT) {
        //struct rlimit  rlmt;

        if ($rlmt = posix_getrlimit() == false) {
            ngx_log_error(NGX_LOG_ALERT, $cycle->log, posix_get_last_error(),
                          "getrlimit(RLIMIT_NOFILE) failed");
            return NGX_ERROR;
        }

        $cycle->files_n = $rlmt['soft openfiles'];

        //$cycle->files = ngx_calloc(sizeof(ngx_connection_t *) * cycle->files_n,
        //                               cycle->log);
        $cycle->files = array();
    }



   // $cycle->connections = ngx_alloc(sizeof(ngx_connection_t) * cycle->connection_n, cycle->log);
    $cycle->connections = array();
//    if (cycle->connections == NULL) {
//    return NGX_ERROR;
//}

    $c = $cycle->connections;

    //$cycle->read_events = ngx_alloc(sizeof(ngx_event_t) * cycle->connection_n,
     //                              cycle->log);
    $cycle->read_events = array();

//    if (cycle->read_events == NULL) {
//    return NGX_ERROR;
//}

    //$rev = $cycle->read_events;
    for ($i = 0; $i < $cycle->connection_n; $i++) {
        $event = new ngx_event_t();
        $event->closed = 1;
        $event->instance = 1;
        $cycle->read_events[$i] = $event;
    }

    //$cycle->write_events = ngx_alloc(sizeof(ngx_event_t) * cycle->connection_n,
    //                                cycle->log);
    $cycle->write_events = array();


    for ($i = 0; $i < $cycle->connection_n; $i++) {
        $event = new ngx_event_t();
        $event->closed = 1;
        $cycle->write_events[$i] = $event;
    }

    $i = $cycle->connection_n;
    $next = NULL;

    do {
        $i--;
        $connection = new ngx_connection_t();
        $connection->data = $next;
        $connection->read = $cycle->read_events[$i];
        $connection->write = $cycle->write_events[$i];
        $connection->fd = -1;
        $next = $connection;
        $cycle->connections[$i] = $connection;
    } while ($i);

    $cycle->free_connections = $next;
    $cycle->free_connection_n = $cycle->connection_n;

    /* for each listening socket */

    $ls = $cycle->listening;
    for ($i = 0; $i < count($cycle->listening); $i++) {

        $c = ngx_get_connection($ls[$i]->fd, $cycle->log);

        if ($c == NULL) {
            return NGX_ERROR;
        }

        $c->log = $ls[$i]->log;

        $c->listening = $ls[$i];
        $ls[$i]->connection = $c;

        $c->read->log = $c->log;
        $c->read->accept = 1;


        if (!(ngx_event_flags() & NGX_USE_IOCP_EVENT)) {
            if ($ls[$i]->previous) {

                /*
                 * delete the old accept events that were bound to
                 * the old cycle read events array
                 */

                //old = ls[i].previous->connection;

                if (ngx_del_event($ls[$i]->previous->connection->read, NGX_READ_EVENT, NGX_CLOSE_EVENT) == NGX_ERROR)
                {
                    return NGX_ERROR;
                }

                //old->fd = (ngx_socket_t) -1;
                $ls[$i]->previous->connection->fd = -1;
            }
        }


        $c->read->handler = 'ngx_event_accept';

        if (ngx_use_accept_mutex())
        {
            continue;
        }

        if (ngx_add_event($c->read, NGX_READ_EVENT, 0) == NGX_ERROR) {
            return NGX_ERROR;
        }


    }

    return NGX_OK;
}

function ngx_timer_signal_handler($signo)
{
    ngx_event_timer_alarm(1);
    $ngx_cycle = ngx_cycle();
    ngx_log_debug0(NGX_LOG_DEBUG_EVENT, $ngx_cycle->log, 0, "timer signal");
}


