<?php
/*
 * Copyright (c) 2010 Moacir de Oliveira <moacirdeoliveira.eng@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 */

class GoogleTranslatorApi {

    const URL = 'http://ajax.googleapis.com/ajax/services/language/translate';
    const API_VERSION = '1.0';

    private $langs = array(
        'UNKNOWN',
        'af' => 'AFRIKAANS',
        'sq' => 'ALBANIAN',
        'am' => 'AMHARIC',
        'ar' => 'ARABIC',
        'hy' => 'ARMENIAN',
        'az' => 'AZERBAIJANI',
        'eu' => 'BASQUE',
        'be' => 'BELARUSIAN',
        'bn' => 'BENGALI',
        'bh' => 'BIHARI',
        'bg' => 'BULGARIAN',
        'my' => 'BURMESE',
        'ca' => 'CATALAN',
        'chr' => 'CHEROKEE',
        'zh' => 'CHINESE',
        'zh-CN' => 'CHINESE_SIMPLIFIED',
        'zh-TW' => 'CHINESE_TRADITIONAL',
        'hr' => 'CROATIAN',
        'cs' => 'CZECH',
        'da' => 'DANISH',
        'dv' => 'DHIVEHI',
        'nl' => 'DUTCH',
        'en' => 'ENGLISH',
        'eo' => 'ESPERANTO',
        'et' => 'ESTONIAN',
        'tl' => 'FILIPINO',
        'fi' => 'FINNISH',
        'fr' => 'FRENCH',
        'gl' => 'GALICIAN',
        'ka' => 'GEORGIAN',
        'de' => 'GERMAN',
        'el' => 'GREEK',
        'gn' => 'GUARANI',
        'gu' => 'GUJARATI',
        'iw' => 'HEBREW',
        'hi' => 'HINDI',
        'hu' => 'HUNGARIAN',
        'is' => 'ICELANDIC',
        'id' => 'INDONESIAN',
        'iu' => 'INUKTITUT',
        'it' => 'ITALIAN',
        'ja' => 'JAPANESE',
        'kn' => 'KANNADA',
        'kk' => 'KAZAKH',
        'km' => 'KHMER',
        'ko' => 'KOREAN',
        'ku' => 'KURDISH',
        'ky' => 'KYRGYZ',
        'lo' => 'LAOTHIAN',
        'lv' => 'LATVIAN',
        'lt' => 'LITHUANIAN',
        'mk' => 'MACEDONIAN',
        'ms' => 'MALAY',
        'ml' => 'MALAYALAM',
        'mt' => 'MALTESE',
        'mr' => 'MARATHI',
        'mn' => 'MONGOLIAN',
        'ne' => 'NEPALI',
        'no' => 'NORWEGIAN',
        'or' => 'ORIYA',
        'ps' => 'PASHTO',
        'fa' => 'PERSIAN',
        'pl' => 'POLISH',
        'pt' => 'PORTUGUESE',
        'pa' => 'PUNJABI',
        'ro' => 'ROMANIAN',
        'ru' => 'RUSSIAN',
        'sa' => 'SANSKRIT',
        'sr' => 'SERBIAN',
        'sd' => 'SINDHI',
        'si' => 'SINHALESE',
        'sk' => 'SLOVAK',
        'sl' => 'SLOVENIAN',
        'es' => 'SPANISH',
        'sw' => 'SWAHILI',
        'sv' => 'SWEDISH',
        'tg' => 'TAJIK',
        'ta' => 'TAMIL',
        'tl' => 'TAGALOG',
        'te' => 'TELUGU',
        'th' => 'THAI',
        'bo' => 'TIBETAN',
        'tr' => 'TURKISH',
        'uk' => 'UKRAINIAN',
        'ur' => 'URDU',
        'uz' => 'UZBEK',
        'ug' => 'UIGHUR',
        'vi' => 'VIETNAMESE',
    );

    private function buildUrl($from, $to, $query) {
        $params = array(
            'v'         => self::API_VERSION,
            'q'         => $query,
            'langpair'  => $from . '|' . $to,
        );
        $qstring = '';
        foreach ($params as $k => $v) {
            $qstring .= $k . '=' . urlencode($v) . '&';
        }
        return self::URL . '?' . $qstring;
    }

    private function query($from, $to, $query) {
        $url = $this->buildUrl($from, $to, $query);
        $json = json_decode(file_get_contents($url));
        if ($json->responseStatus == 200) {
            return $json->responseData->translatedText;
        }
        return 'Error while translating the text: ' . $json->responseDetails;
    }

    public function translate($from, $to, $query) {
        //Code from http://svn.php.net/repository/web/doc-editor/trunk/php/GTranslate.php
        if (strlen($query) > 1000) {
            $strings = explode('§', wordwrap($query, 1000, '§'));
            $translation = '';
            foreach ($strings as $q) {
                $translation .= $this->query($from, $to, $q);
            }
            return $translation;
        }
        return $this->query($from, $to, $query);
    }

    public function supportedLanguages() {
        return $this->langs;
    }

    public function getLanguageName($acronym) {
        if (array_key_exists($acronym, $this->langs)) {
            return $this->langs[$acronym];
        }
        return $this->langs[0];
    }

}

function usage() {
    echo <<<HELP
A command line tool to communicate with the Google Translator API

Usage: php phptr.php <lang-from>_<lang-to> <text>
Example: php phptr.php en_pt "Hello World"

    -l, --langs             List the supported languages
    -h, --help              Display this help

Written by Moacir de Oliveira <moacirdeoliveira.eng@gmail.com>

HELP;
}

$api = new GoogleTranslatorApi;

if (in_array('-h', $argv) || in_array('--help', $argv)) {
    usage();
    exit(0);
}

if (in_array('-l', $argv) || in_array('--langs', $argv)) {
    $langs = $api->supportedLanguages();
    echo 'Supported Languages:' . PHP_EOL;
    foreach ($langs as $k => $v) {
        if ($v != 'UNKNOWN') {
            echo "\t" . $k . ":\t\t" . $v . PHP_EOL;
        }
    }
    exit(0);
}

if ($argc != 3) {
    usage();
    exit(0);
}

$langs = explode('_', $argv[1]);
if (count($langs) != 2) {
    usage();
    exit(0);
}

if (!array_key_exists($langs[0], $api->supportedLanguages())) {
    echo 'Language not supported: ' . $langs[0] . PHP_EOL;
    exit(0);
}

if (!array_key_exists($langs[1], $api->supportedLanguages())) {
    echo 'Language not supported: ' . $langs[1] . PHP_EOL;
    exit(0);
}

echo sprintf('Translating from %s to %s:',
             $api->getLanguageName($langs[0]),
             $api->getLanguageName($langs[1])) . PHP_EOL;

echo $api->translate($langs[0], $langs[1], $argv[2]) . PHP_EOL;
