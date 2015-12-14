<?php
define('ngx_stdout',STDOUT);
define('ngx_stderr',STDERR);
/* Standard file descriptors.  */
define('STDIN_FILENO', STDIN);/* Standard input.  */
define('STDOUT_FILENO',	STDOUT);	/* Standard output.  */
define('STDERR_FILENO',	STDERR);	/* Standard error output.  */
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

define('NGX_INVALID_FILE',false);
define('NGX_FILE_ERROR',false);

define('ngx_open_file_n',"open()");
define('ngx_fd_info_n',"fstat()");
define('ngx_close_file_n',"close()");
define('ngx_read_file_n',"read()");
define('ngx_delete_file_n',"unlink()");
define('ngx_create_dir_n',"mkdir()");
define('ngx_file_info_n',"stat()");
define('ngx_set_stderr_n',"set_stderr(STDERR_FILENO)");

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

//function ngx_write_fd($fd, $p)
//{
//    return fwrite($fd, $p);
//}

function ngx_set_stderr(ngx_log $log){
    $log->file->fd = ngx_stderr;
}

function ngx_fd_info($fd){
    return fstat($fd);
}
function ngx_file_size($sb){

    return $sb['size'];
}

function  ngx_close_file($fd){
   return fclose($fd);
}

function ngx_read_file(ngx_file_t $file, &$buf, $size, $offset)
{

    ngx_log_debug4(NGX_LOG_DEBUG_CORE, $file->log, 0,
                   "read: %d, %p, %uz, %O", $file->fd, $buf, $size, $offset);

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

    $file->offset += $n;

    return $n;
}

function ngx_write_file(ngx_file_t $file, $buf, $size, $offset)
{
//    ssize_t  n, written;

    ngx_log_debug4(NGX_LOG_DEBUG_CORE, $file->log, 0,
                   "write: %d, %p, %uz, %O", $file->fd, $buf, $size, $offset);

    $written = 0;

    if ($file->sys_offset != $offset) {
        if (fseek($file->fd, $offset, SEEK_SET) == -1) {
            ngx_log_error(NGX_LOG_CRIT, $file->log, NGX_FLERROR,
                              "lseek() \"%s\" failed", $file->name);
                return NGX_ERROR;
            }

        $file->sys_offset = $offset;
    }

    for ( ;; ) {
        $buf = substr($buf,$written);
        $n = fwrite($file->fd, $buf, $size);

        if ($n == false) {
            ngx_log_error(NGX_LOG_CRIT, $file->log, NGX_FERROR,
                          "write() \"%s\" failed", $file->name);
            return NGX_ERROR;
        }

        $file->offset += $n;
        $written += $n;

        if ( $n == $size) {
            return $written;
        }

        $size -= $n;
    }
}

function ngx_file_info($file){
  return   stat($file);
}

function ngx_delete_file($name)
{
    return unlink($name);
}
function ngx_create_dir($name, $access)
{
    return mkdir($name, $access);
}


