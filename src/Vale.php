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

    public static function get($data, $keys, $default = null)
    {
        return self::instance()->getValue($data, $keys, $default);
    }

    public static function set($data, $keys, $value)
    {
        return self::instance()->setValue($data, $keys, $value);
    }

    public function getValue($data, $keys, $default = null)
    {
        if (!$keys) {
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
            } else if (is_object($current) && method_exists($current, $key) && is_callable([$current, $key])) {
                $current = $current->$key();
            } else if (is_object($current) && method_exists($current, $getter) && is_callable([$current, $getter])) {
                $current = $current->$getter();
            } else if (is_object($current) && method_exists($current, $hasser) && is_callable([$current, $hasser])) {
                $current = $current->$hasser();
            } else if (is_object($current) && method_exists($current, $isser) && is_callable([$current, $isser])) {
                $current = $current->$isser();
            } else {
                return $default;
            }
        }

        return $current;
    }

    public function setValue($data, $keys, $value)
    {
        if (!$keys) {
            return $data;
        }

        if (is_array($data)) {
            $current = &$data;
        } else {
            $current = $data;
        }
        $depth   = 0;
        foreach ($keys as $key) {
            $setter = 'set'.ucfirst($key);
            $getter = 'get'.ucfirst($key);
            $hasser = 'has'.ucfirst($key);
            $isser  = 'is'.ucfirst($key);

            if (is_array($current) && array_key_exists($key, $current)) {
                $current = &$current[$key];
            } else if (is_array($current) && $depth+1 === count($keys)) {
                $current[$key] = null;
                $current = &$current[$key];
            } else if (is_object($current) && isset($current->$key)) {
                $current = &$current->$key;
            } else if (is_object($current) && method_exists($current, $key) && is_callable([$current, $key])
                    && $depth+1 === count($keys)) {
                $current->$key($value);
                $value = null;
            } else if (is_object($current) && method_exists($current, $setter) && is_callable([$current, $setter])
                    && $depth+1 === count($keys)) {
                $current->$setter($value);
                $value = null;
            } else if (is_object($current) && method_exists($current, $key) && is_callable([$current, $key])) {
                $current = $current->$key();
            } else if (is_object($current) && method_exists($current, $getter) && is_callable([$current, $getter])) {
                $current = $current->$getter();
            } else if (is_object($current) && method_exists($current, $hasser) && is_callable([$current, $hasser])) {
                $current = $current->$hasser();
            } else if (is_object($current) && method_exists($current, $isser) && is_callable([$current, $isser])) {
                $current = $current->$isser();
            } else if (is_object($current) && $depth+1 === count($keys)) {
                $current->$key = null;
                $current = &$current->$key;
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
}
