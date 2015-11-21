<?php
/**
 * Created by PhpStorm.
 * User: zenus@github.com
 * Date: 2015/11/4
 * Time: 12:22
 */
define('NGX_OK', 0);
define('NGX_ERROR',-1);
define('NGX_AGAIN',-2);
define('NGX_BUSY ',-3);
define('NGX_DONE ',-4);
define('NGX_DECLINED',-5);
define('NGX_ABORT',-6);

define('LF','\n');
define('CR','\r');
define('CRLF',"\r\n");

class ngx_open_file_s {

    private $fd = null;
    private $name = '';
    private $flush_handler;
    private $data;

    public function flush(ngx_open_file_s $file, ngx_log_s $log){

        return call_user_func($this->flush_handler,$file,$log);

    }

    public function __set($property_name, $value){

        if($property_name == 'flush_handler' && $value instanceof closure){
            $this->flush_handler = $value;
        }else{

            $this->$property_name = $value;
        }
    }

    public function __get($property_name){

        return $this->$property_name;
    }

//    public function set_fd($fd){
//       $this->fd = $fd;
//    }
//    public function get_fd(){
//       return $this->fd;
//    }
//    public function set_name($name){
//       $this->name = $name;
//    }
//    public function get_name(){
//       return $this->name;
//    }
}

function ngx_cfg($ngx_cfg,$value = null){
    static $cfg = array();
    return  is_null($value) ? (isset($cfg[$ngx_cfg]) ? $cfg[$ngx_cfg] : false) : $cfg[$ngx_cfg] = $value;
}

function ngx_abs($number){
    return abs($number);
}
