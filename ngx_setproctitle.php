<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 15-11-9
 * Time: 下午10:26
 */
include_once 'ngx_log.php';

function ngx_init_setproctitle(ngx_log $log) {
   return  NGX_OK;
}

function ngx_setproctitle($title)
{
//u_char     *p;


    ngx_os_argv(1,NULL);

    $p = 'nginx: ';
    ngx_os_argv(0,$p);
//    p = ngx_cpystrn((u_char *) ngx_os_argv[0], (u_char *) "nginx: ",
//                    ngx_os_argv_last - ngx_os_argv[0]);

    //p = ngx_cpystrn(p, (u_char *) title, ngx_os_argv_last - (char *) p);
    $p .= $title;

    //
//    if (ngx_os_argv_last - (char *) p) {
//
//       ngx_memset(p, NGX_SETPROCTITLE_PAD, ngx_os_argv_last - (char *) p);
//    }
    cli_set_process_title($p);
    $ngx_cycle = ngx_cycle();
    ngx_log_debug1(NGX_LOG_DEBUG_CORE, $ngx_cycle->log, 0,
                   "setproctitle: \"%s\"", ngx_os_argv(0));
}