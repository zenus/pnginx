<?php

include_once 'ngx_files.php';
include_once 'ngx_core.php';


define('NGX_LOG_STDERR',0);
define('NGX_LOG_EMERG',  1);
define('NGX_LOG_ALERT',  2);
define('NGX_LOG_CRIT',   3);
define('NGX_LOG_ERR',    4);
define('NGX_LOG_WARN',   5);
define('NGX_LOG_NOTICE', 6);
define('NGX_LOG_INFO',   7);
define('NGX_LOG_DEBUG',  8);
define('NGX_LOG_DEBUG_CORE',   0x010);
define('NGX_LOG_DEBUG_ALLOC',  0x020);
define('NGX_LOG_DEBUG_MUTEX',  0x040);
define('NGX_LOG_DEBUG_EVENT',  0x080);
define('NGX_LOG_DEBUG_HTTP',   0x100);
define('NGX_LOG_DEBUG_MAIL',   0x200);
define('NGX_LOG_DEBUG_MYSQL',  0x400);
define('NGX_LOG_DEBUG_STREAM', 0x800);

class ngx_log_s {

    private  /**ngx_open_file_s*/ $file = null;
    private $log_level = null;
    private $connection = null;
    private $disk_full_time;
    private /**ngx_log_handler_pt**/ $handler;
    private $data;
    private /**ngx_log_writer_pt*/ $writer;
    private $wdata;
    private $action;

    public function set_file(ngx_open_file_s $file){
        if($file instanceof ngx_open_file_s){
            $this->file = $file;
        }
    }

    public function set_level($level){
        $this->log_level = $level;
    }


}

function ngx_log_stderr($no, $fmt, $var = array()){
    $line = "pnginx[%d]:".$fmt;
    array_unshift($var,$no);
    $log = vsprintf($line,$var);
    ngx_linefeed($log);
    ngx_write_console(ngx_stderr, $log);
}

function ngx_write_console($fd,$s){

    return ngx_write_fd($fd,$s);

}

function ngx_write_fd($fd,$s){

    return fwrite($fd, $s);

}

function ngx_write_stderr($s)
{
  ngx_write_fd(ngx_stderr, $s);
}

function ngx_log_init(){
    $ngx_log_file = new ngx_open_file_s();
    $ngx_log = new ngx_log_s();
    $ngx_log->set_file($ngx_log_file);
    $ngx_log->set_level(NGX_LOG_NOTICE);
    $fd = ngx_open_file(NGX_ERROR_LOG_PATH, NGX_FILE_APPEND, NGX_FILE_DEFAULT_ACCESS);
    if($fd == NGX_INVALID_FILE){
        ngx_log_stderr(ngx_errno,
            "[alert] could not open error log file: ".
                       ngx_open_file_n." \"%s\" failed", NGX_ERROR_LOG_PATH);
    }
    $ngx_log_file->set_fd($fd);


}

