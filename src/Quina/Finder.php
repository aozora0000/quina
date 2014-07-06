<?php
namespace Quina;
/**
 * Created by PhpStorm.
 * User: mkkn
 * Date: 2014/05/20
 * Time: 12:32
 */

interface Finder{

    /**
     * @param $key
     * @return \Quina\Quina
     */
    public function findOne($key);

    /**
     * @param $key
     * @return \Quina\Collection
     */
    public function findAll($conj);
}
