<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/9/20
 * Time: 15:18
 */

namespace cdcchen\curl;


use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;

/**
 * Class OptionCollection
 * @package cdcchen\curl
 */
class OptionCollection implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * @var array
     */
    private $options = [];

    /**
     * @param int $name
     * @param $value
     */
    public function set(int $name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * @param int $name
     * @return mixed
     */
    public function get(int $name)
    {
        return $this->options[$name] ?? null;
    }

    /**
     * @param int $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->options[$name]);
    }

    /**
     * @param int $name
     */
    public function remove(int $name)
    {
        unset($this->options[$name]);
    }

    /**
     * Remove all cookies
     */
    public function removeAll()
    {
        $this->options = [];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->options;
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->options);
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return count($this->options);
    }
}