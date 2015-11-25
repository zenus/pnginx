<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 15-11-21
 * Time: 下午10:56
 */

define('INADDR_ANY',0x00000000);
/* Address to send to all hosts.  */
define('INADDR_BROADCAST',0xffffffff);
/* Address indicating an error return.  */
define('INADDR_NONE',0xffffffff);


class ngx_url_t {
/** ngx_str_t **/ private $url;
/** ngx_str_t **/ private $host;
/** ngx_str_t **/ private $port_text;
/** ngx_str_t **/ private $uri;
/** in_port_t **/ private $port;
/** in_port_t **/ private $default_port;
/** int **/ private $family;
/** unsigned **/ private $listen;
/** unsigned **/ private $uri_part;
/** unsigned **/ private $no_resolve;
/** unsigned **/ private $one_addr;  /* compatibility */
/** unsigned **/ private $no_port;
/** unsigned **/ private $wildcard;
/** socklen_t **/ private $socklen;
/**    u_char **/ private   $sockaddr;
/** ngx_addr_t **/ private $addrs;
/** ngx_uint_t **/ private $naddrs;
/** char **/ private $err;
    public function __set($property, $value){
       $this->$property = $value ;
    }
    public function __get($property){
       return $this->$property;
    }
}

function ngx_parse_url( ngx_url_t $u)
{

    $p = $u->url;

    if (ngx_strncasecmp($p, "unix:", 5) == 0) {
    return ngx_parse_unix_domain_url($u);
}

    //todo we temporarily don't realize this
//    if ($p[0] == '[') {
//    return ngx_parse_inet6_url($u);
//}

    return ngx_parse_inet_url($u);
}

function ngx_parse_inet_url( ngx_url_t $u)
{

    $sin = $u->sockaddr;
    $sin->sin_family = AF_INET;

    $u->family = AF_INET;

    $host = $u->url;

    $last = strlen($host);

    $port = ngx_strlchr($host, $last, ':');

    $uri = ngx_strlchr($host, $last, '/');

    $args = ngx_strlchr($host, $last, '?');

    if ($args) {
        if ($uri == NULL || $args < $uri) {
            $uri = $args;
        }
    }

    if ($uri) {
        if ($u->listen || !$u->uri_part) {
            $u->err = "invalid host";
            return NGX_ERROR;
        }

        $u->uri = substr($host,$uri);

        $last = $uri;

        if ($uri < $port) {
            $port = NULL;
        }
    }

    if ($port) {

        $len = $last - ($port+1);

        $port_data = substr($host,$port);
        $n = ngx_atoi($port_data);

        if ($n < 1 || $n > 65535) {
            $u->err = "invalid port";
            return NGX_ERROR;
        }

        $u->port = $n;
        $sin->sin_port = htons($n);

        $u->port_text = $port_data;

        $last = $port ;

    } else {
        if ($uri == NULL) {

            if ($u->listen) {

                /* test value as port only */

                $n = ngx_atoi(substr($host,0,$last));

                if ($n != NGX_ERROR) {

                    if ($n < 1 || $n > 65535) {
                        $u->err = "invalid port";
                        return NGX_ERROR;
                    }

                    $u->port = $n;
                    $sin->sin_port = htons($n);

                    $u->port_text = $host;

                    $u->wildcard = 1;

                    return NGX_OK;
                }
            }
        }

        $u->no_port = 1;
        $u->port = $u->default_port;
        $sin->sin_port = htons($u->default_port);
    }

    $len = $last;

    if ($len == 0) {
        $u->err = "no host";
        return NGX_ERROR;
    }

    $u->host =substr($host,0,$last);

    if ($u->listen && $len == 1 && $host == '*') {
        $sin->sin_addr = INADDR_ANY;
        $u->wildcard = 1;
        return NGX_OK;
    }

    $sin->sin_addr = ngx_inet_addr($u->host);

    if ($sin->sin_addr != INADDR_NONE) {

    if ($sin->sin_addr == INADDR_ANY) {
        $u->wildcard = 1;
        }
        $u->naddrs = 1;

        $addr = new ngx_addr_t();
        $sin = new sockaddr_in();
        $sin = $u->sockaddr;
        $addr->sockaddr = $sin;
        $addr->name = ngx_sprintf('', "%V:%d", array($u->host, $u->port));


        $u->addrs[] = $addr;



        return NGX_OK;
    }

    if ($u->no_resolve) {
           return NGX_OK;
       }

    if (ngx_inet_resolve_host($u) != NGX_OK) {
        return NGX_ERROR;
    }

    $u->family = $u->addrs[0]->sockaddr->sa_family;
    $u->sockaddr = $u->addrs[0]->sockaddr;

    switch ($u->family) {

        default: /* AF_INET */
            $sin = $u->sockaddr;

            if ($sin->sin_addr == INADDR_ANY) {
                $u->wildcard = 1;
            }
            break;
    }
    return NGX_OK;
}

function ngx_parse_unix_domain_url( ngx_url_t $u)
{

    $len = strlen($u->url);
    $path = $u->url;

    $path = substr($path,5);
    $len -= 5;

    if ($u->uri_part) {
        $last = strlen($path);
        $uri = ngx_strlchr($path, $last,':');

        if ($uri) {
            $len = $uri+1;
            $u->uri->len = $last - ($uri+1);
            $u->uri = substr($path,$uri);
            }
      }

    if ($len == 0) {
        $u->err = "no path in the unix domain socket";
        return NGX_ERROR;
    }

    $u->host = $path;


    //$u->socklen = sizeof(struct sockaddr_un);
//    $saun = $u->sockaddr;
    $saun = new sockaddr_un();
    $saun->sun_family = AF_UNIX;
    $saun->sun_path = $path;

    $addr = new ngx_addr_t();
    $addr->sockaddr = $saun;
    $addr->name = $u->url;
    $u->addrs[0] = $addr;
//    u->addrs = ngx_pcalloc(pool, sizeof(ngx_addr_t));
//    if (u->addrs == NULL) {
//    return NGX_ERROR;
//}

//    saun = ngx_pcalloc(pool, sizeof(struct sockaddr_un));
//    if (saun == NULL) {
//        return NGX_ERROR;
//    }

    $u->family = AF_UNIX;
    $u->naddrs = 1;

//    saun->sun_family = AF_UNIX;
//    (void) ngx_cpystrn((u_char *) saun->sun_path, path, len);

//    $u->addrs[0].sockaddr = (struct sockaddr *) saun;
//    $u->addrs[0].socklen = sizeof(struct sockaddr_un);
//    $u->addrs[0].name.len = len + 4;
//    $u->addrs[0].name.data = u->url.data;

    return NGX_OK;
}

function htons($n){
  return pack('n', $n);
}

function ntohs($n){
    return unpack('v', $n);
}

class sockaddr_un
  {
    private $sun_family;
    private $sun_path;

    public function __set($property, $value){
        $this->$property = $value;
    }
    public function __get($property){
       return $this->$property;
    }
  }

class ngx_addr_t {
    /***struct sockaddr**/  private         $sockaddr;
//    /**socklen_t**/         private        $socklen;
    /**ngx_str_t **/        private        $name;
    /**todo self create**/
    /**ngx_str_t **/        private        $family;
    /**ngx_str_t **/        private        $port;
    public function __set($property, $value){
       $this->$property = $value;
    }
    public function __get($property){
       return $this->$property;
    }
}

function ngx_inet_addr($text)
{
   return inet_pton($text);
}

class sockaddr_in
  {
    //  __SOCKADDR_COMMON (sin_);
    /**  in_port_t **/  private  $sin_port;			/* Port number.  */
    /** struct in_addr **/ private $sin_addr;		/* Internet address.  */
    /**  sin_family* */  private $sin_family;

    /* Pad to size of `struct sockaddr'.  */
//    unsigned char sin_zero[sizeof (struct sockaddr) -
//  __SOCKADDR_COMMON_SIZE -
//  sizeof (in_port_t) -
//  sizeof (struct in_addr)];
   public function __set($property,$value){
       $this->$property = $value;
   }

    public function __get($property){
       return $this->$property;
    }
  }

function ngx_inet_resolve_host( ngx_url_t $u)
{


    $port = htons($u->port);

    $in_addr = ngx_inet_addr($u->host);

    if ($in_addr == INADDR_NONE) {

        $host = $u->host;

        $h = gethostbynamel($host);

//        for (i = 0; h->h_addr_list[i] != NULL; i++) { /* void */ }
//
//        /* MP: ngx_shared_palloc() */
//
//        u->addrs = ngx_pcalloc(pool, i * sizeof(ngx_addr_t));
//        if (u->addrs == NULL) {
//            return NGX_ERROR;
//        }

        $addr = new  ngx_addr_t();

        $i = count($h);
        $u->naddrs = $i;

        for ($i = 0; $i < $u->naddrs; $i++) {

//            sin = ngx_pcalloc(pool, sizeof(struct sockaddr_in));
//            if (sin == NULL) {
//                return NGX_ERROR;
//            }

            $sin = new sockaddr_in();

            $sin->sin_family = AF_INET;
            $sin->sin_port = $port;
            $sin->sin_addr = $h[$i];

            $addr->sockaddr = $sin;

//            $u->addrs[i].sockaddr = (struct sockaddr *) sin;
//
//            len = NGX_INET_ADDRSTRLEN + sizeof(":65535") - 1;
//
//            p = ngx_pnalloc(pool, len);
//            if (p == NULL) {
//                return NGX_ERROR;
//            }

            $p = '';
            //todo why the port is NO 1
            $len = ngx_sock_ntop($sin,
                                $p,  1);

            $addr->name = $p;
            //u->addrs[i].name.len = len;
            $u->addrs[$i] = $addr;
        }

    } else {

        /* MP: ngx_shared_palloc() */

        $addr = new ngx_addr_t();
//        u->addrs = ngx_pcalloc(pool, sizeof(ngx_addr_t));
//        if (u->addrs == NULL) {
//            return NGX_ERROR;
//        }

        $sin = new sockaddr_in();
//        sin = ngx_pcalloc(pool, sizeof(struct sockaddr_in));
//        if (sin == NULL) {
//            return NGX_ERROR;
//        }

        $u->naddrs = 1;

        $sin->sin_family = AF_INET;
        $sin->sin_port = $port;
        $sin->sin_addr = $in_addr;

        $addr->sockaddr = $sin;
        $addr->name = ngx_sprintf('', "%V:%d", array($u->host, ntohs($port)));
    }

    return NGX_OK;
}


function ngx_sock_ntop($sa,  $text,  $port)
{
//    u_char               *p;
//    struct sockaddr_in   *sin;
//#if (NGX_HAVE_INET6)
//    size_t                n;
//    struct sockaddr_in6  *sin6;
//#endif
//#if (NGX_HAVE_UNIX_DOMAIN)
//    struct sockaddr_un   *saun;
//#endif

    switch ($sa->sa_family) {

case AF_INET:

        $sin = $sa;
        $p = $sin->sin_addr;

        if ($port) {
            $p = ngx_snprintf($text,"%ud.%ud.%ud.%ud:%d",
                array($p[0], $p[1], $p[2], $p[3], ntohs($sin->sin_port)));
        } else {
            $p = ngx_snprintf($text, "%ud.%ud.%ud.%ud",
                array($p[0], $p[1], $p[2], $p[3]));
        }

        return $p;



    case AF_UNIX:
        $saun = $sa;
        /* on Linux sockaddr might not include sun_path at all */

//        if ($socklen <= offsetof(struct sockaddr_un, sun_path)) {
//            $p = ngx_snprintf($text,  "unix:%Z");
//
//        } else {
            $p = ngx_snprintf($text,  "unix:%s%Z", $saun->sun_path);
//        }

        /* we do not include trailing zero in address length */

        return $p;

    default:
        return 0;
    }
}
