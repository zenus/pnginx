<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 15-11-8
 * Time: 上午10:40
 */
//define('NGX_INT32_LEN',strlen("-2147483648") - 1);
#define NGX_INT64_LEN   (sizeof("-9223372036854775808") - 1)
define('NGX_MAX_INT_T_VALUE', PHP_INT_MAX);