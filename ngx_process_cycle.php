<?php
/**
 * Created by PhpStorm.
 * User: zenus@github.com
 * Date: 2015/11/4
 * Time: 16:00
 */


define('NGX_CMD_OPEN_CHANNEL' , 1);
define('NGX_CMD_CLOSE_CHANNEL', 2);
define('NGX_CMD_QUIT'          , 3);
define('NGX_CMD_TERMINATE'     , 4);
define('NGX_CMD_REOPEN'        , 5);
define('NGX_PROCESS_SINGLE'    , 0);
define('NGX_PROCESS_MASTER'    , 1);
define('NGX_PROCESS_SIGNALLER' , 2);
define('NGX_PROCESS_WORKER'    , 3);
define('NGX_PROCESS_HELPER'    , 4);

static $ngx_process;
static $ngx_worker;
static $ngx_pid;


static $atomic_ngx_reap;
static $atomic_ngx_sigio;
static $atomic_ngx_sigalrm;
static $atomic_ngx_terminate;
static $atomic_ngx_quit;
static $atomic_ngx_debug_quit;
static $ngx_exiting;
static $atomic_ngx_reconfigure;
static $atomic_ngx_reopen;

static $atomic_ngx_change_binary;
static $ngx_new_binary;
static $ngx_inherited;
static $ngx_daemonized;
static $atomic_ngx_noaccept;
static $ngx_noaccepting;
static $ngx_restart;


static $master_process = "master process";