<?php 

class Language {



    /**
     * Holds the current language
     *
     * @var string
     */
    private static $_language;

    /**
     * Holds the default language
     *
     * @var string
     */
    private static $_defaultlanguage = "en";


    private static $_translation_store = null;
    private static $_translator = null;



    /**
     * Enable/Disable auto google translation
     *
     * @var bool
     */
    private static $_auto = FALSE;

    /**
     * Possible languages
     *
     * @var arrays
     */
    public $languages = array( 	"auto" => "automatic",
    "sq" => "albanian",
    "ar" => "arabic",
    "bg" => "bulgarian",
    "ca" => "catalan",
    "zh-CN" => "chinese",
    "hr" => "croatian",
    "cs" => "czech",
    "da" => "danish",
    "nl" => "dutch",
    "en" => "english",
    "et" => "estonian",
    "tl" => "filipino",
    "fi" => "finnish",
    "fr" => "french",
    "gl" => "galician",
    "de" => "german",
    "el" => "greek",
    "iw" => "hebrew",
    "hi" => "hindi",
    "hu" => "hungarian",
    "id" => "indonesian",
    "it" => "italian",
    "ja" => "japanese",
    "ko" => "korean",
    "lv" => "latvian",
    "lt" => "lithuanian",
    "mt" => "maltese",
    "no" => "norwegian",
    "fa" => "persian alpha",
    "pl" => "polish",
    "pt" => "portuguese",
    "ro" => "romanian",
    "ru" => "russian",
    "sr" => "serbian",
    "sk" => "slovak",
    "sl" => "slovenian",
    "es" => "spanish",
    "sv" => "swedish",
    "th" => "thai",
    "tr" => "turkish",
    "uk" => "ukrainian",
    "vi" => "vietnamese"
    );

    static private function setup(){
        if (empty(self::$_translation_store))
        self::$_translation_store = new CsvTranslationStorer();

        if (empty(self::$_translator))
        self::$_translator = new GoogleTranslator();
        
        if (empty(self::$_language))
        self::$_language = self::$_defaultlanguage;
    }

    /**
     * Method to translate a string
     *
     * @access public
     * @param string $string
     * @return string
     */
    public function _($string) {
        self::setup();


        // If selected language is default
        // just return the value
        if(self::$_language == self::$_defaultlanguage) {
            return $string;
        }
        $value = self::$_translation_store->get($string);
        if(!$value) {
            // Add string to translations
            if(self::$_auto) {
                $value = self::translate(self::$_defaultlanguage, self::$_language, $string);

            }else {
                $value =  '<!-- TRANSLATE THIS -->';
            }
        }

        self::$_translation_store->set($string,$value);
        return $value;
    }





    



    /**
     * Set the default language
     *
     * @access public
     * @param string $language
     * @return void
     */
    public function SetDefault($language) {
        self::setup();
        self::$_defaultlanguage = $language;
    }

    /**
     * Set the current language
     *
     * @access public
     * @param string $language
     * @return void
     */
    public function Set(&$language) {
        self::setup();
        if(!empty($language)) {
            self::$_translation_store->load($language);
            self::$_language = $language;
        }else {
            self::$_translation_store->load(self::$_defaultlanguage);
            $language = self::$_defaultlanguage;
            self::$_language = $language;
        }
    }

    /**
     * Get the current language
     *
     * @access public
     * @return string
     */
    public function Get() {
        self::setup();
        return self::$_language;
    }

    /**
     * Enable auto google translation
     *
     * @access public
     * @param bool $bool
     * @return string
     */
    public function SetAuto($bool) {
        self::setup();
        self::$_auto = $bool;
    }

    /**
     * Translate a string using google translator
     *
     * @access private
     * @param string $from
     * @param string $to
     * @param string $string
     * @return string
     */
    private function translate($from, $to, $string) {
        self::setup();

        return self::$_translator->translate($from, $to, $string);

    }

/**
 * Write content to file
 *
 * @access
 */

}

function _($string) {    
    return Language::_($string);
}



