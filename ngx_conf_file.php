<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 15-11-12
 * Time: 下午10:26
 */
//
define('NGX_CORE_MODULE',0x45524F43);  /* "CORE" */
define('NGX_CONF_MODULE',0x464E4F43);  /* "CONF" */
define('NGX_CONF_UNSET',       -1);
define('NGX_CONF_UNSET_UINT',  -1);
define('NGX_CONF_UNSET_PTR',    -1);
define('NGX_CONF_UNSET_SIZE',  -1);
define('NGX_CONF_UNSET_MSEC', -1) ;


define('NGX_CONF_OK',NULL);
define('NGX_CONF_ERROR',-1);

define('NGX_CONF_NOARGS',0x00000001);
define('NGX_CONF_TAKE1',0x00000002);
define('NGX_CONF_TAKE2',0x00000004);
define('NGX_CONF_TAKE3',0x00000008);
define('NGX_CONF_TAKE4',0x00000010);
define('NGX_CONF_TAKE5',0x00000020);
define('NGX_CONF_TAKE6',0x00000040);
define('NGX_CONF_TAKE7',0x00000080);

define('NGX_CONF_TAKE12',(NGX_CONF_TAKE1|NGX_CONF_TAKE2));
define('NGX_CONF_TAKE13',(NGX_CONF_TAKE1|NGX_CONF_TAKE3));

define('NGX_CONF_TAKE23',(NGX_CONF_TAKE2|NGX_CONF_TAKE3));
define('NGX_CONF_TAKE123',(NGX_CONF_TAKE1|NGX_CONF_TAKE2|NGX_CONF_TAKE3));
define('NGX_CONF_TAKE1234',(NGX_CONF_TAKE1|NGX_CONF_TAKE2|NGX_CONF_TAKE3|NGX_CONF_TAKE4));

define('NGX_CONF_MAX_ARGS',8);

define('NGX_CONF_BLOCK_START', 1);
define('NGX_CONF_BLOCK_DONE',2);
define('NGX_CONF_FILE_DONE',3);
define('NGX_CONF_BUFFER',4096);
define('parse_file',0);
define('parse_block',1);
define('parse_param',2);

define('NGX_CONF_ARGS_NUMBER',0x000000ff);
define('NGX_CONF_BLOCK',0x00000100);
define('NGX_CONF_FLAG',0x00000200);
define('NGX_CONF_ANY',0x00000400);
define('NGX_CONF_1MORE',0x00000800);
define('NGX_CONF_2MORE',0x00001000);
define('NGX_CONF_MULTI',0x00000000);  /* compatibility */

define('NGX_DIRECT_CONF',0x00010000);

define('NGX_MAIN_CONF',0x01000000);
define('NGX_ANY_CONF',0x0F000000);

//define('NGX_MODULE_V1',  0, 0, 0, 0, 0, 0, 1);
//define('NGX_MODULE_V1_PADDING',  0, 0, 0, 0, 0, 0, 0, 0);




define('NGX_MAX_CONF_ERRSTR',1024);


/* The eight fixed arguments */

function argument_number($i){
    static $argument_number = [
            NGX_CONF_NOARGS,
            NGX_CONF_TAKE1,
            NGX_CONF_TAKE2,
            NGX_CONF_TAKE3,
            NGX_CONF_TAKE4,
            NGX_CONF_TAKE5,
            NGX_CONF_TAKE6,
            NGX_CONF_TAKE7
            ];
    return $argument_number($i);
}

class ngx_module_t {
/** ngx_uint_t **/ public $ctx_index;
/** ngx_uint_t **/ public $index;

/** ngx_uint_t **/ public $spare0;
/** ngx_uint_t **/ public $spare1;
/** ngx_uint_t **/ public $spare2;
/** ngx_uint_t **/ public $spare3;

/** ngx_uint_t **/ public $version;

/** void **/ public $ctx;
/** ngx_command_t **/ public $commands;
/** ngx_uint_t **/ public $type;

/**    ngx_int_t           (*init_master)(ngx_log_t *log); **/ public $init_master;
/**
/**    ngx_int_t           (*init_module)(ngx_cycle_t *cycle);**/ public $init_module;
/**
/**    ngx_int_t           (*init_process)(ngx_cycle_t *cycle);**/ public $init_process;
/**    ngx_int_t           (*init_thread)(ngx_cycle_t *cycle);**/  public $init_thread;
/**    void                (*exit_thread)(ngx_cycle_t *cycle);**/  public $exit_thread;
/**    void                (*exit_process)(ngx_cycle_t *cycle);**/ public $exit_process;
/**    void                (*exit_master)(ngx_cycle_t *cycle);**/  public $exit_master;

/** uintptr_t **/ public $spare_hook0;
/** uintptr_t **/ public $spare_hook1;
/** uintptr_t **/ public $spare_hook2;
/** uintptr_t **/ public $spare_hook3;
/** uintptr_t **/ public $spare_hook4;
/** uintptr_t **/ public $spare_hook5;
/** uintptr_t **/ public $spare_hook6;
/** uintptr_t **/ public $spare_hook7;

}

class ngx_conf_t {
  /**  char ***/  private      $name;
  /**  ngx_array_t **/  private       $args;
/** ngx_cycle_t **/ private $cycle;
///** ngx_pool_t **/ private $pool;
///** ngx_pool_t **/ private $temp_pool;
/** ngx_conf_file_t **/ private $conf_file;
/** ngx_log_t **/ private $log;
/** void **/ private $ctx;
/** ngx_uint_t **/ private $module_type; 
/** ngx_uint_t **/ private $cmd_type; 
/** ngx_conf_handler_pt **/ private $handler; 
/** char **/ private $handler_conf;
    public function __set($property,$value){
        if($property == 'cycle' && $value instanceof ngx_cycle_t){
          $this->cycle = $value;
        }elseif($property == 'conf_file' && $value instanceof ngx_conf_file_t){
            $this->conf_file = $value;
        }elseif($property == 'log' && $value instanceof ngx_log){
            $this->log = $value;
        }elseif($property == 'handler') {

                if($value instanceof Closure){
                    $this->handler = $value ;
                }else{
                    die('ngx_conf_t  handler type');
                }

        } else{
            $this->$property = $value;
        }
    }
    public function __get($property){
       return $this->$property;
    }

    public function handle(ngx_conf_t $cf, /*ngx_command_t*/ $cmd ,$s){

        return call_user_func($this->handler,$cf,$cmd,$s);
    }
}

function ngx_conf_module(){
    static $ngx_conf_module;
    if(is_null($ngx_conf_module)){
        $obj = new ngx_module_t();
        $ngx_conf_module = $obj;
        $ngx_conf_module->version = 1;
        $ngx_conf_module->ctx = null;
        $ngx_conf_module->commands = ngx_conf_commands();
        $ngx_conf_module->type = NGX_CONF_MODULE;
        $ngx_conf_module->exit_process = 'ngx_conf_flush_files';
    }
    return $ngx_conf_module;

}

function ngx_conf_commands(){

    $ngx_conf_commands = array(
        array(
            'name'=>"include",
            'type'=>NGX_ANY_CONF|NGX_CONF_TAKE1,
            'set'=>'ngx_conf_include',
            'conf'=>0,
            'offset'=>0,
            'post'=>NULL
        ),
        array(
            'name'=>'',
            'type'=>0,
            'set'=>NULL,
            'conf'=>0,
            'offset'=>0,
            'post'=>NULL
        ),
    );

    return $ngx_conf_commands;

}

class ngx_core_module_t {
 /***   ngx_str_t **/   private         $name;
 /**   void  **/        private     $create_conf; /****(*create_conf)(ngx_cycle_t *cycle); ***/
  /***  char **/        private    $init_conf; /*** *(*init_conf)(ngx_cycle_t *cycle, void *conf); *
 * @param $property
 * @param $value
 */
    public function __set($property,$value){

        $this->$property = $value;
    }
    public function __get($property){
        return $this->$property;
    }

}

//class ngx_command_t {
///** ngx_str_t **/ private $name;
///** ngx_uint_t **/ private $type;
///**    char  **/   private $set;/*(ngx_conf_t *cf, ngx_command_t *cmd, void *conf);*/
///** ngx_uint_t **/ private $conf;
///** ngx_uint_t **/ private $offset;
///** void **/ private $post;
//
//    public function __set($property, $value){
//        if($property == 'set'){
//            if($value instanceof Closure){
//                $this->set = $value ;
//            }else{
//                die('ngx_command_t handler type');
//            }
//        }else{
//            $this->$property = $value;
//        }
//    }
//    public function __get($property){
//       return $this->$property;
//    }
//
//    public function handle(ngx_conf_t $cf, ngx_command_t $cmd, $conf){
//
//        return call_user_func($this->set,$cf,$cmd,$conf);
//    }
//}

function ngx_conf_param(ngx_conf_t $cf)
{
//char             *rv;
//ngx_str_t        *param;
//ngx_buf_t         b;
//    ngx_conf_file_t   conf_file;

    $param = $cf->cycle->conf_param;

    if ($param->len == 0) {
        return NGX_CONF_OK;
       }

   // ngx_memzero(&conf_file, sizeof(ngx_conf_file_t));
    $conf_file = new ngx_conf_file_t();

    //ngx_memzero(&b, sizeof(ngx_buf_t));
    $b = new ngx_buf_t();

    $b->start = $param->data;
    $b->pos = $param->data;
    $b->last = $param->data + $param->len;
    $b->end = $b->last;
    $b->temporary = 1;

    $conf_file->file->fd = NGX_INVALID_FILE;
    $conf_file->file->name = NULL;
    $conf_file->line = 0;

    $cf->conf_file = $conf_file;
    $cf->conf_file->buffer = $b;

    $rv = ngx_conf_parse($cf, NULL);

    $cf->conf_file = NULL;

    return $rv;
}

class ngx_conf_file_t {
    /**  ngx_file_t **/     private     $file;
/**    ngx_buf_t   **/      private   $buffer;
/**    ngx_buf_t   **/      private   $dump;
   /**  ngx_uint_t **/      private   $line;
    public function __set($property,$value){
        if($property == 'file' && $value instanceof ngx_file_t){
            $this->file = $value;
        }else{
            $this->$property = $value;
        }
    }
    public function __get($property){
       return $this->$property;
    }
}

function ngx_conf_parse(ngx_conf_t $cf, $filename)
{
//char             *rv;
//u_char           *p;
//off_t             size;
//    ngx_fd_t          fd;
//    ngx_int_t         rc;
//    ngx_buf_t         buf, *tbuf;
//    ngx_conf_file_t  *prev, conf_file;
//    ngx_conf_dump_t  *cd;
//    enum {
//    parse_file = 0,
//        parse_block,
//        parse_param
//    } type;
//
//#if (NGX_SUPPRESS_WARN)
//    fd = NGX_INVALID_FILE;
//    prev = NULL;
//#endif

    if ($filename) {

        /* open configuration file */

        $fd = ngx_open_file($filename, NGX_FILE_RDONLY, 0);
        if ($fd == NGX_INVALID_FILE) {
            ngx_conf_log_error(NGX_LOG_EMERG,$cf,NGX_FERROR ,
                ngx_open_file_n ." \"%s\" failed",
                               array($filename));
            return NGX_CONF_ERROR;
        }

        $prev = $cf->conf_file;

        $conf_file = new ngx_conf_file_t();
        $buf = new ngx_buf_t();
        $cf->conf_file = $conf_file;
        if ( ($stat = ngx_fd_info($fd)) == NGX_FILE_ERROR) {
            ngx_log_error(NGX_LOG_EMERG, $cf->log, NGX_FERROR,
                          ngx_fd_info_n ." \"%s\" failed", $filename);
        }

        $cf->conf_file->file  = new stdClass();
        $cf->conf_file->file->info = new ArrayObject($stat);
        $buf->pos = $buf->start = 0;
        $buf->last =$buf->start;
        //$buf->end = $n-1;
        $buf->temporary = 1;

        $cf->conf_file->file->fd = $fd;
        $cf->conf_file->file->name = $filename;
        $cf->conf_file->file->log = $cf->log;
        $cf->conf_file->buffer = $buf;
        $cf->conf_file->line = 1;

        $type = parse_file;

        if (ngx_dump_config())
        {


            $tbuf = new ngx_buf_t();

            $cd = new ngx_conf_dump_t();

            $cd->name =  $filename;
            $cd->buffer =  $tbuf;
            $cf->cycle->config_dump[] = $cd;
            $cf->conf_file->dump = $tbuf;
            //$tbuf->last = $n;

        } else {
            $cf->conf_file->dump = NULL;
        }

    } else if ($cf->conf_file->file->fd != NGX_INVALID_FILE) {

    $type = parse_block;

} else {
    $type = parse_param;
}


    for ( ;; ) {
        $rc = ngx_conf_read_token($cf);

        /*
         * ngx_conf_read_token() may return
         *
         *    NGX_ERROR             there is error
         *    NGX_OK                the token terminated by ";" was found
         *    NGX_CONF_BLOCK_START  the token terminated by "{" was found
         *    NGX_CONF_BLOCK_DONE   the "}" was found
         *    NGX_CONF_FILE_DONE    the configuration file is done
         */

        if ($rc == NGX_ERROR) {
            goto done;
        }

        if ($rc == NGX_CONF_BLOCK_DONE) {

            if ($type != parse_block) {
                ngx_conf_log_error(NGX_LOG_EMERG, $cf, 0, "unexpected \"}\"");
                goto failed;
            }

            goto done;
        }

        if ($rc == NGX_CONF_FILE_DONE) {

            if ($type == parse_block) {
                ngx_conf_log_error(NGX_LOG_EMERG, $cf, 0,
                    "unexpected end of file, expecting \"}\"");
                goto failed;
            }

            goto done;
        }

        if ($rc == NGX_CONF_BLOCK_START) {

            if ($type == parse_param) {
                ngx_conf_log_error(NGX_LOG_EMERG, $cf, 0,
                    "block directives are not supported ".
                                   "in -g option");
                goto failed;
            }
        }

        /* rc == NGX_OK || rc == NGX_CONF_BLOCK_START */

        if ($cf->handler) {

            /*
             * the custom handler, i.e., that is used in the http's
             * "types { ... }" directive
             */

            if ($rc == NGX_CONF_BLOCK_START) {
                ngx_conf_log_error(NGX_LOG_EMERG, $cf, 0, "unexpected \"{\"");
                goto failed;
            }

            $rv = $cf->handle($cf, NULL, $cf->handler_conf);
            if ($rv == NGX_CONF_OK) {
                continue;
            }

            if ($rv == NGX_CONF_ERROR) {
                goto failed;
            }

            ngx_conf_log_error(NGX_LOG_EMERG, $cf, 0, $rv);

            goto failed;
        }


        $rc = ngx_conf_handler($cf, $rc);

        if ($rc == NGX_ERROR) {
            goto failed;
        }
    }

failed:

    $rc = NGX_ERROR;

done:

    if ($filename) {
        if (!empty($cf->conf_file->buffer->data)) {
            //ngx_free($cf->conf_file->buffer->start);
            $cf->conf_file->buffer = null;
        }

        if (ngx_close_file($fd) == NGX_FILE_ERROR) {
            ngx_log_error(NGX_LOG_ALERT, $cf->log, NGX_FERROR,
                          ngx_close_file_n ." %s failed",
                          $filename);
            $rc = NGX_ERROR;
        }

        $cf->conf_file = $prev;
    }

    if ($rc == NGX_ERROR) {
        return NGX_CONF_ERROR;
    }

    return NGX_CONF_OK;
}

class ngx_conf_dump_t {
  /**  ngx_str_t **/     private       $name;
 /**  ngx_buf_t  ***/   private       $buffer;
    public function __set($property, $name){
       $this->$property = $name;
    }
}


function ngx_conf_log_error($level, ngx_conf_t $cf, $err, $fmt, array $args = array())
{
//    u_char   errstr[NGX_MAX_CONF_ERRSTR], *p, *last;
//    va_list  args;
//
//    last = errstr + NGX_MAX_CONF_ERRSTR;

    //va_start(args, fmt);
    $p = '';
    $p = ngx_vslprintf($p, $fmt, $args);
    //va_end(args);

    if ($err) {
        $p = ngx_log_errno($p, $err);
    }

    if ($cf->conf_file == NULL) {
            ngx_log_error($level, $cf->log, 0, "%*s", array($p));
                return;
    }

    if ($cf->conf_file->file->fd == NGX_INVALID_FILE) {
            ngx_log_error($level, $cf->log, 0, "%*s in command line",
                              array($p));
        return;
    }

    $args = array(
        $p,
        $cf->conf_file->file->name,
        $cf->conf_file->line,
    );
    ngx_log_error($level, $cf->log, 0, "%*s in %s:%ui",$args);
}

function ngx_conf_read_token(ngx_conf_t $cf)
{
//u_char      *start, ch, *src, *dst;
//    off_t        file_size;
//    size_t       len;
//    ssize_t      n, size;
//    ngx_uint_t   found, need_space, last_space, sharp_comment, variable;
//    ngx_uint_t   quoted, s_quoted, d_quoted, start_line;
//    ngx_str_t   *word;
//    ngx_buf_t   *b, *dump;
//
    $found = 0;
    $need_space = 0;
    $last_space = 1;
    $sharp_comment = 0;
    $variable = 0;
    $quoted = 0;
    $s_quoted = 0;
    $d_quoted = 0;

    $dump = $cf->conf_file->dump;
    $b = $cf->conf_file->buffer;
    $start = $b->pos;
    $start_line = $cf->conf_file->line;


    $file_size = ngx_file_size($cf->conf_file->file->info);

    for ( ;; ) {

        if ($b->pos >= $b->last) {

            if ($cf->conf_file->file->offset >= $file_size) {

                if (count($cf->args)>0 || !$last_space) {

                    if ($cf->conf_file->file->fd == NGX_INVALID_FILE) {
                        ngx_conf_log_error(NGX_LOG_EMERG, $cf, 0,
                            "unexpected end of parameter, ".
                                           "expecting \";\"");
                        return NGX_ERROR;
                    }

                    ngx_conf_log_error(NGX_LOG_EMERG, $cf, 0,
                        "unexpected end of file, ".
                                  "expecting \";\" or \"}\"");
                    return NGX_ERROR;
                }

                return NGX_CONF_FILE_DONE;
            }

            $len = $b->pos - $start;

            if ($len == NGX_CONF_BUFFER) {

                $cf->conf_file->line = $start_line;

                if ($d_quoted) {
                    $ch = '"';

                } else if ($s_quoted) {
                    $ch = '\'';

                } else {
                    ngx_conf_log_error(NGX_LOG_EMERG, $cf, 0,
                        "too long parameter \"%*s...\" started",
                        array(10, $b));
                    return NGX_ERROR;
                }

                ngx_conf_log_error(NGX_LOG_EMERG, $cf, 0,
                    "too long parameter, probably ".
                                   "missing terminating \"%c\" character", $ch);
                return NGX_ERROR;
            }

            if ($len) {
                $b->data = substr($b->data,$start,$len);
            }

            $size = $file_size - $cf->conf_file->file->offset;

            if ($size > $b->end - ($b->start + $len)) {
                $size = $b->end - ($b->start + $len);
            }

            $n = ngx_read_file($cf->conf_file->file, $b->data, $size,
                              $cf->conf_file->file->offset);

            if ($n == NGX_ERROR) {
                return NGX_ERROR;
            }

            if ($n != $file_size) {
                ngx_conf_log_error(NGX_LOG_EMERG, $cf, 0,
                    ngx_read_file_n ." returned ".
                                   "only %z bytes instead of %z",
                                   array($n, $file_size));
                return NGX_ERROR;
            }

            $b->pos = $b->start + $len;
            $b->last = $b->pos + $n;
            $start = $b->start;

            if ($dump) {
                $dump = substr($b->data,$b->pos,$size);
            }
       }

        $ch = $b->data[$b->pos++];

        if ($ch == LF) {

            $cf->conf_file->line++;

            if ($sharp_comment) {
                $sharp_comment = 0;
            }
        }

        if ($sharp_comment) {
            continue;
        }

        if ($quoted) {
            $quoted = 0;
            continue;
        }

        if ($need_space) {
            if ($ch == ' ' || $ch == '\t' || $ch == CR || $ch == LF) {
                $last_space = 1;
                $need_space = 0;
                continue;
            }

            if ($ch == ';') {
                return NGX_OK;
            }

            if ($ch == '{') {
                return NGX_CONF_BLOCK_START;
            }

            if ($ch == ')') {
                $last_space = 1;
                $need_space = 0;

            } else {
                ngx_conf_log_error(NGX_LOG_EMERG, $cf, 0,
                    "unexpected \"%c\"", $ch);
                return NGX_ERROR;
            }
        }

        if ($last_space) {
            if ($ch == ' ' || $ch == '\t' || $ch == CR || $ch == LF) {
                continue;
            }

            $start = $b->pos - 1;
            $start_line = $cf->conf_file->line;

            switch ($ch) {

                case ';':
                case '{':
                    if (count($cf->args)) {
                    ngx_conf_log_error(NGX_LOG_EMERG, $cf, 0,
                        "unexpected \"%c\"", $ch);
                    return NGX_ERROR;
                }

                if ($ch == '{') {
                    return NGX_CONF_BLOCK_START;
                }

                return NGX_OK;

                case '}':
                    if (count($cf->args)!= 0) {
                    ngx_conf_log_error(NGX_LOG_EMERG, $cf, 0,
                        "unexpected \"}\"");
                    return NGX_ERROR;
                }

                return NGX_CONF_BLOCK_DONE;

                case '#':
                    $sharp_comment = 1;
                    continue;

                case '\\':
                    $quoted = 1;
                    $last_space = 0;
                    continue;

                case '"':
                    $start++;
                    $d_quoted = 1;
                    $last_space = 0;
                    continue;

                case '\'':
                    $start++;
                    $s_quoted = 1;
                    $last_space = 0;
                    continue;

                default:
                    $last_space = 0;
            }

        } else {
            if ($ch == '{' && $variable) {
                continue;
            }

            $variable = 0;

            if ($ch == '\\') {
                $quoted = 1;
                continue;
            }

            if ($ch == '$') {
                $variable = 1;
                continue;
            }

            if ($d_quoted) {
                if ($ch == '"') {
                    $d_quoted = 0;
                    $need_space = 1;
                    $found = 1;
                }

            } else if ($s_quoted) {
                if ($ch == '\'') {
                    $s_quoted = 0;
                    $need_space = 1;
                    $found = 1;
                }

            } else if ($ch == ' ' || $ch == '\t' || $ch == CR || $ch == LF
                || $ch == ';' || $ch == '{')
            {
                $last_space = 1;
                $found = 1;
            }

            if ($found) {
                $k = 0;
                for ($dst = '', $p = $start, $len = 0;
                     $p < $b->pos - 1;
                     $len++)
                {
                    if ($b->data[$p] == '\\') {
                    switch ($b->data[$p+1]) {
                    case '"':
                        case '\'':
                        case '\\':
                            $p++;
                            break;

                        case 't':
                            $dst[$k++] = '\t';
                            $p += 2;
                            continue;

                        case 'r':
                            $dst[$k++] = '\r';
                            $p += 2;
                            continue;

                        case 'n':
                            $dst[$k++] = '\n';
                            $p += 2;
                            continue;
                        }

                    }
                    $dst[$k++] = $b->data[$p++];
                }

                $cf->args[] = $dst;

                if ($ch == ';') {
                    return NGX_OK;
                }

                if ($ch == '{') {
                    return NGX_CONF_BLOCK_START;
                }

                $found = 0;
            }
        }
    }
}

function ngx_conf_handler(ngx_conf_t $cf, $last)
{
//    char           *rv;
//    void           *conf, **confp;
//    ngx_uint_t      i, found;
//    ngx_str_t      *name;
//    ngx_command_t  *cmd;

    $name = current($cf->args);

    $found = 0;

//    $ngx_modules = ngx_modules();
    for ($i = 0; ngx_modules($i); $i++) {

        $cmds = ngx_modules($i)->commands;
        if (empty($cmds)) {
            continue;
        }

        //for ( /* void */ ; cmd->name.len; cmd++) {
        foreach($cmds as $cmd){

        if (strlen($name)!= strlen($cmd['name'])) {
            continue;
        }

        if (ngx_strcmp($name, $cmd['name']) != 0) {
            continue;
        }

            $found = 1;

        if (ngx_modules($i)->type != NGX_CONF_MODULE
        && ngx_modules($i)->type != $cf->module_type)
            {
                continue;
            }

            /* is the directive's location right ? */

        if (!($cmd['type'] & $cf->cmd_type)) {
            continue;
        }

        if (!($cmd['type'] & NGX_CONF_BLOCK) && $last != NGX_OK) {
            ngx_conf_log_error(NGX_LOG_EMERG, $cf, 0,
            "directive \"%s\" is not terminated by \";\"",
            $name);
            return NGX_ERROR;
        }

        if (($cmd['type'] & NGX_CONF_BLOCK) && $last != NGX_CONF_BLOCK_START) {
                ngx_conf_log_error(NGX_LOG_EMERG, $cf, 0,
            "directive \"%s\" has no opening \"{\"",
            $name);
            return NGX_ERROR;
        }

            /* is the directive's argument count right ? */
            //$argument_number = ngx_cfg('argument_number');

            if (!($cmd['type'] & NGX_CONF_ANY)) {

            if ($cmd['type'] & NGX_CONF_FLAG) {

                if (count($cf->args) != 2) {
                    goto invalid;
                }

                } else if ($cmd['type'] & NGX_CONF_1MORE) {

                if (count($cf->args) < 2) {
                    goto invalid;
                }

                } else if ($cmd['type'] & NGX_CONF_2MORE) {

                if (count($cf->args) < 3) {
                    goto invalid;
                }

                } else if (count($cf->args) > NGX_CONF_MAX_ARGS) {

                goto invalid;

            } else if (!($cmd['type'] & argument_number(count($cf->args) - 1)))
                {
                    goto invalid;
                }
            }

            /* set up the directive's configuration context */

            $conf = NULL;

            if ($cmd['type'] & NGX_DIRECT_CONF) {
                //todo 0 offset is ok?
            $conf =  $cf->ctx[0][ngx_modules($i)->index];
                //conf = ((void **) cf->ctx)[ngx_modules[i]->index];
            } else if ($cmd['type'] & NGX_MAIN_CONF) {
                //todo  direct_conf and main_conf should use the same offset
            $conf = $cf->ctx[0][ngx_modules($i)->index];
                //todo should know why use & or not
                //conf = &((void **) cf->ctx)[ngx_modules[i]->index];
            } else if ($cf->ctx) {
            //$confp = $cf->ctx + $cmd->conf;
            $conf = $cf->ctx[$cmd['conf']][ngx_modules($i)->ctx_index];
            }

            $rv = $cmd['set']($cf, $cmd, $conf);

            if ($rv == NGX_CONF_OK) {
                return NGX_OK;
            }

            if ($rv == NGX_CONF_ERROR) {
                return NGX_ERROR;
            }

            ngx_conf_log_error(NGX_LOG_EMERG, $cf, 0,
                "\"%s\" directive %s", array($name, $rv));

            return NGX_ERROR;
        }
    }

    if ($found) {
        ngx_conf_log_error(NGX_LOG_EMERG, $cf, 0,
            "\"%s\" directive is not allowed here", (array)$name);

        return NGX_ERROR;
    }

    ngx_conf_log_error(NGX_LOG_EMERG, $cf, 0,
        "unknown directive \"%s\"", (array)$name);

    return NGX_ERROR;

invalid:

    ngx_conf_log_error(NGX_LOG_EMERG, $cf, 0,
        "invalid number of arguments in \"%s\" directive",
        (array)$name);

    return NGX_ERROR;
}

function ngx_conf_init_value(&$conf, $default){
    if ($conf == NGX_CONF_UNSET) {
        $conf = $default;
    }
}

function ngx_conf_init_msec_value(&$conf, $default){

    if ($conf == NGX_CONF_UNSET_MSEC) {
        $conf = $default;
        }
}

function ngx_conf_full_name(ngx_cycle_t $cycle, $name, $conf_prefix)
{

    //todo where prefix come from
    $prefix = $conf_prefix ? $cycle->conf_prefix : $cycle->prefix;

    return ngx_get_full_name($prefix, $name);
}

//function ngx_conf_set_flag_slot_closure(){
//   return function(ngx_conf_t $cf, ngx_command_t $cmd,$conf){
//      ngx_conf_set_flag_slot($cf,$cmd,$conf);
//   };
//}

function ngx_conf_set_flag_slot(ngx_conf_t $cf, /*ngx_command_t*/ $cmd, $conf)
{
//char  *p = conf;
    $p = $conf;

//ngx_str_t        *value;
//ngx_flag_t       *fp;
//ngx_conf_post_t  *post;

    //todo know why should do this
//fp = (ngx_flag_t *) (p + cmd->offset);
  $fp =  $p[$cmd['offset']];

    if ($fp != NGX_CONF_UNSET) {
    return "is duplicate";
    }

    $value = $cf->args;

    if (ngx_strcasecmp($value[1], "on") == 0) {
            $fp = 1;
    } else if (ngx_strcasecmp($value[1], "off") == 0) {
        $fp = 0;

    } else {
    ngx_conf_log_error(NGX_LOG_EMERG, $cf, 0,
        "invalid value \"%s\" in \"%s\" directive, ".
                     "it must be \"on\" or \"off\"",
                     array($value[1], $cmd->name));
        return NGX_CONF_ERROR;
    }

    if ($cmd['post']) {
        $post = $cmd['post'];
        return $post($cf, $post, $fp);
    }

    return NGX_CONF_OK;
}

function offsetof( $obj,$property){
    //return $obj->$property;
}

//function ngx_conf_set_msec_slot_closure(){
//    return function(ngx_conf_t $cf, ngx_command_t $cmd,$conf){
//        ngx_conf_set_msec_slot($cf,$cmd,$conf);
//    };
//}
function ngx_conf_set_msec_slot(ngx_conf_t $cf, /*ngx_command_t*/ $cmd, $conf)
{
   $p = $conf;
//msp = (ngx_msec_t *) (p + cmd->offset);
    $msp =  $p[$cmd['offset']];
    if ($msp != NGX_CONF_UNSET_MSEC) {
    return "is duplicate";
     }

    $value = $cf->args;

    $msp = ngx_parse_time($value[1], 0);
    if ($msp == NGX_ERROR) {
    return "invalid value";
     }

    if ($cmd['post']) {
        $post = $cmd['post'];
        return $post($cf, $post, $msp);
    }
    return NGX_CONF_OK;
}

//function ngx_conf_set_str_slot_closure(){
//    return function(ngx_conf_t $cf, ngx_command_t $cmd,$conf){
//        ngx_conf_set_str_slot($cf,$cmd,$conf);
//    };
//}

function ngx_conf_set_str_slot(ngx_conf_t $cf, /*ngx_command_t*/ $cmd, $conf)
{
      $p = $conf;

//    field = (ngx_str_t *) (p + cmd->offset);
      $field = $p[$cmd['offset']];

    if (!empty($field)) {
    return "is duplicate";
       }

    $value = $cf->args;

    $field = $value[1];

    if ($cmd['post']) {
        $post = $cmd['post'];
        return $post($cf, $post, $field);
    }
    return NGX_CONF_OK;
}

//function ngx_conf_set_enum_slot_closure(){
//    return function(ngx_conf_t $cf, ngx_command_t $cmd, $conf){
//        ngx_conf_set_enum_slot($cf,$cmd,$conf);
//    };
//}

function ngx_conf_set_enum_slot(ngx_conf_t $cf, /*ngx_command_t*/ $cmd, $conf)
{
    $p = $conf;

//ngx_uint_t       *np, i;
//    ngx_str_t        *value;
//    ngx_conf_enum_t  *e;

//    np = (ngx_uint_t *) (p + cmd->offset);
      $np = $p[$cmd['offset']];

    if ($np != NGX_CONF_UNSET_UINT) {
        return "is duplicate";
        }

    $value = $cf->args;
    $e = $cmd['post'];

    for ($i = 0; $e[$i]; $i++) {
    if (ngx_strcasecmp($e[$i]->name, $value[1]) != 0)
        {
            continue;
        }

        $np = $e[$i]->value;
        return NGX_CONF_OK;
    }

    ngx_conf_log_error(NGX_LOG_WARN, $cf, 0,
        "invalid value \"%s\"", $value[1]);

    return NGX_CONF_ERROR;
}

//function ngx_conf_set_num_slot_closure(){
//   return function(ngx_conf_t $cf, ngx_command_t $cmd, $conf){
//        ngx_conf_set_num_slot($cf,  $cmd, $conf);
//   } ;
//}

function ngx_conf_set_num_slot(ngx_conf_t $cf, /*ngx_command_t*/ $cmd, $conf)
{
//char  *p = conf;
      $p = $conf;

//ngx_int_t        *np;
//ngx_str_t        *value;
//ngx_conf_post_t  *post;


    //np = (ngx_int_t *) (p + cmd->offset);
    $np = $p[$cmd['offset']];

    if ($np != NGX_CONF_UNSET) {
         return "is duplicate";
       }

    $value = $cf->args;
    $np = ngx_atoi($value[1]);
    if ($np == NGX_ERROR) {
    return "invalid number";
    }

    if ($cmd['post']) {
        $post = $cmd['post'];
        return $post($cf, $post, $np);
    }

    return NGX_CONF_OK;
}

//function ngx_conf_set_off_slot_closure(){
//    return function(ngx_conf_t $cf, ngx_command_t $cmd, $conf){
//        ngx_conf_set_off_slot( $cf,  $cmd, $conf);
//    };
//}

function ngx_conf_set_off_slot(ngx_conf_t $cf, /*ngx_command_t*/ $cmd, $conf)
{
//char  *p = conf;
    $p = $conf;

//off_t            *op;
//ngx_str_t        *value;
//ngx_conf_post_t  *post;


    //op = (off_t *) (p + cmd->offset);
    $op = $p[$cmd['offset']];
    if ($op != NGX_CONF_UNSET) {
        return "is duplicate";
    }

    $value = $cf->args;

    $op = ngx_parse_offset($value[1]);
    if ($op == NGX_ERROR) {
    return "invalid value";
    }

    if ($cmd['post']) {
    $post = $cmd['post'];
        return $post($cf, $post, $op);
    }

    return NGX_CONF_OK;
}

function ngx_conf_open_file(ngx_cycle_t $cycle, $name)
{
//ngx_str_t         full;
//    ngx_uint_t        i;
//    ngx_list_part_t  *part;
//    ngx_open_file_t  *file;
//
//#if (NGX_SUPPRESS_WARN)
//    ngx_str_null(&full);
//#endif

    if (!empty($name)) {
        $full = $name;

        if (ngx_conf_full_name($cycle, $full, 0) != NGX_OK) {
            return NULL;
        }

        /**open_file_s  ngx_list_t**/
        $part = $cycle->open_files;
        $part->rewind();
        //file = part->elts;
        for ($i = 0; /* void */ ; $i++) {
            //todo here use a array
            $files = $part->current();
            if ($i >= count($files)) {
                $part->next();
                $files = $part->current();
                if (empty($files)) {
                    break;
                }
                $i = 0;
            }

            if (strlen($full) != strlen($files[$i]->name)) {
                continue;
            }

            if (ngx_strcmp($full, $files[$i]->name) == 0) {
                return $files[$i];
            }
        }
    }

    $file = new ngx_open_file_s();
//    file = ngx_list_push(&cycle->open_files);
//    if (file == NULL) {
//        return NULL;
//    }

    if (!empty($name)) {
        $file->fd = NGX_INVALID_FILE;
        $file->name = $full;
    } else {
        $file->fd = ngx_stderr;
        $file->name = $name;
    }

    $file->flush_handler = NULL;
    $file->data = NULL;
    //todo we evently use array
    $cycle->open_files->push(array($file));
    return $file;
}

function ngx_get_conf($conf_ctx,ngx_module_t $module)
{
   return $conf_ctx[$module->index];
}


function ngx_conf_include(ngx_conf_t $cf, /*ngx_command_t*/ $cmd, $conf)
{
//char        *rv;
//ngx_int_t    n;
//    ngx_str_t   *value, file, name;
//    ngx_glob_t   gl;

    $value = $cf->args;
    $file = $value[1];

    ngx_log_debug1(NGX_LOG_DEBUG_CORE, $cf->log, 0, "include %s", $file);

    if (ngx_conf_full_name($cf->cycle, $file, 1) != NGX_OK) {
        return NGX_CONF_ERROR;
    }

    if (strpbrk($file, "*?[") == NULL) {

        ngx_log_debug1(NGX_LOG_DEBUG_CORE, $cf->log, 0, "include %s", $file);

        return ngx_conf_parse($cf, $file);
    }

    //ngx_memzero(&gl, sizeof(ngx_glob_t));

//    gl.pattern = file.data;
//    gl.log = cf->log;
//    gl.test = 1;

    if ($paths = ngx_open_glob($file) == NGX_ERROR) {
            ngx_conf_log_error(NGX_LOG_EMERG, $cf, 0,
            ngx_open_glob_n ." \"%s\" failed", array($file));
        return NGX_CONF_ERROR;
    }

    $rv = NGX_CONF_OK;

    $count = count($paths);
    $n = 0;
    for ( ;; ) {

        if ($n < $count) {
            $file = $paths[$n];
            $n++;
            if (empty($file)) {
                return NGX_CONF_ERROR;
            }
            ngx_log_debug1(NGX_LOG_DEBUG_CORE, $cf->log, 0, "include %s", $file);
            $rv = ngx_conf_parse($cf, $file);
            if ($rv != NGX_CONF_OK) {
                break;
            }
        }
        break;

    }

    return $rv;
}


function ngx_conf_flush_files(ngx_cycle_t $cycle)
{
//ngx_uint_t        i;
//    ngx_list_part_t  *part;
//    ngx_open_file_t  *file;

    ngx_log_debug0(NGX_LOG_DEBUG_CORE, $cycle->log, 0, "flush files");

    $open_files_list = $cycle->open_files;
    $files = $open_files_list->current();

    for ($i = 0; /* void */ ; $i++) {

        if ($i >= count($files)) {
            $open_files_list->next();
            $files = $open_files_list->current();
            if (empty($files)) {
                break;
            }
            $i = 0;
        }

        if ($files[$i]->flush) {
            $files[$i]->flush($files[$i], $cycle->log);
        }
    }
}

function ngx_conf_init_uint_value($conf, $default){
    if ($conf == NGX_CONF_UNSET_UINT) {
    $conf = $default;
    }
}

function ngx_conf_init_ptr_value($conf, $default){
    if ($conf == NGX_CONF_UNSET_PTR) {
        $conf = $default;
    }
}




