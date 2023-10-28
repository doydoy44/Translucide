<?php

namespace Translucide\services;

use Exception;

class UtilsFunctionsImage
{
    /**
     * The Singleton's instance is stored in a static field. This field is an
     * array, because we'll allow our Singleton to have subclasses. Each item in
     * this array will be an instance of a specific Singleton's subclass. You'll
     * see how this works in a moment.
     */
    private static array $instances = [];

    private ?UtilsFunctionsNavigation $utilsFunctionNaviation = null;
    private ?UtilsFunctionsLanguage $utilsFunctionsLanguage = null;

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

    public static function getInstance(): UtilsFunctionsImage
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new UtilsFunctionsImage();
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

    public function getUtilsFunctionsLanguage(): UtilsFunctionsLanguage
    {
        if (!$this->utilsFunctionsLanguage) {
            $this->utilsFunctionsLanguage = UtilsFunctionsLanguage::getInstance();
        }
        return $this->utilsFunctionsLanguage;
    }

    // Verifie que le fichier et supporter et pas de hack
    public function file_check($file, $force_file_check_hack = false)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_infos['mime'] = finfo_file($finfo, $_FILES[$file]['tmp_name']);
        finfo_close($finfo);

        // Vérifie que le type mime est supporté (Hack protection : contre les mauvais mimes types)
        if (in_array($file_infos['mime'], $GLOBALS['mime_supported'])) {
            if (@$GLOBALS['file_check_hack'] or $force_file_check_hack) {
                // Le fichier tmp ne contient pas de php ou de javascript
                if (!preg_match("/<\?php|<\? |<\?=|<scr/", file_get_contents($_FILES[$file]['tmp_name']), $matches)) {
                    return true;
                } else {
                    return false;
                }
                //print_r($matches);
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    // Redimentionne une image
    public function resize($source_file, $new_width = null, $new_height = null, $dest_dir = null, $option = null)
    {
        // Supprime les arguments après l'extension (timer...)
        $source_file = explode("?", $source_file)[0];

        // Extention du fichier
        $ext = pathinfo($source_file, PATHINFO_EXTENSION);

        // Récupération des informations de l'image source
        list($source_width, $source_height, $type, $attr) = getimagesize($source_file);

        if (!$source_width and !$source_height and $ext != 'svg') {
            exit($this->getUtilsFunctionsLanguage()->__("Size of source file unspecified"));
        }

        // Récupération de l'extension
        $source_ext = pathinfo($source_file, PATHINFO_EXTENSION);

        // file_name : on récup le nom du fichier, on lui supp l'extension (qui ne passe pas l'encode), on l'encode
        $root_dir = $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['path'];
        $file_name = $this->getUtilsFunctionNaviation()->encode(basename(basename($source_file), "." . $source_ext));

        // Dossier final d'image redimensionnée
        $dir = ($dest_dir ? $dest_dir . '/' : '');

        // dir clean si media forcé
        $dir_clean = ltrim(str_replace($GLOBALS['media_dir'], '', $dir), '/');

        // Si image à réduire ou à forcer
        if (($new_width and $source_width > $new_width) or ($new_height and $source_height > $new_height) or $option) {
            // Version original pour le zoom
            $zoom = $GLOBALS['media_dir'] . '/' . $dir_clean . $file_name . '.' . $source_ext;

            // Si media dans dir on force. ne met pas dans /resize/
            $dir = (strpos($dir, $GLOBALS['media_dir']) === 0 ? '' : $GLOBALS['media_dir'] . '/resize/') . $dir;

            // Crée les dossiers
            @mkdir($root_dir . $dir, 0755, true);

            // On crée une image avec l'image source en fonction de son type
            switch ($type) {
                case 1:
                    $source_img = imagecreatefromgif($source_file);
                    break;
                case 2:
                    $source_img = imagecreatefromjpeg($source_file);
                    break;
                case 3:
                    $source_img = imagecreatefrompng($source_file);
                    break;
                case 18:
                    $source_img = imagecreatefromwebp($source_file);
                    break;
                default:
                    exit($this->getUtilsFunctionsLanguage()->__("Unsupported file type"));
                    break;
            }

            // Callage de l'image
            $x = $y = 0;

            if ($new_width and $new_height) { // On redimensionne dans tous les sens
                $ratio_width = $source_width / $new_width;
                $ratio_height = $source_height / $new_height;

                if ($ratio_width > 1 or $ratio_height > 1) { // Taille maximale dépassée dans un sens ?
                    if ($option == "crop") {
                        if ($ratio_width < $ratio_height) {
                            $dest_width = $new_width;
                            $dest_height = ceil(round($source_height / $ratio_width, 2));
                        } else {
                            $dest_width = ceil(round($source_width / $ratio_height, 2));
                            $dest_height = $new_height;
                        }

                        // Positionnement de l'image cropé
                        $x = ($new_width - $dest_width) / 2;
                        $y = ($new_height - $dest_height) / 3; // Paramètre pour callé en hauteur le crop (2 à l'origine)
                    } else { // Si pas crop on resize la taille la plus grande
                        if ($ratio_width < $ratio_height) {
                            $dest_width = $new_width = ceil(round($source_width / $ratio_height, 2));
                            $dest_height = $new_height;
                        } else {
                            $dest_width = $new_width;
                            $dest_height = $new_height = ceil(round($source_height / $ratio_width, 2));
                        }
                    }
                } else { // Image carrée
                    $dest_width = $new_width;
                    $dest_height = $new_height;
                }

            } elseif ($new_width and !$new_height) { // On force la largeur => on calcule la nouvelle hauteur
                $new_width = $dest_width = $new_width;
                $new_height = $dest_height = ceil(round($new_width * $source_height / $source_width, 2));
            } elseif (!$new_width and $new_height) { // On force la hauteur => on calcule la nouvelle largeur
                $new_width = $dest_width = ceil(round($new_height * $source_width / $source_height, 2));
                $new_height = $dest_height = $new_height;
            }

            // Cas ou pas de nouvelle taille => on prend les tailles de l'image d'origine
            if (!$new_width and !$new_height) {
                $new_width = $dest_width = $source_width;
                $new_height = $dest_height = $source_height;
            }

            // Création de l'image vide de base pour y coller l'image finale
            $final_img = imagecreatetruecolor($new_width, $new_height);

            // S'il y a une transparence on la conserve
            switch ($type) {
                case 1: // Gif
                    imagecolortransparent($final_img, imagecolorallocatealpha($final_img, 0, 0, 0, 127));
                    // no break
                case 3: // Png
                case 18: // Webp
                    // Si conversion vers image sans transparence on met du blanc au fond
                    if ($option == 'tojpg') {
                        $white = imagecolorallocate($final_img, 255, 255, 255);
                        imagefilledrectangle($final_img, 0, 0, $new_width, $new_height, $white);
                    } else {
                        imagealphablending($final_img, false);
                        imagesavealpha($final_img, true);
                    }
                    break;
            }

            // On copie et resize l'image dans l'image de base finale
            imagecopyresampled($final_img, $source_img, $x, $y, 0, 0, $dest_width, $dest_height, $source_width, $source_height);

            // Libère la mémoire
            imagedestroy($source_img);

            // Si l'image n'a pas la bonne orientation (consomme pas mal de mémoire)
            switch ($option) {
                case 3:
                    $deg = 180;
                    break;
                case 6:
                    $deg = 270;
                    break;
                case 8:
                    $deg = 90;
                    break;
            }
            if (isset($deg)) {
                $final_img = imagerotate($final_img, $deg, 0);
            }

            // Si convertion de format
            switch ($option) {
                case 'tojpg':
                    $source_ext = 'jpg';
                    $type = 2;
                    $zoom = '';
                    break;
                case 'topng':
                    $source_ext = 'png';
                    $type = 3;
                    $zoom = '';
                    break;
                case 'towebp':
                    $source_ext = 'webp';
                    $type = 18;
                    $zoom = '';
                    break;
            }


            // Ajoute la taille de la nouvelle image en supprimant l'ancienne si besoin
            preg_match("/(-[0-9]+x[0-9]+)$/", $file_name, $matches);
            if (isset($matches[0])) {
                $file_name = str_replace($matches[0], "", $file_name);
            }
            $file_name_ext = $file_name . "-" . round($new_width) . "x" . round($new_height) . "." . $source_ext;

            // Création de l'image finale dans le bon type
            switch ($type) {
                case 1:
                    imagegif($final_img, $root_dir . $dir . $file_name_ext);
                    break;
                case 2:
                    imagejpeg($final_img, $root_dir . $dir . $file_name_ext, $GLOBALS['jpg_quality']);
                    break;
                case 3:
                    imagepng($final_img, $root_dir . $dir . $file_name_ext);
                    break; // $GLOBALS['png_quality']
                case 18:
                    imagewebp($final_img, $root_dir . $dir . $file_name_ext, $GLOBALS['webp_quality']);
                    break;
            }

            imagedestroy($final_img); // Libère la mémoire
        } else { // Copie l'image si elle est plus petite ou à la bonne taille
            $zoom = ""; // Pas de zoom

            $dir = $GLOBALS['media_dir'] . "/" . $dir_clean; // @todo ajouter le dir (sans resize)
            $file_name_ext = $file_name . "." . $source_ext;

            @mkdir($root_dir . $dir, 0755, true); // Crée les dossiers

            copy($source_file, $root_dir . $dir . $file_name_ext);
        }

        return $dir . $file_name_ext . "?" . ($zoom ? "zoom=" . $zoom . "&" : "") . time(); // Time pour forcer le refresh
    }

    // Examine et traite une image
    public function img_process($root_file, $dest_dir = null, $new_width = null, $new_height = null, $resize = null)
    {
        // Valeur par défaut
        $option = null;
        $dir = ($dest_dir ? $GLOBALS['media_dir'] . '/' . $dest_dir : $GLOBALS['media_dir']);
        $src_file = $dir . '/' . basename($root_file) . '?' . time();

        // Taille de l'image uploadée
        list($source_width, $source_height, $type) = getimagesize($root_file);

        // Limite max de taille d'image pour l'upload global
        list($max_width, $max_height) = explode("x", $GLOBALS['max_image_size']);

        // On vérifie la bonne orientation de l'image jpeg
        if ($type == 2) { // Exif ne fonctionne qu'avec les jpeg
            $exif = @exif_read_data($root_file);
            if (isset($exif['Orientation']) and $exif['Orientation'] != 1) {
                $max_width = ($source_width > $max_width ? $max_width : $source_width);
                $max_height = ($source_height > $max_height ? $max_height : $source_height);
                $option = $exif['Orientation'];
            }
        }

        // Image trop grande (> global) pour le web : on la redimensionne
        if ($source_width > $max_width or $source_height > $max_height or $option) {
            // Redimensionne sans crop
            $src_file = $this->resize($root_file, $max_width, $max_height, $dir, $option);

            // Supprime l'image originale puisque l'on ne garde que la maxsize
            unlink($root_file);

            // La maxsize devient l'image root (explode: supp le timer)
            $root_file = $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['path'] . explode("?", $src_file)[0];
        }


        // L'interface a demandé un redimensionnement ?
        if ($resize and (($new_width and $source_width > $new_width) or ($new_height and $source_height > $new_height))) {
            return $this->resize($root_file, $new_width, $new_height, $dest_dir, $resize); // Redimensionne

            //unlink($root_file); // Si on a redimensionné on supp l'image de base
        } else { // Pas de redimensionnement
            $dest_file = $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['path'] . explode("?", $src_file)[0];

            // Le fichier destination est demandé dans un endroit different du fichier source
            if ($root_file != $dest_file) {
                // Si dossier destination on copie l'image dans la destination
                copy($root_file, $dest_file);

                // Et on supprime l'image source
                unlink($root_file);
            }

            // Retourne l'url du fichier
            return $src_file;
        }
    }
}
