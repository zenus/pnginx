<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 16-1-26
 * Time: 上午7:14
 */

function ngx_unix_send(ngx_connection_t $c, $buf, $size)
{
//    ssize_t       n;
//    ngx_err_t     err;
//    ngx_event_t  *wev;

    $wev = $c->write;


    for ( ;; ) {
        $n = socket_send($c->fd, $buf, $size, 0);

        ngx_log_debug3(NGX_LOG_DEBUG_EVENT, $c->log, 0,
                       "send: fd:%d %d of %d", $c->fd, $n, $size);

        if ($n > 0) {
            if ($n <  $size) {
                $wev->ready = 0;
            }

            $c->sent += $n;

            return $n;
        }

        $err = socket_last_error();

        if ($n == 0) {
            ngx_log_error(NGX_LOG_ALERT, $c->log, $err, "send() returned zero");
            $wev->ready = 0;
            return $n;
        }

        if ($err == NGX_EAGAIN || $err == NGX_EINTR) {
            $wev->ready = 0;

            ngx_log_debug0(NGX_LOG_DEBUG_EVENT, $c->log, $err,
                           "send() not ready");

            if ($err == NGX_EAGAIN) {
                return NGX_AGAIN;
            }

        } else {
            $wev->error = 1;
            ngx_connection_error($c, $err, "send() failed");
            return NGX_ERROR;
        }
    }
}