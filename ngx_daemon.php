<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 15-12-13
 * Time: 下午7:43
 * @param ngx_log $log
 * @return int
 */

function ngx_daemon(ngx_log $log)
{

//int  fd;

    switch (pcntl_fork()) {
        case -1:
            ngx_log_error(NGX_LOG_EMERG, $log, pcntl_get_last_error(), "fork() failed");
            return NGX_ERROR;

        case 0:
            break;

        default:
            exit(0);
    }

    ngx_pid(ngx_getpid());

    if (posix_setsid() == -1) {
        ngx_log_error(NGX_LOG_EMERG, $log, posix_get_last_error(), "setsid() failed");
        return NGX_ERROR;
    }
    umask(0);
    //todo instead of redirect to /dev/null, i simple close them;
    if (fclose(STDIN) == false) {
        ngx_log_error(NGX_LOG_EMERG, $log, NGX_FERROR, "close stdin failed");
        return NGX_ERROR;
    }
    if (fclose(STDOUT) == false) {
        ngx_log_error(NGX_LOG_EMERG, $log, NGX_FERROR, "close stdout failed");
        return NGX_ERROR;
    }
    if (fclose(STDERR) == false) {
        ngx_log_error(NGX_LOG_EMERG, $log, NGX_FERROR, "close stderr failed");
        return NGX_ERROR;
    }
    return NGX_OK;
//    $STDIN = fopen('/dev/null', 'r');
//    $STDOUT = fopen('/dev/null', 'wb');
//    $STDERR = fopen('/dev/null', 'wb');


}

