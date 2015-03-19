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

            return true;
        } else if ($this->isObjectWithMethod($this->current, $key)) {
            $this->current = $this->current->$key();

            return true;
        } else if ($this->isObjectWithMethod($this->current, $getter)) {
            $this->current = $this->current->$getter();

            return true;
        } else if ($this->isObjectWithMethod($this->current, $hasser)) {
            $this->current = $this->current->$hasser();

            return true;
        } else if ($this->isObjectWithMethod($this->current, $isser)) {
            $this->current = $this->current->$isser();

            return true;
        } else if (is_object($this->current) && isset($this->current->$key)) {
            $this->current = $this->current->$key;

            return true;
        }

        return false;
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

            return true;
        } else if ($this->isObjectWithMethod($this->current, $key)) {
            $this->current->$key($value);

            return true;
        } else if ($this->isObjectWithMethod($this->current, $setter)) {
            $this->current->$setter($value);

            return true;
        } else if (is_object($this->current)) {
            $this->current->$key = $value;

            return true;
        }

        return false;
    }

    /**
     * @param string|int $key
     *
     * @return bool
     */
    public function has($key)
    {
        $setter = 'set'.ucfirst($key);
        $getter = 'get'.ucfirst($key);
        $hasser = 'has'.ucfirst($key);
        $isser  = 'is'.ucfirst($key);

        return (is_array($this->current) && array_key_exists($key, $this->current))
            || is_object($this->current) && isset($this->current->$key)
            || $this->isObjectWithMethod($this->current, $key)
            || $this->isObjectWithMethod($this->current, $setter)
            || $this->isObjectWithMethod($this->current, $getter)
            || $this->isObjectWithMethod($this->current, $hasser)
            || $this->isObjectWithMethod($this->current, $isser);
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

            return true;
        } else if (is_object($this->current) && isset($this->current->$key)) {
            unset($this->current->$key);

            return true;
        } else if ($this->isObjectWithMethod($this->current, $unsetter)) {
            $this->current->$unsetter();

            return true;
        } else if ($this->isObjectWithMethod($this->current, $remover)) {
            $this->current->$remover();

            return true;
        }

        return false;
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
