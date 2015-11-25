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


function ngx_errlog_module(){

    static $ngx_errlog_module = array(
        0, 0, 0, 0, 0, 0, 1,
    ngx_errlog_module_ctx(),                /* module context */
    ngx_errlog_commands(),                   /* module directives */
    NGX_CORE_MODULE,                       /* module type */
    NULL,                                  /* init master */
    NULL,                                  /* init module */
    NULL,                                  /* init process */
    NULL,                                  /* init thread */
    NULL,                                  /* exit thread */
    NULL,                                  /* exit process */
    NULL,                                  /* exit master */
    0, 0, 0, 0, 0, 0, 0, 0
);
    return $ngx_errlog_module;
}


function ngx_errlog_commands(){
    static $ngx_errlog_commands = array(
        array(
              "error_log",
              NGX_MAIN_CONF|NGX_CONF_1MORE,
              ngx_error_log,
              0,
              0,
              NULL),
         array( '', 0, NULL, 0, 0, NULL ),
          );
    return $ngx_errlog_commands;

}

static ngx_command_t  ngx_errlog_commands[] = {

    {ngx_string("error_log"),
     NGX_MAIN_CONF|NGX_CONF_1MORE,
     ngx_error_log,
     0,
     0,
     NULL},

    ngx_null_command
};

//
//static ngx_core_module_t  ngx_errlog_module_ctx = {
//    ngx_string("errlog"),
//    NULL,
//    NULL
//};

function ngx_error_log(ngx_conf_t $cf, ngx_command_t $cmd, $conf)
{
//ngx_log_t  *dummy;

    $dummy = $cf->cycle->new_log;

    return ngx_log_set_log($cf, $dummy);
}

function ngx_log_set_log(ngx_conf_t $cf, /**ngx_log array***/ $heads)
{
    /**** $head is a array of ngx_log **/
//ngx_log_t          *new_log;
//    ngx_str_t          *value, name;
//    ngx_syslog_peer_t  *peer;

    $head = current($heads);
    if ($head instanceof ngx_log && $head->log_level == 0) {
           $new_log = $head;
    } else {
        $new_log = new ngx_log();
        if (empty($heads)) {
        $heads[] = $new_log;
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

        $new_log->writer = ngx_syslog_writer_closure();
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

    if (*head != new_log) {
    ngx_log_insert(*head, new_log);
    }

    return NGX_CONF_OK;
}

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

     ngx_syslog_send($peer, $msg, p - msg);

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

function ngx_syslog_send(ngx_syslog_peer_t $peer, $buf, $len)
{
//    ssize_t  n;

    if (empty($peer->conn->fd)) {
    if (ngx_syslog_init_peer($peer) != NGX_OK) {
        return NGX_ERROR;
    }
}

    /* log syslog socket events with valid log */
    peer->conn.log = ngx_cycle->log;

    if (ngx_send) {
        n = ngx_send(&peer->conn, buf, len);

    } else {
        /* event module has not yet set ngx_io */
        n = ngx_os_io.send(&peer->conn, buf, len);
    }


    if (n == NGX_ERROR && peer->server.sockaddr->sa_family == AF_UNIX) {

    if (ngx_close_socket($peer->conn.fd) == -1) {
        ngx_log_error(NGX_LOG_ALERT, $ngx_cycle->log, $ngx_socket_errno,
                          ngx_close_socket_n ." failed");
        }

        $peer->conn.fd = (ngx_socket_t) -1;
    }


    return $n;
}


