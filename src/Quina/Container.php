<?php
/**
 * Created by PhpStorm.
 * User: mkkn
 * Date: 2014/06/08
 * Time: 20:47
 */

namespace Quina;


abstract class Container implements \ArrayAccess,\IteratorAggregate{

    protected $_data = [];

    public function __construct($param){
        $this->_data = $param;
    }

    public function __get($name){
        return $this->get($name);
    }

    public function __set($name,$value){
        return $this->set($name,$value);
    }

    abstract public function get($name,$default=null);
    abstract public function set($name,$value=null);

    public function offsetExists($offset)
    {
        $rand = sha1(mt_rand(1,10000));
        $value = $this->get($offset,$rand);
        return !(bool)($value === $rand);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator()
    {
        return new \ArrayObject($this->_data);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        return $this->set($offset,$value);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        throw new \Exception("unset not supported");
    }

    public function toArray(){
        return $this->_data;
    }
} 