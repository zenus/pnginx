<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 15-11-5
 * Time: 下午9:56
 */

//int              ngx_argc;
//char           **ngx_argv;
//char           **ngx_os_argv;

function ngx_getpid(){
   return posix_getpid();
}