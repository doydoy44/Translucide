<?php

use Translucide\services\UtilsFunctionsContent;

if (!$GLOBALS['domain']) {
    exit;
}

$contentFn = UtilsFunctionsContent::getInstance();
?>

<section class="mw960p mod center">
    <?php $contentFn->h1('title', 'mbn tc up color') ?>
    <?php $contentFn->h2('sstitre', 'mbn tc big color-alt') ?>
    <article class="pal ptm">
        <?php $contentFn->txt('texte') ?>
    </article>
</section>
