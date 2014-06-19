<?php
/**
 * Created by PhpStorm.
 * User: mkkn
 * Date: 2014/05/24
 * Time: 21:11
 */

namespace Quina;

interface Config {

    static public function init($sitename);

//    /**
//     * データの格納
//     */
//    public function set(array $data);
//
//    public function setItem($key,$default);
//
//    /**
//     * データの取り出し
//     */
//    public function get();

    public function getItem($key,$default=null);

    /**
     * 値の格納
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function setItem($key,$value);
}