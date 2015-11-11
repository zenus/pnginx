<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 15-11-8
 * Time: 上午7:10
 */

include_once 'ngx_log.php';

function ngx_os_specific_init(ngx_log $log)
{

//    if(empty(php_uname())){
//        ngx_log_error(NGX_LOG_ALERT, $log, EPUND, "uname() failed");
//        return NGX_ERROR;
//    }

//    (void) ngx_cpystrn(ngx_linux_kern_ostype, (u_char *) u.sysname,
      ngx_cfg('ngx_linux_kern_ostype', php_uname('s'));
//                       sizeof(ngx_linux_kern_ostype));
//
//    (void) ngx_cpystrn(ngx_linux_kern_osrelease, (u_char *) u.release,
//                       sizeof(ngx_linux_kern_osrelease));
      ngx_cfg('ngx_linux_kern_osrelease', php_uname('r'));

    //todo should find where ngx_linux_io init
    //ngx_os_io = ngx_linux_io;

    return NGX_OK;
}