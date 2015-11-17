<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 15-11-15
 * Time: 下午12:31
 */
class ngx_buf_t {
    /** u_char **/ private $data;
/** u_char **/ private $pos;
/** u_char **/ private $last;
/** off_t **/ private $file_pos;
/** off_t **/ private $file_last;
/** u_char **/ private $start;          /* start of buffer */
/** u_char **/ private $end;            /* end of buffer */
/** ngx_buf_tag_t **/ private $tag;
/** ngx_file_t **/ private $file;
/** ngx_buf_t **/ private $shadow;


    /* the buf's content could be changed */
/** unsigned **/ private $temporary;

    /*
     * the buf's content is in a memory cache or in a read only memory
     * and must not be changed
     */
/** unsigned **/ private $memory;

    /* the buf's content is mmap()ed and must not be changed */
/** unsigned **/ private $mmap;
/** unsigned **/ private $recycled;
/** unsigned **/ private $in_file;
/** unsigned **/ private $flush;
/** unsigned **/ private $sync;
/** unsigned **/ private $last_buf;
/** unsigned **/ private $last_in_chain;
/** unsigned **/ private $last_shadow;
/** unsigned **/ private $temp_file;

 /**   int **/ private $num;
    public function __set($property,$value){
       $this->$property = $value;
    }
    public function __get($property){
       return $this->$property ;
    }
};
