<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 15-11-15
 * Time: 下午12:31
 */

define('NGX_CHAIN_ERROR', NGX_ERROR);
class ngx_buf_t {
    /** u_char **/ public $data;
/** u_char **/ public $pos;
/** u_char **/ public $last;
/** off_t **/ public $file_pos;
/** off_t **/ public $file_last;
/** u_char **/ public $start;          /* start of buffer */
/** u_char **/ public $end;            /* end of buffer */
/** ngx_buf_tag_t **/ public $tag;
/** ngx_file_t **/ public $file;
/** ngx_buf_t **/ public $shadow;


    /* the buf's content could be changed */
/** unsigned **/ public $temporary;

    /*
     * the buf's content is in a memory cache or in a read only memory
     * and must not be changed
     */
/** unsigned **/ public $memory;

    /* the buf's content is mmap()ed and must not be changed */
/** unsigned **/ public $mmap;
/** unsigned **/ public $recycled;
/** unsigned **/ public $in_file;
/** unsigned **/ public $flush;
/** unsigned **/ public $sync;
/** unsigned **/ public $last_buf;
/** unsigned **/ public $last_in_chain;
/** unsigned **/ public $last_shadow;
/** unsigned **/ public $temp_file;

 /**   int **/ public $num;
//    public function __set($property,$value){
//       $this->$property = $value;
//    }
//    public function __get($property){
//       return $this->$property ;
//    }
}

class ngx_chain_t extends SplDoublyLinkedList
{
    /**    ngx_buf_t    *buf;***/
}
#define NGX_CHAIN_ERROR     (ngx_chain_t *) NGX_ERROR

