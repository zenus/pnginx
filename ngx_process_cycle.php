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

//static $ngx_process;
//static $ngx_worker;
//static $ngx_pid;


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
    if(!is_null($i)){
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

function ngx_single_process_cycle(ngx_cycle_t $cycle)
{
//ngx_uint_t  i;

    if (ngx_set_environment($cycle, NULL) == NULL) {
        /* fatal */
        exit(2);
    }

    for ($i = 0; ngx_modules($i); $i++) {
    if (ngx_modules($i)->init_process) {
        if (ngx_modules($i)->init_process($cycle) == NGX_ERROR) {
            /* fatal */
            exit(2);
        }
        }
    }

    for ( ;; ) {
        ngx_log_debug0(NGX_LOG_DEBUG_EVENT, $cycle->log, 0, "worker cycle");

        ngx_process_events_and_timers($cycle);

        if (ngx_terminate() || ngx_quit()) {

            for ($i = 0; ngx_modules($i); $i++) {
                if (ngx_modules($i)->exit_process) {
                    ngx_modules($i)->exit_process($cycle);
                }
            }

            ngx_master_process_exit($cycle);
        }

        if (ngx_reconfigure()) {
            ngx_reconfigure(0);
            ngx_log_error(NGX_LOG_NOTICE, $cycle->log, 0, "reconfiguring");

            $cycle = ngx_init_cycle($cycle);
            if ($cycle == NULL) {
                $cycle = ngx_cycle();
                continue;
            }

            ngx_cycle($cycle);
        }

        if (ngx_reopen()) {
            ngx_reopen(0);
            ngx_log_error(NGX_LOG_NOTICE, $cycle->log, 0, "reopening logs");
            ngx_reopen_files($cycle,  -1);
        }
    }
}

function ngx_exit_log_file(ngx_open_file_s $file = null){
    static $ngx_exit_log_file = null;
    if(!is_null($file)){
       $ngx_exit_log_file = $file;
    }else{
       return  $ngx_exit_log_file;
    }

}

function ngx_exit_cycle(ngx_cycle_t $cycle = null){

    static  $ngx_exit_cycle = null;
    if(!is_null($ngx_exit_cycle)){
       $ngx_exit_cycle = $cycle;
    }else{
       return $ngx_exit_cycle;
    }

}

function ngx_master_process_exit(ngx_cycle_t $cycle)
{
//ngx_uint_t  i;

    ngx_delete_pidfile($cycle);

    ngx_log_error(NGX_LOG_NOTICE, $cycle->log, 0, "exit");

    for ($i = 0; ngx_modules($i); $i++) {
        if (ngx_modules($i)->exit_master) {
            ngx_modules($i)->exit_master($cycle);
            }
    }

    ngx_close_listening_sockets($cycle);

    /*
     * Copy ngx_cycle->log related data to the special static exit cycle,
     * log, and log file structures enough to allow a signal handler to log.
     * The handler may be called when standard ngx_cycle->log allocated from
     * ngx_cycle->pool is already destroyed.
     */


    $ngx_cycle = ngx_cycle();
    $ngx_exit_log = ngx_log_get_file_log($ngx_cycle->log);

    $ngx_exit_log_file = new ngx_open_file_s();
    $ngx_exit_log_file->fd = $ngx_exit_log->file->fd;
    $ngx_exit_log->file = $ngx_exit_log_file;
    //$ngx_exit_log.next = NULL;
    $ngx_exit_log->writer = NULL;

    $ngx_exit_cycle = new ngx_cycle_t();
    $ngx_exit_cycle->log = $ngx_exit_log;
    $ngx_exit_cycle->files = $ngx_cycle->files;
    $ngx_exit_cycle->files_n =$ngx_cycle->files_n;
    ngx_cycle($ngx_exit_cycle);

    //ngx_destroy_pool($cycle->pool);

    exit(0);
}

