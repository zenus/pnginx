<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 15-12-8
 * Time: 下午10:43
 */

#define ngx_trylock(lock)  (*(lock) == 0 && ngx_atomic_cmp_set(lock, 0, 1))
#define ngx_unlock(lock)    *(lock) = 0

function ngx_trylock($lock){
  return true;
}

function ngx_unlock($lock){
  return true;
}