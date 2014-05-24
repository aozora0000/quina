<?php
/**
 * Created by PhpStorm.
 * User: mkkn
 * Date: 2014/05/24
 * Time: 23:32
 */

namespace Quina\Tests;

use Quina\Quina;

class QuinaDataSingle extends \PHPUnit_Framework_TestCase{


    /**
     * @var \Quina\Quina
     */
    protected $quina;



    protected function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        Quina::init("\\Quina\\Tests\\Config");
        $this->quina = Quina::loadFromYaml("single");
    }

    public function varsProvider(){
        return [
            ["test title","test summary"],
        ];
    }

    /**
     * vars モジュールの調査
     *
     * getModuleでモジュールが取得できる。
     * モジュールから値が取れる
     * オブジェクトから直で値が取れる
     * @dataProvider varsProvider
     */
    public function test_vars($title,$summary){
        $vars = $this->quina->getModule("vars");
        $this->assertTrue($vars instanceof \Quina\Driver\Vars);
        $this->assertEquals($title,$vars->title);
        $this->assertEquals($summary,$vars->summary);
        $this->assertEquals($title,$this->quina->title);
        $this->assertEquals($summary,$this->quina->summary);
    }

    public function restContentProvider(){
        $expect = <<<TXT


test

this is test
TXT;
        return [[$expect]];
    }

    /**
     * @dataProvider restContentProvider
     */
    public function test_restContent($expect){
        $this->assertEquals($expect,$this->quina->getRestContent());
    }

    public function metaProvider(){
        $path = realpath(__DIR__."/../../../data/single.yml");
        return [
            ["single",$path]
        ];
    }

    /**
     * @param $key
     * @dataProvider metaProvider
     */
    public function test_meta($key,$path){
        $this->assertEquals($key,$this->quina->meta["key"]);
        $this->assertEquals("yaml",$this->quina->meta["type"]);
        $this->assertEquals($path,$this->quina->meta["path"]);
        $this->assertEquals(filectime($path),$this->quina->meta["createdAt"]);
        $this->assertEquals(filemtime($path),$this->quina->meta["updatedAt"]);
        $this->assertTrue(is_float($this->quina->meta["start"]));
    }

//    public function urlProvider(){
//        return [
//            ["http://sample.com/quina/single"]
//        ];
//    }
//    /**
//     * @dataProvider urlProvider
//     */
//    public function test_url($url){
//        $this->assertEquals($url,$this->quina->url());
//    }


} 