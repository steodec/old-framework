<?php

namespace Humbrain\Framework\database;

class Hydrator
{
    /**
     * Hydrate an object with data
     * @param string $object
     * @param array $data
     * @return object
     */
    public static function hydrate(string $object, array $data): object
    {
        $instance = new $object();
        foreach ($data as $key => $value) :
            $method = self::getSetter($key);
            if (method_exists($instance, $method)) :
                $instance->$method($value);
            else :
                $property = self::getProperty($key);
                if (property_exists($instance, $property)) :
                    $instance->$property = $value;
                endif;
            endif;
        endforeach;
        return $instance;
    }

    public static function getSetter(string $key): string
    {
        return 'set' . self::getProperty($key);
    }

    public static function getProperty(string $key): string
    {
        return implode('', array_map('ucfirst', explode('_', $key)));
    }
}
