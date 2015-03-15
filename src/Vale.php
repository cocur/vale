<?php

namespace Cocur\Vale;

use InvalidArgumentException;

/**
 * Vale
 *
 * @package   Cocur\Vale
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2015 Florian Eckerstorfer
 * @license   http://opensource.org/licenses/MIT The MIT License
 */
class Vale
{
    /**
     * @var Vale
     */
    private static $instance;

    /**
     * @return Vale
     */
    public static function instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param mixed $data
     * @param array $keys
     * @param mixed $default
     *
     * @return mixed
     */
    public static function get($data, $keys, $default = null)
    {
        return self::instance()->getValue($data, $keys, $default);
    }

    /**
     * @param mixed $data
     * @param array $keys
     * @param mixed $value
     *
     * @return mixed
     */
    public static function set($data, $keys, $value)
    {
        return self::instance()->setValue($data, $keys, $value);
    }

    /**
     * @param mixed      $data
     * @param array      $keys
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function getValue($data, $keys, $default = null)
    {
        if ($keys === null || $keys === '' || count($keys) === 0) {
            return $data;
        }

        $current = $data;
        foreach ($keys as $key) {
            $getter = 'get'.ucfirst($key);
            $hasser = 'has'.ucfirst($key);
            $isser  = 'is'.ucfirst($key);

            if (is_array($current) && array_key_exists($key, $current)) {
                $current = $current[$key];
            } else if (is_object($current) && isset($current->$key)) {
                $current = $current->$key;
            } else if ($this->isObjectWithMethod($current, $key)) {
                $current = $current->$key();
            } else if ($this->isObjectWithMethod($current, $getter)) {
                $current = $current->$getter();
            } else if ($this->isObjectWithMethod($current, $hasser)) {
                $current = $current->$hasser();
            } else if ($this->isObjectWithMethod($current, $isser)) {
                $current = $current->$isser();
            } else {
                return $default;
            }
        }

        return $current;
    }

    /**
     * @param mixed $data
     * @param array $keys
     * @param mixed $value
     *
     * @return mixed
     */
    public function setValue($data, $keys, $value)
    {
        if ($keys === null || $keys === '' || count($keys) === 0) {
            return $data;
        }

        if (is_array($data)) {
            $current = &$data;
        } else {
            $current = $data;
        }
        $depth = 0;
        $keyCount = count($keys);
        foreach ($keys as $key) {
            $setter = 'set'.ucfirst($key);
            $getter = 'get'.ucfirst($key);
            $hasser = 'has'.ucfirst($key);
            $isser  = 'is'.ucfirst($key);

            if (is_array($current) && array_key_exists($key, $current)) {
                $current = &$current[$key];
            } else if (is_array($current) && $depth+1 === $keyCount) {
                $current[$key] = null;
                $current       = &$current[$key];
            } else if (is_object($current) && isset($current->$key)) {
                $current = &$current->$key;
            } else if ($this->isObjectWithMethod($current, $key) && $depth+1 === $keyCount) {
                $current->$key($value);
                $value = null;
            } else if ($this->isObjectWithMethod($current, $setter) && $depth+1 === $keyCount) {
                $current->$setter($value);
                $value = null;
            } else if ($this->isObjectWithMethod($current, $key)) {
                $current = $current->$key();
            } else if ($this->isObjectWithMethod($current, $getter)) {
                $current = $current->$getter();
            } else if ($this->isObjectWithMethod($current, $hasser)) {
                $current = $current->$hasser();
            } else if ($this->isObjectWithMethod($current, $isser)) {
                $current = $current->$isser();
            } else if (is_object($current) && $depth+1 === $keyCount) {
                $current->$key = null;
                $current       = &$current->$key;
            } else {
                throw new InvalidArgumentException(sprintf('Did not find path %s in structure %s', json_encode($keys), json_encode($data)));
            }
            ++$depth;
        }

        if ($value !== null) {
            $current = $value;
        }

        return $data;
    }

    /**
     * @param array|object $data
     * @param string       $key
     *
     * @return bool
     */
    protected function isObjectWithMethod($data, $key)
    {
        return is_object($data) && method_exists($data, $key) && is_callable([$data, $key]);
    }
}
