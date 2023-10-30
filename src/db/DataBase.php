<?php

namespace Translucide\db;

use \mysqli;
use Translucide\services\Globals;

class DataBase
{
    /**
     * The Singleton's instance is stored in a static field. This field is an
     * array, because we'll allow our Singleton to have subclasses. Each item in
     * this array will be an instance of a specific Singleton's subclass. You'll
     * see how this works in a moment.
     */
    private static array $instances = [];

    private ?mysqli $connect;
    private ?Globals $globals = null;
    
    protected function __construct()
    {
    }

    protected function __clone()
    {
    }
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }
    public static function getInstance(): DataBase
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new DataBase();
        }

        return self::$instances[$cls];
    }

    public function getGlobals(): Globals
    {
        if (!$this->globals) {
            $this->globals = Globals::getInstance();
        }
        return $this->globals;
    }

    // Pour un bon encodage dans les sorties de la page
    private function init(): void
    {
        if ($this->getGlobals()->getDbCharset()) {
            $this->connect->query("SET NAMES '" . $this->getGlobals()->getDbCharset() . "'");
        }
    }

    public function setGlobalConnexion(): void
    {
        $this->connect = new mysqli($this->getGlobals()->getDbServer(), $this->getGlobals()->getDbUser(), $this->getGlobals()->getDbPwd(), $this->getGlobals()->getDb());

        // Si pas de connexion, on affiche pour Google une indisponibilité
        if ($this->connect->connect_errno) {
            header($_SERVER['SERVER_PROTOCOL'] . " 503 Service Unavailable");
            exit($this->connect->connect_error);
        }

        $this->init();

    }

    public function setPostConnextion(): void
    {
        $this->connect = @new mysqli(addslashes($_POST['db_server']), addslashes($_POST['db_user']), addslashes($_POST['db_pwd']), addslashes($_POST['db']));
    }

    public function getConnect(): ?mysqli
    {
        return $this->connect;
    }
}
