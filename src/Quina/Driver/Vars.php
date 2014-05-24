<?php
/**
 * Created by PhpStorm.
 * User: mkkn
 * Date: 2014/05/10
 * Time: 16:25
 */

namespace Quina\Driver;

class Vars extends \Quina\Driver{

    protected $arrayFilter = true;

    public function __get($key){
        return $this->get($key);
    }

    public function get($key,$default=null){
        if(isset($this->params[$key])){
            return $this->params[$key];
        }else{
            return $default;
        }
    }

    public function __set($key,$value){
        return $this->set($key,$value);
    }

    public function set($key,$value=null){
        if((func_num_args()===1) && is_array($key)){
            $this->params = array_merge($this->params,$key);
        }else{
            $this->params[$key] = $value;
        }
        return true;
    }

    public function toArray(){
        return $this->params;
    }
}