<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 15-11-26
 * Time: 下午10:51
 */

class ngx_os_io_t {
/** ngx_recv_pt **/ private $recv; 
/** ngx_recv_chain_pt **/ private $recv_chain; 
/** ngx_recv_pt **/ private $udp_recv; 
/** ngx_send_pt **/ private $send; 
/** ngx_send_chain_pt **/ private $send_chain; 
/** ngx_uint_t **/ private $flags;
    public function __set($property,$value){
       $this->$property = $value;
    }
    public function __get($property){
       return $this->$property;
    }
}