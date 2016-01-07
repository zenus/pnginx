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

define('NGX_PROCESS_NORESPAWN' ,    -1);
define('NGX_PROCESS_JUST_SPAWN',    -2);
define('NGX_PROCESS_RESPAWN',       -3);
define('NGX_PROCESS_JUST_RESPAWN',  -4);
define('NGX_PROCESS_DETACHED',      -5);
define('NGX_INVALID_PID',  -1);
define('NGX_MAX_PROCESSES',         1024);


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

class ngx_exec_ctx_t {
  private  /*** char  **/       $path;
  private  /** char  **/       $name;
  private /** char *const ***/ $argv;
  private /** char *const ***/ $envp;
    public function __set($name,$value){
       $this->$name = $value;
    }
    public function __get($name){
       return $this->name;
    }
}

function ngx_signal_value($n){
   return ngx_signal_helper($n);
}
function ngx_signal_helper($n){
   return 'SIG##'.$n;
}

function ngx_argc($i = null){
    static $ngx_argc = null;
    if(!is_null($ngx_argc)){
       $ngx_argc = $i;
    }else{
       return $ngx_argc;
    }
}

function ngx_os_argv($i,$v=null){
    static $ngx_os_argv = null;
    if(is_null($v)){
       return $ngx_os_argv[$i];
    }else{
       $ngx_os_argv[$i] = $v;
    }
}

function ngx_argv($mixed = null) {
    static $ngx_argv = null;
    if(is_array($mixed)){
        $ngx_argv = $mixed;
    }elseif(!is_null($mixed)){
        return $ngx_argv[$mixed];
    }else{
        return $ngx_argv;
    }
}

function ngx_channel($i = null){
    static $ngx_channel = null;
    if(!is_null($i)){
        $ngx_channel = $i;
    }else{
        return $ngx_channel;
    }

}




function ngx_value($n){
   return ngx_value_helper($n);
}

function ngx_value_helper($n){
   return '#'.$n;
}

function ngx_process_slot($i = null){
    static $ngx_process_slot = null;
    if(!is_null($i)){
       $ngx_process_slot = $i;
    }else{
       return $ngx_process_slot;
    }

}

//typedef struct {
//    int     signo;
//    char   *signame;
//    char   *name;
//    void  (*handler)(int signo);
//} ngx_signal_t;
//function ngx_conf_set_num_slot_closure(){
//    return function(ngx_conf_t $cf, ngx_command_t $cmd, $conf){
//        ngx_conf_set_num_slot($cf,  $cmd, $conf);
//    } ;
//}

//typedef void (*ngx_spawn_proc_pt) (ngx_cycle_t *cycle, void *data);

//function ngx_signal_handler_closure(){
//    return function($signo){
//       ngx_signal_handler($signo);
//    };
//}
function signals($i = 0){
   static  $signals = array(
         array(
            'signo'=> ngx_signal_value(NGX_RECONFIGURE_SIGNAL),
             'signame'=>"SIG ".ngx_value(NGX_RECONFIGURE_SIGNAL),
             'name'=>'reload',
             'handler'=>'ngx_signal_handler',
         ),
         array(
             'signo'=> ngx_signal_value(NGX_REOPEN_SIGNAL),
             'signame'=>"SIG ".ngx_value(NGX_REOPEN_SIGNAL),
             'name'=>'reopen',
             'handler'=>'ngx_signal_handler',
         ),
         array(
             'signo'=> ngx_signal_value(NGX_NOACCEPT_SIGNAL),
             'signame'=>"SIG ".ngx_value(NGX_NOACCEPT_SIGNAL),
             'name'=>'',
             'handler'=>'ngx_signal_handler',
         ),
         array(
             'signo'=> ngx_signal_value(NGX_TERMINATE_SIGNAL),
             'signame'=>"SIG ".ngx_value(NGX_TERMINATE_SIGNAL),
             'name'=>'stop',
             'handler'=>'ngx_signal_handler',
         ),
         array(
             'signo'=> ngx_signal_value(NGX_SHUTDOWN_SIGNAL),
             'signame'=>"SIG ".ngx_value(NGX_SHUTDOWN_SIGNAL),
             'name'=>'quit',
             'handler'=>'ngx_signal_handler',
         ),
         array(
             'signo'=> ngx_signal_value(NGX_CHANGEBIN_SIGNAL),
             'signame'=>"SIG ".ngx_value(NGX_CHANGEBIN_SIGNAL),
             'name'=>'',
             'handler'=>'ngx_signal_handler',
         ),
         array(
             'signo'=>SIGALRM,
             'signame'=>"SIGALRM",
             'name'=>'',
             'handler'=>'ngx_signal_handler',
         ),
         array(
             'signo'=>SIGINT,
             'signame'=>"SIGINT",
             'name'=>'',
             'handler'=>'ngx_signal_handler',
         ),
         array(
             'signo'=>SIGIO,
             'signame'=>"SIGIO",
             'name'=>'',
             'handler'=>'ngx_signal_handler',
         ),
         array(
             'signo'=>SIGCHLD,
             'signame'=>"SIGCHLD",
             'name'=>'',
             'handler'=>'ngx_signal_handler',
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

function ngx_signal_handler($signo)
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
    $ngx_cycle = ngx_cycle();
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

            if ($err == NGX_ECHILD) {
                ngx_log_error(NGX_LOG_INFO, $ngx_cycle->log, $err, "waitpid() failed");
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
     //   ngx_unlock_mutexes($pid);
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

   for ($i=0; $sig = signals($i),$sig->signo != 0; $i++) {
       if (ngx_strcmp($name, $sig->name) == 0) {
          if (posix_kill($pid, $sig->signo) != false) {
             return 0;
          }

                ngx_log_error(NGX_LOG_ALERT, $cycle->log, posix_get_last_error(),
                              "kill(%P, %d) failed", array($pid, $sig->signo));
            }
        }

        return 1;
}

function ngx_init_signals(ngx_log $log)
{
//ngx_signal_t      *sig;
//    struct sigaction   sa;

    for ($i=0,$sig = signals($i); $sig->signo != 0; $i++) {
        if (pcntl_signal($sig['signo'], $sig['handler'], NULL) == false) {
            ngx_log_error(NGX_LOG_EMERG, $log, pcntl_get_last_error(),
                "sigaction(%s) failed", $sig['signame']);
            return NGX_ERROR;
        }
    }

    return NGX_OK;
}

function ngx_spawn_process(ngx_cycle_t $cycle, /**ngx_spawn_proc_pt func name**/  $proc, $data, $name,  $respawn)
{
//    u_long     on;
//    ngx_pid_t  pid;
//    ngx_int_t  s;

    if ($respawn >= 0) {
        $s = $respawn;
    } else {
        for ($s = 0; $s < ngx_last_process(); $s++) {
            $ngx_processes_s = ngx_processes($s);
            if ($ngx_processes_s->pid == null) {
                break;
            }
        }

        if ($s == NGX_MAX_PROCESSES) {
            ngx_log_error(NGX_LOG_ALERT, $cycle->log, 0,
                          "no more than %d processes can be spawned",
                          NGX_MAX_PROCESSES);
            return NGX_INVALID_PID;
        }
    }


    if ($respawn != NGX_PROCESS_DETACHED) {

        /* Solaris 9 still has no AF_LOCAL */
        $ngx_processes_s = ngx_processes($s);
        if (socket_create_pair(AF_UNIX, SOCK_STREAM, 0, $ngx_processes_s->channel) == false)
        {
            ngx_log_error(NGX_LOG_ALERT, $cycle->log, socket_last_error(),
                          "socketpair() failed while spawning \"%s\"", $name);
            return NGX_INVALID_PID;
        }

        ngx_log_debug2(NGX_LOG_DEBUG_CORE, $cycle->log, 0,
                       "channel %d:%d",
                       $ngx_processes_s->channel[0],
                       $ngx_processes_s->channel[1]);

        if (ngx_nonblocking($ngx_processes_s->channel[0]) == false) {
            ngx_log_error(NGX_LOG_ALERT, $cycle->log, socket_last_error(),
                          ngx_nonblocking_n ." failed while spawning \"%s\"",
                          $name);
            ngx_close_channel($ngx_processes_s->channel, $cycle->log);
            return NGX_INVALID_PID;
        }

        if (ngx_nonblocking($ngx_processes_s->channel[1]) == false) {
            ngx_log_error(NGX_LOG_ALERT, $cycle->log, socket_last_error(),
                          ngx_nonblocking_n ." failed while spawning \"%s\"",
                          $name);
            ngx_close_channel($ngx_processes_s->channel, $cycle->log);
            return NGX_INVALID_PID;
        }

//        on = 1;
//        if (ioctl(ngx_processes[s].channel[0], FIOASYNC, &on) == -1) {
//            ngx_log_error(NGX_LOG_ALERT, cycle->log, ngx_errno,
//                          "ioctl(FIOASYNC) failed while spawning \"%s\"", name);
//            ngx_close_channel(ngx_processes[s].channel, cycle->log);
//            return NGX_INVALID_PID;
//        }
//
//        if (fcntl(ngx_processes[s].channel[0], F_SETOWN, ngx_pid) == -1) {
//            ngx_log_error(NGX_LOG_ALERT, cycle->log, ngx_errno,
//                          "fcntl(F_SETOWN) failed while spawning \"%s\"", name);
//            ngx_close_channel(ngx_processes[s].channel, cycle->log);
//            return NGX_INVALID_PID;
//        }
//
//        if (fcntl(ngx_processes[s].channel[0], F_SETFD, FD_CLOEXEC) == -1) {
//            ngx_log_error(NGX_LOG_ALERT, cycle->log, ngx_errno,
//                          "fcntl(FD_CLOEXEC) failed while spawning \"%s\"",
//                           name);
//            ngx_close_channel(ngx_processes[s].channel, cycle->log);
//            return NGX_INVALID_PID;
//        }
//
//        if (fcntl(ngx_processes[s].channel[1], F_SETFD, FD_CLOEXEC) == -1) {
//            ngx_log_error(NGX_LOG_ALERT, cycle->log, ngx_errno,
//                          "fcntl(FD_CLOEXEC) failed while spawning \"%s\"",
//                           name);
//            ngx_close_channel(ngx_processes[s].channel, cycle->log);
//            return NGX_INVALID_PID;
//        }

        ngx_channel($ngx_processes_s->channel[1]);

    } else {
        $ngx_processes_s->channel[0] = null;
        $ngx_processes_s->channel[1] = null;
    }

    ngx_process_slot($s);

    $pid = pcntl_fork();

    switch ($pid) {

        case -1:
            ngx_log_error(NGX_LOG_ALERT, $cycle->log, pcntl_get_last_error(),
                      "fork() failed while spawning \"%s\"", $name);
            ngx_close_channel($ngx_processes_s->channel, $cycle->log);
            return NGX_INVALID_PID;

        case 0:
            $ngx_pid = ngx_getpid();
            ngx_pid($ngx_pid);
            if(is_callable($proc)){
                call_user_func($proc,$cycle, $data);
            }else{
                ngx_log_error(NGX_LOG_ALERT, $cycle->log, 0, $proc."is not callbale");
            }
            break;

        default:
            break;
    }

    ngx_log_error(NGX_LOG_NOTICE, $cycle->log, 0, "start %s %P", array($name, $pid));

    $ngx_processes_s->pid = $pid;
    $ngx_processes_s->exited = 0;

    if ($respawn >= 0) {
        return $pid;
    }

    $ngx_processes_s->proc = $proc;
    $ngx_processes_s->data = $data;
    $ngx_processes_s->name = $name;
    $ngx_processes_s->exiting = 0;

    switch ($respawn) {

        case NGX_PROCESS_NORESPAWN:
            $ngx_processes_s->respawn = 0;
            $ngx_processes_s->just_spawn = 0;
            $ngx_processes_s->detached = 0;
        break;

        case NGX_PROCESS_JUST_SPAWN:
            $ngx_processes_s->respawn = 0;
            $ngx_processes_s->just_spawn = 1;
            $ngx_processes_s->detached = 0;
        break;

        case NGX_PROCESS_RESPAWN:
            $ngx_processes_s->respawn = 1;
            $ngx_processes_s->just_spawn = 0;
            $ngx_processes_s->detached = 0;
        break;

        case NGX_PROCESS_JUST_RESPAWN:
            $ngx_processes_s->respawn = 1;
            $ngx_processes_s->just_spawn = 1;
            $ngx_processes_s->detached = 0;
        break;

        case NGX_PROCESS_DETACHED:
            $ngx_processes_s->respawn = 0;
            $ngx_processes_s->just_spawn = 0;
            $ngx_processes_s->detached = 1;
        break;
    }

    if ($s == ngx_last_process()) {
        $s++;
        ngx_last_process($s);
    }

    return $pid;
}


function ngx_execute(ngx_cycle_t $cycle, ngx_exec_ctx_t $ctx)
{
    return ngx_spawn_process($cycle, 'ngx_execute_proc', $ctx, $ctx->name,
                             NGX_PROCESS_DETACHED);
}


function ngx_execute_proc(ngx_cycle_t $cycle, $data)
{
//ngx_exec_ctx_t  *ctx = data;
    $ctx = $data;

    //todo should fix it execv = fork + exec
    if (pcntl_exec($ctx->path, $ctx->argv, $ctx->envp) == -1) {
          ngx_log_error(NGX_LOG_ALERT, $cycle->log, pcntl_get_last_error(),
                      "execve() failed while executing %s \"%s\"",
                      array($ctx->name, $ctx->path));
    }
    exit(1);
}




