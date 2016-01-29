<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 16-1-24
 * Time: 下午3:22
 */

function  ngx_event_flags($event){
    static $ngx_event_flags = null;
    if(!is_null($event)){
       $ngx_event_flags = $event;
    }else{
        return $ngx_event_flags;
    }
}

function ngx_libevent_base($base  = null){
    static $ngx_libevent_base;
    if(!is_null($base)){
       $ngx_libevent_base = $base;
    }else{
        return $ngx_libevent_base;
    }
}

class ngx_event_actions_t {
    //ngx_int_t  (*add)(ngx_event_t *ev, ngx_int_t event, ngx_uint_t flags);
    public $add;
    //ngx_int_t  (*del)(ngx_event_t *ev, ngx_int_t event, ngx_uint_t flags);
    public $del;

//    ngx_int_t  (*enable)(ngx_event_t *ev, ngx_int_t event, ngx_uint_t flags);
    public $enable;

//    ngx_int_t  (*disable)(ngx_event_t *ev, ngx_int_t event, ngx_uint_t flags);
   public $disable;
//
//    ngx_int_t  (*add_conn)(ngx_connection_t *c);
    public $add_conn;
//    ngx_int_t  (*del_conn)(ngx_connection_t *c, ngx_uint_t flags);
    public $del_conn;
//
//    ngx_int_t  (*notify)(ngx_event_handler_pt handler);
    public $notify;
//
//    ngx_int_t  (*process_events)(ngx_cycle_t *cycle, ngx_msec_t timer,
//                   ngx_uint_t flags);
//
    public $process_events;
//    ngx_int_t  (*init)(ngx_cycle_t *cycle, ngx_msec_t timer);
    public $init;
//    void       (*done)(ngx_cycle_t *cycle);
    public $done;
}

function   ngx_event_actions(ngx_event_actions_t $action){
    static $ngx_event_actions = null;
    if(!is_null($action)){
       $ngx_event_actions = $action;
    }else{
       return $ngx_event_actions;
    }
}




function ngx_epoll_module(){
    static $ngx_epoll_module;
    if(is_null($ngx_epoll_module)){
        $obj = new ngx_module_t();
        $ngx_epoll_module = $obj;
        $ngx_epoll_module->version = 1;
        $ngx_epoll_module->ctx = ngx_epoll_module_ctx();
        $ngx_epoll_module->commands = ngx_epoll_commands();
        $ngx_epoll_module->type = NGX_EVENT_MODULE;
    }
    return $ngx_epoll_module;
}
function ngx_epoll_module_ctx(){

    static $ngx_epoll_module_ctx;
    if(is_null($ngx_epoll_module_ctx)){
        $obj= new ngx_event_module_t();
        $ngx_epoll_module_ctx = $obj;
        $ngx_epoll_module_ctx->name = 'epoll';
        $ngx_epoll_module_ctx->create_conf = 'ngx_epoll_create_conf';
        $ngx_epoll_module_ctx->init_conf = 'ngx_epoll_init_conf';
        $event_action = new ngx_event_actions_t();
        $event_action->add = 'ngx_epoll_add_event';             /* add an event */
       $event_action->del =  'ngx_epoll_del_event';             /* delete an event */
       $event_action->enable = 'ngx_epoll_add_event';             /* enable an event */
       $event_action->disable = 'ngx_epoll_del_event';             /* disable an event */
       $event_action->add_conn = 'ngx_epoll_add_connection';        /* add an connection */
       $event_action->del_conn = 'ngx_epoll_del_connection';        /* delete an connection */
       $event_action->notify = NULL;                            /* trigger a notify */
       $event_action->process_events = 'ngx_epoll_process_events';        /* process the events */
       $event_action->init ='ngx_epoll_init';                  /* init the events */
       $event_action->done = 'ngx_epoll_done';                  /* done the events */
       $ngx_epoll_module_ctx->actions = $event_action;
    }
    return $ngx_epoll_module_ctx;
}

function ngx_epoll_commands(){

    $ngx_epoll_commands = array(
        array(
            'name'=>"epoll_event",
            'type'=>NGX_EVENT_CONF|NGX_CONF_TAKE1,
            'set'=>'ngx_conf_set_num_slot',
            'conf'=>0,
            //todo find right way
            'offset'=>0,
            'post'=>NULL
        ),
        array(
            'name'=>"worker_aio_requests",
            'type'=>NGX_EVENT_CONF|NGX_CONF_TAKE1,
            'set'=>'ngx_conf_set_num_slot',
            'conf'=>0,
            //todo find right way
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
        )
    );

    return $ngx_epoll_commands;

}

function ngx_epoll_create_conf()
{
    $epcf = new ngx_epoll_conf_t();

    $epcf->events = NGX_CONF_UNSET;
    $epcf->aio_requests = NGX_CONF_UNSET;

    return $epcf;
}

function ngx_epoll_init_conf($conf)
{
    $epcf = $conf;

    ngx_conf_init_uint_value($epcf->events, 512);
    ngx_conf_init_uint_value($epcf->aio_requests, 32);

    return NGX_CONF_OK;
}


class  ngx_epoll_conf_t {
    public $events;
    //ngx_uint_t  events;
    //ngx_uint_t  aio_requests;
    public $aio_requests;
}

function ngx_epoll_add_event(ngx_event_t $ev, $event, $flags)
{
//    int                  op;
//    uint32_t             events, prev;
//    ngx_event_t         *e;
//    ngx_connection_t    *c;
//    struct epoll_event   ee;

    $c = $ev->data;
/**
 * Q:  What  happens  if you register the same file descriptor on an epoll  instance twice?
 *
 * A: You will probably get EEXIST.
 * However, it is  possible  to  add  a   duplicate  (dup(2),
 * dup2(2),  fcntl(2)  F_DUPFD) descriptor to the  same epoll instance.
 * This can be a useful technique for  filtering   events,
 * if the duplicate file descriptors are registered with different events masks.*/


    $events =  $event;

    if ($event == NGX_READ_EVENT) {
        $e = $c->write;
        //todo may have problem
        $prev = Event::WRITE|Event::PERSIST;

    } else {
        $e = $c->read;
        //todo may have problem
        $prev = Event::READ|Event::PERSIST;
    }

    $add = 0;
    if ($e->active) {
        //$op = EPOLL_CTL_MOD;
        $events |= $prev;

    } else {
       // $op = EPOLL_CTL_ADD;
        $add = 1;
    }


    $events = $events | $flags;
//    ee.data.ptr = (void *) ((uintptr_t) c | ev->instance);

    ngx_log_debug2(NGX_LOG_DEBUG_EVENT, $ev->log, 0,
                   "epoll add event: fd:%d  ev:%08XD",
                   $c->fd,$events);

    if(!$add) {
        $ev->libevent->del();
    }
    //todo complete callbacks
    $ev->libevent = new Event(ngx_libevent_base(), $c->fd, $events, 'ngx_epoll_process_events');
    $ev->libevent->add();
    $ev->active = 1;


    return NGX_OK;
}


function ngx_epoll_init(ngx_cycle_t $cycle, $timer)
{
    //ngx_epoll_conf_t  *epcf;

    $epcf = ngx_event_get_conf($cycle->conf_ctx, ngx_epoll_module());

    ngx_libevent_base(new EventBase());
//    if (ep == -1) {
//        ep = epoll_create(cycle->connection_n / 2);
//
//        if (ep == -1) {
//            ngx_log_error(NGX_LOG_EMERG, cycle->log, ngx_errno,
//                          "epoll_create() failed");
//            return NGX_ERROR;
//        }




    //todo it is useful ?
//    if (nevents < epcf->events) {
//    if (event_list) {
//        ngx_free(event_list);
//    }
//
//    event_list = ngx_alloc(sizeof(struct epoll_event) * epcf->events,
//                               cycle->log);
//        if (event_list == NULL) {
//            return NGX_ERROR;
//        }
//    }
//
//    nevents = epcf->events;

    ngx_io(ngx_os_io());

    ngx_event_actions(ngx_epoll_module_ctx()->actions);

    ngx_event_flags(NGX_USE_LEVEL_EVENT
        |NGX_USE_GREEDY_EVENT
        |NGX_USE_EPOLL_EVENT);

    return NGX_OK;
}

function ngx_epoll_del_event(ngx_event_t $ev, $event, $flags)
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
        //todo may have problem
        $prev = Event::WRITE|Event::PERSIST;

    } else {
        $e = $c->read;
        //todo may have problem
        $prev = Event::READ|Event::PERSIST;
    }

    $del = 0;
    if ($e->active) {
        $events = $prev |  $flags;
//        ee.data.ptr = (void *) ((uintptr_t) c | ev->instance);

    } else {
        $del = 1;
        $events = 0;
    //ee.data.ptr = NULL;
    }

    $ev->libevent->del();
    if(!$del){
        //todo complete callbacks
        $ev->libevent = new Event(ngx_libevent_base(), $c->fd, $events, 'ngx_epoll_process_events');
        $ev->libevent->add();
    }

    ngx_log_debug3(NGX_LOG_DEBUG_EVENT, $ev->log, 0,
                   "epoll del event: fd:%d op:%d ev:%08XD",
                   $c->fd, $del, $events);


    $ev->active = 0;

    return NGX_OK;
}


function ngx_epoll_add_connection(ngx_connection_t $c)
{
//struct epoll_event  ee;

    $events = Event::WRITE|Event::READ|Event::PERSIST|Event::ET;
    //$prev = Event::WRITE|Event::PERSIST;
    //ee.data.ptr = (void *) ((uintptr_t) c | c->read->instance);

    $event = new Event(ngx_libevent_base(), $c->fd, $events, 'ngx_epoll_process_events');
    $event->add();
    //todo is right to do so ?
    $c->read->libevent = $event;
    $c->write->libevent = $event;
    ngx_log_debug2(NGX_LOG_DEBUG_EVENT, $c->log, 0,
                   "epoll add connection: fd:%d ev:%08XD", $c->fd, $events);

//    if (epoll_ctl(ep, EPOLL_CTL_ADD, c->fd, &ee) == -1) {
//    ngx_log_error(NGX_LOG_ALERT, c->log, ngx_errno,
//                      "epoll_ctl(EPOLL_CTL_ADD, %d) failed", c->fd);
//        return NGX_ERROR;
//    }

    $c->read->active = 1;
    $c->write->active = 1;

    return NGX_OK;
}


function ngx_epoll_del_connection(ngx_connection_t $c,  $flags)
{
//    int                 op;
//    struct epoll_event  ee;

    /*
     * when the file descriptor is closed the epoll automatically deletes
     * it from its queue so we do not need to delete explicitly the event
     * before the closing the file descriptor
     */

    if ($flags & NGX_CLOSE_EVENT) {
        $c->read->active = 0;
        $c->write->active = 0;
        return NGX_OK;
    }

    ngx_log_debug1(NGX_LOG_DEBUG_EVENT, $c->log, 0,
                   "epoll del connection: fd:%d", $c->fd);

//    op = EPOLL_CTL_DEL;
//    ee.events = 0;
//    ee.data.ptr = NULL;

//    if (epoll_ctl(ep, op, c->fd, &ee) == -1) {
//    ngx_log_error(NGX_LOG_ALERT, c->log, ngx_errno,
//                      "epoll_ctl(%d, %d) failed", op, c->fd);
//        return NGX_ERROR;
//    }
    $c->read->libevent->del();
    $c->write->libevent->del();

    $c->read->active = 0;
    $c->write->active = 0;

    return NGX_OK;
}

function ngx_epoll_process_events(ngx_connection_t $c, $timer, $flags)
{
//    int                events;
//    uint32_t           revents;
//    ngx_int_t          instance, i;
//    ngx_uint_t         level;
//    ngx_err_t          err;
//    ngx_event_t       *rev, *wev;
//    ngx_queue_t       *queue;
//    ngx_connection_t  *c;

    /* NGX_TIMER_INFINITE == INFTIM */

    ngx_log_debug1(NGX_LOG_DEBUG_EVENT, $cycle->log, 0,
                   "epoll timer: %M", $timer);

    //todo to replace by libevent
    $events = epoll_wait($ep, $event_list,  $nevents, $timer);

    $err = ($events == -1) ? socket_last_error() : 0;

    if ($flags & NGX_UPDATE_TIME || ngx_event_timer_alarm()) {
        ngx_time_update();
    }

    if ($err) {
        if ($err == NGX_EINTR) {

            if (ngx_event_timer_alarm()) {
                ngx_event_timer_alarm(0);
                return NGX_OK;
            }

            $level = NGX_LOG_INFO;

        } else {
            $level = NGX_LOG_ALERT;
        }

        ngx_log_error($level, $cycle->log, $err, "epoll_wait() failed");
        return NGX_ERROR;
    }

    if ($events == 0) {
        if ($timer != NGX_TIMER_INFINITE) {
            return NGX_OK;
        }

        ngx_log_error(NGX_LOG_ALERT, $cycle->log, 0,
                      "epoll_wait() returned no events without timeout");
        return NGX_ERROR;
    }

    for ($i = 0; $i < $events; $i++) {
        $c = $event_list[$i]->data->ptr;

        $instance = $c & 1;
        $c =  ( $c &  ~1);

        $rev = $c->read;

        if ($c->fd == -1 ) {

            /*
             * the stale event from a file descriptor
             * that was just closed in this iteration
             */

            ngx_log_debug1(NGX_LOG_DEBUG_EVENT, $cycle->log, 0,
                           "epoll: stale event %p", $c);
            continue;
        }

        $revents = $event_list[$i]->events;

        ngx_log_debug3(NGX_LOG_DEBUG_EVENT, $cycle->log, 0,
                       "epoll: fd:%d ev:%04XD d:%p",
                       $c->fd, $revents, $event_list[$i]->data->ptr);

        if ($revents & (EPOLLERR|EPOLLHUP)) {
            ngx_log_debug2(NGX_LOG_DEBUG_EVENT, $cycle->log, 0,
                           "epoll_wait() error on fd:%d ev:%04XD",
                           $c->fd, $revents);
        }



        if (($revents & (EPOLLERR|EPOLLHUP))
            && ($revents & (EPOLLIN|EPOLLOUT)) == 0)
        {
            /*
             * if the error events were returned without EPOLLIN or EPOLLOUT,
             * then add these flags to handle the events at least in one
             * active handler
             */

            $revents |= EPOLLIN|EPOLLOUT;
        }

        if (($revents & EPOLLIN) && $rev->active) {


            $rev->ready = 1;

            if ($flags & NGX_POST_EVENTS) {
                $queue = $rev->accept ? ngx_posted_accept_events()
                : ngx_posted_events();

                ngx_post_event($rev, $queue);

            } else {
                $rev->handler($rev);
            }
        }

        $wev = $c->write;

        if (($revents & EPOLLOUT) && $wev->active) {

            if ($c->fd == -1 || $wev->instance != $instance) {

                /*
                 * the stale event from a file descriptor
                 * that was just closed in this iteration
                 */

                ngx_log_debug1(NGX_LOG_DEBUG_EVENT, $cycle->log, 0,
                               "epoll: stale event %p", $c);
                continue;
            }

            $wev->ready = 1;

            if ($flags & NGX_POST_EVENTS) {
                ngx_post_event($wev, ngx_posted_events());

            } else {
                $wev->handler($wev);
            }
        }
    }

    return NGX_OK;
}

