<?php

use Translucide\services\UtilsFunctionsLanguage;

include_once(dirname(__FILE__)."/../src/services/UtilsFunctionsLanguage.php");

$languageFc = UtilsFunctionsLanguage::getInstance();

//$lang = $language->get_lang(); // Sélectionne la langue
$languageFc->load_translation('api'); // Chargement des traductions du système

switch(@$_GET['mode']) {
    // Retour Ajax
    case "start": // CRÉATION / Mise à jour des données de configuration
        include_once 'Install/installStartMode.php';
        break;
    // Affichage de la page
    default: // FORMULAIRE de configuration
        include_once 'Install/installDefaultMode.php';
        break;
}
