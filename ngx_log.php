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

class ngx_log_s {

    private  /**ngx_open_file_s*/ $file = null;
    public $log_level = null;
    private $connection = null;
    private $disk_full_time;
    private /**ngx_log_handler_pt**/ $handler;
    private $data;
    private /**ngx_log_writer_pt*/ $writer;
    private $wdata;
    private $action;

    public function set_file(ngx_open_file_s &$file){
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
    $ngx_log_file = new ngx_open_file_s();
    $ngx_log = new ngx_log_s();
    $ngx_log->set_file($ngx_log_file);
    $ngx_log->set_level(NGX_LOG_NOTICE);
    $fd = ngx_open_file(NGX_ERROR_LOG_PATH, NGX_FILE_APPEND, NGX_FILE_DEFAULT_ACCESS);
    if($fd == NGX_INVALID_FILE){
        ngx_log_stderr(NGX_FERROR,
            "[alert] could not open error log file: ".
                       ngx_open_file_n." \"%s\" failed", NGX_ERROR_LOG_PATH);
    }
    $ngx_log_file->set_fd($fd);

    return $ngx_log;
}


function ngx_log_error($level, ngx_log_s $log, $err_no,
    $fmt, array $args = array())
{
    if ($log->log_level >= $level) {

       ngx_log_error_core($level, $log, $err_no, $fmt, $args);
   }
}

function ngx_log_error_core($level, ngx_log_s $log, $err_no,
    $fmt, array $args = array())

{
//#if (NGX_HAVE_VARIADIC_MACROS)
//    va_list      args;
//#endif
//    u_char      *p, *last, *msg;
//    ssize_t      n;
//    ngx_uint_t   wrote_stderr, debug_connection;
//    u_char       errstr[NGX_MAX_ERROR_STR];
//
//    last = errstr + NGX_MAX_ERROR_STR;

//    p = ngx_cpymem(errstr, ngx_cached_err_log_time.data,
//        ngx_cached_err_log_time.len);

    $p = date("Y-m-d H:i:s");

    $err_levels = ngx_cfg('err_levels');

    $p = ngx_slprintf($p,  " [%V] ", (array) $err_levels[$level]);

    /* pid#tid */
    p = ngx_slprintf(p, last, "%P#" NGX_TID_T_FMT ": ",
                    ngx_log_pid, ngx_log_tid);

    if (log->connection) {
    p = ngx_slprintf(p, last, "*%uA ", log->connection);
    }

    msg = p;

#if (NGX_HAVE_VARIADIC_MACROS)

    va_start(args, fmt);
    p = ngx_vslprintf(p, last, fmt, args);
    va_end(args);

#else

    p = ngx_vslprintf(p, last, fmt, args);

#endif

    if (err) {
        p = ngx_log_errno(p, last, err);
    }

    if (level != NGX_LOG_DEBUG && log->handler) {
    p = log->handler(log, p, last - p);
    }

    if (p > last - NGX_LINEFEED_SIZE) {
        p = last - NGX_LINEFEED_SIZE;
    }

    ngx_linefeed(p);

    wrote_stderr = 0;
    debug_connection = (log->log_level & NGX_LOG_DEBUG_CONNECTION) != 0;

    while (log) {

        if (log->log_level < level && !debug_connection) {
            break;
        }

        if (log->writer) {
            log->writer(log, level, errstr, p - errstr);
            goto next;
        }

        if (ngx_time() == log->disk_full_time) {

            /*
             * on FreeBSD writing to a full filesystem with enabled softupdates
             * may block process for much longer time than writing to non-full
             * filesystem, so we skip writing to a log for one second
             */

            goto next;
        }

        n = ngx_write_fd(log->file->fd, errstr, p - errstr);

        if (n == -1 && ngx_errno == NGX_ENOSPC) {
            log->disk_full_time = ngx_time();
        }

        if (log->file->fd == ngx_stderr) {
            wrote_stderr = 1;
        }

    next:

        log = log->next;
    }

    if (!ngx_use_stderr
        || level > NGX_LOG_WARN
        || wrote_stderr)
    {
        return;
    }

    msg -= (7 + err_levels[level].len + 3);

    (void) ngx_sprintf(msg, "nginx: [%V] ", &err_levels[level]);

    (void) ngx_write_console(ngx_stderr, msg, p - msg);
}

