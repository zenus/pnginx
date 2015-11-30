<?php
/**
 * Created by PhpStorm.
 * User: zenus@github.com
 * Date: 2015/11/4
 * Time: 15:40
 */

static $ngx_test_config = 'hi';
static $ngx_dump_config;
static $ngx_quiet_mode;

define('NGX_DEBUG_POINTS_STOP',   1);
define('NGX_DEBUG_POINTS_ABORT',  2);

//define('NGX_MAIN_CONF',0x01000000);
//define('NGX_ANY_CONF',0x0F000000);

function ngx_cycle(ngx_cycle_t $obj = null){
    static $ngx_cycle = null;
    if(!is_null($obj)){
       $ngx_cycle = $obj;
    }else{
       return $ngx_cycle;
    }
}

function ngx_quiet_mode($i = null){
    static $ngx_quiet_mode = null;
    if(!is_null($i)){
       $ngx_quiet_mode = $i;
    }else{
       return $ngx_quiet_mode;
    }
}

class ngx_cycle_t {
    /**void **/        private         /**   ****conf_ctx  ****/ $conf_ctx;
    //ngx_pool_t               *pool;

    /**ngx_log_t**/  private     $log;
   /** ngx_log_t **/  private   $new_log;

   /**ngx_uint_t***/ private            $log_use_stderr;  /* unsigned  log_use_stderr:1; */

    /**ngx_connection_t**/ private    /**  **files ***/ $files;
    /**ngx_connection_t**/ private       $free_connections;
    /**ngx_uint_t**/       private       $free_connection_n;

    /**ngx_queue_t**/  private    $reusable_connections_queue;

    /**ngx_array_t**/  private             $listening;
    /**ngx_array_t**/  private             $paths;
    /**ngx_array_t**/  private             $config_dump;
    /**ngx_list_t**/   private             $open_files;
    //ngx_list_t                shared_memory;

    /**ngx_uint_t**/       private         $connection_n;
    /**ngx_uint_t**/       private         $files_n;

    /**ngx_connection_t**/ private        $connections;
    /**ngx_event_t**/      private        $read_events;
    /**ngx_event_t**/      private        $write_events;

    /**ngx_cycle_t**/      private        $old_cycle;

    /**ngx_str_t**/        private         $conf_file;
    /**ngx_str_t**/        private         $conf_param;
    /**ngx_str_t**/        private         $conf_prefix;
    /**ngx_str_t**/        private         $prefix;
    /**ngx_str_t**/        private         $lock_file;
    /**ngx_str_t**/        private         $hostname;


    public function __set($property, $value){
        if($property == 'log' && $value instanceof ngx_log){
            $this->log = $value;
        }elseif($property == 'listening' && $value instanceof ngx_listening_s){
            $this->listening[] =  $value;
        }elseif($property == 'old_cycle' && $value instanceof ngx_cycle_t){
            $this->old_cycle =  $value;
        }else{
            $this->$property = $value;
        }
    }

    public function __get($property){
       return $this->$property;
    }
}

class ngx_core_conf_t {
/** ngx_flag_t **/ private $daemon; 
/** ngx_flag_t **/ private $master; 
/** ngx_msec_t **/ private $timer_resolution; 
/** ngx_int_t **/ private $worker_processes; 
/** ngx_int_t **/ private $debug_points; 
/** ngx_int_t **/ private $rlimit_nofile; 
/** off_t **/ private $rlimit_core; 
/** int **/ private $priority; 
/** ngx_uint_t **/ private $cpu_affinity_n; 
/**     uint64_t **/   private            $cpu_affinity;
/** char **/ private $username;
/** ngx_uid_t **/ private $user; 
/** ngx_gid_t **/ private $group; 
/** ngx_str_t **/ private $working_directory; 
/** ngx_str_t **/ private $lock_file; 
/** ngx_str_t **/ private $pid; 
/** ngx_str_t **/ private $oldpid; 
/** ngx_array_t **/ private $env; 
/** char **/ private $environment;

    public function __set($property,$value){
       $this->$property = $value;
    }
    public function __get($property){
       return $this->$property;
    }
}



function ngx_init_cycle(ngx_cycle_t $old_cycle)
{
//void                *rv;
//char               **senv, **env;
//    ngx_uint_t           i, n;
//    ngx_log_t           *log;
//    ngx_time_t          *tp;
//    ngx_conf_t           conf;
//    ngx_pool_t          *pool;
//    ngx_cycle_t         *cycle, **old;
//    ngx_shm_zone_t      *shm_zone, *oshm_zone;
//    ngx_list_part_t     *part, *opart;
//    ngx_open_file_t     *file;
//    ngx_listening_t     *ls, *nls;
//    ngx_core_conf_t     *ccf, *old_ccf;
//    ngx_core_module_t   *module;
//    char                 hostname[NGX_MAXHOSTNAMELEN];

    ngx_timezone_update();

    /* force localtime update with a new timezone */

    $tp = ngx_timeofday();
    $tp['sec'] = 0;

    ngx_time_update();


    $log = $old_cycle->log;

    $cycle = new ngx_cycle_t();
    $cycle->log = $log;

    $cycle->old_cycle = $old_cycle;

    $cycle->conf_prefix = $old_cycle->conf_prefix;

    $cycle->conf_file = $old_cycle->conf_file;

//    ngx_cpystrn(cycle->conf_file.data, old_cycle->conf_file.data,
//                old_cycle->conf_file.len + 1);
//
    $cycle->conf_param = $old_cycle->conf_param;
//
//
//    n = old_cycle->paths.nelts ? old_cycle->paths.nelts : 10;
//
//    cycle->paths.elts = ngx_pcalloc(pool, n * sizeof(ngx_path_t *));
//    if (cycle->paths.elts == NULL) {
//    ngx_destroy_pool(pool);
//    return NULL;
//}
//
//    cycle->paths.nelts = 0;
//    cycle->paths.size = sizeof(ngx_path_t *);
//    cycle->paths.nalloc = n;
//    cycle->paths.pool = pool;
//
//
//    if (ngx_array_init(&cycle->config_dump, pool, 1, sizeof(ngx_conf_dump_t))
//        != NGX_OK)
//    {
//        ngx_destroy_pool(pool);
//        return NULL;
//    }
//    if ($old_cycle->open_files.part.nelts) {
//    n = old_cycle->open_files.part.nelts;
//        for (part = old_cycle->open_files.part.next; part; part = part->next) {
//        n += part->nelts;
//        }
//
//    } else {
//    n = 20;
//   }
//
//    if (ngx_list_init(&cycle->open_files, pool, n, sizeof(ngx_open_file_t))
//        != NGX_OK)
//    {
//        ngx_destroy_pool(pool);
//        return NULL;
//    }
//
//    if (old_cycle->shared_memory.part.nelts) {
//    n = old_cycle->shared_memory.part.nelts;
//        for (part = old_cycle->shared_memory.part.next; part; part = part->next)
//        {
//            n += part->nelts;
//        }
//
//    } else {
//    n = 1;
//}
//
//    if (ngx_list_init(&cycle->shared_memory, pool, n, sizeof(ngx_shm_zone_t))
//        != NGX_OK)
//    {
//        ngx_destroy_pool(pool);
//        return NULL;
//    }
//
//    n = old_cycle->listening.nelts ? old_cycle->listening.nelts : 10;
//
//    cycle->listening.elts = ngx_pcalloc(pool, n * sizeof(ngx_listening_t));
//    if (cycle->listening.elts == NULL) {
//    ngx_destroy_pool(pool);
//    return NULL;
//}
//
//    cycle->listening.nelts = 0;
//    cycle->listening.size = sizeof(ngx_listening_t);
//    cycle->listening.nalloc = n;
//    cycle->listening.pool = pool;
//
//
//    ngx_queue_init(&cycle->reusable_connections_queue);
//
//
//    cycle->conf_ctx = ngx_pcalloc(pool, ngx_max_module * sizeof(void *));
//    if (cycle->conf_ctx == NULL) {
//    ngx_destroy_pool(pool);
//    return NULL;
//}
//
//
    if ($hostname = gethostname() == false) {
        ngx_log_error(NGX_LOG_EMERG, $log, socket_last_error(), "gethostname() failed");
        return NULL;
    }
//
//    /* on Linux gethostname() silently truncates name that does not fit */
//
//    hostname[NGX_MAXHOSTNAMELEN - 1] = '\0';
//    cycle->hostname.len = ngx_strlen(hostname);
//
//    cycle->hostname.data = ngx_pnalloc(pool, cycle->hostname.len);
//    if (cycle->hostname.data == NULL) {
//    ngx_destroy_pool(pool);
//    return NULL;
//}
//
    $cycle->hostname = $hostname;
//    ngx_strlow(cycle->hostname.data, (u_char *) hostname, cycle->hostname.len);
    //$ngx_modules = ngx_cfg('ngx_modules');
    for ($i = 0; ngx_modules($i); $i++) {
    if (ngx_modules($i,'type') != NGX_CORE_MODULE) {
        continue;
    }

        $module = ngx_modules($i,'ctx');
        if ($create_conf = $module->create_conf) {
            $rv = $create_conf($cycle);
            if ($rv == NULL) {
                return NULL;
            }
            $cycle->conf_ctx[ngx_modules($i,'index')] = $rv;
        }
    }
//
// todo find where to use
//    senv = environ;
//
//
    $conf = new ngx_conf_t();
    $conf->ctx = $cycle->conf_ctx;
    $conf->cycle = $cycle;
    $conf->log = $log;
    $conf->module_type = NGX_CORE_MODULE;
    $conf->cmd_type = NGX_MAIN_CONF;


    if (ngx_conf_param($conf) != NGX_CONF_OK) {
        //environ = senv;
        unset($conf);
        //ngx_destroy_cycle_pools(&conf);
        return NULL;
    }

    if (ngx_conf_parse($conf, $cycle->conf_file) != NGX_CONF_OK) {
        //environ = senv;
//        ngx_destroy_cycle_pools($conf);
        unset($conf);
        return NULL;
    }

//    $ngx_test_config = ngx_cfg('ngx_test_config');
//    $ngx_quiet_mode = ngx_cfg('ngx_quiet_mode');

    if (ngx_test_config() && !ngx_quiet_mode()) {
        ngx_log_stderr(0, "the configuration file %s syntax is ok",
            $cycle->conf_file);
    }

    for ($i = 0; ngx_modules($i); $i++) {
    if (ngx_modules($i,'type') != NGX_CORE_MODULE) {
        continue;
    }

        $module = ngx_modules($i,'ctx');

        if ($module->init_conf) {
        if ($module->init_conf($cycle, $cycle->conf_ctx[ngx_modules($i,'index')])
                == NGX_CONF_ERROR)
            {
                //environ = senv;
                //ngx_destroy_cycle_pools($conf);
                unset($conf);
                return NULL;
            }
        }
    }

    if (ngx_process() == NGX_PROCESS_SIGNALLER) {
        return $cycle;
    }

    //ccf = (ngx_core_conf_t *) ngx_get_conf(cycle->conf_ctx, ngx_core_module);
    $ccf = ngx_get_conf($cycle->conf_ctx, ngx_core_module());
//
    if (ngx_test_config()) {

            if (ngx_create_pidfile($ccf->pid, $log) != NGX_OK) {
            goto failed;
        }

    } else if (!ngx_is_init_cycle($old_cycle)) {

        /*
         * we do not create the pid file in the first ngx_init_cycle() call
         * because we need to write the demonized process pid
         */

        $old_ccf = ngx_get_conf($old_cycle->conf_ctx, ngx_core_module());

        if (ngx_strcmp($ccf->pid, $old_ccf->pid) != 0)
        {
            /* new pid file name */

            if (ngx_create_pidfile($ccf->pid, $log) != NGX_OK) {
            goto failed;
        }
            ngx_delete_pidfile($old_cycle);
        }
    }
//
//
    if (ngx_test_lockfile($cycle->lock_file, $log) != NGX_OK) {
            goto failed;
        }
//
//
    if (ngx_create_paths($cycle, $ccf->user) != NGX_OK) {
            goto failed;
        }
//
//
    if (ngx_log_open_default($cycle) != NGX_OK) {
        goto failed;
    }
//
//    /* open the new files */
//
    $open_files_list = $cycle->open_files;
    $file = $open_files_list->current();
//
    for ($i = 0; /* void */ ; $i++) {

        if ($i >= count($file)) {
            $open_files_list->next();
            $file = $open_files_list->current();
            if (empty($file)) {
                break;
            }
            $i = 0;
        }

        if (strlen($file[$i]->name)== 0) {
            continue;
        }

        $file[$i]->fd = ngx_open_file($file[$i]->name,
                                   NGX_FILE_APPEND,
                                   NGX_FILE_DEFAULT_ACCESS);

        ngx_log_debug3(NGX_LOG_DEBUG_CORE, $log, 0,
            "log: %p %d \"%s\"",
            $file[$i], $file[$i]->fd, $file[$i]->name);

        if ($file[$i]->fd == NGX_INVALID_FILE) {
            ngx_log_error(NGX_LOG_EMERG, $log, NGX_FERROR,
                ngx_open_file_n ." \"%s\" failed",
                          $file[$i]->name);
            goto failed;
        }
//
//#if !(NGX_WIN32)
//        if (fcntl(file[i].fd, F_SETFD, FD_CLOEXEC) == -1) {
//            ngx_log_error(NGX_LOG_EMERG, log, ngx_errno,
//                "fcntl(FD_CLOEXEC) \"%s\" failed",
//                file[i].name.data);
//            goto failed;
//        }
//#endif
    }

    $cycle->log = $cycle->new_log;

//todo find the usage of shared memory
//    /* create shared memory */
//
//    part = &cycle->shared_memory.part;
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
//        if (shm_zone[i].shm.size == 0) {
//            ngx_log_error(NGX_LOG_EMERG, log, 0,
//                "zero size shared memory zone \"%V\"",
//                &shm_zone[i].shm.name);
//            goto failed;
//        }
//
//        shm_zone[i].shm.log = cycle->log;
//
//        opart = &old_cycle->shared_memory.part;
//        oshm_zone = opart->elts;
//
//        for (n = 0; /* void */ ; n++) {
//
//            if (n >= opart->nelts) {
//                if (opart->next == NULL) {
//                    break;
//                }
//                opart = opart->next;
//                oshm_zone = opart->elts;
//                n = 0;
//            }
//
//            if (shm_zone[i].shm.name.len != oshm_zone[n].shm.name.len) {
//                continue;
//            }
//
//            if (ngx_strncmp(shm_zone[i].shm.name.data,
//                            oshm_zone[n].shm.name.data,
//                            shm_zone[i].shm.name.len)
//                != 0)
//            {
//                continue;
//            }
//
//            if (shm_zone[i].tag == oshm_zone[n].tag
//            && shm_zone[i].shm.size == oshm_zone[n].shm.size
//            && !shm_zone[i].noreuse)
//            {
//                shm_zone[i].shm.addr = oshm_zone[n].shm.addr;
//#if (NGX_WIN32)
//                shm_zone[i].shm.handle = oshm_zone[n].shm.handle;
//#endif
//
//                if (shm_zone[i].init(&shm_zone[i], oshm_zone[n].data)
//                    != NGX_OK)
//                {
//                    goto failed;
//                }
//
//                goto shm_zone_found;
//            }
//
//            ngx_shm_free(&oshm_zone[n].shm);
//
//            break;
//        }
//
//        if (ngx_shm_alloc(&shm_zone[i].shm) != NGX_OK) {
//            goto failed;
//        }
//
//        if (ngx_init_zone_pool(cycle, &shm_zone[i]) != NGX_OK) {
//            goto failed;
//        }
//
//        if (shm_zone[i].init(&shm_zone[i], NULL) != NGX_OK) {
//            goto failed;
//        }
//
//    shm_zone_found:
//
//        continue;
//    }
//
//
//    /* handle the listening sockets */
//
    if (!empty($old_cycle->listening)) {
        $ls = $old_cycle->listening;
        for ($i = 0; $i < count($old_cycle->listening); $i++) {
            $ls[$i]->remain = 0;
        }

        $nls = $cycle->listening;
        for ($n = 0; $n < count($cycle->listening); $n++) {

            for ($i = 0; $i < count($old_cycle->listening); $i++) {
                if ($ls[$i]->ignore) {
                    continue;
                }

                if ($ls[$i]->remain) {
                    continue;
                }

                if (ngx_cmp_sockaddr($nls[$n]->sockaddr, $ls[$i]->sockaddr,  1)
                    == NGX_OK)
                {
                    $nls[$n]->fd = $ls[$i]->fd;
                    $nls[$n]->previous = $ls[$i];
                    $ls[$i]->remain = 1;

                    if ($ls[$i]->backlog != $nls[$n]->backlog) {
                        $nls[$n]->listen = 1;
                    }
                    break;
                }
            }
            if ($nls[$n]->fd == false) {
                $nls[$n]->open = 1;
            }
        }

    } else {
        $ls = $cycle->listening;
        for ($i = 0; $i < count($cycle->listening); $i++) {
                $ls[$i]->open = 1;
        }
    }

    if (ngx_open_listening_sockets($cycle) != NGX_OK) {
        goto failed;
    }

    if (!ngx_test_config()) {
        ngx_configure_listening_sockets($cycle);
    }
//
//
//    /* commit the new cycle configuration */
//
//    if (!ngx_use_stderr) {
//        (void) ngx_log_redirect_stderr(cycle);
//    }
//
//    pool->log = cycle->log;
//
//    for (i = 0; ngx_modules[i]; i++) {
//    if (ngx_modules[i]->init_module) {
//        if (ngx_modules[i]->init_module(cycle) != NGX_OK) {
//            /* fatal */
//            exit(1);
//        }
//        }
//    }
//
//
//    /* close and delete stuff that lefts from an old cycle */
//
//    /* free the unnecessary shared memory */
//
//    opart = &old_cycle->shared_memory.part;
//    oshm_zone = opart->elts;
//
//    for (i = 0; /* void */ ; i++) {
//
//        if (i >= opart->nelts) {
//            if (opart->next == NULL) {
//                goto old_shm_zone_done;
//            }
//            opart = opart->next;
//            oshm_zone = opart->elts;
//            i = 0;
//        }
//
//        part = &cycle->shared_memory.part;
//        shm_zone = part->elts;
//
//        for (n = 0; /* void */ ; n++) {
//
//            if (n >= part->nelts) {
//                if (part->next == NULL) {
//                    break;
//                }
//                part = part->next;
//                shm_zone = part->elts;
//                n = 0;
//            }
//
//            if (oshm_zone[i].shm.name.len == shm_zone[n].shm.name.len
//            && ngx_strncmp(oshm_zone[i].shm.name.data,
//                               shm_zone[n].shm.name.data,
//                               oshm_zone[i].shm.name.len)
//                == 0)
//            {
//                goto live_shm_zone;
//            }
//        }
//
//        ngx_shm_free(&oshm_zone[i].shm);
//
//    live_shm_zone:
//
//        continue;
//    }
//
//old_shm_zone_done:
//
//
//    /* close the unnecessary listening sockets */
//
//    ls = old_cycle->listening.elts;
//    for (i = 0; i < old_cycle->listening.nelts; i++) {
//
//    if (ls[i].remain || ls[i].fd == (ngx_socket_t) -1) {
//        continue;
//    }
//
//        if (ngx_close_socket(ls[i].fd) == -1) {
//        ngx_log_error(NGX_LOG_EMERG, log, ngx_socket_errno,
//            ngx_close_socket_n " listening socket on %V failed",
//                          &ls[i].addr_text);
//        }
//
//#if (NGX_HAVE_UNIX_DOMAIN)
//
//        if (ls[i].sockaddr->sa_family == AF_UNIX) {
//        u_char  *name;
//
//        name = ls[i].addr_text.data + sizeof("unix:") - 1;
//
//            ngx_log_error(NGX_LOG_WARN, cycle->log, 0,
//                          "deleting socket %s", name);
//
//            if (ngx_delete_file(name) == NGX_FILE_ERROR) {
//                ngx_log_error(NGX_LOG_EMERG, cycle->log, ngx_socket_errno,
//                              ngx_delete_file_n " %s failed", name);
//            }
//        }
//
//#endif
//    }
//
//
//    /* close the unnecessary open files */
//
//    part = &old_cycle->open_files.part;
//    file = part->elts;
//
//    for (i = 0; /* void */ ; i++) {
//
//        if (i >= part->nelts) {
//            if (part->next == NULL) {
//                break;
//            }
//            part = part->next;
//            file = part->elts;
//            i = 0;
//        }
//
//        if (file[i].fd == NGX_INVALID_FILE || file[i].fd == ngx_stderr) {
//            continue;
//        }
//
//        if (ngx_close_file(file[i].fd) == NGX_FILE_ERROR) {
//            ngx_log_error(NGX_LOG_EMERG, log, ngx_errno,
//                ngx_close_file_n " \"%s\" failed",
//                          file[i].name.data);
//        }
//    }
//
//    ngx_destroy_pool(conf.temp_pool);
//
//    if (ngx_process == NGX_PROCESS_MASTER || ngx_is_init_cycle(old_cycle)) {
//
//        /*
//         * perl_destruct() frees environ, if it is not the same as it was at
//         * perl_construct() time, therefore we save the previous cycle
//         * environment before ngx_conf_parse() where it will be changed.
//         */
//
//        env = environ;
//        environ = senv;
//
//        ngx_destroy_pool(old_cycle->pool);
//        cycle->old_cycle = NULL;
//
//        environ = env;
//
//        return cycle;
//    }
//
//
//    if (ngx_temp_pool == NULL) {
//        ngx_temp_pool = ngx_create_pool(128, cycle->log);
//        if (ngx_temp_pool == NULL) {
//            ngx_log_error(NGX_LOG_EMERG, cycle->log, 0,
//                          "could not create ngx_temp_pool");
//            exit(1);
//        }
//
//        n = 10;
//        ngx_old_cycles.elts = ngx_pcalloc(ngx_temp_pool,
//            n * sizeof(ngx_cycle_t *));
//        if (ngx_old_cycles.elts == NULL) {
//            exit(1);
//        }
//        ngx_old_cycles.nelts = 0;
//        ngx_old_cycles.size = sizeof(ngx_cycle_t *);
//        ngx_old_cycles.nalloc = n;
//        ngx_old_cycles.pool = ngx_temp_pool;
//
//        ngx_cleaner_event.handler = ngx_clean_old_cycles;
//        ngx_cleaner_event.log = cycle->log;
//        ngx_cleaner_event.data = &dumb;
//        dumb.fd = (ngx_socket_t) -1;
//    }
//
//    ngx_temp_pool->log = cycle->log;
//
//    old = ngx_array_push(&ngx_old_cycles);
//    if (old == NULL) {
//        exit(1);
//    }
//    *old = old_cycle;
//
//    if (!ngx_cleaner_event.timer_set) {
//        ngx_add_timer(&ngx_cleaner_event, 30000);
//        ngx_cleaner_event.timer_set = 1;
//    }
//
//    return cycle;
//
//
//failed:
//
//    if (!ngx_is_init_cycle(old_cycle)) {
//        old_ccf = (ngx_core_conf_t *) ngx_get_conf(old_cycle->conf_ctx,
//                                                   ngx_core_module);
//        if (old_ccf->environment) {
//            environ = old_ccf->environment;
//        }
//    }
//
//    /* rollback the new cycle configuration */
//
//    part = &cycle->open_files.part;
//    file = part->elts;
//
//    for (i = 0; /* void */ ; i++) {
//
//        if (i >= part->nelts) {
//            if (part->next == NULL) {
//                break;
//            }
//            part = part->next;
//            file = part->elts;
//            i = 0;
//        }
//
//        if (file[i].fd == NGX_INVALID_FILE || file[i].fd == ngx_stderr) {
//            continue;
//        }
//
//        if (ngx_close_file(file[i].fd) == NGX_FILE_ERROR) {
//            ngx_log_error(NGX_LOG_EMERG, log, ngx_errno,
//                ngx_close_file_n " \"%s\" failed",
//                          file[i].name.data);
//        }
//    }
//
//    if (ngx_test_config) {
//        ngx_destroy_cycle_pools(&conf);
//        return NULL;
//    }
//
//    ls = cycle->listening.elts;
//    for (i = 0; i < cycle->listening.nelts; i++) {
//    if (ls[i].fd == (ngx_socket_t) -1 || !ls[i].open) {
//        continue;
//    }
//
//        if (ngx_close_socket(ls[i].fd) == -1) {
//        ngx_log_error(NGX_LOG_EMERG, log, ngx_socket_errno,
//            ngx_close_socket_n " %V failed",
//                          &ls[i].addr_text);
//        }
//    }
//
//    ngx_destroy_cycle_pools(&conf);
//
//    return NULL;
}

function ngx_create_pidfile($name, ngx_log $log)
{
//size_t      len;
//    ngx_uint_t  create;
//    ngx_file_t  file;
//    u_char      pid[NGX_INT64_LEN + 2];

    if (ngx_process() > NGX_PROCESS_MASTER) {
        return NGX_OK;
    }

    $file = new ngx_file_t();
    $file->name = $name;
    $file->log = $log;
//    ngx_memzero(&file, sizeof(ngx_file_t));
//
//    file.name = *name;
//    file.log = log;

//    $create = ngx_test_config() ? NGX_FILE_CREATE_OR_OPEN : NGX_FILE_TRUNCATE;

    $file->fd = ngx_open_file($file->name, NGX_FILE_RDWR, NGX_FILE_DEFAULT_ACCESS);

    if ($file->fd == NGX_INVALID_FILE) {
        ngx_log_error(NGX_LOG_EMERG, $log, NGX_FERROR,
            ngx_open_file_n ." \"%s\" failed", $file->name);
        return NGX_ERROR;
    }

    $pid = '';
    if (ngx_test_config()) {
        $pid = ngx_snprintf($pid,  "%P%N", (array)ngx_pid());

        if (ngx_write_file($file, $pid, strlen($pid), 0) == NGX_ERROR) {
            return NGX_ERROR;
        }
    }

    if (ngx_close_file($file->fd) == NGX_FILE_ERROR) {
        ngx_log_error(NGX_LOG_ALERT, $log, NGX_FCERROR,
            ngx_close_file_n ." \"%s\" failed", $file->name);
    }

    return NGX_OK;
}

function ngx_test_config($i = null){
    static $ngx_test_config = null;
    if(!is_null($ngx_test_config)){
       $ngx_test_config = $i;
    }else{
        return $ngx_test_config ;
    }
}

function ngx_is_init_cycle(ngx_cycle_t $cycle) {

   return ($cycle->conf_ctx == NULL);
}

function ngx_delete_pidfile(ngx_cycle_t $cycle)
{
//u_char           *name;
//    ngx_core_conf_t  *ccf;

    $ccf = ngx_get_conf($cycle->conf_ctx, ngx_core_module());

    $name = ngx_new_binary()? $ccf->oldpid : $ccf->pid;

    if (ngx_delete_file($name) == NGX_FILE_ERROR) {
        ngx_log_error(NGX_LOG_ALERT, $cycle->log, NGX_FDERROR,
                      ngx_delete_file_n ." \"%s\" failed", $name);
    }
}


function ngx_test_lockfile($file, ngx_log $log)
{
//ngx_fd_t  fd;

    $fd = ngx_open_file($file, NGX_FILE_RDWR, NGX_FILE_DEFAULT_ACCESS);

    if ($fd == NGX_INVALID_FILE) {
        ngx_log_error(NGX_LOG_EMERG, $log, NGX_FERROR,
            ngx_open_file_n ." \"%s\" failed", $file);
        return NGX_ERROR;
    }

    if (ngx_close_file($fd) == NGX_FILE_ERROR) {
        ngx_log_error(NGX_LOG_ALERT, $log, NGX_FCERROR,
            ngx_close_file_n ." \"%s\" failed", $file);
    }

    if (ngx_delete_file($file) == NGX_FILE_ERROR) {
        ngx_log_error(NGX_LOG_ALERT, $log, NGX_FDERROR,
            ngx_delete_file_n ." \"%s\" failed", $file);
    }

    return NGX_OK;
}

