<?php
require 'typeConvert.php';

//fugly unit tests

class tcTests extends PHPUnit_Framework_TestCase
{
    
    public function testStrToInt()
    {
        $this->markTestSkipped('too many unknowns');

        $q=[];
        $q[]=["INF", PHP_INT_MAX];
        $q[]=["-INF", -PHP_INT_MAX];
        $q[]=["NAN", 0];
        $q[]=["", 0];
        $q[]=['0', 0];
        $q[]=['false', 0];
        $q[]=['FALSE', 0];
        $q[]=['False', 0];
        $q[]=['true', 1]; //?
        $q[]=['TRUE', 1]; //?
        $q[]=['True', 1];  //?
        $q[]=['1', 1];
        $q[]=['01', 1]; //?
        $q[]=['001', 1]; //?
        $q[]=['text', 0];
        $q[]=['-01', -1]; //?
        $q[]=['-001', -1]; //?
        $q[]=['-', 0];
        $q[]=["\0", 0];
        $q[]=["\0\0\0\0\0\0\0", 0];

        $tc=new tc();
        $tc->strict(false);
        foreach ($q as $a) {
            //PHP's cast behaviour
            //$this->assertSame((int)$a[0], $tc->toInt($a[0]));
            
            if (!$this->assertSame($a[1], $tc->toInt($a[0]))) var_dump($a);
        }
    }

    public function testFloatToInt()
    {
        $q=[];
        $q[]=[INF, PHP_INT_MAX];
        $q[]=[-INF, -PHP_INT_MAX];
        $q[]=[NAN, 0];
        
        $q[]=[3.14158, 3];
        $q[]=[M_PI, 3];
        
        $q[]=[2.1, 2];
        $q[]=[2.5, 3];
        $q[]=[2.8, 3];
        
        $q[]=[0.8, 1];
        $q[]=[0.7, 1];
        $q[]=[0.1, 0];

        $q[]=[0e0, 0];
        $q[]=[1e0, 1];
        $q[]=[1e1, 10];
        $q[]=[1e2, 100];
        $q[]=[1e3, 1000];
        $q[]=[1e4, 10000];
        $q[]=[1e5, 100000];
        $q[]=[1e6, 1000000];
        $q[]=[1e7, 10000000];
        $q[]=[1e8, 100000000];
        $q[]=[1e9, 1000000000];
        $q[]=[1e10, 10000000000];
        $q[]=[1e11, 100000000000];
        $q[]=[1e12, 1000000000000];
        $q[]=[1e13, 10000000000000];
        $q[]=[8e18, 8000000000000000000];
        $q[]=[9e18, 9000000000000000000];
        $q[]=[9.1e18, 9100000000000000000];
        $q[]=[9.2e18, 9200000000000000000];
        //$q[]=[9.3e18, 9300000000000000000]; //maxint already, STRICT
        $q[]=[1e19, PHP_INT_MAX];
        $q[]=[1e53, PHP_INT_MAX];
        $q[]=[1e301, PHP_INT_MAX];
        $q[]=[1e302, PHP_INT_MAX];
        $q[]=[1e303, PHP_INT_MAX];
        $q[]=[1e304, PHP_INT_MAX];
        $q[]=[1e305, PHP_INT_MAX];
        $q[]=[1e306, PHP_INT_MAX];
        $q[]=[1e307, PHP_INT_MAX];
        $q[]=[1e308, PHP_INT_MAX];
        $q[]=[1e309, PHP_INT_MAX];
        $q[]=[1e310, PHP_INT_MAX];
        $q[]=[1e311, PHP_INT_MAX];
        $q[]=[1e312, PHP_INT_MAX];
        $q[]=[1e313, PHP_INT_MAX];
        $q[]=[1e314, PHP_INT_MAX];
        $q[]=[1e315, PHP_INT_MAX];
        $q[]=[1e316, PHP_INT_MAX];
        $q[]=[1e317, PHP_INT_MAX];
        $q[]=[1e318, PHP_INT_MAX];
        $q[]=[1e319, PHP_INT_MAX];
        $q[]=[1e320, PHP_INT_MAX];
        $q[]=[1e321, PHP_INT_MAX];
        
        $tc=new tc();
        $tc->strict(false);
        foreach ($q as $a) {
            //PHP's cast behaviour
            //$this->assertSame((int)$a[0], $tc->toInt($a[0]));
            
            $this->assertSame($a[1], $tc->toInt($a[0]));
        }

    }

    public function testBoolToInt()
    {
        $q=[];
        $q[]=[true, 1];
        $q[]=[false, 0];
        
        $tc=new tc();
        $tc->strict(false);
        foreach ($q as $a) {
            $this->assertSame($a[1], $tc->toInt($a[0]));
        }

    }

    public function testNULLtoInt()
    {
        $q=[];
        $q[]=[NULL, 0];
        
        $tc=new tc();
        $tc->strict(false);
        foreach ($q as $a) {
            $this->assertSame($a[1], $tc->toInt($a[0]));
        }

    }

    //FLOATS
    //FLOATS
    //FLOATS
    public function testStrToFloat() {
        $q=[];
        $q[]=["INF", INF];
        $q[]=["-INF", -INF];
        $q[]=["NAN", NAN];
        $q[]=["NaN", NAN];
        $q[]=["nan", NAN];
        $q[]=["", 0];
        $q[]=['0', 0];
        $q[]=['false', 0];
        $q[]=['FALSE', 0];
        $q[]=['False', 0];
        $q[]=['true', 1]; //?
        $q[]=['TRUE', 1]; //?
        $q[]=['True', 1];  //?
        $q[]=['1', 1];
        $q[]=['01', 1]; //?
        $q[]=['001', 1]; //?
        $q[]=['text', 0];
        $q[]=['-01', -1]; //?
        $q[]=['-001', -1]; //?
        $q[]=['-', 0];
        $q[]=["\0", 0];
        $q[]=["\0\0\0\0\0\0\0", 0];

        $tc=new tc();
        $tc->strict(false);
        $this->assertSame(INF, $tc->toFloat("INF"));
        $this->assertSame(-INF, $tc->toFloat("-INF"));
        $this->assertSame(true, is_nan($tc->toFloat("NAN")));
        $this->assertSame(true, is_nan($tc->toFloat("NaN")));
        $this->assertSame(true, is_nan($tc->toFloat("nan")));
    }

    public function testBoolToFloat()
    {
        $q=[];
        $q[]=[true, 1.0];
        $q[]=[false, 0.0];
        
        $tc=new tc();
        $tc->strict(false);
        foreach ($q as $a) {
            $this->assertSame($a[1], $tc->toFloat($a[0]));
        }

    }

    public function testNULLtoFloat()
    {
        $q=[];
        $q[]=[NULL, 0e0];
        
        $tc=new tc();
        $tc->strict(false);
        foreach ($q as $a) {
            $this->assertSame($a[1], $tc->toFloat($a[0]));
        }

    }

    //to NULL
    //to NULL
    //to NULL
    public function testBoolToNULL()
    {
        $q=[];
        $q[]=[true, null];
        $q[]=[false, null];
        
        $tc=new tc();
        $tc->strict(false);
        foreach ($q as $a) {
            $this->assertSame($a[1], $tc->toNULL($a[0]));
        }

    }

    // to BOOLEAN
    // to BOOLEAN
    // to BOOLEAN
    public function testNULLtoBool()
    {
        $q=[];
        $q[]=[NULL, false];
        
        $tc=new tc();
        $tc->strict(false);
        foreach ($q as $a) {
            $this->assertSame($a[1], $tc->toBool($a[0]));
        }

    }

    public function testIntToBool()
    {
        $q=[];
        $q[]=[1, true];
        $q[]=[2, true];
        $q[]=[PHP_INT_MAX, true];
        $q[]=[0, false];
        $q[]=[-1, false];
        $q[]=[-2, false];
        $q[]=[-PHP_INT_MAX, false];
        
        $tc=new tc();
        $tc->strict(false);
        foreach ($q as $a) {
            $this->assertSame($a[1], $tc->toBool($a[0]));
        }

    }

    public function testFloatToBool()
    {
        $tc=new tc();
        $tc->strict(false);
        $this->assertSame(true, $tc->toBool(0.1));
        $this->assertSame(true, $tc->toBool(0.01));
        $this->assertSame(true, $tc->toBool(0.001));
        $this->assertSame(true, $tc->toBool(0.0001));
        $this->assertSame(true, $tc->toBool(0.00001));
        $this->assertSame(true, $tc->toBool(0.000001));
        $this->assertSame(true, $tc->toBool(0.0000001));
        $this->assertSame(true, $tc->toBool(0.00000001));
        $this->assertSame(true, $tc->toBool(0.000000001));
        $this->assertSame(true, $tc->toBool(0.0000000001));
        $this->assertSame(true, $tc->toBool(0.00000000001));
        $this->assertSame(true, $tc->toBool(0.000000000001));
        $this->assertSame(true, $tc->toBool(0.0000000000001));
        $this->assertSame(true, $tc->toBool(0.00000000000001));
        $this->assertSame(true, $tc->toBool(0.000000000000001));
        $this->assertSame(true, $tc->toBool(0.0000000000000001));
        $this->assertSame(true, $tc->toBool(0.00000000000000001));
        $this->assertSame(true, $tc->toBool(0.000000000000000001));
        $this->assertSame(true, $tc->toBool(0.0000000000000000001));
        $this->assertSame(true, $tc->toBool(0.00000000000000000001));
        $this->assertSame(true, $tc->toBool(0.000000000000000000001));
        $this->assertSame(true, $tc->toBool(0.0000000000000000000001));
        $this->assertSame(true, $tc->toBool(0.00000000000000000000001));
        $this->assertSame(true, $tc->toBool(0.000000000000000000000001));
        $this->assertSame(true, $tc->toBool(0.0000000000000000000000001));
        $this->assertSame(true, $tc->toBool(0.00000000000000000000000001));
        $this->assertSame(true, $tc->toBool(0.000000000000000000000000001));
        $this->assertSame(true, $tc->toBool(0.0000000000000000000000000001));
        $this->assertSame(true, $tc->toBool(0.00000000000000000000000000001));
        $this->assertSame(true, $tc->toBool(0.000000000000000000000000000001));
        $this->assertSame(true, $tc->toBool(0.0000000000000000000000000000001));
        $this->assertSame(true, $tc->toBool(0.00000000000000000000000000000001));
        $this->assertSame(true, $tc->toBool(0.000000000000000000000000000000001));
        $this->assertSame(true, $tc->toBool(0.0000000000000000000000000000000001));
        $this->assertSame(true, $tc->toBool(0.00000000000000000000000000000000001));
        $this->assertSame(true, $tc->toBool(0.000000000000000000000000000000000001));
        $this->assertSame(true, $tc->toBool(0.0000000000000000000000000000000000001));
        $this->assertSame(true, $tc->toBool(0.00000000000000000000000000000000000001));
        $this->assertSame(true, $tc->toBool(0.000000000000000000000000000000000000001));
        $this->assertSame(true, $tc->toBool(0.1e-302));
        $this->assertSame(true, $tc->toBool(0.3));
        $this->assertSame(true, $tc->toBool(0.6));
        $this->assertSame(true, $tc->toBool(0.7));
        $this->assertSame(true, $tc->toBool(0.8));
        $this->assertSame(true, $tc->toBool(1.2));
        $this->assertSame(true, $tc->toBool(1.1));
        $this->assertSame(true, $tc->toBool(1.0));
        $this->assertSame(true, $tc->toBool(2e0));
        $this->assertSame(true, $tc->toBool(3e0));
        $this->assertSame(true, $tc->toBool(INF));
        $this->assertSame(true, $tc->toBool(PHP_INT_MAX));
         ///? NAN can be either positive or negative so, FALSE is best conversion
        //$this->assertSame(false, $tc->toBool(NAN));
        $this->assertSame(false, $tc->toBool(0.0));
        $this->assertSame(false, $tc->toBool(-PHP_INT_MAX));
        $this->assertSame(false, $tc->toBool(-INF));
        $this->assertSame(false, $tc->toBool(-2.0));
        $this->assertSame(false, $tc->toBool(-1.0));
        $this->assertSame(false, $tc->toBool(-0.8));
        $this->assertSame(false, $tc->toBool(-0.7));
        $this->assertSame(false, $tc->toBool(-0.6));
        $this->assertSame(false, $tc->toBool(-0.5));
        $this->assertSame(false, $tc->toBool(-0.4));
        $this->assertSame(false, $tc->toBool(-0.3));
        $this->assertSame(false, $tc->toBool(-0.2));
        $this->assertSame(false, $tc->toBool(-0.1));
        

    }

    //to STRING
    //to STRING
    //to STRING
    public function testBoolToStr()
    {
        $q=[];
        $q[]=[false, 'false'];
        $q[]=[true, 'true'];
        
        $tc=new tc();
        $tc->strict(false);
        foreach ($q as $a) {
            $this->assertSame($a[1], $tc->toStr($a[0]));
        }

    }

    public function testNULLtoStr()
    {
        $q=[];
        $q[]=[NULL, ''];
        
        $tc=new tc();
        $tc->strict(false);
        foreach ($q as $a) {
            $this->assertSame($a[1], $tc->toStr($a[0]));
        }

    }


    //not implemented yet!
    /*
    public function testNULLtoArray()
    {
        $q=[];
        $q[]=[NULL, []];
        
        $tc=new tc();
        $tc->strict(false);
        foreach ($q as $a) {
            $this->assertSame($a[1], $tc->toArray($a[0]));
        }

    }
    */

}
