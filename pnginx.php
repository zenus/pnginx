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


/**
 * Created by PhpStorm.
 * User: zenus@github.com
 * Date: 2015/11/2
 * Time: 14:56
 * @param null $i
 * @return int
 * @internal param $value
 * @internal param $name
 * @internal param $argc
 * @internal param array $argv
 */

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

ini_set('include_path', get_include_path()
    . ':' .dirname(__FILE__)
);
$dir = dirname(__FILE__);
foreach(scandir(dirname(__FILE__)) as $file){
    $path = $dir.'/'.$file;
    $info = pathinfo($path);
    if($info['extension'] == 'php' && $info['filename'] != 'pnginx'){
        require_once $dir.'/'.$file;
    }
}




function main($argc, array $argv){

    /***ngx_log_s***/ $log = null;

    /***ngx_cycle_s***/ $cycle = null;
    $init_cycle = null;

    if (ngx_strerror_init() != NGX_OK) {
        return 1;
    }

    if(ngx_get_options($argc,$argv) != NGX_OK) {
       return 1;
    }
    ngx_write_stderr("nginx version: ". NGINX_VER_BUILD. NGX_LINEFEED);

        if (ngx_show_help()) {
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

        if (ngx_show_configure()) {

            ngx_write_stderr("configure arguments:" .NGX_CONFIGURE .NGX_LINEFEED);
        }

        if (ngx_test_config()) {
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

    ngx_pid(ngx_getpid());
    $log = ngx_log_init();
    if($log == null){
        return 1;
    }
    //TODO
    //ngx_ssl_init();
    $init_cycle = new ngx_cycle_t();
    $init_cycle->log = $log;
    ngx_cycle($init_cycle);
    //$ngx_cycle = &$init_cycle;
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
    for ($i = 0; ngx_modules($i); $i++) {
        ngx_modules($i,'index',$ngx_max_module++);
    }
    $cycle = ngx_init_cycle($init_cycle);
    if ($cycle == NULL) {
        if (ngx_test_config()) {
            ngx_log_stderr(0, "configuration file %s test failed",
                $init_cycle->conf_file);
        }

        return 1;
    }

    if (ngx_test_config()) {
        if (!ngx_quiet_mode()) {
            ngx_log_stderr(0, "configuration file %s test is successful",
                $cycle->conf_file);
        }

        if (ngx_dump_config()) {
            $cd = $cycle->config_dump;

            for ($i = 0; $i < count($cycle->config_dump); $i++) {

                ngx_write_stdout("# configuration file ");
                ngx_write_fd(ngx_stdout, $cd[$i]->name);
                ngx_write_stdout(":". NGX_LINEFEED);

                $b = $cd[$i]->buffer;

                //todo should have a good method to deal  with  ngx_buf_t struct
                 ngx_write_fd(ngx_stdout, $b->pos);
                ngx_write_stdout(NGX_LINEFEED);
            }
        }

        return 0;
    }


    if ($ngx_signal = ngx_signal()) {
        return ngx_signal_process($cycle, $ngx_signal);
    }

    ngx_os_status($cycle->log);

    ngx_cycle($cycle);

    $ccf =  ngx_get_conf($cycle->conf_ctx, ngx_core_module());


    if ($ccf->master && ngx_process() == NGX_PROCESS_SINGLE) {
        ngx_process(NGX_PROCESS_MASTER);
    }


    if (ngx_init_signals($cycle->log) != NGX_OK) {
        return 1;
    }

    if (!ngx_inherited() && $ccf->daemon) {
        if (ngx_daemon($cycle->log) != NGX_OK) {
            return 1;
        }

        ngx_daemonized(1);
    }

    if (ngx_inherited()) {
        ngx_daemonized(1);
    }


    if (ngx_create_pidfile($ccf->pid, $cycle->log) != NGX_OK) {
        return 1;
    }

    if (ngx_log_redirect_stderr($cycle) != NGX_OK) {
        return 1;
    }

    if ($log->file->fd != ngx_stderr) {
        if (ngx_close_file($log->file->fd) == NGX_FILE_ERROR) {
            ngx_log_error(NGX_LOG_ALERT, $cycle->log, NGX_FCERROR,
                          ngx_close_file_n ." built-in log failed");
        }
    }

    ngx_use_stderr(0);

    if (ngx_process() == NGX_PROCESS_SINGLE) {
        ngx_single_process_cycle($cycle);

    } else {
        ngx_master_process_cycle($cycle);
    }

    return 0;

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
                    ngx_show_version(1);
                    ngx_show_help(1);
                    break;

                case 'v':
                    ngx_show_version(1);
                    break;

                case 'V':
                    ngx_show_version(1);
                    ngx_show_configure(1);
                    break;

                case 't':
                    ngx_test_config(1);
                    break;

                case 'T':
                    ngx_test_config(1);
                    ngx_dump_config(1);
                    break;

                case 'q':
                    ngx_quiet_mode(1);
                    break;

                case 'p':
                    if (ngx_strhas($p,$ptr)) {
                        ngx_prefix(substr($p,$ptr));
                        goto next;
                    }

                    if ($argv[++$i]) {
                        ngx_prefix($argv[$i]);
                        goto next;
                    }

                    ngx_log_stderr(0, "option \"-p\" requires directory name");
                    return NGX_ERROR;

                case 'c':
                    if (ngx_strhas($p,$ptr)) {
                        ngx_conf_file(substr($p,$ptr));
                        goto next;
                    }

                    if ($argv[++$i]) {
                        ngx_conf_file($argv[$i]);
                        goto next;
                    }

                    ngx_log_stderr(0, "option \"-c\" requires file name");
                    return NGX_ERROR;

                case 'g':
                    if (ngx_strhas($p,$ptr)) {
                        ngx_conf_params(substr($p,$ptr));
                        goto next;
                    }

                    if ($argv[++$i]) {
                        ngx_conf_params($argv[$i]);
                        goto next;
                    }

                    ngx_log_stderr(0, "option \"-g\" requires parameter");
                    return NGX_ERROR;

                case 's':
                    if (ngx_strhas($p,$ptr)) {
                        ngx_signal(substr($p,$ptr));

                    } else if ($argv[++$i]) {
                        ngx_signal($argv[$i]);

                    } else {
                        ngx_log_stderr(0, "option \"-s\" requires parameter");
                        return NGX_ERROR;
                    }

                    if (ngx_strcmp(ngx_signal(), "stop") == 0
                        || ngx_strcmp(ngx_signal(), "quit") == 0
                        || ngx_strcmp(ngx_signal(), "reopen") == 0
                        || ngx_strcmp(ngx_signal(), "reload") == 0
                    ) {
                        ngx_process(NGX_PROCESS_SIGNALLER);
                        goto next;
                    }

                    ngx_log_stderr(0, "invalid option: \"-s %s\"", ngx_signal());
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

    ngx_os_argv($argv);
    ngx_argc($argc);
    ngx_argv($argv);

    if (empty($argv)) {
        return NGX_ERROR;
    }

    //todo
    ngx_os_environ(environ());

    return NGX_OK;
}


function ngx_process_options(ngx_cycle_t &$cycle)
{
    $conf_file = ngx_conf_file();
    if ($conf_file) {
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
    {
        if (ngx_path_separator($cycle->conf_file[$p])) {
            $cycle->conf_prefix = substr($cycle->conf_file,0,$p);
            break;
        }
    }

    if ($conf_params = ngx_conf_params()) {
        $cycle->conf_param = $conf_params;
    }

    if (ngx_test_config()) {
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

    ngx_inherited(1);

    return ngx_set_inherited_sockets($cycle);
}

function ngx_core_module()
{
    static $ngx_core_module ;
    $ngx_core_module = new ngx_module_t();
    $ngx_core_module->version = 1;
    $ngx_core_module->ctx = ngx_core_module_ctx();
    $ngx_core_module->commands = ngx_core_commands();
    $ngx_core_module->type = NGX_CORE_MODULE;
    return $ngx_core_module;
//   static  $ngx_core_module = array(
//    //NGX_MODULE_V1,
//    'ctx_index'=>0,
//    'index'=>0,
//    'spare0'=>0,
//    'spare1'=>0,
//    'spare2'=>0,
//    'spare3'=>0,
//    'version'=>1,
//    'ctx'=>ngx_core_module_ctx(),                  /* module context */
//    'commands'=>ngx_core_commands(),                     /* module directives */
//    'type'=>NGX_CORE_MODULE,                       /* module type */
//    'init_master'=>NULL,                                  /* init master */
//    'init_module'=>NULL,                                  /* init module */
//    'init_process'=>NULL,                                  /* init process */
//    'init_thread'=>NULL,                                  /* init thread */
//    'exit_thread'=>NULL,                                  /* exit thread */
//    'exit_process'=>NULL,                                  /* exit process */
//    'exit_master'=>NULL,                                  /* exit master */
//    'spare_hook0'=>0,
//    'spare_hook1'=>0,
//    'spare_hook2'=>0,
//    'spare_hook3'=>0,
//    'spare_hook4'=>0,
//    'spare_hook5'=>0,
//    'spare_hook6'=>0,
//    'spare_hook7'=>0
//    );
    }

function ngx_core_commands()
{

     $ngx_core_commands = array(
        array(
            'name'=>"daemon",
            'type'=>NGX_MAIN_CONF|NGX_DIRECT_CONF|NGX_CONF_FLAG,
            'set'=>'ngx_conf_set_flag_slot',
            'conf'=>0,
            //todo if it really need do this
//      //offsetof(ngx_core_conf_t, daemon),
           'post'=> NULL
        ),

        array(
           'name'=> "master_process",
            'type'=>NGX_MAIN_CONF | NGX_DIRECT_CONF | NGX_CONF_FLAG,
            'set'=>'ngx_conf_set_flag_slot',
            'conf'=>0,
            ////offsetof(ngx_core_conf_t, master),
          'post'=>NULL
        ),

        array(
            'name'=>"timer_resolution",
            'type'=>NGX_MAIN_CONF | NGX_DIRECT_CONF | NGX_CONF_TAKE1,
            'set'=>'ngx_conf_set_msec_slot',
            'conf'=>0,
            ////offsetof(ngx_core_conf_t, timer_resolution),
            'post'=>NULL
        ),

        array(
            'name'=>"pid",
            'type'=>NGX_MAIN_CONF | NGX_DIRECT_CONF | NGX_CONF_TAKE1,
            'set'=>'ngx_conf_set_str_slot',
            'conf'=>0,
            //offsetof(ngx_core_conf_t, pid),
           'post'=>NULL
        ),

        array(
            'name'=>"lock_file",
            'type'=>NGX_MAIN_CONF | NGX_DIRECT_CONF | NGX_CONF_TAKE1,
            'set'=>'ngx_conf_set_str_slot',
            'conf'=>0,
            //offsetof(ngx_core_conf_t, lock_file),
            'post'=>NULL
        ),

        array(
            'name'=>"worker_processes",
            'type'=>NGX_MAIN_CONF | NGX_DIRECT_CONF | NGX_CONF_TAKE1,
            'set'=>'ngx_set_worker_processes',
            'conf'=>0,
            //0,
            'post'=>NULL
        ),

        array(
            'name'=>"debug_points",
            'type'=>NGX_MAIN_CONF | NGX_DIRECT_CONF | NGX_CONF_TAKE1,
            'set'=>'ngx_conf_set_enum_slot',
            'conf'=>0,
            //offsetof(ngx_core_conf_t, debug_points),
           'post'=>ngx_debug_points()
        ),

        array(
            'name'=>"user",
            'type'=>NGX_MAIN_CONF | NGX_DIRECT_CONF | NGX_CONF_TAKE12,
            'set'=>'ngx_set_user',
            'conf'=>0,
           // 0,
           'post'=>NULL
        ),

        array(
            'name'=>"worker_priority",
            'type'=>NGX_MAIN_CONF | NGX_DIRECT_CONF | NGX_CONF_TAKE1,
            'set'=>'ngx_set_priority',
            'conf'=>0,
            //0,
            'post'=>NULL
        ),

        array(
           'name'=>"worker_cpu_affinity",
            'type'=>NGX_MAIN_CONF | NGX_DIRECT_CONF | NGX_CONF_1MORE,
            'set'=>'ngx_set_cpu_affinity',
            'conf'=>0,
           // 0,
           'post'=>NULL
        ),

        array(
           'name'=>"worker_rlimit_nofile",
            'type'=>NGX_MAIN_CONF | NGX_DIRECT_CONF | NGX_CONF_TAKE1,
            'set'=>'ngx_conf_set_num_slot',
            'conf'=>0,
            //offsetof(ngx_core_conf_t, rlimit_nofile),
            'post'=>NULL
        ),

        array(
            'name'=>"worker_rlimit_core",
            'type'=>NGX_MAIN_CONF | NGX_DIRECT_CONF | NGX_CONF_TAKE1,
            'set'=>'ngx_conf_set_off_slot',
            'conf'=>0,
            //offsetof(ngx_core_conf_t, rlimit_core),
           'post'=>NULL
        ),

        array(
            'name'=>"working_directory",
            'type'=>NGX_MAIN_CONF | NGX_DIRECT_CONF | NGX_CONF_TAKE1,
            'set'=>'ngx_conf_set_str_slot',
            'conf'=>0,
            //offsetof(ngx_core_conf_t, working_directory),
            'post'=>NULL
        ),

        array(
            'name'=>"env",
            'type'=>NGX_MAIN_CONF | NGX_DIRECT_CONF | NGX_CONF_TAKE1,
            'set'=>'ngx_set_env',
            'conf'=>0,
            //0,
            'post'=>NULL
        ),
         array(
             'name'=>'',
             'type'=>0,
             'set'=>NULL,
             'conf'=>0,
            // 0,
            'post'=>NULL
         )
    );
    return $ngx_core_commands;
}

function ngx_core_module_ctx(){

    static $ngx_core_module_ctx ;
    if(!is_null($ngx_core_module_ctx)){
        $obj = new ngx_core_module_t();
        $ngx_core_module_ctx = $obj;
        $ngx_core_module_ctx->name = 'core';
        $ngx_core_module_ctx->create_conf = create_core_module_conf('create');
        $ngx_core_module_ctx->init_conf = create_core_module_conf('init');
    }
//    static  $ngx_core_module_ctx = array(
//      'name'=>"core",
//      'create_conf'=>create_core_module_conf('create'),
//       'init_conf'=>create_core_module_conf('init'),
//     );
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

//function ngx_set_worker_processes_closure(){
//    return function(ngx_conf_t $cf, ngx_command_t $cmd, $conf){
//               ngx_set_worker_processes($cf,$cmd,$conf);
//    };
//}


function ngx_os_environ($var = null){
    static $ngx_os_environ = null;
    if(!is_null($var)){
        if(is_array($var)){
            $ngx_os_environ = $var;
        }elseif(is_string($var)){
           $ngx_os_environ[] = $var;
        }
    }else{
       return $ngx_os_environ;
    }
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


    if (ngx_strcmp($value[1], "auto") == 0) {
       $ccf->worker_processes = ngx_ncpu();
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

//function ngx_set_user_closure(){
//    return function(ngx_conf_t $cf, ngx_command_t $cmd, $conf){
//        ngx_set_user($cf,$cmd,$conf);
//    };
//}

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

    $ccf->group = $grp['gid'];

    return NGX_CONF_OK;
}

//function ngx_set_priority_closure(){
//    return function(ngx_conf_t $cf, ngx_command_t $cmd, $conf){
//        ngx_set_priority($cf,$cmd,$conf);
//    };
//}

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

//function ngx_set_cpu_affinity_closure(){
//   return function(ngx_conf_t $cf, ngx_command_t $cmd, $conf){
//       ngx_set_cpu_affinity($cf, $cmd, $conf);
//   } ;
//}

function ngx_set_cpu_affinity(ngx_conf_t $cf, ngx_command_t $cmd, $conf)
{
    ngx_conf_log_error(NGX_LOG_WARN, $cf, 0,
        "\"worker_cpu_affinity\" is not supported ".
                       "on this platform, ignored");

    return NGX_CONF_OK;
}

//function ngx_set_env_closure(){
//    return function(ngx_conf_t $cf, ngx_command_t $cmd, $conf){
//        ngx_set_env($cf,  $cmd, $conf);
//    };
//}

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

main($argc,$argv);

function ngx_conf_params($s = null){
   static $ngx_conf_params = null;
    if(!is_null($s)){
       $ngx_conf_params = $s;
    }else{
       return $ngx_conf_params;
    }
}

function ngx_set_environment(ngx_cycle_t $cycle, $last)
{
//char             **p, **env;
    $env = null;
//    ngx_str_t         *var;
//    ngx_uint_t         i, n;
//    ngx_core_conf_t   *ccf;

    $ccf =  ngx_get_conf($cycle->conf_ctx, ngx_core_module());

    if ($last == NULL && !empty($ccf->environment)) {
        return $ccf->environment;
    }

    $var = $ccf->env;

    for ($i = 0; $i < count($ccf->env); $i++) {
        if (ngx_strcmp($var[$i], "TZ") == 0
        || ngx_strncmp($var[$i], "TZ=", 3) == 0)
            {
                goto tz_found;
            }
    }

//    var = ngx_array_push(&ccf->env);
//    if (var == NULL) {
//    return NULL;
//}

//    var->len = 2;
//    var->data = (u_char *) "TZ";
    $ccf->env[] = 'TZ';
    $var = $ccf->env;


tz_found:

    $n = 0;

    for ($i = 0; $i < count($ccf->env); $i++) {

        if ($var[$i][strlen($var[$i])] == '=') {
            $n++;
            continue;
        }

        for ($j=0,$p = ngx_os_environ(); $p[$j]; $j++) {

            if (ngx_strncmp($p[$j], $var[$i], strlen($var[$i])) == 0
            && $p[strlen($var[$i])] == '=')
                {
                    $n++;
                    break;
                }
        }
    }

    if ($last) {
        //env = ngx_alloc((*last + n + 1) * sizeof(char *), cycle->log);
        $last = $n;

    } else {
        //env = ngx_palloc(cycle->pool, (n + 1) * sizeof(char *));
    }

//    if ($env == NULL) {
//        return NULL;
//    }

    $n = 0;

    for ($i = 0; $i < count($ccf->env); $i++) {

    if ($var[$i][strlen($var[$i])] == '=') {
            $env[$n++] = $var[$i];
            continue;
        }

        for ($j=0,$p = ngx_os_environ(); $p[$j]; $j++) {

            if (ngx_strncmp($p[$j], $var[$i], strlen($var[$i])) == 0
            && $p[strlen($var[$i])] == '=')
                {
                    $env[$n++] = $p[$j];
                    break;
                }
        }
    }

    $env[$n] = NULL;

    if ($last == NULL) {
        $ccf->environment = $env;
        environ($env);
    }

    return $env;
}

function ngx_get_cpu_affinity($n)
{
//    ngx_core_conf_t  *ccf;

    $ngx_cycle = ngx_cycle();
    $ccf = ngx_get_conf($ngx_cycle->conf_ctx, ngx_core_module());

    if($ccf->cpu_affinity == NULL) {
        return 0;
      }

    if ($ccf->cpu_affinity_n > $n) {
        return $ccf->cpu_affinity[$n];
    }

    return $ccf->cpu_affinity[$ccf->cpu_affinity_n - 1];
}

function ngx_exec_new_binary(ngx_cycle_t $cycle, $argv)
{
//    char             **env, *var;
//    u_char            *p;
//    ngx_uint_t         i, n;
//    ngx_pid_t          pid;
//    ngx_exec_ctx_t     ctx;
//    ngx_core_conf_t   *ccf;
//    ngx_listening_t   *ls;

    //ngx_memzero(&ctx, sizeof(ngx_exec_ctx_t));
    $ctx = new ngx_exec_ctx_t();
    $ctx->path = $argv[0];
    $ctx->name = "new binary process";
    $ctx->argv = $argv;

    $n = 2;
    $env = ngx_set_environment($cycle, $n);
    if ($env == NULL) {
        return NGX_INVALID_PID;
    }

//    var = ngx_alloc(sizeof(NGINX_VAR)
//    + cycle->listening.nelts * (NGX_INT32_LEN + 1) + 2,
//                    cycle->log);
//    if (var == NULL) {
//    ngx_free(env);
//    return NGX_INVALID_PID;
//    }

    //p = ngx_cpymem(var, NGINX_VAR "=", sizeof(NGINX_VAR));
    $p = NGINX_VAR.'=';

    $ls = $cycle->listening;
    for ($i = 0; $i < count($cycle->listening); $i++) {
        $p = ngx_sprintf($p, "%ud;", $ls[$i]->fd);
    }

    $env[$n++] = $p;

    $ctx->envp = $env;

    $ccf = ngx_get_conf($cycle->conf_ctx, ngx_core_module());

    if (ngx_rename_file($ccf->pid, $ccf->oldpid) == NGX_FILE_ERROR) {
        ngx_log_error(NGX_LOG_ALERT, $cycle->log, NGX_FRNERROR,
                          ngx_rename_file_n ." %s to %s failed ".
                          "before executing new binary process \"%s\"",
                          array($ccf->pid, $ccf->oldpid, $argv[0]));

        unset($env);
        unset($p);

        return NGX_INVALID_PID;
    }

    $pid = ngx_execute($cycle, $ctx);

    if ($pid == NGX_INVALID_PID) {
        if (ngx_rename_file($ccf->oldpid, $ccf->pid)
            == NGX_FILE_ERROR)
        {
            ngx_log_error(NGX_LOG_ALERT, $cycle->log, NGX_FRNERROR,
                          ngx_rename_file_n ." %s back to %s failed after ".
                          "an attempt to execute new binary process \"%s\"",
                          array($ccf->oldpid, $ccf->pid, $argv[0]));
        }
    }
    unset($env);
    unset($p);
    return $pid;
}

function ngx_show_help($i = null){
    static $ngx_show_help = null;
    if(!is_null($i)){
        $ngx_show_help = $i;
    }else{
        return $ngx_show_help;
    }
}

function ngx_show_version($i = null){
    static $ngx_show_version = null;
    if(!is_null($i)){
        $ngx_show_version = $i;
    }else{
        return $ngx_show_version;
    }
}

function ngx_show_configure($i = null){
    static $ngx_show_configure = null;
    if(!is_null($i)){
        $ngx_show_configure = $i;
    }else{
        return $ngx_show_configure;
    }
}

function ngx_signal($char = null){
    static $ngx_signal = null;
    if(!is_null($char)){
        $ngx_signal = $char;
    }else{
        return $ngx_signal;
    }
}

function ngx_prefix($char = null){
    static $ngx_prefix = null;
    if(!is_null($char)){
        $ngx_prefix = $char;
    }else{
        return $ngx_prefix;
    }
}

function ngx_conf_file($char = null){
    static $ngx_conf_file = null;
    if(!is_null($ngx_conf_file)){
        $ngx_conf_file = $char;
    }else{
        return $ngx_conf_file;
    }
}

function ngx_pid($i = null){
    static $ngx_pid = null;
    if(!is_null($i)){
        $ngx_pid = $i;
    }else{
        return $ngx_pid;
    }

}

function ngx_os_argv($arr = null ){
    static $ngx_os_argv = null;
    if(!is_null($arr)){
        $ngx_os_argv = $arr;
    }else{
        return $ngx_os_argv;
    }
}

function ngx_argc($i = null){
    static $ngx_argc = null;
    if(!is_null($i)){
        $ngx_argc = $i;
    }else{
        return $ngx_argc;
    }
}

function ngx_argv($arr = null){
    static $ngx_argv = null;
    if(!is_null($arr)){
        $ngx_argv = $arr;
    }else{
        return $ngx_argv;
    }
}





