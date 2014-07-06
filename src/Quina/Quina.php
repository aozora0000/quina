<?php
namespace Quina;

    /**
 * Created by PhpStorm.
 * User: mkkn
 * Date: 2014/05/09
 * Time: 20:32
 */

abstract class Quina extends Container{


    /**
     * @var array
     */
    static protected $moduleParam = [];
    /**
     * @var \Quina\Loader
     */
    static protected $moduleLoader = [];

    static protected $instances = [];

    public static function __callStatic($name, $arguments)
    {
        return static::getInstance()->$name();
    }

    static public function getInstance($key = null){
        if($key){
            $i = static::$instances[$key];
        }else{
            $i = reset(static::$instances);
        }
        if($i instanceof static){
            return $i;
        }else{
            \Profiler::console(static::$instances);
            throw new \Exception("no valid instance returned");
        }
    }

    static public function init(array $moduleParam,array $moduleMap){
        static::$moduleParam = $moduleParam;
        static::$moduleLoader = new Loader($moduleMap);
        static::$instances[] = new static();
    }

    public $modules = [];

    public function __construct($param=[])
    {
        parent::__construct(array_merge(static::$moduleParam,$param));
    }

    function __call($name, $arguments)
    {
        try{
            if( ($module = $this->getModule($name)) && is_callable($module)){
                return call_user_func_array($module,$arguments);
            }else{
                \Profiler::console("test");
                \Profiler::console($module);
                throw new \Exception("invalid method call exception");
            }
        }catch (\Exception $e){
            throw $e;
        }
    }


    public function registerModule($moduleName){
        return static::$moduleLoader->load($moduleName,$this);
    }

    public function getModule($name){
        if(empty($this->modules[$name])){
            $this->modules[$name] = $this->registerModule($name);
        }
        return $this->modules[$name];
    }

    public function getModuleParam($moduleName){
        if(empty($this->_data[$moduleName])){
            $params = [];
        }else{
            $params = (array)$this->_data[$moduleName];
        }
        return $params;
    }


}

//class Exception extends \Exception{}