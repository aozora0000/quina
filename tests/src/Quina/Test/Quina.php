<?php
/**
 * Created by PhpStorm.
 * User: t.goto
 * Date: 2014/07/09
 * Time: 11:59
 */

namespace Quina\Test;


class Quina extends \PHPUnit_Framework_TestCase{


    public function test_sample(){
        $exp = "hoge";
        $act = "hoge";
        $this->assertEquals($exp,$act);
    }

} 