<?php

namespace Translucide\services;

use Exception;
use Translucide\db\DataBase;

class UtilsFunctionsContent
{
    /**
     * The Singleton's instance is stored in a static field. This field is an
     * array, because we'll allow our Singleton to have subclasses. Each item in
     * this array will be an instance of a specific Singleton's subclass. You'll
     * see how this works in a moment.
     */
    private static array $instances = [];

    private ?UtilsFunctionsNavigation $utilsFunctionNaviation = null;
    private ?UtilsFunctionsImage $utilsFunctionsImage = null;
    private ?Globals $globals = null;
    private ?DataBase $dataBase = null;


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

    public static function getInstance(): UtilsFunctionsContent
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new UtilsFunctionsContent();
        }

        return self::$instances[$cls];
    }

    public function getUtilsFunctionNaviation(): UtilsFunctionsNavigation
    {
        if (!$this->utilsFunctionNaviation) {
            $this->utilsFunctionNaviation = UtilsFunctionsNavigation::getInstance();
        }
        return $this->utilsFunctionNaviation;
    }

    public function getUtilsFunctionsImage(): UtilsFunctionsImage
    {
        if (!$this->utilsFunctionsImage) {
            $this->utilsFunctionsImage = UtilsFunctionsImage::getInstance();
        }
        return $this->utilsFunctionsImage;
    }

    public function getGlobals(): Globals
    {
        if (!$this->globals) {
            $this->globals = Globals::getInstance();
        }
        return $this->globals;
    }

    public function getDatabase(): DataBase
    {
        if (!$this->dataBase) {
            $this->dataBase = DataBase::getInstance();
        }
        return $this->dataBase;
    }

    // Contenu texte
    public function txt($key = null, $filter = [])
    {
        $key = ($key ? $key : "txt-" . $this->getGlobals()->getEditkey());

        // S'il y a une valeur pour le filter mais que != tableau => c'est une class
        if (!is_array($filter)) {
            $filter = ["class" => $filter];
        }

        // Si contenu global on rapatri le contenu depuis la table méta (Anciennement "universel")
        if (isset($filter['global'])) {
            $sel = $this->getDatabase()->getConnect()->query("SELECT * FROM " . $this->getGlobals()->getTableMeta() . " WHERE type='global' AND cle='" . $this->getUtilsFunctionNaviation()->encode($key) . "' LIMIT 1");
            $res = $sel->fetch_assoc();

            $content = $this->getGlobals()->getContent();
            $content[$key] = $res['val'];
            $this->getGlobals()->setContent($content);
        }

        echo "<" . ($filter['tag'] ?? "div");

        echo " id='" . $this->getUtilsFunctionNaviation()->encode($key) . "'";

        echo " class='";
        if (isset($filter['editable'])) {
            echo $filter['editable'];
        } else {
            echo "editable";
        }
        if (isset($filter['class'])) {
            echo " " . $filter['class'];
        }
        if (isset($filter['global'])) {
            echo " global";
        }
        if (isset($filter['lazy'])) {
            echo " lazy";
        }
        echo "'";

        if (isset($filter['placeholder'])) {
            echo " placeholder=\"" . $filter['placeholder'] . "\"";
        }

        if (isset($filter['itemprop'])) {
            echo " itemprop=\"" . $filter['itemprop'] . "\"";
        }

        if (isset($filter['dir'])) {
            echo " data-dir='" . $filter['dir'] . "'";
        } // Desitation de stockage du fichier

        if (isset($filter['builder'])) {
            echo " data-builder='" . $filter['builder'] . "'";
        }

        echo ">";

        if (isset($this->getGlobals()->getContent()[$key])) {
            if (isset($filter['function'])) {
                echo $filter['function']($this->getGlobals()->getContent()[$key]);
            } else {
                echo $this->getGlobals()->getContent()[$key];
            }
        } elseif (isset($filter['default'])) {
            echo $filter['default'];
        }

        echo "</" . ($filter['tag'] ?? "div") . ">";

        $this->getGlobals()->increaseEditkey();
    }


    // Fonction raccourcie
    public function h1($key = null, $filter = [])
    {
        // S'il y a une valeur pour le filter mais que != tableau => c'est une class
        if (!is_array($filter)) {
            $filter = ['class' => $filter];
        }

        $filter['tag'] = __FUNCTION__; // Force le tag

        $this->txt($key, $filter); // Appel de la fonction d'origine
    }

    public function h2($key = null, $filter = [])
    {
        // S'il y a une valeur pour le filter mais que != tableau => c'est une class
        if (!is_array($filter)) {
            $filter = ['class' => $filter];
        }

        $filter['tag'] = __FUNCTION__; // Force le tag

        $this->txt($key, $filter); // Appel de la fonction d'origine
    }

    public function h3($key = null, $filter = [])
    {
        // S'il y a une valeur pour le filter mais que != tableau => c'est une class
        if (!is_array($filter)) {
            $filter = ['class' => $filter];
        }

        $filter['tag'] = __FUNCTION__; // Force le tag

        $this->txt($key, $filter); // Appel de la fonction d'origine
    }

    public function span($key = null, $filter = [])
    {
        // S'il y a une valeur pour le filter mais que != tableau => c'est une class
        if (!is_array($filter)) {
            $filter = ['class' => $filter];
        }

        $filter['tag'] = __FUNCTION__; // Force le tag

        $this->txt($key, $filter); // Appel de la fonction d'origine
    }


    // Contenu image/fichier
    public function media($key = null, $filter = [])
    {
        $key = ($key ? $key : "file-" . $this->getGlobals()->getEditkey());

        // Si contenu global on rapatri le contenu depuis la table méta
        if (isset($filter['global'])) {
            $sel = $this->getDatabase()->getConnect()->query("SELECT * FROM " . $this->getGlobals()->getTableMeta() . " WHERE type='global' AND cle='" . $this->getUtilsFunctionNaviation()->encode($key) . "' LIMIT 1");
            $res = $sel->fetch_assoc();
            $content = $this->getGlobals()->getContent();
            $content[$key] = $res['val'];
            $this->getGlobals()->setContent($content);
        }


        // Verification de la config de https pour crée le bon chemin (on force https dans les chemins)
        if (@$_SERVER['REQUEST_SCHEME'] == 'https' and $this->getGlobals()->getScheme() != 'https://') {
            $this->getGlobals()->setHome(str_replace('http://', 'https://', $this->getGlobals()->getHome()));
        }

        // S'il y a une valeur pour le filter mais != tableau => c'est la taille de l'image
        if (!is_array($filter)) {
            $filter = ["size" => $filter];
        }

        // Une taille est définie
        if (isset($filter['size'])) {
            $size = explode("x", $filter['size']);
        }

        // Nom du fichier
        if (isset($this->getGlobals()->getContent()[$key]) and $this->getGlobals()->getContent()[$key] != "") {
            // Si c'est une url externe on pointe vers, sinon on clean et ajoute le nom du site courant
            if (isset(parse_url($this->getGlobals()->getContent()[$key])['scheme'])) {
                $filename = $this->getGlobals()->getContent()[$key];
            } else {
                $filename = $this->getGlobals()->getHome() . ltrim($this->getGlobals()->getContent()[$key], $this->getGlobals()->getReplacePath());
            }
        } else {
            $filename = "";
        }

        if ($filename) {
            // Extention du fichier
            $ext = pathinfo(explode("?", $filename)[0], PATHINFO_EXTENSION);

            // Recherche du type de fichier
            switch ($ext) {
                case"jpg":
                case"jpeg":
                case"png":
                case"gif":
                case"svg":
                case"webp":
                    $img = true;
                    break;

                case"webm":
                case"mp4":
                case"ogg":
                    $video = true;
                    break;

                default:
                    $fa = "doc";
                    break;

                case"zip":
                    $fa = "file-archive";
                    break;
                case"msword":
                    $fa = "file-word";
                    break;
                case"vnd.ms-excel":
                    $fa = "file-excel";
                    break;
                case"vnd.ms-powerpoint":
                    $fa = "file-powerpoint";
                    break;
                case"pdf":
                    $fa = "file-pdf";
                    break;
            }
        }

        echo '<span';

        echo ' id="' . $this->getUtilsFunctionNaviation()->encode($key) . '"';

        echo ' class="';
        if (isset($filter['editable'])) {
            echo $filter['editable'];
        } else {
            echo 'editable-media';
        }
        if (isset($filter['global']) and $filter['crop'] == true) {
            echo ' global';
        }
        //if(isset($size[0]) and isset($size[1])) echo' crop';
        if (isset($filter['crop']) and $filter['crop'] == true) {
            echo ' crop';
        }
        echo '"';

        if (isset($filter['class'])) {
            echo ' data-class="' . $filter['class'] . '"';
        }
        if (isset($filter['dir'])) {
            echo ' data-dir="' . $filter['dir'] . '"';
        } // Desitation de stockage du fichier
        if (isset($size[0])) {
            echo ' data-width="' . $size[0] . '"';
        }
        if (isset($size[1])) {
            echo ' data-height="' . $size[1] . '"';
        }

        if (isset($size[0]) or isset($size[1])) { // @todo Vérifier si on met max-width ou width
            echo ' style="' .
                (isset($size[0]) ? 'max-width:' . $size[0] . 'px;' : '') .
                (isset($size[1]) ? 'max-height:' . $size[1] . 'px' : '') .
                '"';
        }

        if (isset($filter['placeholder'])) {
            echo ' placeholder="' . $filter['placeholder'] . '"';
        }

        echo '>';

        if (isset($img)) { // C'est une image
            // Si on veux voir la version grande au clic
            if (isset($filter['zoom'])) {
                $parse_url = parse_url($filename);
                parse_str($parse_url['query'], $get);
                if (@$get['zoom']) {
                    echo '<a href="' . $get['zoom'] . '">';
                }
            }

            echo '<img ';

            //srcset pour image adaptative
            if (isset($filter['srcset'])) {

                //on récupère le chemin de l'image de référence à partir de laquelle on fait les miniatures
                $parse_url = parse_url($filename);
                parse_str($parse_url['query'], $get);
                if (@$get['zoom']) {
                    $source = $get['zoom'];
                } else {
                    $source = $filename;
                }

                //récupération des informations de l'image de référence
                $pathinfo = pathinfo($source);
                list($source_width, $source_height) = getimagesize($source);

                //ecriture de l'attribut
                echo 'srcset="';
                foreach ($filter['srcset'] as $key => $thumbnail_width) {

                    //si la taille de la miniature est inférieur à la taille de l'image source
                    if ($source_width > $thumbnail_width) {

                        //calcul de la hauteur de la miniature
                        $thumbnail_height = round($thumbnail_width * $source_height / $source_width);

                        // on regarde s'il y a déjà une miniature existante
                        $thumbnail_path = $pathinfo['dirname'] . "/resize/" . $pathinfo['filename'] . "-" . $thumbnail_width . "x" . $thumbnail_height . "." . $pathinfo['extension'];
                        $thumbnail_clean = parse_url($thumbnail_path, PHP_URL_PATH);

                        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $this->getGlobals()->getPath() . $thumbnail_clean)) {
                            $thumbnail = $thumbnail_clean;
                        } else {
                            $thumbnail = $this->getUtilsFunctionsImage()->resize($source, $thumbnail_width, $thumbnail_height);
                        }

                    } else {
                        $thumbnail = $source;
                    }

                    echo($key > 0 ? ',' : '')
                        . parse_url($thumbnail, PHP_URL_PATH)
                        . ' ' . $thumbnail_width . 'w';

                }
                echo '"';

            }

            // Si lazyloading on met une image transparente dans le src
            if (isset($filter['lazy'])) {
                echo 'src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-src="' . $filename . '" loading="lazy"';
            } else {
                echo 'src="' . $filename . '"';
            }

            if (isset($size[0]) or isset($size[1])) {
                echo ' style="';
                if (isset($size[0])) {
                    echo 'max-width: ' . $size[0] . 'px;';
                }
                if (isset($size[1])) {
                    echo 'max-height: ' . $size[1] . 'px;';
                }
                echo '"';
            }

            // On met en data l'url de la version grande
            if (isset($filter['data-zoom'])) {
                echo ' data-zoom="' . @$filter['data-zoom'] . '"';
            }

            if (isset($filter['itemprop'])) {
                echo ' itemprop="' . @$filter['itemprop'] . '"';
            }

            // Image map
            if (isset($filter['usemap'])) {
                echo ' usemap="' . @$filter['usemap'] . '"';
            }

            // Texte ALT
            if (isset($this->getGlobals()->getContent()[$key . '-alt'])) {
                echo ' alt="' . $this->getGlobals()->getContent()[$key . '-alt'] . '"';
            } else {
                echo ' alt=""';
            }

            echo ' class="';
            if (isset($filter['zoom'])) {
                echo ' zoom';
            }
            if (isset($filter['class'])) {
                echo ' ' . $filter['class'];
            }
            echo '">';

            // Fin lien zoom
            if (isset($filter['zoom'])) {
                echo '</a>';
            }
        } elseif (isset($video)) { // C'est une video
            echo '<video' . (isset($size[0]) ? ' width="' . $size[0] . '"' : '') . ' src="' . $filename . '" title="' . $this->getGlobals()->getContent()[$key] . '" preload="none" controls></video>';
        } elseif ($filename) { // C'est un fichier
            echo '<a href="' . $this->getGlobals()->getContent()[$key] . '" target="_blank"><i class="fa fa-fw fa-' . $fa . ' mega" title="' . $this->getGlobals()->getContent()[$key] . '"></i></a>';
        }


        echo '</span>';

        $this->getGlobals()->increaseEditkey();
    }

    // Image de fond de bloc
    public function bg($key = null, $filter = [])
    {
        $key = ($key ? $key : "bg-" . $this->getGlobals()->getEditkey());

        // Si contenu global on rapatri le contenu depuis la table méta
        if (isset($filter['global'])) {
            $sel = $this->getDatabase()->getConnect()->query("SELECT * FROM " . $this->getGlobals()->getTableMeta() . " WHERE type='global' AND cle='" . $this->getUtilsFunctionNaviation()->encode($key) . "' LIMIT 1");
            $res = $sel->fetch_assoc();

            $content = $this->getGlobals()->getContent();
            $content[$key] = $res['val'];
            $this->getGlobals()->setContent($content);
        }

        // Si pas d'array et qu'il y a une variable c'est que c'est un lazyload
        if (!is_array($filter) and isset($filter)) {
            $filter = ["lazy" => true];
        }

        $url = (isset($this->getGlobals()->getContent()[$key]) ? $this->getGlobals()->getHome() . ltrim($this->getGlobals()->getContent()[$key], $this->getGlobals()->getReplacePath()) : "");

        echo " data-id='" . $this->getUtilsFunctionNaviation()->encode($key) . "' data-bg=\"" . $url . "\"";

        if (isset($filter['global'])) {
            echo " data-global='true'";
        }

        if (isset($filter['dir'])) {
            echo " data-dir='" . $filter['dir'] . "'";
        } // Desitation de stockage du fichier

        // Si lazy load des images de fond
        if (isset($filter['lazy'])) {
            echo ' data-lazy="bg"';
        } elseif ($url) {
            echo ' style="background-image: url(\'' . $url . '\')"';
        }

        $this->getGlobals()->increaseEditkey();
    }

    // Bloc de contenu générique duplicable
    public function module($module = "module", $content = null)
    {
        if ($content == null) {
            $content = $this->getGlobals()->getContent();
        }

        // Extrait les données module du tableau des contenu
        $keys = array_keys($content);
        foreach ($keys as $key) {
            if (preg_match("/^" . $module . "-/", $key) == 1) {
                // Récupère le denier chiffre (numéro d'occurance)
                preg_match('/(\d+)(?!.*\d)/', $key, $match);
                $num_module = @$match[1];

                // Récupère le type d'élément (txt, img, href, alt...) = dernier texte
                preg_match('/([a-z]+)(?!.*[a-z])/', $key, $match);
                $type_module = $match[1];

                // @todo supp = ancienne version qui ne marche pas avec les alt éditables
                // Supprime le préfix du nom du module en cours
                //$type_num_module = str_replace($module."-", "", $key);
                // Sépare les elements du nom du module
                //$exp_key = explode("-", $type_num_module);
                // Numéro de l'occurence du module
                //$num_module = $exp_key[(count($exp_key)-1)];
                // Nom distinctif du module (txt, img...)
                //$type_module = rtrim($type_num_module, "-".$num_module);

                // Si une variable dans la zone originale duplicable (0) on la raz par sécurité
                if ($num_module == 0) {
                    $content[$key] = "";
                }

                // Création du tableau avec les elements de modules
                $array_module[$module][$num_module][$type_module] = $content[$key];

                // Force le contenu du bloc vide duplicable (0) à vide
                if ($num_module == 0) {
                    $content2 = $this->getGlobals()->getContent();
                    $content2[$key] = '';
                    $this->getGlobals()->setContent($content2);
                }

                //echo $key." | ".$type_module."*".$num_module."*".($num_module == 0)." : ".$content[$key]."<br>";
            }
        }

        // Bloc vide pour l'ajout de nouveau élément (bloc duplicable)
        $array_module[$module][0]['titre'] = "";

        // Re-init le tableau
        reset($array_module[$module]);

        return $array_module[$module];
    }

    // Contenu champ checkbox
    public function checkbox($key = null, $filter = [])
    {
        $key = ($key ? $key : "checkbox-" . $this->getGlobals()->getEditkey());

        // fa-check/fa-close => fa-ok/fa-cancel
        echo "<i class='" . (isset($filter['editable']) ? $filter['editable'] : "editable-checkbox") . " fa fa-fw " . ((isset($this->getGlobals()->getContent()[$key]) and $this->getGlobals()->getContent()[$key] == true) ? "fa-ok yes" : "fa-cancel no") . (isset($filter['class']) ? " " . $filter['class'] : "") . "' id='" . $this->getUtilsFunctionNaviation()->encode($key) . "'></i>";

        $this->getGlobals()->increaseEditkey();
    }

    // Contenu champ radio / si checked = true => checked par défaut si pas de radio selectionné
    public function radio($key = null, $name = null, $checked = null)
    {
        $this->input($key, ['type' => 'radio', 'name' => $name, 'checked' => $checked]);
    }

    // Contenu champ select
    public function select($key = null, $filter = [])
    {
        $key = ($key ? $key : "select-" . $this->getGlobals()->getEditkey());

        if (!is_array($filter)) {
            $filter = ["option" => $filter];
        }

        $option_decode = json_decode($filter['option'], true);

        // inverse les clés et les valeurs
        if (@$filter['flip']) {
            $option_decode = array_flip($option_decode);
            $filter['option'] = json_encode($option_decode, JSON_UNESCAPED_UNICODE);
        }

        if (isset($this->getGlobals()->getContent()[$key]) and isset($option_decode[$this->getGlobals()->getContent()[$key]])) {
            $selected_key = $this->getGlobals()->getContent()[$key];
            $selected_option = $option_decode[$this->getGlobals()->getContent()[$key]];
        } else {
            $selected_key = key($option_decode);
            if ($selected_key) {
                $selected_option = $option_decode[$selected_key];
            }
        }

        echo "<" . (isset($filter['tag']) ? $filter['tag'] : "span") . (isset($filter['href']) ? ' href="' . $filter['href'] . '"' : '') . " id='" . $this->getUtilsFunctionNaviation()->encode($key) . "' class='" . (isset($filter['editable']) ? $filter['editable'] : "editable-select") . (isset($filter['class']) ? " " . $filter['class'] : "") . "' data-option='" . str_ireplace("'", "&apos;", $filter['option']) . "' data-selected=\"" . $selected_key . "\">" . @$selected_option . "</" . (isset($filter['tag']) ? $filter['tag'] : "span") . ">";

        $this->getGlobals()->increaseEditkey();
    }

    // Contenu champ input
    public function input($key = null, $filter = null): void
    {
        $key = ($key ? $key : "input-" . $this->getGlobals()->getEditkey());

        if (!is_array($filter)) {
            $filter = ["class" => $filter];
        }
        if (!isset($filter['type'])) {
            $filter['type'] = "text";
        }

        echo '<input type="' . $filter['type'] . '" id="' . $this->getUtilsFunctionNaviation()->encode($key) . '"';

        if (@$filter['name']) {
            echo ' name="' . $filter['name'] . '"';
        }

        echo ' value="';

        if (isset($this->getGlobals()->getContent()[$key])) {
            if (@$filter['type'] == 'number') {
                echo str_replace(',', '.', $this->getGlobals()->getContent()[$key]);
            } else {
                echo $this->getGlobals()->getContent()[$key];
            }
        } else {
            echo @$filter['default'];
        }

        echo '"';


        echo ' class="editable-input ' . @$filter['class'] . '"';

        if ($filter['type'] == "checkbox" and $this->getGlobals()->getContent()[$key] == true) {
            echo ' checked="checked"';
        } elseif ($filter['type'] == "radio" and @$filter['name'] and ($this->getGlobals()->getContent()[$filter['name']] == $key or (!$this->getGlobals()->getContent()[$filter['name']] and $filter['checked']))) {
            echo ' checked="checked"';
        }

        if (isset($filter['placeholder'])) {
            echo ' placeholder="' . $filter['placeholder'] . '"';
        }
        if (@$filter['autocomplete'] == 'off') {
            echo ' autocomplete="off"';
        }

        if (@$filter['readonly']) {
            echo ' readonly';
        }
        if (@$filter['required']) {
            echo ' required';
        }

        echo '>';

        // Si autocomplete
        if (isset($filter['autocomplete']) and $filter['autocomplete'] != 'off') { ?>
            <script>
              edit.push(function () {
                $("#<?=$this->getUtilsFunctionNaviation()->encode($key)?>").autocomplete({
                  source: <?='["' . implode('","', $filter['autocomplete']) . '"]';?>,
                  minLength: 0,
                }).focus(function () {
                  $(this).autocomplete("search");
                });
              });
            </script>
        <?php }

        $this->getGlobals()->increaseEditkey();
    }

    // Lien éditable
    public function href($key = null, $target = null): void
    {
        $key = ($key ? $key : "href-" . $this->getGlobals()->getEditkey());

        if (isset($this->getGlobals()->getContent()[$key])) {
            echo 'href="' . $this->getGlobals()->getContent()[$key] . '" ';
        }

        echo 'data-href="' . $this->getUtilsFunctionNaviation()->encode($key) . '" ';

        if ($target == 'file' and strstr($this->getGlobals()->getContent()[$key], ".")) {
            echo 'target="_blank"';
        }

        $this->getGlobals()->increaseEditkey();
    }

    // Tags
    public function tag($key = null, $filter = [])
    {
        $key = $this->getUtilsFunctionNaviation()->encode($key ? $key : "tag");

        echo '<'
            . (isset($filter['tag']) ? $filter['tag'] : "nav")
            . ' id="' . $key . '" class="editable-tag' . (isset($filter['class']) ? " " . $filter['class'] : '') . '"'
            . (isset($filter['placeholder']) ? ' placeholder="' . $filter['placeholder'] . '"' : '')
            . (isset($filter['aria-label']) ? ' aria-label="' . $filter['aria-label'] . '"' : '')
            . (isset($filter['separator']) ? ' data-separator="' . $filter['separator'] . '"' : '')
            . (isset($filter['itemprop']) ? ' itemprop="' . $filter['itemprop'] . '"' : '')
            . ((!isset($filter['tag']) or @$filter['tag'] == 'nav') ? ' role="navigation"' : '')
            . '>';

        $i = 1;
        $sel_tag = $this->getDatabase()->getConnect()->query("SELECT * FROM " . $this->getGlobals()->getTableTag() . " WHERE id='" . (int)$this->getGlobals()->getId() . "' AND zone='" . $key . "' AND lang='" . $this->getGlobals()->getLang() . "' ORDER BY ordre ASC LIMIT 10");
        while ($res_tag = $sel_tag->fetch_assoc()) {
            $tags = $this->getGlobals()->getTags();
            $tags[$res_tag['encode']] = $res_tag['name'];
            $this->getGlobals()->setTags($tags);

            // Ajout de séparateur
            if (@$filter['tag'] != 'ul' and $i > 1) {
                echo(@$filter['separator'] ? $filter['separator'] : ', ');
            }

            // Si ul
            if (@$filter['tag'] == 'ul') {
                echo '<li>';
            }

            // Pas de lien ?
            if (@$filter['href'] === false) {
                echo '<span>' . $res_tag['name'] . '</span>';
            } else {
                echo '<a href="' . $this->getUtilsFunctionNaviation()->make_url($key, [$res_tag['encode'], 'domaine' => true]) . '" class="tdn">' . $res_tag['name'] . '</a>';
            }

            // Si ul
            if (@$filter['tag'] == 'ul') {
                echo '</li>';
            }

            $ordre = $res_tag['ordre'];

            $i++;
        }

        echo '</' . (isset($filter['tag']) ? $filter['tag'] : "nav") . '>';

        // Si on veut choisir l'ordre du tag
        if (isset($filter['ordre'])) {
            echo '<input type="number" data-zone="' . $key . '" class="editable-tag-ordre" value="' . (is_numeric($filter['ordre']) ? $filter['ordre'] : $ordre) . '" size="2" title="' . $filter['ordre'] . '"' . (is_numeric($filter['ordre']) ? ' readonly' : '') . '>';
        }
    }
}
