<?php

namespace Cocur\Getter;

/**
 * Hasser
 *
 * @package   Cocur\Getter
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2015 Florian Eckerstorfer
 */
class Hasser
{
    /**
     * @param mixed        $data
     * @param array|string $keys
     *
     * @return bool
     */
    public static function has($data, $keys)
    {
        if (is_scalar($data)) {
            return true;
        }
        if (!is_array($keys)) {
            $keys = [$keys];
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
            } else if (is_object($current) && method_exists($current, $key)) {
                $current = $current->$key();
            } else if (is_object($current) && method_exists($current, $getter) && is_callable([$current, $getter])) {
                $current = $current->$getter();
            } else if (is_object($current) && method_exists($current, $hasser) && is_callable([$current, $hasser])) {
                $current = $current->$hasser();
            } else if (is_object($current) && method_exists($current, $isser) && is_callable([$current, $isser])) {
                $current = $current->$isser();
            } else {
                return false;
            }
        }

        return true;
    }
}
