<?php

use Translucide\db\DataBase;
use Translucide\services\Globals;
use Translucide\services\UtilsFunctionsContent;
use Translucide\services\UtilsFunctionsLanguage;
use Translucide\services\UtilsFunctionsNavigation;
use Translucide\services\UtilsFunctionsText;

if (!$GLOBALS['domain']) {
    exit;
}

$globals = Globals::getInstance();
$dataBase = DataBase::getInstance();
$contentFn = UtilsFunctionsContent::getInstance();
$textFn = UtilsFunctionsText::getInstance();
$languageFn = UtilsFunctionsLanguage::getInstance();
$navigationFn = UtilsFunctionsNavigation::getInstance();

?>

<style>
    .content article {
        border-left: 0.2em solid #35747f;
    }
</style>


<section class="mw960p mod center mtm mbl">


    <?php $contentFn->h1('title', 'mbn tc') ?>


    <nav role="navigation" class="mts tc italic">
        <?php
        // Liste les tags pour filtrer la page
        $i = 1;
        $sel_tag_list = $dataBase->getConnect()->query("SELECT distinct encode, name FROM " . $globals->getTabletag() . " WHERE zone='" . $res['url'] . "' ORDER BY ordre ASC, encode ASC");
        //echo $connect->error;

        if ($sel_tag_list->num_rows) {
            $languageFn->_e("Catégories : ");
        }

while ($res_tag_list = $sel_tag_list->fetch_assoc()) {
    if ($i > 1) {
        echo ', ';
    }
    echo '<a href="' . $navigationFn->make_url($res['url'], [$res_tag_list['encode'], 'domaine' => true]) . '" class="color tdn dash">' . $res_tag_list['name'] . '</a>';
    $i++;
}
?>
    </nav>

    <div class="mod">
        <div class="fl"><?php $contentFn->media('img', '130') ?></div>
        <div class="fl mlm"><?php $contentFn->txt('description') ?></div>
    </div>


    <?php
    // Si on n'a pas les droits d'édition des articles on affiche uniquement ceux actifs
    if (!@$_SESSION['auth']['edit-article']) {
        $sql_state = "AND state='active'";
    } else {
        $sql_state = "";
    }

    // Navigation par page
    if (isset($globals->getFilter()['page'])) {
        $globals->setPage((int)$globals->getFilter()['page']);
    } else {
        $globals->setPage(1);
    }

    $start = ($globals->getPage() * $globals->getNumPp()) -$globals->getNumPp();


// Construction de la requete
$sql = "SELECT SQL_CALC_FOUND_ROWS " . $globals->getTc()  . ".id, " . $globals->getTc()  . ".* FROM " . $globals->getTc();

// Si filtre tag
if (isset($tag)) {
    $sql .= " RIGHT JOIN " . $globals->getTt()  . "
	ON
	(
		" . $globals->getTt() . ".id = " . $globals->getTc()  . ".id AND
		" . $globals->getTt() . ".zone = 'actualites' AND
		" . $globals->getTt() . ".encode = '" . $tag . "'
	)";
}

$sql .= " WHERE (" . $globals->getTc()  . ".type='article') AND " . $globals->getTc()  . ".lang='" . $globals->getLang() . "' " . $sql_state . "
	ORDER BY " . $globals->getTc()  . ".date_insert DESC
	LIMIT " . $start . ", " . $globals->getNumPp();

$sel_fiche = $dataBase->getConnect()->query($sql);

$num_total = $dataBase->getConnect()->query("SELECT FOUND_ROWS()")->fetch_row()[0]; // Nombre total de fiche

while ($res_fiche = $sel_fiche->fetch_assoc()) {
    // Affichage du message pour dire si l'article est invisible ou pas
    if ($res_fiche['state'] != "active") {
        $state = " <span class='deactivate pat'>" . $languageFn->__("Article d&eacute;sactiv&eacute;") . "</span>";
    } else {
        $state = "";
    }

    $content_fiche = json_decode((string)$res_fiche['content'], true);

    $date = explode("-", explode(" ", $res_fiche['date_insert'])[0]);
    ?>
        <article class="mod plm mrm mtl mbm">

            <div class="date fl prm up bold big tc">
                <div class="bigger"><?= $date[2] ?></div>
                <div><?= trim(mb_convert_encoding(date("M", strtotime($res_fiche['date_insert'])), 'UTF-8', mb_list_encodings()), ".") ?></div>
            </div>

            <h2 class="mts up bigger"><a href="<?= $navigationFn->make_url($res_fiche['url'], ["domaine" => true]); ?>"
                                         class="tdn"><?= $res_fiche['title'] ?></a><?= $state ?></h2>

            <?php if (isset($content_fiche['texte'])) {
                echo $textFn->word_cut($content_fiche['texte'], '350') . "...";
            } ?>

            <div class="fr mtm">
                <a href="<?= $navigationFn->make_url($res_fiche['url'], ["domaine" => true]); ?>"
                   class="bt bg-color bold">
                    <?php $languageFn->_e("Lire l'article") ?>
                </a>
            </div>

        </article>
        <?php
}

$navigationFn->page($num_total, $page);

?>
</section>
