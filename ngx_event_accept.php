<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 15-12-16
 * Time: 下午10:44
 */


define('NGX_TCP_NOPUSH_UNSET', 0);
define('NGX_TCP_NOPUSH_SET', 1);
define('NGX_TCP_NOPUSH_DISABLED', 2);

define('NGX_TCP_NODELAY_UNSET', 0);
define('NGX_TCP_NODELAY_SET', 1);
define('NGX_TCP_NODELAY_DISABLED', 2);


function ngx_enable_accept_events(ngx_cycle_t $cycle)
{
//ngx_uint_t         i;
//    ngx_listening_t   *ls;
//    ngx_connection_t  *c;

    $ls = $cycle->listening;
    for ($i = 0; $i < count($cycle->listening); $i++) {
            $c = $ls[$i]->connection;
            if ($c == NULL || $c->read->active) {
            continue;
        }
         //todo should complete event method
        if (ngx_add_event($c->read, NGX_READ_EVENT, 0) == NGX_ERROR) {
                return NGX_ERROR;
           }
    }

    return NGX_OK;
}

function ngx_disable_accept_events(ngx_cycle_t $cycle,  $all)
{
//    ngx_uint_t         i;
//    ngx_listening_t   *ls;
//    ngx_connection_t  *c;

    $ls = $cycle->listening;
    for ($i = 0; $i < count($cycle->listening); $i++) {

        $c = $ls[$i]->connection;

        if ($c == NULL || !$c->read->active) {
        continue;
    }

//#if (NGX_HAVE_REUSEPORT)
//
//        /*
//         * do not disable accept on worker's own sockets
//         * when disabling accept events due to accept mutex
//         */
//
//        if (ls[i].reuseport && !all) {
//        continue;
//    }
//
//#endif

//todo should complete event method
        if (ngx_del_event($c->read, NGX_READ_EVENT, NGX_DISABLE_EVENT)
            == NGX_ERROR)
        {
            return NGX_ERROR;
        }
    }

    return NGX_OK;
}


function ngx_trylock_accept_mutex(ngx_cycle_t $cycle)
{
//todo should complete lock method
    if (ngx_shmtx_trylock(ngx_accept_mutex())) {

        ngx_log_debug0(NGX_LOG_DEBUG_EVENT, $cycle->log, 0,
                       "accept mutex locked");

        if (ngx_accept_mutex_held() && ngx_accept_events() == 0) {
            return NGX_OK;
        }

        if (ngx_enable_accept_events($cycle) == NGX_ERROR) {
//todo should complete lock method
            ngx_shmtx_unlock(ngx_accept_mutex());
            return NGX_ERROR;
        }

        ngx_accept_events(0);
        ngx_accept_mutex_held(1);

        return NGX_OK;
    }

    ngx_log_debug1(NGX_LOG_DEBUG_EVENT, $cycle->log, 0,
                   "accept mutex lock failed: %ui", ngx_accept_mutex_held());

    if (ngx_accept_mutex_held()) {
        if (ngx_disable_accept_events($cycle, 0) == NGX_ERROR) {
            return NGX_ERROR;
        }

        ngx_accept_mutex_held(0);
    }

    return NGX_OK;
}

function ngx_event_accept(ngx_event_t $ev)
{
//socklen_t          socklen;
//    ngx_err_t          err;
//    ngx_log_t         *log;
//    ngx_uint_t         level;
//    ngx_socket_t       s;
//    ngx_event_t       *rev, *wev;
//    ngx_listening_t   *ls;
//    ngx_connection_t  *c, *lc;
//    ngx_event_conf_t  *ecf;
//    u_char             sa[NGX_SOCKADDRLEN];
#if (NGX_HAVE_ACCEPT4)
//    static ngx_uint_t  use_accept4 = 1;
#endif

    $ngx_cycle = ngx_cycle();

    if ($ev->timedout) {
        if (ngx_enable_accept_events($ngx_cycle) != NGX_OK) {
            return;
        }

        $ev->timedout = 0;
    }

    $ecf = ngx_event_get_conf($ngx_cycle->conf_ctx, ngx_event_core_module());

    if (!(ngx_event_flags() & NGX_USE_KQUEUE_EVENT)) {
        $ev->available = $ecf->multi_accept;
    }

    $lc = $ev->data;
    $ls = $lc->listening;
    $ev->ready = 0;

    ngx_log_debug2(NGX_LOG_DEBUG_EVENT, $ev->log, 0,
                   "accept on %V, ready: %d", $ls->addr_text, $ev->available);

    do {
        //socklen = NGX_SOCKADDRLEN;

        //todo refer in php manual, it may have problem
        $s = socket_accept($lc->fd);

        if ($s == false) {
            $err = socket_last_error();
            if ($err == NGX_EAGAIN) {
                ngx_log_debug0(NGX_LOG_DEBUG_EVENT, $ev->log, $err,
                               "accept() not ready");
                return;
            }

            $level = NGX_LOG_ALERT;

            if ($err == NGX_ECONNABORTED) {
                $level = NGX_LOG_ERR;

            } else if ($err == NGX_EMFILE || $err == NGX_ENFILE) {
                $level = NGX_LOG_CRIT;
            }


            ngx_log_error($level, $ev->log, $err, "accept() failed");

            if ($err == NGX_ECONNABORTED) {
                if (ngx_event_flags() & NGX_USE_KQUEUE_EVENT) {
                    $ev->available--;
                }

                if ($ev->available) {
                    continue;
                }
            }

            if ($err == NGX_EMFILE || $err == NGX_ENFILE) {
                if (ngx_disable_accept_events( $ngx_cycle, 1)
                    != NGX_OK)
                {
                    return;
                }

                if (ngx_use_accept_mutex()) {
                    if (ngx_accept_mutex_held()) {
                        ngx_shmtx_unlock(ngx_accept_mutex());
                        ngx_accept_mutex_held(0);
                    }

                    ngx_accept_disabled(1);

                } else {
                    ngx_add_timer($ev, $ecf->accept_mutex_delay);
                }
            }

            return;
        }



        ngx_accept_disabled($ngx_cycle->connection_n / 8 - $ngx_cycle->free_connection_n);

        $c = ngx_get_connection($s, $ev->log);

        if ($c == NULL) {
            if (ngx_close_socket($s) == false) {
                ngx_log_error(NGX_LOG_ALERT, $ev->log, socket_last_error(),
                              ngx_close_socket_n ." failed");
            }

            return;
        }


//        c->pool = ngx_create_pool(ls->pool_size, ev->log);
//        if ($c->pool == NULL) {
//            ngx_close_accepted_connection($c);
//            return;
//        }

//        $c->sockaddr = ngx_palloc($c->pool, socklen);
//        if (c->sockaddr == NULL) {
//            ngx_close_accepted_connection($c);
//            return;
//        }

//        ngx_memcpy(c->sockaddr, sa, socklen);
//        $c->sockaddr =

//        log = ngx_palloc(c->pool, sizeof(ngx_log_t));
//        if (log == NULL) {
//            ngx_close_accepted_connection(c);
//            return;
//        }

        /* set a blocking mode for iocp and non-blocking mode for others */

        if (ngx_inherited_nonblocking()) {
            if (ngx_event_flags() & NGX_USE_IOCP_EVENT) {
                if (ngx_blocking($s) == false) {
                    ngx_log_error(NGX_LOG_ALERT, $ev->log, socket_last_error(),
                                  ngx_blocking_n ." failed");
                    ngx_close_accepted_connection($c);
                    return;
                }
            }

        } else {
            if (!(ngx_event_flags() & NGX_USE_IOCP_EVENT)) {
                if (ngx_nonblocking($s) == false) {
                    ngx_log_error(NGX_LOG_ALERT, $ev->log, socket_last_error(),
                                  ngx_nonblocking_n ." failed");
                    ngx_close_accepted_connection($c);
                    return;
                }
            }
        }

        $log = $ls->log;

        $ngx_io = ngx_io();
        $c->recv = $ngx_io->recv;
        $c->send = $ngx_io->send;
        $c->recv_chain = $ngx_io->recv_chain;
        $c->send_chain = $ngx_io->send_chain;

        $c->log = log;
        //$c->pool->log = log;

        //todo default value
        $c->socklen = 0;
        $c->listening = $ls;
        $c->local_sockaddr = $ls->sockaddr;
        $c->local_socklen = $ls->socklen;

        $c->unexpected_eof = 1;

        if ($c->sockaddr->sa_family == AF_UNIX) {
            $c->tcp_nopush = NGX_TCP_NOPUSH_DISABLED;
            $c->tcp_nodelay = NGX_TCP_NODELAY_DISABLED;
        }

        $rev = $c->read;
        $wev = $c->write;

        $wev->ready = 1;

        if (ngx_event_flags() & NGX_USE_IOCP_EVENT) {
            $rev->ready = 1;
        }

        if ($ev->deferred_accept) {
            $rev->ready = 1;
        }

        $rev->log = $log;
        $wev->log = $log;

        /*
         * TODO: MT: - ngx_atomic_fetch_add()
         *             or protection by critical section or light mutex
         *
         * TODO: MP: - allocated in a shared memory
         *           - ngx_atomic_fetch_add()
         *             or protection by critical section or light mutex
         */

        //todo .....
//        $c->number = ngx_atomic_fetch_add(ngx_connection_counter, 1);



        if ($ls->addr_ntop) {

            $c->addr_text = ngx_sock_ntop($c->sockaddr,$c->addr_text, 0);
        }


        if (is_callable('ngx_add_conn') && (ngx_event_flags() & NGX_USE_EPOLL_EVENT) == 0) {
            if (ngx_add_conn($c) == NGX_ERROR) {
                ngx_close_accepted_connection($c);
                return;
            }
        }

        $log->data = NULL;
        $log->handler = NULL;

        $ls->handler($c);

        if (ngx_event_flags() & NGX_USE_KQUEUE_EVENT) {
            $ev->available--;
        }

    } while ($ev->available);
}

function ngx_close_accepted_connection(ngx_connection_t $c)
{
//ngx_socket_t  fd;

    ngx_free_connection($c);

    $fd = $c->fd;
    $c->fd =  -1;

    if (ngx_close_socket($fd) == false) {
        ngx_log_error(NGX_LOG_ALERT, $c->log, socket_last_error(),
                      ngx_close_socket_n ." failed");
    }

}

