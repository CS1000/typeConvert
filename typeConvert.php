<?php
/***********************************************************************
* Proper Type Conversion in PHP UserLand (Standard Aware)
* Extra crispy layer, STRICT + Greedy/Loose flavors
*
* Original Author: CS`
* Licence: MIT & FreeBSD
*
* Ref.: http://docs.oracle.com/cd/E19957-01/806-3568/ncg_goldberg.html
* Standards: 
  *  ISO/IEC 10967
  *  ISO/IEC 11404:2007
  *  ISO/IEC 60559:2011 (IEEE 754:2008) (*partial)
***********************************************************************/

class tc {

//STRICT not fully implemented yet !!! 
//toString/string_to mostly NOT implemented yet !!! 
// ^--- TODO: remove me by implementing those everywhere

    private $strict; //define loose/greedy conversion VS. Strict loseless or error
    
    //error hangling
    const EXCEPTIONS = 0;
    const LOGS = 2; //unused


    
    public function __construct($strict=false)
    {
        // need max ints overhead?

        $this->strict=$strict;
    }
    public function strict($strict=true) { 
        $this->strict=$strict;
    }

    
    /*oooooooooo
    `888'     `8
     888         oooo d8b oooo d8b  .ooooo.  oooo d8b  .oooo.o
     888oooo8    `888""8P `888""8P d88' `88b `888""8P d88(  "8
     888    "     888      888     888   888  888     `"Y88b.
     888       o  888      888     888   888  888     o.  )88b
    o888ooooood8 d888b    d888b    `Y8bod8P' d888b    8""888*/

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
            if ($this->strict) $error.='Strict mode. ';
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

    /*ooooooo.                             .
    `888   `Y88.                         .o8
     888   .d88'  .ooooo.  oooo  oooo  .o888oo  .ooooo.  oooo d8b
     888ooo88P'  d88' `88b `888  `888    888   d88' `88b `888""8P
     888`88b.    888   888  888   888    888   888ooo888  888
     888  `88b.  888   888  888   888    888 . 888    .o  888
    o888o  o888o `Y8bod8P'  `V88V"V8P'   "888" `Y8bod8P' d88*/

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
        
        //try {
        $type=$types[gettype($var)];
        //} catch (Exception $e) {
        //    return $this->conversionError("Error Converting ".strtoupper(gettype($var)).". (".$e.")"); //need rewrite <-TODO
        //}
        
        if ($type===$to) return $var; //bypass conversion if it already is the destination type
        
        $route=$type.'_to'.$to;
        if (method_exists($this, $route)) return $this->$route($var);
        
        return $this->conversionError("Error Converting ".strtoupper($type)." to ".strtoupper($to).". Nothing to do."); //need rewrite <-TODO
    }


    /*ooo                 .
    `888'               .o8
     888  ooo. .oo.   .o888oo  .ooooo.   .oooooooo  .ooooo.  oooo d8b
     888  `888P"Y88b    888   d88' `88b 888' `88b  d88' `88b `888""8P
     888   888   888    888   888ooo888 888   888  888ooo888  888
     888   888   888    888 . 888    .o `88bod8P'  888    .o  888
    o888o o888o o888o   "888" `Y8bod8P' `8oooooo.  `Y8bod8P' d888b
                                        d"     YD
                                        "Y88888*/
    
    //Integers (int/integer)

    public function toInt($var) { return $this->unknown_to($var, 'int'); }
    //public function toInteger($var) { return $this->unknown_to($var, 'int'); }


    //public function boolean_toInt($var) { return $this->bool_toInt($var); }
    public function bool_toInt($var) 
    { 
        $converted=(int)$var; //loseless conversion
        return $converted;
    }

    /*
    public function double_toInt($var) { return $this->float_toInt($var); }
    public function real_toInt($var) { return $this->float_toInt($var); }
    */
    public function float_toInt($var) 
    { 
        $converted=(int)$var;
        if ($this->strict) {
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

    //public function string_toInt($var) { return $this->str_toInt($var); }
    public function str_toInt($var) 
    { 
        $converted=(int)$var;
        
        if ($this->strict) {
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



    /*oooooooooo oooo                          .
    `888'     `8 `888                        .o8
     888          888   .ooooo.   .oooo.   .o888oo  .oooo.o
     888oooo8     888  d88' `88b `P  )88b    888   d88(  "8
     888    "     888  888   888  .oP"888    888   `"Y88b.
     888          888  888   888 d8(  888    888 . o.  )88b
    o888o        o888o `Y8bod8P' `Y888""8o   "888" 8""888*/

    //Floating Point Numbers (real/double/float)

    public function toFloat($var) { return $this->unknown_to($var, 'float'); }
    //public function toDouble($var) { return $this->unknown_to($var, 'float'); }
    //public function toReal($var) { return $this->unknown_to($var, 'float'); }


    /*public function integer_toDouble($var) { return $this->int_toFloat($var); }
    public function int_toDouble($var) { return $this->int_toFloat($var); }
    public function integer_toReal($var) { return $this->int_toFloat($var); }
    public function int_toReal($var) { return $this->int_toFloat($var); }
    public function integer_toFloat($var) { return $this->int_toFloat($var); }
    */
    public function int_toFloat($var) 
    {
        // CAST
        return (float)$var; //loseless
    }

    /*
    public function string_toDouble($var) { return $this->str_toFloat($var); }
    public function str_toDouble($var) { return $this->str_toFloat($var); }
    public function string_toReal($var) { return $this->str_toFloat($var); }
    public function str_toReal($var) { return $this->str_toFloat($var); }
    public function string_toFloat($var) { return $this->str_toFloat($var); }
    */
    public function str_toFloat($var) 
    {
        $converted=(float)$var;
        if ($converted==$var) {
            //pretty good so far
            //return $converted;

            // TODO TODO TODO TODO ------ BROKEN
        } 
        if ($this->strict) return $this->conversionError(431);
        if (preg_match('/^(-)?INF$/i',$var, $m)===1) {
            if (isset($m[1])) return -INF;
            return INF;
        }

        /**
        * TODO !!!
        */

        return NAN;
    }

    /*
    public function boolean_toDouble($var) { return $this->bool_toFloat($var); }
    public function bool_toDouble($var) { return $this->bool_toFloat($var); }
    public function boolean_toReal($var) { return $this->bool_toFloat($var); }
    public function bool_toReal($var) { return $this->bool_toFloat($var); }
    public function boolean_toFloat($var) { return $this->bool_toFloat($var); }
    */
    public function bool_toFloat($var) 
    {
        return (float)$var; //loseless
    }
    
    public function null_toFloat($var) 
    { 
        return 0e0; 
    }



    /*oooooooo.                      oooo
    `888'   `Y8b                     `888
     888     888  .ooooo.   .ooooo.   888   .ooooo.   .oooo.   ooo. .oo.
     888oooo888' d88' `88b d88' `88b  888  d88' `88b `P  )88b  `888P"Y88b
     888    `88b 888   888 888   888  888  888ooo888  .oP"888   888   888
     888    .88P 888   888 888   888  888  888    .o d8(  888   888   888
    o888bood8P'  `Y8bod8P' `Y8bod8P' o888o `Y8bod8P' `Y888""8o o888o o88*/

    //Boolean (bool/boolean)

    public function toBool($var) { return $this->unknown_to($var, 'bool'); }
    //public function toBoolean($var) { return $this->unknown_to($var, 'bool'); }


    //public function integer_toBool($var) { return $this->int_toBool($var); }
    public function int_toBool($var) 
    {
        return $var>0;
        // 0++ true
        //-1-- false
    }

    /*
    public function real_toBool($var) { return $this->float_toBool($var); }
    public function double_toBool($var) { return $this->float_toBool($var); }
    */
    public function float_toBool($var) 
    {
        return $var>0 or is_nan($var);
        //return $var>0 ?: $var<0;
        // $var>0 sets NAN -> false
        // (bool) sets NAN -> false, -INF -> true
    }

    //public function string_toBool($var) { return $this->str_toBool($var); }
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



    
     /*ooooo..o     .             o8o
    d8P'    `Y8   .o8             `"'
    Y88bo.      .o888oo oooo d8b oooo  ooo. .oo.    .oooooooo
     `"Y8888o.    888   `888""8P `888  `888P"Y88b  888' `88b
         `"Y88b   888    888      888   888   888  888   888
    oo     .d8P   888 .  888      888   888   888  `88bod8P'
    8""88888P'    "888" d888b    o888o o888o o888o `8oooooo.
                                                   d"     YD
                                                   "Y88888*/
    //Strings (string)

    public function toStr($var) { return $this->unknown_to($var, 'str'); }
    //public function toString($var) { return $this->unknown_to($var, 'str'); }


    //public function integer_toStr($var) { return $this->int_toStr($var); }
    public function int_toStr($var) 
    {
        return (string)$var; 
    }

    /*
    public function real_toStr($var) { return $this->float_toStr($var); }
    public function double_toStr($var) { return $this->float_toStr($var); }
    */
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

    //public function boolean_toStr($var) { return $this->bool_toStr($var); }
    public function bool_toStr($var) 
    {
        //loseless 
        return $var?'true':'false'; 
        //BUT which one complies best?
        //return $var?'1':'0';
        //return (string)($var+0);
        //return (string)(int)$var;
    }

    public function null_toStr($var) 
    { 
        //loseless
        return ""; 
        //BUT which one complies best?
        //return "\0"; //string with NUL byte
    }


    /*ooo      ooo ooooo     ooo ooooo        ooooo
    `888b.     `8' `888'     `8' `888'        `888'
     8 `88b.    8   888       8   888          888
     8   `88b.  8   888       8   888          888
     8     `88b.8   888       8   888          888
     8       `888   `88.    .8'   888       o  888       o
    o8o        `8     `YbodP'    o888ooooood8 o888oooooo*/

    //public function toNIL($var) { return $this->toNULL($var); } //add more boilerplate
    public function toNULL($var) 
    { 
        //can't actually convert anything into NULL if it is not effectively NULL
        if ($this->strict) return $this->conversionError(801);
        
        return NULL; //complete loss of data.
    }

    /******************************************************************/
}