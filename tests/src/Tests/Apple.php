<?php
/**
 * Created by PhpStorm.
 * User: mkkn
 * Date: 2014/05/10
 * Time: 16:25
 */

namespace Quina\Tests;

/**
 * フルスタックモジュール
 *
 * 静的メソド
 *
 *
 * @package Quina\Tests
 */
class Apple extends \Quina\Driver{

    static function callStatic()
    {
        return "This is Apple";
    }


}