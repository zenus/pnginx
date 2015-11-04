<?php
include_once 'ngx_core.php';
include_once 'ngx_log.php';
include_once 'ngx_string.php';
include_once 'ngx_process_cycle.php';
include_once 'ngx_cycle.php';

define('DS',DIRECTORY_SEPARATOR);
define('nginx_version',1000000);
define('NGINX_VERSION',"1.0.0");
define('NGINX_VER',"nginx/".NGINX_VERSION);
define('NGINX_VER_BUILD',NGINX_VER);
define('NGINX_VAR',"NGINX");
define('NGX_OLDPID_EXT',".oldbin");
define('NGX_CONF_PATH',__DIR__.DS.'conf'.DS.'pnginx.conf');
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

function ngx_cfg($ngx_cfg,$value = null){
    static $cfg = array();
    return  is_null($value) ? (isset($cfg[$ngx_cfg]) ? $cfg[$ngx_cfg] : false) : $cfg[$ngx_cfg] = $value;
}
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


    /***
     * TODO
    if (ngx_strerror_init() != NGX_OK) {
        return 1;
    }
     * */

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


main($argc,$argv);




