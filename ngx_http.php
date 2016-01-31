<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 16-1-31
 * Time: 上午9:31
 */

define('NGX_HTTP_MODULE',0x50545448);   /* "HTTP" */

define('NGX_HTTP_MAIN_CONF',0x02000000);
define('NGX_HTTP_SRV_CONF',0x04000000);
define('NGX_HTTP_LOC_CONF',0x08000000);
define('NGX_HTTP_UPS_CONF',0x10000000);
define('NGX_HTTP_SIF_CONF',0x20000000);
define('NGX_HTTP_LIF_CONF',0x40000000);
define('NGX_HTTP_LMT_CONF',0x80000000);

function  ngx_http_max_module($i = null){
    static $ngx_http_max_module;
    if(!is_null($i)){
       $ngx_http_max_module  = $i;
    }else{
       return $ngx_http_max_module;
    }

}

function ngx_http_module(){
    static $ngx_http_module;
    if(is_null($ngx_http_module)){
        $obj = new ngx_module_t();
        $ngx_http_module = $obj;
        $ngx_http_module->version = 1;
        $ngx_http_module->ctx = ngx_http_module_ctx();
        $ngx_http_module->commands = ngx_http_commands();
        $ngx_http_module->type = NGX_CORE_MODULE;
    }
    return $ngx_http_module;
}
function ngx_http_module_ctx(){

    static $ngx_http_module_ctx;
    if(is_null($ngx_http_module_ctx)){
        $obj= new ngx_core_module_t();
        $ngx_http_module_ctx = $obj;
        $ngx_http_module_ctx->name = 'http';
        $ngx_http_module_ctx->create_conf = null;
        $ngx_http_module_ctx->init_conf = null;
    }
    return $ngx_http_module_ctx;
}

function ngx_http_commands(){

    $ngx_http_commands = array(
        array(
            'name'=>"http",
            'type'=>NGX_MAIN_CONF|NGX_CONF_BLOCK|NGX_CONF_NOARGS,
            'set'=>'ngx_http_block',
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
        )
    );

    return $ngx_http_commands;

}

function ngx_http_block(ngx_conf_t $cf, /*ngx_command_t*/ $cmd, $conf)
{
//char                        *rv;
//    ngx_uint_t                   mi, m, s;
//    ngx_conf_t                   pcf;
//    ngx_http_module_t           *module;
//    ngx_http_conf_ctx_t         *ctx;
//    ngx_http_core_loc_conf_t    *clcf;
//    ngx_http_core_srv_conf_t   **cscfp;
//    ngx_http_core_main_conf_t   *cmcf;

    if ($conf) {
        return "is duplicate";
    }

    /* the main http context */

//    ctx = ngx_pcalloc(cf->pool, sizeof(ngx_http_conf_ctx_t));
//    if (ctx == NULL) {
//        return NGX_CONF_ERROR;
//    }
    $ctx = new ngx_http_conf_ctx_t();
    //*(ngx_http_conf_ctx_t **) conf = ctx;
    //todo may have problem; more likely as $conf[][] = $ctx;
    $conf = $ctx;
    //


    /* count the number of the http modules and set up their indices */

    ngx_http_max_module(0);
    for ($m = 0; ngx_modules($m); $m++) {
        if (ngx_modules($m)->type != NGX_HTTP_MODULE) {
            continue;
        }

        $ngx_http_max_module = ngx_http_max_module();
        $ngx_http_max_module++;
        ngx_modules($m)->ctx_index = ngx_http_max_module($ngx_http_max_module);
    }


    /* the http main_conf context, it is the same in the all http contexts */

        //    ctx->main_conf = ngx_pcalloc(cf->pool,
        //                                 sizeof(void *) * ngx_http_max_module);
        //    if (ctx->main_conf == NULL) {
        //    return NGX_CONF_ERROR;
        //}


    /*
     * the http null srv_conf context, it is used to merge
     * the server{}s' srv_conf's
     */

    //    ctx->srv_conf = ngx_pcalloc(cf->pool, sizeof(void *) * ngx_http_max_module);
    //    if (ctx->srv_conf == NULL) {
    //    return NGX_CONF_ERROR;
    //}


    /*
     * the http null loc_conf context, it is used to merge
     * the server{}s' loc_conf's
     */

//    ctx->loc_conf = ngx_pcalloc(cf->pool, sizeof(void *) * ngx_http_max_module);
//    if (ctx->loc_conf == NULL) {
//    return NGX_CONF_ERROR;
//    }


    /*
     * create the main_conf's, the null srv_conf's, and the null loc_conf's
     * of the all http modules
     */

    for ($m = 0; ngx_modules($m); $m++) {
        if (ngx_modules($m)->type != NGX_HTTP_MODULE) {
            continue;
        }

        $module = ngx_modules($m)->ctx;
        $mi = ngx_modules($m)->ctx_index;

        if ($module->create_main_conf) {
                $ctx->main_conf[$mi] = $module->create_main_conf($cf);
                if ($ctx->main_conf[$mi] == NULL) {
                return NGX_CONF_ERROR;
            }
        }

        if ($module->create_srv_conf) {
            $ctx->srv_conf[$mi] = $module->create_srv_conf($cf);
            if ($ctx->srv_conf[$mi] == NULL) {
            return NGX_CONF_ERROR;
            }
        }

        if ($module->create_loc_conf) {
            $ctx->loc_conf[$mi] = $module->create_loc_conf($cf);
            if ($ctx->loc_conf[$mi] == NULL) {
            return NGX_CONF_ERROR;
            }
        }
    }

    $pcf = $cf;
    $cf->ctx = $ctx;

    for ($m = 0; ngx_modules($m); $m++) {
        if (ngx_modules($m)->type != NGX_HTTP_MODULE) {
            continue;
    }

        $module = ngx_modules($m)->ctx;

        if ($module->preconfiguration) {
            if ($module->preconfiguration($cf) != NGX_OK) {
                return NGX_CONF_ERROR;
            }
        }
    }

    /* parse inside the http{} block */

    $cf->module_type = NGX_HTTP_MODULE;
    $cf->cmd_type = NGX_HTTP_MAIN_CONF;
    $rv = ngx_conf_parse($cf, NULL);

    if ($rv != NGX_CONF_OK) {
        goto failed;
    }

    /*
     * init http{} main_conf's, merge the server{}s' srv_conf's
     * and its location{}s' loc_conf's
     */

    $ngx_http_core_module = ngx_http_core_module();
    $cmcf = $ctx->main_conf[$ngx_http_max_module->ctx_index];
    $cscfp = $cmcf->servers;

    for ($m = 0; ngx_modules($m); $m++) {
        if (ngx_modules($m)->type != NGX_HTTP_MODULE) {
            continue;
        }

        $module = ngx_modules($m)->ctx;
        $mi = ngx_modules($m)->ctx_index;

        /* init http{} main_conf's */

        if ($module->init_main_conf) {
            $rv = $module->init_main_conf($cf, $ctx->main_conf[$mi]);
            if ($rv != NGX_CONF_OK) {
                goto failed;
            }
        }

        $rv = ngx_http_merge_servers($cf, $cmcf, $module, $mi);
        if ($rv != NGX_CONF_OK) {
            goto failed;
        }
    }


    /* create location trees */

    for ($s = 0; $s < count($cmcf->servers); $s++) {

        $clcf = $cscfp[$s]->ctx->loc_conf[$ngx_http_core_module->ctx_index];

        if (ngx_http_init_locations($cf, $cscfp[$s], $clcf) != NGX_OK) {
        return NGX_CONF_ERROR;
    }

        if (ngx_http_init_static_location_trees($cf, $clcf) != NGX_OK) {
            return NGX_CONF_ERROR;
        }
    }


    if (ngx_http_init_phases($cf, $cmcf) != NGX_OK) {
        return NGX_CONF_ERROR;
    }

    if (ngx_http_init_headers_in_hash($cf, $cmcf) != NGX_OK) {
        return NGX_CONF_ERROR;
    }


    for ($m = 0; ngx_modules($m); m++) {
        if (ngx_modules($m)->type != NGX_HTTP_MODULE) {
            continue;
        }

        $module = ngx_modules($m)->ctx;

        if ($module->postconfiguration) {
            if ($module->postconfiguration($cf) != NGX_OK) {
                return NGX_CONF_ERROR;
            }
        }
    }

    if (ngx_http_variables_init_vars($cf) != NGX_OK) {
        return NGX_CONF_ERROR;
    }

    /*
     * http{}'s cf->ctx was needed while the configuration merging
     * and in postconfiguration process
     */

    $cf = $pcf;


    if (ngx_http_init_phase_handlers($cf, $cmcf) != NGX_OK) {
        return NGX_CONF_ERROR;
    }


    /* optimize the lists of ports, addresses and server names */

    if (ngx_http_optimize_servers($cf, $cmcf, $cmcf->ports) != NGX_OK) {
        return NGX_CONF_ERROR;
       }

    return NGX_CONF_OK;

failed:

    $cf = $pcf;

    return $rv;
}

function ngx_http_merge_servers(ngx_conf_t $cf, /*ngx_http_core_main_conf_t*/ $cmcf,
                                /*ngx_http_module_t*/ $module,  $ctx_index)
{
//    char                        *rv;
//    ngx_uint_t                   s;
//    ngx_http_conf_ctx_t         *ctx, saved;
//    ngx_http_core_loc_conf_t    *clcf;
//    ngx_http_core_srv_conf_t   **cscfp;

    $cscfp = $cmcf->servers;
    $ctx =  $cf->ctx;
    $saved = $ctx;
    $rv = NGX_CONF_OK;
    $ngx_http_core_module = ngx_http_core_module();

    for ($s = 0; $s < count($cmcf->servers); $s++) {

    /* merge the server{}s' srv_conf's */

        $ctx->srv_conf = $cscfp[$s]->ctx->srv_conf;

        if ($module->merge_srv_conf) {
            $rv = $module->merge_srv_conf($cf, $saved->srv_conf[$ctx_index],$cscfp[$s]->ctx->srv_conf[$ctx_index]);
            if ($rv != NGX_CONF_OK) {
                goto failed;
            }
        }

        if ($module->merge_loc_conf) {

        /* merge the server{}'s loc_conf */

        $ctx->loc_conf = $cscfp[$s]->ctx->loc_conf;

            $rv = $module->merge_loc_conf($cf, $saved->loc_conf[$ctx_index],
                                        $cscfp[$s]->ctx->loc_conf[$ctx_index]);
            if ($rv != NGX_CONF_OK) {
                goto failed;
            }

            /* merge the locations{}' loc_conf's */

            $clcf = $cscfp[$s]->ctx->loc_conf[$ngx_http_core_module->ctx_index];

            $rv = ngx_http_merge_locations($cf, $clcf->locations,
                                          $cscfp[$s]->ctx->loc_conf,
                                          $module, $ctx_index);
            if ($rv != NGX_CONF_OK) {
                goto failed;
            }
        }
    }

failed:

    $ctx = $saved;

    return $rv;
}


function ngx_http_merge_locations(ngx_conf_t $cf, /*ngx_queue_t*/ $locations,
    /*void */ $loc_conf, /*ngx_http_module_t*/ $module, /*ngx_uint_t*/ $ctx_index)
{
//    char                       *rv;
//    ngx_queue_t                *q;
//    ngx_http_conf_ctx_t        *ctx, saved;
//    ngx_http_core_loc_conf_t   *clcf;
//    ngx_http_location_queue_t  *lq;

    if ($locations == NULL) {
        return NGX_CONF_OK;
    }

    $ctx = /*(ngx_http_conf_ctx_t *)*/ $cf->ctx;
    $saved = $ctx;

//    for ($q = $locations->bottom();
//         $q != ngx_queue_sentinel($locations);
//         $q = ngx_queue_next($q))
    for( $locations->rewind(); $locations->valid(); $locations->next())
    {
        $q = $locations->current();
        $lq = $q;

        $clcf = $lq->exact ? $lq->exact : $lq->inclusive;
        $ctx->loc_conf = $clcf->loc_conf;

        $rv = $module->merge_loc_conf($cf, $loc_conf[$ctx_index],
                                    $clcf->loc_conf[$ctx_index]);
        if ($rv != NGX_CONF_OK) {
            return $rv;
        }

        $rv = ngx_http_merge_locations($cf, $clcf->locations, $clcf->loc_conf,
                                      $module, $ctx_index);
        if ($rv != NGX_CONF_OK) {
            return $rv;
        }
    }

    $ctx = $saved;

    return NGX_CONF_OK;
}

function ngx_http_init_locations(ngx_conf_t $cf, /*ngx_http_core_srv_conf_t */ $cscf,
    /*ngx_http_core_loc_conf_t */$pclcf)
{
//ngx_uint_t                   n;
//    ngx_queue_t                 *q, *locations, *named, tail;
//    ngx_http_core_loc_conf_t    *clcf;
//    ngx_http_location_queue_t   *lq;
//    ngx_http_core_loc_conf_t   **clcfp;
//#if (NGX_PCRE)
//    ngx_uint_t                   r;
//    ngx_queue_t                 *regex;
//#endif

    $tail = new ngx_queue_t();
    $tqueue = new ngx_queue_t();
    $locations = $pclcf->locations;

    if ($locations == NULL) {
        return NGX_OK;
    }

    ngx_queue_sort($locations, 'ngx_http_cmp_locations');

    $named = NULL;
    $n = 0;

    for ($locations->rewind();
         $locations->valid();
         $locations->next())
    {
        $q = $locations->current();
        $lq = $q;

        $clcf = $lq->exact ? $lq->exact : $lq->inclusive;

        if (ngx_http_init_locations($cf, NULL, $clcf) != NGX_OK) {
            return NGX_ERROR;
        }

        if ($clcf->named) {
            $n++;

            if ($named == NULL) {
                $named = $q;
            }
            continue;
        }

        if ($clcf->noname) {
            break;
        }
    }

    if ($q != $locations->offsetGet(0)) {
         ngx_queue_split($locations, $q, $tail);
    }

    if ($named) {

        $cscf->named_locations = array();

        ngx_queue_split($locations, $named, $tqueue);
        $i = 0;
        for ($tqueue->rewind();
             $tqueue->valid();
             $tqueue->next()
            )

        {
            $q  = $tqueue->current();
            $lq = $q;
            $clcfp[$i++] = $lq->exact;
        }

        $clcfp = NULL;

        ngx_queue_split($locations, $named, $tail);
    }


    return NGX_OK;
}

function ngx_http_cmp_locations( ngx_queue_t $one, ngx_queue_t $two)
{
//    ngx_int_t                   rc;
//    ngx_http_core_loc_conf_t   *first, *second;
//    ngx_http_location_queue_t  *lq1, *lq2;

    $lq1 = $one;
    $lq2 = $two;

    $first = $lq1->exact ? $lq1->exact : $lq1->inclusive;
    $second = $lq2->exact ? $lq2->exact : $lq2->inclusive;

    if ($first->noname && !$second->noname) {
    /* shift no named locations to the end */
        return 1;
    }

    if (!$first->noname && $second->noname) {
    /* shift no named locations to the end */
    return -1;
    }

    if ($first->noname || $second->noname) {
    /* do not sort no named locations */
    return 0;
    }

    if ($first->named && !$second->named) {
    /* shift named locations to the end */
    return 1;
    }

    if (!$first->named && $second->named) {
    /* shift named locations to the end */
    return -1;
    }

    if ($first->named && $second->named) {
    return ngx_strcmp($first->name, $second->name);
    }


    $rc = ngx_filename_cmp($first->name, $second->name,
                          ngx_min($first->name, $second->name) + 1);

    if ($rc == 0 && !$first->exact_match && $second->exact_match) {
    /* an exact match must be before the same inclusive one */
    return 1;
    }

    return $rc;
}


function ngx_http_init_static_location_trees(ngx_conf_t $cf, ngx_http_core_loc_conf_t $pclcf)
{
//ngx_queue_t                *q, *locations;
//    ngx_http_core_loc_conf_t   *clcf;
//    ngx_http_location_queue_t  *lq;

    $locations = $pclcf->locations;

    if ($locations == NULL) {
        return NGX_OK;
    }

    if ($locations->isEmpty()) {
        return NGX_OK;
    }

    for ($locations->rewind();
         $locations->vaild();
         $locations->next()
        )
    {
        $q = $locations->current();
        $lq = $q;

        $clcf = $lq->exact ? $lq->exact : $lq->inclusive;

        if (ngx_http_init_static_location_trees($cf, $clcf) != NGX_OK) {
            return NGX_ERROR;
        }
    }

    if (ngx_http_join_exact_locations($cf, $locations) != NGX_OK) {
        return NGX_ERROR;
    }

    ngx_http_create_locations_list($locations, 0);

    $pclcf->static_locations = ngx_http_create_locations_tree($cf, $locations, 0);
    if ($pclcf->static_locations == NULL) {
        return NGX_ERROR;
    }

    return NGX_OK;
}


function ngx_http_join_exact_locations(ngx_conf_t $cf, ngx_queue_t $locations)
{

//ngx_queue_t                *q, *x;
//    ngx_http_location_queue_t  *lq, *lx;

    $q = $locations->bottom();
    $qk = 0;

    while ($q != $locations->top()) {

        $locations->next();
        $x = $locations->current();

        $k = $locations->key();
        $lq =  $q;
        $lx =  $x;

        if (ngx_filename_cmp($lq->name, $lx->name, strlen($lx->name)) == 0)
        {
            if (($lq->exact && $lx->exact) || ($lq->inclusive && $lx->inclusive)) {
                ngx_log_error(NGX_LOG_EMERG, $cf->log, 0,
                                  "duplicate location \"%V\" in %s:%ui",
                                  array($lx->name, $lx->file_name, $lx->line));

                return NGX_ERROR;
            }

            $lq->inclusive = $lx->inclusive;

            $locations->offsetUnset($k);

            continue;
        }

        $qk += 1;
        $q = $locations->offsetGet($qk);
    }

    return NGX_OK;
}


function ngx_http_create_locations_list(ngx_queue_t $locations, $index)
{
//u_char                     *name;
//    size_t                      len;
//    ngx_queue_t                *x, tail;
//    ngx_http_location_queue_t  *lq, *lx;
    $tail = new ngx_queue_t();

    $q = $locations->offsetGet($index);
    if ($locations->offsetGet($index) == $locations->top()) {
        return;
    }
    $lq = $q;

    if ($lq->inclusive == NULL) {
        ngx_http_create_locations_list($locations, $index+1);
        return;
    }

    $name = $lq->name;
    $lk = $index;

    for ($x=$locations->offsetGet($lk);
         $x != $locations->top();
         $lk++)
    {
        $lx = $x;

        if (ngx_filename_cmp($name, $lx->name, strlen($name)) != 0)
        {
            break;
        }
    }

    $q = $locations->offsetGet(++$index);

    if ($q == $x) {
        ngx_http_create_locations_list($locations, $x);
        return;
    }

    ngx_queue_split($locations, $q, $tail);
    ngx_queue_add($lq->list, $tail);

    if ($x == $locations->top()) {
        ngx_http_create_locations_list($lq->list, 0);
        return;
    }

    ngx_queue_split($lq->list, $x, $tail);
    ngx_queue_add($locations, $tail);

    ngx_http_create_locations_list($lq->list, 0);

    ngx_http_create_locations_list($locations, $x);
}


