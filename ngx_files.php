<?php
define('ngx_stdout',STDOUT);
define('ngx_stderr',STDERR);
define('NGX_LINEFEED',"\x0a");

include_once 'ngx_core.php';
/**
 * Created by PhpStorm.
 * User: lb@carisok.com
 * Date: 2015/11/4
 * Time: 12:30
 * @param $p
 * @return string
 */

function ngx_linefeed(&$p)
{
    return  $p.LF;
}
