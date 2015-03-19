<?php

namespace Cocur\Vale;

/**
 * Accessor
 *
 * @package   Cocur\Vale
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2015 Florian Eckerstorfer
 */
class Accessor
{
    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var mixed
     */
    protected $current;

    /**
     * @param mixed $data
     */
    public function __construct($data)
    {
        if (is_array($data)) {
            $this->data    = &$data;
            $this->current = &$data;
        } else {
            $this->data    = $data;
            $this->current = $data;
        }
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * Navigates to `$key`.
     *
     * @param string|int $key
     *
     * @return bool `true` if it is possible to navigate to `$key`, `false` if not
     */
    public function to($key)
    {
        $getter = 'get'.ucfirst($key);
        $hasser = 'has'.ucfirst($key);
        $isser  = 'is'.ucfirst($key);

        if (is_array($this->current) && array_key_exists($key, $this->current)) {
            $this->current = &$this->current[$key];
        } else if ($this->isObjectWithMethod($this->current, $key)) {
            $this->current = $this->current->$key();
        } else if ($this->isObjectWithMethod($this->current, $getter)) {
            $this->current = $this->current->$getter();
        } else if ($this->isObjectWithMethod($this->current, 'get')) {
            $this->current = $this->current->get($key);
        } else if ($this->isObjectWithMethod($this->current, $hasser)) {
            $this->current = $this->current->$hasser();
        } else if ($this->isObjectWithMethod($this->current, 'has')) {
            $this->current = $this->current->has($key);
        } else if ($this->isObjectWithMethod($this->current, $isser)) {
            $this->current = $this->current->$isser();
        } else if ($this->isObjectWithMethod($this->current, 'is')) {
            $this->current = $this->current->is($key);
        } else if (is_object($this->current) && isset($this->current->$key)) {
            $this->current = $this->current->$key;
        } else {
            return false;
        }

        return true;
    }

    /**
     * @param string|int $key
     * @param mixed      $value
     *
     * @return bool
     */
    public function set($key, $value)
    {
        $setter = 'set'.ucfirst($key);

        if (is_array($this->current)) {
            $this->current[$key] = $value;
        } else if ($this->isObjectWithMethod($this->current, $key)) {
            $this->current->$key($value);
        } else if ($this->isObjectWithMethod($this->current, $setter)) {
            $this->current->$setter($value);
        } else if ($this->isObjectWithMethod($this->current, 'set')) {
            $this->current->set($key, $value);
        } else if (is_object($this->current)) {
            $this->current->$key = $value;
        } else {
            return false;
        }

        return true;
    }

    /**
     * @param string|int $key
     *
     * @return bool
     */
    public function has($key)
    {
        $getter = 'get'.ucfirst($key);
        $hasser = 'has'.ucfirst($key);
        $isser  = 'is'.ucfirst($key);

        return (is_array($this->current) && array_key_exists($key, $this->current))
            || is_object($this->current) && isset($this->current->$key)
            || ($this->isObjectWithMethod($this->current, $hasser) && $this->current->$hasser())
            || ($this->isObjectWithMethod($this->current, 'has') && $this->current->has($key))
            || ($this->isObjectWithMethod($this->current, $isser) && $this->current->$isser())
            || ($this->isObjectWithMethod($this->current, 'is') && $this->current->is($key))
            || ($this->isObjectWithMethod($this->current, $key) && $this->current->$key())
            || ($this->isObjectWithMethod($this->current, $getter) && $this->current->$getter())
            || ($this->isObjectWithMethod($this->current, 'get') && $this->current->get($key));
    }

    /**
     * @param string|int $key
     *
     * @return bool
     */
    public function remove($key)
    {
        $unsetter = 'unset'.ucfirst($key);
        $remover  = 'remove'.ucfirst($key);

        if (is_array($this->current) && array_key_exists($key, $this->current)) {
            unset($this->current[$key]);
        } else if (is_object($this->current) && isset($this->current->$key)) {
            unset($this->current->$key);
        } else if ($this->isObjectWithMethod($this->current, $unsetter)) {
            $this->current->$unsetter();
        } else if ($this->isObjectWithMethod($this->current, $remover)) {
            $this->current->$remover();
        } else {
            return false;
        }

        return true;
    }

    /**
     * @param mixed      $data
     * @param string|int $key
     *
     * @return bool
     */
    protected function isObjectWithMethod($data, $key)
    {
        return is_object($data) && method_exists($data, $key) && is_callable([$data, $key]);
    }
}
