<?php
/**
 * Created by PhpStorm.
 * User: mkkn
 * Date: 2014/06/26
 * Time: 12:33
 */

namespace Quina;


class Loader {

    protected $moduleMap = [];
    protected $moduleNS = [];

    function __construct(array $moduleMap)
    {
        \Profiler::console($moduleMap);
        $this->moduleMap = $moduleMap;
        $this->moduleNS = [];
    }


    /**
     * モジュールのクラス名を返す。
     * @param $className
     * @return string
     * @throws \Exception
     */
    protected function getModuleClassName($moduleName){
        $className = null;
        if(isset($this->moduleMap[$moduleName])){
            $className = $this->moduleMap[$moduleName];
        }elseif(class_exists(ucfirst($moduleName))){
            $className = ucfirst($moduleName);
        }else{
            foreach($this->moduleNS as $ns){
                if(class_exists("{$ns}$moduleName")){
                    $className = "{$ns}$moduleName";
                    break;
                }
            }
        }
        if($className && is_subclass_of($className,"\\Quina\\Module")){
            return $className;
        }else{
            throw new \Exception("module $moduleName not found");
        }
    }

    public function load($moduleName,\Quina\Quina $quina){
        $moduleClass = static::getModuleClassName($moduleName);
        return new $moduleClass($quina,$moduleName);
    }

} 