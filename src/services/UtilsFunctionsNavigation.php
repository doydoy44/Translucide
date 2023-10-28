<?php

namespace Translucide\services;

use Exception;

class UtilsFunctionsNavigation
{
    /**
     * The Singleton's instance is stored in a static field. This field is an
     * array, because we'll allow our Singleton to have subclasses. Each item in
     * this array will be an instance of a specific Singleton's subclass. You'll
     * see how this works in a moment.
     */
    private static array $instances = [];


    protected function __construct()
    {
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

    public static function getInstance(): UtilsFunctionsNavigation
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new UtilsFunctionsNavigation();
        }

        return self::$instances[$cls];
    }


    // Trim tous type d'espaces
    public function trimer($value)
    {
        return trim(html_entity_decode($value), " \t\n\r\0\x0B\xC2\xA0");
    }

    // Nettoie et encode les mots
    public function encode($value, $separator = "-", $pass = null)
    {
        if (!is_null($value)) {
            // Tableau des special chars PHP 7.2
            //$from = str_split(utf8_decode("ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñß@\’\"'_-&()=/*+$!:;,.\²~#?§µ%£°{[|`^]}¤€<>")); // SUPP 20/03/2023
            $from = str_split(mb_convert_encoding("ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñß@\’\"'_-&()=/*+$!:;,.\²~#?§µ%£°{[|`^]}¤€<>", 'ISO-8859-1', 'UTF-8'));
            $to = str_split("aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynnba                                         ");

            // Si on doit laisser certains caractères
            if (isset($pass) and @count($pass)) {
                foreach ($pass as $char) {
                    $strpos = strpos(implode($from), $char);
                    $from[$strpos] = "";
                    $to[$strpos] = "";
                }
            }

            //$value = strtolower(strtr(utf8_decode($value), implode($from), implode($to))); // Supp les caractères indésirables// SUPP 20/03/2023
            $value = strtolower(strtr(mb_convert_encoding($value, 'ISO-8859-1', 'UTF-8'), implode($from), implode($to))); // Supp les caractères indésirables


            //		$value = trimer($value, " \t\n\r\0\x0B\xC2\xA0"); // Supprime les espaces et espaces insecable de début et fin
            //		$value = preg_replace('/\t+/', $separator, $value); // Remplace les tabulations
            //		$value = preg_replace('/ {2,}/', $separator, $value); // Remplace les double espaces
            //		$value = preg_replace('/ /', $separator, $value); // Remplace les espaces simple
            //		//$value = preg_replace('/\xa0/', $separator, $value); // Remplace les espaces insecable [\xc2\xa0]

            $value = $this->trimer($value, " \t\n\r\0\x0B\xC2\xA0"); // Supprime les espaces et espaces insecable de début et fin
            $value = preg_replace('/\t+/', $separator, $value); // Remplace les tabulations
            $value = preg_replace('/ {2,}/', $separator, $value); // Remplace les double espaces
            $value = preg_replace('/ /', $separator, $value); // Remplace les espaces simple
            //$value = preg_replace('/\xa0/', $separator, $value); // Remplace les espaces insecable [\xc2\xa0]
        }

        return $value;
    }

    // Récupère l'url rewriter
    public function get_url($url_source = null)
    {
        // Si pas d'url forcé on donne l'url en cours complète
        if (!$url_source) {
            $url_source = (@$_SERVER['REQUEST_SCHEME'] ? $_SERVER['REQUEST_SCHEME'] : "http") . "://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        }

        // Parse l'url pour ne garder que la partie rewrite sans le chemin de base du site
        $parse_url = parse_url($url_source);
        $path = preg_replace("/^" . addcslashes($GLOBALS['path'], "/") . "*/", "", $parse_url['path']);

        // Si l'url est vide : url = index
        if (!$this->encode($path)) {
            $url = (isset($GLOBALS['static']) ? 'index' : 'home');
        } // @todo mettre que 'index' à terme 13/07/2020
        else {
            // Si il y a des filtres/page dans l'url
            if (strstr($parse_url['path'], "/") or strstr($parse_url['path'], "page_")) {
                $explode_path = explode("/", $path);

                // Home si le premier element est la nav par page
                if (strstr($explode_path[0], "page_")) {
                    $url = (isset($GLOBALS['static']) ? 'index' : 'home');
                } // @todo mettre que 'index' à terme
                else {
                    $url = $explode_path[0]; // Url raçine
                    unset($explode_path[0]); // Supp la racine des filtres si dossier
                }

                foreach ($explode_path as $cle => $dir) {
                    $dir = urldecode($dir); // Pour supprimer les %20 ..

                    $explode_dir = explode("_", $dir);

                    if ($explode_dir[0]) {
                        $GLOBALS['filter'][$this->encode($explode_dir[0], "-", [".", "'"])] =
                            $this->encode(preg_replace("/^" . $explode_dir[0] . "_/", "", $dir), "-", [".", "_", "'", "@"]);
                    }
                }
            } else {
                $url = $path;
            }
        }

        return $this->encode($url);
    }

    // Retourne l'url rewriter
    public function make_url($url, $filter = [])
    {
        $dir = "";

        if (is_array($filter)) {
            // Force le domaine sur la variable défini
            if (isset($filter['domaine'])) {
                $domaine = $filter['domaine'];
            }
            unset($filter['domaine']);

            // Force le chemin absolu
            if (isset($filter['absolu'])) {
                $absolu = $filter['absolu'];
            }
            unset($filter['absolu']);

            // Création des dossier dans l'url en fonction des filtres
            foreach ($filter as $cle => $val) {
                if ($cle == "page" and $val == 1) {
                    unset($filter['page']);
                } // Si Page == 1 on ne l'affiche pas dans l'url
                elseif ($val) {
                    $dir .= "/" . (($cle and $cle != $val) ? $this->encode($cle) . "_" : "") . $this->encode($val, "-", [".", "'", "@", "_"]);
                }
            }
        }

        if ($url == "home" or $url == "index") { // @todo A terme supprimer "home" car on utilise "index" 13/07/2020
            $url = $GLOBALS['path'];

            if (isset($domaine)) {
                $url = ($domaine === true ? $GLOBALS['home'] : $domaine);
            }
        } elseif (preg_match("/(http|https):\/\//", $url)) { // Si url externe on retourne l'url directement
            return $url;
        } else {
            $url = $this->encode($url, "-", ["#", "/"]);

            if (isset($domaine)) {
                $url = ($domaine === true ? $GLOBALS['home'] : $domaine) . ltrim($url, "/");
            }
        }

        // Si filtre ou page
        if ($dir) {
            $url = trim($url, "/") . $dir;
        }

        // Si on demande le chemin absolu
        if (isset($absolu)) {
            $url = $GLOBALS['path'] . $url;
        }

        return $url;
    }

    // Navigation par page
    public function page($num_total, $page, $filter = []): void
    {
        global $num_pp, $res;

        // S'il y a une valeur pour le filter mais que != tableau => c'est que l'on veut afficher toutes les pages
        if (!is_array($filter)) {
            $filter = ["full" => $filter];
        }

        // Si navigation par page
        if ($num_total > $num_pp) {
            ?>
            <nav role="navigation"<?= (isset($filter['aria-label']) ? ' aria-label="' . htmlspecialchars($filter['aria-label']) . '"' : '') ?>>
                <ul class="page unstyled inbl man pan">
                    <?php

                    $num_page = ceil($num_total / $num_pp);

            // Page 1
            ?>
                    <li class="fl mrs mbs"><a
                                href="<?= $this->make_url($res['url'], array_merge($GLOBALS['filter'], ["page" => "1", "domaine" => true])) ?>"
                                class="bt<?= ($page == 1 ? ' selected' : ''); ?>"<?= ($page == 1 ? ' aria-current="page"' : '') ?>>1</a>
                    </li><?php

            if ($num_page > 10 and $page >= 10 and !isset($filter['full'])) { // + de 10 page
                ?>
                        <li class="fl mrs mtt">...</li><?php

                for ($i = ($page - 1); $i <= ($page + 1) and $i < $num_page; $i++) { ?>
                            <li class="fl mrs mbs"><a
                                        href="<?= $this->make_url($res['url'], array_merge($GLOBALS['filter'], ["page" => $i, "domaine" => true])) ?>"
                                        class="bt<?= ($page == $i ? ' selected' : ''); ?>"<?= ($page == $i ? ' aria-current="page"' : '') ?>><?= $i ?></a>
                            </li>
                        <?php }
                } else { // - de 10 page
                    for ($i = 2; $i <= (isset($filter['full']) ? $num_page : 10) and $i < $num_page; $i++) { ?>
                            <li class="fl mrs mbs"><a
                                        href="<?= $this->make_url($res['url'], array_merge($GLOBALS['filter'], ["page" => $i, "domaine" => true])) ?>"
                                        class="bt<?= ($page == $i ? ' selected' : ''); ?>"<?= ($page == $i ? ' aria-current="page"' : '') ?>><?= $i ?></a>
                            </li>
                        <?php }
                    }

            if ($num_page > 10 and $page < ($num_page - 2) and !isset($filter['full'])) { ?>
                        <li class="fl mrs">...</li><?php }

            // Page final
            ?>
                    <li class="fl mrs mbs"><a
                                href="<?= $this->make_url($res['url'], array_merge($GLOBALS['filter'], ['page' => $num_page, "domaine" => true])) ?>"
                                class="bt<?= ($page == $num_page ? ' selected' : ''); ?>"<?= ($page == $num_page ? ' aria-current="page"' : '') ?>><?= $num_page ?></a>
                    </li><?php

            ?>
                </ul>
            </nav>
            <?php
        }
    }
}
