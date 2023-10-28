<?php

use Translucide\services\UtilsFunctionsContent;

if (!$GLOBALS['domain']) {
    exit;
}

$contentFc = UtilsFunctionsContent::getInstance();
?>

<section class="mw960p mod center">
    <?php $contentFc->h1('title', 'mbn tc up color') ?>
    <?php $contentFc->h2('sstitre', 'mbn tc big color-alt') ?>
    <article class="pal ptm">
        <?php $contentFc->txt('texte') ?>
    </article>
</section>
