<?php
/**
 * Created by PhpStorm.
 * User: mkkn
 * Date: 2014/06/08
 * Time: 20:47
 */

namespace Quina\Quina;


class Container {
    /**
     * @var \Quina\Config
     */
    static protected $config = null;

    /**
     * @var \Callable
     */
    static protected $logger = null;

    static public function getConfig($key,$default=null){
        if(static::$config){
            return static::$config->getItem($key,$default);
        }else{
            throw new \Exception("call init method before use Quina");
        }
    }
    static public function setConfig($key,$value){
        if(static::$config){
            return static::$config->setItem($key,$value);
        }else{
            throw new \Exception("call init method before use Quina");
        }
    }

    static public function getConfigRequired($key){
        $salt = sha1(mt_rand(10000000,90000000));
        $value = static::getConfig($key,$salt);
        if($value === $salt){
            throw new \Exception("required config $key not set");
        }
        return $value;
    }

    static public function log(){
        if(static::$logger){
            $callable = static::$logger;
            call_user_func_array($callable,func_get_args());
        }
    }

    public static function __callStatic($name, $arguments)
    {
        try{
            $class = static::getModuleClassName($name);
            if(is_callable($class."::callStatic")){
                return call_user_func_array($class."::callStatic",$arguments);
            }else{
                throw new \Exception("Invalid static method call. ",255,$e);
            }
        }catch (\Exception $e){
            throw $e;
            throw new \Exception("Invalid static method call. ",255,$e);
        }
    }

    /**
     * モジュールのクラス名を返す。
     * @param $className
     * @return string
     * @throws \Exception
     */
    static protected function getModuleClassName($moduleName){
        $className = null;
        $moduleMap = array_merge(static::getCoreConfig("moduleMap"),static::getConfig("moduleMap",[]));
        if(isset($moduleMap[$moduleName])){
            $className = $moduleMap[$moduleName];
        }elseif(class_exists(ucfirst($moduleName))){
            $className = ucfirst($moduleName);
        }else{
            foreach(static::getConfig("moduleNS",[]) as $ns){
                if(class_exists("{$ns}$moduleName")){
                    $className = "{$ns}$moduleName";
                    break;
                }
            }
        }

        if($className && is_subclass_of($className,"\\Quina\\Driver")){
            return $className;
        }else{
            throw new \Exception("module $moduleName not found");
        }
    }


    public $_data = [];
    public $modules = [];

    public function __construct($param){
        $this->_data = $param;
    }

    public function __get($name){
        return $this->get($name);
    }

    public function __set($name,$value){
        return $this->set($name,$value);
    }

    function __call($name, $arguments)
    {
        try{
            if( ($module = $this->getModule($name)) && is_callable($module)){
                return call_user_func_array($module,$arguments);
            }else{
                throw new \Exception("invalid method call exception");
            }
        }catch (\Exception $e){
            throw $e;
        }
    }


    public function get($name){
        $vars = $this->getModule("vars");
        return $vars->$name;
    }


    public function set($name,$value){
        $vars = $this->getModule("vars");
        return $vars->$name = $value;
    }

    public function getModule($name){
        if(empty($this->modules[$name])){
            $this->modules[$name] = $this->registerModule($name);
        }
        return $this->modules[$name];
    }

    public function registerModule($moduleName){
        $moduleClass = static::getModuleClassName($moduleName);
//        if(empty($this->_data[$moduleName])){
//            $params = [];
//        }else{
//            $params = (array)$this->_data[$moduleName];
//        }
        return new $moduleClass($this,$moduleName);
    }

    public function getModuleParam($moduleName){
        if(empty($this->_data[$moduleName])){
            $params = [];
        }else{
            $params = (array)$this->_data[$moduleName];
        }
        return $params;
    }

    public function toArray($includeModules = true){
        $vars = $this->getModule("vars")->toArray();

        if($includeModules){
            $modules = [];
            foreach($this->modules as $moduleName => $moduleObject){
                if(!$moduleObject->arrayFilter()){
                    $modules[$moduleName] = $moduleObject->getParams();
                }
            }
            $vars["_module"] = $modules;
        }
        return $vars;
    }
} 