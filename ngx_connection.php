<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 15-11-7
 * Time: 下午7:16
 */
 define('NGX_ERROR_ALERT', 0);
 define('NGX_ERROR_ERR',1);
 define('NGX_ERROR_INFO',2);
 define('NGX_ERROR_IGNORE_ECONNRESET',3);
 define('NGX_ERROR_IGNORE_EINVAL',4);
class ngx_connection_t {
    /**  void   **/      private       $data;
    /**  ngx_event_t **/ private      $read;
    /**  ngx_event_t **/ private      $write;

    /**  ngx_socket_t **/private       $fd;

    /**  ngx_recv_pt  **/ private      $recv;
    /**  ngx_send_pt  **/     private  $send;
    /**  ngx_recv_chain_pt **/ private $recv_chain;
    /**  ngx_send_chain_pt **/ private $send_chain;

    /**  ngx_listening_t   **/ private $listening;

    /**  off_t             **/  private $sent;

    /**  ngx_log_t         **/ private $log;

    //  ngx_pool_t         *pool;

    /**  struct sockaddr   **/ private $sockaddr;
    /**  socklen_t         **/ private $socklen;
    /**  ngx_str_t         **/ private $addr_text;

    /**  ngx_str_t         **/ private $proxy_protocol_addr;


    /**  struct sockaddr  **/ private $local_sockaddr;
    /**  socklen_t        **/ private  $local_socklen;

    //  ngx_buf_t        buffer;

    /**  ngx_queue_t      **/ private  $queue;

    /**  ngx_atomic_uint_t **/ private $number;

    /**  ngx_uint_t     **/    private $requests;

   /** unsigned  **/       private   $buffered;

   /** unsigned  **/       private   $log_error;     /* ngx_connection_log_error_e */

   /** unsigned  **/       private   $unexpected_eof;
   /** unsigned  **/       private   $timedout;
   /** unsigned  **/       private   $error;
   /** unsigned  **/       private   $destroyed;

   /** unsigned  **/       private   $idle;
   /** unsigned  **/       private   $reusable;
   /** unsigned  **/       private   $close;

   /** unsigned  **/       private   $sendfile;
   /** unsigned  **/       private   $sndlowat;
   /** unsigned  **/       private   $tcp_nodelay;   /* ngx_connection_tcp_nodelay_e */
   /** unsigned  **/       private   $tcp_nopush;    /* ngx_connection_tcp_nopush_e */

   /** unsigned  **/       private   $need_last_buf;

    public function __set($property,$value){
       $this->$property = $value;
    }

    public function __get($property){
       return $this->$property;
    }

//#if (NGX_HAVE_IOCP)
//    unsigned            accept_context_updated:1;
//#endif
//
//#if (NGX_HAVE_AIO_SENDFILE)
//    unsigned            busy_count:2;
//#endif

};

class ngx_listening_s {
/**  ngx_socket_t  **/   private  $fd;

/**    struct sockaddr **/ private   $sockaddr;
/**  socklen_t  **/   private  $socklen;    /* size of sockaddr */
/**  size_t  **/   private  $addr_text_max_len;
/**  ngx_str_t  **/   private  $addr_text;
/**  int  **/   private  $type;
/**  int  **/   private  $backlog;
/**  int  **/   private  $rcvbuf;
/**  int  **/   private  $sndbuf;
#if (NGX_HAVE_KEEPALIVE_TUNABLE)
/**  int  **/   private  $keepidle;
/**  int  **/   private  $keepintvl;
/**  int  **/   private  $keepcnt;
#endif

    /* handler of accepted connection */
/**  ngx_connection_handler_pt  **/   private  $handler;
/**  void  **/   private  $servers;  /* array of ngx_http_in_addr_t, for example */
/**  ngx_log_t  **/   private  $log;
/**  ngx_log_t  **/   private  $logp;
/**  size_t  **/   private  $pool_size;
    /* should be here because of the AcceptEx() preread */
/**  size_t  **/   private  $post_accept_buffer_size;
    /* should be here because of the deferred accept */
/**  ngx_msec_t  **/   private  $post_accept_timeout;
/**  ngx_listening_t  **/   private  $previous;
/**  ngx_connection_t  **/   private  $connection;
/**  ngx_uint_t  **/   private  $worker;
/**  unsigned  **/   private  $open;
/**  unsigned  **/   private  $remain;
/**  unsigned  **/   private  $ignore;
/**  unsigned  **/   private  $bound;       /* already bound */
/**  unsigned  **/   private  $inherited;   /* inherited from previous process */
/**  unsigned  **/   private  $nonblocking_accept;
/**  unsigned  **/   private  $listen;
/**  unsigned  **/   private  $nonblocking;
/**  unsigned  **/   private  $shared;    /* shared between threads or processes */
/**  unsigned  **/   private  $addr_ntop;

#if (NGX_HAVE_INET6 && defined IPV6_V6ONLY)
/**  unsigned  **/   private  $ipv6only;
#endif
/**  unsigned  **/   private  $keepalive;

#if (NGX_HAVE_DEFERRED_ACCEPT)
/**  unsigned  **/   private  $deferred_accept;
/**  unsigned  **/   private  $delete_deferred;
/**  unsigned  **/   private  $add_deferred;
#ifdef SO_ACCEPTFILTER
/**  char  **/   private  $accept_filter;
#endif
#endif


    public function __set($property,$value){
       $this->$property = $value;
    }

   public function __get($property){
       return $this->$property;
    }
}

/**
 * @param ngx_cycle_s $cycle
 * @return int
 */
function ngx_set_inherited_sockets(ngx_cycle_t &$cycle)
{

    //todo should know how to use this in php

    for ($i = 0; $i < count($cycle->listening); $i++) {

        $ls = $cycle->listening;


        //todo A valid socket resource created with socket_create() or socket_accept().
        if (socket_getsockname($ls[$i]->fd, $ls[$i]->sockaddr, $ls[$i]->port) == -1) {
            ngx_log_error(NGX_LOG_CRIT, $cycle->log, socket_last_error(),
                "getsockname() of the inherited " .
                "socket #%d failed", $ls[$i]->fd);
            $ls[$i]->ignore = 1;
            continue;
        }

        $ls[$i]->backlog = NGX_LISTEN_BACKLOG;


        if (socket_get_option($ls[$i]->fd, SOL_SOCKET, SO_RCVBUF) == false) {
            ngx_log_error(NGX_LOG_ALERT, $cycle->log, socket_last_error(),
                "getsockopt(SO_RCVBUF) %V failed, ignored",
                $ls[$i]->sockaddr);

            $ls[$i]->rcvbuf = -1;
        }


        if (socket_get_option($ls[$i]->fd, SOL_SOCKET, SO_SNDBUF) == false) {
            ngx_log_error(NGX_LOG_ALERT, $cycle->log, socket_last_error(),
                "getsockopt(SO_SNDBUF) %V failed, ignored",
                $ls[$i]->sockaddr);

            $ls[$i]->sndbuf = -1;
        }


//todo php can not get tcp_defer_accept option
//        if (getsockopt(ls[i].fd, IPPROTO_TCP, TCP_DEFER_ACCEPT, &timeout, &olen)
//            == -1)
    }

    return NGX_OK;
}

function ngx_open_listening_sockets(ngx_cycle_t $cycle)
{
//int               reuseaddr;
//    ngx_uint_t        i, tries, failed;
//    ngx_err_t         err;
//    ngx_log_t        *log;
//    ngx_socket_t      s;
//    ngx_listening_t  *ls;
//
//    reuseaddr = 1;
//#if (NGX_SUPPRESS_WARN)
//    failed = 0;
//#endif

    $log = $cycle->log;

    /* TODO: configurable try number */

    for ($tries = 5; $tries; $tries--) {
            $failed = 0;

        /* for each listening socket */

        $ls = $cycle->listening;
        for ($i = 0; $i < count($cycle->listening); $i++) {

            if ($ls[$i]->ignore) {
                continue;
            }

            if ($ls[$i]->fd != false) {
                continue;
            }

            if ($ls[$i]->inherited) {

                /* TODO: close on exit */
                /* TODO: nonblocking */
                /* TODO: deferred accept */

                continue;
            }

            $s = ngx_socket($ls[$i]->sockaddr->sa_family, $ls[$i]->type, 0);

            if ($s == false) {
                ngx_log_error(NGX_LOG_EMERG, $log, socket_last_error(),
                    ngx_socket_n ." %V failed", $ls[$i]->addr_text);
                return NGX_ERROR;
            }

            if (socket_set_option($s, SOL_SOCKET, SO_REUSEADDR, 1) == false)
            {
                ngx_log_error(NGX_LOG_EMERG, $log, socket_last_error(),
                    "setsockopt(SO_REUSEADDR) %V failed", $ls[$i]->addr_text);

                if (ngx_close_socket($s) == false) {
                    ngx_log_error(NGX_LOG_EMERG, $log, socket_last_error(),
                        ngx_close_socket_n ." %V failed",
                                  $ls[$i]->addr_text);
                }

                return NGX_ERROR;
            }

            /* TODO: close on exit */

            if (!(ngx_event_flags() & NGX_USE_IOCP_EVENT)) {
                if (ngx_nonblocking($s) == false) {
                    ngx_log_error(NGX_LOG_EMERG, $log, socket_last_error(),
                        ngx_nonblocking_n ." %V failed",
                                  $ls[$i]->addr_text);

                    if (ngx_close_socket($s) == false) {
                        ngx_log_error(NGX_LOG_EMERG, $log, socket_last_error(),
                            ngx_close_socket_n. " %V failed",
                                      $ls[$i]->addr_text);
                    }

                    return NGX_ERROR;
                }
            }

            ngx_log_debug2(NGX_LOG_DEBUG_CORE, $log, 0,
                "bind() %V #%d ", $ls[$i]->addr_text, $s);

            if (socket_bind($s, $ls[$i]->sockaddr->sin_addr, $ls[$i]->sockaddr->sin_port) == false) {
                $err = socket_last_error();

                if ($err != NGX_EADDRINUSE || !ngx_test_config()) {
                    ngx_log_error(NGX_LOG_EMERG, $log, $err,
                        "bind() to %V failed", $ls[$i]->addr_text);
                }

                if (ngx_close_socket($s) == false) {
                    ngx_log_error(NGX_LOG_EMERG, $log, socket_last_error(),
                        ngx_close_socket_n ." %V failed",
                                  $ls[$i]->addr_text);
                }

                if ($err != NGX_EADDRINUSE) {
                    return NGX_ERROR;
                }

                if (!ngx_test_config()) {
                    $failed = 1;
                }

                continue;
            }


            if ($ls[$i]->sockaddr->sa_family == AF_UNIX) {
//                mode_t   mode;
//                u_char  *name;

                //name = ls[i].addr_text.data + sizeof("unix:") - 1;
                $name = substr($ls[$i]->addr_text, strlen("unix:") - 1);
                $mode = (S_IRUSR|S_IWUSR|S_IRGRP|S_IWGRP|S_IROTH|S_IWOTH);

                if (chmod($name, $mode) == false) {
                    ngx_log_error(NGX_LOG_EMERG, $cycle->log, NGX_FCMERROR,
                                  "chmod() \"%s\" failed", $name);
                }

                if (ngx_test_config()) {
                    if (ngx_delete_file($name) == false) {
                        ngx_log_error(NGX_LOG_EMERG, $cycle->log, NGX_FDERROR,
                                      ngx_delete_file_n ." %s failed", $name);
                    }
                }
            }

            if (socket_listen($s, $ls[$i]->backlog) == false) {
                $err = socket_last_error();

                /*
                 * on OpenVZ after suspend/resume EADDRINUSE
                 * may be returned by listen() instead of bind(), see
                 * https://bugzilla.openvz.org/show_bug.cgi?id=2470
                 */

                if ($err != NGX_EADDRINUSE || !ngx_test_config()) {
                    ngx_log_error(NGX_LOG_EMERG, $log, $err,
                        "listen() to %V, backlog %d failed",
                        array($ls[$i]->addr_text, $ls[$i]->backlog));
                }

                if (ngx_close_socket($s) == false) {
                    ngx_log_error(NGX_LOG_EMERG, $log, NGX_FCERROR,
                        ngx_close_socket_n ." %V failed",
                                  $ls[$i]->addr_text);
                }

                if ($err != NGX_EADDRINUSE) {
                    return NGX_ERROR;
                }

                if (!ngx_test_config()) {
                    $failed = 1;
                }

                continue;
            }

            $ls[$i]->listen = 1;

            $ls[$i]->fd = $s;
        }

        if (!$failed) {
            break;
        }

        /* TODO: delay configurable */

        ngx_log_error(NGX_LOG_NOTICE, $log, 0,
            "try again to bind() after 500ms");

        ngx_msleep(500);
    }

    if ($failed) {
        ngx_log_error(NGX_LOG_EMERG, $log, 0, "still could not bind()");
        return NGX_ERROR;
    }

    return NGX_OK;
}


function ngx_configure_listening_sockets(ngx_cycle_t $cycle)
{
//int                        value;
//    ngx_uint_t                 i;
//    ngx_listening_t           *ls;

//#if (NGX_HAVE_DEFERRED_ACCEPT && defined SO_ACCEPTFILTER)
//    struct accept_filter_arg   af;
//#endif

    $ls = $cycle->listening;
    for ($i = 0; $i < count($cycle->listening); $i++) {

        /**todo logp is pointer of log we may have wrong change`**/
        $ls[$i]->log = $ls[$i]->logp;

        if ($ls[$i]->rcvbuf != false) {
        if (socket_set_option($ls[$i]->fd, SOL_SOCKET, SO_RCVBUF,
                           $ls[$i]->rcvbuf) == false)
            {
                ngx_log_error(NGX_LOG_ALERT, $cycle->log, socket_last_error(),
                              "setsockopt(SO_RCVBUF, %d) %V failed, ignored",
                              array($ls[$i]->rcvbuf, $ls[$i]->addr_text));
            }
        }

        if ($ls[$i]->sndbuf != false) {
        if (socket_set_option($ls[$i]->fd, SOL_SOCKET, SO_SNDBUF,
                           $ls[$i]->sndbuf) == false)
            {
                ngx_log_error(NGX_LOG_ALERT, $cycle->log, socket_last_error(),
                              "setsockopt(SO_SNDBUF, %d) %V failed, ignored",
                              array($ls[$i]->sndbuf, $ls[$i]->addr_text));
            }
        }

        if ($ls[$i]->keepalive) {
            $value = ($ls[$i]->keepalive == 1) ? 1 : 0;

            if (socket_set_option($ls[$i]->fd, SOL_SOCKET, SO_KEEPALIVE, $value) == false)
            {
                ngx_log_error(NGX_LOG_ALERT, $cycle->log, socket_last_error(),
                              "setsockopt(SO_KEEPALIVE, %d) %V failed, ignored",
                              array($value, $ls[$i]->addr_text));
            }
        }

//#if (NGX_HAVE_KEEPALIVE_TUNABLE)
//
//        if (ls[i].keepidle) {
//        value = ls[i].keepidle;
//
//#if (NGX_KEEPALIVE_FACTOR)
//            value *= NGX_KEEPALIVE_FACTOR;
//#endif
//
//            if (setsockopt(ls[i].fd, IPPROTO_TCP, TCP_KEEPIDLE,
//                           (const void *) &value, sizeof(int))
//                == -1)
//            {
//                ngx_log_error(NGX_LOG_ALERT, cycle->log, ngx_socket_errno,
//                              "setsockopt(TCP_KEEPIDLE, %d) %V failed, ignored",
//                              value, &ls[i].addr_text);
//            }
//        }
//
//        if (ls[i].keepintvl) {
//        value = ls[i].keepintvl;

//#if (NGX_KEEPALIVE_FACTOR)
//            value *= NGX_KEEPALIVE_FACTOR;
//#endif
//
//            if (setsockopt(ls[i].fd, IPPROTO_TCP, TCP_KEEPINTVL,
//                           (const void *) &value, sizeof(int))
//                == -1)
//            {
//                ngx_log_error(NGX_LOG_ALERT, cycle->log, ngx_socket_errno,
//                             "setsockopt(TCP_KEEPINTVL, %d) %V failed, ignored",
//                             value, &ls[i].addr_text);
//            }
//        }
//
//        if (ls[i].keepcnt) {
//        if (setsockopt(ls[i].fd, IPPROTO_TCP, TCP_KEEPCNT,
//                           (const void *) &ls[i].keepcnt, sizeof(int))
//                == -1)
//            {
//                ngx_log_error(NGX_LOG_ALERT, cycle->log, ngx_socket_errno,
//                              "setsockopt(TCP_KEEPCNT, %d) %V failed, ignored",
//                              ls[i].keepcnt, &ls[i].addr_text);
//            }
//        }
//
//#endif

//#if (NGX_HAVE_SETFIB)
//        if (ls[i].setfib != -1) {
//        if (setsockopt(ls[i].fd, SOL_SOCKET, SO_SETFIB,
//                           (const void *) &ls[i].setfib, sizeof(int))
//                == -1)
//            {
//                ngx_log_error(NGX_LOG_ALERT, cycle->log, ngx_socket_errno,
//                              "setsockopt(SO_SETFIB, %d) %V failed, ignored",
//                              ls[i].setfib, &ls[i].addr_text);
//            }
//        }
//#endif

//#if (NGX_HAVE_TCP_FASTOPEN)
//        if (ls[i].fastopen != -1) {
//        if (setsockopt(ls[i].fd, IPPROTO_TCP, TCP_FASTOPEN,
//                           (const void *) &ls[i].fastopen, sizeof(int))
//                == -1)
//            {
//                ngx_log_error(NGX_LOG_ALERT, cycle->log, ngx_socket_errno,
//                              "setsockopt(TCP_FASTOPEN, %d) %V failed, ignored",
//                              ls[i].fastopen, &ls[i].addr_text);
//            }
//        }
//#endif


        if ($ls[$i]->listen) {

        /* change backlog via listen() */

        if (socket_listen($ls[$i]->fd, $ls[$i]->backlog) == false) {
            ngx_log_error(NGX_LOG_ALERT, $cycle->log, socket_last_error(),
                              "listen() to %V, backlog %d failed, ignored",
                              array($ls[$i]->addr_text, $ls[$i]->backlog));
            }
        }

        /*
         * setting deferred mode should be last operation on socket,
         * because code may prematurely continue cycle on failure
         */

//#if (NGX_HAVE_DEFERRED_ACCEPT)
//
//#ifdef SO_ACCEPTFILTER
//
//        if (ls[i].delete_deferred) {
//        if (setsockopt(ls[i].fd, SOL_SOCKET, SO_ACCEPTFILTER, NULL, 0)
//                == -1)
//            {
//                ngx_log_error(NGX_LOG_ALERT, cycle->log, ngx_socket_errno,
//                              "setsockopt(SO_ACCEPTFILTER, NULL) "
//                              "for %V failed, ignored",
//                              &ls[i].addr_text);
//
//                if (ls[i].accept_filter) {
//                ngx_log_error(NGX_LOG_ALERT, cycle->log, 0,
//                                  "could not change the accept filter "
//                                  "to \"%s\" for %V, ignored",
//                                  ls[i].accept_filter, &ls[i].addr_text);
//                }
//
//                continue;
//            }
//
//            ls[i].deferred_accept = 0;
//        }
//
//        if (ls[i].add_deferred) {
//        ngx_memzero(&af, sizeof(struct accept_filter_arg));
//            (void) ngx_cpystrn((u_char *) af.af_name,
//                               (u_char *) ls[i].accept_filter, 16);
//
//            if (setsockopt(ls[i].fd, SOL_SOCKET, SO_ACCEPTFILTER,
//                           &af, sizeof(struct accept_filter_arg))
//                == -1)
//            {
//                ngx_log_error(NGX_LOG_ALERT, cycle->log, ngx_socket_errno,
//                              "setsockopt(SO_ACCEPTFILTER, \"%s\") "
//                              "for %V failed, ignored",
//                              ls[i].accept_filter, &ls[i].addr_text);
//                continue;
//            }
//
//            ls[i].deferred_accept = 1;
//        }
//
//#endif

//#ifdef TCP_DEFER_ACCEPT
//
//        if (ls[i].add_deferred || ls[i].delete_deferred) {
//
//        if (ls[i].add_deferred) {
//            /*
//             * There is no way to find out how long a connection was
//             * in queue (and a connection may bypass deferred queue at all
//             * if syncookies were used), hence we use 1 second timeout
//             * here.
//             */
//            value = 1;
//
//        } else {
//            value = 0;
//        }
//
//            if (setsockopt(ls[i].fd, IPPROTO_TCP, TCP_DEFER_ACCEPT,
//                           &value, sizeof(int))
//                == -1)
//            {
//                ngx_log_error(NGX_LOG_ALERT, cycle->log, ngx_socket_errno,
//                              "setsockopt(TCP_DEFER_ACCEPT, %d) for %V failed, "
//                              "ignored",
//                              value, &ls[i].addr_text);
//
//                continue;
//            }
//        }
//
//        if (ls[i].add_deferred) {
//        ls[i].deferred_accept = 1;
//        }
//
//#endif

#endif /* NGX_HAVE_DEFERRED_ACCEPT */
    }

    return;
}

function ngx_close_listening_sockets(ngx_cycle_t $cycle)
{
//ngx_uint_t         i;
//    ngx_listening_t   *ls;
//    ngx_connection_t  *c;
    $ngx_event_flags = ngx_event_flags();
    if ($ngx_event_flags & NGX_USE_IOCP_EVENT) {
        return;
    }

    ngx_accept_mutex_held(0);
    ngx_use_accept_mutex(0);

    $ls = $cycle->listening;
    for ($i = 0; $i < count($cycle->listening); $i++) {
        $c = $ls[$i]->connection;
        if ($c) {
            if ($c->read->active) {
                //todo should finish event deal method
                ngx_del_event($c->read, NGX_READ_EVENT, NGX_CLOSE_EVENT);
            }

            ngx_free_connection($c);

            $c->fd = null;
        }

        ngx_log_debug2(NGX_LOG_DEBUG_CORE, $cycle->log, 0,
            "close listening %V #%d ", $ls[$i]->addr_text, $ls[$i]->fd);

        if (ngx_close_socket($ls[$i]->fd) == false) {
            ngx_log_error(NGX_LOG_EMERG, $cycle->log, socket_last_error(),
                ngx_close_socket_n . " %V failed", $ls[$i]->addr_text);
        }


        if ($ls[$i]->sockaddr->sa_family == AF_UNIX &&
            ngx_process() <= NGX_PROCESS_MASTER && ngx_new_binary() == 0
        ) {
            $name = substr($ls[$i]->addr_text, strlen("unix:") - 1);

            if (ngx_delete_file($name) == NGX_FILE_ERROR) {
                ngx_log_error(NGX_LOG_EMERG, $cycle->log, socket_last_error(),
                    ngx_delete_file_n . " %s failed", $name);
            }
        }

        $ls[$i]->fd = null;
    }
    //cycle->listening.nelts = 0;
}

function ngx_free_connection(ngx_connection_t $c)
{
    $ngx_cycle = ngx_cycle();
    $c->data = $ngx_cycle->free_connections;
    $ngx_cycle->free_connections = $c;
    $ngx_cycle->free_connection_n++;

    if ($ngx_cycle->files) {
        $ngx_cycle->files[$c->fd] = NULL;
    }
}

function ngx_get_connection( $s, ngx_log $log)
{
//    ngx_uint_t         instance;
//    ngx_event_t       *rev, *wev;
//    ngx_connection_t  *c;
    $ngx_cycle = ngx_cycle();

    /* disable warning: Win32 SOCKET is u_int while UNIX socket is int */

    if ($ngx_cycle->files && $s >= $ngx_cycle->files_n) {
        ngx_log_error(NGX_LOG_ALERT, $log, 0,
        "the new socket has number %d, ".
                      "but only %ui files are available",
                      array($s, $ngx_cycle->files_n));
        return NULL;
    }

    $c = $ngx_cycle->free_connections;

    if ($c == NULL) {
         ngx_drain_connections();
        $c = $ngx_cycle->free_connections;
    }

    if ($c == NULL) {
        ngx_log_error(NGX_LOG_ALERT, $log, 0,
            "%ui worker_connections are not enough",
            $ngx_cycle->connection_n);

        return NULL;
    }

    $ngx_cycle->free_connections = $c->data;
    $ngx_cycle->free_connection_n--;

    if ($ngx_cycle->files) {
        $ngx_cycle->files[$s] = $c;
    }

    $rev = $c->read;
    $wev = $c->write;

    //ngx_memzero(c, sizeof(ngx_connection_t));
    $c = new ngx_connection_t();

    $c->read = $rev;
    $c->write = $wev;
    $c->fd = $s;
    $c->log = $log;

    $instance = $rev->instance;

//    ngx_memzero(rev, sizeof(ngx_event_t));
//    ngx_memzero(wev, sizeof(ngx_event_t));

    $rev->instance = !$instance;
    $wev->instance = !$instance;

    $rev->index = NGX_INVALID_INDEX;
    $wev->index = NGX_INVALID_INDEX;

    $rev->data = $c;
    $wev->data = $c;

    $wev->write = 1;

    return $c;
}


function ngx_drain_connections()
{
//ngx_int_t          i;
//    ngx_queue_t       *q;
//    ngx_connection_t  *c;
    $ngx_cycle = ngx_cycle();

    for ($i = 0; $i < 32; $i++) {
        if ($ngx_cycle->reusable_connections_queue->isEmpty()) {
            break;
        }

//        $q = ngx_queue_last($ngx_cycle->reusable_connections_queue);
//        $c = ngx_queue_data($q, ngx_connection_t, queue);
        $c = $ngx_cycle->reusable_connections_queue->pop();

        ngx_log_debug0(NGX_LOG_DEBUG_CORE, $c->log, 0,
                       "reusing connection");

        $c->close = 1;
        call_user_func($c->read->handler,$c->read);
    }
}

function ngx_close_connection(ngx_connection_t $c)
{
//ngx_err_t     err;
//    ngx_uint_t    log_error, level;
//    ngx_socket_t  fd;

    if ($c->fd == false) {
        ngx_log_error(NGX_LOG_ALERT, $c->log, 0, "connection already closed");
        return;
    }

    if ($c->read->timer_set) {
        ngx_del_timer($c->read);
    }

    if ($c->write->timer_set) {
        ngx_del_timer($c->write);
    }

    if (is_callable('ngx_del_conn')) {
        ngx_del_conn($c, NGX_CLOSE_EVENT);

    } else {
        if ($c->read->active || $c->read->disabled) {
            ngx_del_event($c->read, NGX_READ_EVENT, NGX_CLOSE_EVENT);
        }

        if ($c->write->active || $c->write->disabled) {
            ngx_del_event($c->write, NGX_WRITE_EVENT, NGX_CLOSE_EVENT);
        }
    }

    if ($c->read->posted) {
        ngx_delete_posted_event($c->read);
    }

    if ($c->write->posted) {
        ngx_delete_posted_event($c->write);
    }

    $c->read->closed = 1;
    $c->write->closed = 1;

    ngx_reusable_connection($c, 0);

    $log_error = $c->log_error;

    ngx_free_connection($c);

    $fd = $c->fd;
    $c->fd = false;

    if (ngx_close_socket($fd) == false) {

        $err = socket_last_error();

        if ($err == NGX_ECONNRESET || $err == NGX_ENOTCONN) {

            switch ($log_error) {

                case NGX_ERROR_INFO:
                    $level = NGX_LOG_INFO;
                    break;

                case NGX_ERROR_ERR:
                    $level = NGX_LOG_ERR;
                    break;

                default:
                    $level = NGX_LOG_CRIT;
            }

        } else {
            $level = NGX_LOG_CRIT;
        }

        /* we use ngx_cycle->log because c->log was in c->pool */

        $ngx_cycle = ngx_cycle();
        ngx_log_error($level, $ngx_cycle->log, $err,
                      ngx_close_socket_n ." %d failed", $fd);
    }
}

