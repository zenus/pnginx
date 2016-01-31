<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 16-1-31
 * Time: 下午8:47
 */

class ngx_http_core_loc_conf_t {

    /**ngx_str_t**/   public  $name;          /* location name */


    /**unsigned*/    public  $noname;   /* "if () {}" block or limit_except */
    /**unsigned*/    public  $lmt_excpt;
    /**unsigned*/      public $named;

    /*unsigned*/     public $exact_match;
    /*unsigned*/     public $noregex;

    /*unsigned*/     public $auto_redirect;

    /*ngx_http_location_tree_node_t**/   public $static_locations;

    /* pointer to the modules' loc_conf */
    /*void**/     public  $loc_conf;

    /*uint32_t*/  public    $limit_except;
    /*void*/      public  $limit_except_loc_conf;

    /*ngx_http_handler_pt**/  public $handler;

    /* location name length for inclusive location with inherited alias */
/** size_t **/ public $alias;
/** ngx_str_t **/ public $root;                    /* root, alias */
/** ngx_str_t **/ public $post_action;
/** ngx_array_t **/ public $root_lengths;
/** ngx_array_t **/ public $root_values;
/** ngx_array_t **/ public $types;
/** ngx_hash_t **/ public $types_hash;
/** ngx_str_t **/ public $default_type;
/** off_t **/ public $client_max_body_size;    /* client_max_body_size */
/** off_t **/ public $directio;                /* directio */
/** off_t **/ public $directio_alignment;      /* directio_alignment */
/** size_t **/ public $client_body_buffer_size; /* client_body_buffer_size */
/** size_t **/ public $send_lowat;              /* send_lowat */
/** size_t **/ public $postpone_output;         /* postpone_output */
/** size_t **/ public $limit_rate;              /* limit_rate */
/** size_t **/ public $limit_rate_after;        /* limit_rate_after */
/** size_t **/ public $sendfile_max_chunk;      /* sendfile_max_chunk */
/** size_t **/ public $read_ahead;              /* read_ahead */
/** ngx_msec_t **/ public $client_body_timeout;     /* client_body_timeout */
/** ngx_msec_t **/ public $send_timeout;            /* send_timeout */
/** ngx_msec_t **/ public $keepalive_timeout;       /* keepalive_timeout */
/** ngx_msec_t **/ public $lingering_time;          /* lingering_time */
/** ngx_msec_t **/ public $lingering_timeout;       /* lingering_timeout */
/** ngx_msec_t **/ public $resolver_timeout;        /* resolver_timeout */
/** ngx_resolver_t **/ public $resolver;             /* resolver */
/** time_t **/ public $keepalive_header;        /* keepalive_timeout */
/** ngx_uint_t **/ public $keepalive_requests;      /* keepalive_requests */
/** ngx_uint_t **/ public $keepalive_disable;       /* keepalive_disable */
/** ngx_uint_t **/ public $satisfy;                 /* satisfy */
/** ngx_uint_t **/ public $lingering_close;         /* lingering_close */
/** ngx_uint_t **/ public $if_modified_since;       /* if_modified_since */
/** ngx_uint_t **/ public $max_ranges;              /* max_ranges */
/** ngx_uint_t **/ public $client_body_in_file_only; /* client_body_in_file_only */
/** ngx_flag_t **/ public $client_body_in_single_buffer;
                                           /* client_body_in_singe_buffer */
/** ngx_flag_t **/ public $internal;                /* internal */
/** ngx_flag_t **/ public $sendfile;                /* sendfile */
/** ngx_flag_t **/ public $aio;                     /* aio */
/** ngx_flag_t **/ public $tcp_nopush;              /* tcp_nopush */
/** ngx_flag_t **/ public $tcp_nodelay;             /* tcp_nodelay */
/** ngx_flag_t **/ public $reset_timedout_connection; /* reset_timedout_connection */
/** ngx_flag_t **/ public $server_name_in_redirect; /* server_name_in_redirect */
/** ngx_flag_t **/ public $port_in_redirect;        /* port_in_redirect */
/** ngx_flag_t **/ public $msie_padding;            /* msie_padding */
/** ngx_flag_t **/ public $msie_refresh;            /* msie_refresh */
/** ngx_flag_t **/ public $log_not_found;           /* log_not_found */
/** ngx_flag_t **/ public $log_subrequest;          /* log_subrequest */
/** ngx_flag_t **/ public $recursive_error_pages;   /* recursive_error_pages */
/** ngx_flag_t **/ public $server_tokens;           /* server_tokens */
/** ngx_flag_t **/ public $chunked_transfer_encoding; /* chunked_transfer_encoding */
/** ngx_flag_t **/ public $etag;                    /* etag */
/** ngx_array_t **/ public $error_pages;             /* error_page */
/** ngx_http_try_file_t **/ public $try_files;     /* try_files */
/** ngx_path_t **/ public $client_body_temp_path;   /* client_body_temp_path */
/** ngx_open_file_cache_t **/ public $open_file_cache;
/** time_t **/ public $open_file_cache_valid;
/** ngx_uint_t **/ public $open_file_cache_min_uses;
/** ngx_flag_t **/ public $open_file_cache_errors;
/** ngx_flag_t **/ public $open_file_cache_events;
/** ngx_log_t **/ public $error_log;
/** ngx_uint_t **/ public $types_hash_max_size;
/** ngx_uint_t **/ public $types_hash_bucket_size;
/** ngx_queue_t **/ public $locations;

}