<?php

/********** FONCTION DU THEME **********/
if (isset($GLOBALS['function']) and $GLOBALS['function'] != "") {
    include_once($_SERVER["DOCUMENT_ROOT"] . $GLOBALS['path'] . "theme/" . $GLOBALS['theme'] . ($GLOBALS['theme'] ? "/" : "") . $GLOBALS['function']);
}
