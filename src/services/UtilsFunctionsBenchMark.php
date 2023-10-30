<?php

namespace Translucide\services;

use Exception;

class UtilsFunctionsBenchMark
{
    /**
     * The Singleton's instance is stored in a static field. This field is an
     * array, because we'll allow our Singleton to have subclasses. Each item in
     * this array will be an instance of a specific Singleton's subclass. You'll
     * see how this works in a moment.
     */
    private static array $instances = [];

    protected function __construct()
    {
    }

    protected function __clone()
    {
    }

    /**
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize a singleton.");
    }

    public static function getInstance(): UtilsFunctionsBenchMark
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new UtilsFunctionsBenchMark();
        }

        return self::$instances[$cls];
    }

    /********** BENCHMARK **********/
    // Mesure le temps de traitement Php
    public function benchmark()
    {
        return (microtime(true) - $GLOBALS['microtime']);
    }
}
