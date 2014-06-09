<?php
/**
 * Created by PhpStorm.
 * User: mkkn
 * Date: 2014/06/08
 * Time: 20:59
 */

namespace Quina;


class Parser {
    public static function __callStatic($name, $arguments)
    {
        if(class_exists("Quina\\Parser\\$name")){


        }
        // TODO: Implement __callStatic() method.
    }


} 