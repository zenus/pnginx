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