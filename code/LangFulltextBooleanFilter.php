<?php

/**
 * @author    Donatas Navidonskis <donatas@navidonskis.com>
 * @since     2017
 * @class     LangFulltextBooleanFilter
 *
 */
class LangFulltextBooleanFilter extends FulltextFilter {

    /**
     * @param DataQuery $query
     *
     * @return DataQuery
     */
    protected function applyOne(DataQuery $query) {
        $this->model = $query->applyRelation($this->relation);
        $predicate = sprintf("MATCH (%s) AGAINST (? IN BOOLEAN MODE)", $this->getDbName());

        $keywords = $this->convertForeignToLatin($this->getValue());

        $andProcessor = create_function('$matches', '
	 		return " +" . $matches[2] . " +" . $matches[4] . " ";
	 	');
        $notProcessor = create_function('$matches', '
	 		return " -" . $matches[3];
	 	');

        $keywords = preg_replace_callback('/()("[^()"]+")( and )("[^"()]+")()/i', $andProcessor, $keywords);
        $keywords = preg_replace_callback('/(^| )([^() ]+)( and )([^ ()]+)( |$)/i', $andProcessor, $keywords);
        $keywords = preg_replace_callback('/(^| )(not )("[^"()]+")/i', $notProcessor, $keywords);
        $keywords = preg_replace_callback('/(^| )(not )([^() ]+)( |$)/i', $notProcessor, $keywords);

        $keywords = $this->addStarsToKeywords($keywords);

        return $query->where([$predicate => $keywords]);
    }

    /**
     * @param string $keywords
     *
     * @return string
     */
    protected function addStarsToKeywords($keywords) {
        if (! trim($keywords)) {
            return "";
        }
        // Add * to each keyword
        $splitWords = preg_split("/ +/", trim($keywords));
        while (list($i, $word) = each($splitWords)) {
            if ($word[0] == '"') {
                while (list($i, $subword) = each($splitWords)) {
                    $word .= ' '.$subword;
                    if (substr($subword, -1) == '"') {
                        break;
                    }
                }
            } else {
                $word .= '*';
            }
            $newWords[] = $word;
        }

        return implode(" ", $newWords);
    }

    /**
     * Convert Foreign symbols to Latin
     *
     * @param string $str .
     *
     * @return string with only Latin Symbols
     */
    public static function convertForeignToLatin($str) {
        $tr = [
            "А" => "a", "Б" => "b", "В" => "v", "Г" => "g", "Д" => "d",
            "Е" => "e", "Ё" => "yo", "Ж" => "zh", "З" => "z", "И" => "i",
            "Й" => "j", "К" => "k", "Л" => "l", "М" => "m", "Н" => "n",
            "О" => "o", "П" => "p", "Р" => "r", "С" => "s", "Т" => "t",
            "У" => "u", "Ф" => "f", "Х" => "kh", "Ц" => "ts", "Ч" => "ch",
            "Ш" => "sh", "Щ" => "sch", "Ъ" => "", "Ы" => "y", "Ь" => "",
            "Э" => "e", "Ю" => "yu", "Я" => "ya", "а" => "a", "б" => "b",
            "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ё" => "yo",
            "ж" => "zh", "з" => "z", "и" => "i", "й" => "j", "к" => "k",
            "л" => "l", "м" => "m", "н" => "n", "о" => "o", "п" => "p",
            "р" => "r", "с" => "s", "т" => "t", "у" => "u", "ф" => "f",
            "х" => "kh", "ц" => "ts", "ч" => "ch", "ш" => "sh", "щ" => "sch",
            "ъ" => "", "ы" => "y", "ь" => "", "э" => "e", "ю" => "yu",
            "я" => "ya", "ą" => "a", "č" => "c", "ę" => "e", "ė" => "e",
            "į" => "i", "š" => "s", "ų" => "u", "ū" => "u", "ž" => "z",
        ];

        return strtr($str, $tr);
    }
}