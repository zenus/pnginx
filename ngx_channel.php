<?php

/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 15-12-18
 * Time: 下午10:30
 */

class ngx_channel_t {
/**    ngx_uint_t **/ private $command;
/**     ngx_pid_t **/ private  $pid;
/**     ngx_int_t **/ private  $slot;
/**     ngx_fd_t ***/ private  $fd;
    public function __set($name,$value){
       $this->$name = $value;
    }
    public function __get($name){
       return $this->$name;
    }
}

function ngx_close_channel( $fd, ngx_log $log)
{
    if (fclose($fd[0]) == false) {
        ngx_log_error(NGX_LOG_ALERT, $log, socket_last_error(), "close() channel failed");
    }

    if (fclose($fd[1]) == false) {
        ngx_log_error(NGX_LOG_ALERT, $log, socket_last_error(), "close() channel failed");
      }
}

function ngx_write_channel( $s, ngx_channel_t $ch,  ngx_log $log)
{
//    ssize_t             n;
//    ngx_err_t           err;
//    struct iovec        iov[1];
//    struct msghdr       msg;

    $n = socket_sendmsg($s, get_object_vars($ch), 0);

    if ($n == -1) {
        $err = socket_last_error();
        if ($err == NGX_EAGAIN) {
            return NGX_AGAIN;
        }
        ngx_log_error(NGX_LOG_ALERT, $log, $err, "sendmsg() failed");
        return NGX_ERROR;
    }

    return NGX_OK;
}

function ngx_add_channel_event(ngx_cycle_t $cycle,  $fd,  $event, callable $handler)
{
//    ngx_event_t       *ev, *rev, *wev;
//    ngx_connection_t  *c;

    $c = ngx_get_connection($fd, $cycle->log);

    if ($c == NULL) {
        return NGX_ERROR;
    }

    //c->pool = cycle->pool;

    $rev = $c->read;
    $wev = $c->write;

    $rev->log = $cycle->log;
    $wev->log = $cycle->log;

    $rev->channel = 1;
    $wev->channel = 1;

    $ev = ($event == NGX_READ_EVENT) ? $rev : $wev;

    $ev->handler = $handler;

    if (is_callable('ngx_add_conn') && (ngx_event_flags() & NGX_USE_EPOLL_EVENT) == 0) {
        if (ngx_add_conn($c) == NGX_ERROR) {
            ngx_free_connection($c);
            return NGX_ERROR;
        }

    } else {
        if (ngx_add_event($ev, $event, 0) == NGX_ERROR) {
            ngx_free_connection($c);
            return NGX_ERROR;
        }
    }

    return NGX_OK;
}


function ngx_read_channel($s, ngx_channel_t $ch, ngx_log $log)
{
//    ssize_t             n;
//    ngx_err_t           err;
//    struct iovec        iov[1];
//    struct msghdr       msg;


//    iov[0].iov_base = (char *) ch;
//    iov[0].iov_len = size;
//
//    msg.msg_name = NULL;
//    msg.msg_namelen = 0;
//    msg.msg_iov = iov;
//    msg.msg_iovlen = 1;
//
//
//    msg.msg_accrights = (caddr_t) &fd;
//    msg.msg_accrightslen = sizeof(int);

    $n = socket_recvmsg($s, $msg, 0);

    if ($n == -1) {
        $err = socket_last_error();
        if ($err == NGX_EAGAIN) {
            return NGX_AGAIN;
        }

        ngx_log_error(NGX_LOG_ALERT, $log, $err, "recvmsg() failed");
        return NGX_ERROR;
    }

    if ($n == 0) {
        ngx_log_debug0(NGX_LOG_DEBUG_CORE, $log, 0, "recvmsg() returned zero");
        return NGX_ERROR;
    }

//    if ((size_t) n < sizeof(ngx_channel_t)) {
//        ngx_log_error(NGX_LOG_ALERT, $log, 0,
//        "recvmsg() returned not enough data: %z", n);
//    return NGX_ERROR;
//    }

    if ($ch->command == NGX_CMD_OPEN_CHANNEL) {
//    if (msg.msg_accrightslen != sizeof(int)) {
//        ngx_log_error(NGX_LOG_ALERT, log, 0,
//            "recvmsg() returned no ancillary data");
//        return NGX_ERROR;
//    }

        $ch->fd = $msg;
    }


    return $n;
}



