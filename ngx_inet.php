<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 15-11-21
 * Time: 下午10:56
 */


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
    return ngx_parse_unix_domain_url(pool, u);
}

    if ($p[0] == '[') {
    return ngx_parse_inet6_url(pool, u);
}

    return ngx_parse_inet_url(pool, u);
}