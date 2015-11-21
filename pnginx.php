<?php
include_once 'ngx_core.php';
include_once 'ngx_log.php';
include_once 'ngx_string.php';
include_once 'ngx_process_cycle.php';
include_once 'ngx_cycle.php';
include_once 'ngx_process.php';

define('DS',DIRECTORY_SEPARATOR);
define('pnginx_version',1000000);
define('NGINX_VERSION',"1.0.0");
define('NGINX_VER',"pnginx/".NGINX_VERSION);
define('NGINX_VER_BUILD',NGINX_VER);
define('NGINX_VAR',"NGINX");
define('NGX_OLDPID_EXT',".oldbin");
define('NGX_CONF_PATH',__DIR__.DS.'conf'.DS.'pnginx.conf');
define('NGX_ERROR_LOG_PATH',__DIR__.DS.'log'.DS.'error.log');
define('NGX_CONFIGURE','');






/**
 * Created by PhpStorm.
 * User: zenus@github.com
 * Date: 2015/11/2
 * Time: 14:56
 * @param $ngx_cfg
 * @param $value
 * @return int
 * @internal param $name
 * @internal param $argc
 * @internal param array $argv
 */

//function ngx_cfg($ngx_cfg,$value = null){
//    static $cfg = array();
//    return  is_null($value) ? (isset($cfg[$ngx_cfg]) ? $cfg[$ngx_cfg] : false) : $cfg[$ngx_cfg] = $value;
//}
//static $ngx_show_version = 0;
//static $ngx_show_help = 0;
//static $ngx_show_configure = 0;
//static $ngx_prefix = '';
//static $ngx_conf_file = '';
//static $ngx_conf_params = '';
//static $ngx_signal = '';

//static $ngx_show_version = 0;
//static $ngx_show_help = 0;
//static $ngx_show_configure = 0;
//static $ngx_prefix = '';
//static $ngx_conf_file = '';
//static $ngx_conf_params = '';
//static $ngx_signal = '';

function main($argc, array $argv){


    /***ngx_log_s***/ $log = null;

    /***ngx_cycle_s***/ $cycle = null; $init_cycle = null;

    if (ngx_strerror_init() != NGX_OK) {
        return 1;
    }

    if(ngx_get_options($argc,$argv) != NGX_OK){
       return 1;
    }
    ngx_write_stderr("nginx version: ". NGINX_VER_BUILD. NGX_LINEFEED);

        if (ngx_cfg('ngx_show_help')) {
            ngx_write_stderr(
                "Usage: nginx [-?hvVtTq] [-s signal] [-c filename] ".
                             "[-p prefix] [-g directives]". NGX_LINEFEED
                             .NGX_LINEFEED.
                "Options:" .NGX_LINEFEED.
                "  -?,-h         : this help". NGX_LINEFEED.
                "  -v            : show version and exit". NGX_LINEFEED.
                "  -V            : show version and configure options then exit".
                                   NGX_LINEFEED.
                "  -t            : test configuration and exit". NGX_LINEFEED.
                "  -T            : test configuration, dump it and exit"
                                   .NGX_LINEFEED.
                "  -q            : suppress non-error messages ".
                                   "during configuration testing" .NGX_LINEFEED.
                "  -s signal     : send signal to a master process: ".
                                   "stop, quit, reopen, reload" .NGX_LINEFEED.
                "  -p prefix     : set prefix path (default: NONE)" .NGX_LINEFEED.
                "  -c filename   : set configuration file (default: "
                                   .NGX_CONF_PATH .")" .NGX_LINEFEED.
                "  -g directives : set global directives out of configuration ".
                                   "file" .NGX_LINEFEED .NGX_LINEFEED
                );
        }

        if (ngx_cfg('ngx_show_configure')) {

            ngx_write_stderr("configure arguments:" .NGX_CONFIGURE .NGX_LINEFEED);
        }

        if (!ngx_cfg('ngx_test_config')) {
            return 0;
        }

    //TODO
   // ngx_time_init();
    if (extension_loaded('pcre')){
        ngx_regex_init();
    }

    if (!extension_loaded('posix')){
        ngx_write_stderr("pnginx need --enable-posix" .NGX_LINEFEED);
    }

    ngx_cfg('ngx_pid',ngx_getpid());
    $log = ngx_log_init();
    if($log == null){
        return 1;
    }
    //TODO
    //ngx_ssl_init();
    $init_cycle = new ngx_cycle_t();
    $init_cycle->set_log($log);
    $ngx_cycle = &$init_cycle;
    if (ngx_save_argv($argc, $argv) != NGX_OK) {
        return 1;
    }

    if (ngx_os_init($log) != NGX_OK) {
        return 1;
    }

    if (ngx_add_inherited_sockets($init_cycle) != NGX_OK) {
        return 1;
    }

    $ngx_max_module = 0;
    $ngx_modules = ngx_cfg('ngx_modules');
    for ($i = 0; $ngx_modules[$i]; $i++) {
        $ngx_modules[$i]->index = $ngx_max_module++;
    }
    $cycle = ngx_init_cycle($init_cycle);
//    if (cycle == NULL) {
//        if (ngx_test_config) {
//            ngx_log_stderr(0, "configuration file %s test failed",
//                init_cycle.conf_file.data);
//        }
//
//        return 1;
//    }



}


function ngx_get_options($argc ,array $argv)
{

    for ($i = 1; $i < $argc; $i++) {
        $ptr = 0;
        $p = $argv[$i];
        if ($p[$ptr++] != '-') {
            ngx_log_stderr(0, "invalid option: \"%s\"", (array)$argv[$i]);
            return NGX_ERROR;
        }
        while (ngx_strhas($p,$ptr)) {

            switch ($p[$ptr++]) {

                case '?':
                case 'h':
                    ngx_cfg('ngx_show_version',1);
                    ngx_cfg('ngx_show_help',1);
                    break;

                case 'v':
                    ngx_cfg('ngx_show_version',1);
                    break;

                case 'V':
                    ngx_cfg('ngx_show_version',1);
                    ngx_cfg('ngx_show_configure',1);
                    break;

                case 't':
                    ngx_cfg('ngx_test_config',1);
                    break;

                case 'T':
                    ngx_cfg('ngx_test_config',1);
                    ngx_cfg('ngx_dump_config',1);
                    break;

                case 'q':
                    ngx_cfg('ngx_quiet_mode',1);
                    break;

                case 'p':
                    if (ngx_strhas($p,$ptr)) {
                        //$ngx_prefix = substr($p,$ptr);
                        ngx_cfg('ngx_prefix',substr($p,$ptr));
                        goto next;
                    }

                    if ($argv[++$i]) {
                        //$ngx_prefix = substr($p,$ptr);
                        ngx_cfg('ngx_prefix',$argv[$i]);
                        goto next;
                    }

                    ngx_log_stderr(0, "option \"-p\" requires directory name");
                    return NGX_ERROR;

                case 'c':
                    if (ngx_strhas($p,$ptr)) {
                        //$ngx_conf_file = substr($p,$ptr);
                        ngx_cfg('ngx_conf_file',substr($p,$ptr));
                        goto next;
                    }

                    if ($argv[++$i]) {
                        //$ngx_conf_file = $argv[$i];
                        ngx_cfg('ngx_conf_file',$argv[$i]);
                        goto next;
                    }

                    ngx_log_stderr(0, "option \"-c\" requires file name");
                    return NGX_ERROR;

                case 'g':
                    if (ngx_strhas($p,$ptr)) {
                        ngx_cfg('ngx_conf_params',substr($p,$ptr));
                        //$ngx_conf_params = substr($p,$ptr);
                        goto next;
                    }

                    if ($argv[++$i]) {
                        //$ngx_conf_params = $argv[$i];
                        ngx_cfg('ngx_conf_params',$argv[$i]);
                        goto next;
                    }

                    ngx_log_stderr(0, "option \"-g\" requires parameter");
                    return NGX_ERROR;

                case 's':
                    if (ngx_strhas($p,$ptr)) {
                        //$ngx_signal = substr($p,$ptr);
                        ngx_cfg('ngx_signal',substr($p,$ptr));

                    } else if ($argv[++$i]) {
                        //$ngx_signal = $argv[$i];
                        ngx_cfg('ngx_signal',$argv[$i]);

                    } else {
                        ngx_log_stderr(0, "option \"-s\" requires parameter");
                        return NGX_ERROR;
                    }

                    if (ngx_strcmp(ngx_cfg('ngx_signal'), "stop") == 0
                        || ngx_strcmp(ngx_cfg('ngx_signal'), "quit") == 0
                        || ngx_strcmp(ngx_cfg('ngx_signal'), "reopen") == 0
                        || ngx_strcmp(ngx_cfg('ngx_signal'), "reload") == 0
                    ) {
                        ngx_cfg('ngx_process',NGX_PROCESS_SIGNALLER);
                        //$ngx_process = NGX_PROCESS_SIGNALLER;
                        goto next;
                    }

                    ngx_log_stderr(0, "invalid option: \"-s %s\"", $ngx_signal);
                    return NGX_ERROR;

                default:
                    ngx_log_stderr(0, "invalid option: \"%c\"", $p);
                    return NGX_ERROR;
            }

        }
        next:
        continue;
    }
    return NGX_OK;
}

function ngx_save_argv( $argc, $argv)
{

    ngx_cfg('ngx_os_argv',$argv);
    ngx_cfg('ngx_argc', $argc);
    ngx_cfg('ngx_argv', $argv);

    if (empty($argv)) {
        return NGX_ERROR;
    }

    //todo
    //ngx_os_environ = environ;

    return NGX_OK;
}


function ngx_process_options(ngx_cycle_t &$cycle)
{
    if ($conf_file = ngx_cfg('ngx_conf_file')) {
        $cycle->conf_file = $conf_file;
    } else {
        $cycle->conf_file = NGX_CONF_PATH;
    }

    //to do test conf path
//    if (ngx_conf_full_name(cycle, &cycle->conf_file, 0) != NGX_OK) {
//    return NGX_ERROR;
//}

    for ($p = strlen($cycle->conf_file) - 1;
         $p >= 0;
         $p--)
array(
        if (ngx_path_separator($cycle->conf_file[$p])) {
            $cycle->conf_prefix = substr($cycle->conf_file,0,$p);
            break;
        }
    }

    if ($conf_params = ngx_cfg('ngx_conf_params')) {
        $cycle->conf_param = $conf_params;
    }

    if (ngx_cfg('ngx_test_config')) {
        $cycle->get_log()->log_level = NGX_LOG_INFO;
    }

    return NGX_OK;
}


function ngx_add_inherited_sockets(ngx_cycle_t $cycle)
{
    $inherited =  getenv(NGINX_VAR);

    if (empty($inherited)) {
        return NGX_OK;
    }

    ngx_log_error(NGX_LOG_NOTICE, $cycle->log, 0,
                  "using inherited sockets from \"%s\"", $inherited);

    for ($p = 0, $v = $p; $inherited[$p]; $p++) {
        if ($inherited[$p] == ':' || $inherited[$p] == ';') {
            $s = ngx_atoi(substr($inherited, $v, $p));
            if ($s == NGX_ERROR) {
                ngx_log_error(NGX_LOG_EMERG, $cycle->log, 0,
                    "invalid socket number \"%s\" in " . NGINX_VAR .
                    " environment variable, ignoring the rest" .
                    " of the variable", $inherited);
                break;
            }

            $v = $p + 1;
            $ls = new ngx_listening_s();
            $ls->fd = $s;
            $cycle->listening = &$ls;
        }
    }

    ngx_cfg('ngx_inherited', 1);

    return ngx_set_inherited_sockets($cycle);
}

function ngx_core_module()
{

   static  $ngx_core_module = array(
    //NGX_MODULE_V1,
    0, 0, 0, 0, 0, 0, 1,
    ngx_core_module_ctx(),                  /* module context */
    ngx_core_commands(),                     /* module directives */
    NGX_CORE_MODULE,                       /* module type */
    NULL,                                  /* init master */
    NULL,                                  /* init module */
    NULL,                                  /* init process */
    NULL,                                  /* init thread */
    NULL,                                  /* exit thread */
    NULL,                                  /* exit process */
    NULL,                                  /* exit master */
    0, 0, 0, 0, 0, 0, 0, 0
//    NGX_MODULE_V1_PADDING
    );
    return $ngx_core_module;
    }

function ngx_core_commands()
{

    static $ngx_core_commands = array(

        array(
            "daemon",
            NGX_MAIN_CONF | NGX_DIRECT_CONF | NGX_CONF_FLAG,
            ngx_conf_set_flag_slot_closure(),
            0,
            //todo if it really need do this
//      //offsetof(ngx_core_conf_t, daemon),
            NULL),

        array("master_process",
            NGX_MAIN_CONF | NGX_DIRECT_CONF | NGX_CONF_FLAG,
            ngx_conf_set_flag_slot_closure(),
            0,
            ////offsetof(ngx_core_conf_t, master),
            NULL),

        array("timer_resolution",
            NGX_MAIN_CONF | NGX_DIRECT_CONF | NGX_CONF_TAKE1,
            ngx_conf_set_msec_slot_closure(),
            0,
            ////offsetof(ngx_core_conf_t, timer_resolution),
            NULL),

        array("pid",
            NGX_MAIN_CONF | NGX_DIRECT_CONF | NGX_CONF_TAKE1,
            ngx_conf_set_str_slot_closure(),
            0,
            //offsetof(ngx_core_conf_t, pid),
            NULL),

        array("lock_file",
            NGX_MAIN_CONF | NGX_DIRECT_CONF | NGX_CONF_TAKE1,
            ngx_conf_set_str_slot_closure(),
            0,
            //offsetof(ngx_core_conf_t, lock_file),
            NULL),

        array("worker_processes",
            NGX_MAIN_CONF | NGX_DIRECT_CONF | NGX_CONF_TAKE1,
            ngx_set_worker_processes_closure(),
            0,
            0,
            NULL),

        array("debug_points",
            NGX_MAIN_CONF | NGX_DIRECT_CONF | NGX_CONF_TAKE1,
            ngx_conf_set_enum_slot_closure(),
            0,
            //offsetof(ngx_core_conf_t, debug_points),
            ngx_debug_points()),

        array("user",
            NGX_MAIN_CONF | NGX_DIRECT_CONF | NGX_CONF_TAKE12,
            ngx_set_user_closure(),
            0,
            0,
            NULL),

        array("worker_priority",
            NGX_MAIN_CONF | NGX_DIRECT_CONF | NGX_CONF_TAKE1,
            ngx_set_priority_closure(),
            0,
            0,
            NULL),

        array("worker_cpu_affinity",
            NGX_MAIN_CONF | NGX_DIRECT_CONF | NGX_CONF_1MORE,
            ngx_set_cpu_affinity_closure(),
            0,
            0,
            NULL),

        array("worker_rlimit_nofile",
            NGX_MAIN_CONF | NGX_DIRECT_CONF | NGX_CONF_TAKE1,
            ngx_conf_set_num_slot_closure(),
            0,
            //offsetof(ngx_core_conf_t, rlimit_nofile),
            NULL),

        array("worker_rlimit_core",
            NGX_MAIN_CONF | NGX_DIRECT_CONF | NGX_CONF_TAKE1,
            ngx_conf_set_off_slot_closure(),
            0,
            //offsetof(ngx_core_conf_t, rlimit_core),
            NULL),

        array("working_directory",
            NGX_MAIN_CONF | NGX_DIRECT_CONF | NGX_CONF_TAKE1,
            ngx_conf_set_str_slot_closure(),
            0,
            //offsetof(ngx_core_conf_t, working_directory),
            NULL),

        array("env",
            NGX_MAIN_CONF | NGX_DIRECT_CONF | NGX_CONF_TAKE1,
            ngx_set_env_closure(),
            0,
            0,
            NULL),
         array( '', 0, NULL, 0, 0, NULL )
    );
    return $ngx_core_commands;

}

function ngx_core_module_ctx(){

    static  $ngx_core_module_ctx = array(
        "core",
    //ngx_core_module_create_conf,
        create_core_module_conf('create'),
    //ngx_core_module_init_conf
        create_core_module_conf('init'),
     );
    return $ngx_core_module_ctx;
}

function create_core_module_conf($type){

    if($type == 'create'){
       return function(ngx_cycle_t $cycle) {
           ngx_core_module_create_conf($cycle);
       };
    }else{
        return function(ngx_cycle_t $cycle, ngx_core_conf_t $conf) {
            ngx_core_module_init_conf($cycle, $conf);
        };
    }
}

function ngx_core_module_create_conf(ngx_cycle_t $cycle)
{
//ngx_core_conf_t  *ccf;
//
//    ccf = ngx_pcalloc(cycle->pool, sizeof(ngx_core_conf_t));
//    if (ccf == NULL) {
//        return NULL;
//    }
    $ccf = new ngx_core_conf_t();

    /*
     * set by ngx_pcalloc()
     *
     *     ccf->pid = NULL;
     *     ccf->oldpid = NULL;
     *     ccf->priority = 0;
     *     ccf->cpu_affinity_n = 0;
     *     ccf->cpu_affinity = NULL;
     */

    $ccf->daemon = NGX_CONF_UNSET;
    $ccf->master = NGX_CONF_UNSET;
    $ccf->timer_resolution = NGX_CONF_UNSET_MSEC;

    $ccf->worker_processes = NGX_CONF_UNSET;
    $ccf->debug_points = NGX_CONF_UNSET;

    $ccf->rlimit_nofile = NGX_CONF_UNSET;
    $ccf->rlimit_core = NGX_CONF_UNSET;

    $ccf->user =  NGX_CONF_UNSET_UINT;
    $ccf->group = NGX_CONF_UNSET_UINT;

//    if (ngx_array_init(&ccf->env, cycle->pool, 1, sizeof(ngx_str_t))
//        != NGX_OK)
//    {
//        return NULL;
//    }

    return $ccf;
}

function ngx_core_module_init_conf(ngx_cycle_t $cycle, ngx_core_conf_t $conf)
{
    $ccf = $conf;

    ngx_conf_init_value($ccf->daemon, 1);
    ngx_conf_init_value($ccf->master, 1);
    ngx_conf_init_msec_value($ccf->timer_resolution, 0);

    ngx_conf_init_value($ccf->worker_processes, 1);
    ngx_conf_init_value($ccf->debug_points, 0);

    //todo how to use php find cpu-affinity
//#if (NGX_HAVE_CPU_AFFINITY)
//
//    if (ccf->cpu_affinity_n
//&& ccf->cpu_affinity_n != 1
//&& ccf->cpu_affinity_n != (ngx_uint_t) ccf->worker_processes)
//    {
//        ngx_log_error(NGX_LOG_WARN, cycle->log, 0,
//                      "the number of \"worker_processes\" is not equal to "
//                      "the number of \"worker_cpu_affinity\" masks, "
//                      "using last mask for remaining worker processes");
//    }
//
//#endif


    //todo find NGX_PID_PATH
    if (empty($ccf->pid)) {
        $ccf->pid = NGX_PID_PATH;
    }

    //todo how to save the pid file
    if (ngx_conf_full_name($cycle, $ccf->pid, 0) != NGX_OK) {
          return NGX_CONF_ERROR;
       }

        $ccf->oldpid = $ccf->pid.NGX_OLDPID_EXT;

    if ($ccf->user == NGX_CONF_UNSET_UINT && posix_geteuid() == 0) {

        $pwd = posix_getpwnam(NGX_USER);
        if (empty($pwd)) {
            ngx_log_error(NGX_LOG_EMERG, $cycle->log, posix_get_last_error(),
                          "getpwnam(\"". NGX_USER ."\") failed");
            return NGX_CONF_ERROR;
        }

        $ccf->username = NGX_USER;
        $ccf->user = $pwd['uid'];

        //ngx_set_errno(0);
        $grp = posix_getgrnam(NGX_GROUP);
        if (empty($grp)) {
            ngx_log_error(NGX_LOG_EMERG, $cycle->log, posix_get_last_error(),
                          "getgrnam(\"" .NGX_GROUP ."\") failed");
            return NGX_CONF_ERROR;
        }

        $ccf->group = $grp['gid'];
    }


    if ($ccf->lock_file == '') {
          $ccf->lock_file = NGX_LOCK_PATH;
        }

    if (ngx_conf_full_name($cycle, $ccf->lock_file, 0) != NGX_OK) {
        return NGX_CONF_ERROR;
        }

    {
    $lock_file = $cycle->old_cycle->lock_file;

    if ($lock_file) {
        //todo find why should --
        //lock_file.len--;

        if ($ccf->lock_file= $lock_file
        || ngx_strcmp($ccf->lock_file, $lock_file)
               != 0)
        {
            ngx_log_error(NGX_LOG_EMERG, $cycle->log, 0,
                          "\"lock_file\" could not be changed, ignored");
        }

        //todo why should add .accept
        //lock_file.len += sizeof(".accept");

        $cycle->lock_file= $lock_file;

    } else {

        $cycle->lock_file = $ccf->lock_file.'.accept';

    }
    }

    return NGX_CONF_OK;
}

function ngx_set_worker_processes_closure(){
    return function(ngx_conf_t $cf, ngx_command_t $cmd, $conf){
               ngx_set_worker_processes($cf,$cmd,$conf);
    };
}

function ngx_set_worker_processes(ngx_conf_t $cf, ngx_command_t $cmd, $conf)
{
//ngx_str_t        *value;
//    ngx_core_conf_t  *ccf;

    $ccf =  $conf;

    if ($ccf->worker_processes != NGX_CONF_UNSET) {
    return "is duplicate";
    }

    $value = $cf->args;

    $ngx_ncpu = ngx_cfg('ngx_ncpu',1);

    if (ngx_strcmp($value[1], "auto") == 0) {
       $ccf->worker_processes = $ngx_ncpu;
        return NGX_CONF_OK;
    }

    $ccf->worker_processes = ngx_atoi($value[1]);
    if ($ccf->worker_processes == NGX_ERROR) {
    return "invalid value";
}
    return NGX_CONF_OK;
}

function  ngx_debug_points(){
    static $ngx_debug_points = array(
        array( "stop", NGX_DEBUG_POINTS_STOP ),
        array( "abort", NGX_DEBUG_POINTS_ABORT ),
        array( '', 0 )
    );
    return $ngx_debug_points;
}

function ngx_set_user_closure(){
    return function(ngx_conf_t $cf, ngx_command_t $cmd, $conf){
        ngx_set_user($cf,$cmd,$conf);
    };
}

function ngx_set_user(ngx_conf_t $cf, ngx_command_t $cmd, $conf)
{

    /**ngx_core_conf_t**/  $ccf = $conf;

//    char             *group;
//    struct passwd    *pwd;
//    struct group     *grp;
//    ngx_str_t        *value;

    if ($ccf->user != NGX_CONF_UNSET_UINT) {
        return "is duplicate";
       }

    if (posix_geteuid() != 0) {
        ngx_conf_log_error(NGX_LOG_WARN, $cf, 0,
            "the \"user\" directive makes sense only ".
                           "if the master process runs ".
                           "with super-user privileges, ignored");
        return NGX_CONF_OK;
    }

    $value = $cf->args;

    $ccf->username = $value[1];

    $pwd = posix_getpwnam($value[1]);
    if ($pwd == NULL) {
        ngx_conf_log_error(NGX_LOG_EMERG, $cf, posix_get_last_error(),
            "getpwnam(\"%s\") failed", $value[1]);
        return NGX_CONF_ERROR;
    }

    $ccf->user = $pwd['uid'];

    $group = count($cf->args) == 2 ? $value[1] : $value[2];

    //ngx_set_errno(0);
    $grp = posix_getgrnam($group);
    if ($grp == NULL) {
        ngx_conf_log_error(NGX_LOG_EMERG, $cf, posix_get_last_error(),
            "getgrnam(\"%s\") failed", $group);
        return NGX_CONF_ERROR;
    }

    $ccf->group = $grp->['gid'];

    return NGX_CONF_OK;
}

function ngx_set_priority_closure(){
    return function(ngx_conf_t $cf, ngx_command_t $cmd, $conf){
        ngx_set_priority($cf,$cmd,$conf);
    };
}

function ngx_set_priority(ngx_conf_t $cf, ngx_command_t $cmd, $conf)
{
/**ngx_core_conf_t***/  $ccf = $conf;

//    ngx_str_t        *value;
//    ngx_uint_t        n, minus;

    if ($ccf->priority != 0) {
        return "is duplicate";
      }

    $value = $cf->args;

    if ($value[1][0] == '-') {
        $n = 1;
        $minus = 1;

    } else if ($value[1][0] == '+') {
        $n = 1;
        $minus = 0;

    } else {
        $n = 0;
        $minus = 0;
    }

    $ccf->priority = ngx_atoi($value[1][$n]);
    if ($ccf->priority == NGX_ERROR) {
    return "invalid number";
    }
    if ($minus) {
        $ccf->priority = -$ccf->priority;
    }

    return NGX_CONF_OK;
}

function ngx_set_cpu_affinity_closure(){
   return function(ngx_conf_t $cf, ngx_command_t $cmd, $conf){
       ngx_set_cpu_affinity($cf, $cmd, $conf);
   } ;
}

function ngx_set_cpu_affinity(ngx_conf_t $cf, ngx_command_t $cmd, $conf)
{
    ngx_conf_log_error(NGX_LOG_WARN, $cf, 0,
        "\"worker_cpu_affinity\" is not supported ".
                       "on this platform, ignored");

    return NGX_CONF_OK;
}

function ngx_set_env_closure(){
    return function(ngx_conf_t $cf, ngx_command_t $cmd, $conf){
        ngx_set_env($cf,  $cmd, $conf);
    };
}

function ngx_set_env(ngx_conf_t $cf, ngx_command_t $cmd, $conf)
{
/**ngx_core_conf_t**/  $ccf = $conf;

//    ngx_str_t   *value, *var;
//    ngx_uint_t   i;

//    var = ngx_array_push(&ccf->env);

    $value = $cf->args;
    $ccf->env[] = $value[1];
    //*var = $value[1];

    for ($i = 0; $i < strlen($value[1]); $i++) {

        if ($value[1][$i] == '=') {

            $ccf->env[] = substr($value[1],0,$i);

            return NGX_CONF_OK;
        }
    }
    return NGX_CONF_OK;
}
//main($argc,$argv);




