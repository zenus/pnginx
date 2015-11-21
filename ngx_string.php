<?php
/**
 * Created by PhpStorm.
 * User: zenus@github.com
 * Date: 2015/11/4
 * Time: 15:54
 */
function ngx_strcmp($s1, $s2){
   return strcmp($s1,$s2);
}

function ngx_strcasecmp($s1, $s2)
{
    return strcasecmp($s1,$s2);
}

function ngx_strncmp($s1, $s2, $n)
{
    return strncmp($s1, $s2, $n);
}
function ngx_strhas($s,$i){
   return isset($s[$i])&&!empty($s[$i]);
}

function ngx_sprintf($s, $fmt, $args)
{
    $p = ngx_vslprintf($s, $fmt, $args);

    return $p;
}

function ngx_atoi($str){

   return intval($str);
}

function ngx_atoof($line)
{
    return intval($line);
}


function ngx_slprintf($s,  $fmt, array $args = array())
{

    $p = ngx_vslprintf($s, $fmt, $args);

    return $p;
}


function ngx_vslprintf($s, $fmt, $args)
{
//    u_char                *p, zero;
//    int                    d;
//    double                 f;
//    size_t                 len, slen;
//    int64_t                i64;
//    uint64_t               ui64, frac;
//    ngx_msec_t             ms;
//    ngx_uint_t             width, sign, hex, max_width, frac_width, scale, n;
//    ngx_str_t             *v;
//    ngx_variable_value_t  *vv;

    $ptr = 0;
    while (!empty($fmt)) {

    /*
     * "buf < last" means that we could copy at least one character:
     * the plain character, "%%", "%c", and minus without the checking
     */

    if ($fmt[$ptr] == '%') {

        $i64 = 0;
        $ui64 = 0;

        $zero = (($fmt[++$ptr] == '0') ? '0' : ' ');
            $width = 0;
            $sign = 1;
            $hex = 0;
            $max_width = 0;
            $frac_width = 0;
            $slen =  -1;

            while ($fmt[$ptr] >= '0' && $fmt[$ptr] <= '9') {
            $width = $width * 10 + $fmt[$ptr++] - '0';
            }


            for ( ;; ) {
                switch ($fmt[$ptr]) {

                case 'u':
                    $sign = 0;
                    $ptr++;
                    continue;

                case 'm':
                    $max_width = 1;
                    $ptr++;
                    continue;

                case 'X':
                    $hex = 2;
                    $sign = 0;
                    $ptr++;
                    continue;

                case 'x':
                    $hex = 1;
                    $sign = 0;
                    $ptr++;
                    continue;

                case '.':
                    $ptr++;

                    while ($fmt[$ptr] >= '0' && $fmt[$ptr] <= '9') {
                        $frac_width = $frac_width * 10 + $fmt[$ptr++] - '0';
                    }

                    break;

                case '*':
                    $slen = array_shift($args);
                    $ptr++;
                    continue;

                default:
                    break;
                }

                break;
            }


            switch ($fmt[$ptr++]) {

        case 'V':

            $v = array_shift($args);
            $s .= $v;
            $ptr++;

                continue;

            case 'v':
                $v = array_shift($args);
                $s .= $v;
                $ptr++;
                continue;

            case 's':
                $ps = array_shift($args);

                $s .= $ps;

                $ptr++;

                continue;

            case 'O':
                $i64 = array_shift($args);
                $sign = 1;
                break;

            case 'P':
                $i64 = array_shift($args);
                $sign = 1;
                break;

            case 'T':
                $i64 = array_shift($args);
                $sign = 1;
                break;

            case 'M':
                $ms = array_shift($args);
                if ($ms == -1) {
                $sign = 1;
                $i64 = -1;
            } else {
                $sign = 0;
                $ui64 = $ms;
                }
                break;

            case 'z':
                if ($sign) {
                    $i64 = array_shift($args);
                } else {
                    $ui64 = array_shift($args);
                }
                break;

            case 'i':
                if ($sign) {
                    $i64 = array_shift($args);
                    //i64 = (int64_t) va_arg(args, ngx_int_t);
                } else {
                    $ui64 = array_shift($args);
                    //ui64 = (uint64_t) va_arg(args, ngx_uint_t);
                }

                if ($max_width) {
                    $width = PHP_INT_MAX;
                }

                break;

            case 'd':
                if ($sign) {
                    $i64 = array_shift($args);
                } else {
                    $ui64 = array_shift($args);
                }
                break;

            case 'l':
                if ($sign) {
                    $i64 = array_shift($args);
                } else {
                    $ui64 = array_shift($args);
                }
                break;

            case 'D':
                if ($sign) {
                    $i64 = array_shift($args);
                } else {
                    $ui64 = array_shift($args);
                }
                break;

            case 'L':
                if ($sign) {
                    $i64 = array_shift($args);
                } else {
                    $ui64 = array_shift($args);
                }
                break;

            case 'A':
                if ($sign) {
                    $i64 = array_shift($args);
                } else {
                    $ui64 = array_shift($args);
                }

                if ($max_width) {
                    $width = PHP_INT_MAX;
                }

                break;

            case 'f':
                $f = doubleval(array_shift($args));

                if ($f < 0) {
                    $s .= '-';
                    $f = -$f;
                }

                $ui64 = $f;
                $frac = 0;

                if ($frac_width) {

                    $scale = 1;
                    for ($n = $frac_width; $n; $n--) {
                        $scale *= 10;
                    }

                    $frac = (($f - doubleval($ui64)) * $scale + 0.5);

                    if ($frac == $scale) {
                        $ui64++;
                        $frac = 0;
                    }
                }

                $s = ngx_sprintf_num($s,  $ui64, $zero, 0, $width);

                if ($frac_width) {
                        $s .= '.';
                    $s = ngx_sprintf_num($s, $frac, '0', 0, $frac_width);
                }

                $ptr++;

                continue;

            case 'r':
                $i64 = array_shift($args);
                $sign = 1;
                break;

            case 'p':
                $ui64 = array_shift($args);
                $hex = 2;
                $sign = 0;
                $zero = '0';
                $width = 4 * 2;
                break;

            case 'c':
                $d = array_shift($args);
                $s .= $d & 0xff;
                $ptr++;

                continue;

            case 'Z':
                $s .= '\0';
                $ptr++;

                continue;

            case 'N':
                $s .= LF;
                $ptr++;

                continue;

            case '%':
                $s .= '%';
                $ptr++;

                continue;

            default:
                $s .= $args[$ptr];
                $ptr++;
                continue;
            }

            if ($sign) {
                if ($i64 < 0) {
                    $s .= '-';
                    $ui64 =  -$i64;

                } else {
                    $ui64 = $i64;
                }
            }

            $s = ngx_sprintf_num($s, $ui64, $zero, $hex, $width);

            $ptr++;

        } else {
        $s .= $args[$ptr];
        $ptr++;
        }
    }

    return $s;
}

function ngx_sprintf_num($s,  $ui64, $zero,
    $hexadecimal, $width)
{
//    u_char         *p, temp[NGX_INT64_LEN + 1];
//                       /*
//                        * we need temp[NGX_INT64_LEN] only,
//                        * but icc issues the warning
//                        */
//    size_t          len;
//    uint32_t        ui32;
       $l_hex = "0123456789abcdef";
       $u_hex = "0123456789ABCDEF";

//    p = temp + NGX_INT64_LEN;

    $p = '';
    if ($hexadecimal == 0) {

            do {
                $dn = $ui64 % 10 . '0';
                $p = $dn.$p;
            } while ($ui64 /= 10);

    } else if ($hexadecimal == 1) {

        do {

            /* the "(uint32_t)" cast disables the BCC's warning */
            $dn = $l_hex[($ui64 & 0xf)];
            $p = $dn.$p;

        } while ($ui64 >>= 4);

    } else { /* hexadecimal == 2 */

        do {

            $dn = $u_hex[($ui64 & 0xf)];
            $p = $dn.$p;

        } while ($ui64 >>= 4);
    }

    /* zero or space padding */

    $len = strlen(PHP_INT_MAX) - strlen($p);

    while ($len++ < $width) {
        $s .= $zero;
    }

    return $s.$p;
}


