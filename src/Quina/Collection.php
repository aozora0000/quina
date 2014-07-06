<?php
/**
 * Created by PhpStorm.
 * User: t.goto
 * Date: 2014/06/23
 * Time: 13:17
 */
namespace Quina;

class Collection extends Container{

    public function get($name, $default = null)
    {
        if(isset($this->_data[$name])){
            return $this->_data[$name];
        }else{
            return null;
        }
    }

    public function set($name, $value = null)
    {
        if(is_array($name)){
            $this->_data = $name;
        }else{
            $this->_data[$name] = $value;
        }
    }

    public function each(\Closure $each){
        foreach($this as $list){
            $each($list);
        }
    }

    public function uasort(\Closure $callable){
        uasort($this,$callable);
    }
}