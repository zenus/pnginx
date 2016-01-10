<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 15-11-19
 * Time: 下午11:19
 */
define('NGX_CONFIGURE',"");






define('NGX_HAVE_GCC_ATOMIC',1);




define('NGX_HAVE_C99_VARIADIC_MACROS',1);




define('NGX_HAVE_GCC_VARIADIC_MACROS',1);




define('NGX_HAVE_EPOLL',1);




define('NGX_HAVE_CLEAR_EVENT',1);




define('NGX_HAVE_EPOLLRDHUP',1);




define('NGX_HAVE_O_PATH',1);




define('NGX_HAVE_SENDFILE',1);




define('NGX_HAVE_SENDFILE64',1);




define('NGX_HAVE_PR_SET_DUMPABLE',1);




define('NGX_HAVE_SCHED_SETAFFINITY',1);




define('NGX_HAVE_GNU_CRYPT_R',1);




define('NGX_HAVE_NONALIGNED',1);




define('NGX_CPU_CACHE_LINE',64);



#define NGX_KQUEUE_UDATA_T  (void *)



define('NGX_HAVE_POSIX_FADVISE',1);




define('NGX_HAVE_O_DIRECT',1);




define('NGX_HAVE_ALIGNED_DIRECTIO',1);




define('NGX_HAVE_STATFS',1);




define('NGX_HAVE_STATVFS',1);




define('NGX_HAVE_SCHED_YIELD',1);




define('NGX_HAVE_DEFERRED_ACCEPT',1);




define('NGX_HAVE_KEEPALIVE_TUNABLE',1);




define('NGX_HAVE_TCP_FASTOPEN',1);




define('NGX_HAVE_TCP_INFO',1);




define('NGX_HAVE_ACCEPT4',1);




define('NGX_HAVE_UNIX_DOMAIN',1);




define('NGX_PTR_SIZE',8);




define('NGX_SIG_ATOMIC_T_SIZE',4);




define('NGX_HAVE_LITTLE_ENDIAN',1);






define('NGX_MAX_SIZE_T_VALUE', PHP_INT_MAX);
define('NGX_MAX_OFF_T_VALUE', PHP_INT_MAX);


#define NGX_SIZE_T_LEN  (sizeof("-9223372036854775808") - 1)








#define NGX_OFF_T_LEN  (sizeof("-9223372036854775808") - 1)




define('NGX_TIME_T_SIZE',8);




#define NGX_TIME_T_LEN  (sizeof("-9223372036854775808") - 1)








define('NGX_HAVE_PREAD',1);




define('NGX_HAVE_PWRITE',1);








define('NGX_HAVE_LOCALTIME_R',1);




define('NGX_HAVE_POSIX_MEMALIGN',1);




define('NGX_HAVE_MEMALIGN',1);




define('NGX_HAVE_MAP_ANON',1);




define('NGX_HAVE_MAP_DEVZERO',1);




define('NGX_HAVE_SYSVSHM',1);




define('NGX_HAVE_POSIX_SEM',1);




define('NGX_HAVE_MSGHDR_MSG_CONTROL',1);




define('NGX_HAVE_FIONBIO',1);




define('NGX_HAVE_GMTOFF',1);




define('NGX_HAVE_D_TYPE',1);




define('NGX_HAVE_SC_NPROCESSORS_ONLN',1);




define('NGX_HAVE_OPENAT',1);




define('NGX_HAVE_GETADDRINFO',1);




define('NGX_HTTP_CACHE',1);




define('NGX_HTTP_GZIP',1);




define('NGX_HTTP_SSI',1);




define('NGX_CRYPT',1);




define('NGX_HTTP_X_FORWARDED_FOR',1);








define('NGX_PCRE',1);




define('NGX_HAVE_PCRE_JIT',1);




define('NGX_ZLIB',1);




define('NGX_PREFIX',"/usr/local/nginx/");




define('NGX_CONF_PREFIX',"conf/");




define('NGX_SBIN_PATH',"");








define('NGX_PID_PATH',"logs/nginx.pid");




define('NGX_LOCK_PATH',"logs/nginx.lock");








define('NGX_HTTP_LOG_PATH',"logs/access.log");




define('NGX_HTTP_CLIENT_TEMP_PATH',"client_body_temp");




define('NGX_HTTP_PROXY_TEMP_PATH',"proxy_temp");




define('NGX_HTTP_FASTCGI_TEMP_PATH',"fastcgi_temp");




define('NGX_HTTP_UWSGI_TEMP_PATH',"uwsgi_temp");




define('NGX_HTTP_SCGI_TEMP_PATH',"scgi_temp");




define('NGX_SUPPRESS_WARN',1);




define('NGX_SMP',1);




define('NGX_USER',  "nobody");




define('NGX_GROUP',  "nogroup");
