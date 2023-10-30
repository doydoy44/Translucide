<?php

use Translucide\db\DataBase;
use Translucide\services\Globals;
use Translucide\services\UtilsFunctionsContent;
use Translucide\services\UtilsFunctionsLanguage;
use Translucide\services\UtilsFunctionsNavigation;

if (!$GLOBALS['domain']) {
    exit;
}

$globals = Globals::getInstance();
$dataBase = DataBase::getInstance();
$contentFn = UtilsFunctionsContent::getInstance();
$navigationFn = UtilsFunctionsNavigation::getInstance();
$languageFn = UtilsFunctionsLanguage::getInstance();
?>


<section class="mw960p mod center mtm mbl">

    <?php $contentFn->h1('titre', 'tc mbl') ?>

    <div class="w50 fl tc animation slide-left">
        <article>
            <?php $contentFn->media('media-2', '130') ?>
            <?php $contentFn->h3('titre-2', 'mbn tdn') ?>
            <?php $contentFn->txt('txt-2', 'w50 center block') ?>
            <a <?php $contentFn->href("lien-2") ?>>
                <span class="bt mts">
                    <?php $languageFn->_e("Lire plus") ?>
                </span>
            </a>
        </article>
    </div>

    <div class="w50 fl tc animation slide-right">
        <article>
            <?php $contentFn->media('media-3', '130') ?>
            <?php $contentFn->h3('titre-3', 'mbn tdn') ?>
            <?php $contentFn->txt('txt-3', 'w50 center block') ?>
            <a <?php $contentFn->href("lien-3") ?>>
                <span class="bt mts">
                    <?php $languageFn->_e("Lire plus") ?>
                </span>
            </a>
        </article>
    </div>
</section>

<section class="mw960p mod center mbl">
    <?php $contentFn->h2('titre-4', 'tc mbt mtt ptm') ?>

    <article class="pbm mtl mod">
        <div class="plm fl w40 animation slide-left">
            <a <?php $contentFn->href('lien-6') ?>>
                <?php $contentFn->media('image-6', '470') ?>
            </a>
        </div>

        <div class="pll fr w60">
            <div class="animation slide-right">
                <?php $contentFn->h3('titre-5', 'mbn') ?>
                <?php $contentFn->txt('txt-5') ?>
            </div>

            <div class="mtl animation slide-right">
                <?php $contentFn->h3('titre-6', 'mbn') ?>
                <?php $contentFn->txt('txt-6') ?>
            </div>
        </div>

    </article>

    <article class="ptm mod" style="border-top: solid 1px #eee;" <?php $contentFn->bg("bg", 'lazy') ?>>

        <div class="prs mtl ptm fl w60 tr animation slide-left">
            <?php $contentFn->h3('titre-7', 'mbn') ?>
            <?php $contentFn->txt('txt-7', ['lazy' => true]) ?>
        </div>

        <div class="prl fr w40 animation slide-right">
            <a <?php $contentFn->href('lien-map') ?>>
                <?php $contentFn->media('image-7', '470') ?>
            </a>
        </div>
    </article>
</section>

<!-- Module -->
<section class="mw960p mod center mbl">

    <?php $contentFn->h2('titre-module', 'tc') ?>

    <div class="flex">
        <!-- module pour bien identifier que ce sont les elements à dupliquer et a sauvegardé -->
        <ul id="partenaire" class="module unstyled pan auto tc">
            <?php
            // nom du module "partenaire" = id du module, et au début des id des txt() media() ...
            $module = $contentFn->module("partenaire");
foreach ($module as $key => $val) {
    ?>
                <li class="fl">
                    <div>
                        <?php $contentFn->media("partenaire-img-" . $key, ['size' => '250x250', 'lazy' => true]); ?>
                    </div>
                    <div class="pam">
                        « <?php $contentFn->txt("partenaire-text-" . $key, ["tag" => "span"]); ?> »
                    </div>
                </li>
            <?php
}
?>
        </ul>
    </div>
</section>


<!-- Event -->
<style>
    .event {
        margin-left: -2.5em;
        border-radius: 0.5em;
    }
    .event .date {
        border-radius: 100%;
        margin: 0rem 2rem;
        padding: 1rem 2rem;

        background-color: white;
        border-color: #35747f;
        color: #35747f;
    }
</style>
<section>
    <div class="mw960p mod center mbl">
        <?php $contentFn->h2('titre-event', 'tc') ?>

        <div class="fl w50 tr no-small-screen">
            <span class="editable-event" id="img-illu-event">
                <?php $contentFn->media('media-event', '425') ?>
            </span>
        </div>

        <div class="fl w50 mts">
            <?php
$sel_event = $dataBase->getConnect()->query("SELECT * FROM " . $globals->getTableContent() . " WHERE type='event' AND lang='" . $globals->getLang() . "' AND state='active' ORDER BY date_insert DESC LIMIT 0, 3");
while ($res_event = $sel_event->fetch_assoc()) {
    $content_event = json_decode($res_event['content'], true);
    ?>
                <div class="event pts pbs mtm mbm animation slide-right">

                    <article>

                        <!--Picot
						<div class="picto fl">
                        <?php
                $res_picto = ('article' == $res_event['type']) ? 'picto-actu.png' : 'picto-evenement.png';
    ?>
							<img src="/<?= $globals->getMediaDir() ?>/tpl/<?= $res_picto ?>" alt="picto <?= $res_event['type'] ?>">
						</div>-->

                        <div class="date bold bt bg-color fl up big tc">
                            <div>
                                <?= explode("-", $content_event['aaaa-mm-jj'])[2] ?>
                            </div>
                            <div>
                                <?= trim(
                                    mb_convert_encoding(
                                        date("M", strtotime($content_event['aaaa-mm-jj'])),
                                        'UTF-8',
                                        mb_list_encodings()
                                    ),
                                    "."
                                ) ?>
                            </div>
                        </div>

                        <div>
                            <h2 class="bold mod up bigger man nowrap tdn"><?= $res_event['title'] ?></h2>

                            <a href="<?= $navigationFn->make_url($res_event['url']); ?>">
                                <span class="bt bg-color">
                                    <?php $languageFn->_e("Lire") ?>
                                </span>
                            </a>
                        </div>

                    </article>

                </div>
                <?php
}
?>
        </div>

    </div>
</section>
<!-- Fin Event -->
