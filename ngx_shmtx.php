<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 15-12-9
 * Time: 下午10:57
 */

function ngx_shmtx_force_unlock(/*ngx_shmtx_t*/ $mtx, $pid)
{
    $ngx_cycle = ngx_cycle();
    ngx_log_debug0(NGX_LOG_DEBUG_CORE, $ngx_cycle->log, 0,
                   "shmtx forced unlock");

//    if (ngx_atomic_cmp_set($mtx->lock, pid, 0)) {
//        ngx_shmtx_wakeup(mtx);
//        return 1;
//    }

    return 0;
}

//todo should complete lock method
function ngx_shmtx_trylock($mtx)
{
    //return (*mtx->lock == 0 && ngx_atomic_cmp_set(mtx->lock, 0, ngx_pid));
    return true;

}

//todo should complete lock method
function ngx_shmtx_trylock($mtx)
function ngx_shmtx_unlock($mtx)
{
//    if (mtx->spin != (ngx_uint_t) -1) {
//    ngx_log_debug0(NGX_LOG_DEBUG_CORE, ngx_cycle->log, 0, "shmtx unlock");
//    }
//
//    if (ngx_atomic_cmp_set(mtx->lock, ngx_pid, 0)) {
//    ngx_shmtx_wakeup(mtx);
//}
}
