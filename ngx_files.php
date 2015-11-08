<?php
define('ngx_stdout',STDOUT);
define('ngx_stderr',STDERR);
define('NGX_LINEFEED',"\x0a");
define('NGX_FILE_RDONLY',         'r');
define('NGX_FILE_WRONLY',          'w');
define('NGX_FILE_RDWR',            'rw');
define('NGX_FILE_CREATE_OR_OPEN',  'c');
define('NGX_FILE_OPEN',           'r');
define('NGX_FILE_TRUNCATE',        'w');
define('NGX_FILE_APPEND',          'a');

define('NGX_FILE_DEFAULT_ACCESS',  0644);
define('NGX_FILE_OWNER_ACCESS',   0600);

//define('NGX_FILE_NONBLOCK',        O_NONBLOCK);

define('NGX_INVALID_FILE',FALSE);
#define NGX_FILE_ERROR

define('ngx_open_file_n',"open()");

include_once 'ngx_core.php';
/**
 * Created by PhpStorm.
 * User: zenus@github.com
 * Date: 2015/11/4
 * Time: 12:30
 * @param $p
 * @return string
 */

function ngx_linefeed(&$p)
{
    return  $p.LF;
}
function ngx_open_file($file,$mode,$access){
    $fp =  fopen($file,$mode);
    chmod($file, $access);
    return $fp;
}

function ngx_path_separator($c){

    return (($c) == '/');
}
