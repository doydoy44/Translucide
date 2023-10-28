<?php

use Translucide\db\DataBase;
use Translucide\services\UtilsFunctionsConnexion;
use Translucide\services\UtilsFunctionsLanguage;

//include_once("config.init.php"); // Les variables par défaut
//include_once("function.php"); // Fonction
include_once("../../src/db/DataBase.php");
include_once("../../src/services/UtilsFunctionsConnexion.php");
include_once("../../src/services/UtilsFunctionsLanguage.php");

$languageFc = UtilsFunctionsLanguage::getInstance();
$connexion = UtilsFunctionsConnexion::getInstance();

//$lang = $language->get_lang(); // Sélectionne  la langue
//$language->load_translation('api'); // Chargement des traductions du système

// CRÉATION / Mise à jour des données de configuration

// Chemin des fichiers de config
$config_sample_file = "config.init.php";
$config_final_file = "../config.php";

@include_once($config_final_file);

// Vérification du nonce et si la config n'est pas déjà créée
if ($_SESSION['nonce'] == @$_REQUEST['nonce'] and (!$GLOBALS['db_server'] or !$GLOBALS['db_user'] or !$GLOBALS['db'])) {
    // Traduction de la page d'installation
    $add_translation = [
        "Table already exists" => ["fr" => "La table existe déjà"],
        "User already exists : update password" => ["fr" => "L'utilisateur existe déjà : mise à jour du mot de passe"],
        "Wrong email" => ["fr" => "Mauvais email"],
        "Successful installation ! Redirection to homepage ..." => ["fr" => "Installation réussie ! Redirection vers la page d'accueil ..."]
    ];

    $languageFc->add_translation($add_translation);


    if (@$_POST['db_server'] and @$_POST['db_user'] and @$_POST['db']) {
        // BASE DE DONNEE
        // Connexion à la bdd

        $dataBase = DataBase::getInstance();
        $dataBase->setPostConnextion();
        

        // Désactive les exceptions qui bloquent l'exécution de php > 8.0
        mysqli_report(MYSQLI_REPORT_OFF);

        if ($dataBase->getConnect()->connect_errno) { // Erreur
            ?>
        <script>
          submittable();
          error("<?=mb_convert_encoding($dataBase->getConnect()->connect_error, 'UTF-8', mb_list_encodings());?>");
        </script>
        <?php
            exit;
        }

        // Réussite

        // Nom des tables
        $GLOBALS['table_content'] = addslashes($_POST['db_prefix'] . "content");
        $GLOBALS['table_tag'] = addslashes($_POST['db_prefix'] . "tag");
        $GLOBALS['table_meta'] = addslashes($_POST['db_prefix'] . "meta");
        $GLOBALS['table_user'] = addslashes($_POST['db_prefix'] . "user");

        // Vérification de l'existence des bases de données
        if ($dataBase->getConnect()->query("SELECT id FROM " . $GLOBALS['table_content'])) {
            // Table déjà existante
            ?>
        <script>light("<?php $languageFc->_e("Table already exists")?> : content");</script><?php
        } else {
            // Création de la base de données
            $dataBase->getConnect()->query("
                CREATE TABLE IF NOT EXISTS `" . $GLOBALS['table_content'] . "` (
                    `id` bigint(20) NOT NULL AUTO_INCREMENT,
                    `state` varchar(20) NOT NULL DEFAULT 'deactivate',
                    `lang` varchar(8) NOT NULL,
                    `robots` varchar(18) DEFAULT NULL,
                    `type` varchar(20) NOT NULL DEFAULT 'page',
                    `tpl` varchar(80) NOT NULL,
                    `url` varchar(70) DEFAULT NULL,
                    `title` varchar(70) NOT NULL,
                    `description` varchar(160) DEFAULT NULL,
                    `content` longtext,
                    `user_update` bigint(20) UNSIGNED DEFAULT NULL,
                    `date_update` datetime DEFAULT NULL,
                    `user_insert` bigint(20) UNSIGNED NOT NULL,
                    `date_insert` datetime NOT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `url` (`url`,`lang`) USING BTREE,
                    KEY `state` (`state`),
                    KEY `type` (`type`),
                    KEY `lang` (`lang`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
            ");

            if ($dataBase->getConnect()->error) {
                ?>
            <script>
              submittable();
              error("<?=mb_convert_encoding($dataBase->getConnect()->error, 'UTF-8', mb_list_encodings());?>");
            </script>
            <?php
                exit;
            }
        }

        // Vérification de l'existence des bases de données
        if ($dataBase->getConnect()->query("SELECT id FROM " . $GLOBALS['table_meta'])) {
            // Table déjà existante
            ?>
        <script>light("<?php $languageFc->_e("Table already exists")?> : meta");</script><?php
        } else {
            // Création de la base de données
            $dataBase->getConnect()->query("
                CREATE TABLE IF NOT EXISTS `" . $GLOBALS['table_meta'] . "` (
                    `id` bigint(20) NOT NULL DEFAULT '0',
                    `type` varchar(32) NOT NULL,
                    `cle` varchar(255) NOT NULL DEFAULT '',
                    `val` text,
                    `ordre` smallint(6) NOT NULL DEFAULT '0',
                    PRIMARY KEY (`id`,`type`,`cle`),
                    KEY `type` (`type`,`cle`),
                    KEY `ordre` (`ordre`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
            ");

            if ($dataBase->getConnect()->error) {
                ?>
            <script>
              submittable();
              error("<?=mb_convert_encoding($dataBase->getConnect()->error, 'UTF-8', mb_list_encodings());?>");
            </script>
            <?php
                exit;
            }
        }

        // Vérification de l'existence des base de données
        if ($dataBase->getConnect()->query("SELECT id FROM " . $GLOBALS['table_tag'])) {
            // Table déjà existante
            ?>
        <script>light("<?php $languageFc->_e("Table already exists")?> : tag");</script><?php
        } else {
            // Création de la base de données
            $dataBase->getConnect()->query("
                CREATE TABLE IF NOT EXISTS `" . $GLOBALS['table_tag'] . "` (
                    `id` bigint(20) NOT NULL DEFAULT '0',
                    `zone` varchar(32) NOT NULL,
                    `lang` varchar(8) NOT NULL DEFAULT 'fr',
                    `encode` varchar(255) NOT NULL DEFAULT '',
                    `name` text NOT NULL,
                    `ordre` smallint(6) NOT NULL DEFAULT '0',
                    PRIMARY KEY (`id`,`zone`,`lang`,`encode`),
                    KEY `zone` (`zone`,`lang`,`encode`),
                    KEY `ordre` (`ordre`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
            ");

            if ($dataBase->getConnect()->error) {
                ?>
            <script>
              submittable();
              error("<?=mb_convert_encoding($dataBase->getConnect()->error, 'UTF-8', mb_list_encodings());?>");
            </script>
            <?php
                exit;
            }
        }

        // Vérification de l'existence des bases de données
        if ($dataBase->getConnect()->query("SELECT id FROM " . $GLOBALS['table_user'])) {
            // Table déjà existante
            ?>
        <script>light("<?php $languageFc->_e("Table already exists")?> : user");</script><?php
        } else {
            // Création de la base de données
            $dataBase->getConnect()->query("
                CREATE TABLE IF NOT EXISTS `" . $GLOBALS['table_user'] . "` (
                    `id` bigint(20) NOT NULL AUTO_INCREMENT,
                    `state` varchar(20) NOT NULL DEFAULT 'active',
                    `auth` text NOT NULL,
                    `name` varchar(60) DEFAULT NULL,
                    `email` varchar(100) NOT NULL,
                    `info` text,
                    `password` char(64) DEFAULT NULL,
                    `salt` char(16) DEFAULT NULL,
                    `token` varchar(255) DEFAULT NULL COMMENT 'token light',
                    `oauth` text COMMENT 'Token api externe',
                    `date_update` datetime DEFAULT NULL,
                    `date_insert` datetime NOT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `email` (`email`),
                    KEY `state` (`state`)								
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
            ");

            if ($dataBase->getConnect()->error) {
                ?>
            <script>
              submittable();
              error("<?=mb_convert_encoding($dataBase->getConnect()->error, 'UTF-8', mb_list_encodings());?>");
            </script>
            <?php
                exit;
            }
        }

        // UTILISATEUR

        // Droit d'edition de base
        $auth = null;
        foreach ($GLOBALS['add_content'] as $cle => $val) {
            $auth .= ',add-' . $cle . ',edit-' . $cle;
        }

        // Vérification de l'email
        $email = filter_input(INPUT_POST, 'email_contact', FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            ?>
        <script>
          submittable();
          light("<?php $languageFc->_e("Wrong email")?>");
        </script>
        <?php
            exit;
        }


        // Clean l'email pour éviter les injections
        $email = $dataBase->getConnect()->real_escape_string($email);

        // Crée un hash si pas déjà un chargé par la config maison
        if (!$GLOBALS['pub_hash']) {
            $GLOBALS['pub_hash'] = $_POST['pub_hash'] = $connexion->make_pwd(mt_rand(32, 64), true, true);
        }
        if (!$GLOBALS['priv_hash']) {
            $GLOBALS['priv_hash'] = $_POST['priv_hash'] = $connexion->make_pwd(mt_rand(32, 64), true, true);
        }
        if (!$GLOBALS['pwd_hash_loop']) {
            $GLOBALS['pwd_hash_loop'] = $_POST['pwd_hash_loop'] = mt_rand(60536, 65536);
        }

        // Email pour le login automatique
        $_POST['email'] = $email;

        // Vérifie que l'utilisateur n'existe pas déjà
        $sel = $dataBase->getConnect()->query("SELECT id FROM " . addslashes($_POST['db_prefix']) . "user WHERE email='" . $email . "' AND state='active' LIMIT 1");
        if ($res = $sel->fetch_assoc()) { // User déjà existant : on update ses données
            $uid = $res['id'];

            // Création de la requête
            $sql = "UPDATE " . addslashes($_POST['db_prefix']) . "user SET ";
            $sql .= "state = 'active', ";
            $sql .= "auth = '" . addslashes(implode(",", array_keys($GLOBALS['auth_level'])) . $auth) . "', "; // Donne tous les droits

            list($password, $unique_salt) = $connexion->hash_pwd($_POST['password']);

            if ($password and $unique_salt) {
                $sql .= "password = '" . addslashes($password) . "', ";
                $sql .= "salt = '" . addslashes($unique_salt) . "', ";
                //if($GLOBALS['security'] != 'high') $sql .= "token = '".addslashes(token_light((int)$_REQUEST['uid'], $unique_salt))."', "; Voir si utile !??
            }

            $sql .= "date_update = NOW() ";

            $sql .= "WHERE id = '" . $res['id'] . "'";

            // Exécution de la requête
            $dataBase->getConnect()->query($sql);

            if ($dataBase->getConnect()->error) {
                ?>
            <script>
              submittable();
              error("<?=mb_convert_encoding($dataBase->getConnect()->error, 'UTF-8', mb_list_encodings());?>");
            </script>
            <?php
                exit;
            }
            ?>
        <script>
          light("<?php $languageFc->_e("User already exists : update password")?>");
        </script>
        <?php
        } else { // Création de l'utilisateur admin avec tous les droits
            // Création de la requête
            $sql = "INSERT INTO " . addslashes($_POST['db_prefix']) . "user SET ";
            $sql .= "state = 'active', ";

            $sql .= "auth = '" . addslashes(implode(",", array_keys($GLOBALS['auth_level'])) . $auth) . "', "; // Donne tous les droits

            $sql .= "email = '" . addslashes($email) . "', ";

            list($password, $unique_salt) = $connexion->hash_pwd($_POST['password']);

            if ($password and $unique_salt) {
                $sql .= "password = '" . addslashes($password) . "', ";
                $sql .= "salt = '" . addslashes($unique_salt) . "', ";
                //if($GLOBALS['security'] != 'high') $sql .= "token = '".addslashes(token_light((int)$_REQUEST['uid'], $unique_salt))."', "; Voir si utile !??
            }

            $sql .= "date_insert = NOW() ";

            // Exécution de la requête
            $dataBase->getConnect()->query($sql);

            if ($dataBase->getConnect()->error) {
                ?>
            <script>
              submittable();
              error("<?=mb_convert_encoding($dataBase->getConnect()->error, 'UTF-8', mb_list_encodings());?>");
            </script>
            <?php
                exit;
            } else {
                $uid = $dataBase->getConnect()->insert_id;
            }
        }

        // ECRITURE DE LA CONFIGRATION

        // Ouverture du fichier config. Si pas de config on prend le sample
        if (file_exists($config_final_file)) {
            $config_file = file($config_final_file);
        } else {
            $config_file = file($config_sample_file);
        }

        // Séparation des données du chemin du site
        $parse_url = parse_url($_POST['scheme_domain_path']);
        $_POST['scheme'] = $parse_url['scheme'] . "://";
        $_POST['domain'] = $GLOBALS['domain'] = $parse_url['host'];
        $_POST['path'] = $GLOBALS['path'] = $parse_url['path'];

        // Formate le nom du site
        $_POST['sitename'] = htmlspecialchars(stripslashes(@$_POST['sitename']));

        // Cache du jour de l'install
        $_POST['cache'] = $GLOBALS['cache'] = date("Ymd");

        // On parcourt le fichier config
        foreach ($config_file as $line_num => $line) {
            // On récupère la clé de la variable en cours
            preg_match("/GLOBALS\[\'([a-z_]+)\'\]/", $line, $match);

            if (isset($match[1])) {
                $key = $match[1];
            } else {
                $key = "";
            }

            // Changement de la ligne et ajout de la nouvelle variable
            if ($key !== "" and isset($_POST[$key])) {
                $config_file[$line_num] = "\$GLOBALS['" . $key . "'] = \"" . mb_convert_encoding($_POST[$key], 'ISO-8859-1', 'UTF-8') . "\";\r\n";
            }
        }

        unset($line);

        // écriture dans le fichier config
        $fopen = fopen($config_final_file, 'w');
        foreach ($config_file as $line) {
            fwrite($fopen, $line);
        }
        fclose($fopen);

        // Force les droits sur le fichier config
        chmod($config_final_file, 0666);


        // AJOUTE LA PAGE D'ACCUEIL
        // Vérifie qu'il n'y a pas déjà une page home
        $sel = $dataBase->getConnect()->query("SELECT id FROM " . addslashes($_POST['db_prefix']) . "content WHERE url='index' LIMIT 1");
        $res = $sel->fetch_assoc();
        if (!@$res['id']) { // Page non existante : on la crée
            // Ajout de la page d'accueil
            $sql = "INSERT " . addslashes($_POST['db_prefix']) . "content SET ";
            $sql .= "title = '" . addslashes(mb_convert_encoding(@$_POST['sitename'], 'ISO-8859-1', 'UTF-8')) . "', ";
            $sql .= "tpl = 'home', ";
            $sql .= "url = 'index', ";
            $sql .= "lang = '" . $GLOBALS['language'][0] . "', ";
            $sql .= "type = 'page', ";
            $sql .= "user_insert = '" . (int)$uid . "', ";
            $sql .= "date_insert = NOW() ";
            $dataBase->getConnect()->query($sql);

            if ($dataBase->getConnect()->error) {
                ?>
            <script>
              submittable();
              error("<?=mb_convert_encoding($dataBase->getConnect()->error, 'UTF-8', mb_list_encodings());?>");
            </script>
            <?php
                exit;
            } else {
                // Pose un cookie pour demander l'ouverture de l'admin automatiquement au chargement
                setcookie("autoload_edit", "true", time() + 60 * 60, $_POST['path'], $_POST['domain']);
            }
        }

        // LOGIN AUTOMATIQUE
        $connexion->login();

        // MESSAGE DE BIENVENUE et d'information qu'il faut créé la page d'accueil du site
        ?>
    <script>
      light("<?php $languageFc->_e("Successful installation ! Redirection to homepage ...")?>");
      setTimeout(function () {
        $("#error, #highlight, #light").slideUp("slow").fadeOut(function () {
          window.location.reload(); // window.location = window.location.href;
        });
      }, 3000);
    </script>
    <?php
    }
}

exit;
