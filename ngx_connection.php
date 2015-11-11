<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 15-11-7
 * Time: 下午7:16
 */
class ngx_connection_s {
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
#if (NGX_HAVE_REUSEPORT)
/**  unsigned  **/   private  $reuseport;
/**  unsigned  **/   private  $add_reuseport;
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
#if (NGX_HAVE_SETFIB)
/**  int  **/   private  $setfib;
#endif

#if (NGX_HAVE_TCP_FASTOPEN)
/**  int  **/   private  $fastopen;
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
function ngx_set_inherited_sockets(ngx_cycle_s &$cycle)
{

    //todo should know how to use this in php
//size_t                     len;
//    ngx_uint_t                 i;
//    ngx_listening_t           *ls;
//    socklen_t                  olen;
//#if (NGX_HAVE_DEFERRED_ACCEPT || NGX_HAVE_TCP_FASTOPEN)
//    ngx_err_t                  err;
//#endif
//#if (NGX_HAVE_DEFERRED_ACCEPT && defined SO_ACCEPTFILTER)
//    struct accept_filter_arg   af;
//#endif
//#if (NGX_HAVE_DEFERRED_ACCEPT && defined TCP_DEFER_ACCEPT)
//    int                        timeout;
//#endif
//#if (NGX_HAVE_REUSEPORT)
//    int                        reuseport;
//#endif

    //ls = cycle->listening.elts;
    for ($i = 0; $i < count($cycle->listening); $i++) {

        $ls = $cycle->listening;


        if (socket_getsockname($ls[$i]->fd, $ls[$i]->sockaddr, $ls[$i]->port) == -1) {
        ngx_log_error(NGX_LOG_CRIT, $cycle->log, socket_last_error(),
                          "getsockname() of the inherited ".
                          "socket #%d failed", $ls[$i]->fd);
            $ls[$i]->ignore = 1;
            continue;
        }

//        switch (ls[i].sockaddr->sa_family) {
//
//#if (NGX_HAVE_INET6)
//    case AF_INET6:
//        ls[i].addr_text_max_len = NGX_INET6_ADDRSTRLEN;
//             len = NGX_INET6_ADDRSTRLEN + sizeof("[]:65535") - 1;
//             break;
//#endif
//
//#if (NGX_HAVE_UNIX_DOMAIN)
//        case AF_UNIX:
//             ls[i].addr_text_max_len = NGX_UNIX_ADDRSTRLEN;
//             len = NGX_UNIX_ADDRSTRLEN;
//             break;
//#endif
//
//        case AF_INET:
//             ls[i].addr_text_max_len = NGX_INET_ADDRSTRLEN;
//             len = NGX_INET_ADDRSTRLEN + sizeof(":65535") - 1;
//             break;
//
//        default:
//            ngx_log_error(NGX_LOG_CRIT, cycle->log, ngx_socket_errno,
//                          "the inherited socket #%d has "
//                          "an unsupported protocol family", ls[i].fd);
//            ls[i].ignore = 1;
//            continue;
//        }
//
//        ls[i].addr_text.data = ngx_pnalloc(cycle->pool, len);
//        if (ls[i].addr_text.data == NULL) {
//        return NGX_ERROR;
//    }
//
//        len = ngx_sock_ntop(ls[i].sockaddr, ls[i].socklen,
//                            ls[i].addr_text.data, len, 1);
//        if (len == 0) {
//            return NGX_ERROR;
//        }
//
//        ls[i].addr_text.len = len;
//
//        ls[i].backlog = NGX_LISTEN_BACKLOG;
//
//        olen = sizeof(int);
//
//        if (getsockopt(ls[i].fd, SOL_SOCKET, SO_RCVBUF, (void *) &ls[i].rcvbuf,
//                       &olen)
//            == -1)
//        {
//            ngx_log_error(NGX_LOG_ALERT, cycle->log, ngx_socket_errno,
//                          "getsockopt(SO_RCVBUF) %V failed, ignored",
//                          &ls[i].addr_text);
//
//            ls[i].rcvbuf = -1;
//        }
//
//        olen = sizeof(int);
//
//        if (getsockopt(ls[i].fd, SOL_SOCKET, SO_SNDBUF, (void *) &ls[i].sndbuf,
//                       &olen)
//            == -1)
//        {
//            ngx_log_error(NGX_LOG_ALERT, cycle->log, ngx_socket_errno,
//                          "getsockopt(SO_SNDBUF) %V failed, ignored",
//                          &ls[i].addr_text);
//
//            ls[i].sndbuf = -1;
//        }
//
//#if 0
//        /* SO_SETFIB is currently a set only option */
//
//#if (NGX_HAVE_SETFIB)
//
//        olen = sizeof(int);
//
//        if (getsockopt(ls[i].fd, SOL_SOCKET, SO_SETFIB,
//                       (void *) &ls[i].setfib, &olen)
//            == -1)
//        {
//            ngx_log_error(NGX_LOG_ALERT, cycle->log, ngx_socket_errno,
//                          "getsockopt(SO_SETFIB) %V failed, ignored",
//                          &ls[i].addr_text);
//
//            ls[i].setfib = -1;
//        }
//
//#endif
//#endif
//
//#if (NGX_HAVE_REUSEPORT)
//
//        reuseport = 0;
//        olen = sizeof(int);
//
//        if (getsockopt(ls[i].fd, SOL_SOCKET, SO_REUSEPORT,
//                       (void *) &reuseport, &olen)
//            == -1)
//        {
//            ngx_log_error(NGX_LOG_ALERT, cycle->log, ngx_socket_errno,
//                          "getsockopt(SO_REUSEPORT) %V failed, ignored",
//                          &ls[i].addr_text);
//
//        } else {
//        ls[i].reuseport = reuseport ? 1 : 0;
//        }
//
//#endif
//
//#if (NGX_HAVE_TCP_FASTOPEN)
//
//        olen = sizeof(int);
//
//        if (getsockopt(ls[i].fd, IPPROTO_TCP, TCP_FASTOPEN,
//                       (void *) &ls[i].fastopen, &olen)
//            == -1)
//        {
//            err = ngx_socket_errno;
//
//            if (err != NGX_EOPNOTSUPP && err != NGX_ENOPROTOOPT) {
//                ngx_log_error(NGX_LOG_NOTICE, cycle->log, err,
//                              "getsockopt(TCP_FASTOPEN) %V failed, ignored",
//                              &ls[i].addr_text);
//            }
//
//            ls[i].fastopen = -1;
//        }
//
//#endif
//
//#if (NGX_HAVE_DEFERRED_ACCEPT && defined SO_ACCEPTFILTER)
//
//        ngx_memzero(&af, sizeof(struct accept_filter_arg));
//        olen = sizeof(struct accept_filter_arg);
//
//        if (getsockopt(ls[i].fd, SOL_SOCKET, SO_ACCEPTFILTER, &af, &olen)
//            == -1)
//        {
//            err = ngx_socket_errno;
//
//            if (err == NGX_EINVAL) {
//                continue;
//            }
//
//            ngx_log_error(NGX_LOG_NOTICE, cycle->log, err,
//                          "getsockopt(SO_ACCEPTFILTER) for %V failed, ignored",
//                          &ls[i].addr_text);
//            continue;
//        }
//
//        if (olen < sizeof(struct accept_filter_arg) || af.af_name[0] == '\0') {
//        continue;
//    }
//
//        ls[i].accept_filter = ngx_palloc(cycle->pool, 16);
//        if (ls[i].accept_filter == NULL) {
//        return NGX_ERROR;
//    }
//
//        (void) ngx_cpystrn((u_char *) ls[i].accept_filter,
//                           (u_char *) af.af_name, 16);
//#endif
//
//#if (NGX_HAVE_DEFERRED_ACCEPT && defined TCP_DEFER_ACCEPT)
//
//        timeout = 0;
//        olen = sizeof(int);
//
//        if (getsockopt(ls[i].fd, IPPROTO_TCP, TCP_DEFER_ACCEPT, &timeout, &olen)
//            == -1)
//        {
//            err = ngx_socket_errno;
//
//            if (err == NGX_EOPNOTSUPP) {
//                continue;
//            }
//
//            ngx_log_error(NGX_LOG_NOTICE, cycle->log, err,
//                          "getsockopt(TCP_DEFER_ACCEPT) for %V failed, ignored",
//                          &ls[i].addr_text);
//            continue;
//        }
//
//        if (olen < sizeof(int) || timeout == 0) {
//        continue;
//    }
//
//        ls[i].deferred_accept = 1;
//#endif
//    }
//
//    return NGX_OK;
}