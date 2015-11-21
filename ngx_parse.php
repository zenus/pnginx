<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 15-11-21
 * Time: 上午10:38
 */

function ngx_parse_time($line, $is_sec = true)
{
    return strtotime($line);
}


function ngx_parse_offset($line)
{
//u_char  unit;
//    off_t   offset, scale, max;
//    size_t  len;

    $len = strlen($line);
    $unit = $line[$len - 1];
    switch ($unit) {
        case 'K':
        case 'k':
            $len--;
            $max = NGX_MAX_OFF_T_VALUE / 1024;
            $scale = 1024;
            break;

        case 'M':
        case 'm':
            $len--;
            $max = NGX_MAX_OFF_T_VALUE / (1024 * 1024);
            $scale = 1024 * 1024;
            break;

        case 'G':
        case 'g':
            $len--;
            $max = NGX_MAX_OFF_T_VALUE / (1024 * 1024 * 1024);
            $scale = 1024 * 1024 * 1024;
            break;

        default:
            $max = NGX_MAX_OFF_T_VALUE;
            $scale = 1;
    }

    $offset = ngx_atoof(substr($line,0,$len));
    if ($offset == NGX_ERROR || $offset > $max) {
        return NGX_ERROR;
    }

    $offset *= $scale;

    return $offset;
}