<?php

namespace Translucide\services;

use Exception;

class UtilsFunctionsConnexion
{
    /**
     * The Singleton's instance is stored in a static field. This field is an
     * array, because we'll allow our Singleton to have subclasses. Each item in
     * this array will be an instance of a specific Singleton's subclass. You'll
     * see how this works in a moment.
     */
    private static array $instances = [];

    private ?UtilsFunctionsNavigation $utilsFunctionNavigation = null;
    private ?UtilsFunctionsLanguage $utilsFunctionsLanguage = null;
    private Globals $globals;

    protected function __construct()
    {
        $this->globals = Globals::getInstance();
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

    public static function getInstance(): UtilsFunctionsConnexion
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new UtilsFunctionsConnexion();
        }

        return self::$instances[$cls];
    }

    public function getUtilsFunctionNavigation(): UtilsFunctionsNavigation
    {
        if (!$this->utilsFunctionNavigation) {
            $this->utilsFunctionNavigation = UtilsFunctionsNavigation::getInstance();
        }
        return $this->utilsFunctionNavigation;
    }

    public function getUtilsFunctionsLanguage(): UtilsFunctionsLanguage
    {
        if (!$this->utilsFunctionsLanguage) {
            $this->utilsFunctionsLanguage = UtilsFunctionsLanguage::getInstance();
        }
        return $this->utilsFunctionsLanguage;
    }

    public function curl($url, $params = null): bool|string
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/101.0.4951.67 Safari/537.36');
        if ($params) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params, null, '&'));
        }
        $return = curl_exec($curl);
        $getinfo = curl_getinfo($curl);
        curl_close($curl);

        //@todo: si retour erreur : faire un message d'erreur
        //highlight_string(print_r($getinfo, true));

        return $return;
    }

    // Crypte le mot de passe
    public function hash_pwd($pwd, $salt = null)
    {
        // Création du salt unique a cet utilisateur char(16)
        if (!$salt) {
            $unique_salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647));
        } // @todo: peut-etre remplacer cette fonction par make_pwd
        else {
            $unique_salt = $salt;
        }

        // Boucle pour encoder x fois le pwd avec le salt unique
        for ($i = 0; $i < $this->globals->pwd_hash_loop; $i++) {
            $pwd = hash('sha256', $pwd . $unique_salt . $this->globals->priv_hash);
        }

        if ($salt) {
            return $pwd; // Retour pour comparaison
        }
        return [$pwd, $unique_salt]; // Retour pour stockage
    }

    // Crée un password
    public function make_pwd($length = 12, $special_chars = false, $extra_special_chars = false): string
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

        if ($special_chars) {
            $chars .= "!@#%^&*()";
        } //$ <= Créé parfois un bug : crée une variable php

        if ($extra_special_chars) {
            $chars .= "-_ []{}<>~`+=,.;:/?|";
        }

        $password = "";

        for ($i = 0; $i < $length; $i++) {
            $password .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }

        return $password;
    }

    // Un hash/nonce pour faire des signatures. Evite les cross-site request forgery CSRF
    public function nonce($session = null): string
    {
        $nonce = hash("sha256", uniqid(mt_rand(), true));

        if ($session) {
            $_SESSION[$session] = $nonce;
        }

        return $nonce;
    }

    // Retourne l'adresse IP du client (utilisé pour empêcher le détournement de cookie de session)
    public function ip()
    {
        $ip = $_SERVER['REMOTE_ADDR'];

        // Ensuite, nous utilisons plusieurs en-têtes HTTP pour empêcher le détournement de session des utilisateurs derrière le même proxy
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $ip . '_' . $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $ip . '_' . $_SERVER['HTTP_CLIENT_IP'];
        }

        return $ip;
    }

    // Création d'un token
    public function token($uid, $email = null, $auth = null) // @todo: Vérif l'intérêt de mettre le mail et pas le name ou rien
    {
        // Si la fonction de memorisation de connexion de l'utilisateur et coché
        if (isset($_POST['rememberme'])) {
            setcookie("rememberme", $this->getUtilsFunctionNavigation()->encode($_POST['rememberme']), 0, $this->globals->path, $this->globals->domain);
            $_COOKIE['rememberme'] = $this->getUtilsFunctionNavigation()->encode($_POST['rememberme']);
        }

        // Date d'expiration (si on ne mémorise pas l'utilisateur on crée une session de 30min
        $time = time() + ((isset($_COOKIE['rememberme']) and $_COOKIE['rememberme'] == "false") ? (30 * 60) : $this->globals->session_expiration);

        // Id de l'utilisateur
        $_SESSION['uid'] = (int)$uid;

        // Nom de l'utilisateur
        if ($email) {
            $_SESSION['email'] = $email;
        }

        // Cookie+Session pour connaitre les autorisations utilisateur
        if ($auth) {
            $array_auth = explode(",", $auth);
            foreach ($array_auth as $cle => $val) {
                $_SESSION['auth'][$val] = true;
            }
            setcookie("auth", $this->getUtilsFunctionNavigation()->encode($auth, ",", ["-"]), $time, $this->globals->path, $this->globals->domain);
        }

        // Date d'expiration du login
        $_SESSION['expires'] = $time;

        // Faire en sorte que le token soit plus complet et autonome sans trop de variable dans la session
        $_SESSION['token'] = $token = hash("sha256", $_SESSION['uid'] . $_SESSION['expires'] . $this->ip() . $_SERVER['HTTP_USER_AGENT'] . $_SERVER['SERVER_NAME'] . $this->globals->pub_hash);

        // Niveau de sécurité élever, on enregistre le token dans la bdd
        if ($this->globals->security == 'high') {
            if (!$this->globals->connect) {
                include_once("db.php");
            } // Connexion à la db

            $this->globals->connect->query("UPDATE " . $this->globals->table_user . " SET token='" . $token . "' WHERE id='" . (int)$uid . "'");
        }

        return $token;
    }

    // Création d'un token light (utile lors des changements de mot de passe et donne la possibiliter le log sur plusieurs machines)
    public function token_light($uid, $salt)
    {
        $_SESSION['token_light'] = $token_light = hash("sha256", $salt . $uid . $this->globals->pub_hash);

        return $token_light;
    }

    // Vérifie si le token est bon
    public function token_check($token)
    {
        // @todo verif si ce n'est pas ça qui crée un bug collatéral de perte de session
        if (isset($_SESSION['uid']) and $token == hash("sha256", $_SESSION['uid'] . @$_SESSION['expires'] . $this->ip() . $_SERVER['HTTP_USER_AGENT'] . $_SERVER['SERVER_NAME'] . $this->globals->pub_hash) and time() < $_SESSION['expires']) {
            // On update la date d'expiration de la session
            $this->token($_SESSION['uid'], $_SESSION['email']);

            return true;
        } else {
            return false;
        }
    }

    // Connexion au site avec le système interne de login+password
    public function login($level = 'low', $auth = null, $quiet = null)
    {
        //////// Le level détermine le niveau de vérification pour des taches plus ou moins sensible
        // low : Vérif juste s'il y a un token dans la session
        // medium : Check le contenu du token
        // high : Check le token, et s'il est identique dans bdd (config : security=high ou token_light)

        // Vérifie que la personne qui a posté le formulaire a bien la variable de session de protection contre les CSRF
        $csrf = false;
        if (isset($_SESSION['nonce']) and $_SESSION['nonce'] != @$_REQUEST['nonce']) {
            $csrf = true;
        }

        // Pas de hack on vérifie l'utilisateur
        if (!$csrf) {
            // On se log avec le formulaire donc on check password & mail
            if (isset($_POST['email']) and isset($_POST['password'])) {
                if (!isset($this->globals->connect)) {
                    include_once("db.php");
                } // Connexion à la db

                // Supprime l'ancienne session
                @session_regenerate_id(true);

                // Nettoyage du mail envoyé
                $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
                $email = $this->globals->connect->real_escape_string($email);

                // Extraction des données de l'utilisateur
                $sel = $this->globals->connect->query("SELECT * FROM " . $this->globals->table_user . " WHERE email='" . $email . "' " . ($level == 'low' ? "" : "AND state='active'") . " LIMIT 1");
                $res = $sel->fetch_assoc();

                if (@$res['email']) {
                    // Création d'un token maison
                    if ($res['password'] == $this->hash_pwd($_POST['password'], $res['salt'])) {
                        $array_diff = array_diff(explode(",", (string)$auth), explode(",", $res['auth']));
                        if (isset($auth) and !empty($array_diff)) { // Vérifie les auth d'utilisateur si c'est demandée
                            $msg = $this->getUtilsFunctionsLanguage()->__("Bad credential");
                            $this->logout();
                        } elseif ($token = $this->token($res['id'], $res['email'], $res['auth'])) { // Tout est ok on crée le token
                            // Création d'un token light : permet une vérif au changement de mdp et permet log sur plusieurs machines
                            if ($this->globals->security != 'high') {
                                $token_light = $this->token_light($res['id'], $res['salt']);
                                $this->globals->connect->query("UPDATE LOW_PRIORITY " . $this->globals->table_user . " SET token='" . $token_light . "' WHERE id='" . $res['id'] . "'");
                            }

                            // On est logé !
                            return true;
                        }
                    } else {
                        $msg = $this->getUtilsFunctionsLanguage()->__("Connection error"); //Password error
                        $this->logout();
                    }
                } else {
                    $msg = $this->getUtilsFunctionsLanguage()->__("Connection error"); //Password error
                    $this->logout();
                }
            } // Sinon on vérifie la validité du token et s'il n'a pas expiré
            elseif (isset($_SESSION['token'])) {
                if ($level == 'medium' and $this->globals->security != 'high') { // Vérification mode moyen
                    if (!$this->token_check($_SESSION['token'])) { // Vérification du contenu du token
                        $msg = $this->getUtilsFunctionsLanguage()->__("Token error");
                        $this->logout();
                    } elseif (isset($auth)) { // Vérifie les autorisations utilisateur dans la bdd si c'est demandée
                        if (!isset($this->globals->connect)) {
                            include_once("db.php");
                        } // Connexion à la db

                        // Extraction des données de l'utilisateur
                        $sel = $this->globals->connect->query("SELECT auth FROM " . $this->globals->table_user . " WHERE id='" . (int)$_SESSION['uid'] . "' AND state='active' LIMIT 1");
                        $res = $sel->fetch_assoc();

                        $array_diff = array_diff(explode(",", $auth), explode(",", $res['auth']));
                        if (!empty($array_diff)) {
                            $msg = $this->getUtilsFunctionsLanguage()->__("Bad credential");
                            $this->logout();
                        } else {
                            return true;
                        }
                    } else {
                        return true;
                    }
                } elseif (($this->globals->security == 'high' or $level == 'high') and $this->token_check($_SESSION['token'])) { // Comparaison avec le token dans la bdd
                    if (!isset($this->globals->connect)) {
                        include_once("db.php");
                    } // Connexion à la db

                    @session_regenerate_id(true); // Supprime l'ancienne session

                    $sel = $this->globals->connect->query("SELECT auth, token FROM " . $this->globals->table_user . " WHERE id='" . (int)$_SESSION['uid'] . "' AND state='active' LIMIT 1");
                    $res = $sel->fetch_assoc();

                    $array_diff = array_diff(explode(",", $auth), explode(",", $res['auth']));
                    if (isset($auth) and !empty($array_diff)) { // Vérifie les autorisations
                        $msg = $this->getUtilsFunctionsLanguage()->__("Bad credential");
                        $this->logout();
                    } elseif ($this->globals->security == 'high' and $res['token'] == $_SESSION['token']) {
                        return true;
                    } // Sécurité haute forcée dans la config
                    elseif ($level == 'high' and $res['token'] == $_SESSION['token_light']) {
                        return true;
                    } // Verification du token light (changement de pwd...)
                    else {
                        $msg = $this->getUtilsFunctionsLanguage()->__("Connection error");
                        $this->logout();
                    }
                } else {
                    return true;
                }
            } else {
                //$msg = $this->getUtilsFunctionsLanguage()->__("No token");
                $this->logout();
            }
        } else {
            $msg = $this->getUtilsFunctionsLanguage()->__("Nonce error");
            $this->logout();
        }


        // Si pas de token ou si le login échoue on lance la dialog de connexion et exit l'action courante
        if (!isset($_SESSION['token']) and !$quiet) {
            ?>
            <link rel="stylesheet" href="<?= $this->globals->jquery_ui_css ?>">

            <link rel="stylesheet" href="<?= $this->globals->path ?>api/lucide.css">

            <script>
              // Ouverture de la dialog de connexion
              $(function () {
                //$(".ui-dialog-content").dialog("close"); // On ferme les dialogs en cours

                if (typeof tosave == 'function') tosave(); // Mode : A sauvegarder

                // Chargement de Jquery UI
                $.ajax({
                  url: "<?=$this->globals->jquery_ui?>",
                  dataType: 'script',
                  cache: true,
                  success: function ()// Si Jquery UI bien charger on charge la dialog de choix de login
                  {
                    // On ferme la dialog de connexion s'il y en a une d'ouvert
                    if ($("#dialog-connect").length) $("#dialog-connect").dialog("close");

                    // On ouvre la dialog de choix du système de login et affiche une erreur
                    $.ajax({
                      url: "<?=$this->globals->path?>api/ajax.php?mode=internal-login",
                      data: {
                        callback: "<?=(isset($_REQUEST['callback']) ? $this->getUtilsFunctionNavigation()->encode($_REQUEST['callback'], "_") : "")?>",
                        msg: "<?=htmlspecialchars((isset($msg) ? $msg : ""));?>"
                      }
                    })
                      .done(function (html) {
                        $("body").append(html);

                        // Effet sur la dialog
                        $("#dialog-connect").dialog({
                          //modal: true, // Fond gris lors du login
                          width: 'auto',
                          minHeight: 0,
                          show: {effect: "fadeIn"},
                          //hide: {effect: "fadeOut"},// Bug collateral : empèche la re-ouverture rapide de la dialog de connexion
                          create: function () {
                            // Change le title en H1 pour l'accessibilitée
                            $(".ui-dialog-title").attr("role", "heading").attr("aria-level", "1");
                          },
                          closeText: __("Close"),
                          close: function () {
                            $("#dialog-connect").remove();
                          }
                        });
                      });
                  },
                  async: true
                });


              });
            </script>
            <?php
            exit;
        } elseif ($quiet == 'error') { ?>
            <script>$(function () {
                error("<?=$msg?>", 4000);
              });</script>
        <?php }
    }

    public function logout($redirect = null): void
    {
        // Supprime les variables de session de connexion
        unset($_SESSION['token'], $_SESSION['uid'], $_SESSION['expires'], $_SESSION['nonce'], $_SESSION['auth'], $_COOKIE['auth'], $_SESSION['state']); // session_destroy();

        // Supprime le cookie d'autorisation user
        @setcookie("auth", "", time() - 3600, $this->globals->path, $this->globals->domain);

        // Supprime le cookie de memorisation de l'utilisateur
        @setcookie("rememberme", "", time() - 3600, $this->globals->path, $this->globals->domain);

        // Si redirection
        if ($redirect == "login") {
            header("Location: ajax.php");
            exit;
        } elseif ($redirect == "home" or $redirect == "index") { // @todo A terme supprimer "home" car on utilise "index" maintenant 13/07/2020
            header("Location: " . $this->globals->home);
            exit;
        }
    }

}
