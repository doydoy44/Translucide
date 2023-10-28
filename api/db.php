<?php
// Connexion a la base de données
use Translucide\db\DataBase;
use Translucide\services\Globals;

include_once(dirname(__FILE__)."/../src/db/DataBase.php");
include_once(dirname(__FILE__)."/../src/services/Globals.php");

$globals = Globals::getInstance();

// Si pas de connexion, on lance l'installateur
if (!($globals->db_server and $globals->db_user and $globals->db)) {
    include_once("install.php");
    return;
}

// Initialisation de la connexion
$dataBase = DataBase::getInstance();
$dataBase->setGlobalConnexion();
