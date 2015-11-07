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

};