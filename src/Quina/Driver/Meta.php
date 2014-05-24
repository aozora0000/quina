<?php
/**
 * Created by PhpStorm.
 * User: mkkn
 * Date: 2014/05/10
 * Time: 16:25
 */

namespace Quina\Driver;

class Meta extends \Quina\Driver{

    static function loadModule(\Quina\Quina $quina,array $params){
        $meta = [];
        foreach($params as $key=>$value){
            $meta[$key] = $value;
        }
        $quina->meta = $meta;
        return new static($quina,$params);
    }


}