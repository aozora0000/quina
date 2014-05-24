<?php
/**
 * Created by PhpStorm.
 * User: mkkn
 * Date: 2014/05/09
 * Time: 20:30
 */

namespace Quina;


abstract class Driver {

    /**
     * ファクトリ
     * @param Quina $quina
     * @param array $params
     * @return static
     */
    static function loadModule(Quina $quina,array $params){
        return new static($quina,$params);
    }

    static function callStatic(){}


    /**
     * @var \Quina\Quina
     */
    protected $quina;

    /**
     * @var array
     */
    protected $params;

    protected $arrayFilter = false;

    public function __construct($quina,$params){
        $this->quina = $quina;
        $this->params = $params;
    }

    public function getParams(){
        return $this->params;
    }

    public function arrayFilter(){
        return (bool) $this->arrayFilter;
    }


}