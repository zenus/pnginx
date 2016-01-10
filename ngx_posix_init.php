<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 15-11-8
 * Time: 上午7:04
 * @param ngx_log $log
 * @return int
 */

function  ngx_ncpu($i = null){
    static $ngx_ncpu = null;
    if(!is_null($i)){
       $ngx_ncpu = $i ;
    }else{
       return $ngx_ncpu;
    }
}

function ngx_max_sockets($i = null){
    static $ngx_max_sockets = null;
    if(!is_null($i)){
        $ngx_max_sockets = $i;
    }else{
        return $ngx_max_sockets;
    }
}

function ngx_inherited_nonblocking($i = null){
    static $ngx_inherited_nonblocking = null;
    if(!is_null($i)){
       $ngx_inherited_nonblocking = $i;
    }else{
       return $ngx_inherited_nonblocking;
    }
}

function ngx_os_init(ngx_log $log)
{


    if (ngx_os_specific_init($log) != NGX_OK) {
        return NGX_ERROR;
    }

    if (ngx_init_setproctitle($log) != NGX_OK) {
        return NGX_ERROR;
    }

//#if (NGX_HAVE_SC_NPROCESSORS_ONLN)
//    if (ngx_ncpu == 0) {
//        ngx_ncpu = sysconf(_SC_NPROCESSORS_ONLN);
//    }
//#endif
//
//    if (ngx_ncpu < 1) {
    //todo  find way to get the  cpu amount
        ngx_ncpu(1);
    //}

    //todo find way to get cup info
///    ngx_cpuinfo();

    if (empty($rlimit = posix_getrlimit())) {
        ngx_log_error(NGX_LOG_ALERT, $log, posix_get_last_error(),
            "getrlimit(RLIMIT_NOFILE) failed)");
        return NGX_ERROR;
    }

    ngx_max_sockets($rlimit['soft openfiles']);

//#if (NGX_HAVE_INHERITED_NONBLOCK || NGX_HAVE_ACCEPT4)
    // todo find how to do this
    ngx_inherited_nonblocking(1);
//#else
//    ngx_inherited_nonblocking = 0;
//#endif

    //todo find why do this
//    srandom(ngx_time());

    return NGX_OK;
}

function ngx_os_io()
{
     static $ngx_os_io ;
     $obj = new ngx_os_io_t();
    //todo finish function
    if(is_null($ngx_os_io)){
       $ngx_os_io = $obj;
    }
    $ngx_os_io->recv = 'ngx_unix_recv';
    $ngx_os_io->recv_chain = 'ngx_readv_chain';
    $ngx_os_io->udp_recv ='ngx_udp_unix_recv';
    $ngx_os_io->send='ngx_unix_send';
    $ngx_os_io->send_chain='ngx_writev_chain';
    $ngx_os_io->flags=0;
    return $ngx_os_io;
}

function ngx_os_status(ngx_log $log)
{
    ngx_log_error(NGX_LOG_NOTICE, $log, 0, NGINX_VER_BUILD);
    ngx_os_specific_status($log);
    $limit  = posix_getrlimit();
    ngx_log_error(NGX_LOG_NOTICE, $log, 0,
        "getrlimit(RLIMIT_NOFILE): %r:%r",
        array($limit['soft openfiles'], $limit['hard openfiles']));
}



