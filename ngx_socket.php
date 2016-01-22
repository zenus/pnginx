<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 15-11-25
 * Time: 下午10:07
 * @param $domain
 * @param $type
 * @param $protocol
 * @return resource
 */
define('ngx_socket_n', "socket()");
define('ngx_nonblocking_n',"fcntl(O_NONBLOCK)");
define('ngx_blocking_n',"fcntl(!O_NONBLOCK)");
define('ngx_close_socket_n',  "close() socket");
function ngx_socket($domain,$type,$protocol){
    return socket_create( $domain , $type , $protocol );
}

function ngx_nonblocking($s)
{
    //todo should check up in php source code
    return socket_set_nonblock($s);
}

function ngx_blocking($s){
   return socket_set_block($s);
}

function ngx_connect($fd, $addr, $port)
{
    //todo should check up in php source code
    return socket_connect($fd,$addr,$port);
}

function ngx_close_socket($fd)
{
     socket_close($fd);
}