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


define('NGX_LOG_DEBUG_FIRST',       NGX_LOG_DEBUG_CORE);
define('NGX_LOG_DEBUG_LAST',       NGX_LOG_DEBUG_STREAM);
define('NGX_LOG_DEBUG_CONNECTION',  0x80000000);
define('NGX_LOG_DEBUG_ALL',        0x7ffffff0);

//ngx_uint_t              ngx_use_stderr = 1;

// $err_levels = array(
//    '',
//    "emerg",
//    "alert",
//    "crit",
//    "error",
//    "warn",
//    "notice",
//    "info",
//    "debug"
//);

class ngx_log extends SplDoublyLinkedList{

    private $ngx_log_s;

    public  function __construct()
    {
        $this->ngx_log_s = new ngx_log_s();
    }

    public function __set($property_name, $value){

        if($property_name == 'file'){
            if($value instanceof ngx_open_file_s){
                $this->ngx_log_s->file = $value ;
            }else{
                die('ngx_log need right file type');
            }
        }elseif($property_name == 'hander'){

            if($value instanceof Closure){
                $this->ngx_log_s->handler = $value ;
            }else{
                die('ngx_log need right handler type');
            }

        }elseif($property_name == 'writer'){

            if($value instanceof Closure){
                $this->ngx_log_s->writer = $value ;
            }else{
                die('ngx_log need right writer type');
            }
        }else{
            $this->ngx_log_s->$property_name = $value;
        }
    }

    public function __get($property_name){

        return $this->ngx_log_s->$property_name;
    }

//    public function set_file(ngx_open_file_s &$file){
//
//       $this->ngx_log_s->set_file($file);
//
//    }
//    public function get_file(){
//
//        return $this->ngx_log_s->get_file();
//    }
//
//    public function set_level($level){
//        $this->ngx_log_s->set_level($level);
//    }
//
//    public function set_log_handler(Closure $callback){
//
//        $this->ngx_log_s->set_log_handler($callback);
//    }
//
//    public function set_log_writer(Closure $callback){
//
//        $this->ngx_log_s->set_log_writer($callback);
//    }
//
//    public function get_log_writer(){
//
////        return $this->writer;
//        return $this->ngx_log_s->get_log_writer();
//    }
//
//    public function get_log_handler(){
//
//        return  $this->ngx_log_s->get_log_handler();
//    }

    public function handle($log, $s){

        return $this->ngx_log_s->handle($log,$s);
    }

    public function write($log, $level, $s){

        return $this->ngx_log_s->write($log, $level, $s);

    }
    public function _next(){
       $this->next();
        $this->ngx_log_s = $this->current();
    }
}
class ngx_log_s {

    private  /**ngx_open_file_s*/ $file = null;
    private $log_level = null;
    private $connection = null;
  //  private $disk_full_time;
    private /**ngx_log_handler_pt**/ $handler;
    private $data;
    private /**ngx_log_writer_pt*/ $writer;
    private $wdata;
    private $action;

    public function __set($property_name, $value){
        if($property_name == 'file'){
            if($value instanceof ngx_open_file_s){
                $this->file = $value ;
            }else{
                die('ngx_log need right file type');
            }
        }elseif($property_name == 'hander'){

            if($value instanceof Closure){
                $this->handler = $value ;
            }else{
                die('ngx_log need right handler type');
            }

        }elseif($property_name == 'writer'){

            if($value instanceof Closure){
                $this->writer = $value ;
            }else{
                die('ngx_log need right writer type');
            }
        }else{
            $this->$property_name = $value;
        }

      }

    public function __get($property_name){

            return $this->$property_name;
    }


    public function handle($log, $s){

        return call_user_func($this->handler,$log,$s);
    }

    public function write($log, $level, $s){

        return call_user_func($this->writer,$log,$level,$s);
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
     $err_levels = array(
        '',
        "emerg",
        "alert",
        "crit",
        "error",
        "warn",
        "notice",
        "info",
        "debug"
    );
    ngx_cfg('err_levels',$err_levels);
    ngx_cfg('ngx_use_stderr',1);
    $ngx_log_file = new ngx_open_file_s();
    $ngx_log = new ngx_log();
    $ngx_log->file = $ngx_log_file;
    $ngx_log->level = NGX_LOG_NOTICE;
    $fd = ngx_open_file(NGX_ERROR_LOG_PATH, NGX_FILE_APPEND, NGX_FILE_DEFAULT_ACCESS);
    if($fd == NGX_INVALID_FILE){
        ngx_log_stderr(NGX_FERROR,
            "[alert] could not open error log file: ".
                       ngx_open_file_n." \"%s\" failed", NGX_ERROR_LOG_PATH);
    }
    $ngx_log_file->fd = $fd;

    return $ngx_log;
}


function ngx_log_error($level, ngx_log $log, $err_no, $fmt, array $args = array())
{
    if ($log->log_level >= $level) {

       ngx_log_error_core($level, $log, $err_no, $fmt, $args);
   }
}

function ngx_log_error_core($level, ngx_log $log, $err_no,
    $fmt, array $args = array())

{

    $p = date("Y-m-d H:i:s");

    $err_levels = ngx_cfg('err_levels');

    $p = ngx_slprintf($p,  " [%V] ", (array) $err_levels[$level]);

    /* pid#tid */
    $p = ngx_slprintf($p,  "%P#",
                    ngx_cfg('ngx_pid'));

    if ($log->connection) {
    $p = ngx_slprintf($p, "*%uA ", $log->connection);
    }

    $msg = $p;


    $p = ngx_vslprintf($p,  $fmt, $args);


    if ($err_no) {
        $p = ngx_log_errno($p,  $err_no);
    }

    if ($level != NGX_LOG_DEBUG &&  $log->handler) {
        $p = $log->handle($log, $p);
    }

//    if (p > last - NGX_LINEFEED_SIZE) {
//        p = last - NGX_LINEFEED_SIZE;
//    }

    ngx_linefeed($p);

    $wrote_stderr = 0;
    $debug_connection = ($log->log_level & NGX_LOG_DEBUG_CONNECTION) != 0;

    while ($log) {

        if ($log->log_level < $level && !$debug_connection) {
            break;
        }

        if ($log->writer) {
            $log->write($log, $level, $p);
            goto next;
        }

        //todo should know why do it
//        if (ngx_time() == log->disk_full_time) {
//
//            /*
//             * on FreeBSD writing to a full filesystem with enabled softupdates
//             * may block process for much longer time than writing to non-full
//             * filesystem, so we skip writing to a log for one second
//             */
//
//            goto next;
//        }

        $n = ngx_write_fd($log->file->fd, $p);

//        if ($n == -1 && $ngx_errno == NGX_ENOSPC) {
//            log->disk_full_time = ngx_time();
//        }

        if ($log->file->fd == ngx_stderr) {
            $wrote_stderr = 1;
        }

    next:

        $log = $log->_next();
    }

    if (!ngx_cfg('ngx_use_stderr')
        || $level > NGX_LOG_WARN
        || $wrote_stderr)
    {
        return;
    }


     ngx_sprintf($msg, "nginx: [%V] ", (array)$err_levels[$level]);

     ngx_write_console(ngx_stderr, $msg);
}


function ngx_log_errno($p, $err)
{
    $p = ngx_slprintf($p, " (%d: ", $err);

    $p = ngx_strerror($err, $p);

    $p .= ')';

    return $p;
}

function  ngx_log_debug4($level, $log, $err, $fmt, $arg1, $arg2, $arg3, $arg4){

}

