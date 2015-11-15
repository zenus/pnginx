<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 15-11-15
 * Time: 下午8:19
 */
class ngx_file_t {
/** ngx_fd_t **/ private $fd;
/** ngx_str_t **/ private $name;
/** ngx_file_info_t **/ private $info;
/** off_t **/ private $offset;
/** off_t **/ private $sys_offset;
/** ngx_log_t **/ private $log;


#if (NGX_HAVE_FILE_AIO)
/** ngx_event_aio_t **/ private $aio;
#endif
/** unsigned **/ private $valid_info;
/** unsigned **/ private $directio;

    public function __set($property,$value){
       $this->$property = $value;
    }
    public function __get($property){
       return $this->$property;
    }
};