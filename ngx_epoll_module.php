<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 16-1-24
 * Time: 下午3:22
 */

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
