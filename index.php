<?php

use Translucide\db\DataBase;
use Translucide\services\Globals;
use Translucide\services\UtilsFunctionsBenchMark;
use Translucide\services\UtilsFunctionsLanguage;
use Translucide\services\UtilsFunctionsNavigation;

require './vendor/autoload.php';

@include_once("config.php"); // Variables
include_once("api/function.php"); // Fonctions
include_once("api/db.php"); // Connexion à la db

$globals = Globals::getInstance();
$dataBase = DataBase::getInstance();
$languageFn = UtilsFunctionsLanguage::getInstance();
$navigationFn = UtilsFunctionsNavigation::getInstance();
$benchmarkFn = UtilsFunctionsBenchMark::getInstance();

// Pour éviter le duplicate avec index.php
if (stristr($_SERVER['REQUEST_URI'], 'index.php')) {
    header("Status: 301 Moved Permanently");
    header("Location: " . str_ireplace("index.php", "", $_SERVER['REQUEST_URI']));
    exit;
}


// On ajax une page ?
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    $ajax = true;
} else {
    $ajax = false;
}

// Sélectionne la langue
$globals->setLang($languageFn->get_lang());

$languageFn->load_translation('api'); // Chargement des traductions du système
if ($globals->getThemeTranslation()) {
    $languageFn->load_translation('theme');
}// Chargement des traductions du theme


/********** CONTENU **********/

// Permalien de la page
$get_url = $dataBase->getConnect()->real_escape_string($navigationFn->get_url());

$close = false;
// Check si pas admin & horaire de fermeture du site
if (!@$_SESSION['auth']['edit-page'] and $globals->getOffline() !== null) {
    $offline = explode('-', $globals->getOffline());

    // Si l'heure actuelle est dans la tranche de fermeture
    if (time() > strtotime($offline[0]) and time() < strtotime($offline[1])) {
        $close = true;
        $res['state'] = "deactivate";
        $res['url'] = $get_url;
    }
}

// On récupère les données de la page
if (!$close) {
    $sel = $dataBase->getConnect()->query("SELECT * FROM " . $globals->getTableContent() . " WHERE url='" . $get_url . "' AND lang='" . $globals->getLang() . "' LIMIT 1");
    if ($dataBase->getConnect()->error) {
        header($_SERVER['SERVER_PROTOCOL'] . " 503 Service Unavailable");
        exit($dataBase->getConnect()->error);
    }

    $res = $sel->fetch_assoc(); // On récupère les données de la page
}


/********** TAGS **********/
$filter = $globals->getFilter();
// Construction de l'ajout du contenu tag/cat, si filter et la racine de l'url pas dans les filtres autorisés
if (count($filter) > 0 and !in_array($get_url, $globals->getFilterAuth())) {
    $filter_one = array_keys($filter)[0];

    // Si tag et pas uniquement home + filtre autorisé
    if (isset($filter[$filter_one]) and !in_array($filter_one, $globals->getFilterAuth())) {
        $tag = $navigationFn->encode($filter[array_keys($filter)[0]]);

        // On rapatrie les infos du tag
        $sel_tag_info = $dataBase->getConnect()->query("SELECT * FROM " . $globals->getTableMeta() . " WHERE type='tag-info' AND cle='" . $tag . "' LIMIT 1");
        $res_tag_info = $sel_tag_info->fetch_assoc();

        // Il n'y a pas d'infos sur le tag
        if (!@$res_tag_info['val']) {
            // On rapatrie simplement le nom du tag, pour le fil d'ariane par exemple
            $sel_tag = $dataBase->getConnect()->query("SELECT * FROM " . $globals->getTableTag() . " WHERE zone='" . @$res['url'] . "' AND encode='" . $tag . "' LIMIT 1");
            $res_tag = $sel_tag->fetch_assoc();

            // Si tag n'existe pas => page 404
            if (!@$res_tag['name']) {
                $res = null;
            }
        }
    }
}


/********** ACTION après la récupération des données du tag **********/
if ($globals->getAfterGetTag()) {
    include_once($globals->getRoot() . $globals->getAfterGetTag());
}


/********** UNE PAGE EXISTE **********/
$robots_data = '';

if ($res) {
    // Si on veut que le CMS soit en https dans la config, on vérifie le statut d'origine de l'url
    if (str_contains($globals->getScheme(), 'https')) {
        //!\\ @TODO voir BUG redirection infini à cause du script_uri et request_scheme qui ne son pas en https mais en http alors que dans l'url c'est bien https ! => voir la redirection faite automatiquement par le navigateur en cas de http pour redir vers https (HTTP_X_FORWARDED_PROTO // REDIRECT_HTTPS)
        // $_SERVER['HTTPS'] = on ? => ok pour poser le https ?

        // Verif si https dans l'url
        if (
            strpos(@$_SERVER['SCRIPT_URI'], 'https') !== false or
            $_SERVER['REQUEST_SCHEME'] == 'https' or
            isset($_SERVER['HTTPS'])
        ) {
            $http = "https://";
        } else {
            $http = "http://";
        }
    } else {
        $http = $globals->getScheme();
    }


    // On vérifie l'url pour eviter les duplicates : si erreur = redirection
    if ($http . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] != $navigationFn->make_url($res['url'], array_merge($globals->getFilter(), ["domaine" => true]))) {
        header($_SERVER['SERVER_PROTOCOL'] . " 301 Moved Permanently");
        header("location: " . $navigationFn->make_url($res['url'], array_merge($globals->getFilter(), ["domaine" => true])));
        exit;
    }


    $robots_data = @$res['robots']; // paramètre des robots propriétaire à la page courante


    if ($res['state'] != "active") { // Page non activé
        // Si pas admin on affiche page en construction
        if (!@$_SESSION['auth']['edit-' . $res['type']]) {
            // On regarde si une template 503 est définie
            $sel_503 = $dataBase->getConnect()->query("SELECT * FROM " . $globals->getTableContent() . " WHERE url='503' AND lang='" . $globals->getLang() . "' AND state='active' LIMIT 1");
            $res_503 = $sel_503->fetch_assoc();
            if (isset($res_503['id'])) {
                $res = $res_503;
            } else {
                $res = null;
                $res['state'] = 'deactivate';
                if (@$close) {
                    $res['title'] = $msg = $languageFn->__("Site closing time");
                } else {
                    $res['title'] = $msg = $languageFn->__("Under Construction");
                }
            }

            header($_SERVER['SERVER_PROTOCOL'] . " 503 Service Unavailable");
        }

        $robots = "noindex, follow";
    } else { // Si la page est active elle est référençable (on utilise la config ou les param de la page)
        if (!$globals->isOnline()) {
            $robots = 'noindex, nofollow';
        }// Offline
        elseif (@$res['robots']) {
            $robots = $robots_data;
        } // Online + paramètre déterminé
        else {
            $robots = 'index, follow';
        } // Online + pas de paramètre
    }
} else {
    /********** PAS DE PAGE EXISTANTE **********/
    // On regarde si une template 404 est définie
    $sel = $dataBase->getConnect()->query("SELECT * FROM " . $globals->getTableContent() . " WHERE url='404' AND lang='" . $globals->getLang() . "' AND state='active' LIMIT 1");
    $res = $sel->fetch_assoc();

    // Si pas de template
    if (!$res) {
        $res['title'] = $msg = $languageFn->__("404 error : page not found");
        $res['description'] = "";
    }

    // On force un header 404
    header($_SERVER['SERVER_PROTOCOL'] . " 404 Not Found");

    $robots = "noindex, follow";
}



/********** ID DE LA PAGE **********/
if (isset($res['id'])) {
    $globals->setId($res['id']);
} else {
    $globals->setId(null);
}


/********** LES CONTENUS **********/
if (isset($res['content']) and $res['content'] != '') {
    $globals->setContent(json_decode($res['content'], true));
} else {
    $globals->setContent([]);
}

// Si pas de titre/title H1, on met le title de la page/produit
$content = $globals->getContent();
if (!isset($content['title'])) {
    $content['title'] = $res['title'];
    $globals->setContent($content);
}

/********** METAS HEAD **********/

// Title de la page
$title = $res['title'];


// SI TAG ajout au meta
if (isset($res_tag_info['val'])) { // Il y a des infos sur le tag
    // Récupère les informations des tags et écrase celle du contenu
    $GLOBALS['content'] = @array_merge($GLOBALS['content'], json_decode($res_tag_info['val'], true));

    // Ecrase les données meta
    if (isset($GLOBALS['content']['title'])) {
        $title .= ' - ' .  $GLOBALS['content']['title'];
    }
    if (isset($GLOBALS['content']['description'])) {
        $res['description'] = htmlspecialchars(strip_tags($GLOBALS['content']['description'], ENT_COMPAT));
    }
    if (isset($GLOBALS['content']['img'])) {
        $GLOBALS['content']['og-image'] =  $GLOBALS['content']['img'];
    }
} elseif (isset($res_tag['name'])) { // S'il y a juste le nom du tag
    $GLOBALS['content']['title'] = $res_tag['name'];
    $title .= ' - ' . $res_tag['name'];
    $res['description'] =  $GLOBALS['content']['description'] = "";
}

// Si filtre dans les filtres autorisés, on ajoute les filtres à l'URL
if ($globals->getFilter()) {
    foreach ($globals->getFilter() as $cle => $val) {
        if (in_array($cle, $globals->getFilterAuth()) and $cle != 'page') {
            $title .= ' - ' . $languageFn->__($cle) . ' ' . $val;
        }
    }
}


// Si filtre page dans l'url, on enrichie le title
if (isset($globals->getFilter()['page'])) {
    $title .= ' - ' . $languageFn->__('Page') . ' ' . (int)$globals->getFilter()['page'];
}



// SI CONTENU

// Si un NOM DE SITE est défini et pas déjà dans le title
if ($globals->getSitename() !== null and substr($title, -strlen($globals->getSitename())) !== $globals->getSitename()) {
    $title .= ' - ' . $globals->getSitename();
}

// Description
$description = (isset($res['description']) ? htmlspecialchars(strip_tags($res['description']), ENT_COMPAT) : "");

// Image pour les réseaux sociaux
if (isset($GLOBALS['content']['og-image'])) {
    $image =  $GLOBALS['content']['og-image'];
} elseif (isset($GLOBALS['content']['alaune'])) {
    $image =  $GLOBALS['content']['alaune'];
} elseif (isset($GLOBALS['content']['visuel']) or isset($GLOBALS['content']['visuel-1'])) {
    if (isset($GLOBALS['content']['visuel'])) {
        $image =  $GLOBALS['content']['visuel'];
    } else {
        $image =  $GLOBALS['content']['visuel-1'];
    }

    // Si image plus grande (zoom)
    $parse_url = parse_url($image);
    if (isset($parse_url['query'])) {
        parse_str($parse_url['query'], $get);
        if (isset($get['zoom'])) {
            $image = $get['zoom'];
        }
    }
}
// Si l'image n'est pas une url mais un fichier local, on ajoute le domaine du site
if (isset($image) and !@parse_url($image)['scheme']) {
    $image = $globals->getHome() . $image;
}



// Si pas ajax on charge toute la page
if(!$ajax) {
    /********** RÉCUPÉRATION DES DONNÉES META : NAV | HEADER |FOOTER **********/

    $sel_meta = $dataBase->getConnect()->query("SELECT * FROM " . $globals->getTm() . " WHERE type IN ('nav','header','footer') AND cle='" . $globals->getLang() . "' LIMIT 3");
    while ($res_meta = $sel_meta->fetch_assoc()) {
        if (isset($res_meta['val'])) {
            // Si menu de navigation
            if ($res_meta['type'] == 'nav') {
                $globals->setNav(json_decode($res_meta['val'], true));
            } // Si contenu du header ou footer
            else {
                $globals->setContent(@array_merge($globals->getContent(), json_decode($res_meta['val'], true)));
            }
        }
    }

    // Si pas de nav
    if (!$globals->getNav() !== null) {
        $globals->setNav([]);
    }

    // Protection contre le Clickjacking
    header('X-Frame-Options: SAMEORIGIN');

    // Encodage du html
    header('Content-type: text/html; charset=UTF-8');

    ?><!DOCTYPE html>
<html lang="<?= $globals->getLang(); ?>" itemscope itemtype="http://schema.org/WebPage">
<head>

    <meta charset="utf-8">

    <title><?= strip_tags($title); ?></title>
    <?php if ($description) { ?>
        <meta name="description" content="<?= $description; ?>"><?php } ?>

    <meta name="robots" content="<?= $robots; ?>">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta property="og:title" content="<?= strip_tags($title); ?>">
    <meta property="og:type" content="website">
    <?php if (isset($res['url'])) { ?>
        <meta property="og:url"
              content="<?= $navigationFn->make_url($res['url'], array_merge($globals->getFilter(), ["domaine" => true])) ?>">
        <link rel="canonical" href="<?= $navigationFn->make_url($res['url'], array_merge($globals->getFilter(), ["domaine" => true])) ?>">
    <?php } ?>
    <?php if ($description) { ?>
        <meta property="og:description" content="<?= $description; ?>"><?php } ?>
    <?php if ($image) { ?>
        <meta property="og:image" content="<?= $image; ?>"><?php } ?>
    <meta property="article:published_time" content="<?= date(DATE_ISO8601, strtotime(@$res['date_insert'])); ?>">

    <?php if ($globals->getFacebookApiId()) { ?>
        <meta property="fb:app_id" content="<?= $globals->getFacebookApiId; ?>"><?php } ?>
    <?php if ($globals->getGoogleVerification()) { ?>
        <meta name="google-site-verification" content="<?= $globals->getGoogleVerification(); ?>" /><?php } ?>


    <?php if (!$globals->isGlobalCss()) { ?>
        <link rel="stylesheet"
              href="<?= $globals->getPath() ?>api/global<?= $globals->getMin() ?>.css?<?= $globals->getCache() ?>"><?php } ?>

    <link rel="stylesheet"
          href="<?= ($globals->getStyleCss() ?? $globals->getPath() . 'theme/' . $globals->getTheme() . ($globals->getTheme() ? "/" : "") . 'style' . $globals->getMin() . '.css?' . $globals->getCache()) ?>">

    <?php if ($globals->getIcons()) { ?>
        <link rel="stylesheet" href="<?= $globals->getIcons ?>"><?php } else { ?>
        <style>
            @font-face {
                font-family: 'FontAwesome';
                src: url('<?=$globals->getPath()?>api/icons/icons.eot?<?=$globals->getCache()?>');
                src: url('<?=$globals->getPath()?>api/icons/icons.eot?<?=$globals->getCache()?>#iefix') format('embedded-opentype'),
                url('<?=$globals->getPath()?>api/icons/icons.woff2?<?=$globals->getCache()?>') format('woff2'),
                url('<?=$globals->getPath()?>api/icons/icons.woff?<?=$globals->getCache()?>') format('woff'),
                url('<?=$globals->getPath()?>api/icons/icons.ttf?<?=$globals->getCache()?>') format('truetype'),
                url('<?=$globals->getPath()?>api/icons/icons.svg?<?=$globals->getCache()?>#icons') format('svg');
                font-weight: normal;
                font-style: normal;
            }
        </style>
    <?php } ?>

    <?php if ($globals->getFavicon()) { ?>
        <link rel="shortcut icon" type="image/x-icon" href="<?= $globals->getFavicon() ?>"><?php } ?>


    <script src="<?= $globals->getJquery() ?>"></script>

    <script src="<?= $globals->getPath() ?>api/lucide.init<?= $globals->getMin() ?>.js?<?= $globals->getCache() ?>"></script>


    <?php if ($globals->getPlausible()) { ?>
        <script async defer data-domain="<?= $globals->getPlausible() ?>"
                src="https://plausible.io<?= ($globals->getPlausiblePath() ? @$globals->getPlausiblePath() : '/js/plausible.js') ?>"></script>
    <?php } ?>

    <?php if ($globals->getGoogleAnalytics()) { ?>
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?= $globals->getGoogleAnalytics(); ?>"></script>
    <?php } ?>


    <script>

        <?php if($globals->getGoogleAnalytics() and @$_COOKIE['analytics'] == "activer") { ?>
        // Google Analytics
        window.dataLayer = window.dataLayer || [];

        function gtag() {
          dataLayer.push(arguments);
        }

        gtag('js', new Date());
        gtag('config', '<?=$globals->getGoogleAnalytics();?>');
        <?php }


        if($globals->getMatomoUrl() and @$_COOKIE['analytics'] == "activer") { ?>
        // Matomo
        var _paq = window._paq = window._paq || [];
        _paq.push(['trackPageView']);
        _paq.push(['enableLinkTracking']);
        (function () {
          var u = "//{<?=$globals->getMatomoUrl();?>}/";
          _paq.push(['setTrackerUrl', u + 'matomo.php']);
          _paq.push(['setSiteId', {<?=$globals->getMatomoId();?>}]);
          var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
          g.type = 'text/javascript';
          g.async = true;
          g.src = u + 'matomo.js';
          s.parentNode.insertBefore(g, s);
        })();
        <?php }


        if($globals->getFacebookApiId()) { ?>
        // Facebook
        (function (d, s, id) {
          var js, fjs = d.getElementsByTagName(s)[0];
          if (d.getElementById(id)) {
            return;
          }
          js = d.createElement(s);
          js.id = id;
          js.src = "//connect.facebook.net/fr_FR/sdk.js#xfbml=1&version=v2.7&cookie=true&appId=<?=$globals->getFacebookApiId();?>";
          fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
        <?php }


        if(isset($_COOKIE['autoload_edit']) and $_SESSION['auth']['edit-' . $res['type']]) {?>
        // Si demande l'autoload du mode édition et si admin
        $(function () {
          // Supprime le cookie
          set_cookie("autoload_edit", "", "");

          // lance l'édition
          edit_launcher();

          // Efface le bouton d'édition
          $("a.bt.fixed.edit").fadeOut();
        });
        <?php
        // Supprime le cookie qui demande de charger automatiquement l'admin
        @setcookie("autoload_edit", "", time() - 3600, $globals->getPath(), $globals->getDomain());
        }?>


        // Variables
        id = "<?=$globals->getId()?>";
        state = "<?=@$res['state']?>";
        title = "<?=addslashes(strip_tags(trim(str_replace(["\r", "\n"], '', $globals->getContent()['title']))));?>";
        permalink = "<?=@$res['url']?>";
        type = "<?=@$res['type']?>";
        tpl = "<?=@$res['tpl']?>";
        tag = "<?=$navigationFn->encode(@$tag)?>";
        path = "<?=$globals->getPath()?>";
        theme = "<?=$globals->getTheme()?>";
        media_dir = "<?=($globals->getMediaDir() !== "" ? $globals->getMediaDir() : 'media')?>";
        date_update = "<?=@$res['date_update']?>";
        <?=($globals->getLangAlt() !== null ? 'lang_alt = "' . addslashes($languageFn->getLangAlt()) . '";' : '')?>
        <?=($globals->getSitename() !== null ? 'sitename = "' . addslashes($globals->getSitename()) . '";' : '')?>
        <?=($globals->isBtLogin() ? 'bt_login = ' . (!$globals->isBtLogin() ? '"' . $globals->isBtLogin() . '"' : 'true') . ';' : '')?>
        <?=($globals->isBtEdit() ? 'bt_edit = true;' : '')?>
        <?=($globals->isBtTop() ? 'bt_top = true;' : '')?>
        <?=($globals->isShortcut() ? 'shortcut = true;' : '')?>
        <?=($globals->isDev() ? 'dev = true;' : '')?>

    </script>

</head>
<body<?= ($robots_data ? ' data-robots="' . $robots_data . '"' : '') . (@$_COOKIE['high-contrast'] ? ' class="hc"' : '') ?>>
<?php


include_once('theme/' . $globals->getTheme() . ($globals->getTheme() ? '/' : '') . 'header.php');


    echo '<main id="main" role="main" tabindex="-1" class="content' . (isset($res['tpl']) ? ' tpl-' . $navigationFn->encode($res['tpl']) : '') . '">';
}


if (isset($res['tpl'])) { // On a une page
    include('theme/' . $globals->getTheme() . ($globals->getTheme() ? '/' : '') . 'tpl/' . $res['tpl'] . '.php'); // On charge la template du thème pour afficher le contenu
} else { // Pas de contenu a chargé
    echo '<div class="pal tc">' . $msg . '</div>';
}


// Si pas ajax on charge toute la page
if (!$ajax) {
    echo '</main>';

    include_once('theme/' . $globals->getTheme() . ($globals->getTheme() ? '/' : '') . '/footer.php');
    ?>

    <div class="responsive-overlay"></div>

    <script>console.log("<?=$benchmarkFn->benchmark()?>")</script>

</body>
</html>
<?php
} else {
    ?>
    <script>console.log("<?=$benchmarkFn->benchmark()?>")</script>
<?php
}

$dataBase->getConnect()->close();
