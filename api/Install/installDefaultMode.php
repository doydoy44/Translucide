<?php
use Translucide\services\UtilsFunctionsConnexion;
use Translucide\services\UtilsFunctionsLanguage;

include_once(dirname(__FILE__)."/../../src/services/UtilsFunctionsConnexion.php");
include_once(dirname(__FILE__)."/../../src/services/UtilsFunctionsLanguage.php");
//include_once("config.init.php"); // Les variables par défaut
//include_once("function.php"); // Fonction

$languageFn = UtilsFunctionsLanguage::getInstance();
$connexionFn = UtilsFunctionsConnexion::getInstance();
//
//$lang = $language->get_lang(); // Sélectionne  la langue
//$language->load_translation('api'); // Chargement des traductions du système

// FORMULAIRE de configuration

//@todo: ajouter la possibilité de récup notre propre id fb, google, yah, ms (mode silencieux de login tiers)
//@todo: voir pour utiliser ce fichier également en ajax pour édit la config par la suite
//@todo: Ajouter un lien pour test les connexions tierses
//@todo: donner les URL à rentrer dans les applications tierses
//@todo: ajouter un droit d'édition light de la config (nom du site, code analytics, mail contact...) ou visible par tous les éditeurs de contenu ?
//@todo: Vérif le cas ou pas de fichier conf existe
//@todo: Vérif le cas ou fichier conf exist
// highlight_string(print_r($_SERVER, true));

// Pour éviter les problèmes de cache qui appèlerais un fichier inexistant
// cas du favicon.ico qui crée une 404 qui charge donc l'install et crée un nouveau nonce
// @todo: SUPP car crée un bug sur certaine config apache => http2 ?
/*if(isset($_SERVER['REDIRECT_URL'])) {
    header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
    exit("<h1>404 error : page not found</h1>");
}*/

// Verifie que l'on exécute bien depuis index.php
// Evite d'avoir d'autre chargement de la config (ex : favicon.ico inexistant qui charge la conf une 2ème fois))
// Si url de redirection existe, elle doit etre = au nom du script executé qui appel l'install = index.php
if (isset($_SERVER['REDIRECT_URL']) and $_SERVER['REDIRECT_URL'] != $_SERVER['SCRIPT_NAME']) {
    exit;
}

// Si on appelle directement le fichier depuis le dossier api/ => exit
if (strstr($_SERVER['SCRIPT_NAME'], 'install.php')) {
    exit;
}

// Charge la config maison si elle existe depuis les 2 chemins possibles
@include_once("config.php"); // Si chargement en include
@include_once("../config.php"); // Si chargement depuis le dossier api dans l'url

// Traduction de la page d'installation
$add_translation = [
    "Site Installation" => ["fr" => "Installation du site"],

    "Address database" => ["fr" => "Adresse de la base de données"],
    "Name of the data base" => ["fr" => "Nom de la base de données"],
    "MySQL Username" => ["fr" => "Nom d'utilisateur MySQL"],
    "MySQL User Password" => ["fr" => "Mot de passe de l'utilisateur MySQL"],
    "Table Prefix" => ["fr" => "Préfixe de table"],

    "Name of the site" => ["fr" => "Nom du site"],
    "Site theme" => ["fr" => "Thème du site"],

    "Site Location" => ["fr" => "Emplacement du site"],

    "Administrator email" => ["fr" => "Email administrateur"],
    "Administrator password" => ["fr" => "Mot de passe administrateur"],

    "Option" => ["fr" => "Option"],

    "Google analytics code" => ["fr" => "Code google analytics"],

    "System login third" => ["fr" => "Système de login tièrce"],

    "Id of the app facebook" => ["fr" => "Id de l'app facebook"],
    "Secret key of the app facebook" => ["fr" => "Clé secrete de l'app facebook"],

    "Id of the app google" => ["fr" => "Id de l'app google"],
    "Secret Key to google app" => ["fr" => "Clé secrete de l'app google"],

    "Id of the app yahoo" => ["fr" => "Id de l'app yahoo"],
    "Secret key to the app yahoo" => ["fr" => "Clé secrete de l'app yahoo"],

    "Id of the app microsoft" => ["fr" => "Id de l'app microsoft"],
    "Secret key of microsoft app" => ["fr" => "Clé secrete de l'app microsoft"],

    "Start installation" => ["fr" => "Lancer l'installation"],

    "Configuration already created" => ["fr" => "Configuration déjà crée"]
];

$languageFn->add_translation($add_translation);


// On vérifie si la configuration est déjà créée / normalement plus utile, car on bloque plus haut le chargement de install.php directement dans l'url
if ($GLOBALS['db_server'] or $GLOBALS['db_user'] or $GLOBALS['db']) {
    exit('<h1>' . $languageFn->__('Configuration already created') . '</h1>');
}


// Chemin complet du site
$scheme_domain_path = "";
if ($GLOBALS['scheme'] and $GLOBALS['domain'] and $GLOBALS['path']) {
    $scheme_domain_path = $GLOBALS['home'];
} else {
    if (isset($_SERVER['REQUEST_SCHEME'])) {
        $scheme_domain_path .= $_SERVER['REQUEST_SCHEME'] . "://";
    } else {
        $scheme_domain_path .= "http://";
    }

    $scheme_domain_path .= $_SERVER['SERVER_NAME'];

    //@todo vérif car crée un bug sur les install en sous domaine
    //$scheme_domain_path .= str_replace(basename($_SERVER['REQUEST_URI']) , "", $_SERVER['REQUEST_URI']);
    $scheme_domain_path .= $_SERVER['REQUEST_URI'];
}

// Nom du site
if (isset($GLOBALS['sitename'])) {
    $sitename = mb_convert_encoding($GLOBALS['sitename'], 'UTF-8', mb_list_encodings());
} else {
    $parse_url = parse_url($scheme_domain_path);


    if ($parse_url['path'] != '/') { // Si dossier
        $sitename = ucfirst(trim($parse_url['path'], '/'));
    } else { // Si juste domaine
        $domains = explode('.', $_SERVER['SERVER_NAME']);
        $sitename = ucfirst($domains[count($domains) - 2]);
    }
}


header('Content-type: text/html; charset=UTF-8');

?><!DOCTYPE html>
<html lang="<?= $languageFn->get_lang(); ?>">
<head>
    <meta charset="utf-8">
    <title><?php $languageFn->_e("Site Installation"); ?></title>
    <meta name="robots" content="noindex, nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="about:blank">
    <!-- Pour eviter de charger un ico 404 qui recharge la config -->
    <link rel="stylesheet" href="<?= $GLOBALS['jquery_ui_css']; ?>">
    <link rel="stylesheet" href="theme/default/style.min.css?">
    <style>
        @font-face {
            font-family: 'FontAwesome';
            src: url('api/icons/icons.eot?<?=$GLOBALS['cache']?>');
            src: url('api/icons/icons.eot?<?=$GLOBALS['cache']?>#iefix') format('embedded-opentype'),
            url('api/icons/icons.woff2?<?=$GLOBALS['cache']?>') format('woff2'),
            url('api/icons/icons.woff?<?=$GLOBALS['cache']?>') format('woff'),
            url('api/icons/icons.ttf?<?=$GLOBALS['cache']?>') format('truetype'),
            url('api/icons/icons.svg?<?=$GLOBALS['cache']?>#icons') format('svg');
            font-weight: normal;
            font-style: normal;
        }
        body {
            background-color: #75898c;
        }
        .layer {
            box-shadow: 0 0 60px rgba(53, 116, 127, 0.3) inset, 0 0 5px rgba(0, 0, 0, 0.3);
        }
        .layer:after {
            display: none;
        }
        label {
            text-align: right;
            padding-right: 1rem;
            cursor: default;
        }
        @media screen and (max-width: 640px) {
            .w80 {
                width: 95%;
            }

            .w10, .w20, .w30, .w50, .w60 {
                width: 90%;
            }

            label {
                display: block;
                text-align: left;
            }
        }
        .bt.fixed.top {
            display: none !important;
        }
    </style>

    <script src="api/jquery.min.js"></script>
    <script src="<?= $GLOBALS['jquery_ui']; ?>"></script>
    <script src="api/lucide.init.js"></script>
    <script>
      submittable = function () {
        // Icône de chargement
        $("#setup button i").removeClass("fa-spin");

        // Active le submit
        $("#setup button").attr("disabled", false);
      }

      $(function () {
        // Setup
        $("#setup").submit(function (event) {
          event.preventDefault();

          // Icône de chargement
          $("#setup button i").addClass("fa-spin");

          // Désactive le submit
          $("#setup button").attr("disabled", true);

          // Variable
          var data = {};
          $("input, select", $("#setup")).each(function (index) {
            data[$(this).attr("id")] = this.value;
          })

          $.ajax(
            {
              type: "POST",
              url: "api/install.php?mode=start",
              data: data,
              success: function (html) {
                $("body").append(html);
              }
            });
        });
      });
    </script>
    <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body>
    <div class="w80 center">
        <h1 class="tc black"><?php $languageFn->_e("Site Installation"); ?></h1>
        <div class="layer mod pam mbm">
            <form id="setup">
                <input type="hidden" id="nonce" name="nonce" value="<?= $connexionFn->nonce("nonce"); ?>" class="w100">
                <ul class="unstyled">
                    <li>
                        <label class="w30">
                            <?php $languageFn->_e("Address database"); ?>
                        </label>
                        <input type="text" id="db_server"
                               value="<?= $GLOBALS['db_server']; ?>"
                               placeholder="localhost" required
                               class="w60 vatt">
                    </li>
                    <li>
                        <label class="w30">
                            <?php $languageFn->_e("Name of the data base"); ?>
                        </label>
                        <input type="text" id="db"
                            value="<?= $GLOBALS['db']; ?>"
                            required class="w60 vatt">
                    </li>
                    <li>
                        <label class="w30">
                            <?php $languageFn->_e("MySQL Username"); ?>
                        </label>
                        <input type="text" id="db_user"
                             value="<?= $GLOBALS['db_user']; ?>"
                             placeholder="root" required
                             class="w60 vatt">
                    </li>
                    <li>
                        <label class="w30">
                            <?php $languageFn->_e("MySQL User Password"); ?>
                        </label>
                        <input type="text" id="db_pwd"
                              value="<?= $GLOBALS['db_pwd']; ?>"
                              class="w60 vatt">
                        <a href="javascript:void(0);"
                           onclick="if($('#db_pwd').attr('type') == 'password') $('#db_pwd').attr('type','text'); else $('#db_pwd').attr('type','password');"
                           tabindex="-1">
                            <i class="fa fa-fw fa-eye mts vam"></i>
                        </a>
                    </li>
                    <li>
                        <label class="w30">
                            <?php $languageFn->_e("Table Prefix"); ?>
                        </label>
                        <input type="text" id="db_prefix"
                               value="<?= $GLOBALS['db_prefix']; ?>"
                               placeholder="tl_" class="w10 vatt">
                    </li>
                    <li class="mtm">
                        <label class="w30 bold">
                            <?php $languageFn->_e("Name of the site"); ?>
                        </label>
                        <input type="text"
                                id="sitename"
                                value="<?= $sitename; ?>"
                                class="w60 vatt">
                    </li>
                    <li>
                        <label class="w30">
                            <?php $languageFn->_e("Site theme"); ?>
                        </label>
                        <select id="theme" class="vatt">
                            <?php
                            // Un thème dans la racine
                            if (file_exists("theme/header.php")) {
                                echo "<option value=\"\"" . ($GLOBALS['theme'] == "" ? " selected" : "") . ">/</option>";
                            }

                            // Des dossiers de thème
                            $scandir = array_diff(scandir("theme/"), ['..', '.', 'tpl']);

                            foreach ($scandir as $cle => $file) {
                                if (is_dir("theme/" . $file)) {
                                    echo "<option value=\"" . $file . "\"" . ($GLOBALS['theme'] == $file ? " selected" : "") . ">" . $file . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </li>
                    <li>
                        <label class="w30"><?php $languageFn->_e("Site Location"); ?></label>
                        <input type="text" id="scheme_domain_path"
                                value="<?= $scheme_domain_path; ?>"
                                required class="w60 vatt">
                    </li>

                    <li class="mtm">
                        <label class="w30 bold">
                            <i class="fa fa-fw fa-globe"></i> <?php $languageFn->_e("Administrator email"); ?>
                        </label>
                        <input type="email" id="email_contact" value="<?= $GLOBALS['email_contact']; ?>" required
                                maxlength="100" class="w60 vatt" autocomplete="username">
                        <!-- autocomplete="username" -->
                    </li>
                    <li>
                        <label class="w30 bold">
                            <i class="fa fa-fw fa-key"></i> <?php $languageFn->_e("Administrator password"); ?>
                        </label>
                        <input type="password" id="password" required class="w60 vatt">
                        <!--  autocomplete="current-password" -->

                        <a href="javascript:$('#setup #password').make_password();"
                           title="<?php $languageFn->_e("Suggest a password"); ?>" class="tdn">
                            <i class="fa fa-fw fa-arrows-cw mts vam"></i>
                        </a>
                        <a href="javascript:void(0);"
                           onclick="if($('#password').attr('type') == 'password') $('#password').attr('type','text'); else $('#password').attr('type','password');"
                           tabindex="-1">
                            <i class="fa fa-fw fa-eye mts vam"></i>
                        </a>
                        <!-- <a href="javascript:void(0);" onclick="$('#setup #password').make_password();" title="<?php $languageFn->_e("Suggest a password"); ?>"><i class="fa fa-fw fa-arrows-cw mts vam"></i></a> -->
                    </li>
                    <!--
                                <li class="mtl bold"><?php $languageFn->_e("Option"); ?></li>

                                <li><label class="w30"><i class="fa fa-fw fa-line-chart"></i> <?php $languageFn->_e("Google analytics code"); ?></label> <input type="text" id="google_analytics" placeholder="UA-00000000-1" class="w20 vatt"></li>
                                 -->
                </ul>
                <button class="fr mam bold">
                    <?php $languageFn->_e("Start installation"); ?>
                    <i class="fa fa-fw fa-cog"></i>
                </button>
            </form>
        </div>
    </div>

</body>
</html>
<?php

exit;
