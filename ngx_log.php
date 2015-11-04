<?php
include_once 'ngx_files.php';

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

