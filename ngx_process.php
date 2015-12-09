<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 15-11-5
 * Time: 下午9:56
 */

//int              ngx_argc;
//char           **ngx_argv;
//char           **ngx_os_argv;
//define('ngx_log_pid',ngx_cfg('ngx_pid'));


class ngx_process_t {
/** ngx_pid_t **/ private $pid;
/** int **/ private $status;
/**    ngx_socket_t **/  private      $channel[2];
/** ngx_spawn_proc_pt **/ private $proc;
/** void **/ private $data;
/** char **/ private $name;
/** unsigned **/ private $respawn;
/** unsigned **/ private $just_spawn;
/** unsigned **/ private $detached;
/** unsigned **/ private $exiting;
/** unsigned **/ private $exited;
    public function __set($property, $value){
       $this->$property = $value;
    }
    public function __get($property){
        return $this->$property;
    }
}

function ngx_signal_value($n){
   return ngx_signal_helper($n);
}
function ngx_signal_helper($n){
   return 'SIG##'.$n;
}

function ngx_value($n){
   return ngx_value_helper($n);
}

function ngx_value_helper($n){
   return '#'.$n;
}

//typedef struct {
//    int     signo;
//    char   *signame;
//    char   *name;
//    void  (*handler)(int signo);
//} ngx_signal_t;
function ngx_conf_set_num_slot_closure(){
    return function(ngx_conf_t $cf, ngx_command_t $cmd, $conf){
        ngx_conf_set_num_slot($cf,  $cmd, $conf);
    } ;
}
function ngx_signal_handler_closure(){
    return function($signo){
       ngx_signal_handler($signo);
    };
}
function signals($i = 0){
   static  $signals = array(
         array(
            'signo'=> ngx_signal_value(NGX_RECONFIGURE_SIGNAL),
             'signame'=>"SIG ".ngx_value(NGX_RECONFIGURE_SIGNAL),
             'name'=>'reload',
             'handler'=>ngx_signal_handler_closure(),
         ),
         array(
             'signo'=> ngx_signal_value(NGX_REOPEN_SIGNAL),
             'signame'=>"SIG ".ngx_value(NGX_REOPEN_SIGNAL),
             'name'=>'reopen',
             'handler'=>ngx_signal_handler_closure(),
         ),
         array(
             'signo'=> ngx_signal_value(NGX_NOACCEPT_SIGNAL),
             'signame'=>"SIG ".ngx_value(NGX_NOACCEPT_SIGNAL),
             'name'=>'',
             'handler'=>ngx_signal_handler_closure(),
         ),
         array(
             'signo'=> ngx_signal_value(NGX_TERMINATE_SIGNAL),
             'signame'=>"SIG ".ngx_value(NGX_TERMINATE_SIGNAL),
             'name'=>'stop',
             'handler'=>ngx_signal_handler_closure(),
         ),
         array(
             'signo'=> ngx_signal_value(NGX_SHUTDOWN_SIGNAL),
             'signame'=>"SIG ".ngx_value(NGX_SHUTDOWN_SIGNAL),
             'name'=>'quit',
             'handler'=>ngx_signal_handler_closure(),
         ),
         array(
             'signo'=> ngx_signal_value(NGX_CHANGEBIN_SIGNAL),
             'signame'=>"SIG ".ngx_value(NGX_CHANGEBIN_SIGNAL),
             'name'=>'',
             'handler'=>ngx_signal_handler_closure(),
         ),
         array(
             'signo'=>SIGALRM,
             'signame'=>"SIGALRM",
             'name'=>'',
             'handler'=>ngx_signal_handler_closure(),
         ),
         array(
             'signo'=>SIGINT,
             'signame'=>"SIGINT",
             'name'=>'',
             'handler'=>ngx_signal_handler_closure(),
         ),
         array(
             'signo'=>SIGIO,
             'signame'=>"SIGIO",
             'name'=>'',
             'handler'=>ngx_signal_handler_closure(),
         ),
         array(
             'signo'=>SIGCHLD,
             'signame'=>"SIGCHLD",
             'name'=>'',
             'handler'=>ngx_signal_handler_closure(),
         ),
         array(
             'signo'=>SIGSYS,
             'signame'=>"SIGSYS,SIG_IGN",
             'name'=>'',
             'handler'=>SIG_IGN,
         ),
         array(
             'signo'=>SIGPIPE,
             'signame'=>"SIGPIPE,SIG_IGN",
             'name'=>'',
             'handler'=>SIG_IGN
         ),
         array(
             'signo'=>0,
             'signame'=>NULL,
             'name'=>'',
             'handler'=>NULL
         ),
    );
    return  $signals[$i];
}

function ngx_signal_handler( $signo)
{
//    char            *action;
//    ngx_int_t        ignore;
//    ngx_err_t        err;
//    ngx_signal_t    *sig;

    $ignore = 0;

    //$err = ngx_errno();

    for ($i=0,$sig = signals($i); $sig['signo'] != 0; $i++) {
        if ($sig['signo'] == $signo) {
            break;
        }
    }

    ngx_time_sigsafe_update();

    $action = "";

    switch (ngx_process()) {

        case NGX_PROCESS_MASTER:
        case NGX_PROCESS_SINGLE:
            switch ($signo) {

                case ngx_signal_value(NGX_SHUTDOWN_SIGNAL):
                    ngx_quit(1);
                    $action = ", shutting down";
                    break;

                case ngx_signal_value(NGX_TERMINATE_SIGNAL):
                case SIGINT:
                    ngx_terminate(1);
                    $action = ", exiting";
                    break;

                case ngx_signal_value(NGX_NOACCEPT_SIGNAL):
                    if (ngx_daemonized()) {
                        ngx_noaccept(1);
                        $action = ", stop accepting connections";
                    }
                    break;

                case ngx_signal_value(NGX_RECONFIGURE_SIGNAL):
                    ngx_reconfigure(1);
                    $action = ", reconfiguring";
                    break;

                case ngx_signal_value(NGX_REOPEN_SIGNAL):
                    ngx_reopen(1);
                    $action = ", reopening logs";
                    break;

                case ngx_signal_value(NGX_CHANGEBIN_SIGNAL):
                    if (posix_getppid() > 1 || ngx_new_binary() > 0) {

                        /*
                         * Ignore the signal in the new binary if its parent is
                         * not the init process, i.e. the old binary's process
                         * is still running.  Or ignore the signal in the old binary's
                         * process if the new binary's process is already running.
                         */

                        $action = ", ignoring";
                        $ignore = 1;
                        break;
                    }

                    ngx_change_binary(1);
                    $action = ", changing binary";
                    break;

                case SIGALRM:
                    ngx_sigalrm(1);
                    break;

                case SIGIO:
                    ngx_sigio(1);
                    break;

                case SIGCHLD:
                    ngx_reap(1);
                    break;
            }

            break;

        case NGX_PROCESS_WORKER:
        case NGX_PROCESS_HELPER:
            switch ($signo) {

                case ngx_signal_value(NGX_NOACCEPT_SIGNAL):
                    if (!ngx_daemonized()) {
                        break;
                    }
                    ngx_debug_quit(1);
                case ngx_signal_value(NGX_SHUTDOWN_SIGNAL):
                    ngx_quit(1);
                    $action = ", shutting down";
                    break;

                case ngx_signal_value(NGX_TERMINATE_SIGNAL):
                case SIGINT:
                    ngx_terminate(1);
                    $action = ", exiting";
                    break;

                case ngx_signal_value(NGX_REOPEN_SIGNAL):
                    ngx_reopen(1);
                    $action = ", reopening logs";
                    break;

                case ngx_signal_value(NGX_RECONFIGURE_SIGNAL):
                case ngx_signal_value(NGX_CHANGEBIN_SIGNAL):
                case SIGIO:
                    $action = ", ignoring";
                    break;
            }

            break;
    }

    $ngx_cycle = ngx_cycle();
    ngx_log_error(NGX_LOG_NOTICE, $ngx_cycle->log, 0,
                  "signal %d (%s) received%s", array($signo, $sig->signame, $action));

    if ($ignore) {
        ngx_log_error(NGX_LOG_CRIT, $ngx_cycle->log, 0,
                      "the changing binary signal is ignored: ".
                      "you should shutdown or terminate ".
                      "before either old or new binary's process");
    }

    if ($signo == SIGCHLD) {
        ngx_process_get_status();
    }

    //ngx_set_errno($err);
}

function ngx_getpid(){
   return posix_getpid();
}

function ngx_processes($mix){
    static $ngx_processes = null;
    if($mix instanceof ngx_process_t){
        $ngx_processes[] = $mix;
    }else{
        return $ngx_processes[$mix];
    }

}

function ngx_process_get_status()
{
//int              status;
//    char            *process;
//    ngx_pid_t        pid;
//    ngx_err_t        err;
//    ngx_int_t        i;
//    ngx_uint_t       one;

    $one = 0;

    for ( ;; ) {
        $pid = pcntl_waitpid(-1, $status, WNOHANG);

        if ($pid == 0) {
            return;
        }

        if ($pid == -1) {
            $err = pcntl_get_last_error();

            if ($err == NGX_EINTR) {
                continue;
            }

            if ($err == NGX_ECHILD && $one) {
                return;
            }

            /*
             * Solaris always calls the signal handler for each exited process
             * despite waitpid() may be already called for this process.
             *
             * When several processes exit at the same time FreeBSD may
             * erroneously call the signal handler for exited process
             * despite waitpid() may be already called for this process.
             */

            $ngx_cycle = ngx_cycle();
            if ($err == NGX_ECHILD) {
                ngx_log_error(NGX_LOG_INFO, $ngx_cycle->log, $err,
                              "waitpid() failed");
                return;
            }

            ngx_log_error(NGX_LOG_ALERT, $ngx_cycle->log, $err,
                          "waitpid() failed");
            return;
        }


        $one = 1;
        $process = "unknown process";

        for ($i = 0; $i < ngx_last_process(); $i++) {
            $ngx_process = ngx_processes($i);
            if ($ngx_process->pid == $pid) {
                $ngx_process->status = $status;
                $ngx_process->exited = 1;
                $process = $ngx_process->name;
                break;
            }
        }

        //pcntl_wtermsig
        if (pcntl_wtermsig($status)) {

            $ngx_cycle = ngx_cycle();
            ngx_log_error(NGX_LOG_ALERT, $ngx_cycle->log, 0,
                          "%s %P exited on signal %d",
                          array($process, $pid, pcntl_wtermsig($status)));
        } else {
            ngx_log_error(NGX_LOG_NOTICE, $ngx_cycle->log, 0,
                          "%s %P exited with code %d",
                          array($process, $pid, pcntl_wexitstatus($status)));
        }


          $ngx_process = ngx_processes($i);
        if (pcntl_wexitstatus($status) == 2 && $ngx_process->respawn) {
            ngx_log_error(NGX_LOG_ALERT, $ngx_cycle->log, 0,
                          "%s %P exited with fatal code %d ".
                          "and cannot be respawned",
                          array($process, $pid,pcntl_wexitstatus($status)));
            $ngx_process->respawn = 0;
        }

        //todo why should unlock
        ngx_unlock_mutexes($pid);
    }
}

function ngx_unlock_mutexes($pid)
{
//    ngx_uint_t        i;
//    ngx_shm_zone_t   *shm_zone;
//    ngx_list_part_t  *part;
//    ngx_slab_pool_t  *sp;

    /*
     * unlock the accept mutex if the abnormally exited process
     * held it
     */
//
//    if (ngx_accept_mutex_ptr()) {
//         ngx_shmtx_force_unlock(&ngx_accept_mutex, $pid);
//    }
//
//    /*
//     * unlock shared memory mutexes if held by the abnormally exited
//     * process
//     */
//
//    part = (ngx_list_part_t *) &ngx_cycle->shared_memory.part;
//    shm_zone = part->elts;
//
//    for (i = 0; /* void */ ; i++) {
//
//        if (i >= part->nelts) {
//            if (part->next == NULL) {
//                break;
//            }
//            part = part->next;
//            shm_zone = part->elts;
//            i = 0;
//        }
//
//        sp = (ngx_slab_pool_t *) shm_zone[i].shm.addr;
//
//        if (ngx_shmtx_force_unlock(&sp->mutex, pid)) {
//            ngx_log_error(NGX_LOG_ALERT, ngx_cycle->log, 0,
//                          "shared memory zone \"%V\" was locked by %P",
//                          &shm_zone[i].shm.name, pid);
//        }
//    }
}


function  ngx_last_process($i = null){
    static $ngx_last_process = null;
    if(!is_null($i)){
       $ngx_last_process = $i;
    }else{
       return $ngx_last_process;
    }
}

//function  ngx_processes(){
//   // [NGX_MAX_PROCESSES];
//}

function ngx_os_signal_process(ngx_cycle_t $cycle,  $name,  $pid)
{
//   ngx_signal_t  *sig;
//
//   for (sig = signals; sig->signo != 0; sig++) {
//   if (ngx_strcmp(name, sig->name) == 0) {
//      if (kill(pid, sig->signo) != -1) {
//         return 0;
//      }
//
//            ngx_log_error(NGX_LOG_ALERT, cycle->log, ngx_errno,
//                          "kill(%P, %d) failed", pid, sig->signo);
//        }
//    }
//
//    return 1;
}