<?php
/**
 * Created by PhpStorm.
 * User: mkkn
 * Date: 2014/05/09
 * Time: 20:30
 */

namespace Quina;


abstract class Driver {

//    /**
//     * ファクトリ
//     * @param Quina $quina
//     * @param array $params
//     * @return static
//     */
//    static function loadModule(Quina $quina,$calledName){
//        return new static($quina,$calledName);
//    }

    static function callStatic(){}


    /**
     * @var \Quina\Quina
     */
    protected $quina;
    /**
     * @var array
     */
    protected $calledName;

    public function __construct($quina,$calledName){
        $this->quina = $quina;
        $this->calledName = $calledName;
    }

    function __invoke()
    {
        return $this;
    }


    public function getParams(){
        return $this->quina->getModuleParam($this->calledName);
    }
}