<?php
/**
 * Created by PhpStorm.
 * User: mkkn
 * Date: 2014/05/09
 * Time: 20:30
 */

namespace Quina;


abstract class Driver {

    static protected $name;

    static public function getConfig($key=null,$default = null){
        $name = static::$name;
        $configValue = Quina::getConfig("m:{$name}",[]);
        if(func_num_args()){
            return \Arr::get($configValue,$key,$default);
        }else{
            return $configValue;
        }
    }


//    static public function callStatic(){}
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

    public function getParams($key=null,$default=null){
        $configValue = static::getConfig();
        $passedValue =  $this->quina->getModuleParam($this->calledName);
        $params = array_merge($configValue,$passedValue);
        if(func_num_args()){
            return \Arr::get($params,$key,$default);
        }else{
            return $params;
        }
    }

}