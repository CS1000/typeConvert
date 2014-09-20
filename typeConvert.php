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

class tc {

    //STRICT not fully implemented yet !!!
    const STRICT = true; //define loose/greedy conversion VS. Strict loseless or error
    
    //error hangling
    const EXCEPTIONS = 0;
    const LOGS = 2;
    

    // need max ints overhead?

    private function conversionError($code) 
    {
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
            if (self::STRICT) $error.='Strict mode. ';
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
        if (self::EXCEPTIONS) throw new Exception($error);
        error_log($error);
        return null; // <--- !!! NULL 
    }

    private function unknown_to($var, $to) 
    {
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
            return $this->conversionError("Error Converting ".strtoupper(gettype($var)).". (".$e.")"); //need rewrite <-TODO
        }
        
        if ($type===$to) return $var; //bypass conversion if it already is the destination type
        
        $route=$type.'_to'.$to;
        if (method_exists($this, $route)) return $this->$route($var);
        
        return $this->conversionError("Error Converting ".strtoupper($type)." to ".strtoupper($to).". Nothing to do."); //need rewrite <-TODO
    }



    /*******************************************************************
    * INTEGERS
    * Int *whole numbers
    *******************************************************************/
    public function toInt($var) { return $this->unknown_to($var, 'int'); }
    public function toInteger($var) { return $this->unknown_to($var, 'int'); }


    public function boolean_toInt($var) { return $this->bool_toInt($var); }
    public function bool_toInt($var) 
    { 
        $converted=(int)$var; //loseless conversion
        return $converted;
    }

    public function double_toInt($var) { return $this->float_toInt($var); }
    public function real_toInt($var) { return $this->float_toInt($var); }
    public function float_toInt($var) 
    { 
        $converted=(int)$var;
        if (self::STRICT) {
            // Integer does not have: [-0, INF, -INF, NAN]
            if ($converted==$var) {
                return $converted;
            } else {
                return $this->conversionError(321);
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

    public function string_toInt($var) { return $this->str_toInt($var); }
    public function str_toInt($var) 
    { 
        $converted=(int)$var;
        
        if (self::STRICT) {
            if ($converted==$var) {
                return $converted;
            } else {
                return $this->conversionError(421);
            }
        }

        /**
        *TODO ?
        */
        
        //convert as much possible
        return $converted;
    }

    public function null_toInt($var) 
    { 
        return 0; 
    }



    /*******************************************************************
    * FLOATS
    * Floating Point *real/double/float
    *******************************************************************/
    public function toFloat($var) { return $this->unknown_to($var, 'float'); }
    public function toDouble($var) { return $this->unknown_to($var, 'float'); }
    public function toReal($var) { return $this->unknown_to($var, 'float'); }


    public function integer_toDouble($var) { return $this->int_toFloat($var); }
    public function int_toDouble($var) { return $this->int_toFloat($var); }
    public function integer_toReal($var) { return $this->int_toFloat($var); }
    public function int_toReal($var) { return $this->int_toFloat($var); }
    public function integer_toFloat($var) { return $this->int_toFloat($var); }
    public function int_toFloat($var) 
    {
        // CAST
        return (float)$var; //loseless
    }

    public function string_toDouble($var) { return $this->str_toFloat($var); }
    public function str_toDouble($var) { return $this->str_toFloat($var); }
    public function string_toReal($var) { return $this->str_toFloat($var); }
    public function str_toReal($var) { return $this->str_toFloat($var); }
    public function string_toFloat($var) { return $this->str_toFloat($var); }
    public function str_toFloat($var) 
    {
        $converted=(float)$var;
        if ($converted==$var) {
            //pretty good so far
            return $converted;
        } 
        if (self::STRICT) return $this->conversionError(431);
        if (preg_match('/^(-)?INF$/i',$var, $m)===1) {
            if ($m[1]) return -INF;
            return INF;
        }

        return NAN;
    }

    public function boolean_toDouble($var) { return $this->bool_toFloat($var); }
    public function bool_toDouble($var) { return $this->bool_toFloat($var); }
    public function boolean_toReal($var) { return $this->bool_toFloat($var); }
    public function bool_toReal($var) { return $this->bool_toFloat($var); }
    public function boolean_toFloat($var) { return $this->bool_toFloat($var); }
    public function bool_toFloat($var) 
    {
        return (float)$var; //loseless
    }
    
    public function null_toFloat($var) 
    { 
        return 0e0; 
    }



    /*******************************************************************
    * BOOLEANS
    * Bool *logical: true/false
    *******************************************************************/
    public function toBool($var) { return $this->unknown_to($var, 'bool'); }
    public function toBoolean($var) { return $this->unknown_to($var, 'bool'); }


    public function integer_toBool($var) { return $this->int_toBool($var); }
    public function int_toBool($var) 
    {
        return $var>0;
        // 0++ true
        //-1-- false
    }

    public function real_toBool($var) { return $this->float_toBool($var); }
    public function double_toBool($var) { return $this->float_toBool($var); }
    public function float_toBool($var) 
    {
           //false <0 &  //fix NaN
        return $var>0 or is_nan($var);
        // $var>0 sets NAN -> false
        // (bool) sets NAN -> false, -INF -> true
    }

    public function string_toBool($var) { return $this->str_toBool($var); }
    public function str_toBool($var) {
        //string can include [bool+int+float]
        //should it return false for floats < 0 also??? <-- QUESTION / TODO: config

        //allow whitespace** + "false" or zero(even float in sci. not.)
        $falseRegex='/\A[\0\pC\pZ\s]*+(-?+0([.]0*+)?(e[0-9]*+)?+|false)?+[\0\pC\pZ\s]*+\Z/i'; //wow, wtf
        return $var!=='' and preg_match($falseRegex, $var)!==1;
    }

    public function null_toBool($var) 
    { 
        return false; 
    }



    /*******************************************************************
    * STRINGS
    * Strings *char_array
    *******************************************************************/
    public function toStr($var) { return $this->unknown_to($var, 'str'); }
    public function toString($var) { return $this->unknown_to($var, 'str'); }


    public function integer_toStr($var) { return $this->int_toStr($var); }
    public function int_toStr($var) 
    {
        return (string)$var; 
    }

    public function real_toStr($var) { return $this->float_toStr($var); }
    public function double_toStr($var) { return $this->float_toStr($var); }
    public function float_toStr($var) 
    {
        //precise vs. any other choice (by 2 digits)
        //input:      (double)     1.12345678901234567890e9
        //Converted:  (string(27) "1123456789.0123457908630371" 
        //Cast:       (string(15) "1123456789.0123" ^inexact from here
        return sprintf("%.16F",$var); //not nice for 1e300 (ends in: .0000000000000000)
        // const M_PI FAIL (last digit)!!! 
        //FAIL for -INF !!!!!!!!!!!!!!!
        //FAIL for 2.8 !!!!!!!!!!!!!!!
    }

    public function boolean_toStr($var) { return $this->bool_toStr($var); }
    public function bool_toStr($var) 
    {
        return $var?'true':'false';
        //other
        //return $var?'1':'0';
        //return (string)($var+0);
    }

    public function null_toStr($var) 
    { 
        //return "\0"; 
        return ""; 
    }


    
    /*******************************************************************
    * NULL
    * null *nothing/nil/nada
    *******************************************************************/
    public function toNIL($var) { return $this->toNULL($var); } //add more boilerplate
    public function toNULL($var) 
    { 
        if (self::STRICT) {
            //refuse !
            return $this->conversionError(801);
        }
        return NULL;
    }

    /******************************************************************/
}