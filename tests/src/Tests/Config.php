<?php
/**
 * Created by PhpStorm.
 * User: mkkn
 * Date: 2014/05/25
 * Time: 0:52
 */

namespace Quina\Tests;


class Config implements \Quina\Config{


    static public function init($sitename)
    {
        // TODO: Implement init() method.
        return new static();
    }


    public function getItem($key, $default = null)
    {
        switch($key){
            case "basePath":
                return realpath(__DIR__."/../../data/")."/";
            case "moduleMap":
                return [
                    "apple" => "\\Quina\\Tests\\Apple",
                    "banana" => "\\Quina\\Tests\\Banana",
                    "lemon" => "\\Quina\\Tests\\Lemon",
                ];
            default:
                return $default;
        }
    }


} 