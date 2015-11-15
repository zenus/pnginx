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
define('NGX_FILE_ERROR',-1);

define('ngx_open_file_n',"open()");
define('ngx_fd_info_n',"fstat()");
define('ngx_close_file_n',"close()");
define('ngx_read_file_n',"read()");

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

function ngx_write_fd($fd, $p)
{
    return fwrite($fd, $p);
}

function ngx_fd_info($fd){
    return fstat($fd);
}
function ngx_file_size($sb){

    return $sb['size'];
}

function ngx_read_file(ngx_file_t &$file, $buf, $size, $offset)
{

    ngx_log_debug4(NGX_LOG_DEBUG_CORE, $file->log, 0,
                   "read: %d, %p, %uz, %O", $file->fd, $buf, $size, $offset);

//#if (NGX_HAVE_PREAD)
//
//    n = pread(file->fd, buf, size, offset);
//
//    if (n == -1) {
//        ngx_log_error(NGX_LOG_CRIT, file->log, ngx_errno,
//                      "pread() \"%s\" failed", file->name.data);
//        return NGX_ERROR;
//    }
//
//#else

    if ($file->sys_offset != $offset) {
    if (fseek($file->fd, $offset, SEEK_SET) == -1) {
        ngx_log_error(NGX_LOG_CRIT, $file->log, NGX_FERROR,
                          "fseek() \"%s\" failed", (array)$file->name);
            return NGX_ERROR;
        }

        $file->sys_offset = $offset;
    }

    $buf = fread($file->fd,  $size);

    if ($buf == false) {
        ngx_log_error(NGX_LOG_CRIT, $file->log, NGX_FERROR,
                      "read() \"%s\" failed", $file->name);
        return NGX_ERROR;
    }

    $n = strlen($buf);

    $file->sys_offset += $n;

//#endif

    $file->offset += $n;

    return $n;
}

