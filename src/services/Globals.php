<?php

namespace Translucide\services;

use Exception;
use mysqli;
use Translucide\db\DataBase;

class Globals
{
    /**
     * The Singleton's instance is stored in a static field. This field is an
     * array, because we'll allow our Singleton to have subclasses. Each item in
     * this array will be an instance of a specific Singleton's subclass. You'll
     * see how this works in a moment.
     */
    private static array $instances = [];

    private ?DataBase $dataBase = null;

    protected function __construct()
    {
        $this->init();
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
    public static function getInstance(): Globals
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new Globals();
        }

        return self::$instances[$cls];
    }

    private function init(): void
    {
        // Si pas de dossier média définit on force la variable
        if (!@$GLOBALS['media_dir']) {
            $GLOBALS['media_dir'] = 'media';
        }
    }

    private function getConnect(): ?mysqli
    {
        if (!$this->dataBase) {
            $this->dataBase = DataBase::getInstance();
        }
        return $this->dataBase->getConnect();
    }

    public function __get($property)
    {
        return match ($property) {
            'connect' => $this->getConnect(),
            default => $GLOBALS[$property],
        };
    }

    public function __set($property, $value)
    {
        $GLOBALS[$property] = $value;
    }
}
