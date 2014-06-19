<?php
/**
 * Created by PhpStorm.
 * User: mkkn
 * Date: 2014/05/09
 * Time: 20:30
 */

namespace Quina;


abstract class Driver {

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

    public function __get($key){
        return $this->get($key);
    }

    public function get($key,$default=null){
        $params = $this->getParams();
        if(isset($params[$key])){
            return $params[$key];
        }else{
            return $default;
        }
    }


    function __invoke()
    {
        return $this;
    }


    public function getParams(){
        $configValue = Quina::getConfig("m:{$this->calledName}",[]);
        $passedValue =  $this->quina->getModuleParam($this->calledName);
        return array_merge($configValue,$passedValue);
    }
}