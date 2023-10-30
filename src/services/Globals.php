<?php

namespace Translucide\services;

use Exception;

include_once(dirname(__FILE__)."/../../config.php");

class Globals
{
    /**
     * The Singleton's instance is stored in a static field. This field is an
     * array, because we'll allow our Singleton to have subclasses. Each item in
     * this array will be an instance of a specific Singleton's subclass. You'll
     * see how this works in a moment.
     */
    private static array $instances = [];

    private string $dbPrefix = "";
    private string $dbCharset = "";
    private string $tableContent = "";
    private string $tc = "";
    private string $tableMeta = "";
    private string $tm = "";
    private string $tableTag = "";
    private string $tt = "";
    private string $tableUser = "";
    private string $tu = "";
    private string $dbServer = "";
    private string $dbUser = "";
    private string $db = "";
    private string $dbPwd = "";
    private array $language = [];
    private bool $themeTranslation = false;
    private string $theme = "";
    private string $sitename = "";
    private string $scheme = "";
    private string $domain = "";
    private string $path = "";
    private string $replacePath = "";
    private string $emailContact = "";
    private bool $online = false;
    private string $offline = "";
    private string $min = "";
    private bool $static = false;
    private string $staticDir = '';
    private bool $accessCheck = false;
    private bool $imagCheck = false;
    private bool $ecoIndex = false;
    private bool $toWebp = false;
    private string $cache = '';
    private string $function = '';
    private string $afterGetTag = '';
    private string $facebookApiId = '';
    private string $googleAnalytics = '';
    private string $googleVerification = '';
    private string $matomoUrl = '';
    private string $matomoId = '';
    private string $plausible = '';
    private string $plausiblePath = '';
    private array $toolbox = [];
    private int $nbColor = 0;
    private string $pubHash = '';
    private string $privHash = '';
    private string $pwdHashLoop = '';
    private string $security = '';
    private string $fileCheckHack = '';
    private int $sessionExpiration = 0;
    private bool $publicAccount = false;
    private string $defaultState = '';
    private bool $mailModerate = false;
    private string $defaultAuth = '';
    private ?array $userInfo = null;
    private array $authLevel = [];
    private array $addContent = [];
    private array $tplName = [];
    private array $addMenu = [];
    private bool $btLogin = false;
    private bool $btEdit = false;
    private bool $btTop = false;
    private bool $shortcut = false;
    private array $mimeSupported = [];
    private string $maxImageSize = '';
    private int $jpgQuality = 0;
    private int $pngQuality = 0;
    private int $wepbQuality = 0;
    private string $imgGreen = '';
    private string $imgWarning = '';
    private string $imgsGreen = '';
    private string $imgsWarning = '';
    private string $imgsNum = '';
    private bool $listMediaDir = false;
    private string $mediaDir = '';
    private string $favicon = '';
    private string $icons = '';
    private bool $globalCss = false;
    private ?string $styleCss = null;
    private string $jquery = '';
    private string $jqueryUi = '';
    private string $jqueryUiCss = '';
    private ?string $tutoriel = null;
    private array $filterAuth = [];
    private array $filter = [];
    private array $translation = [];
    private array $content = [];
    private int $editkey = 0;
    private string $home = '';
    private string $root = '';

    private $id = null;
    private $title = null;
    private $description = null;
    private $image = null;
    private $tag = null;
    private $mode = null;
    private $uid = null;
    private $error = null;
    private $robots = null;
    private $robotsData = null;
    private $close = null;
    private int $page = 1;
    private int $numPp = 20;
    private array $nav = [];
    private array $tags = [];

    ///////////////////////////////////////////
    private bool $dev = false;

    private string $lang = "en";

    protected function __construct()
    {
        $this->init();
        $this->initDataFromConfig();
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
        // Serveur local ou online ? DEV || PROD
        $this->dev =
            $_SERVER['SERVER_ADDR'] == '127.0.0.1' ||
            str_contains($_SERVER['SERVER_ADDR'], '::1');

        // Fixe la langue
        $this->lang = (str_contains($_SERVER['SERVER_NAME'], 'domaine.com')) ? 'en' : 'fr';

        // Si pas de dossier média définit, on force la variable
        if (!@$GLOBALS['media_dir']) {
            $GLOBALS['media_dir'] = 'media';
        }

    }

    public function isDev(): bool
    {
        return $this->dev;
    }
    
    ////////////////////////////////////////////////////
    ////////////////////////////////////////////////////
    ////////////////////////////////////////////////////
    
    public function getDbPrefix(): string
    {
        return $this->dbPrefix;
    }
    public function setDbPrefix(string $dbPrefix):void
    {
        $this->dbPrefix = $dbPrefix;
    }
    public function getDbCharset(): string
    {
        return $this->dbCharset;
    }
    public function setDbCharset(string $dbCharset):void
    {
        $this->dbCharset = $dbCharset;
    }
    public function getTableContent(): string
    {
        return $this->tableContent;
    }
    public function setTableContent(string $tableContent):void
    {
        $this->tableContent = $tableContent;
    }
    public function getTc(): string
    {
        return $this->tc;
    }
    public function setTc(string $tc):void
    {
        $this->tc = $tc;
    }
    public function getTableMeta(): string
    {
        return $this->tableMeta;
    }
    public function setTableMeta(string $tableMeta):void
    {
        $this->tableMeta = $tableMeta;
    }
    public function getTm(): string
    {
        return $this->tm;
    }
    public function setTm(string $tm):void
    {
        $this->tm = $tm;
    }
    public function getTableTag(): string
    {
        return $this->tableTag;
    }
    public function setTableTag(string $tableTag):void
    {
        $this->tableTag = $tableTag;
    }
    public function getTt(): string
    {
        return $this->tt;
    }
    public function setTt(string $tt):void
    {
        $this->tt = $tt;
    }
    public function getTableUser(): string
    {
        return $this->tableUser;
    }
    public function setTableUser(string $tableUser):void
    {
        $this->tableUser = $tableUser;
    }
    public function getTu(): string
    {
        return $this->tu;
    }
    public function setTu(string $tu):void
    {
        $this->tu = $tu;
    }
    public function getDbServer(): string
    {
        return $this->dbServer;
    }
    public function setDbServer(string $dbServer):void
    {
        $this->dbServer = $dbServer;
    }
    public function getDbUser(): string
    {
        return $this->dbUser;
    }
    public function setDbUser(string $dbUser):void
    {
        $this->dbUser = $dbUser;
    }
    public function getDb(): string
    {
        return $this->db;
    }
    public function setDb(string $db):void
    {
        $this->db = $db;
    }
    public function getDbPwd(): string
    {
        return $this->dbPwd;
    }
    public function setDbPwd(string $dbPwd):void
    {
        $this->dbPwd = $dbPwd;
    }
    public function getLanguage(): array
    {
        return $this->language;
    }
    public function setLanguage(array $language):void
    {
        $this->language = $language;
    }
    public function getThemeTranslation(): bool
    {
        return $this->themeTranslation;
    }
    public function setThemeTranslation(bool $themeTranslation):void
    {
        $this->themeTranslation = $themeTranslation;
    }
    public function getTheme(): string
    {
        return $this->theme;
    }
    public function setTheme(string $theme):void
    {
        $this->theme = $theme;
    }
    public function getSitename(): string
    {
        return $this->sitename;
    }
    public function setSitename(string $sitename):void
    {
        $this->sitename = $sitename;
    }
    public function getScheme(): string
    {
        return $this->scheme;
    }
    public function setScheme(string $scheme):void
    {
        $this->scheme = $scheme;
    }
    public function getDomain(): string
    {
        return $this->domain;
    }
    public function setDomain(string $domain):void
    {
        $this->domain = $domain;
    }
    public function getPath(): string
    {
        return $this->path;
    }
    public function setPath(string $path):void
    {
        $this->path = $path;
    }
    public function getReplacePath(): string
    {
        return $this->replacePath;
    }
    public function setReplacePath(string $replacePath):void
    {
        $this->replacePath = $replacePath;
    }
    public function getEmailContact(): string
    {
        return $this->emailContact;
    }
    public function setEmailContact(string $emailContact):void
    {
        $this->emailContact = $emailContact;
    }
    public function isOnline(): bool
    {
        return $this->online;
    }
    public function setOnline(bool $online):void
    {
        $this->online = $online;
    }
    public function getOffline(): string
    {
        return $this->offline;
    }
    public function setOffline(string $offline):void
    {
        $this->offline = $offline;
    }
    public function getMin(): string
    {
        return $this->min;
    }
    public function setMin(string $min):void
    {
        $this->min = $min;
    }
    public function isStatic(): bool
    {
        return $this->static;
    }
    public function setStatic(bool $static):void
    {
        $this->static = $static;
    }
    public function getStaticDir(): string
    {
        return $this->staticDir;
    }
    public function setStaticDir(string $staticDir):void
    {
        $this->staticDir = $staticDir;
    }
    public function isAccessCheck(): bool
    {
        return $this->accessCheck;
    }
    public function setAccessCheck(bool $accessCheck):void
    {
        $this->accessCheck = $accessCheck;
    }
    public function isImagCheck(): bool
    {
        return $this->imagCheck;
    }
    public function setImagCheck(bool $imagCheck):void
    {
        $this->imagCheck = $imagCheck;
    }
    public function isEcoIndex(): bool
    {
        return $this->ecoIndex;
    }
    public function setEcoIndex(bool $ecoIndex):void
    {
        $this->ecoIndex = $ecoIndex;
    }
    public function isToWebp(): bool
    {
        return $this->toWebp;
    }
    public function setToWebp(bool $toWebp):void
    {
        $this->toWebp = $toWebp;
    }
    public function getCache(): string
    {
        return $this->cache;
    }
    public function setCache(string $cache):void
    {
        $this->cache = $cache;
    }
    public function getFunction(): string
    {
        return $this->function;
    }
    public function setFunction(string $function):void
    {
        $this->function = $function;
    }
    public function getAfterGetTag(): string
    {
        return $this->afterGetTag;
    }
    public function setAfterGetTag(string $afterGetTag):void
    {
        $this->afterGetTag = $afterGetTag;
    }
    public function getFacebookApiId(): string
    {
        return $this->facebookApiId;
    }
    public function setFacebookApiId(string $facebookApiId):void
    {
        $this->facebookApiId = $facebookApiId;
    }
    public function getGoogleAnalytics(): string
    {
        return $this->googleAnalytics;
    }
    public function setGoogleAnalytics(string $googleAnalytics):void
    {
        $this->googleAnalytics = $googleAnalytics;
    }
    public function getGoogleVerification(): string
    {
        return $this->googleVerification;
    }
    public function setGoogleVerification(string $googleVerification):void
    {
        $this->googleVerification = $googleVerification;
    }
    public function getMatomoUrl(): string
    {
        return $this->matomoUrl;
    }
    public function setMatomoUrl(string $matomoUrl):void
    {
        $this->matomoUrl = $matomoUrl;
    }
    public function getMatomoId(): string
    {
        return $this->matomoId;
    }
    public function setMatomoId(string $matomoId):void
    {
        $this->matomoId = $matomoId;
    }
    public function getPlausible(): string
    {
        return $this->plausible;
    }
    public function setPlausible(string $plausible):void
    {
        $this->plausible = $plausible;
    }
    public function getPlausiblePath(): string
    {
        return $this->plausiblePath;
    }
    public function setPlausiblePath(string $plausiblePath):void
    {
        $this->plausiblePath = $plausiblePath;
    }
    public function getToolbox(): array
    {
        return $this->toolbox;
    }
    public function setToolbox(array $toolbox):void
    {
        $this->toolbox = $toolbox;
    }
    public function getNbColor(): int
    {
        return $this->nbColor;
    }
    public function setNbColor(int $nbColor):void
    {
        $this->nbColor = $nbColor;
    }
    public function getPubHash(): string
    {
        return $this->pubHash;
    }
    public function setPubHash(string $pubHash):void
    {
        $this->pubHash = $pubHash;
    }
    public function getPrivHash(): string
    {
        return $this->privHash;
    }
    public function setPrivHash(string $privHash):void
    {
        $this->privHash = $privHash;
    }
    public function getPwdHashLoop(): string
    {
        return $this->pwdHashLoop;
    }
    public function setPwdHashLoop(string $pwdHashLoop):void
    {
        $this->pwdHashLoop = $pwdHashLoop;
    }
    public function getSecurity(): string
    {
        return $this->security;
    }
    public function setSecurity(string $security):void
    {
        $this->security = $security;
    }
    public function getFileCheckHack(): string
    {
        return $this->fileCheckHack;
    }
    public function setFileCheckHack(string $fileCheckHack):void
    {
        $this->fileCheckHack = $fileCheckHack;
    }
    public function getSessionExpiration(): int
    {
        return $this->sessionExpiration;
    }
    public function setSessionExpiration(int $sessionExpiration):void
    {
        $this->sessionExpiration = $sessionExpiration;
    }
    public function isPublicAccount(): bool
    {
        return $this->publicAccount;
    }
    public function setPublicAccount(bool $publicAccount):void
    {
        $this->publicAccount = $publicAccount;
    }
    public function getDefaultState(): string
    {
        return $this->defaultState;
    }
    public function setDefaultState(string $defaultState):void
    {
        $this->defaultState = $defaultState;
    }
    public function isMailModerate(): bool
    {
        return $this->mailModerate;
    }
    public function setMailModerate(bool $mailModerate):void
    {
        $this->mailModerate = $mailModerate;
    }
    public function getDefaultAuth(): string
    {
        return $this->defaultAuth;
    }
    public function setDefaultAuth(string $defaultAuth):void
    {
        $this->defaultAuth = $defaultAuth;
    }
    public function getUserInfo(): ?array
    {
        return $this->userInfo;
    }
    public function setUserInfo(?array $userInfo):void
    {
        $this->userInfo = $userInfo;
    }
    public function getAuthLevel(): array
    {
        return $this->authLevel;
    }
    public function setAuthLevel(array $authLevel):void
    {
        $this->authLevel = $authLevel;
    }
    public function getAddContent(): array
    {
        return $this->addContent;
    }
    public function setAddContent(array $addContent):void
    {
        $this->addContent = $addContent;
    }
    public function getTplName(): array
    {
        return $this->tplName;
    }
    public function setTplName(array $tplName):void
    {
        $this->tplName = $tplName;
    }
    public function getAddMenu(): array
    {
        return $this->addMenu;
    }
    public function setAddMenu(array $addMenu):void
    {
        $this->addMenu = $addMenu;
    }
    public function isBtLogin(): bool
    {
        return $this->btLogin;
    }
    public function setBtLogin(bool $btLogin):void
    {
        $this->btLogin = $btLogin;
    }
    public function isBtEdit(): bool
    {
        return $this->btEdit;
    }
    public function setBtEdit(bool $btEdit):void
    {
        $this->btEdit = $btEdit;
    }
    public function isBtTop(): bool
    {
        return $this->btTop;
    }
    public function setBtTop(bool $btTop):void
    {
        $this->btTop = $btTop;
    }
    public function isShortcut(): bool
    {
        return $this->shortcut;
    }
    public function setShortcut(bool $shortcut):void
    {
        $this->shortcut = $shortcut;
    }
    public function getMimeSupported(): array
    {
        return $this->mimeSupported;
    }
    public function setMimeSupported(array $mimeSupported):void
    {
        $this->mimeSupported = $mimeSupported;
    }
    public function getMaxImageSize(): string
    {
        return $this->maxImageSize;
    }
    public function setMaxImageSize(string $maxImageSize):void
    {
        $this->maxImageSize = $maxImageSize;
    }
    public function getJpgQuality(): int
    {
        return $this->jpgQuality;
    }
    public function setJpgQuality(int $jpgQuality):void
    {
        $this->jpgQuality = $jpgQuality;
    }
    public function getPngQuality(): int
    {
        return $this->pngQuality;
    }
    public function setPngQuality(int $pngQuality):void
    {
        $this->pngQuality = $pngQuality;
    }
    public function getWepbQuality(): int
    {
        return $this->wepbQuality;
    }
    public function setWepbQuality(int $wepbQuality):void
    {
        $this->wepbQuality = $wepbQuality;
    }
    public function getImgGreen(): string
    {
        return $this->imgGreen;
    }
    public function setImgGreen(string $imgGreen):void
    {
        $this->imgGreen = $imgGreen;
    }
    public function getImgWarning(): string
    {
        return $this->imgWarning;
    }
    public function setImgWarning(string $imgWarning):void
    {
        $this->imgWarning = $imgWarning;
    }
    public function getImgsGreen(): string
    {
        return $this->imgsGreen;
    }
    public function setImgsGreen(string $imgsGreen):void
    {
        $this->imgsGreen = $imgsGreen;
    }
    public function getImgsWarning(): string
    {
        return $this->imgsWarning;
    }
    public function setImgsWarning(string $imgsWarning):void
    {
        $this->imgsWarning = $imgsWarning;
    }
    public function isListMediaDir(): bool
    {
        return $this->listMediaDir;
    }
    public function setListMediaDir(bool $listMediaDir):void
    {
        $this->listMediaDir = $listMediaDir;
    }
    public function getMediaDir(): string
    {
        return $this->mediaDir;
    }
    public function setMediaDir(string $mediaDir):void
    {
        $this->mediaDir = $mediaDir;
    }
    public function getFavicon(): string
    {
        return $this->favicon;
    }
    public function setFavicon(string $favicon):void
    {
        $this->favicon = $favicon;
    }
    public function getIcons(): string
    {
        return $this->icons;
    }
    public function setIcons(string $icons):void
    {
        $this->icons = $icons;
    }

    public function getImgsNum(): string
    {
        return $this->imgsNum;
    }
    public function setImgsNum(string $imgsNum):void
    {
        $this->imgsNum = $imgsNum;
    }
    public function isGlobalCss(): bool
    {
        return $this->globalCss;
    }
    public function setGlobalCss(bool $globalCss):void
    {
        $this->globalCss = $globalCss;
    }
    public function getStyleCss(): ?string
    {
        return $this->styleCss;
    }
    public function setStyleCss(?string $styleCss):void
    {
        $this->styleCss = $styleCss;
    }
    public function getJquery(): string
    {
        return $this->jquery;
    }
    public function setJquery(string $jquery):void
    {
        $this->jquery = $jquery;
    }
    public function getJqueryUi(): string
    {
        return $this->jqueryUi;
    }
    public function setJqueryUi(string $jqueryUi):void
    {
        $this->jqueryUi = $jqueryUi;
    }
    public function getJqueryUiCss(): string
    {
        return $this->jqueryUiCss;
    }
    public function setJqueryUiCss(string $jqueryUiCss):void
    {
        $this->jqueryUiCss = $jqueryUiCss;
    }
    public function getTutoriel(): ?string
    {
        return $this->tutoriel;
    }
    public function setTutoriel(?string $tutoriel):void
    {
        $this->tutoriel = $tutoriel;
    }
    public function getFilterAuth(): array
    {
        return $this->filterAuth;
    }
    public function setFilterAuth(array $filterAuth):void
    {
        $this->filterAuth = $filterAuth;
    }
    public function getFilter(): array
    {
        return $this->filter;
    }
    public function setFilter(array $filter):void
    {
        $this->filter = $filter;
    }
    public function getTranslation(): array
    {
        return $this->translation;
    }
    public function setTranslation(array $translation):void
    {
        $this->translation = $translation;
    }
    public function getContent(): array
    {
        return $this->content;
    }
    public function setContent(array $content):void
    {
        $this->content = $content;
    }
    public function getEditkey(): int
    {
        return $this->editkey;
    }
    public function setEditkey(int $editkey):void
    {
        $this->editkey = $editkey;
    }
    public function increaseEditkey():void
    {
        $this->editkey++;
    }
    public function getHome(): string
    {
        return $this->home;
    }
    public function setHome(string $home):void
    {
        $this->home = $home;
    }
    public function getRoot(): string
    {
        return $this->root;
    }
    public function setRoot(string $root):void
    {
        $this->root = $root;
    }
    public function getId(): null
    {
        return $this->id;
    }
    public function setId(null $id):void
    {
        $this->id = $id;
    }
    public function getTitle(): null
    {
        return $this->title;
    }
    public function setTitle(null $title):void
    {
        $this->title = $title;
    }
    public function getDescription(): null
    {
        return $this->description;
    }
    public function setDescription(null $description):void
    {
        $this->description = $description;
    }
    public function getImage(): null
    {
        return $this->image;
    }
    public function setImage(null $image):void
    {
        $this->image = $image;
    }
    public function getTag(): null
    {
        return $this->tag;
    }
    public function setTag(null $tag):void
    {
        $this->tag = $tag;
    }
    public function getMode(): null
    {
        return $this->mode;
    }
    public function setMode(null $mode):void
    {
        $this->mode = $mode;
    }
    public function getUid(): null
    {
        return $this->uid;
    }
    public function setUid(null $uid):void
    {
        $this->uid = $uid;
    }
    public function getError(): null
    {
        return $this->error;
    }
    public function setError(null $error):void
    {
        $this->error = $error;
    }
    public function getRobots(): null
    {
        return $this->robots;
    }
    public function setRobots(null $robots):void
    {
        $this->robots = $robots;
    }
    public function getRobotsData(): null
    {
        return $this->robotsData;
    }
    public function setRobotsData(null $robotsData):void
    {
        $this->robotsData = $robotsData;
    }
    public function getClose(): null
    {
        return $this->close;
    }
    public function setClose(null $close):void
    {
        $this->close = $close;
    }
    public function getPage(): int
    {
        return $this->page;
    }
    public function setPage(int $page):void
    {
        $this->page = $page;
    }
    public function getNumPp(): int
    {
        return $this->numPp;
    }
    public function setNumPp(int $numPp):void
    {
        $this->numPp = $numPp;
    }
    public function getLang(): string
    {
        return $this->lang;
    }
    public function setLang(string $lang):void
    {
        $this->lang = $lang;
    }

    public function getNav(): array
    {
        return $this->nav;
    }
    public function setNav(array $nav):void
    {
        $this->nav = $nav;
    }
    public function getTags(): array
    {
        return $this->tags;
    }
    public function setTags(array $tags):void
    {
        $this->tags = $tags;
    }

    /////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////

    private function initDataFromConfig(): void
    {
        // Variables de la base de données
        $this->dbPrefix = $GLOBALS['db_prefix'];
        $this->dbCharset = $GLOBALS['db_charset']; // utf8 => classique || utf8mb4 => pour les emoji mac
        $this->tableContent = $GLOBALS['table_content'];
        $this->tc = $GLOBALS['tc'];
        $this->tableMeta = $GLOBALS['table_meta'];
        $this->tm = $GLOBALS['tm'];
        $this->tableTag = $GLOBALS['table_tag'];
        $this->tt = $GLOBALS['tt'];
        $this->tableUser = $GLOBALS['table_user'];
        $this->tu = $GLOBALS['tu'];
        $this->dbServer = $GLOBALS['db_server'];
        $this->dbUser = $GLOBALS['db_user'];
        $this->db = $GLOBALS['db'];
        $this->dbPwd = $GLOBALS['db_pwd'];
        // VARIABLES SITES
        $this->language = $GLOBALS['language'];
        // charge le fichier translation.php dans le dossier du theme
        $this->themeTranslation = $GLOBALS['theme_translation'];
        $this->theme = $GLOBALS['theme'];
        $this->sitename = $GLOBALS['sitename'];
        $this->scheme = $GLOBALS['scheme'];
        $this->domain = $GLOBALS['domain'];
        $this->path = $GLOBALS['path'];
        $this->replacePath = $GLOBALS['replace_path']; // "/" Pour les chemins des médias sur les sites avec dossier dans les url (filtre)
        $this->emailContact= $GLOBALS['email_contact'];
        $this->online = $GLOBALS['online']; // false => noindex, nofollow | true => index, follow
        // Heure de fermeture du site (strtotime) = 20:00-06:00 +1 day
        $this->offline = $GLOBALS['offline'];
        // Utilisation de librairie minifier
        $this->min = $GLOBALS['min'];
        // Générer une page en statique html
        $this->static = $GLOBALS['static'];
        $this->staticDir = $GLOBALS['static_dir'];
        // Vérifie l'accessibilité du contenu
        $this->accessCheck = $GLOBALS['access_check'];
        // Vérifie l'état d'écoconception des images
        $this->imagCheck = $GLOBALS['img_check'];
        // Conversion vers le webp autorisé
        $this->toWebp = $GLOBALS['towebp'];
        // Ecoindex
        $this->ecoIndex = $GLOBALS['ecoindex'];
        // Cache sur les fichiers du CMS
        $this->cache = $GLOBALS['cache'];
        // Include
        $this->function = $GLOBALS['function']; // fonction du theme
        $this->afterGetTag = $GLOBALS['after_get_tag']; // Action avant d'afficher l'header
        // https://developers.facebook.com/apps/
        $this->facebookApiId = $GLOBALS['facebook_api_id'];
        // https://analytics.google.com/analytics/web/
        $this->googleAnalytics = $GLOBALS['google_analytics'];
        // https://search.google.com/search-console
        $this->googleVerification = $GLOBALS['google_verification'];
        // Matomo
        $this->matomoUrl = $GLOBALS['matomo_url'];
        $this->matomoId = $GLOBALS['matomo_id'];
        // https://plausible.io
        $this->plausible = $GLOBALS['plausible']; // $GLOBALS['domain']
        $this->plausiblePath = $GLOBALS['plausible_path']; // /js/script.file-downloads.js
        // Toolbox
        $this->toolbox = $GLOBALS['toolbox'];
        // Nombre de couleurs custom dans la css color-x
        $this->nbColor = $GLOBALS['nbcolor'];
        // Clé hash pour les cryptages
        $this->pubHash = $GLOBALS['pub_hash'];
        $this->privHash = $GLOBALS['priv_hash'];
        // Nom de boucle de hashage du mdp
        $this->pwdHashLoop = $GLOBALS['pwd_hash_loop'];
        // Niveau de sécurité du système de login // medium : token en session | high : ajout du token dans la base (multilog impossible)
        $this->security = $GLOBALS['security'];
        // Vérifie que les fichiers uploadés ne contiennent pas des caractères susceptibles d'être des codes exécutables pour des hacks => A utiliser si compte public actif
        $this->fileCheckHack = $GLOBALS['file_check_hack'];
        // Temps d'expiration des sessions de connexion
        $this->sessionExpiration = $GLOBALS['session_expiration'];
        // Compte public autorisé
        $this->publicAccount = $GLOBALS['public_account'];
        // Statue d'activation par défaut des comptes utilisateur
        $this->defaultState = $GLOBALS['default_state']; // moderate / mail / active / deactivate
        $this->mailModerate = $GLOBALS['mail_moderate'];
        // Niveaux d'authentification par défaut des comptes utilisateur
        $this->defaultAuth = $GLOBALS['default_auth']; // add-media-public
        // Info supplémentaire sur l'utilisateur
        $this->userInfo = $GLOBALS['user_info'];
        // Niveaux d'authentification possible
        $this->authLevel = $GLOBALS['auth_level'];
        // Type de contenu ajoutable
        $this->addContent = $GLOBALS['add_content'];
        // Pour des noms de modele plus explicites dans le select
        $this->tplName = $GLOBALS['tpl_name'];
        // Type de contenu ajoutable dans le menu
        $this->addMenu = $GLOBALS['add_menu'];
        // Bouton en bas en layer
        $this->btLogin = $GLOBALS['bt_login']; // Possibilité de mettre l'emplacement ou le bouton sera injectée. ex : "footer .webmaster"
        $this->btEdit = $GLOBALS['bt_edit'];
        $this->btTop = $GLOBALS['bt_top'];
        // Raccourci clavier pour une administration rapide
        $this->shortcut = $GLOBALS['shortcut'];
        // Type mime supporté pour l'upload
        $this->mimeSupported = $GLOBALS['mime_supported'];
        // Variables tailles images
        $this->maxImageSize = $GLOBALS['max_image_size'];
        $this->jpgQuality = $GLOBALS['jpg_quality'];
        $this->pngQuality = $GLOBALS['png_quality'];
        $this->wepbQuality = $GLOBALS['webp_quality'];
        $this->imgGreen = $GLOBALS['img_green']; //ko
        $this->imgWarning = $GLOBALS['img_warning']; //ko
        $this->imgsGreen = $GLOBALS['imgs_green']; //ko
        $this->imgsWarning = $GLOBALS['imgs_warning']; //ko
        $this->imgsNum = $GLOBALS['imgs_num']; // nombre d'image max
        // On peut voir les dossiers dans la librairie des médias
        $this->listMediaDir = $GLOBALS['list_media_dir'];
        // Nom du dossier média
        $this->mediaDir = $GLOBALS['media_dir'];
        // Favicon navigateur
        $this->favicon = $GLOBALS['favicon'];
        // Librairie d'icons spécifiques à la template
        $this->icons = $GLOBALS['icons']; // $GLOBALS['scheme'].$GLOBALS['domain'].$GLOBALS['path']."api/icons/icons.min.css"
        // Utilisation de global.css ? à supprimer à termes (06/01/2021)
        $this->globalCss = $GLOBALS['global.css'];
        // Url de la css du thème
        $this->styleCss = $GLOBALS['style.css'];
        // Librairie externe
        $this->jquery = $GLOBALS['jquery']; // ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js
        $this->jqueryUi = $GLOBALS['jquery_ui'];
        $this->jqueryUiCss = $GLOBALS['jquery_ui_css']; // cupertino flick smoothness base
        // Url pour faire un lien vers un tutoriel externe
        $this->tutoriel = $GLOBALS['tutoriel'];
        // Filtre url autorisé
        $this->filterAuth = $GLOBALS['filter_auth'];
        //// Sécurité / défaut
        $this->filter = $GLOBALS['filter'];
        $this->translation = $GLOBALS['translation'];
        $this->content = $GLOBALS['content'];
        $this->editkey = $GLOBALS['editkey'];
        $this->home = $GLOBALS['home'];
        $this->root = $GLOBALS['root'];
    }
}



