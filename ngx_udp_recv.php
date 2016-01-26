<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 16-1-26
 * Time: 上午7:08
 */

function ngx_udp_unix_recv(ngx_connection_t $c, $buf, $size)
{
//    ssize_t       n;
//    ngx_err_t     err;
//    ngx_event_t  *rev;

    $rev = $c->read;

    do {

        $n = socket_recv($c->fd, $buf, $size, 0);

        ngx_log_debug3(NGX_LOG_DEBUG_EVENT, $c->log, 0,
                       "recv: fd:%d %d of %d", $c->fd, $n, $size);

        if ($n >= 0) {
            return $n;
        }

        $err = socket_last_error();

        if ($err == NGX_EAGAIN || $err == NGX_EINTR) {
            ngx_log_debug0(NGX_LOG_DEBUG_EVENT, $c->log, $err,
                           "recv() not ready");
            $n = NGX_AGAIN;

        } else {
            $n = ngx_connection_error($c, $err, "recv() failed");
            break;
        }

    } while ($err == NGX_EINTR);

    $rev->ready = 0;

    if ($n == NGX_ERROR) {
        $rev->error = 1;
    }

    return $n;
}