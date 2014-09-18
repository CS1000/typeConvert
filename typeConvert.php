<?php
/***********************************************************************
* Proper Type Conversion in PHP UserLand (Standard Aware)
* Extra crispy layer, STRICT + Greedy/Loose flavors
*
* Original Author: CS`
* Licence: MIT & FreeBSD
*
* Ref. : http://docs.oracle.com/cd/E19957-01/806-3568/ncg_goldberg.html
* Ref. : ISO/IEC 10967
***********************************************************************/

    //STRICT not fully implemented yet !!!
const STRICT = false; //define loose/greedy conversion VS. Strict loseless or error
const EXCEPTIONS = true;
// need max ints overhead?

//private
function conversionError($code) {
    //nine (9) types reported by gettype()
    //eg. 832 => 'Error Converting NULL to Float. {STRICT} mode. No Method Available. '
    $errors=[
          0=>'$variable',
          1=>"Boolean",
          2=>"Integer",
          3=>"Float",
          4=>"String",
          5=>"Array",
          6=>"Object",
          7=>"Resource",
          8=>"NULL",
          9=>"Unknown"
    ];

    $code=(string)$code;

    if (preg_match('/^[0-9]{3}$/', $code)===1) {
        $error='Error Converting '.$errors[$code[0]].' to '.$errors[$code[1]].'. ';
        if (STRICT) $error.='Strict mode. ';
        else $error.='Greedy/Loose mode. ';
        switch ($code[2]) {
            case 0:
                $error.='Greedy/Loose call. ';
                break;
            case 1:
                $error.='Strict call. ';
                break;
            case 2:
                $error.='No Method Available. ';
                break;
            case 3:
                $error.='Could not compute. ';
                break;
            default:
                $error.='WTF ??!?! ';
                break;
        }
    } else $error=$code;
    if (EXCEPTIONS) throw new Exception($error);
    error_log($error);
    return null; // <--- !!! NULL 
}

//private
function unknown_to($var, $to) {
    $types=[
        //scalar
        'boolean'=>'bool', 
        'integer'=>'int', 
        'double' =>'float', 
        'string' =>'str', 
        
        //compound
        'array'=>'array', //not implemented, TODO
        'object'=>'obj', //not implemented, TODO
        //SPL types ?

        //special
        'resource'=>'resource', //not implemented, TODO
        'NULL'   =>'null',
        
        //pseudo-type (for readability)
        // mixed, number/numeric, callback
        // +                     ^
        'unknown type'=>'unknown' //not implemented, TODO
    ];
    
    try { //gettype() failsafe
        $type=$types[gettype($var)];
    } catch (Exception $e) {
        conversionError("Error Converting ".strtoupper(gettype($var)).". (".$e.")"); //need rewrite <-TODO
    }
    
    if ($type===$to) return $var; //bypass conversion if it already is the destination type
    
    $route=$type.'_to'.$to;
    if (function_exists($route)) return $route($var);
    conversionError("Error Converting ".strtoupper($type)." to ".strtoupper($to).". No Method Available."); //need rewrite <-TODO
}


<<<INTEGERS
        Int *whole numbers
INTEGERS;
{
    function toInt($var) { return unknown_to($var, 'int'); }
    function toInteger($var) { return unknown_to($var, 'int'); }

    function boolean_toInt($var) { return bool_toInt($var); }
    function bool_toInt($var) { 
        $converted=(int)$var; //loseless conversion
        return $converted;
    }

    function double_toInt($var) { return float_toInt($var); }
    function real_toInt($var) { return float_toInt($var); }
    function float_toInt($var) { 

        $converted=(int)$var;
        if (STRICT) {
            // Integer does not have: [-0, INF, -INF, NAN]
            if ($converted==$var) {
                return $converted;
            } else {
                conversionError(321);
            }
        }

        $realValue=var_export($var, true); //actual usable value
        
        //can't use a switch here, comp is loose!
        if ($realValue==='INF') {
            //fix: -PHP_INT_MAX return
            return PHP_INT_MAX;
        }    
        /*elseif ($realValue==='-INF) {
            //fix Future: https://wiki.php.net/rfc/integer_semantics
            return -PHP_INT_MAX; 
        }*/
        elseif ($realValue==='NAN') {
            return 0;
        }
        else {
            //fix: ints are automagically converted to floats after INT_MAX
            if ($var>=PHP_INT_MAX) { 
                return PHP_INT_MAX;
            } elseif ($var<=-PHP_INT_MAX) { 
                return -PHP_INT_MAX;
            }
            return (int)round($var); //return a ROUNDED int, VS. a Floored one 
        }
    }


    function string_toInt($var) { return str_toInt($var); }
    function str_toInt($var) { 
        $converted=(int)$var;
        
        if (STRICT) {
            if ($converted==$var) {
                return $converted;
            } else {
                conversionError(421);
            }
        }

        /**
        *TODO ?
        */
        
        //convert as much possible
        return $converted;
    }

    //only generic needed
    function null_toInt($var) { return 0; }
}


<<<FLOATS
        Floating Point *real/double/float
FLOATS;
{
    function toFloat($var) { return unknown_to($var, 'float'); }
    function toDouble($var) { return unknown_to($var, 'float'); }
    function toReal($var) { return unknown_to($var, 'float'); }

    function integer_toDouble($var) { return int_toFloat($var); }
    function int_toDouble($var) { return int_toFloat($var); }
    function integer_toReal($var) { return int_toFloat($var); }
    function int_toReal($var) { return int_toFloat($var); }
    function integer_toFloat($var) { return int_toFloat($var); }
    function int_toFloat($var) {
        return (float)$var; //loseless
    }

    function string_toDouble($var) { return str_toFloat($var); }
    function str_toDouble($var) { return str_toFloat($var); }
    function string_toReal($var) { return str_toFloat($var); }
    function str_toReal($var) { return str_toFloat($var); }
    function string_toFloat($var) { return str_toFloat($var); }
    function str_toFloat($var) {
        $converted=(float)$var;
        if ($converted==$var) {
            //pretty good so far
            return $converted;
        } 
        if (STRICT) conversionError(431);
        if (preg_match('/^(-)?INF$/i',$var, $m)===1) {
            if ($m[1]) return -INF;
            return INF;
        }

        return NAN;
    }

    function boolean_toDouble($var) { return bool_toFloat($var); }
    function bool_toDouble($var) { return bool_toFloat($var); }
    function boolean_toReal($var) { return bool_toFloat($var); }
    function bool_toReal($var) { return bool_toFloat($var); }
    function boolean_toFloat($var) { return bool_toFloat($var); }
    function bool_toFloat($var) {
        return (float)$var; //loseless
    }
    
    //only generic needed
    function null_toFloat($var) { return 0e0; }
}


<<<BOOLEANS
        Bool *logical: true/false
BOOLEANS;
{
    function toBool($var) { return unknown_to($var, 'bool'); }
    function toBoolean($var) { return unknown_to($var, 'bool'); }

    function integer_toBool($var) { return int_toBool($var); }
    function int_toBool($var) {
        return $var>0;
        // 0++ true
        //-1-- false
    }

    function real_toBool($var) { return float_toBool($var); }
    function double_toBool($var) { return float_toBool($var); }
    function float_toBool($var) {
           //false <0 &  //fix NaN
        return $var>0 or is_nan($var);
        // $var>0 sets NAN -> false
        // (bool) sets NAN -> false, -INF -> true
    }

    function string_toBool($var) { return str_toBool($var); }
    function str_toBool($var) {
        //string can include [bool+int+float]
        //should it return false for floats < 0 also??? <-- QUESTION / TODO: config

        //allow whitespace** + "false" or zero(even float in sci. not.)
        $falseRegex='/\A[\0\pC\pZ\s]*+(-?+0([.]0*+)?(e[0-9]*+)?+|false)?+[\0\pC\pZ\s]*+\Z/i'; //wow, wtf
        return $var!=='' and preg_match($falseRegex, $var)!==1;
    }

    function null_toBool($var) { return false; }
}


<<<STRINGS
        Strings *char_array
STRINGS;
{
    function toStr($var) { return unknown_to($var, 'str'); }
    function toString($var) { return unknown_to($var, 'str'); }

    function integer_toStr($var) { return int_toStr($var); }
    function int_toStr($var) {
        return (string)$var; 
    }

    function real_toStr($var) { return float_toStr($var); }
    function double_toStr($var) { return float_toStr($var); }
    function float_toStr($var) {
        //precise vs. any other choice (by 2 digits)
        //input:      (double)     1.12345678901234567890e9
        //Converted:  (string(27) "1123456789.0123457908630371" 
        //Cast:       (string(15) "1123456789.0123" ^inexact from here
        return sprintf("%.16F",$var); //not nice for 1e300 (ends in: .0000000000000000)
        // const M_PI FAIL (last digit)!!! 
        //FAIL for -INF !!!!!!!!!!!!!!!
        //FAIL for 2.8 !!!!!!!!!!!!!!!
    }

    function boolean_toStr($var) { return bool_toStr($var); }
    function bool_toStr($var) {
        return $var?'true':'false';
        //other
        //return $var?'1':'0';
        //return (string)($var+0);
    }

    function null_toStr($var) { 
        //return "\0"; 
        return ""; 
    }
}


<<<NULL
        null *nothing/nil/nada
NULL;
{
    function toNIL($var) { return toNULL($var); }
    function toNULL($var) { 
        if (STRICT) {
            //refuse !
            conversionError(801);
        }
        return NULL;
    }
}
