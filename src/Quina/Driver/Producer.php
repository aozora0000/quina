<?php
/**
 * Created by PhpStorm.
 * User: mkkn
 * Date: 2014/05/10
 * Time: 16:25
 */

namespace Quina\Driver;

/**
 * Class Producer
 * 生成情報の格納
 *
 * key 探索キー
 * category カテゴリ
 * created_at 生成日時 MysqlTS
 * updated_at 更新日時 MysqlTS
 *
 *
 * @package Quina\Driver
 */
class Producer extends \Quina\Driver{

    protected $data;
//    /**
//     * @param $key
//     */
//    public function set($key,$category,$created_at,$updated_at){
//        $data = array_combine(["key","category","created_at","updated_at"],func_get_args());
//
//        $this->data = $data;
//    }
//
//    public function get($key,$default=null){
//        if(empty($this->data["$key"])){
//            return $default;
//        }else{
//            return $this->data[$key];
//        }
//    }

    protected function _getDate($key,$fomat){
        $ts = strtotime($this->get($key));
        return date($fomat,$ts);
    }

    protected function created_at($fomat){
        return $this->_getDate("created_at",$fomat);
    }

    protected function updated_at($fomat){
        return $this->_getDate("updated_at",$fomat);
    }


}