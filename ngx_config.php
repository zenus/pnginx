<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 15-11-8
 * Time: 上午10:40
 */
//define('NGX_INT32_LEN',strlen("-2147483648") - 1);
#define NGX_INT64_LEN   (sizeof("-9223372036854775808") - 1)
define('NGX_MAX_INT_T_VALUE', PHP_INT_MAX);

define('NGX_SHUTDOWN_SIGNAL',      'QUIT');
define('NGX_TERMINATE_SIGNAL',     'TERM');
define('NGX_NOACCEPT_SIGNAL',      'WINCH');
define('NGX_RECONFIGURE_SIGNAL',   'HUP');

define('NGX_REOPEN_SIGNAL',        'USR1');
define('NGX_CHANGEBIN_SIGNAL',     'USR2');
