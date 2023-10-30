<?php

use Translucide\services\Globals;

include_once(dirname(__FILE__)."/../src/services/Globals.php");

$globals = Globals::getInstance();

/********** FONCTION DU THEME **********/
if ($globals->getFunction() != "") {
    include_once($_SERVER["DOCUMENT_ROOT"] . $globals->getPath() . "theme/" . $globals->getTheme() . ($globals->getTheme() ? "/" : "") . $globals->getFunction());
}
