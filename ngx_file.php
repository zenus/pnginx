<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 15-11-15
 * Time: 下午8:19
 */
define('S_IRUSR',0400);
define('S_IWUSR',0200);
define('S_IXUSR',0100);

define('S_IRGRP',(S_IRUSR >> 3));  /* Read by group.  */
define('S_IWGRP',(S_IWUSR >> 3));  /* Write by group.  */
define('S_IROTH',(S_IRGRP >> 3));  /* Read by others.  */
define('S_IWOTH',(S_IWGRP >> 3));  /* Write by others.  */
//S_IRGRP|S_IWGRP|S_IROTH|S_IWOTH)
class ngx_file_t {
/** ngx_fd_t **/ private $fd;
/** ngx_str_t **/ private $name;
/** ngx_file_info_t **/ private $info;
/** off_t **/ private $offset;
/** off_t **/ private $sys_offset;
/** ngx_log_t **/ private $log;


#if (NGX_HAVE_FILE_AIO)
/** ngx_event_aio_t **/ private $aio;
#endif
/** unsigned **/ private $valid_info;
/** unsigned **/ private $directio;

    public function __set($property,$value){
       $this->$property = $value;
    }
    public function __get($property){
       return $this->$property;
    }
}

function ngx_test_full_name($name)
{

    if ($name[0] == '/') {
        return NGX_OK;
    }
    return NGX_DECLINED;
}
function ngx_get_full_name($prefix, &$name)
{

    $rc = ngx_test_full_name($name);

    if ($rc == NGX_OK) {
        return $rc;
    }

    $name = $prefix.$name;

    return NGX_OK;
}
function ngx_create_paths(ngx_cycle_t $cycle,  $user)
{
//    ngx_err_t         err;
//    ngx_uint_t        i;
//    ngx_path_t      **path;

    $path = $cycle->paths;
    for ($i = 0; $i < $cycle->paths; $i++) {

    if (ngx_create_dir($path[$i]->name, 0700) == false) {
        //$err = ngx_errno;
            ngx_log_error(NGX_LOG_EMERG, $cycle->log, NGX_DCERROR,
                              ngx_create_dir_n. " \"%s\" failed",
                              $path[$i]->name);
                return NGX_ERROR;
    }

        if ($user ==  NGX_CONF_UNSET_UINT) {
        continue;
     }
        {
        //    ngx_file_info_t   fi;


        if ($fi = ngx_file_info($path[$i]->name)
            == NGX_FILE_ERROR)
        {
            ngx_log_error(NGX_LOG_EMERG, $cycle->log, NGX_FIERROR,
                          ngx_file_info_n ." \"%s\" failed", $path[$i]->name);
            return NGX_ERROR;
        }

        if ($fi['uid'] != $user) {
            if (chown($path[$i]->name, $user) == false) {
                ngx_log_error(NGX_LOG_EMERG, $cycle->log, NGX_FCNERROR,
                              "chown(\"%s\", %d) failed",
                              array($path[$i]->name, $user));
                return NGX_ERROR;
            }
        }

        if (($fi['mode'] & (S_IRUSR|S_IWUSR|S_IXUSR))
            != (S_IRUSR|S_IWUSR|S_IXUSR))
        {
            $fi['mode'] |= (S_IRUSR|S_IWUSR|S_IXUSR);

            if (chmod($path[$i]->name, $fi['mode']) == -1) {
                ngx_log_error(NGX_LOG_EMERG, $cycle->log, NGX_FCNERROR,
                                  "chmod() \"%s\" failed", $path[$i]->name);
                return NGX_ERROR;
            }
        }
        }
    }
    return NGX_OK;
}



