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

function master_process(){
   static $master_process = 'master process' ;
    return $master_process;
}

function ngx_restart($i = null){
    static $ngx_restart = null;
    if(!is_null($ngx_restart)){
       $ngx_restart = $i;
    }else{
       return $ngx_restart;
    }
}

function ngx_noaccepting($i = null){
    static $ngx_noaccepting = null;
    if(!is_null($i)){
        $ngx_noaccepting = $i;
    }else{
        return $ngx_noaccepting;
    }
}

function ngx_worker($i = null){
    static $ngx_worker = null;
    if(!is_null($i)){
       $ngx_worker = $i;
    }else{
        return $ngx_worker;
    }
}

function ngx_exiting($i = null){
    static $ngx_exiting = null;
    if(!is_null($i)){
        $ngx_exiting = $i;
    }else{
        return $ngx_exiting;
    }
}

function  ngx_exit_log_file (ngx_open_file_s  $file = null){
    static $ngx_exit_log_file = null;
    if(!is_null($file) && $file instanceof ngx_open_file_s){
       $ngx_exit_log_file = $file;
    }else{
        if(is_null($ngx_exit_log_file)){
           $ngx_exit_log_file = new ngx_open_file_s();
        }
       return $ngx_exit_log_file;
    }
}

function ngx_exit_log(ngx_log $log = null){
    static $ngx_exit_log = null;
    if(!is_null($log)){
       $ngx_exit_log = $log;
    }else{
        if(is_null($ngx_exit_log)){
            $ngx_exit_log = new ngx_log();
        }
        return $ngx_exit_log;
    }
}

function ngx_exit_cycle(ngx_cycle_t $cycle = null){
    static $ngx_exit_cycle = null;
    if(!is_null($cycle)){
        $ngx_exit_cycle = $cycle;
    }else{
        if(is_null($ngx_exit_cycle)){
            $ngx_exit_cycle = new ngx_cycle_t();
        }
        return $ngx_exit_cycle;
    }
}

function  ngx_cache_manager_ctx(){
   static $ngx_cache_manager_ctx = array(
       'ngx_cache_manager_process_handler',
        'cache manager process',
        0
    );
    return $ngx_cache_manager_ctx;
}

function  ngx_cache_loader_ctx(){
    static $ngx_cache_loader_ctx = array(
        'ngx_cache_loader_process_handler',
        'cache manager process',
        0
    );
    return $ngx_cache_loader_ctx;
}

function ngx_cache_manager_process_handler(ngx_event_t $ev)
{
//time_t        next, n;
//    ngx_uint_t    i;
//    ngx_path_t  **path;

    $next = 60 * 60;

    $ngx_cycle = ngx_cycle();
    $path = $ngx_cycle->paths;
    for ($i = 0; $i < count($ngx_cycle->paths); $i++) {

        if ($path[$i]->manager) {
            $n = $path[$i]->manager($path[$i]->data);

                $next = ($n <= $next) ? $n : $next;

                ngx_time_update();
            }
    }

    if ($next == 0) {
        $next = 1;
    }

    ngx_add_timer($ev, $next * 1000);
}



function ngx_cache_loader_process_handler(ngx_event_t $ev)
{
//ngx_uint_t     i;
//    ngx_path_t   **path;
//    ngx_cycle_t   *cycle;

    $cycle = ngx_cycle();

    $path = $cycle->paths;
    for ($i = 0; $i < count($cycle->paths); $i++) {
        if (ngx_terminate() || ngx_quit()) {
            break;
        }

    if ($path[$i]->loader) {
        $path[$i]->loader($path[$i]->data);
            ngx_time_update();
        }
    }
    exit(0);
}

function ngx_cache_loader_process_handler_closure(){

    return function(ngx_event_t $ev){
        ngx_cache_loader_process_handler($ev);
    };
}


function ngx_cache_loader_ctx(){
    static $ngx_cache_loader_ctx = array(
        ngx_cache_loader_process_handler_closure(),
        "cache loader process",
        60000);
    return $ngx_cache_loader_ctx;
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

function ngx_master_process_cycle(ngx_cycle_t $cycle)
{
//char              *title;
//    u_char            *p;
//    size_t             size;
//    ngx_int_t          i;
//    ngx_uint_t         n, sigio;
//    sigset_t           set;
//    struct itimerval   itv;
//    ngx_uint_t         live;
//    ngx_msec_t         delay;
//    ngx_listening_t   *ls;
//    ngx_core_conf_t   *ccf;
//
    $set = array(
        SIGCHLD,
        SIGALRM,
        SIGIO,
        SIGINT,
        ngx_signal_value(NGX_RECONFIGURE_SIGNAL),
        ngx_signal_value(NGX_REOPEN_SIGNAL),
        ngx_signal_value(NGX_NOACCEPT_SIGNAL),
        ngx_signal_value(NGX_TERMINATE_SIGNAL),
        ngx_signal_value(NGX_SHUTDOWN_SIGNAL),
        ngx_signal_value(NGX_CHANGEBIN_SIGNAL)
    );

    if (pcntl_sigprocmask(SIG_BLOCK, $set) == false) {
        ngx_log_error(NGX_LOG_ALERT, $cycle->log, pcntl_get_last_error(),
            "sigprocmask() failed");
    }

    //sigemptyset(&set);


    $master_process = master_process();
    $size = strlen($master_process);

    for ($i = 0; $i < ngx_argc(); $i++) {
        $size += ngx_strlen(ngx_argv($i)) + 1;
    }

//    title = ngx_pnalloc(cycle->pool, size);
//    if (title == NULL) {
//        /* fatal */
//        exit(2);
//    }

    //p = ngx_cpymem(title, master_process, sizeof(master_process) - 1);
    $title = $master_process;
    for ($i = 0; $i < ngx_argc(); $i++) {
        $title .= ' ';
        $title .= ngx_argv($i);
    }

    //todo self idea may have problem
    ngx_setproctitle($title);


    $ccf = ngx_get_conf($cycle->conf_ctx, ngx_core_module());

    ngx_start_worker_processes($cycle, $ccf->worker_processes,
        NGX_PROCESS_RESPAWN);
    ngx_start_cache_manager_processes($cycle, 0);

    ngx_new_binary(0);
    $delay = 0;
    $sigio = 0;
    $live = 1;

    for (; ;) {
        if ($delay) {
            if (ngx_sigalrm()) {
                $sigio = 0;
                $delay *= 2;
                ngx_sigalrm(0);
            }

            ngx_log_debug1(NGX_LOG_DEBUG_EVENT, $cycle->log, 0,
                "termination cycle: %d", $delay);

            //todo delay do timer
//            itv.it_interval.tv_sec = 0;
//            itv.it_interval.tv_usec = 0;
//            itv.it_value.tv_sec = delay / 1000;
//            itv.it_value.tv_usec = (delay % 1000 ) * 1000;
//
//            if (setitimer(ITIMER_REAL, &itv, NULL) == -1) {
//                ngx_log_error(NGX_LOG_ALERT, cycle->log, ngx_errno,
//                              "setitimer() failed");
//            }
        }

        ngx_log_debug0(NGX_LOG_DEBUG_EVENT, $cycle->log, 0, "sigsuspend");

        //todo php have a function pcntl_sigwaitinfo() but i have no ideas of their difference
//        sigsuspend(&set);
        ngx_time_update();

        ngx_log_debug1(NGX_LOG_DEBUG_EVENT, $cycle->log, 0,
            "wake up, sigio %i", $sigio);

        if (ngx_reap()) {
            ngx_reap(0);
            ngx_log_debug0(NGX_LOG_DEBUG_EVENT, $cycle->log, 0, "reap children");

            $live = ngx_reap_children($cycle);
        }

        if (!$live && (ngx_terminate() || ngx_quit())) {
            ngx_master_process_exit($cycle);
        }

        if (ngx_terminate()) {
            if ($delay == 0) {
                $delay = 50;
            }

            if ($sigio) {
                $sigio--;
                continue;
            }

            $sigio = $ccf->worker_processes + 2 /* cache processes */
            ;

            if ($delay > 1000) {
                ngx_signal_worker_processes($cycle, SIGKILL);
            } else {
                ngx_signal_worker_processes($cycle,
                    ngx_signal_value(NGX_TERMINATE_SIGNAL));
            }
            continue;
        }
//
//        if (ngx_quit) {
//            ngx_signal_worker_processes(cycle,
//                ngx_signal_value(NGX_SHUTDOWN_SIGNAL));
//
//            ls = cycle->listening.elts;
//            for (n = 0; n < cycle->listening.nelts; n++) {
//                if (ngx_close_socket(ls[n].fd) == -1) {
//                    ngx_log_error(NGX_LOG_EMERG, cycle->log, ngx_socket_errno,
//                                  ngx_close_socket_n " %V failed",
//                                  &ls[n].addr_text);
//                }
//            }
//            cycle->listening.nelts = 0;
//
//            continue;
//        }
//
//        if (ngx_reconfigure) {
//            ngx_reconfigure = 0;
//
//            if (ngx_new_binary) {
//                ngx_start_worker_processes(cycle, ccf->worker_processes,
//                                           NGX_PROCESS_RESPAWN);
//                ngx_start_cache_manager_processes(cycle, 0);
//                ngx_noaccepting = 0;
//
//                continue;
//            }
//
//            ngx_log_error(NGX_LOG_NOTICE, cycle->log, 0, "reconfiguring");
//
//            cycle = ngx_init_cycle(cycle);
//            if (cycle == NULL) {
//                cycle = (ngx_cycle_t *) ngx_cycle;
//                continue;
//            }
//
//            ngx_cycle = cycle;
//            ccf = (ngx_core_conf_t *) ngx_get_conf(cycle->conf_ctx,
//                                                   ngx_core_module);
//            ngx_start_worker_processes(cycle, ccf->worker_processes,
//                                       NGX_PROCESS_JUST_RESPAWN);
//            ngx_start_cache_manager_processes(cycle, 1);
//
//            /* allow new processes to start */
//            ngx_msleep(100);
//
//            live = 1;
//            ngx_signal_worker_processes(cycle,
//                ngx_signal_value(NGX_SHUTDOWN_SIGNAL));
//        }
//
//        if (ngx_restart) {
//            ngx_restart = 0;
//            ngx_start_worker_processes(cycle, ccf->worker_processes,
//                                       NGX_PROCESS_RESPAWN);
//            ngx_start_cache_manager_processes(cycle, 0);
//            live = 1;
//        }
//
//        if (ngx_reopen) {
//            ngx_reopen = 0;
//            ngx_log_error(NGX_LOG_NOTICE, cycle->log, 0, "reopening logs");
//            ngx_reopen_files(cycle, ccf->user);
//            ngx_signal_worker_processes(cycle,
//                ngx_signal_value(NGX_REOPEN_SIGNAL));
//        }
//
//        if (ngx_change_binary) {
//            ngx_change_binary = 0;
//            ngx_log_error(NGX_LOG_NOTICE, cycle->log, 0, "changing binary");
//            ngx_new_binary = ngx_exec_new_binary(cycle, ngx_argv);
//        }
//
//        if (ngx_noaccept) {
//            ngx_noaccept = 0;
//            ngx_noaccepting = 1;
//            ngx_signal_worker_processes(cycle,
//                ngx_signal_value(NGX_SHUTDOWN_SIGNAL));
//        }
//    }
    }


function ngx_signal_worker_processes(ngx_cycle_t $cycle, $signo)
{
//    ngx_int_t      i;
//    ngx_err_t      err;
//    ngx_channel_t  ch;

    $ch = new ngx_channel_t();
//    ngx_memzero(&ch, sizeof(ngx_channel_t));

//#if (NGX_BROKEN_SCM_RIGHTS)
//
//    ch.command = 0;
//
//#else

    switch ($signo) {

        case ngx_signal_value(NGX_SHUTDOWN_SIGNAL):
            $ch->command = NGX_CMD_QUIT;
            break;

        case ngx_signal_value(NGX_TERMINATE_SIGNAL):
            $ch->command = NGX_CMD_TERMINATE;
            break;

        case ngx_signal_value(NGX_REOPEN_SIGNAL):
            $ch->command = NGX_CMD_REOPEN;
            break;

        default:
            $ch->command = 0;
    }

//#endif

    $ch->fd = false;


    $ngx_last_process = ngx_last_process();
    for ($i = 0; $i < $ngx_last_process; $i++) {

        $ngx_processes_i = ngx_processes($i);
        ngx_log_debug7(NGX_LOG_DEBUG_EVENT, $cycle->log, 0,
                       "child: %d %P e:%d t:%d d:%d r:%d j:%d",
                       $i,
                       $ngx_processes_i->pid,
                       $ngx_processes_i->exiting,
                       $ngx_processes_i->exited,
                       $ngx_processes_i->detached,
                       $ngx_processes_i->respawn,
                       $ngx_processes_i->just_spawn);

        if ($ngx_processes_i->detached || $ngx_processes_i->pid == null) {
            continue;
        }

        if ($ngx_processes_i->just_spawn) {
            $ngx_processes_i->just_spawn = 0;
            continue;
        }

        if ($ngx_processes_i->exiting
        && $signo == ngx_signal_value(NGX_SHUTDOWN_SIGNAL))
        {
            continue;
        }

        if ($ch->command) {
            if (ngx_write_channel($ngx_processes_i->channel[0],
                                  $ch,  $cycle->log)
                == NGX_OK)
            {
                if ($signo != ngx_signal_value(NGX_REOPEN_SIGNAL)) {
                    $ngx_processes_i->exiting = 1;
                }

                continue;
            }
        }

        ngx_log_debug2(NGX_LOG_DEBUG_CORE, $cycle->log, 0,
                       "kill (%P, %d)", $ngx_processes_i->pid, $signo);

        if (posix_kill($ngx_processes_i->pid, $signo) == false) {
             $err = posix_get_last_error();
            ngx_log_error(NGX_LOG_ALERT, $cycle->log, $err,
                          "kill(%P, %d) failed", array($ngx_processes_i->pid, $signo));

            if ($err == NGX_ESRCH) {
                $ngx_processes_i->exited = 1;
                $ngx_processes_i->exiting = 0;
                ngx_reap(1);
            }

            continue;
        }

        if ($signo != ngx_signal_value(NGX_REOPEN_SIGNAL)) {
            $ngx_processes_i->exiting = 1;
        }
    }
}

    function ngx_exit_log_file(ngx_open_file_s $file = null)
    {
        static $ngx_exit_log_file = null;
        if (!is_null($file)) {
            $ngx_exit_log_file = $file;
        } else {
            return $ngx_exit_log_file;
        }

    }

    function ngx_exit_cycle(ngx_cycle_t $cycle = null)
    {

        static $ngx_exit_cycle = null;
        if (!is_null($ngx_exit_cycle)) {
            $ngx_exit_cycle = $cycle;
        } else {
            return $ngx_exit_cycle;
        }

    }

    /**
     * @param ngx_cycle_t $cycle
     */
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
        $ngx_exit_cycle->files_n = $ngx_cycle->files_n;
        ngx_cycle($ngx_exit_cycle);

        //ngx_destroy_pool($cycle->pool);

        exit(0);
    }

function ngx_reap_children(ngx_cycle_t $cycle)
{
//ngx_int_t         i, n;
//    ngx_uint_t        live;
//    ngx_channel_t     ch;
//    ngx_core_conf_t  *ccf;


    $ch = new ngx_channel_t();
    //ngx_memzero(&ch, sizeof(ngx_channel_t));

    $ch->command = NGX_CMD_CLOSE_CHANNEL;
    //$ch->fd = null;

    $live = 0;
    for ($i = 0; $i < ngx_last_process(); $i++) {

        $ngx_processes_i = ngx_processes($i);
        ngx_log_debug7(NGX_LOG_DEBUG_EVENT, $cycle->log, 0,
                       "child: %d %P e:%d t:%d d:%d r:%d j:%d",
                       $i,
                       $ngx_processes_i->pid,
                       $ngx_processes_i->exiting,
                       $ngx_processes_i->exited,
                       $ngx_processes_i->detached,
                       $ngx_processes_i->respawn,
                       $ngx_processes_i->just_spawn);

        if ($ngx_processes_i->pid == null) {
            continue;
        }

        if ($ngx_processes_i->exited) {

            if (!$ngx_processes_i->detached) {
                ngx_close_channel($ngx_processes_i->channel, $cycle->log);

                $ngx_processes_i->channel[0] = null;
                $ngx_processes_i->channel[1] = null;

                $ch->pid = $ngx_processes_i->pid;
                $ch->slot = $i;

                for ($n = 0; $n < ngx_last_process(); $n++) {
                    $ngx_processes_n = ngx_processes($n);
                    if ($ngx_processes_n->exited
                    || $ngx_processes_n->pid == null
                    || $ngx_processes_n->channel[0] == null)
                    {
                        continue;
                    }

                    ngx_log_debug3(NGX_LOG_DEBUG_CORE, $cycle->log, 0,
                                   "pass close channel s:%i pid:%P to:%P",
                                   $ch->slot, $ch->pid, $ngx_processes_n->pid);


                    ngx_write_channel($ngx_processes_n->channel[0],  $ch,  $cycle->log);
                }
            }

            if ($ngx_processes_i->respawn
            && !$ngx_processes_i->exiting
            && !ngx_terminate()
            && !ngx_quit())
            {
                if (ngx_spawn_process($cycle, $ngx_processes_i->proc,
                                      $ngx_processes_i->data,
                                      $ngx_processes_i->name, $i)
                    == NGX_INVALID_PID)
                {
                    ngx_log_error(NGX_LOG_ALERT, $cycle->log, 0,
                                  "could not respawn %s",
                                  $ngx_processes_i->name);
                    continue;
                }


                $ngx_processes_s =  ngx_processes(ngx_process_slot());
                $ch->command = NGX_CMD_OPEN_CHANNEL;
                $ch->pid = $ngx_processes_s->pid;
                $ch->slot = ngx_process_slot();
                $ch->fd = $ngx_processes_s->channel[0];

                ngx_pass_open_channel($cycle, $ch);

                $live = 1;

                continue;
            }

            if ($ngx_processes_i->pid == ngx_new_binary()) {

                $ccf = ngx_get_conf($cycle->conf_ctx, ngx_core_module());

                if (ngx_rename_file($ccf->oldpid,
                                    $ccf->pid)
                    == NGX_FILE_ERROR)
                {
                    ngx_log_error(NGX_LOG_ALERT, $cycle->log, NGX_FRNERROR,
                                  ngx_rename_file_n ." %s back to %s failed ".
                                  "after the new binary process \"%s\" exited",
                                  array($ccf->oldpid, $ccf->pid, ngx_argv(0)));
                }
                ngx_new_binary(0);
                if (ngx_noaccepting()) {
                    ngx_restart(1);
                    ngx_noaccepting(0);
                }
            }

            if ($i == ngx_last_process() - 1) {
                ngx_last_process($i);

            } else {
                $ngx_processes_i->pid = null;
            }

        } else if ($ngx_processes_i->exiting || !$ngx_processes_i->detached) {
            $live = 1;
        }
    }

    return $live;
}

function ngx_pass_open_channel(ngx_cycle_t $cycle, ngx_channel_t $ch)
{
//ngx_int_t  i;
    $ngx_last_process = ngx_last_process();
    $ngx_process_slot = ngx_process_slot();

    for ($i = 0; $i < $ngx_last_process; $i++) {
        $ngx_processes_i = ngx_processes($i);
        if ($i == $ngx_process_slot
            || $ngx_processes_i->pid == null
        || $ngx_processes_i->channel[0] == null)
        {
            continue;
        }

        ngx_log_debug6(NGX_LOG_DEBUG_CORE, $cycle->log, 0,
                      "pass channel s:%d pid:%P fd:%d to s:%i pid:%P fd:%d",
                      $ch->slot, $ch->pid, $ch->fd,
                      $i, $ngx_processes_i->pid,
                      $ngx_processes_i->channel[0]);

        /* TODO: NGX_AGAIN */

        ngx_write_channel($ngx_processes_i->channel[0],
                          $ch, $cycle->log);
    }
}

function ngx_start_worker_processes(ngx_cycle_t $cycle,  $n,  $type)
{
//    ngx_int_t      i;
//    ngx_channel_t  ch;

    ngx_log_error(NGX_LOG_NOTICE, $cycle->log, 0, "start worker processes");

    $ch = new ngx_channel_t();
    //ngx_memzero(&ch, sizeof(ngx_channel_t));

    $ch->command = NGX_CMD_OPEN_CHANNEL;

    for ($i = 0; $i < $n; $i++) {

        $ngx_process_slot = ngx_process_slot();
        $ngx_processes_slot = ngx_processes($ngx_process_slot);
        ngx_spawn_process($cycle, 'ngx_worker_process_cycle',
            $i, "worker process", $type);

        $ch->pid = $ngx_processes_slot->pid;
        $ch->slot = $ngx_process_slot;
        $ch->fd = $ngx_processes_slot->channel[0];

        ngx_pass_open_channel($cycle, $ch);
    }
}

function ngx_worker_process_cycle(ngx_cycle_t $cycle, $data)
{
//ngx_int_t worker = (intptr_t) data;

    $worker = $data;
    ngx_process(NGX_PROCESS_WORKER);
    ngx_worker($worker);

    ngx_worker_process_init($cycle, $worker);

    ngx_setproctitle("worker process");

    for ( ;; ) {

        if (ngx_exiting()) {
//todo should complete event method
            ngx_event_cancel_timers();

//todo should complete event method
//            if (ngx_event_timer_rbtree.root == ngx_event_timer_rbtree.sentinel)
//            {
//                ngx_log_error(NGX_LOG_NOTICE, cycle->log, 0, "exiting");
//
//                ngx_worker_process_exit($cycle);
//            }
        }

        ngx_log_debug0(NGX_LOG_DEBUG_EVENT, $cycle->log, 0, "worker cycle");

        ngx_process_events_and_timers($cycle);

        if (ngx_terminate()) {
            ngx_log_error(NGX_LOG_NOTICE, $cycle->log, 0, "exiting");

            ngx_worker_process_exit($cycle);
        }

        if (ngx_quit()) {
            ngx_quit(0);
            ngx_log_error(NGX_LOG_NOTICE, $cycle->log, 0,
                          "gracefully shutting down");
            ngx_setproctitle("worker process is shutting down");

            if (!ngx_exiting()) {
                ngx_exiting(1);
                ngx_close_listening_sockets($cycle);
                ngx_close_idle_connections($cycle);
            }
        }

        if (ngx_reopen()) {
            ngx_reopen(0);
            ngx_log_error(NGX_LOG_NOTICE, $cycle->log, 0, "reopening logs");
            ngx_reopen_files($cycle, -1);
        }
    }
}

function ngx_close_idle_connections(ngx_cycle_t $cycle)
{
//ngx_uint_t         i;
//    ngx_connection_t  *c;

    $c = $cycle->connections;

    for ($i = 0; $i < $cycle->connection_n; $i++) {

        if ($c[$i]->fd != null && $c[$i]->idle) {
                $c[$i]->close = 1;
              call_user_func($c[$i]->read->handler,$c[$i]->read);
            }
    }
}

function ngx_worker_process_init(ngx_cycle_t $cycle,  $worker)
{
//    sigset_t          set;
//    uint64_t          cpu_affinity;
//    ngx_int_t         n;
//    ngx_uint_t        i;
//    struct rlimit     rlmt;
//    ngx_core_conf_t  *ccf;
//    ngx_listening_t  *ls;

    if (ngx_set_environment($cycle, NULL) == NULL) {
        /* fatal */
        exit(2);
    }

    $ccf =  ngx_get_conf($cycle->conf_ctx, ngx_core_module());

    if ($worker >= 0 && $ccf->priority != 0) {
    if (pcntl_setpriority(PRIO_PROCESS, 0, $ccf->priority) == false) {
        ngx_log_error(NGX_LOG_ALERT, $cycle->log, pcntl_get_last_error(),
                          "setpriority(%d) failed", $ccf->priority);
        }
    }

    if ($ccf->rlimit_nofile != NGX_CONF_UNSET) {

        //todo php7's new constant may have bugs
        if (posix_setrlimit(POSIX_RLIMIT_NOFILE , $ccf->rlimit_nofile, $ccf->rlimit_nofile) == false) {
            ngx_log_error(NGX_LOG_ALERT, $cycle->log, posix_get_last_error(),
                          "setrlimit(RLIMIT_NOFILE, %i) failed",
                          $ccf->rlimit_nofile);
        }
    }

    if ($ccf->rlimit_core != NGX_CONF_UNSET) {

        if (posix_setrlimit(POSIX_RLIMIT_CORE, $ccf->rlimit_nofile, $ccf->rlimit_nofile) == false) {
            ngx_log_error(NGX_LOG_ALERT, $cycle->log, posix_get_last_error(),
                          "setrlimit(RLIMIT_CORE, %O) failed",
                          $ccf->rlimit_core);
        }
    }

    if (posix_geteuid() == 0) {
        if (posix_setgid($ccf->group) == false) {
            ngx_log_error(NGX_LOG_EMERG, $cycle->log, posix_get_last_error(),
                          "setgid(%d) failed", $ccf->group);
            /* fatal */
            exit(2);
        }

        if (posix_initgroups($ccf->username, $ccf->group) == false) {
            ngx_log_error(NGX_LOG_EMERG, $cycle->log, posix_get_last_error(),
                          "initgroups(%s, %d) failed",
                          array($ccf->username, $ccf->group));
        }

        if (posix_setuid($ccf->user) == false) {
            ngx_log_error(NGX_LOG_EMERG, $cycle->log, posix_get_last_error(),
                          "setuid(%d) failed", $ccf->user);
            /* fatal */
            exit(2);
        }
    }

    //todo php don't have a cpu function
//    if ($worker >= 0) {
//        $cpu_affinity = ngx_get_cpu_affinity($worker);
//
//        if ($cpu_affinity) {
//            ngx_setaffinity($cpu_affinity, $cycle->log);
//        }
//    }

    //todo php don't have a function to work
//#if (NGX_HAVE_PR_SET_DUMPABLE)
//
//    /* allow coredump after setuid() in Linux 2.4.x */
//
//    if (posix_prctl(PR_SET_DUMPABLE, 1, 0, 0, 0) == -1) {
//        ngx_log_error(NGX_LOG_ALERT, cycle->log, ngx_errno,
//                      "prctl(PR_SET_DUMPABLE) failed");
//    }
//
//#endif

    if (!empty($ccf->working_directory)) {
        if (chdir( $ccf->working_directory) == false) {
            ngx_log_error(NGX_LOG_ALERT, $cycle->log, NGX_FCNERROR,
                          "chdir(\"%s\") failed", $ccf->working_directory);
            /* fatal */
            exit(2);
        }
    }

    //sigemptyset(&set);
    $set = array();
    if (pcntl_sigprocmask(SIG_SETMASK, $set) == false) {
        ngx_log_error(NGX_LOG_ALERT, $cycle->log, pcntl_get_last_error(),
                      "sigprocmask() failed");
    }

    //srandom((ngx_pid() << 16) ^ ngx_time());

    /*
     * disable deleting previous events for the listening sockets because
     * in the worker processes there are no events at all at this point
     */
    //ls = cycle->listening.elts;
    $ls= $cycle->listening;
    for ($i = 0; $i < count($cycle->listening); $i++) {
        $ls[$i]->previous = NULL;
    }

    for ($i = 0; $ngx_modules_i = ngx_modules($i); $i++) {
        if ($ngx_modules_i->init_process) {
            if ($ngx_modules_i->init_process($cycle) == NGX_ERROR) {
                /* fatal */
                exit(2);
            }
        }
    }

    for ($n = 0; $n < ngx_last_process(); $n++) {
        $ngx_processes_n = ngx_processes($n);
        if ($ngx_processes_n->pid == null) {
            continue;
        }

        if ($n == ngx_process_slot()) {
            continue;
        }

        if ($ngx_processes_n->channel[1] == false) {
            continue;
        }

        if (fclose($ngx_processes_n->channel[1]) == false) {
            ngx_log_error(NGX_LOG_ALERT, $cycle->log, NGX_FCERROR,
                          "close() channel failed");
        }
    }

    $ngx_processes_slot = ngx_processes(ngx_process_slot());
    if (fclose($ngx_processes_slot->channel[0]) == false) {
        ngx_log_error(NGX_LOG_ALERT, $cycle->log, NGX_FCERROR,
                      "close() channel failed");
    }


    if (ngx_add_channel_event($cycle, ngx_channel(), NGX_READ_EVENT,
            'ngx_channel_handler')
        == NGX_ERROR)
    {
        /* fatal */
        exit(2);
    }
}

function ngx_channel_handler(ngx_event_t $ev)
{
//ngx_int_t          n;
//    ngx_channel_t      ch;
//    ngx_connection_t  *c;
    $ch = new ngx_channel_t();

    if ($ev->timedout) {
        $ev->timedout = 0;
        return;
        }

    $c = $ev->data;

    ngx_log_debug0(NGX_LOG_DEBUG_CORE, $ev->log, 0, "channel handler");

    for ( ;; ) {

        $n = ngx_read_channel($c->fd, $ch,  $ev->log);

        ngx_log_debug1(NGX_LOG_DEBUG_CORE, $ev->log, 0, "channel: %i", $n);

        if ($n == NGX_ERROR) {

            if (ngx_event_flags() & NGX_USE_EPOLL_EVENT) {
                ngx_del_conn($c, 0);
            }

            ngx_close_connection($c);
            return;
        }

        if (ngx_event_flags() & NGX_USE_EVENTPORT_EVENT) {
            if (ngx_add_event($ev, NGX_READ_EVENT, 0) == NGX_ERROR) {
                return;
            }
        }

        if ($n == NGX_AGAIN) {
            return;
        }

        ngx_log_debug1(NGX_LOG_DEBUG_CORE, $ev->log, 0,
                       "channel command: %d", $ch->command);

        switch ($ch->command) {

            case NGX_CMD_QUIT:
                ngx_quit(1);
                break;

            case NGX_CMD_TERMINATE:
                ngx_terminate(1);
                break;

            case NGX_CMD_REOPEN:
                ngx_reopen(1);
                break;

            case NGX_CMD_OPEN_CHANNEL:

                ngx_log_debug3(NGX_LOG_DEBUG_CORE, $ev->log, 0,
                           "get channel s:%i pid:%P fd:%d",
                           $ch->slot, $ch->pid, $ch->fd);

            $ngx_processes_slot = ngx_processes($ch->slot);
            $ngx_processes_slot->pid = $ch->pid;
            $ngx_processes_slot->channel[0] = $ch->fd;
            break;

            case NGX_CMD_CLOSE_CHANNEL:

                ngx_log_debug4(NGX_LOG_DEBUG_CORE, $ev->log, 0,
                           "close channel s:%i pid:%P our:%P fd:%d",
                           $ch->slot, $ch->pid, $ngx_processes_slot->pid,
                           $ngx_processes_slot->channel[0]);

            if (fclose($ngx_processes_slot->channel[0]) == false) {
                ngx_log_error(NGX_LOG_ALERT, $ev->log, NGX_FCERROR,
                              "close() channel failed");
            }

            $ngx_processes_slot->channel[0] = null;
            break;
        }
    }
}

function ngx_worker_process_exit(ngx_cycle_t $cycle)
{
//ngx_uint_t         i;
//    ngx_connection_t  *c;


    for ($i = 0; ngx_modules($i); $i++) {
        if (ngx_modules($i)->exit_process) {
            ngx_modules($i)->exit_process($cycle);
            }
    }

    if (ngx_exiting()) {
        $c = $cycle->connections;
        for ($i = 0; $i < $cycle->connection_n; $i++) {
            if ($c[$i]->fd != -1
            && $c[$i]->read
            && !$c[$i]->read->accept
            && !$c[$i]->read->channel
            && !$c[$i]->read->resolver)
            {
                ngx_log_error(NGX_LOG_ALERT, $cycle->log, 0,
                              "*%uA open socket #%d left in connection %ui",
                              array($c[$i]->number, $c[$i]->fd, $i));
                ngx_debug_quit(1);
            }
        }
    }

    /*
     * Copy ngx_cycle->log related data to the special static exit cycle,
     * log, and log file structures enough to allow a signal handler to log.
     * The handler may be called when standard ngx_cycle->log allocated from
     * ngx_cycle->pool is already destroyed.
     */

    $ngx_cycle = ngx_cycle();
    //$ngx_exit_log = ngx_log_get_file_log($ngx_cycle->log);

    $ngx_exit_log = ngx_exit_log();
    $ngx_exit_log_file = ngx_exit_log_file();
    $ngx_exit_log_file->fd = $ngx_exit_log->file->fd;
    $ngx_exit_log->file = $ngx_exit_log_file;

    $ngx_exit_cycle = ngx_exit_cycle();
    $ngx_exit_cycle->log = $ngx_exit_log;
    $ngx_exit_cycle->files = $ngx_cycle->files;
    $ngx_exit_cycle->files_n = $ngx_cycle->files_n;
    $ngx_cycle = $ngx_exit_cycle;

    ngx_log_error(NGX_LOG_NOTICE, $ngx_cycle->log, 0, "exit");

    exit(0);
}


function ngx_start_cache_manager_processes(ngx_cycle_t $cycle,  $respawn)
{
//    ngx_uint_t       i, manager, loader;
//    ngx_path_t     **path;
//    ngx_channel_t    ch;
//
    $manager = 0;
    $loader = 0;
    $ngx_cycle  = ngx_cycle();

    $path = $ngx_cycle->paths;
    for ($i = 0; $i < count($path); $i++) {

        if ($path[$i]->manager) {
            $manager = 1;
        }

        if ($path[$i]->loader) {
            $loader = 1;
        }
    }

    if ($manager == 0) {
        return;
    }

    ngx_spawn_process($cycle, 'ngx_cache_manager_process_cycle',
        ngx_cache_manager_ctx(), "cache manager process",
        $respawn ? NGX_PROCESS_JUST_RESPAWN : NGX_PROCESS_RESPAWN);

    //ngx_memzero(&ch, sizeof(ngx_channel_t));
    $ch = new ngx_channel_t();

    $ngx_processes_slot = ngx_processes(ngx_process_slot());
    $ch->command = NGX_CMD_OPEN_CHANNEL;
    $ch->pid = $ngx_processes_slot->pid;
    $ch->slot = ngx_process_slot();
    $ch->fd = $ngx_processes_slot->channel[0];

    ngx_pass_open_channel($cycle, $ch);

    if ($loader == 0) {
        return;
    }

    ngx_spawn_process($cycle, 'ngx_cache_manager_process_cycle',
        ngx_cache_loader_ctx(), "cache loader process",
        $respawn ? NGX_PROCESS_JUST_SPAWN : NGX_PROCESS_NORESPAWN);

    $ngx_processes_slot = ngx_processes(ngx_process_slot());
    $ch->command = NGX_CMD_OPEN_CHANNEL;
    $ch->pid = $ngx_processes_slot->pid;
    $ch->slot = ngx_process_slot();
    $ch->fd = $ngx_processes_slot->channel[0];
    ngx_pass_open_channel($cycle, $ch);
}

function ngx_cache_manager_process_cycle(ngx_cycle_t $cycle, $data)
{
//ngx_cache_manager_ctx_t *ctx = data;
//
//    void         *ident[4];
//    ngx_event_t   ev;

    /*
     * Set correct process type since closing listening Unix domain socket
     * in a master process also removes the Unix domain socket file.
     */
    ngx_process(NGX_PROCESS_HELPER);

    ngx_close_listening_sockets($cycle);

    /* Set a moderate number of connections for a helper process. */
    $cycle->connection_n = 512;

    ngx_worker_process_init($cycle, -1);

    //ngx_memzero(&ev, sizeof(ngx_event_t));
    $ev = new ngx_event_t();
    $ev->handler = $ctx->handler;
    $ident[3] =  -1;
    $ev->data = $ident;
    $ev->log = $cycle->log;

    ngx_use_accept_mutex(0);

    ngx_setproctitle($ctx->name);

    ngx_add_timer($ev, $ctx->delay);

    for ( ;; ) {

        if (ngx_terminate() || ngx_quit()) {
            ngx_log_error(NGX_LOG_NOTICE, $cycle->log, 0, "exiting");
            exit(0);
        }

        if (ngx_reopen()) {
            ngx_reopen(0);
            ngx_log_error(NGX_LOG_NOTICE, $cycle->log, 0, "reopening logs");
            ngx_reopen_files($cycle, -1);
        }
        ngx_process_events_and_timers($cycle);
    }
}



