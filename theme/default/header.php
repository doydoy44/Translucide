<?php

use Translucide\services\UtilsFunctionsContent;
use Translucide\services\UtilsFunctionsLanguage;
use Translucide\services\UtilsFunctionsNavigation;

if (!$GLOBALS['domain']) {
    exit;
}
$contentFc = UtilsFunctionsContent::getInstance();
$languageFc = UtilsFunctionsLanguage::getInstance();
$navigation = UtilsFunctionsNavigation::getInstance();
?>

<header role="banner">
    <section class="mw960p mod center relative">
        <div>
            <nav role="navigation" aria-label="<?php $languageFc->_e("Quick access") ?>" class="inline mlt">
                <a href="#main" class="acces-rapide">
                    <?php $languageFc->_e("Skip to content") ?>
                </a>
            </nav>
            |
            <input type="checkbox" name="high-contrast"
                   id="high-contrast"<?= (@$_COOKIE['high-contrast'] ? 'checked="checked"' : '') ?>>
            <label for="high-contrast">
                <?php $languageFc->_e("Enhanced contrast") ?>
            </label>
        </div>

        <div class="center ptm tc">
            <a href="<?= $GLOBALS['home'] ?>">
                <?php $contentFc->media('logo', '320') ?>
            </a>
        </div>

        <nav role="navigation" class="mtm mbm tc" aria-label="<?php $languageFc->_e("Browsing menu") ?>">
            <button type="button" class="burger" aria-expanded="false" aria-controls="main-navigation">
                <span class="open">Menu</span>
                <span class="close none"><?php $languageFc->_e("Close") ?></span>
            </button>

            <ul id="main-navigation" class="grid up">
                <?php
                // Extraction du menu
                foreach ($GLOBALS['nav'] as $cle => $val) {
                    // Menu sélectionné si page en cours ou article (actu)
                    if ($navigation->get_url() == $val['href'] or (@$res['type'] == "article" and $val['href'] == "actualites")) {
                        $selected = " selected";
                    } else {
                        $selected = "";
                    }

                    echo "<li><a href=\"" . $navigation->make_url($val['href'], ["domaine" => true]) . "\"" . ($val['id'] ? " id='" . $val['id'] . "'" : "") . "" . ($val['target'] ? " target='" . $val['target'] . "'" : "") . " class='" . $selected . "'" . ($selected ? ' title="' . $val['text'] . ' - ' . $languageFc->__("current page") . '"' : '') . ">" . $val['text'] . "</a></li>";
                }
?>
            </ul>
        </nav>
    </section>
</header>
