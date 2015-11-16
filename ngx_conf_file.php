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


define('NGX_CONF_OK',NULL);
define('NGX_CONF_ERROR',-1);

define('NGX_CONF_BLOCK_START', 1);
define('NGX_CONF_BLOCK_DONE',2);
define('NGX_CONF_FILE_DONE',3);
define('NGX_CONF_BUFFER',4096);



define('NGX_MAX_CONF_ERRSTR',1024);


class ngx_module_t {
/** ngx_uint_t **/ private $ctx_index;
/** ngx_uint_t **/ private $index;

/** ngx_uint_t **/ private $spare0;
/** ngx_uint_t **/ private $spare1;
/** ngx_uint_t **/ private $spare2;
/** ngx_uint_t **/ private $spare3;

/** ngx_uint_t **/ private $version;

/** void **/ private $ctx;
/** ngx_command_t **/ private $commands;
/** ngx_uint_t **/ private $type;

/**    ngx_int_t           (*init_master)(ngx_log_t *log); **/ private $init_master_handler;
/**
/**    ngx_int_t           (*init_module)(ngx_cycle_t *cycle);**/ private $init_module_handler;
/**
/**    ngx_int_t           (*init_process)(ngx_cycle_t *cycle);**/ private $init_process_handler;
/**    ngx_int_t           (*init_thread)(ngx_cycle_t *cycle);**/  private $init_thread_handler;
/**    void                (*exit_thread)(ngx_cycle_t *cycle);**/  private $exit_thread_handler;
/**    void                (*exit_process)(ngx_cycle_t *cycle);**/ private $exit_process_hander;
/**    void                (*exit_master)(ngx_cycle_t *cycle);**/  private $exit_master_handler;

/** uintptr_t **/ private $spare_hook0;
/** uintptr_t **/ private $spare_hook1;
/** uintptr_t **/ private $spare_hook2;
/** uintptr_t **/ private $spare_hook3;
/** uintptr_t **/ private $spare_hook4;
/** uintptr_t **/ private $spare_hook5;
/** uintptr_t **/ private $spare_hook6;
/** uintptr_t **/ private $spare_hook7;

    public function __set($property,$value){
        $this->$property = $value;
    }

    public function __get($property,$value){
       return $this->$value;
    }
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
        if($property == 'cycle' && $value instanceof ngx_cycle_s){
          $this->cycle = $value;
        }elseif($property == 'conf_file' && $value instanceof ngx_conf_file_t){
            $this->conf_file = $value;
        }elseif($property == 'log' && $value instanceof ngx_log){
            $this->log = $value;
        }
        else{
            $this->$property = $value;
        }
    }
    public function __get($property){
       return $this->$property;
    }
}

function ngx_conf_param(ngx_conf_t &$cf)
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
//    $b = new ngx_buf_t();
//
//    $b->start = $param->data;
//    $b->pos = $param->data;
//    $b->last = $param->data + $param->len;
//    $b->end = b.last;
//    $b->temporary = 1;

    $conf_file->file->fd = NGX_INVALID_FILE;
    $conf_file->file->name = NULL;
    $conf_file->line = 0;

    $cf->conf_file = $conf_file;
//    $cf->conf_file->buffer = &b;

    $rv = ngx_conf_parse($cf, NULL);

    $cf->conf_file = NULL;

    return $rv;
}

class ngx_conf_file_t {
    /**  ngx_file_t **/     private     $file;
/**    ngx_buf_t   **/      private   $buffer;
/**    ngx_buf_t   **/      private   $dump;
   /**  ngx_uint_t **/      private        $line;
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
                               $filename);
            return NGX_CONF_ERROR;
        }

        $prev = $cf->conf_file;

        $conf_file = new ngx_conf_file_t();
        $cf->conf_file = &$conf_file;

        if ($cf->conf_file->file->info = ngx_fd_info($fd) == NGX_FILE_ERROR) {
            ngx_log_error(NGX_LOG_EMERG, $cf->log, NGX_FERROR,
                          ngx_fd_info_n ." \"%s\" failed", $filename);
        }

//        $cf->conf_file->buffer = &buf;
//
//        buf.start = ngx_alloc(NGX_CONF_BUFFER, cf->log);
//        if (buf.start == NULL) {
//            goto failed;
//        }

//        buf.pos = buf.start;
//        buf.last = buf.start;
//        buf.end = buf.last + NGX_CONF_BUFFER;
//        buf.temporary = 1;

        $cf->conf_file->file->fd = $fd;
        $cf->conf_file->file->name = $filename;
        $cf->conf_file->file->log = $cf->log;
        $cf->conf_file->line = 1;

        $type = 0;

        if (ngx_cfg('ngx_dump_config'))
        {
//            p = ngx_pstrdup(cf->cycle->pool, filename);
//            if (p == NULL) {
//                goto failed;
//            }

            $size = ngx_file_size($cf->conf_file->file->info);

//            tbuf = ngx_create_temp_buf(cf->cycle->pool, (size_t) size);
//            if (tbuf == NULL) {
//                goto failed;
//            }

            $cd = new ngx_conf_dump_t();
             $cf->cycle->config_dump[] = $cd;

            //cd->name.len = filename->len;
            $cd->name =  $filename;
            //cd->buffer = tbuf;

            //cf->conf_file->dump = tbuf;

        } else {
        //    cf->conf_file->dump = NULL;
        }

    } else if ($cf->conf_file->file->fd != NGX_INVALID_FILE) {

    $type = 1;

} else {
    $type = 2;
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

            if ($type != 1) {
                ngx_conf_log_error(NGX_LOG_EMERG, $cf, 0, "unexpected \"}\"");
                goto failed;
            }

            goto done;
        }

        if ($rc == NGX_CONF_FILE_DONE) {

            if ($type == 1) {
                ngx_conf_log_error(NGX_LOG_EMERG, $cf, 0,
                    "unexpected end of file, expecting \"}\"");
                goto failed;
            }

            goto done;
        }

        if ($rc == NGX_CONF_BLOCK_START) {

            if ($type == 2) {
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

            $rv = (*cf->handler)(cf, NULL, cf->handler_conf);
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
        if ($cf->conf_file->buffer->start) {
            ngx_free($cf->conf_file->buffer->start);
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
 //   ngx_buf_t            *buffer;
    public function __set($property, $name){
       $this->$property = $name;
    }
}


function ngx_conf_log_error($level, ngx_conf_t &$cf, $err, $fmt, array $args = array())
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

function ngx_conf_read_token(ngx_conf_t &$cf)
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
    $pos = 0;

//
    //cf->args->nelts = 0;
    //todo know pos and last?
    $b = $cf->conf_file->buffer;
    $dump = $cf->conf_file->dump;
    $start = $pos;
    $start_line = $cf->conf_file->line;

    $file_size = ngx_file_size($cf->conf_file->file->info);

    $n = ngx_read_file($cf->conf_file->file, $b, $file_size,
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
    if ($dump) {
        $dump = $b;
    }
    for ( ;; ) {

        //if ($b['pos'] >= $b['last']) {

//            if ($cf->conf_file->file->offset >= $file_size) {
//
//                if (count($cf->args)>0 || !$last_space) {
//
//                    if ($cf->conf_file->file->fd == NGX_INVALID_FILE) {
//                        ngx_conf_log_error(NGX_LOG_EMERG, $cf, 0,
//                            "unexpected end of parameter, ".
//                                           "expecting \";\"");
//                        return NGX_ERROR;
//                    }
//
//                    ngx_conf_log_error(NGX_LOG_EMERG, $cf, 0,
//                        "unexpected end of file, ".
//                                  "expecting \";\" or \"}\"");
//                    return NGX_ERROR;
//                }

         //       return NGX_CONF_FILE_DONE;
          //  }

//            len = b->pos - start;
           //   $len = strlen($b);

//            if ($len == NGX_CONF_BUFFER) {
//                $cf->conf_file->line = $start_line;
//
//                if ($d_quoted) {
//                    $ch = '"';
//
//                } else if ($s_quoted) {
//                    $ch = '\'';
//
//                } else {
//                    ngx_conf_log_error(NGX_LOG_EMERG, $cf, 0,
//                        "too long parameter \"%*s...\" started",
//                        array(10, $b));
//                    return NGX_ERROR;
//                }
//
//                ngx_conf_log_error(NGX_LOG_EMERG, $cf, 0,
//                    "too long parameter, probably ".
//                                   "missing terminating \"%c\" character", $ch);
//                return NGX_ERROR;
//            }

//            if (len) {
//                ngx_memmove(b->start, start, len);
//            }

            //$size = $file_size - $cf->conf_file->file->offset;

//            if ($size > $b->end - ($b->start + $len)) {
//                $size = $b->end - ($b->start + $len);
//            }

//            $n = ngx_read_file($cf->conf_file->file, $b, $file_size,
//                              $cf->conf_file->file->offset);

//            if ($n == NGX_ERROR) {
//                return NGX_ERROR;
//            }
//
//            if ($n != $file_size) {
//                ngx_conf_log_error(NGX_LOG_EMERG, $cf, 0,
//                    ngx_read_file_n ." returned ".
//                                   "only %z bytes instead of %z",
//                                   array($n, $file_size));
//                return NGX_ERROR;
//            }

//            b->pos = b->start + len;
//            b->last = b->pos + n;
//            start = b->start;

//            if ($dump) {
//                $dump = $b;
//            }
        //}

        $ch = $b[$pos++];

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

            $start = $pos - 1;
            $start_line = $cf->conf_file->line;

            switch ($ch) {

                case ';':
                case '{':
                    if (count($cf->args)) {
                    ngx_conf_log_error(NGX_LOG_EMERG, cf, 0,
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
//                word = ngx_array_push(cf->args);
//                if (word == NULL) {
//                    return NGX_ERROR;
//                }
//
//                word->data = ngx_pnalloc(cf->pool, b->pos - 1 - start + 1);
//                if (word->data == NULL) {
//                    return NGX_ERROR;
//                }
                $word = '';
                $cf->args[] = &$word;

                $pr = 0;
                for ($dst = $word, $src = $start, $len = 0;
                     $src < $pos - 1;
                     $len++)
                {
                    if ($b[$src] == '\\') {
                    switch ($b[$src+1]) {
                    case '"':
                        case '\'':
                        case '\\':
                            $src++;
                            break;

                        case 't':
                            $dst[$pr++] = '\t';
                            $src += 2;
                            continue;

                        case 'r':
                            $dst[$pr++] = '\r';
                            $src += 2;
                            continue;

                        case 'n':
                            $dst[$pr++] = '\n';
                            $src += 2;
                            continue;
                        }

                    }
                    $dst[$pr++] = $b[$src++];
                }
               // $dst[$pr] = '\0';
                //$word->len = $len;

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
