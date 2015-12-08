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


//static $atomic_ngx_reap;
//static $atomic_ngx_sigio;
//static $atomic_ngx_sigalrm;
//static $atomic_ngx_terminate;
//static $atomic_ngx_quit;
//static $atomic_ngx_debug_quit;
//static $ngx_exiting;
//static $atomic_ngx_reconfigure;
//static $atomic_ngx_reopen;
//
//static $atomic_ngx_change_binary;
//static $ngx_new_binary;
//static $ngx_inherited;
//static $ngx_daemonized;
//static $atomic_ngx_noaccept;
//static $ngx_noaccepting;
//static $ngx_restart;
//
//
//static $master_process = "master process";

function ngx_process($i = null){
    static $ngx_process = null;
    if(!is_null($i)){
       $ngx_process = $i;
    }else{
       return $ngx_process;
    }
}

function ngx_new_binary($i = null){
    static $ngx_new_binary = null;
    if(!is_null($i)){
       $ngx_new_binary = $i;
    }else{
       return $ngx_new_binary;
    }
}

function ngx_inherited($i = null){
    static $ngx_inherited = null;
    if(!is_null($i)){
       $ngx_inherited = $i;
    }else{
       return $ngx_inherited;
    }
}

function ngx_daemonized($i = null){
    static $ngx_daemonized = null;
    if(!is_null($i)){
       $ngx_daemonized = $i;
    }else{
       return $ngx_daemonized;
    }
}

//ngx_uint_t    ngx_process;
//ngx_uint_t    ngx_worker;
//ngx_pid_t     ngx_pid;
//
//sig_atomic_t  ngx_reap;
function ngx_reap($i = null){
   static $ngx_reap = null;
    if(!is_null($i)){
        $ngx_reap = $i;
    }else{
       return $ngx_reap;
    }
}
//sig_atomic_t  ngx_sigio;
function ngx_sigio($i = null){
   static $ngx_sigio = null;
    if(!is_null($i)){
       $ngx_sigio = $i;
    }else{
       return $ngx_sigio;
    }
}
//sig_atomic_t  ngx_sigalrm;
function ngx_sigalrm($i = null){
   static $ngx_sigalrm = null;
    if(!is_null($i)){
        $ngx_sigalrm = $i;
    }else{
       return $ngx_sigalrm;
    }
}
//sig_atomic_t  ngx_terminate;
function ngx_terminate($i = null){
   static $ngx_terminate = null;
    if(!is_null($i)){
       $ngx_terminate  = $i;
    }else{
       return $ngx_terminate;
    }
}
//sig_atomic_t  ngx_quit;
function ngx_quit($i = null){
    static $ngx_quit = null;
   if(!is_null($i)){
      $ngx_quit = $i;
   }else{
       return $ngx_quit;
   }
}
//sig_atomic_t  ngx_debug_quit;
function ngx_debug_quit($i = null){
   static $ngx_debug_quit = null;
    if(!is_null($ngx_debug_quit)){
       $ngx_debug_quit = $i;
    }else{
       return $ngx_debug_quit;
    }
}
//ngx_uint_t    ngx_exiting;
//sig_atomic_t  ngx_reconfigure;
function ngx_reconfigure($i = null){
   static $ngx_reconfigure = null;
    if(!is_null($i)){
        $ngx_reconfigure = $i;
    }else{
       return $ngx_reconfigure;
    }
}
//sig_atomic_t  ngx_reopen;
function ngx_reopen($i = null){
   static $ngx_reopen = null;
    if(!is_null($i)){
       $ngx_reopen = $i;
    }else{
       return $ngx_reopen;
    }
}
//
//sig_atomic_t  ngx_change_binary;
function ngx_change_binary($i = null){
    static $ngx_change_binary = null;
   if(!is_null($i)){
      $ngx_change_binary = $i;
   } else{
      return $ngx_change_binary;
   }
}
//ngx_pid_t     ngx_new_binary;
function ngx_new_binary($i = null){
   static $ngx_new_binary = null;
    if(!is_null($i)){
       $ngx_new_binary = $i;
    }else{
       return $ngx_new_binary;
    }
}
//ngx_uint_t    ngx_inherited;
//ngx_uint_t    ngx_daemonized;
//
//sig_atomic_t  ngx_noaccept;
function ngx_noaccept($i = null){
   static $ngx_noaccept = null;
    if(!is_null($i)){
       $ngx_noaccept = $i;
    }else{
       return $ngx_noaccept;
    }
}
//ngx_uint_t    ngx_noaccepting;
//ngx_uint_t    ngx_restart;

