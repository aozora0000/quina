<?php
namespace Quina;

    /**
 * Created by PhpStorm.
 * User: mkkn
 * Date: 2014/05/09
 * Time: 20:32
 */

class Quina extends Quina\Container{

    static public function init($configClass,$sitename=null){
        if((is_object($configClass)) && ($configClass instanceof Config))
        {
            static::$config = $configClass;
        }elseif((is_string($configClass))&& (is_subclass_of($configClass,"\\Quina\\Config")))
        {
            static::$config = $configClass::init($sitename);
        }else{
            throw new Exception("invalid argument");
        }
    }

    static public function setLogger($logger){
        if(is_callable($logger)){
            static::$logger = $logger;
        }else{
            throw new \Exception("uncallable logger injected");
        }
    }

    static public function version(){
        return static::getCoreConfig("version");
    }

    static public function getCoreConfig($key=null){
        $data = include __DIR__."/../../config/quina.php";
        return $data[$key];
    }

    static public function addHook($hookKey,$callable){
        $hooks = static::getHooks($hookKey);
        $hooks = array_merge($hooks,[$callable]);
        static::setConfig("h:$hookKey",$hooks);
    }

    static public function getHooks($hookKey){
        return static::getConfig("h:$hookKey",[]);
    }

    static public function parseParam($paramString){
        if(is_array($paramString)){
            return $paramString;
        }
        $rtn = [];
        $params = array_filter(explode(" ",$paramString));
        foreach($params as $p){
            if(($p = explode("=",$p)) && count($p) === 2){
                $rtn[$p[0]] = $p[1];
            }else{
                $rtn[]= $p[0];
            }
        }
        return $rtn;
    }

    public function __construct($param)
    {
        parent::__construct($param);
        $preload = (array)static::getConfig("preload",[]);
        foreach($preload as $module){
            $this->getModule($module);
        }
    }


}

class Exception extends \Exception{}