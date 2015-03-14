<?php

namespace Cocur\Getter;

/**
 * Getter
 *
 * @package   Cocur\Getter
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2015 Florian Eckerstorfer
 */
class Getter
{
    /**
     * Returns the value from $data defined by $keys.
     *
     * If $data is an object or an array (or a mixture with an arbitrary depth) the value defined by $keys will be
     * returned. For example,
     *   Getter::get($data, ['children', 0, 'firstName']);
     * would be the same as:
     *   $data->children[0]->getFirstName()
     * In addition get() performs checks if the given elements exists at each level.
     *
     * At each level a value is tried to be retrieved in the following order:
     * 1. If $data is an array, return element with the key
     * 2. If $data is an object
     *      a. Try property key
     *      b. Try method key()
     *      c. Try method getKey()
     *      d. Try method hasKey()
     *      e. Try method isKey()
     *
     * If $data is a scalar value, this value is returned immediately.
     *
     * @param mixed      $data
     * @param array      $keys
     * @param mixed|null $default
     *
     * @return mixed
     */
    public static function get($data, $keys, $default = null)
    {
        if (is_scalar($data)) {
            return $data;
        }

        $current = $data;
        foreach ($keys as $key) {
            $getter = 'get'.ucfirst($key);
            $hasser = 'has'.ucfirst($key);
            $isser  = 'is'.ucfirst($key);

            if (is_array($current) && array_key_exists($key, $current)) {
                $current = $current[$key];
            } else if (is_object($current) && property_exists($current, $key)) {
                $current = $current->$key;
            } else if (is_object($current) && method_exists($current, $key)) {
                $current = $current->$key();
            } else if (is_object($current) && method_exists($current, $getter)) {
                $current = $current->$getter();
            } else if (is_object($current) && method_exists($current, $hasser)) {
                $current = $current->$hasser();
            } else if (is_object($current) && method_exists($current, $isser)) {
                $current = $current->$isser();
            } else {
                return $default;
            }
        }

        return $current;
    }
}
