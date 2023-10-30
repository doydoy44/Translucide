<?php

use Translucide\services\Globals;
use Translucide\services\UtilsFunctionsContent;
use Translucide\services\UtilsFunctionsLanguage;
use Translucide\services\UtilsFunctionsNavigation;

if (!$GLOBALS['domain']) {
    exit;
}

$globals = Globals::getInstance();
$contentFn = UtilsFunctionsContent::getInstance();
$languageFn = UtilsFunctionsLanguage::getInstance();
$navigationFn = UtilsFunctionsNavigation::getInstance();
?>

<header role="banner">
    <section class="mw960p mod center relative">
        <div>
            <nav role="navigation" aria-label="<?php $languageFn->_e("Quick access") ?>" class="inline mlt">
                <a href="#main" class="acces-rapide">
                    <?php $languageFn->_e("Skip to content") ?>
                </a>
            </nav>
            |
            <input type="checkbox" name="high-contrast"
                   id="high-contrast"<?= (@$_COOKIE['high-contrast'] ? 'checked="checked"' : '') ?>>
            <label for="high-contrast">
                <?php $languageFn->_e("Enhanced contrast") ?>
            </label>
        </div>

        <div class="center ptm tc">
            <a href="<?= $globals->getHome() ?>">
                <?php $contentFn->media('logo', '320') ?>
            </a>
        </div>

        <nav role="navigation" class="mtm mbm tc" aria-label="<?php $languageFn->_e("Browsing menu") ?>">
            <button type="button" class="burger" aria-expanded="false" aria-controls="main-navigation">
                <span class="open">Menu</span>
                <span class="close none"><?php $languageFn->_e("Close") ?></span>
            </button>

            <ul id="main-navigation" class="grid up">
                <?php
                // Extraction du menu
                foreach ($globals->getNav() as $cle => $val) {
                    // Menu sélectionné si page en cours ou article (actu)
                    if ($navigationFn->get_url() == $val['href'] or (@$res['type'] == "article" and $val['href'] == "actualites")) {
                        $selected = " selected";
                    } else {
                        $selected = "";
                    }

                    echo "<li><a href=\"" . $navigationFn->make_url($val['href'], ["domaine" => true]) . "\"" . ($val['id'] ? " id='" . $val['id'] . "'" : "") . "" . ($val['target'] ? " target='" . $val['target'] . "'" : "") . " class='" . $selected . "'" . ($selected ? ' title="' . $val['text'] . ' - ' . $languageFn->__("current page") . '"' : '') . ">" . $val['text'] . "</a></li>";
                }
?>
            </ul>
        </nav>
    </section>
</header>
