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
        $ngx_epoll_module_ctx->add = 'ngx_epoll_add_event';             /* add an event */
       $ngx_epoll_module_ctx->del =  'ngx_epoll_del_event';             /* delete an event */
       $ngx_epoll_module_ctx->enable = 'ngx_epoll_add_event';             /* enable an event */
       $ngx_epoll_module_ctx->disable = 'ngx_epoll_del_event';             /* disable an event */
       $ngx_epoll_module_ctx->add_conn = 'ngx_epoll_add_connection';        /* add an connection */
       $ngx_epoll_module_ctx->del_conn = 'ngx_epoll_del_connection';        /* delete an connection */
       $ngx_epoll_module_ctx->notify = NULL;                            /* trigger a notify */
       $ngx_epoll_module_ctx->process_events = 'ngx_epoll_process_events';        /* process the events */
       $ngx_epoll_module_ctx->init ='ngx_epoll_init';                  /* init the events */
       $ngx_epoll_module_ctx->done = 'ngx_epoll_done';                  /* done the events */
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

    events = (uint32_t) event;

    if (event == NGX_READ_EVENT) {
        e = c->write;
        prev = EPOLLOUT;
#if (NGX_READ_EVENT != EPOLLIN|EPOLLRDHUP)
        events = EPOLLIN|EPOLLRDHUP;
#endif

    } else {
        e = c->read;
        prev = EPOLLIN|EPOLLRDHUP;
#if (NGX_WRITE_EVENT != EPOLLOUT)
        events = EPOLLOUT;
#endif
    }

    if (e->active) {
    op = EPOLL_CTL_MOD;
    events |= prev;

} else {
    op = EPOLL_CTL_ADD;
}

    ee.events = events | (uint32_t) flags;
    ee.data.ptr = (void *) ((uintptr_t) c | ev->instance);

    ngx_log_debug3(NGX_LOG_DEBUG_EVENT, ev->log, 0,
                   "epoll add event: fd:%d op:%d ev:%08XD",
                   c->fd, op, ee.events);

    if (epoll_ctl(ep, op, c->fd, &ee) == -1) {
    ngx_log_error(NGX_LOG_ALERT, ev->log, ngx_errno,
                      "epoll_ctl(%d, %d) failed", op, c->fd);
        return NGX_ERROR;
    }

    ev->active = 1;
#if 0
    ev->oneshot = (flags & NGX_ONESHOT_EVENT) ? 1 : 0;
#endif

    return NGX_OK;
}


function ngx_epoll_init(ngx_cycle_t $cycle, $timer)
{
    //ngx_epoll_conf_t  *epcf;

    $epcf = ngx_event_get_conf($cycle->conf_ctx, ngx_epoll_module());

    if (ep == -1) {
        ep = epoll_create(cycle->connection_n / 2);

        if (ep == -1) {
            ngx_log_error(NGX_LOG_EMERG, cycle->log, ngx_errno,
                          "epoll_create() failed");
            return NGX_ERROR;
        }




    if (nevents < epcf->events) {
    if (event_list) {
        ngx_free(event_list);
    }

    event_list = ngx_alloc(sizeof(struct epoll_event) * epcf->events,
                               cycle->log);
        if (event_list == NULL) {
            return NGX_ERROR;
        }
    }

    nevents = epcf->events;

    ngx_io = ngx_os_io;

    $ngx_event_actions = ngx_epoll_module_ctx()actions;

    ngx_event_flags(NGX_USE_LEVEL_EVENT
        |NGX_USE_GREEDY_EVENT
        |NGX_USE_EPOLL_EVENT);

    return NGX_OK;
}
