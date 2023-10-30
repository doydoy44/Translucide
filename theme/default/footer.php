<?php

use Translucide\db\DataBase;
use Translucide\services\Globals;
use Translucide\services\UtilsFunctionsContent;
use Translucide\services\UtilsFunctionsNavigation;

if (!$GLOBALS['domain']) {
    exit;
}

$globals = Globals::getInstance();
$dataBase = DataBase::getInstance();
$contentFn = UtilsFunctionsContent::getInstance();
$navigationFn = UtilsFunctionsNavigation::getInstance();
?>

<footer role="contentinfo">
    <section class="mw960p center grid">
        <div>
            <?php $contentFn->h3('footer-titre-contact', 'medium up') ?>
            <?php $contentFn->txt('footer-texte-contact') ?>
        </div>
        <div>
            <?php $contentFn->h3('footer-titre-actu', 'medium up') ?>
            <!--Va chercher les dernieres actu-->
            <ul class="unstyled pan">
                <?php
                $sel_actu = $dataBase->getConnect()->query("SELECT * FROM " . $globals->getTc() . " WHERE (type='article' OR type='event') AND lang='" . $globals->getLang() . "' AND state='active' ORDER BY date_insert DESC LIMIT 0, 3");
                while ($res_actu = $sel_actu->fetch_assoc()) {
                ?>
                    <li class="mbs"><i
                                class="fa-li fa fa-fw fa-<?= ($res_actu['type'] == 'article' ? 'rss' : 'calendar-empty') ?> fl mrt"></i>
                        <a href="<?= $navigationFn->make_url($res_actu['url']); ?>" class="tdn"
                           style="color: black;">
                            <?= $res_actu['title'] ?>
                        </a>
                    </li>
                <?php
                }
                ?>
            </ul>
        </div>
        <div>
            <?php $contentFn->h3('footer-titre-suivez-nous', 'medium up') ?>
            <?php $contentFn->txt('footer-texte-suivez-nous', 'color bigger') ?>
        </div>
    </section>
    <section class="mod w100 tc"><?php $contentFn->txt('webmaster') ?></section>
</footer>

<noscript>
    <style>
        /* Si pas de Javascript on affiche les contenus invisibles en attente d'animation */
        .animation {
            opacity: 1 !important;
            transform: translate3d(0, 0, 0) !important;
        }
    </style>
</noscript>
