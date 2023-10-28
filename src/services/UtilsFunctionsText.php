<?php

namespace Translucide\services;

use Exception;

class UtilsFunctionsText
{
    /**
     * The Singleton's instance is stored in a static field. This field is an
     * array, because we'll allow our Singleton to have subclasses. Each item in
     * this array will be an instance of a specific Singleton's subclass. You'll
     * see how this works in a moment.
     */
    private static array $instances = [];

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

    public static function getInstance(): UtilsFunctionsText
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new UtilsFunctionsText();
        }

        return self::$instances[$cls];
    }

    public function getUtilsFunctionsLanguage(): UtilsFunctionsLanguage
    {
        if (!$this->utilsFunctionsLanguage) {
            $this->utilsFunctionsLanguage = UtilsFunctionsLanguage::getInstance();
        }
        return $this->utilsFunctionsLanguage;
    }

    // Coupe une phrase proprement
    public function word_cut($texte, $limit, $end = '', $tags = ''): array|string|null //$tags = '<br><div>'  $end = '...'
    {
        $texte = strip_tags($texte . ' ', $tags); // texte sans html
        $word_cut = preg_replace('/\s+?(\S+)?$/', '', substr($texte, 0, $limit)); // /\s+?(\S+)?$/u => /u => pour l'utf8
        if (strlen($word_cut) < strlen(trim($texte))) {
            $word_cut .= $end;
        } // Si coupure on ajoute une ponctuation à la fin
        return $word_cut;
    }

    // Format de date lisible
    public function date_lang($date): false|string
    {

        $date = date_create($date);

        $jour = date_format($date, 'j');
        $mois = date_format($date, 'n');
        $annee = date_format($date, 'Y');

        $nom_mois = [
            1 => 'january',
            2 => 'february',
            3 => 'march',
            4 => 'april',
            5 => 'may',
            6 => 'june',
            7 => 'july',
            8 => 'august',
            9 => 'september',
            10 => 'october',
            11 => 'november',
            12 => 'december',
        ];

        // si 1 on écrit 1er
        if ($jour == 1) {
            $jour = '1' . $this->getUtilsFunctionsLanguage()->__('st');
        }

        // Phrase avec nom du mois traduit
        $date = $jour . ' ' . $this->getUtilsFunctionsLanguage()->__($nom_mois[$mois]) . ' ' . $annee;

        // Convertir en utf8 si besoin en fonction du serveur
        return iconv(mb_detect_encoding($date, mb_detect_order(), true), 'UTF-8', $date);
    }

}
