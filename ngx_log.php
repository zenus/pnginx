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

function  err_levels($i = null){

     static  $err_levels = array(
                "",
            "emerg",
            "alert",
            "crit",
            "error",
            "warn",
            "notice",
            "info",
            "debug"
         );
    if(!is_null($i)){
        return $err_levels[$i];
    }else{
       return $err_levels;
    }
}

function debug_levels($i)
{
    static $debug_levels = array(
        "debug_core", "debug_alloc", "debug_mutex", "debug_event",
        "debug_http", "debug_mail", "debug_mysql", "debug_stream"
    );
    return $debug_levels[$i];
    }



class ngx_log extends SplDoublyLinkedList{

    private $ngx_log_s;

    public  function __construct()
    {
        $this->ngx_log_s = new ngx_log_s();
        $this->push($this->ngx_log_s);
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

    public function handle($log, $s){

        return $this->ngx_log_s->handle($log,$s);
    }

    public function write($log, $level, $s){
        return $this->ngx_log_s->write($log, $level, $s);
    }
//    public function _current(){
//        return $this->ngx_log_s;
//    }
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
    if(!empty($var)){
        array_unshift($var,$no);
    }
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

function ngx_write_stdout($text)
{
    ngx_write_fd(ngx_stdout, $text);
}

function  ngx_use_stderr($i = null){
   static $ngx_use_stderr = 1;
    if(!is_null($ngx_use_stderr)){
       $ngx_use_stderr = $i;
    }else{
       return $ngx_use_stderr;
    }
}


function ngx_log_init(){
    $ngx_log_file = new ngx_open_file_s();
    $ngx_log = new ngx_log();
    $ngx_log->file = $ngx_log_file;
    $ngx_log->level = NGX_LOG_NOTICE;
    $fd = ngx_open_file(NGX_ERROR_LOG_PATH, NGX_FILE_APPEND, NGX_FILE_DEFAULT_ACCESS);
    if($fd == NGX_INVALID_FILE){
        ngx_log_stderr(NGX_FERROR,
            "[alert] could not open error log file: ".
                       ngx_open_file_n." \"%s\" failed", array(NGX_ERROR_LOG_PATH));
    }
    $ngx_log_file->fd = $fd;

    return $ngx_log;
}


function ngx_log_error($level, ngx_log $log, $err_no, $fmt,  $args = array())
{
    $args = is_array($args) ? $args : array($args);
    if ($log->log_level >= $level) {

       ngx_log_error_core($level, $log, $err_no, $fmt, $args);
   }
}

function ngx_log_error_core($level, ngx_log $log, $err_no,
    $fmt, array $args = array())

{

    $p = date("Y-m-d H:i:s");

    $err_levels = err_levels();

    $p = ngx_slprintf($p,  " [%V] ", (array) $err_levels[$level]);

    /* pid#tid */
    $p = ngx_slprintf($p,  "%P#",
                    ngx_pid());

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

    if (ngx_use_stderr()
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

function ngx_log_debug6($level, $log, $err, $fmt, $arg1, $arg2, $arg3, $arg4, $arg5, $arg6){

}
function  ngx_log_debug7($level, $log, $err, $fmt, $arg1, $arg2, $arg3, $arg4, $arg5, $arg6, $arg7){

}
function ngx_log_debug3($level, $log, $err, $fmt, $arg1, $arg2, $arg3){

}

function ngx_log_debug2($level, $log, $err, $fmt, $arg1, $arg2){

}

function ngx_log_debug1($level, $log, $err, $fmt, $arg1){

}

function ngx_log_debug0($level, $log, $err, $fmt){

}



function ngx_errlog_module(){
    static $ngx_errlog_module;
    if(is_null($ngx_errlog_module)){
        $obj = new ngx_module_t();
        $ngx_errlog_module = $obj;
        $ngx_errlog_module->version = 1;
        $ngx_errlog_module->ctx = ngx_errlog_module_ctx();
        $ngx_errlog_module->commands = ngx_errlog_commands();
        $ngx_errlog_module->type = NGX_CORE_MODULE;
    }
        return $ngx_errlog_module;
}
function ngx_errlog_module_ctx(){

    static $ngx_errlog_module_ctx;
    if(is_null($ngx_errlog_module_ctx)){
        $obj= new ngx_core_module_t();
        $ngx_errlog_module_ctx = $obj;
        $ngx_errlog_module_ctx->name = 'errlog';
        $ngx_errlog_module_ctx->create_conf = null;
        $ngx_errlog_module_ctx->init_conf = null;
    }
    return $ngx_errlog_module_ctx;
}

function ngx_errlog_commands(){

     $ngx_errlog_commands = array(
        array(
              'name'=>"error_log",
              'type'=>NGX_MAIN_CONF|NGX_CONF_1MORE,
              'set'=>'ngx_error_log',
              'conf'=>0,
            //  0,
              'post'=>NULL
        ),
         array(
             'name'=>'',
             'type'=>0,
             'set'=>NULL,
             'conf'=>0,
             //0,
             'post'=>NULL
         ),
          );

    return $ngx_errlog_commands;

}




function ngx_error_log(ngx_conf_t $cf, ngx_command_t $cmd, $conf)
{
//ngx_log_t  *dummy;

    $dummy = $cf->cycle->new_log;

    return ngx_log_set_log($cf, $dummy);
}

function ngx_log_set_log(ngx_conf_t $cf, ngx_log $head)
{
    /**** $head is a array of ngx_log **/
//ngx_log_t          *new_log;
//    ngx_str_t          *value, name;
//    ngx_syslog_peer_t  *peer;

    if ($head instanceof ngx_log && $head->log_level == 0) {
           $new_log = $head;
    } else {
        $new_log = new ngx_log();
        if (empty($head)) {
            $head = $new_log;
         }
    }

    $value = $cf->args;

    if (ngx_strcmp($value[1], "stderr") == 0) {
        $name = '';
        $cf->cycle->log_use_stderr = 1;
        $new_log->file = ngx_conf_open_file($cf->cycle, $name);
        if ($new_log->file == NULL) {
        return NGX_CONF_ERROR;
    }

     } else if (ngx_strncmp($value[1], "memory:", 7) == 0) {
        ngx_conf_log_error(NGX_LOG_EMERG, $cf, 0,
            "nginx was built without debug support");
        return NGX_CONF_ERROR;

     } else if (ngx_strncmp($value[1], "syslog:", 7) == 0) {

        $peer = new ngx_syslog_peer_t();

        if (ngx_syslog_process_conf($cf, $peer) != NGX_CONF_OK) {
            return NGX_CONF_ERROR;
        }

        $new_log->writer = 'ngx_syslog_writer';
        $new_log->wdata = $peer;

    } else {
        $new_log->file = ngx_conf_open_file($cf->cycle, $value[1]);
        if ($new_log->file == NULL) {
        return NGX_CONF_ERROR;
    }
    }

    if (ngx_log_set_levels($cf, $new_log) != NGX_CONF_OK) {
        return NGX_CONF_ERROR;
    }

    if ($head != $new_log) {

        ngx_log_insert($head, $new_log);
    }
    return NGX_CONF_OK;
}

function  ngx_log_insert(ngx_log $log, ngx_log $new_log)
{

    //todo  insert into list by log_level
    $log->push($new_log->current());
}

//function ngx_syslog_writer_closure(){
//    return function(ngx_log $log, $level, $buf){
//        ngx_syslog_writer($log,$level,$buf);
//    };
//}

function ngx_syslog_writer(ngx_log $log, $level, $buf)
{
//    u_char             *p, msg[NGX_SYSLOG_MAX_STR];
//    ngx_uint_t          head_len;
//    ngx_syslog_peer_t  *peer;

    $peer = $log->wdata;

    if ($peer->busy) {
    return;
    }

    $peer->busy = 1;
    $peer->severity = $level - 1;

    $msg = '';
    $p = ngx_syslog_add_header($peer, $msg);
    //head_len = p - msg;

//    len -= NGX_LINEFEED_SIZE;
//
//    if (len > NGX_SYSLOG_MAX_STR - head_len) {
//        len = NGX_SYSLOG_MAX_STR - head_len;
//    }

    $p = ngx_snprintf($p,  "%s", $buf);

     ngx_syslog_send($peer, $msg);

    $peer->busy = 0;
}

function ngx_syslog_add_header(ngx_syslog_peer_t $peer, $buf)
{
    //ngx_uint_t  pri;

    $pri = $peer->facility * 8 + $peer->severity;

    if ($peer->nohostname) {
        return ngx_sprintf($buf, "<%ui>%V %V: ", array($pri, ngx_cached_syslog_time(),
            $peer->tag));
    }
    $ngx_cycle = ngx_cycle();
    return ngx_sprintf($buf, "<%ui>%V %V %V: ", array($pri, ngx_cached_syslog_time(),
        $ngx_cycle->hostname, $peer->tag));
}

function ngx_syslog_send(ngx_syslog_peer_t $peer, $buf)
{
//    ssize_t  n;

    if (empty($peer->conn->fd)) {
    if (ngx_syslog_init_peer($peer) != NGX_OK) {
        return NGX_ERROR;
    }
}

    /* log syslog socket events with valid log */
    $ngx_cycle = ngx_cycle();
    $peer->conn->log = $ngx_cycle->log;

    $ngx_io = ngx_io();
    if ($ngx_io->send) {
        //todo find where to create the function
        $n = $ngx_io->send($peer->conn, $buf, $len);

    } else {
        /* event module has not yet set ngx_io */
        $ngx_os_io = ngx_os_io();
        $n = $ngx_os_io->send($peer->conn, $buf, $len);
    }


    //todo we change the ngx_addr_t
    if ($n == NGX_ERROR && $peer->server->family == AF_UNIX) {
        ngx_close_socket($peer->conn->fd);
        if (socket_last_error()) {
            ngx_log_error(NGX_LOG_ALERT, $ngx_cycle->log, socket_last_error(),
                              ngx_close_socket_n ." failed");
            }

        $peer->conn->fd = null;
    }


    return $n;
}

function ngx_log_set_levels(ngx_conf_t $cf, ngx_log $log)
{
//ngx_uint_t   i, n, d, found;
//    ngx_str_t   *value;

    if (count($cf->args) == 2) {
        $log->log_level = NGX_LOG_ERR;
        return NGX_CONF_OK;
    }

    $value = $cf->args;

    for ($i = 2; $i < count($cf->args); $i++) {
            $found = 0;

        for ($n = 1; $n <= NGX_LOG_DEBUG; $n++) {
            if (ngx_strcmp($value[$i], err_levels($n)) == 0) {

                if ($log->log_level != 0) {
                    ngx_conf_log_error(NGX_LOG_EMERG, $cf, 0,
                        "duplicate log level \"%V\"",
                        $value[$i]);
                        return NGX_CONF_ERROR;
                    }

                    $log->log_level = $n;
                    $found = 1;
                    break;
                }
            }

        for ($n = 0, $d = NGX_LOG_DEBUG_FIRST; $d <= NGX_LOG_DEBUG_LAST; $d <<= 1) {
            if (ngx_strcmp($value[$i], debug_levels($n++)) == 0) {
                if ($log->log_level & ~NGX_LOG_DEBUG_ALL) {
                    ngx_conf_log_error(NGX_LOG_EMERG, $cf, 0,
                        "invalid log level \"%V\"",
                        $value[$i]);
                        return NGX_CONF_ERROR;
                    }

                    $log->log_level |= $d;
                    $found = 1;
                    break;
                }
            }


    if (!$found) {
        ngx_conf_log_error(NGX_LOG_EMERG, $cf, 0,
            "invalid log level \"%V\"", $value[$i]);
            return NGX_CONF_ERROR;
        }
    }

    if ($log->log_level == NGX_LOG_DEBUG) {
        $log->log_level = NGX_LOG_DEBUG_ALL;
      }

    return NGX_CONF_OK;
}

function ngx_log_open_default(ngx_cycle_t $cycle)
{
//ngx_log_t         *log;
    static   $error_log = NGX_ERROR_LOG_PATH;

    $log = new ngx_log();
    if (ngx_log_get_file_log($cycle->log) != NULL) {
            return NGX_OK;
        }

    if ($cycle->new_log->log_level != 0) {
    /* there are some error logs, but no files */
        $log = $cycle->new_log;
    }

    $log->log_level = NGX_LOG_ERR;

    $log->file = ngx_conf_open_file($cycle, $error_log);
    if ($log->file == NULL) {
        return NGX_ERROR;
    }

    if ($log != $cycle->new_log) {
        ngx_log_insert($cycle->new_log, $log);
    }

    return NGX_OK;
}

function ngx_log_get_file_log(ngx_log $head)
{
    for ($head->rewind(); $head->valid(); $head->_next()) {
        if($head->file != null){
           return $head;
        }
    }

    return null;
}

function ngx_log(ngx_log $log = null){
    static $ngx_log = null;
    if(!is_null($log)){
       $ngx_log = $log;
    }else{
        return $ngx_log;
    }

}

function ngx_log_file(ngx_open_file_s $file = null){
   static $ngx_log_file = null;
    if(!is_null($file)){
       $ngx_log_file = $file;
    }else{
        return $ngx_log_file;
    }
}

//function ngx_use_stderr(){
//
//    static $ngx_use_stderr = 1;
//    return $ngx_use_stderr;
//}

function ngx_log_redirect_stderr(ngx_cycle_t $cycle)
{
//ngx_fd_t  fd;

    if ($cycle->log_use_stderr) {
         return NGX_OK;
     }

    /* file log always exists when we are called */
    $log = ngx_log_get_file_log($cycle->log)->file->fd;

    if ($log->file->fd != ngx_stderr) {
        if (ngx_set_stderr($log) == NGX_FILE_ERROR) {
            ngx_log_error(NGX_LOG_ALERT, $cycle->log, NGX_FILE_ERROR,
                          ngx_set_stderr_n ." failed");

            return NGX_ERROR;
        }
    }

    return NGX_OK;
}

