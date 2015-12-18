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

function ngx_noaccepting($i = null){
    static $ngx_noaccepting = null;
    if(!is_null($i)){
        $ngx_noaccepting = $i;
    }else{
        return $ngx_noaccepting;
    }
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

function ngx_cache_manager_process_handler_closure(){

    return function(ngx_event_t $ev){
        ngx_cache_manager_process_handler($ev);
    };
}

function ngx_cache_manager_ctx(){
    static $ngx_cache_manager_ctx = array(
        ngx_cache_manager_process_handler_closure(),
        "cache manager process",
        0);
    return $ngx_cache_manager_ctx;
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

    //todo may have problem
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

        if ($ngx_processes_i->pid == -1) {
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
                    || $ngx_processes_n->pid == -1
                    || $ngx_processes_n->channel[0] == -1)
                    {
                        continue;
                    }

                    ngx_log_debug3(NGX_LOG_DEBUG_CORE, $cycle->log, 0,
                                   "pass close channel s:%i pid:%P to:%P",
                                   $ch->slot, $ch->pid, $ngx_processes_n->pid);

                    /* TODO: NGX_AGAIN */

                    ngx_write_channel($ngx_processes_n->channel[0],
                                      $ch, sizeof(ngx_channel_t), $cycle->log);
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
                                  ngx_rename_file_n ." %s back to %s failed "
                                  "after the new binary process \"%s\" exited",
                                  $ccf->oldpid, $ccf->pid, ngx_argv(0));
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

