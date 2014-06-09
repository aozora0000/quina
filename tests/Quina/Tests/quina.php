<?php
/**
 * Created by PhpStorm.
 * User: mkkn
 * Date: 2014/05/24
 * Time: 23:32
 */

namespace Quina\Tests;

use Quina\Quina;

class QuinaTest extends \PHPUnit_Framework_TestCase{

    public function test_something(){
        $this->assertEquals("0.1",Quina::version());
    }


    public function configKeyProvider(){
        return [
            ["basePath",realpath(__DIR__."/../../data/")."/"]
        ];
    }

    /**
     * Configから値が適切に取得できるかのテスト
     *
     * @dataProvider configKeyProvider
     */
    public function test_getConfig($key,$value){
        Quina::init("\Quina\Tests\Config");
        $basePath = Quina::getConfig($key);
        $this->assertEquals($value,$basePath);
    }

    public function test_staticModule(){
        Quina::init("\Quina\Tests\Config");
        $expect = Quina::apple();
        $this->assertEquals("This is Apple",$expect);
    }

} 