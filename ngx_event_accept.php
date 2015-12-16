<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 15-12-16
 * Time: 下午10:44
 */


function ngx_enable_accept_events(ngx_cycle_t $cycle)
{
//ngx_uint_t         i;
//    ngx_listening_t   *ls;
//    ngx_connection_t  *c;

    $ls = $cycle->listening;
    for ($i = 0; $i < count($cycle->listening); $i++) {
            $c = $ls[$i]->connection;
            if ($c == NULL || $c->read->active) {
            continue;
        }
         //todo should complete event method
        if (ngx_add_event($c->read, NGX_READ_EVENT, 0) == NGX_ERROR) {
                return NGX_ERROR;
           }
    }

    return NGX_OK;
}

function ngx_disable_accept_events(ngx_cycle_t $cycle,  $all)
{
//    ngx_uint_t         i;
//    ngx_listening_t   *ls;
//    ngx_connection_t  *c;

    $ls = $cycle->listening;
    for ($i = 0; $i < count($cycle->listening); $i++) {

        $c = $ls[$i]->connection;

        if ($c == NULL || !$c->read->active) {
        continue;
    }

//#if (NGX_HAVE_REUSEPORT)
//
//        /*
//         * do not disable accept on worker's own sockets
//         * when disabling accept events due to accept mutex
//         */
//
//        if (ls[i].reuseport && !all) {
//        continue;
//    }
//
//#endif

//todo should complete event method
        if (ngx_del_event($c->read, NGX_READ_EVENT, NGX_DISABLE_EVENT)
            == NGX_ERROR)
        {
            return NGX_ERROR;
        }
    }

    return NGX_OK;
}


function ngx_trylock_accept_mutex(ngx_cycle_t $cycle)
{
//todo should complete lock method
    if (ngx_shmtx_trylock(ngx_accept_mutex())) {

        ngx_log_debug0(NGX_LOG_DEBUG_EVENT, $cycle->log, 0,
                       "accept mutex locked");

        if (ngx_accept_mutex_held() && ngx_accept_events() == 0) {
            return NGX_OK;
        }

        if (ngx_enable_accept_events($cycle) == NGX_ERROR) {
//todo should complete lock method
            ngx_shmtx_unlock(ngx_accept_mutex());
            return NGX_ERROR;
        }

        ngx_accept_events(0);
        ngx_accept_mutex_held(1);

        return NGX_OK;
    }

    ngx_log_debug1(NGX_LOG_DEBUG_EVENT, $cycle->log, 0,
                   "accept mutex lock failed: %ui", ngx_accept_mutex_held());

    if (ngx_accept_mutex_held()) {
        if (ngx_disable_accept_events($cycle, 0) == NGX_ERROR) {
            return NGX_ERROR;
        }

        ngx_accept_mutex_held(0);
    }

    return NGX_OK;
}

