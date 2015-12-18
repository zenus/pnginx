<?php

/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 15-12-18
 * Time: 下午10:30
 */

class ngx_channel_t {
/**    ngx_uint_t **/ private $command;
/**     ngx_pid_t **/ private  $pid;
/**     ngx_int_t **/ private  $slot;
/**     ngx_fd_t ***/ private  $fd;
    public function __set($name,$value){
       $this->$name = $value;
    }
    public function __get($name){
       return $this->$name;
    }
}

function ngx_close_channel( $fd, ngx_log $log)
{
    if (fclose($fd[0]) == false) {
        ngx_log_error(NGX_LOG_ALERT, $log, socket_last_error(), "close() channel failed");
    }

    if (fclose($fd[1]) == false) {
        ngx_log_error(NGX_LOG_ALERT, $log, socket_last_error(), "close() channel failed");
      }
}



