<?php

namespace Translucide\services;

use Exception;

class UtilsFunctionsLanguage
{
    /**
     * The Singleton's instance is stored in a static field. This field is an
     * array, because we'll allow our Singleton to have subclasses. Each item in
     * this array will be an instance of a specific Singleton's subclass. You'll
     * see how this works in a moment.
     */
    private static array $instances = [];

    private ?UtilsFunctionsNavigation $utilsFunctionNaviation = null;
    private ?Globals $globals = null;

    // Langue alternative si une traduction n'existe pas
    private string $langAlt = 'en';

    private array $addApiTranslation = [
        "404 error : page not found" => ["fr" => "Erreur 404 : page introuvable"],
        "Under Construction" => ["fr" => "En construction"],
        "Site closing time" => ["fr" => "Heure de fermeture du site"],

        "Quick access" => ["fr" => "Accès rapide"],
        "Skip to content" => ["fr" => "Aller au contenu"],
        "Enhanced contrast" => ["fr" => "Contraste renforcé"],
        "Browsing menu" => ["fr" => "Menu de navigation"],
        "current page" => ["fr" => "page active"],
        "Close" => ["fr" => "Fermer"],

        "Search" => ["fr" => "Recherche"],
        "Next" => ["fr" => "Suivant"],
        "Previous" => ["fr" => "Précédent"],

        "Text" => ["fr" => "Texte"],

        "Log in" => ["fr" => "Connexion"],
        "Administrator Login" => ["fr" => "Connexion Administrateur"],
        "Connection with" => ["fr" => "Connexion avec"],
        "Connection error" => ["fr" => "Erreur de connexion"],
        "Token error" => ["fr" => "Erreur de jeton"],
        "Password error" => ["fr" => "Erreur de mot de passe"],
        "Unknown user" => ["fr" => "Utilisateur inconnu"],
        "Unable to find the user number" => ["fr" => "Impossible de trouver le numéro d'utilisateur"],
        "Unable to find the access token" => ["fr" => "Impossible de trouver le jeton d'accès"],
        "Bad credential" => ["fr" => "Vous n'avez pas le niveau d'accès requis"],
        "Connection required" => ["fr" => "Connexion requise"],
        "You are already connected" => ["fr" => "Vous êtes déjà connecté"],

        "Validate the connection in the popup" => ["fr" => "Validez la connexion dans la fenêtre qui vient de s'ouvrir"],

        "My email" => ["fr" => "Mon courriel"],
        "My password" => ["fr" => "Mon mot de passe"],
        "Remember me" => ["fr" => "Se souvenir de moi"],
        "Forgot your password" => ["fr" => "Mot de passe oublié"],
        "Suggest a password" => ["fr" => "Suggérer un mot de passe"],
        "Send password by mail" => ["fr" => "Envoyer le mot de passe par mail"],
        "New Password" => ["fr" => "Nouveau mot de passe"],
        "See password" => ["fr" => "Voir le mot de passe"],
        "Not a member yet ?" => ["fr" => "Pas encore membre ?"],
        "Sign up" => ["fr" => "Inscrivez-vous"],
        "All fields are mandatory" => ["fr" => "Tous les champs sont obligatoires"],
        "Expected format" => ["fr" => "Format attendu"],
        "Invalid email" => ["fr" => "E-mail invalide"],

        "List of contents" => ["fr" => "Liste des contenus"],
        "Editing tutorial" => ["fr" => "Tutoriel édition"],


        "Add content" => ["fr" => "Ajouter un contenu"],
        "Add page" => ["fr" => "Ajouter une page"],
        "Add article" => ["fr" => "Ajouter une actualité"],
        "Add event" => ["fr" => "Ajouter un évènement agenda"],
        "Add media" => ["fr" => "Ajouter un média"],
        "Add video" => ["fr" => "Ajouter une vidéo"],
        "Add product" => ["fr" => "Ajouter un produit"],

        "Add Item" => ["fr" => "Ajouter un élément"],


        "Title" => ["fr" => "Titre"],
        "Select template" => ["fr" => "Sélectionner un modèle"],
        "Permanent link" => ["fr" => "Lien Permanent"],
        "Regenerate address" => ["fr" => "Regénérer l'adresse"],
        "Permanent link: 'index' if homepage" => ["fr" => "Lien permanent: 'index' si c'est la page d'accueil"],
        "No permanent link for content" => ["fr" => "Pas de lien permanent pour le contenu"],


        "Page title" => ["fr" => "Titre de la page"],
        "Description for search engines" => ["fr" => "Description pour les moteurs de recherche"],
        "Formatted web address" => ["fr" => "Adresse web formaté"],

        "Template" => ["fr" => "Modèle de page"],
        "Type of page" => ["fr" => "Type de page"],
        "Creation date" => ["fr" => "Date de création"],

        "Image on social networks" => ["fr" => "Image sur les réseaux sociaux"],

        "Close the edit mode" => ["fr" => "Fermer le mode d'édition"],
        "Activation status" => ["fr" => "Etat d'activation"],


        "Home page" => ["fr" => "Page d'accueil"],

        "New window" => ["fr" => "Nouvelle fenêtre"],

        "january" => ["fr" => "janvier"],
        "february" => ["fr" => "février"],
        "march" => ["fr" => "mars"],
        "april" => ["fr" => "avril"],
        "may" => ["fr" => "mai"],
        "june" => ["fr" => "juin"],
        "july" => ["fr" => "juillet"],
        "august" => ["fr" => "août"],
        "september" => ["fr" => "septembre"],
        "october" => ["fr" => "octobre"],
        "november" => ["fr" => "novembre"],
        "december" => ["fr" => "décembre"],
        "st" => ["fr" => "er"],

        "zero" => ["fr" => "zéro"],
        "one" => ["fr" => "un"],
        "two" => ["fr" => "deux"],
        "three" => ["fr" => "trois"],
        "four" => ["fr" => "quatre"],
        "five" => ["fr" => "cinq"],
        "six" => ["fr" => "six"],
        "seven" => ["fr" => "sept"],
        "eight" => ["fr" => "huit"],
        "nine" => ["fr" => "neuf"],
        "ten" => ["fr" => "dix"],


        "Media Library" => ["fr" => "Bibliothèque des médias"],
        "Drag and drop a file here or click me" => ["fr" => "Glisser-déplacer un fichier ici ou cliquez-moi"],
        "Delete file" => ["fr" => "Supprimer le fichier"],
        "Media" => ["fr" => "Médias"],
        "Images" => ["fr" => "Images"],
        "Resized" => ["fr" => "Redimensionnées"],
        "Files" => ["fr" => "Fichiers"],
        "Specific" => ["fr" => "Spécifique"],
        "Videos" => ["fr" => "Vidéos"],
        "Audios" => ["fr" => "Audios"],
        "Get resized image" => ["fr" => "Obtenir une image redimensionnée"],
        "Copy to clipboard" => ["fr" => "Copier dans le presse papier"],

        "Size of source file unspecified" => ["fr" => "Taille du fichier source non précisée"],
        "Unsupported file type" => ["fr" => "Type de fichier non pris en charge"],
        "A file with the same name already exists" => ["fr" => "Un fichier avec le même nom existe déjà"],

        "Icon Library" => ["fr" => "Bibliothèque d'icône"],


        "Loading" => ["fr" => "Chargement"],


        "Save" => ["fr" => "Enregistrer"],
        "Add" => ["fr" => "Ajouter"],
        "Delete" => ["fr" => "Supprimer"],
        "Archive" => ["fr" => "Archiver"],


        "Show user info" => ["fr" => "Voir les infos utilisateur"],

        "Register" => ["fr" => "S'inscrire"],
        "Account created" => ["fr" => "Compte créé"],
        "User deleted" => ["fr" => "Utilisateur supprimé"],

        "Add user" => ["fr" => "Ajouter un utilisateur"],
        "List of user" => ["fr" => "Liste des utilisateur"],
        "My profil" => ["fr" => "Mon profil"],

        "Your profile" => ["fr" => "Votre profil"],
        "Profile" => ["fr" => "Profil"],

        "State" => ["fr" => "Etat"],
        "Authorization" => ["fr" => "Autorisation"],
        "Name" => ["fr" => "Pseudo"],
        "Password" => ["fr" => "Mot de passe"],
        "Updated the" => ["fr" => "Mise à jour le"],
        "Add the" => ["fr" => "Ajouté le"],

        "Active" => ["fr" => "Actif"],
        "Moderate" => ["fr" => "Modérer"],
        "User email" => ["fr" => "Mail utilisateur"],
        "Blacklist" => ["fr" => "Liste noire"],
        "Deactivate" => ["fr" => "Désactivé"],

        "New user to activate" => ["fr" => "Nouvel utilisateur a activé"],
        "User profile" => ["fr" => "Profil de l'utilisateur"],


        "Managing admins" => ["fr" => "Gestion des admins"],
        "Managing users" => ["fr" => "Gestion des utilisateurs"],

        "Edit Config" => ["fr" => "Modifier la config"],

        "Edit menu" => ["fr" => "Edition du menu"],
        "Edit header" => ["fr" => "Modifier l'en-tête"],
        "Edit footer" => ["fr" => "Modifier le pied de page"],

        "Send Files" => ["fr" => "Envoyer des fichiers"],
        "Edit Files" => ["fr" => "Modifier les fichiers"],

        "Edit page" => ["fr" => "Edition des pages"],
        "Edit article" => ["fr" => "Edition des articles"],
        "Edit product" => ["fr" => "Edition des produits"],
        "Edit video" => ["fr" => "Edition des vidéo"],

        "Public content" => ["fr" => "Contenu public"],
        "Public file" => ["fr" => "Dossier public"],

        "Page archived, redirecting" => ["fr" => "Page archivée, redirection en cours"],
        "Page deleted" => ["fr" => "Page supprimée"],
        "Page deleted, redirecting" => ["fr" => "Page supprimée, redirection en cours"],


        "Log out" => ["fr" => "Se déconnecter"]
    ];

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

    public static function getInstance(): UtilsFunctionsLanguage
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new UtilsFunctionsLanguage();
        }

        return self::$instances[$cls];
    }

    public function getUtilsFunctionNaviation(): UtilsFunctionsNavigation
    {
        if (!$this->utilsFunctionNaviation) {
            $this->utilsFunctionNaviation = UtilsFunctionsNavigation::getInstance();
        }
        return $this->utilsFunctionNaviation;
    }

    public function getGlobals(): Globals
    {
        if (!$this->globals) {
            $this->globals = Globals::getInstance();
        }
        return $this->globals;
    }

    public function getLangAlt(): string
    {
        return $this->langAlt;
    }

    // Sélectionne la langue
    public function get_lang($lang = '')
    {
        // Si la langue est déjà dans la session
        if (isset($_SESSION['lang'])) {
            $lang = $_SESSION['lang'];
        } elseif (!$lang and @$_SERVER['HTTP_ACCEPT_LANGUAGE']) { // Si pas de langue on prend la 1er langue du navigateur
            preg_match_all('~([\w-]+)(?:[^,\d]+([\d.]+))?~', strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']), $matches, PREG_SET_ORDER);
            $explode = explode("-", $matches[0][1]);
            $lang = $explode[0];
        }

        // Si la langue de l'utilisateur n'existe pas pour les contenus de ce site on charge la langue par défaut
        if (!in_array($lang, $this->getGlobals()->getLanguage())) {
            $lang = $this->getGlobals()->getLanguage()[0];
        }

        // Création du cookie avec la langue. Utile pour le js
        setcookie("lang", $lang, time() + $this->getGlobals()->getSessionExpiration(), $this->getGlobals()->getPath(), $this->getGlobals()->getDomain());

        $this->getGlobals()->setLang($lang);
        $_SESSION['lang'] = $_COOKIE['lang'] = $lang;

        return $this->getUtilsFunctionNaviation()->encode($lang);
    }

    // Charge une traduction
    public function load_translation($id)
    {
        if ($id === "api") {
            $this->add_translation($this->addApiTranslation);
            return;
        }
        $translation_file = match ($id) {
            "theme" => "theme/" . $this->getGlobals()->getTheme() . ($this->getGlobals()->getTheme() ? "/" : "") . "translation.php",
            default => "plugin/" . $id . "/translation.php",
        };

        // On récupère le fichier de traduction
        @include($_SERVER['DOCUMENT_ROOT'] . $this->getGlobals()->getPath() . $translation_file);

        // Ajoute la traduction au tableau des traductions
        if (isset($add_translation)) { // définit dans l'include au dessus
            $this->add_translation($add_translation);
        }
    }

    // Ajoute la traduction
    public function add_translation($add_translation): void
    {
        // On met toutes les clés en minuscule pour une recherche insensible à la case
        $add_translation = array_change_key_case($add_translation, CASE_LOWER);

        // Encodage des clés avec accent => ne fonctionne pas / a peaufiné pour les traductions à partir du Français
        //foreach($add_translation as $cle => $val) $add_translation_encode[utf8_encode($cle)] = $val;
        //$add_translation = $add_translation_encode;

        // On ajoute la nouvelle traduction au tableau de toutes les traductions
        $this->getGlobals()->setTranslation(array_merge($this->getGlobals()->getTranslation(), $add_translation));
    }

    // Retourne une traduction
    public function __($singulier, $pluriel = "", $num = 0)
    {
        // Traduction direct dans la fonction
        if (is_array($singulier)) {
            if (isset($singulier[key($singulier)][$this->getGlobals()->getLang()])) { // Une traduction dans la langue courante
                return $singulier[key($singulier)][$this->getGlobals()->getLang()];
            }
            return key($singulier);
        }
        if ($num > 1) {
            $txt = $pluriel;
        } else {
            $txt = $singulier;
        }

        // Si une traduction existe
        if (isset($this->getGlobals()->getTranslation()[mb_strtolower($txt)][$this->getGlobals()->getLang()])) {
            return $this->getGlobals()->getTranslation()[mb_strtolower($txt)][$this->getGlobals()->getLang()];
        }
        // Si une langue alternative est définie et qu'une traduction existe
        if (isset($this->getGlobals()->getTranslation()[mb_strtolower($txt)][$this->langAlt])) {
            return $this->getGlobals()->getTranslation()[mb_strtolower($txt)][$this->langAlt];
        }

        return $txt;
    }

    // Affichage d'une traduction
    public function _e($singulier, $pluriel = "", $num = 0): void
    {
        echo $this->__($singulier, $pluriel, $num);
    }

}
