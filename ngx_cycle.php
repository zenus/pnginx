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

class ngx_cycle_s {
    //void                  ****conf_ctx;
    //ngx_pool_t               *pool;

    /**ngx_log_t**/  private     $log;
   // ngx_log_t                 new_log;

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

    /**ngx_str_t**/        public         $conf_file;
    /**ngx_str_t**/        public         $conf_param;
    /**ngx_str_t**/        public         $conf_prefix;
   // /**ngx_str_t**/        private         $prefix;
    /**ngx_str_t**/        private         $lock_file;
    /**ngx_str_t**/        private         $hostname;


    public function __set($property, $value){
        if($property == 'log' && $value instanceof ngx_log){
            $this->log = $value;
        }elseif($property == 'listening' && $value instanceof ngx_listening_s){
            $this->listening[] =  $value;
        }
        else{
            $this->$property = $value;
        }
    }

    public function __get($property){
       return $this->$property;
    }


};